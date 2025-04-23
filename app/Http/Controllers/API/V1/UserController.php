<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\OperationHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserRegisterRequest;
use App\Http\Requests\Auth\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\API\V1\UserRepositoryInterface;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;


/**
 * @OA\Info(
 *      title="API 1 Swagger",
 *      version="1.0",
 *      description="API TocDoc"
 * )
 * @OA\Server(url="http://tocdoc-api.test/")
 */

class UserController extends Controller
{
    private UserRepositoryInterface $userRepositoryInterface;
    protected ApiResponseService $apiResponseService;

    public function __construct(UserRepositoryInterface $userRepositoryInterface, ApiResponseService $apiResponseService)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
        $this->apiResponseService = $apiResponseService;
    }

    /**
     * @OA\SecurityScheme(
     *     securityScheme="bearerAuth",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     description="Enter your token in the format 'Bearer {token}'"
     * )
     * @OA\Get(
     *      path="/api/v1/users",
     *      tags={"Users"},
     *      summary="Get list of users",
     *      description="Return list of users",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/UserResource"))
     *      )
     * )
     */
    function index()
    {
        return OperationHelper::executeWithoutTransaction(function() {
            $data = $this->userRepositoryInterface->getAllUsers();
            return UserResource::collection($data);
        }, 'Users retrieved successfully', 200);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/users/{id}",
     *      tags={"Users"},
     *      summary="Get user by ID",
     *      description="Return user data",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="User ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/UserResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      )
     * )
     */
    public function show(string $userId)
    {
        return OperationHelper::executeWithoutTransaction(function() use ($userId){
            $user = $this->userRepositoryInterface->getUserById($userId);
            return new UserResource($user);
        }, 'User retrieved successfully', 200);
    }

    /**
     * @OA\Schema(
     *      schema="UserRegisterRequest",
     *      required={"name", "email", "password", "password_confirmation"},
     *      @OA\Property(property="name", type="string", example="John Cena"),
     *      @OA\Property(property="email", type="string", format="email", example="john.cena@example.com"),
     *      @OA\Property(property="password", type="string", format="password", example="123456"),
     *      @OA\Property(property="password_confirmation", type="string", format="password", example="123456")
     * )
     *
     * @OA\Post(
     *      path="/api/v1/users",
     *      tags={"Users"},
     *      summary="Create new user",
     *      description="Create new user and return user data",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UserRegisterRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User created successfully",
     *          @OA\JsonContent(ref="#/components/schemas/UserResource")
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request"
     *      )
     * )
     */
    public function store(UserRegisterRequest $request)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ];

        return OperationHelper::executeWithTransaction(function() use ($data){
            $user = $this->userRepositoryInterface->storeUser($data);
            return new UserResource($user);
        }, 'User created successfully', 201);
    }

    /**
     * * @OA\Schema(
     *     schema="UserUpdateRequest",
     *     type="object",
     *     required={"name", "email", "password", "password_confirmation"},
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         description="Name of the user"
     *     ),
     *     @OA\Property(
     *         property="email",
     *         type="string",
     *         format="email",
     *         description="Email of the user"
     *     ),
     *     @OA\Property(
     *         property="password",
     *         type="string",
     *         format="password",
     *         description="Password of the user"
     *     ),
     *     @OA\Property(
     *         property="password_confirmation",
     *         type="string",
     *         format="password",
     *         description="Password_confirmation of the user"
     *     )
     * )
     * @OA\Put(
     *      path="/api/v1/users/{id}",
     *      tags={"Users"},
     *      summary="Update user",
     *      description="Update user data",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="User ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UserUpdateRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="User updated successfully",
     *          @OA\JsonContent(ref="#/components/schemas/UserResource")
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      )
     * )
     */
    public function update(UserUpdateRequest $request, string $userId)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        return OperationHelper::executeWithTransaction(function() use ($data, $userId){
            $user = $this->userRepositoryInterface->updateUser($data, $userId);
            return new UserResource($user);
        }, 'User updated successfully',200);
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/users/{id}",
     *      tags={"Users"},
     *      summary="Delete user",
     *      description="Delete user by ID",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="User ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="User deleted successfully"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      )
     * )
     */
    public function destroy(string $userId)
    {
        return OperationHelper::executeWithTransaction(function () use ($userId) {
            $this->userRepositoryInterface->deleteUser($userId);
            return [];
        }, 'User deleted successfully',200);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/users/datatable",
     *      tags={"Users"},
     *      summary="Get users for DataTable",
     *      description="Return paginated list of users for DataTable",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="page",
     *          description="Page number",
     *          in="query",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(type="object")
     *      )
     * )
     */
    public function getUsersDataTable(Request $request)
    {
        return OperationHelper::executeWithoutTransaction(function() use ($request){
            return $this->userRepositoryInterface->getUsersQuery($request);
        },'getUsersDT Successful', 200);
    }
}
