<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware(['auth','role:admin']) or ->middleware(['auth','role:admin,petugas'])
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (! Auth::check()) {
            return redirect()->route('login.create');
        }

        $user = Auth::user();

        $allowedRoles = array_map(function ($r) {
            return strtolower(trim($r));
        }, $roles);

        // Check if user's role is in allowed roles
        $userRole = strtolower(trim($user->role ?? ''));
        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'Unauthorized - Insufficient permissions');
        }

        return $next($request);
    }
}
