<?php

namespace App\Helpers;

use App\Exceptions\CustomException;
use Illuminate\Support\Facades\DB;
use Throwable;
use App\Services\ApiResponseService;

class OperationHelper
{
    public static function executeWithTransaction(callable $callback, $message, $code = 200)
    {
        $apiResponseService = new ApiResponseService();

        DB::beginTransaction();

        try {
            $result = $callback();
            DB::commit();
            return $apiResponseService->sendResponse($result, $message, $code);
        } catch (Throwable $th) {
            DB::rollBack();

            // Inicializa un array de errores vacío
            $errors = [];

            // Verifica si la excepción es de tipo CustomException y tiene detalles de errores
            if ($th instanceof CustomException) {
                $errors = $th->errors;
            }

            return $apiResponseService->sendResponse([], $th->getMessage(), 500, $errors);
        }
    }

    public static function executeWithoutTransaction(callable $callback, $message, $code = 200)
    {
        $apiResponseService = new ApiResponseService();

        try {
            $result = $callback();
            return $apiResponseService->sendResponse($result, $message, $code);
        } catch (Throwable $th) {
            // Inicializa un array de errores vacío
            $errors = [];

            // Verifica si la excepción es de tipo CustomException y tiene detalles de errores
            if ($th instanceof CustomException) {
                $errors = $th->errors;
            }

            return $apiResponseService->sendResponse([], $th->getMessage(), 500, $errors);
        }
    }
}
