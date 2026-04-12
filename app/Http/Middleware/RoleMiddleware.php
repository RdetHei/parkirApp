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
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Satu parameter "admin,petugas" dari role:admin,petugas harus dipecah jadi beberapa role (OR).
        $allowedRoles = [];
        foreach ($roles as $segment) {
            foreach (preg_split('/\s*,\s*/', (string) $segment, -1, PREG_SPLIT_NO_EMPTY) as $part) {
                $allowedRoles[] = strtolower(trim($part));
            }
        }
        $allowedRoles = array_values(array_unique($allowedRoles));

        $userRole = strtolower(trim($user->role ?? ''));
        if (! in_array($userRole, $allowedRoles, true)) {
            abort(403, 'Unauthorized - Insufficient permissions');
        }

        return $next($request);
    }
}
