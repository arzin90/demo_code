<?php

namespace App\Api\V1\Controllers;

use App\Constants\ParamConstant;
use App\Models\ProgramDate;
use App\Models\ProgramUser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProgramRateController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $program = $request->route('program_id');
        $rate = $request->only('rate');
        $rate_range = implode(',', range(ParamConstant::RATE_START, ParamConstant::RATE_END));

        $validator = Validator::make(array_merge($rate, ['program' => $program]), [
            'program' => 'required|exists:programs,id',
            'rate' => sprintf('required|in:%s', $rate_range),
        ], [
            'program.exists' => 'Программа не найдена',
            'rate.in' => sprintf('Оценка может принимать следующие значения %s', $rate_range),
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $auth_user = auth()->user();

        $client_program_exists = ProgramUser::query()->where(['program_id' => $program, 'user_id' => $auth_user->id])->exists();

        $is_program_date_valid = ProgramDate::query()->where(['program_id' => $program])
            ->where('date', '<=', now()->format('Y-m-d'))
            ->where('start_time', '<=', now()->format('H:i:s'))->exists();

        if (!$client_program_exists || !$is_program_date_valid) {
            return $this->sendError(['message' => 'Вы не можете поставить оценку программе'], Response::HTTP_FORBIDDEN);
        }

        try {
            $client_rate = $auth_user->programRates()->where(['program_id' => $program]);

            if ($client_rate->exists()) {
                return $this->sendError(['message' => 'Вы уже оценивали эту программу'], Response::HTTP_FORBIDDEN);
            }

            $client_rate->updateOrCreate(['program_id' => $program], $rate);

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
        $program = $request->route('program_id');

        $validator = Validator::make(['program' => $program], [
            'program' => 'required|exists:programs,id',
        ], [
            'program.exists' => 'Программа не найдена',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $rate = auth()->user()->programRates()->where(['program_id' => $program]);

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
