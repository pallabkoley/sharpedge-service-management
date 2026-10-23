<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $user->load('role');

        if (!$user->role) {
            abort(403, 'Unauthorized access.');
        }

        $userRole = strtolower(trim($user->role->name));
        $requiredRole = strtolower(trim($role));

        if ($userRole !== $requiredRole) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}