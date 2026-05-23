<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (! $token) {
            throw new HttpException(401, 'Token not provided');
        }

        try {
            JWTAuth::setToken($token);
            $payload = JWTAuth::getPayload();

           $userType = $payload->get('user_type');
            $subjectId = $payload->get('sub');

            $user = match ($userType) {
                'customer' => Customer::query()->find($subjectId),
                'user' => User::query()->find($subjectId),
                default => null,
            };
        } catch (Throwable $e) {
    throw new HttpException(401, $e->getMessage());
}

        if (! $user) {
    throw new HttpException(401, 'User not found. user_type=' . $userType . ', sub=' . $subjectId);
}

        $request->attributes->set('auth_user', $user);
        $request->attributes->set('auth_payload', $payload);

        return $next($request);
    }
}