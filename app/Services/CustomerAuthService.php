<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CustomerAuthService
{
    public function login(string $username, string $password): array
    {
        $customer = Customer::query()->where('username', $username)->first();

        if (! $customer || ! Hash::check($password, $customer->password_hash)) {
            throw new HttpException(401, 'Unauthorized');
        }

        $token = JWTAuth::claims([
            'user_type' => 'customer',
            'role' => 'Customer',
            'token_version' => $customer->token_version ?? 0,
        ])->fromUser($customer);

        return [
            'customerId' => $customer->customer_id,
            'fullName' => $customer->full_name,
            'loyaltyPoints' => (int) ($customer->loyalty_points ?? $customer->points ?? 0),
            'role' => 'Customer',
            'token' => $token,
        ];
    }

    public function register(array $payload): void
    {
        Customer::query()->create([
            'username' => $payload['username'],
            'password_hash' => Hash::make($payload['password']),
            'full_name' => $payload['fullName'],
            'phone_number' => $payload['phoneNumber'],
            'email' => $payload['email'],
            'membership_level' => 'Standard',
            'loyalty_points' => 0,
            'token_version' => 0,
            'created_at' => now(),
        ]);
    }
}
