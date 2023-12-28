<?php

namespace App\Api\V1\Controllers;

use App\Constants\ParamConstant;
use App\Models\SpecialistClient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RateController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $specialist = $request->route('specialist_id');
        $rate = $request->only('rate');
        $rate_range = implode(',', range(ParamConstant::RATE_START, ParamConstant::RATE_END));

        $validator = Validator::make(array_merge($rate, ['specialist' => $specialist]), [
            'specialist' => 'required|exists:specialists,id',
            'rate' => sprintf('required|in:%s', $rate_range),
        ], [
            'specialist.exists' => 'Специалист не найден',
            'rate.in' => sprintf('Оценка может принимать следующие значения %s', $rate_range),
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $auth_user = auth()->user();

        $client_specialist_exists = SpecialistClient::query()->where(['specialist_id' => $specialist, 'user_id' => $auth_user->id])->exists();

        if (!$client_specialist_exists) {
            return $this->sendError(['message' => 'Вы не можете поставить оценку специалисту'], Response::HTTP_FORBIDDEN);
        }

        try {
            $client_rate = $auth_user->specialistRates()->where(['specialist_id' => $specialist]);

            if ($client_rate->exists()) {
                return $this->sendError(['message' => 'Вы уже оценивали этого специалиста'], Response::HTTP_FORBIDDEN);
            }

            $client_rate->updateOrCreate(['specialist_id' => $specialist], $rate);

            return $this->sendResponse(['message' => 'Ваша оценка принята']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $specialist = $request->route('specialist_id');

        $validator = Validator::make(['specialist' => $specialist], [
            'specialist' => 'required|exists:specialists,id',
        ], [
            'specialist.exists' => 'Специалист не найден',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $rate = auth()->user()->specialistRates()->where(['specialist_id' => $specialist]);

            if ($rate->exists()) {
                $rate->delete();

                return $this->sendResponse(['message' => 'Оценка успешно удален']);
            }

            return $this->sendError();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }
}
