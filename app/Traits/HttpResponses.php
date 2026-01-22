<?php

namespace App\Traits;
use Illuminate\Http\JsonResponse;
use Laravel\Sanctum\PersonalAccessToken;

trait HttpResponses {
    protected function success($message = null, $data = [], $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'Request successful',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function error($message = null, $data = [], $code = 400): JsonResponse
    {
        return response()->json([
            'status' => 'Error has occurred',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function authUserByToken(): mixed
    {
        return PersonalAccessToken::findToken(
            request()->bearerToken()
        )?->tokenable;
    }
}
