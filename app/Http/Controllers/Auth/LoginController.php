<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LogAktifitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Traits\LogsActivity;

class LoginController extends Controller
{
    use LogsActivity;

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
            \Illuminate\Support\Facades\Log::info('User logged in', ['id' => $user->id, 'email' => $user->email, 'role' => $user->role]);

            // Catat login ke log aktivitas (siapa yang login)
            $roleLabel = ucfirst($user->role ?? 'user');
            $this->logActivity(
                "Login ke sistem - {$user->name} ({$roleLabel})",
                'auth',
                $user
            );

            $userRole = strtolower($user->role ?? 'user');
            \Illuminate\Support\Facades\Log::info('Redirecting user based on role', ['role' => $userRole]);
            
            switch ($userRole) {
                case 'admin':
                    return redirect()->intended(route('dashboard'));
                case 'owner':
                    return redirect()->intended(route('owner.dashboard'));
                case 'petugas':
                    return redirect()->intended(route('petugas.dashboard'));
                default:
                    return redirect()->intended(route('user.dashboard'));
            }
        }

        \Illuminate\Support\Facades\Log::warning('Login failed', ['email' => $request->email]);
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

        // Catat logout ke log aktivitas (sebelum logout agar Auth::id() masih valid)
        if ($user) {
            $roleLabel = ucfirst($user->role ?? 'user');
            $this->logActivity(
                "Logout - {$user->name} ({$roleLabel})",
                'auth',
                $user
            );
        }

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
