<?php

namespace App\Api\V1\Controllers;

use App\Constants\Status;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SpecialtyController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->sendResponse(Specialty::query()->where(['status' => Status::ACTIVE])->paginate(20));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->only('name');

        $validator = Validator::make($data, [
            'name' => 'required|min:2|max:255|unique:specialties,name',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $auth = auth()->user();

            if (!empty($auth->specialist)) {
                $data['requested_by'] = auth()->user()->specialist->id;
            } else {
                $data['requested_by_user'] = $auth->id;
            }

            $specialty = new Specialty($data);

            $specialty->save();

            return $this->sendResponse($specialty->refresh());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this->sendInternalError();
    }
}
