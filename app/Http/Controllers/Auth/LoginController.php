<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\OperationHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserLoginRequest;
use App\Interfaces\API\V1\UserRepositoryInterface;
use App\Services\ApiResponseService;

class LoginController extends Controller
{
    protected UserRepositoryInterface $userRepository;
    protected ApiResponseService $apiResponseService;

    public function __construct(UserRepositoryInterface $userRepository, ApiResponseService $apiResponseService)
    {
        $this->userRepository = $userRepository;
        $this->apiResponseService = $apiResponseService;
    }

    public function login(UserLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $isAuthenticated = $this->userRepository->validateCredentials($credentials);

        if ($isAuthenticated) {
            return OperationHelper::executeWithoutTransaction(function() {
                return $this->userRepository->getGeneralUserData();
            },'Login correcto');
        } else {
            $errors = [
                'credentials' => ['Credenciales erroneas']
            ];
            return $this->apiResponseService->sendResponse([], 'Error de inicio de sesión', 401, $errors);
        }
    }

    public function logout()
    {
        return OperationHelper::executeWithoutTransaction(function(){
            return $this->userRepository->revokeToken();
        },'Logout successfully');
    }
}
