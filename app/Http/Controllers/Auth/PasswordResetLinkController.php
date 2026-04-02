<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Tautan reset password telah dikirim ke email Anda.');
        }

        $msg = match ($status) {
            Password::INVALID_USER => 'Email tidak terdaftar di sistem.',
            Password::RESET_THROTTLED => 'Silakan tunggu sebelum meminta tautan lagi.',
            default => 'Tidak dapat mengirim tautan reset. Coba lagi.',
        };

        return back()->withInput($request->only('email'))->withErrors(['email' => $msg]);
    }
}
