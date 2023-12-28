<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class BaseController extends Controller
{
    /**
     * @param int $code
     *
     * @return JsonResponse
     */
    public function sendResponse($result, $code = Response::HTTP_OK)
    {
        $response = [
            'code' => $code,
            'status' => Response::$statusTexts[$code],
            'data' => $result,
        ];

        return response()->json($response, $code);
    }

    /**
     * @param array $errorMessages
     * @param int   $code
     *
     * @return JsonResponse
     */
    public function sendError($errorMessages = ['message' => 'Не найден'], $code = Response::HTTP_NOT_FOUND)
    {
        $response = [
            'code' => $code,
            'status' => Response::$statusTexts[$code],
        ];

        if (!empty($errorMessages)) {
            $response['errors'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    /**
     * @param int    $code
     * @param string $message
     *
     * @return JsonResponse
     */
    public function sendInternalError($code = Response::HTTP_INTERNAL_SERVER_ERROR, $message = '')
    {
        return response()->json([
            'code' => $code,
            'status' => Response::$statusTexts[$code],
            'errors' => [
                'message' => empty($message) ? __('messages.sever_error') : $message,
            ],
        ], $code);
    }
}
