<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthorizeRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $payload = $request->attributes->get('auth_payload');
        $user = $request->attributes->get('auth_user');

        $role = $payload?->get('role');

        if (! is_string($role) || $role === '') {
            if ($user instanceof User) {
                $role = $user->role;
            } elseif ($user instanceof Customer) {
                $role = 'Customer';
            } else {
                throw new HttpException(401, 'Unauthorized');
            }
        }

        if (! in_array($role, $roles, true)) {
            throw new HttpException(403, 'Forbidden');
        }

        return $next($request);
    }
}
