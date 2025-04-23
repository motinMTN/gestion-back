<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Interfaces\API\V1\PasswordResetRepositoryInterface;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    protected PasswordResetRepositoryInterface $passwordResetRepository;
    protected ApiResponseService $apiResponseService;

    // todos los controladores deben tener constructor a partir de su interface de repositorio
    public function __construct(PasswordResetRepositoryInterface $passwordResetRepository, ApiResponseService $apiResponseService)
    {
        $this->passwordResetRepository = $passwordResetRepository;
        $this->apiResponseService = $apiResponseService;
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        // Obtener el dominio desde el cual se solicitó el restablecimiento para ejemplo
        $domain = $request->input('domain'); //'http://distribuidor1.localhost:5173'; //http://tocdoc-api.test/, cambiarlo por el que viene en el request

        // Intentar enviar el enlace de reseteo de contraseña
        $status = $this->passwordResetRepository->forgotPassword(
            $request->only('email'),
            $domain
        );

        // Verificar el estado y devolver una respuesta JSON usando el ApiResponseService.php
        if ($status == Password::RESET_LINK_SENT) {
            return $this->apiResponseService->sendResponse([], 'Te hemos enviado un correo electrónico con un enlace de recuperación de contraseña.', 200);
        } else {
            return $this->apiResponseService->sendResponse([], __($status), 400);
        }
    }
}
