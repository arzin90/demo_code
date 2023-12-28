<?php

namespace App\Http\Middleware;

use App\Models\UserStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountAccess
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        if (in_array(auth()->user()->status_id, [UserStatus::DELETED, UserStatus::BLOCKED])) {
            $code = Response::HTTP_FORBIDDEN;

            $response = [
                'code' => $code,
                'status' => Response::$statusTexts[$code],
            ];

            return response()->json($response, $code);
        }

        return $next($request);
    }
}
