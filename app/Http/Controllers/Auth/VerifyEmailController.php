<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectVerifiedUser($request);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->redirectVerifiedUser($request);
    }

    protected function redirectVerifiedUser(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();
        $role = strtolower($user->role ?? 'user');

        return match ($role) {
            'admin' => redirect()->intended(route('dashboard', absolute: false).'?verified=1'),
            'owner' => redirect()->intended(route('owner.dashboard', absolute: false).'?verified=1'),
            'petugas' => redirect()->intended(route('petugas.dashboard', absolute: false).'?verified=1'),
            default => redirect()->route('login')->with('status', 'Email Anda berhasil diverifikasi. Silakan masuk.'),
        };
    }
}
