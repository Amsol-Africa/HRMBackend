<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class RequestResponse
{
    public static function badRequest(string $message): JsonResponse
    {
        return response()->json(['message' => $message], 400);
    }

    public static function success($data = null, string $message = 'Success'): JsonResponse
    {
        return response()->json(['message' => $message, 'data' => $data]);
    }
}