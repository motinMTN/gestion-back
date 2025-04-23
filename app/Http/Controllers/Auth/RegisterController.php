<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\API\V1\UserRepositoryInterface;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    private UserRepositoryInterface $userRepositoryInterface;
    private ApiResponseService $apiResponseService;

    public function __construct(UserRepositoryInterface $userRepositoryInterface, ApiResponseService $apiResponseService)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
        $this->apiResponseService = $apiResponseService;
    }

    public function register(UserRegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepositoryInterface->store($request->all());

            // Creating a token without scopes...
            $token = $user->createToken('token')->accessToken;
            $user = (new UserResource($user))->additional(['action' => 'register']);

            if ($user instanceof UserResource)
            {
                $data = [
                    'user' => $user,
                    'token' => $token
                ];

                DB::commit();
                return $this->apiResponseService->sendResponse($data, 'User created successfully', 201);
            }
        } catch (\Throwable $th) {
            $this->apiResponseService->rollback($th,'RegisterController', 'Falla en register', 'register');
            return $this->apiResponseService->sendResponse(['error' => $th->getMessage()], 'Error', 500);
        }
    }
}
