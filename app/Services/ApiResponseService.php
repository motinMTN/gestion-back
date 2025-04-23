<?php

namespace App\Services;

class ApiResponseService
{
    public static function sendResponse($result, $message = '', $code = 200, $errors = [])
    {
        if ($code === 204) {
            return response()->noContent();
        }

        $isSuccess = $code >= 200 && $code < 300;

        $response = [
            'success' => $isSuccess,
            'data' => $isSuccess ? $result : [],
            'errors' => $errors,
        ];

        if (!empty($message)) {
            $response['message'] = $message;
        }

        return response()->json($response, $code);
    }
}
