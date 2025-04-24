<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(string $file, string $messageKey, $data = null, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => trans("$file.success"),
            'message' => trans("$file.messages.$messageKey"),
            'data' => $data,
        ], $statusCode);
    }

    public static function error(string $file, string $errorKey, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => trans("$file.errors.$errorKey"),
        ], $statusCode);
    }
}
