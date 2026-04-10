<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPetugasArea
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Jika user adalah petugas dan memiliki id_area
        if ($user && $user->role === 'petugas' && $user->id_area) {
            // Dapatkan id_area dari request (misal dari parameter route)
            // Asumsi parameter route untuk id_area adalah 'area_id' atau 'id_area'
            $requestedAreaId = $request->route('area_id') ?? $request->route('id_area');

            // Jika ada id_area di request dan tidak sesuai dengan id_area petugas
            if ($requestedAreaId && (int) $requestedAreaId !== (int) $user->id_area) {
                // Tolak akses
                abort(403, 'Unauthorized: Anda tidak memiliki akses ke area ini.');
            }
        }

        return $next($request);
    }
}
