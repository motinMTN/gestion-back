<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Interfaces\API\V1\PasswordResetRepositoryInterface;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    protected PasswordResetRepositoryInterface $passwordResetRepository;
    protected ApiResponseService $apiResponseService;

    // todos los controladores deben tener constructor a partir de su interface de repositorio
    public function __construct(PasswordResetRepositoryInterface $passwordResetRepository, ApiResponseService $apiResponseService)
    {
        $this->passwordResetRepository = $passwordResetRepository;
        $this->apiResponseService = $apiResponseService;
    }

    public function reset(ResetPasswordRequest $request)
    {
        // Intentar resetear la contraseña usando el repositorio
        $status = $this->passwordResetRepository->resetPassword($request->only(
            'email',
            'password',
            'password_confirmation',
            'token'
        ));

        // Verificar el estado y devolver una respuesta JSON usando el ApiResponseService.php
        if ($status == Password::PASSWORD_RESET) {
            return $this->apiResponseService->sendResponse([], 'Your password has been reset.', 200);
        } else {
            return $this->apiResponseService->sendResponse([], __($status), 400);
        }
    }
}
