<?php

namespace App\Api\V1\Controllers;

use App\Constants\ParamConstant;
use App\Constants\Status;
use App\Models\Specialist;
use App\Models\SpecialistClient;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserStatus;
use App\Models\VerifyCode;
use App\Notifications\ForgotPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\PhoneNumber;

/**
 * @group Register
 *
 * Методы для работы с регистрацией
 */
class AuthController extends BaseController
{
    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['checkRegisteredEmail', 'registerOrLogin', 'checkCode', 'forgotPassword', 'resetPassword']]);
    }

    /**
     * @return JsonResponse
     */
    public function checkRegisteredEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => sprintf('nullable|required_if:type,email|required_without:phone|email:filter|max:%d', ParamConstant::EMAIL_MAX),
            'phone' => 'nullable|required_if:type,phone|required_without:email|phone:RU',
            'type' => 'required|in:email,phone',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $type = $request->get('type', 'email');
        $email = $request->get('email');
        $phone = $request->get('phone');

        if ($phone && $type == 'phone') {
            $condition = ['phone' => (string) PhoneNumber::make($phone)->ofCountry('RU')];
        } else {
            $condition = ['email' => $email];
        }

        $user = User::query()->where($condition)->first();

        if (!empty($user)) {
            switch ($user->status_id) {
                case UserStatus::ACTIVE:
                    return $this->sendResponse([$type => __("auth.existing_{$type}"), 'avatar_url' => $user->avatar_url], Response::HTTP_ACCEPTED);
                case UserStatus::BLOCKED:
                    return $this->sendError([$type => __("auth.blocked_{$type}")], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        return $this->sendResponse([$type => __("auth.new_{$type}", $condition)]);
    }

    /**
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function registerOrLogin(Request $request)
    {
        $data = $request->only(['email', 'phone', 'type', 'password', 'device_token']);

        $validator = Validator::make($data, [
            'email' => sprintf('nullable|required_if:type,email|required_without:phone|email:filter|max:%d', ParamConstant::EMAIL_MAX),
            'phone' => 'nullable|required_if:type,phone|required_without:email|phone:RU',
            'type' => 'required|in:email,phone',
            'password' => sprintf('required|string|min:%d|max:%d', ParamConstant::PASSWORD_MIN, ParamConstant::PASSWORD_MAX),
            'device_token' => 'nullable|unique:user_devices,token',
        ], [
            'device_token.unique' => 'Токен устройства уже существует.',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $type = $request->get('type', 'email');
        $email = $request->get('email');
        $phone = $request->get('phone');

        if ($phone && $type == 'phone') {
            $condition = ['phone' => (string) PhoneNumber::make($phone)->ofCountry('RU')];
        } else {
            $condition = ['email' => $email];
        }

        $user = User::query()->where($condition)->first();

        if (!empty($user)) {
            switch ($user->status_id) {
                case UserStatus::ACTIVE:
                    if (!$token = auth()->attempt(Arr::only($validator->validated(), [$type, 'password']))) {
                        return $this->sendError(['invalid_credentials' => __("auth.invalid_{$type}_or_password")], Response::HTTP_UNAUTHORIZED);
                    }

                    if (!empty($data['device_token'])) {
                        auth()->user()->devices()->create(['token' => $data['device_token']]);
                    }

                    return $this->generateToken($token);
                case UserStatus::BLOCKED:
                    return $this->sendError([$type => __("auth.blocked_{$type}")], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        try {
            DB::beginTransaction();

            $code = rand(ParamConstant::VERIFICATION_CODE_START, ParamConstant::VERIFICATION_CODE_END);
            $token = Str::random(ParamConstant::TOKEN_LENGTH);

            $verifyCode = VerifyCode::query()->updateOrCreate($condition, [
                'token' => $token,
                'code' => $code,
                'type' => $type,
            ]);

            User::query()->updateOrCreate($condition, [
                'status_id' => UserStatus::PENDING,
                'password' => Hash::make($data['password']),
            ]);

            $verifyCode->notify(new VerifyEmail($code));

            DB::commit();

            return $this->sendResponse(compact('token'), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        DB::rollBack();

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     */
    public function checkCode(Request $request)
    {
        $data = $request->all();

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

        $password = $request->get('password');

        if (empty($password)) {
            return $this->sendResponse([], Response::HTTP_ACCEPTED);
        }

        $validator = Validator::make($data, [
            'password' => sprintf('required|string|min:%d|max:%d', ParamConstant::PASSWORD_MIN, ParamConstant::PASSWORD_MAX),
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();

            $verifyCodeByToken = VerifyCode::query()->where(['token' => $token]);
            $params = $verifyCodeByToken->first(['email', 'phone', 'type']);

            $verifyCodeByToken->delete();
            $type = $params->type;
            $email = $params->email;
            $phone = $params->phone;

            if ($type == 'phone') {
                $condition = [
                    'phone' => $phone,
                ];
            } else {
                $condition = [
                    'email' => $email,
                ];
            }

            User::query()->updateOrCreate($condition, [
                'status_id' => UserStatus::ACTIVE,
                'verified_at' => now(),
            ]);

            if (!$token = auth()->attempt(compact($params->type, 'password'))) {
                return $this->sendError(['invalid_credentials' => __("auth.invalid_{$type}_or_password")], Response::HTTP_UNAUTHORIZED);
            }

            $specialistClients = SpecialistClient::query()->with('specialist.user')->where($condition)->get();
            $auth = auth()->user();

            if (!empty($specialistClients)) {
                foreach ($specialistClients as $specialistClient) {
                    $specialist_user_info = $specialistClient->specialist->user;
                    $device_tokens = UserDevice::query()->where('user_id', $specialist_user_info->id)->pluck('token')->toArray();

                    $FCM_res = \App\Helper\Notification::sendNotification($device_tokens, 'Пользователь уже в системе', $auth->{$type}, [
                        'form' => [
                            'client_id' => $specialistClient->id,
                            'user_id' => $auth->id,
                            'first_name' => $auth->first_name,
                            'last_name' => $auth->last_name,
                            'email' => $auth->email,
                            'phone' => $auth->phone,
                        ],
                    ]);

//                    Notification::route('mail', $specialist_user_info->email)->notify(new NotifySpecialistForClientRegistration($specialistClient->pseudonym ?: $specialistClient->email));
                }
            }

            DB::commit();

            return $this->generateToken($token);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        DB::rollBack();

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     */
    public function afterConfirm(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $data = $request->only('first_name', 'last_name', 'avatar');

        $validator = Validator::make($data, [
            'first_name' => 'required|min:2|max:255',
            'last_name' => 'nullable|min:2|max:255',
            'avatar' => 'nullable|mimes:jpg,bmp,png,jpeg|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();

        try {
            $user->update($data);

            if ($request->has('avatar') && $request->file('avatar')) {
                $user->clearMediaCollection('avatar');
                $user->addMedia($request->file('avatar'))
                    ->toMediaCollection('avatar');
            }

            DB::commit();

            return $this->sendResponse(['message' => 'Профиль успешно создан']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
        }

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        UserDevice::query()->where(['user_id' => auth()->id(), 'token' => $request->get('device_token')])->delete();
        auth()->logout();

        return $this->sendResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @return JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $data = $request->only(['email', 'phone', 'type']);

        $validator = Validator::make($data, [
            'email' => sprintf('nullable|required_if:type,email|required_without:phone|email:filter|max:%d', ParamConstant::EMAIL_MAX),
            'phone' => 'nullable|required_if:type,phone|required_without:email|phone:RU',
            'type' => 'required|in:email,phone',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $type = $request->get('type', 'email');
        $email = $request->get('email');
        $phone = $request->get('phone');

        if ($phone && $type == 'phone') {
            $condition = ['phone' => (string) PhoneNumber::make($phone)->ofCountry('RU')];
        } else {
            $condition = ['email' => $email];
        }

        $user = User::query()->where($condition)->first();
        $token = null;

        if (!empty($user)) {
            $token = Str::random(ParamConstant::TOKEN_LENGTH);
            $code = rand(ParamConstant::VERIFICATION_CODE_START, ParamConstant::VERIFICATION_CODE_END);

            $verifyCode = VerifyCode::query()->updateOrCreate($condition, [
                'token' => $token,
                'code' => $code,
                'type' => $type,
            ]);

            $verifyCode->notify(new ForgotPassword($code));
        }

        return $this->sendResponse(compact('token'));
    }

    /**
     * @return JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => sprintf('required|string|size:%d|exists:verify_codes', ParamConstant::TOKEN_LENGTH),
            'code' => sprintf('required|numeric|digits:%d', ParamConstant::CODE_LENGTH),
            'password' => sprintf('required|string|min:%d|max:%d', ParamConstant::PASSWORD_MIN, ParamConstant::PASSWORD_MAX),
        ], [
            'token.exists' => __('auth.invalid_token'),
        ]);

        $is_valid_token = VerifyCode::self()->isValidToken($token = $request->get('token'));
        $check_code = VerifyCode::self()->checkCode($token, $request->get('code'));

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
            $email = $params->email;
            $phone = $params->phone;
            $password = $request->get('password');

            if ($type == 'phone') {
                $condition = [
                    'phone' => $phone,
                ];
            } else {
                $condition = [
                    'email' => $email,
                ];
            }

            User::query()->where($condition)->update([
                'password' => Hash::make($password),
            ]);

            if (!$token = auth()->attempt(compact($type, 'password'))) {
                return $this->sendError(['invalid_credentials' => __("auth.invalid_{$type}_or_password")], Response::HTTP_UNAUTHORIZED);
            }

            DB::commit();

            return $this->sendResponse(['message' => 'Пароль успешно изменен']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->generateToken(auth()->refresh());
    }

    /**
     * @return JsonResponse
     */
    public function user()
    {
        return $this->sendResponse(User::self()->getAuthUser());
    }

    /**
     * @return JsonResponse
     */
    public function destroy()
    {
        /** @var User $auth */
        $auth = auth()->user();

        try {
            DB::beginTransaction();
            /** @var Specialist $specialist */
            $specialist = $auth->specialist()->first();

            if (!empty($specialist)) {
                $specialist->clearMediaCollection('passport');
                $specialist->clearMediaCollection('document');

                $educations = $specialist->educations();
                $educations->each(function($fileAdder) {
                    $fileAdder->clearMediaCollection('diploma');
                });
                $educations->delete();

                $programs = $specialist->programs();
                $programs->each(function($fileAdder) {
                    $fileAdder->clearMediaCollection('gallery');
                });
                $programs->delete();
                $specialist->update(['status' => Status::DELETED]);

                $auth->update(['status_id' => UserStatus::DELETED]);
            } else {
                $auth->clearMediaCollection('avatar');
                $auth->update([
                    'status_id' => UserStatus::DELETED,
                    'first_name' => null,
                    'last_name' => null,
                    'patronymic_name' => null,
                    'email' => null,
                    'phone' => null,
                    'location_id' => null,
                    'address' => null,
                    'password' => null,
                    'gender' => null,
                    'b_day' => null,
                    'content' => null,
                    'url' => null,
                ]);
            }

            $auth->devices()->delete();
            auth()->logout();

            DB::commit();

            return $this->sendResponse('', Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @return JsonResponse
     */
    public function mute()
    {
        auth()->user()->update(['is_muted' => 1]);

        return $this->sendResponse(['message' => 'Уведомления отключены']);
    }

    /**
     * @return JsonResponse
     */
    public function unmute()
    {
        auth()->user()->update(['is_muted' => 0]);

        return $this->sendResponse(['message' => 'Уведомления включены']);
    }

    /**
     * @return JsonResponse
     */
    public function lastVisit()
    {
        auth()->user()->update(['last_visit' => date('Y-m-d H:i:s')]);

        return $this->sendResponse([], Response::HTTP_ACCEPTED);
    }

    /**
     * @return JsonResponse
     */
    protected function generateToken($token)
    {
        return $this->sendResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL(),
            'user' => User::self()->getAuthUser(),
        ]);
    }
}
