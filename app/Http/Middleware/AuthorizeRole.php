<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthorizeRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $payload = $request->attributes->get('auth_payload');
        $role = $payload?->get('role');

        if (! in_array($role, $roles, true)) {
            throw new HttpException(403, 'Forbidden');
        }

        return $next($request);
    }
}
