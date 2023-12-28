<?php

namespace App\Api\V1\Controllers;

use App\Constants\ParamConstant;
use App\Constants\Status;
use App\Models\MutedUser;
use App\Models\Specialist;
use App\Models\SpecialistClient;
use App\Models\User;
use App\Models\UserSpecialist;
use App\Models\UserStatus;
use App\Models\VerifyCode;
use App\Notifications\VerifyEmail;
use App\Notifications\VerifyPhone;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends BaseController
{
    /**
     * @return JsonResponse
     */
    public function show($id)
    {
        $auth = auth()->user();
        $user = User::query()->where(['id' => $id]);

        $data = $user->with(['location', 'specialist.location', 'specialist.specialties', 'specialist.educations', 'groupMembares.group' => function($query) use ($auth) {
            $query->where(['user_id' => $auth->id, 'status' => Status::ACTIVE]);
        }])->first();

        if (!empty($data) && !empty($data->groupMembares)) {
            $group_members = array_values($data->groupMembares->filter(function($val) {
                return !is_null($val->group);
            })->toArray());
            $data->group_membares = $group_members;

            unset($data->groupMembares);
        }

        return $this->sendResponse($data);
    }

    /**
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        $s = $request->get('s', '') ?? '';
        $search = sprintf('%%%s%%', $s);
        $data = $request->only('specialties', 'rate', 'online', 'offline', 'locations', 'sort', 'order');

        $validator = Validator::make($data, [
            'sort' => 'nullable|string|in:rate',
            'order' => 'nullable|string|in:asc,desc',
            'specialties' => 'nullable|array',
            'specialties.*' => 'nullable|exists:specialties,id',
            'rate' => 'nullable|array',
            'rate.*' => 'nullable|in:1,2,3,4,5',
            'online' => 'nullable|boolean',
            'offline' => 'nullable|boolean',
            'locations' => 'nullable|array',
            'locations.*' => 'nullable|exists:locations,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $sort = $data['sort'] ?? null;
        $order = $data['order'] ?? null;
        $specialties = $data['specialties'] ?? null;
        $rate = $data['rate'] ?? null;
        $online = isset($data['online']) ? (int) ($data['online']) : '';
        $offline = isset($data['offline']) ? (int) ($data['offline']) : '';
        $location = $data['locations'] ?? null;

        $users = User::query()
            ->where(['status_id' => UserStatus::ACTIVE])
            ->with(['location', 'specialist.specialties', 'specialist.location'])->whereHas('specialist', function(Builder $query) use ($specialties, $online, $offline, $location, $rate) {
                $query->whereIn('status', [Status::ACTIVE, Status::FOR_CHECKING]);

                if ($specialties) {
                    $query->whereHas('specialties', function($query) use ($specialties) {
                        $query->whereIn('speciality_id', $specialties);
                    });
                }

                if ($online === 1) {
                    $query->where(['online' => 1]);
                } elseif ($online === 0) {
                    $query->where(['online' => 0]);
                }

                if ($offline === 1) {
                    $query->where(['offline' => 1]);
                } elseif ($offline === 0) {
                    $query->where(['offline' => 0]);
                }

                if ($rate) {
                    $query->where(function($q) use ($rate) {
                        foreach ($rate as $r) {
                            $q->orWhereBetween('rate', [$r, $r + 0.99]);
                        }
                    });
                }
            });

        if (is_array($location) && !empty($location)) {
            $users->whereIn('location_id', $location);
        }

        if (Str::length($s) > 0) {
            $users->where(function($query) use ($search) {
                return $query->orWhere('first_name', 'like', $search)
                    ->orWhere('last_name', 'like', $search)
                    ->orWhere('patronymic_name', 'like', $search)
                    ->orWhereHas('specialist.specialties', function($query) use ($search) {
                        return $query->where('name', 'like', $search);
                    });
            });
        }

        if ($sort) {
            if ($sort == 'rate') {
                if ($order == 'asc') {
                    $users->orderBy(Specialist::select('rate')
                        ->whereColumn('specialists.user_id', 'users.id'));
                } elseif ($order == 'desc') {
                    $users->orderByDesc(Specialist::select('rate')
                        ->whereColumn('specialists.user_id', 'users.id'));
                }
            }
        } else {
            $users->orderByDesc(Specialist::select('created_at')
                ->whereColumn('specialists.user_id', 'users.id'));
        }

        return $this->sendResponse($users->paginate(10));
    }

    /**
     * @return JsonResponse
     */
    public function searchClientAndSpecialist(Request $request)
    {
        $s = $request->get('s', '') ?? '';
        $search = sprintf('%%%s%%', $s);
        $auth = auth()->user();

        $specialists = $auth->mySpecialists()->with('specialist')->get();
        $specialists = !empty($specialists) ? $specialists->pluck('specialist')->pluck('user_id')->toArray() : [];
        $clients = $auth->specialist()->with('clients')->first();
        $clients = !empty($clients) && !empty($clients['clients']) ? $clients['clients']->pluck('user_id')->toArray() : [];

        $all_list = array_unique(array_merge($specialists, $clients));

        $users = User::query()->where(['status_id' => UserStatus::ACTIVE])->whereIn('id', $all_list)->with('specialist');

        if (Str::length($s) > 0) {
            $users->where(function($query) use ($search) {
                return $query->orWhere('first_name', 'like', $search)
                    ->orWhere('last_name', 'like', $search)
                    ->orWhere('patronymic_name', 'like', $search);
            });
        }

        return $this->sendResponse($users->paginate(ParamConstant::MEMBER_COUNT));
    }

    /**
     * @return JsonResponse
     */
    public function findSpecialist(Request $request)
    {
        $data = $request->only(['email_or_phone', 'type', 'first_name', 'last_name', 'patronymic_name']);

        if (!empty($data['type'])) {
            $rule = [
                'type' => 'required|string|in:email,phone',
                'email_or_phone' => 'required|email:dns|max:255',
            ];

            $isPhone = false;

            if ($data['type'] == ParamConstant::PHONE) {
                $rule = [
                    'type' => 'required|string|in:email,phone',
                    'email_or_phone' => 'required|phone:AUTO',
                ];

                $isPhone = true;
            }

            $validator = Validator::make($data, array_merge([
                'type' => 'required|string|in:email,phone',
            ], $rule), [], ['email_or_phone' => $isPhone ? 'Телефон' : 'Эл. почта', 'type' => 'Тип']);

            if ($validator->fails()) {
                return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $user_id = auth()->id();
            $email_or_phone = $data['email_or_phone'];

            $find = User::query()->where('id', '<>', $user_id)->where(function($query) use ($email_or_phone) {
                $query->orWhere(['email' => $email_or_phone, 'phone' => $email_or_phone]);
            })->whereHas('specialist');

            if ($find->exists()) {
                $user = $find->first();
                $specialist_id = $user->specialist->id;

                $client = UserSpecialist::query()->where(['status' => Status::ACTIVE, 'specialist_id' => $specialist_id, 'user_id' => $user_id]);

                if ($client->exists()) {
                    return $this->sendError(['message' => 'Уже существует'], Response::HTTP_FORBIDDEN);
                }

                return $this->sendResponse($user);
            }

            return $this->sendResponse([]);
        } else {
            $validator = Validator::make($data, [
                'first_name' => 'required|string|min:2|max:255',
                'last_name' => 'nullable|string|min:2|max:255',
                'patronymic_name' => 'nullable|string|min:2|max:255',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $user_id = auth()->id();
            $specialist_ids = UserSpecialist::query()->where(['user_id' => $user_id])->whereNotNull('specialist_id')->get()->pluck('specialist_id');

            $users = User::query()->where(['status_id' => UserStatus::ACTIVE])->where('id', '<>', $user_id)->where(function($query) use ($data) {
                $query->where('first_name', 'like', sprintf('%%%s%%', $data['first_name']));
                if (!empty($data['last_name'])) {
                    $query->where('last_name', 'like', sprintf('%%%s%%', $data['last_name']));
                }
                if (!empty($data['patronymic_name'])) {
                    $query->where('patronymic_name', 'like', sprintf('%%%s%%', $data['patronymic_name']));
                }
            })->with('specialist')->whereHas('specialist');

            if (!$users->exists()) {
                return $this->sendError();
            }

            if (!empty($specialist_ids) && $users->count() == 1 && in_array($users->first()->specialist->id, $specialist_ids->toArray())) {
                return $this->sendError(['message' => 'Данный пользователь уже добавлен в специалисты'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            return $this->sendResponse($users->paginate(ParamConstant::SEARCH_CLIENT_COUNT));
        }
    }

    /**
     * @return JsonResponse
     */
    public function addSpecialist(Request $request)
    {
        $data = $request->only(['pseudonym', 'specialist_id']);

        $validator = Validator::make($data, [
            'specialist_id' => 'required|integer|exists:specialists,id',
            'pseudonym' => 'nullable|min:2|max:255',
        ], [], ['specialist_id' => 'Специалист ID']);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        /** @var User $auth */
        $auth = auth()->user();
        $specialist_id = $data['specialist_id'];

        DB::beginTransaction();

        try {
            $client = UserSpecialist::query()->where(['specialist_id' => $specialist_id, 'user_id' => $auth->id]);

            if ($client->exists()) {
                return $this->sendError(['message' => 'Данный специалист уже добавлен в список'], Response::HTTP_FORBIDDEN);
            }

            UserSpecialist::query()->create([
                'specialist_id' => $specialist_id,
                'user_id' => $auth->id,
                'pseudonym' => !empty($data['pseudonym']) ? $data['pseudonym'] : null,
            ]);

            DB::commit();

            return $this->sendResponse(['message' => 'Успешно добавлено'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     */
    public function mySpecialist(Request $request)
    {
        $s = $request->get('s', '') ?? '';
        $type = $request->get('type');

        if (!empty($type) && !in_array($type, [ParamConstant::NAME, ParamConstant::PSEUDONYM])) {
            return $this->sendError(['message' => 'Выбранное значение для фильтрации ошибочно.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $specialists = auth()->user()->mySpecialists();

        $by_name = $type == ParamConstant::NAME;
        $by_pseudonym = $type == ParamConstant::PSEUDONYM;

        if ($by_pseudonym) {
            $specialists->whereNotNull('pseudonym');
        }

        $specialists = $specialists->orderBy('pseudonym');

        if (empty($s)) {
            if ($by_name) {
                $specialists->whereHas('specialist.user');
            }
        } else {
            $search = sprintf('%%%s%%', $s);

            $specialists->where(function($query) use ($search, $by_name, $by_pseudonym) {
                if ($by_pseudonym) {
                    return $query->orWhere('pseudonym', 'like', $search);
                }
                if ($by_name) {
                    return $query->whereHas('specialist.user', function($query) use ($search) {
                        $query->where('users.first_name', 'like', $search)
                            ->orWhere('users.last_name', 'like', $search)
                            ->orWhere('users.patronymic_name', 'like', $search);
                    });
                }

                return $query->orWhere('pseudonym', 'like', $search)->orWhereHas('specialist.user', function($query) use ($search) {
                    $query->where('users.first_name', 'like', $search)
                        ->orWhere('users.last_name', 'like', $search)
                        ->orWhere('users.patronymic_name', 'like', $search);
                });
            });
        }

        return $this->sendResponse($specialists->paginate(ParamConstant::MEMBER_COUNT));
    }

    /**
     * @param mixed $id
     *
     * @return JsonResponse
     */
    public function deleteSpecialist($id)
    {
        /** @var User $auth */
        $auth = auth()->user()->mySpecialists()->where(['specialist_id' => $id]);

        $specialist = $auth->first();

        if (!$specialist) {
            return $this->sendError();
        }

        try {
            $specialist->delete();

            return $this->sendResponse([], Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::guard()->user();

        $data = $request->only('first_name', 'last_name', 'patronymic_name', 'gender', 'b_day', 'phone', 'content', 'url', 'avatar');

        /** @var Specialist $specialist */
        $specialist = $user->specialist()->first();

        if ($specialist && $specialist->status == Status::BLOCKED) {
            return $this->sendError(
                ['message' => 'Вы можете изменить личные данные только через профиль специалиста, добавив фото паспорта для проверки'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $validator = Validator::make($data, [
            'first_name' => 'required|min:2|max:255',
            'last_name' => 'nullable|min:2|max:255',
            'patronymic_name' => 'nullable|min:2|max:255',
            'gender' => 'nullable|in:male,female',
            'b_day' => 'nullable|date_format:d-m-Y',
            //            'work_phone' => 'nullable|phone:AUTO',
            'phone' => 'nullable|phone:AUTO|unique:users,phone,'.$user->id,
            'location_id' => 'nullable|exists:locations,id',
            'content' => 'nullable|min:2|max:2000',
            'url' => 'nullable|url',
            'avatar' => 'nullable|mimes:jpg,bmp,png,jpeg|max:5120',
        ], [
            'b_day.date_format' => 'Поле :attribute не соответствует формату ДД-ММ-ГГГГ.',
            'phone.unique' => 'Пользователь с таким номером телефона уже зарегистрирован в системе. Пожалуйста, введите другой',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();
        $isChanged = false;
        $isSpecialist = false;

        try {
            if (!empty($specialist)) {
                $isSpecialist = true;
                $specialist->update(['status' => Status::FOR_CHECKING]);
            }

            if (!empty($data['b_day'])) {
                $data['b_day'] = Carbon::createFromFormat('d-m-Y', $data['b_day'])->format('Y-m-d');
            }

            $user->update($data);

            if (!empty($user->getChanges())) {
                $isChanged = true;
            }

            if ($request->has('avatar') && $request->file('avatar')) {
                $user->clearMediaCollection('avatar');
                $user->addMedia($request->file('avatar'))
                    ->toMediaCollection('avatar');
                $isChanged = true;
            }

//            $work_phone = $request->get('work_phone', null);
//
//            if ($is_specialist && $work_phone && $specialist->phone != $work_phone) {
//                $specialist->update(['phone' => $work_phone]);
//                $isChanged = true;
//            }

            if ($isChanged) {
                DB::commit();
                if ($isSpecialist) {
                    return $this->sendResponse(['message' => 'Данные успешно обновлены и отправлены на проверку']);
                }

                return $this->sendResponse(['message' => 'Данные успешно обновлены']);
            }

            return $this->sendResponse(['message' => 'Нет изменений']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
        }

        return $this->sendInternalError(Response::HTTP_INTERNAL_SERVER_ERROR, 'Что-то пошло не так, попробуйте еще раз');
    }

    /**
     * @return JsonResponse
     */
    public function location(Request $request)
    {
        /** @var User $user */
        $user = Auth::guard()->user();

        $data = $request->only('location_id');

        $validator = Validator::make($data, [
            'location_id' => 'required|exists:locations,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $user->update($data);

            return $this->sendResponse(['message' => 'Местоположение успешно обновлено.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     */
    public function about(Request $request)
    {
        /** @var User $user */
        $user = Auth::guard()->user();

        if ($request->isMethod('get')) {
            return $this->sendResponse(['content' => $user->content]);
        }

        $data = $request->only('content');

        $validator = Validator::make($data, [
            'content' => 'required|min:2|max:2000',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $user->update($data);

            return $this->sendResponse($user->refresh());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => sprintf('required|string|min:%d|max:%d', ParamConstant::PASSWORD_MIN, ParamConstant::PASSWORD_MAX),
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $validator->after(function($validator) use ($request) {
                if (Hash::check($request->get('old_password'), Auth::user()->password) == false) {
                    $validator->errors()->add('old_password', __('messages.password.old_not_eq'));
                } elseif (Hash::check(request('new_password'), Auth::user()->password) == true) {
                    $validator->errors()->add('new_password', __('messages.password.new_eq_old'));
                }
            });

            if ($validator->fails()) {
                return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            User::query()->where('id', Auth::user()->id)->update(['password' => Hash::make($request->get('new_password'))]);

            return $this->sendResponse(['message' => __('messages.password.changed')]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     */
    public function changeEmailOrPhone(Request $request)
    {
        $data = $request->only('email', 'phone', 'type');
        $auth_id = Auth::id();

        $validator = Validator::make($data, [
            'email' => sprintf('nullable|required_if:type,email|required_without:phone|email:filter|max:%d|unique:users,email,%d', ParamConstant::EMAIL_MAX, $auth_id),
            'phone' => 'nullable|required_if:type,phone|required_without:email|phone:RU|unique:users,phone,'.$auth_id,
            'type' => 'required|in:email,phone',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $code = rand(ParamConstant::VERIFICATION_CODE_START, ParamConstant::VERIFICATION_CODE_END);
        $token = Str::random(ParamConstant::TOKEN_LENGTH);

        $type = $request->get('type', 'email');

        try {
            $verifyCode = VerifyCode::query()->updateOrCreate([
                'user_id' => $auth_id,
                'type' => $type,
            ], [
                $type => $data[$type],
                'token' => $token,
                'code' => $code,
            ]);

            $verifyCode->notify($type == 'phone' ? (new VerifyPhone($code)) : (new VerifyEmail($code)));

            return $this->sendResponse(compact('token'), Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     */
    public function confirmEmailOrPhone(Request $request)
    {
        $data = $request->only(['token', 'code']);

        $validator = Validator::make($data, [
            'token' => sprintf('required|string|size:%d|exists:verify_codes', ParamConstant::TOKEN_LENGTH),
            'code' => sprintf('required|numeric|digits:%d', ParamConstant::CODE_LENGTH),
        ], [
            'token.exists' => __('auth.invalid_token'),
        ]);

        $is_valid_token = VerifyCode::self()->isValidToken($token = $data['token']);
        $check_code = VerifyCode::self()->checkCode($token, $data['code']);

        $validator->after(function($validator) use ($is_valid_token, $check_code) {
            if (!$is_valid_token) {
                $validator->errors()->add(
                    'token', __('auth.token_expired')
                );
            }

            if (!$check_code) {
                $validator->errors()->add(
                    'code', __('auth.invalid_code')
                );
            }
        });

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();

            $verifyCodeByToken = VerifyCode::query()->where(['token' => $token]);
            $params = $verifyCodeByToken->first(['email', 'phone', 'type']);

            $verifyCodeByToken->delete();
            $type = $params->type;

            Auth::user()->update([$type => $params->{$type}]);

            DB::commit();

            return $this->sendResponse(['message' => __("messages.{$type}").' успешно изменён.', 'user' => Auth::user()], Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        DB::rollBack();

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     */
    public function becomeToSpecialist()
    {
        $specialist = auth()->user()->specialist()->where(['status' => Status::BLOCKED])->first();

        if (!empty($specialist)) {
            Specialist::find($specialist->id)->update(['status' => Status::ACTIVE]);
        }

        return $this->sendResponse(['message' => 'Успешно!']);
    }

    /**
     * @return JsonResponse
     */
    public function specialists()
    {
        $specialist = SpecialistClient::query()->with(['specialist.user', 'specialist.specialties'])->where(['user_id' => auth()->id()])->get();

        return $this->sendResponse($specialist);
    }

    /**
     * @return JsonResponse
     */
    public function mute($id)
    {
        $validator = Validator::make(['user_id' => $id], [
            'user_id' => 'required|exists:users,id',
        ], [], ['user_id' => 'пользователья']);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $muted = MutedUser::query()->updateOrCreate(['user_id' => auth()->id(), 'muted_user_id' => $id]);

            if ($muted->wasRecentlyCreated) {
                return $this->sendResponse(['message' => 'Уведомления отключены']);
            }

            return $this->sendResponse(['message' => 'Уже отключен']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     */
    public function unmute($id)
    {
        $validator = Validator::make(['user_id' => $id], [
            'user_id' => 'required|exists:users,id',
        ], [], ['user_id' => 'пользователья']);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $muted = MutedUser::query()->where(['user_id' => auth()->id(), 'muted_user_id' => $id]);

        try {
            if ($muted->exists()) {
                $muted->delete();

                return $this->sendResponse(['message' => 'Уведомления включены']);
            }

            return $this->sendResponse(['message' => 'Уже включен']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     */
    public function deleteImage(Request $request)
    {
        try {
            Auth::user()->clearMediaCollection('avatar');

            return $this->sendResponse(['message' => 'Аватар успешно удален.'], Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }
}
