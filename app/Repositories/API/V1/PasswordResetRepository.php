<?php

namespace App\Repositories\API\V1;

use App\Helpers\LogHelper;
use App\Interfaces\API\V1\PasswordResetRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetRepository implements PasswordResetRepositoryInterface
{
    function forgotPassword(array $credentials, string $domain) : string
    {
        return LogHelper::executeWithLogging(function() use($credentials,$domain){
            return Password::sendResetLink(
                $credentials,
                function ($user, $token) use ($domain) {
                    $user->sendCustomPasswordResetNotification($token, $domain);
                }
            );
        },"forgotPassword", "PasswordResetRepository");
    }

    public function resetPassword(array $credentials): string
    {
        return LogHelper::executeWithLogging(function() use($credentials){
            return Password::reset(
                $credentials,
                function (User $user, string $password) {
                    $this->resetUserPassword($user, $password);
                }
            );
        },"resetPassword", "PasswordResetRepository");
    }

    protected function resetUserPassword(User $user, string $password): void
    {
        LogHelper::executeWithLogging(function() use ($user,$password){
            $user->password = Hash::make($password);
            $user->setRememberToken(Str::random(60));
            $user->save();
        }, "resetUserPassword", "PasswordResetRepository");
    }
}
