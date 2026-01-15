<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware(['auth','role:admin'])
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if (! Auth::check()) {
            return redirect()->route('login.create');
        }

        $user = Auth::user();

        if ($user->role !== $role) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
