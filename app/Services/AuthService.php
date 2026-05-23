<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function login(string $username, string $password): array
    {
        $user = User::query()->where('username', $username)->first();

        if (! $user || ! Hash::check($password, $user->password_hash)) {
            throw new HttpException(401, 'Unauthorized');
        }

        if (! in_array($user->role, ['Admin', 'Staff'], true)) {
            throw new HttpException(403, 'Forbidden');
        }

        $token = JWTAuth::fromUser($user, [
            'user_type' => 'user',
            'role' => $user->role,
            'token_version' => $user->token_version ?? 0,
        ]);

        return [
            'userId' => $user->user_id,
            'fullName' => $user->full_name,
            'role' => $user->role,
            'token' => $token,
        ];
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword): void
    {
        if (! Hash::check($currentPassword, $user->password_hash)) {
            throw new HttpException(401, 'Unauthorized');
        }

        $user->password_hash = Hash::make($newPassword);

        if (array_key_exists('token_version', $user->getAttributes())) {
            $user->token_version = (int) ($user->token_version ?? 0) + 1;
        }

        $user->save();
    }
}
