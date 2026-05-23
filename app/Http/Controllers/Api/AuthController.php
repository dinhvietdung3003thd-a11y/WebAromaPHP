<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\CustomerLoginRequest;
use App\Http\Requests\Auth\CustomerRegisterRequest;
use App\Models\Customer;
use App\Models\User;
use App\Services\AuthService;
use App\Services\CustomerAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly CustomerAuthService $customerAuthService,
    ) {
    }

    public function login(AdminLoginRequest $request): JsonResponse
    {
        return response()->json($this->authService->login(
            $request->string('username')->toString(),
            $request->string('password')->toString(),
        ));
    }

    public function customerLogin(CustomerLoginRequest $request): JsonResponse
    {
        return response()->json($this->customerAuthService->login(
            $request->string('username')->toString(),
            $request->string('password')->toString(),
        ));
    }

    public function customerRegister(CustomerRegisterRequest $request): JsonResponse
    {
        $this->customerAuthService->register($request->validated());

        return response()->json(['message' => 'Register successful']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->attributes->get('auth_user');
        $payload = $request->attributes->get('auth_payload');

        $role = $payload?->get('role');

        if (! is_string($role) || $role === '') {
            if ($user instanceof User) {
                $role = $user->role;
            } elseif ($user instanceof Customer) {
                $role = 'Customer';
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
        }

        if ($user instanceof User) {
            return response()->json([
                'userId' => $user->user_id,
                'fullName' => $user->full_name,
                'role' => $role,
            ]);
        }

        if ($user instanceof Customer) {
            return response()->json([
                'customerId' => $user->customer_id,
                'fullName' => $user->full_name,
                'loyaltyPoints' => (int) ($user->loyalty_points ?? $user->points ?? 0),
                'role' => $role,
            ]);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->attributes->get('auth_user');

        if (! $user instanceof User) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $this->authService->changePassword(
            $user,
            $request->string('currentPassword')->toString(),
            $request->string('newPassword')->toString(),
        );

        return response()->json(['message' => 'Password changed successfully']);
    }
}
