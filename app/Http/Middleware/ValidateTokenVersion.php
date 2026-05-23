<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ValidateTokenVersion
{
    public function handle(Request $request, Closure $next)
    {
        $payload = $request->attributes->get('auth_payload');
        $user = $request->attributes->get('auth_user');

        if (! $payload || ! ($user instanceof User || $user instanceof Customer)) {
            throw new HttpException(401, 'Unauthorized');
        }

        $tokenVersion = $payload->get('token_version');

        if ($tokenVersion !== null && (int) $tokenVersion !== (int) ($user->token_version ?? 0)) {
            throw new HttpException(401, 'Token version mismatch');
        }

        return $next($request);
    }
}
