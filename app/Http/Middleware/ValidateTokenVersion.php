<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ValidateTokenVersion
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->attributes->get('auth_user');
        $payload = $request->attributes->get('auth_payload');

        if (! $user || ! $payload) {
            throw new HttpException(401, 'Unauthorized');
        }

        $tokenVersion = $payload->get('token_version');

        if (array_key_exists('token_version', $user->getAttributes()) && (int) ($user->token_version ?? 0) !== (int) $tokenVersion) {
            throw new HttpException(401, 'Unauthorized');
        }

        return $next($request);
    }
}
