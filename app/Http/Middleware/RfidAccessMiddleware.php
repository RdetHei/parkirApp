<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RfidAccessMiddleware
{
    /**
     * Require a prior RFID scan (session-based).
     * Optional: pass allowed roles, e.g. ->middleware('rfid.access:admin,petugas')
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $rfidUserId = $request->session()->get('rfid_access_user_id');
        $rfidRole = strtolower(trim((string) $request->session()->get('rfid_access_role', '')));

        if (! $rfidUserId) {
            abort(403, 'RFID access required. Silakan scan kartu terlebih dahulu.');
        }

        if (! empty($roles)) {
            $allowed = array_map(fn ($r) => strtolower(trim($r)), $roles);
            if (! in_array($rfidRole, $allowed, true)) {
                abort(403, 'RFID access denied (role mismatch).');
            }
        }

        return $next($request);
    }
}

