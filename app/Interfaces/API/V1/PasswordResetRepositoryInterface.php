<?php

namespace App\Interfaces\API\V1;

interface PasswordResetRepositoryInterface
{
    public function forgotPassword(array $credentials, string $domain);
    public function resetPassword(array $credentials);
}
