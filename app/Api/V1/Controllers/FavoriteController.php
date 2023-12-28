<?php

namespace App\Api\V1\Controllers;

use App\Models\Specialist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends BaseController
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        return $this->sendResponse(Specialist::query()->with(['user', 'specialties'])->whereHas('favorite', function($query) {
            $query->where('user_id', auth()->id());
        })->paginate(15));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $specialist = $request->route('specialist');

        $validator = Validator::make(['specialist' => $specialist], [
            'specialist' => 'required|exists:specialists,id',
        ], [
            'specialist.exists' => 'Специалист не найден',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $favorites = auth()->user()->favorites();

            if ($favorites->where(['specialist_id' => $specialist])->exists()) {
                return $this->sendResponse(['message' => 'Уже в списке избранных']);
            }

            (clone $favorites)->create(['specialist_id' => $specialist]);

            return $this->sendResponse(['message' => 'Добавлено в список избранных']);
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
        $specialist = $request->route('specialist');

        $validator = Validator::make(['specialist' => $specialist], [
            'specialist' => 'required|exists:specialists,id',
        ], [
            'specialist.exists' => 'Специалист не найден',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $favorites = auth()->user()->favorites();

            if ($favorites->where(['specialist_id' => $specialist])->exists()) {
                $favorites->delete();
            }

            return $this->sendResponse(['message' => 'Удалено из списка избранных']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }
}
