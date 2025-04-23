<?php

namespace App\Helpers;

use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;

class LogHelper
{
    public static function executeWithLogging(callable $callback, string $methodName, string $repositoryName, callable $rollbackCallback = null)
    {
        try {
            return $callback();
        } catch (\Throwable $th) {
            // Si ocurre un error, primero ejecutamos la lógica de reversión si está definida
            if ($rollbackCallback) {
                try {
                    $rollbackCallback();
                } catch (\Throwable $rollbackTh) {
                    self::log($rollbackTh, $methodName . " (Rollback)", $repositoryName);
                }
            }

            self::log($th, $methodName, $repositoryName);

            // Construye un mensaje de error más detallado
            $errorMessage = "Error in {$repositoryName}::{$methodName}: " . $th->getMessage();
            $errorDetails = [
                'line' => $th->getLine(),
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
            ];

            // Arroja una nueva excepción con el mensaje detallado y los errores
            throw new CustomException($errorMessage, $errorDetails, 0, $th);
        }
    }

    public static function log(\Throwable $th, $function, $class)
    {
        Log::error([
            'line' => $th->getLine(),
            'message' => $th->getMessage(),
            'file' => $th->getFile(),
            'function' => $function,
            'class' => $class,
        ]);
    }
}
