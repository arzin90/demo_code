<?php

namespace App\Api\V1\Controllers;

use App\Constants\ParamConstant;
use App\Constants\Status;
use App\Models\Program;
use App\Models\Specialist;
use App\Models\SpecialistClient;
use App\Models\User;
use App\Models\UserStatus;
use App\Notifications\NotifyClient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SpecialistController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = auth()->user();
        /** @var Specialist $specialties */
        $specialties = $user->specialist()->first();

        return $this->sendResponse($specialties->specialties()->get(['speciality_id']));
    }

    /**
     * @param mixed $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function programs($id)
    {
        return $this->sendResponse(Program::query()->where(['specialist_id' => $id])
            ->with(['specialist.user', 'specialist.specialties', 'presenter_user'])
            ->whereIn('status', [Status::ACTIVE, Status::FOR_CHECKING])
            ->paginate(15));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->only('specialties');

        $validator = Validator::make($data, [
            'specialties' => 'required|array|min:1|max:3',
            'specialties.*' => 'required|exists:specialties,id',
        ], [
            'specialties.*.exists' => 'Выбранное значение для Специализации некорректно.',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $specialist = auth()->user()->specialist()->first();

        try {
            $specialist->specialties()->sync(array_unique(array_values($data['specialties'])));

            return $this->sendResponse(['message' => 'Сохранено!']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function becomeToClient()
    {
        $specialist = auth()->user()->specialist()->first();

        Specialist::find($specialist->id)->update(['status' => Status::BLOCKED]);

        return $this->sendResponse(['message' => 'Успешно!']);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function workType()
    {
        $work_types = Auth::user()->specialist()->with('location')->first(['online', 'offline', 'location_id', 'address', 'link']);

        return $this->sendResponse($work_types);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function online(Request $request)
    {
        $data = $request->only('online', 'link');

        $validator = Validator::make($data, [
            'online' => 'required|in:0,1',
            'link' => 'nullable|string|min:2|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            auth()->user()->specialist()->update($data);

            return $this->sendResponse(['message' => 'Тип работы успешно изменен.']);
        } catch (\Exception $e) {
            log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function offline(Request $request)
    {
        $data = $request->only('offline', 'location_id', 'address');

        $validator = Validator::make($data, [
            //            'offline' => 'required|in:0,1',
            'location_id' => 'nullable|numeric|exists:locations,id',
            'address' => 'nullable|string|min:2|max:255',
        ], [
            'location_id.exists' => 'Выбранное значение для город некорректно.',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $data['location_id'] = !empty($data['location_id']) ? $data['location_id'] : null;
            $data['address'] = !empty($data['address']) ? $data['address'] : null;
            $data['offline'] = (is_null($data['location_id']) && is_null($data['address'])) ? 0 : 1;

            auth()->user()->specialist()->update($data);

            return $this->sendResponse(['message' => 'Тип работы успешно изменен']);
        } catch (\Exception $e) {
            log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function videos(Request $request)
    {
        $user = auth()->user();
        /** @var Specialist $specialist */
        $specialist = $user->specialist()->first();
        $video = null;

        if ($specialist->video) {
            $video = [
                'url' => $specialist->video,
            ];
        }

        if (!empty($specialist->videoMedia)) {
            $video = $specialist->videoMedia[0];
        }

        return $this->sendResponse(['video' => $video]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function addVideo(Request $request)
    {
        $data = $request->only('video');
        $isFile = false;
        if ($request->hasFile('video')) {
            $isFile = true;

            $rule = [
                'video' => sprintf('required|mimes:mp4,avi,mpeg|max:%d', 1024 * 1024 * 2), // 2GB
            ];
        } else {
            $rule = [
                'video' => 'required|active_url|max:255',
            ];
        }
        $validator = Validator::make($data, $rule);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = auth()->user();

        try {
            /** @var Specialist $specialist */
            $specialist = $user->specialist()->first();

            $specialist->clearMediaCollection('video');

            if ($isFile) {
                $specialist->addMedia($request->file('video'))
                    ->withCustomProperties(['status' => Status::PENDING])
                    ->toMediaCollection('video');
                $specialist->update(['video' => null, 'video_status' => null]);

                return $this->sendResponse(['video' => $specialist->videoMedia[0]]);
            } else {
                $url = $data['video'];
                $data['video_status'] = Status::PENDING;

                if (strpos($url, 'youtube') > 0) {
                    parse_str(parse_url($url, PHP_URL_QUERY), $query_params);
                    if (!empty($query_params['v'])) {
                        $data['video'] = sprintf('https://www.youtube.com/embed/%s', $query_params['v']);
                    }
                } elseif (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $videoId)) {
                    if (!empty($videoId) && !empty($videoId[1])) {
                        $data['video'] = sprintf('https://www.youtube.com/embed/%s', $videoId[1]);
                    }
                }

                $specialist->update($data);

                return $this->sendResponse([
                    'video' => [
                        'url' => $specialist->video,
                        'status' => $specialist->video_status,
                    ],
                ]);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteVideo()
    {
        $user = auth()->user();
        /** @var Specialist $specialist */
        $specialist = $user->specialist()->first();

        try {
            $specialist->clearMediaCollection('video');

            $specialist->update(['video' => null]);

            return $this->sendResponse(['message' => 'Видео успешно удален']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchClient(Request $request)
    {
        $s = $request->get('s', '') ?? '';
        $type = $request->get('type');

        if (!empty($type) && !in_array($type, [ParamConstant::NAME, ParamConstant::PSEUDONYM])) {
            return $this->sendError(['message' => 'Выбранное значение для фильтрации ошибочно.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $specialist_id = auth()->user()->specialist->id;

        $clients = SpecialistClient::query()->where(['specialist_id' => $specialist_id])->whereNotNull('user_id');

        $by_name = $type == ParamConstant::NAME;
        $by_pseudonym = $type == ParamConstant::PSEUDONYM;

        if ($by_pseudonym) {
            $clients->whereNotNull('pseudonym');
        }

        $clients = $clients->orderBy('pseudonym');

        if (empty($s)) {
            if ($by_name) {
                $clients->whereHas('user');
            }

            $itemsPaginated = $clients->paginate(ParamConstant::SEARCH_CLIENT_COUNT);
        } else {
            $search = sprintf('%%%s%%', $s);

            $itemsPaginated = $clients->where(function($query) use ($search, $by_name, $by_pseudonym) {
                if ($by_pseudonym) {
                    return $query->orWhere('pseudonym', 'like', $search);
                }
                if ($by_name) {
                    return $query->whereHas('user', function($query) use ($search) {
                        $query->where('users.first_name', 'like', $search)
                            ->orWhere('users.last_name', 'like', $search)
                            ->orWhere('users.patronymic_name', 'like', $search);
                    });
                }

                return $query->orWhere('pseudonym', 'like', $search)->orWhereHas('user', function($query) use ($search) {
                    $query->where('users.first_name', 'like', $search)
                        ->orWhere('users.last_name', 'like', $search)
                        ->orWhere('users.patronymic_name', 'like', $search);
                });
            })->paginate(ParamConstant::SEARCH_CLIENT_COUNT);
        }

        $itemsTransformed = $itemsPaginated->getCollection()->groupBy(function($item, $key) use ($type, $by_name, $by_pseudonym) {
            if (empty($type)) {
                if (!empty($item['pseudonym'])) {
                    return Str::substr(Str::ucfirst($item['pseudonym']), 0, 1);
                }
                if (!empty($item['user']) && !empty($item['user']['first_name'])) {
                    return Str::substr(Str::ucfirst($item['user']['first_name']), 0, 1);
                }
            } elseif ($by_pseudonym) {
                if (!empty($item['pseudonym'])) {
                    return Str::substr(Str::ucfirst($item['pseudonym']), 0, 1);
                }
            } elseif ($by_name) {
                if (!empty($item['user']) && !empty($item['user']['first_name'])) {
                    return Str::substr(Str::ucfirst($item['user']['first_name']), 0, 1);
                }
            }

            return '';
        })->sort();

        $itemsTransformedAndPaginated = new LengthAwarePaginator(
            $itemsTransformed,
            $itemsPaginated->total(),
            $itemsPaginated->perPage(),
            $itemsPaginated->currentPage(), [
                'path' => URL::current(),
                'query' => [
                    'page' => $itemsPaginated->currentPage(),
                ],
            ]
        );

        return $this->sendResponse($itemsTransformedAndPaginated);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function findClient(Request $request)
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

            $specialist_id = auth()->user()->specialist->id;
            $user_id = auth()->id();
            $email_or_phone = $data['email_or_phone'];

            $find = User::query()->where('id', '<>', $user_id)->where(function($query) use ($email_or_phone) {
                $query->orWhere(['email' => $email_or_phone, 'phone' => $email_or_phone]);
            });

            if ($find->exists()) {
                $user = $find->first();
                $client = SpecialistClient::query()->where(['specialist_id' => $specialist_id, 'user_id' => $user->id]);

                if ($client->exists()) {
                    return $this->sendError(['message' => 'Уже существует'], Response::HTTP_FORBIDDEN);
                }

                return $this->sendResponse($user);
            }

            return $this->sendResponse(['email_or_phone' => $email_or_phone]);
        } else {
            $validator = Validator::make($data, [
                'first_name' => 'required|string|min:2|max:255',
                'last_name' => 'nullable|string|min:2|max:255',
                'patronymic_name' => 'nullable|string|min:2|max:255',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $specialist_id = auth()->user()->specialist->id;
            $user_id = auth()->id();
            $client_ids = SpecialistClient::query()->where(['specialist_id' => $specialist_id])->whereNotNull('user_id')->get()->pluck('user_id');

            $users = User::query()->where(['status_id' => UserStatus::ACTIVE])->where('id', '<>', $user_id)->where(function($query) use ($data) {
                $query->where('first_name', 'like', sprintf('%%%s%%', $data['first_name']));
                if (!empty($data['last_name'])) {
                    $query->where('last_name', 'like', sprintf('%%%s%%', $data['last_name']));
                }
                if (!empty($data['patronymic_name'])) {
                    $query->where('patronymic_name', 'like', sprintf('%%%s%%', $data['patronymic_name']));
                }
            });

            if (!$users->exists()) {
                return $this->sendError();
            }

            if (!empty($client_ids) && $users->count() == 1 && in_array($users->first()->id, $client_ids->toArray())) {
                return $this->sendError(['message' => 'Данный пользователь уже добавлен в клиенты'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            return $this->sendResponse($users->paginate(ParamConstant::SEARCH_CLIENT_COUNT));
        }
    }

    /**
     * @param mixed $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClient($id)
    {
        /** @var User $auth */
        $auth = auth()->user();
        $specialist_id = $auth->specialist->id;

        $client = SpecialistClient::query()->where(['id' => $id, 'specialist_id' => $specialist_id])->whereNotNull('user_id');

        if (!$client->exists()) {
            return $this->sendError();
        }

        $data = $client->first();

        $user = User::query()->where(['id' => $data->user_id])->with(['groupMembares.group' => function($query) use ($auth) {
            $query->where(['user_id' => $auth->id, 'status' => Status::ACTIVE]);
        }])->first();

        $group_members = null;

        if (!empty($user) && !empty($user->groupMembares)) {
            $group_members = array_values($user->groupMembares->filter(function($val) {
                return !is_null($val->group);
            })->toArray());
        }

        $data->group_membares = $group_members;

        return $this->sendResponse($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function addClient(Request $request)
    {
        $data = $request->only(['email_or_phone', 'type', 'pseudonym', 'user_id']);
        $is_user_id = false;

        if (empty($data['user_id'])) {
            $rule = [
                'email_or_phone' => 'required|email:dns|max:255',
                'type' => 'required|string|in:email,phone',
            ];

            $isPhone = false;

            if (!empty($data['type']) && $data['type'] == ParamConstant::PHONE) {
                $rule = [
                    'email_or_phone' => 'required|phone:AUTO',
                    'type' => 'required|string|in:email,phone',
                ];

                $isPhone = true;
            }

            $attribute = ['email_or_phone' => $isPhone ? 'Телефон' : 'Эл. почта', 'type' => 'Тип'];
        } else {
            $is_user_id = true;
            $rule = [
                'user_id' => 'required|integer|exists:users,id',
            ];

            $attribute = [];
        }

        $validator = Validator::make($data, array_merge([
            'pseudonym' => 'nullable|min:2|max:255',
        ], $rule), [], $attribute);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        /** @var User $auth */
        $auth = auth()->user();
        $specialist_id = $auth->specialist->id;

        DB::beginTransaction();

        try {
            if (!$is_user_id) {
                $email_or_phone = $data['email_or_phone'];

                $user = User::query()->where(['status_id' => UserStatus::ACTIVE])->where(function($query) use ($email_or_phone) {
                    return $query->orWhere(['email' => $email_or_phone, 'phone' => $email_or_phone]);
                });
            } else {
                $user = User::query()->where(['id' => $data['user_id'], 'status_id' => UserStatus::ACTIVE]);
            }

            if (!$user->exists()) {
                return $this->sendError();
            }

            $first = $user->first();
            $id = $first->id;

            if ($auth->id == $id) {
                return $this->sendError([], Response::HTTP_FORBIDDEN);
            }

            $client = SpecialistClient::query()->where(['specialist_id' => $specialist_id, 'user_id' => $id]);

            if ($client->exists()) {
                return $this->sendError(['message' => 'Данный пользователь уже добавлен в клиенты'], Response::HTTP_FORBIDDEN);
            }

            $update_or_create = [
                'specialist_id' => $specialist_id,
            ];

            if (!$is_user_id) {
                if ($isPhone) {
                    $update_or_create['phone'] = $email_or_phone;
                } else {
                    $update_or_create['email'] = $email_or_phone;
                }

                SpecialistClient::query()->updateOrCreate($update_or_create,
                    [
                        'user_id' => $id,
                        'pseudonym' => !empty($data['pseudonym']) ? $data['pseudonym'] : null,
                    ]
                );
            } else {
                SpecialistClient::query()->create([
                    'specialist_id' => $specialist_id,
                    'user_id' => $id,
                    'pseudonym' => !empty($data['pseudonym']) ? $data['pseudonym'] : null,
                ]);
            }

            DB::commit();

            return $this->sendResponse(['message' => 'Успешно добавлено'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return $this->sendInternalError();
    }

    /**
     * @param mixed $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateClient(Request $request, $id)
    {
        $data = $request->only('email_or_phone', 'type', 'pseudonym', 'user_id');
        $is_user_id = false;

        if (empty($data['user_id'])) {
            $rule = [
                'email_or_phone' => 'required|email:dns|max:255',
                'type' => 'required|string|in:email,phone',
            ];

            $isPhone = false;

            if (!empty($data['type']) && $data['type'] == ParamConstant::PHONE) {
                $rule = [
                    'email_or_phone' => 'required|phone:AUTO',
                    'type' => 'required|string|in:email,phone',
                ];

                $isPhone = true;
            }

            $attribute = ['email_or_phone' => $isPhone ? 'Телефон' : 'Эл. почта', 'type' => 'Тип'];
        } else {
            $is_user_id = true;
            $rule = [
                'user_id' => 'required|integer|exists:users,id',
            ];

            $attribute = [];
        }

        $validator = Validator::make($data, array_merge([
            'pseudonym' => 'nullable|min:2|max:255',
        ], $rule), [], $attribute);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        /** @var User $auth */
        $auth = auth()->user();
        $specialist_id = $auth->specialist->id;

        $client = SpecialistClient::query()->where(['id' => $id, 'specialist_id' => $specialist_id]);

        if (!$client->exists()) {
            return $this->sendError();
        }

        $client = $client->first();
        DB::beginTransaction();

        try {
            $update = [];

            if (!$is_user_id) {
                $email_or_phone = $data['email_or_phone'];

                if ($isPhone) {
                    $update['phone'] = $email_or_phone;
                } else {
                    $update['email'] = $email_or_phone;
                }
            }

            if (!empty($data['pseudonym'])) {
                $update['pseudonym'] = $data['pseudonym'];
            } else {
                $update['pseudonym'] = null;
            }

            $user_exists = false;

            if (is_null($client->user_id) && isset($email_or_phone)) {
                $user = User::query()->where(['status_id' => UserStatus::ACTIVE])->where(function($query) use ($email_or_phone) {
                    return $query->orWhere(['email' => $email_or_phone, 'phone' => $email_or_phone]);
                });

                if ($user->exists()) {
                    $user_id = $user->first()->id;
                    if ($auth->id == $user_id) {
                        return $this->sendError([], Response::HTTP_FORBIDDEN);
                    }
                    $user_exists = true;
                    $update['user_id'] = $user_id;
                }
            } else {
                $user_exists = true;
            }

            if (!$is_user_id) {
                if ($isPhone) {
                    $update['email'] = null;
                } else {
                    $update['phone'] = null;
                }

                if (!$user_exists && !$isPhone) {
                    $update['verified'] = 0;
                }
            }

            $client->update($update);

            DB::commit();

            return $this->sendResponse($client->refresh());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
        }

        return $this->sendInternalError();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function notifyClient(Request $request)
    {
        $data = $request->only('email_or_phone', 'type', 'user_id');

        $is_user_id = false;

        if (empty($data['user_id'])) {
            $rule = [
                'email_or_phone' => 'required|email:dns|max:255|unique:users,email',
            ];

            $isPhone = false;

            if (!empty($data['type']) && $data['type'] == ParamConstant::PHONE) {
                $rule = [
                    'email_or_phone' => 'required|phone:AUTO|unique:users,phone',
                ];

                $isPhone = true;
            }

            $validator = Validator::make($data, array_merge([
                'type' => 'required|string|in:email,phone',
            ], $rule), [], ['email_or_phone' => $isPhone ? 'Телефон' : 'Эл. почта', 'type' => 'Тип']);
        } else {
            $is_user_id = true;

            $validator = Validator::make($data, [
                'user_id' => 'required|integer|exists:users,id',
            ]);
        }

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $auth = auth()->user();
        $specialist_id = $auth->specialist->id;

        if (!$is_user_id) {
            $email_or_phone = $data['email_or_phone'];
            $client = SpecialistClient::query()->where(['specialist_id' => $specialist_id])->where(function($query) use ($email_or_phone) {
                $query->orWhere(['email' => $email_or_phone, 'phone' => $email_or_phone]);
            });
        } else {
            $client = SpecialistClient::query()->where(['specialist_id' => $specialist_id, 'user_id' => $data['user_id'], 'notified' => 1]);
        }

        if ($client->exists()) {
            return $this->sendError(['message' => 'Уже существует'], Response::HTTP_FORBIDDEN);
        }

        DB::beginTransaction();

        try {
            if (!$is_user_id) {
                if ($isPhone) {
                    $client = SpecialistClient::create(['specialist_id' => $specialist_id, 'phone' => $email_or_phone]);
                } else {
                    $client = SpecialistClient::create(['specialist_id' => $specialist_id, 'email' => $email_or_phone]);
                }
            } else {
                $user = User::query()->find($data['user_id']);

                $client = SpecialistClient::query()->updateOrCreate(['specialist_id' => $specialist_id, 'user_id' => $user->id], ['email' => $user->email, 'phone' => $user->phone, 'notified' => 1]);
            }

            if (!$is_user_id && !$isPhone || $is_user_id) {
                Notification::route('mail', $client->email)->notify(new NotifyClient($auth->full_name));
            }

            DB::commit();

            return $this->sendResponse(['message' => 'Успешно уведомлено'], Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @param mixed $clientId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function aboutClient(Request $request, $clientId)
    {
        $auth = auth()->user();
        $specialist_id = $auth->specialist->id;

        if ($request->isMethod('get')) {
            $client = SpecialistClient::query()->where(['specialist_id' => $specialist_id, 'user_id' => $clientId]);

            if ($client->exists()) {
                return $this->sendResponse(['about' => $client->first()->about]);
            }

            return $this->sendError();
        }

        $data = $request->only('about');

        $validator = Validator::make($data, [
            'about' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $validated = $validator->validated();

        $client = SpecialistClient::query()->where(['specialist_id' => $specialist_id, 'user_id' => $clientId]);

        if (!$client->exists()) {
            return $this->sendError(['message' => 'Вы не можете писать эту информацию'], Response::HTTP_FORBIDDEN);
        }

        try {
            $client->first()->update(['about' => $validated['about']]);

            return $this->sendResponse(['message' => 'Успешно сохранено']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @param mixed $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteClient($id)
    {
        $client = SpecialistClient::query()->where(['id' => $id, 'specialist_id' => auth()->user()->specialist->id]);

        if ($client->exists()) {
            $client->delete();

            return $this->sendResponse(['message' => 'Клиент успешно удален']);
        }

        return $this->sendError();
    }
}
