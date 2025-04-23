<?php

namespace App\Interfaces\API\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function getAllUsers() : Collection;
    public function getUserById(string $id) : User;
    public function storeUser(array $data) : User;
    public function updateUser(array $data, string $userId) : User;
    public function deleteUser(string $id) : User;
    public function getUsersQuery(Request $request) : array;
    public function validateCredentials(array $credentials) : bool;
    public function getGeneralUserData() : array;
    public function revokeToken() : void;
}
