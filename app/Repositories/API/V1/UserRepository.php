<?php

namespace App\Repositories\API\V1;

use App\Helpers\LogHelper;
use App\Http\Resources\UserResourceDT;
use App\Interfaces\API\V1\UserRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\FilterService;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    protected $filterService;

    public function __construct(FilterService $filterService)
    {
        $this->filterService = $filterService;
    }

    public function getAllUsers() : Collection
    {
        return LogHelper::executeWithLogging(function () {
            return User::all();
        }, "getAll", "UserRepository");
    }

    public function getUserById(string $userId) : User
    {
        return LogHelper::executeWithLogging(function() use ($userId){
            return User::findOrFail($userId);
        }, "getById", "UserRepository");
    }

    public function storeUser(array $data) : User
    {
        return LogHelper::executeWithLogging(function() use ($data){
            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
        }, "store", "UserRepository");
    }

    public function updateUser(array $data, string $userId) : User
    {
        return LogHelper::executeWithLogging(function() use ($data,$userId){
            $user = User::findOrFail($userId);
            $user->update($data);
            return $user;
        }, "update", "UserRepository");
    }

    public function deleteUser(string $userId) : User
    {
        return LogHelper::executeWithLogging(function() use ($userId){
            $user = User::findOrFail($userId);
            $user->delete();
            return $user;
        }, "delete", "UserRepository");
    }

    public function getUsersQuery(Request $request) : array
    {
         return LogHelper::executeWithLogging(function() use($request){
            $query = User::query();

            // Definir campos que se pueden filtrar y sus operadores
            $filterableFields = [
                'name' => 'like',
                'email' => 'like'
            ];

            // Aplicar filtros
            $query = $this->filterService->applyFilters($query, $request, $filterableFields);

            // Aplicar ordenamiento
            $query = $this->filterService->applySorting($query, $request);

            // Contar total de registros antes de la paginación
            $totalRecords = $query->count();

            // Aplicar paginación y obtener resultados
            $users = $this->filterService->applyPagination($query, $request);

            $users->each(function ($user) {
                $user->action = '<a href="user/edit" class="edit btn btn-success btn-sm">Edit</a>
                             <a href="user/delete" class="delete btn btn-danger btn-sm">Delete</a>';
            });

            return [
                'data' => UserResourceDT::collection($users),
                'totalRecords' => $totalRecords,
            ];
        }, "getUsersQuery", "UserRepository");
    }

    public function validateCredentials($credentials) : bool
    {
        return LogHelper::executeWithLogging(function() use ($credentials){
            return auth()->attempt($credentials);
        }, "validateCredentials", "UserRepository", );
    }

    public function getAuthUser(): Authenticatable
    {
        return LogHelper::executeWithLogging(function(){
            return auth()->user();
        },"getAuthUser","UserRepository");
    }

    public function getAuthGuardApiUser() : User
    {
        return LogHelper::executeWithLogging(function(){
            return auth()->guard('api')->user();
        },"getAuthGuardApiUser","UserRepository");
    }

    public function getUserData(User $user): array
    {
        return LogHelper::executeWithLogging(function() use ($user){
            if ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            }else{
                throw new Exception('Error getUserData, User not found');
            }
        },"getUserData","UserRepository");
    }

    public function generateToken(User $user): string
    {
        return LogHelper::executeWithLogging(function() use ($user){
            $user->tokens()->delete(); // Revoca todos los tokens anteriores del usuario
            $token = $user->createToken('token')->accessToken;
            return $token;
        },"generateToken","UserRepository");
    }

    public function getGeneralUserData() : array
    {
        return LogHelper::executeWithLogging(function(){
            $user = $this->getAuthUser();

            $generalUserData = [
                'user' => $this->getUserData($user),
                'token' => $this->generateToken($user),
            ];

            return $generalUserData;
        },"getGeneralUserData","UserRepository");
    }

    public function revokeToken() : void
    {
        LogHelper::executeWithLogging(function(){
            $user = $this->getAuthGuardApiUser();
            $token = $user->token();

            $token->revoke();
        },"revokeToken","UserRepository");
    }
}
