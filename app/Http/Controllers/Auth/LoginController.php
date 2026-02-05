<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LogAktifitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Catat login ke log aktivitas (siapa yang login)
            $roleLabel = ucfirst($user->role ?? 'user');
            $aktivitas = sprintf('Login ke sistem - %s (%s)', $user->name, $roleLabel);
            if (strlen($aktivitas) > 100) {
                $aktivitas = substr($aktivitas, 0, 97) . '...';
            }
            LogAktifitas::create([
                'id_user' => $user->id,
                'aktivitas' => $aktivitas,
                'waktu_aktivitas' => now(),
            ]);

            switch ($user->role) {
                case 'admin':
                    return redirect()->intended(route('dashboard'));
                case 'owner':
                    return redirect()->intended(route('owner.dashboard'));
                case 'petugas':
                    return redirect()->intended(route('petugas.dashboard'));
                default:
                    return redirect()->intended(route('transaksi.create-check-in'));
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Catat logout ke log aktivitas (setelah logout, pakai data user yang masih di memory)
        if ($user) {
            $roleLabel = ucfirst($user->role ?? 'user');
            $aktivitas = sprintf('Logout - %s (%s)', $user->name, $roleLabel);
            if (strlen($aktivitas) > 100) {
                $aktivitas = substr($aktivitas, 0, 97) . '...';
            }
            LogAktifitas::create([
                'id_user' => $user->id,
                'aktivitas' => $aktivitas,
                'waktu_aktivitas' => now(),
            ]);
        }

        return redirect('/');
    }
}
