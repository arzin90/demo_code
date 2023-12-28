<?php

namespace App\Http\Middleware;

use App\Constants\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SpecialistAccess
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        if (empty(auth()->user()->specialist()) || !auth()->user()->specialist()->whereIn('status', [Status::ACTIVE, Status::FOR_CHECKING])->exists()) {
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
