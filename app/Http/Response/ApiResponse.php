<?php

namespace App\Http\Response;

class ApiResponse{
    public static function success($message = '', $statusCode = 200, $data = []) {
        return response()->json([
            'message' => $message,
            'statusCode' => $statusCode,
            'success' => true,
            'data' => $data
        ], $statusCode);
    }

    public static function error($message = '', $statusCode = 200, $data = []) {
        return response()->json([
            'message' => $message,
            'statusCode' => $statusCode,
            'success' => false,
            'errors' => $data
        ], $statusCode);
    }
}
