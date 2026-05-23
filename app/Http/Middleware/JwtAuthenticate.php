<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $payload = JWTAuth::parseToken()->getPayload();
        } catch (JWTException) {
            throw new HttpException(401, 'Unauthorized');
        }

        if (! $user) {
            throw new HttpException(401, 'Unauthorized');
        }

        $request->attributes->set('auth_user', $user);
        $request->attributes->set('auth_payload', $payload);

        return $next($request);
    }
}
