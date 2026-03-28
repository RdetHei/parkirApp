<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - NESTON</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .auth-grid {
            background-image: radial-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        .bg-batik {
            background-image: url("{{ asset('images/batik-pattern.svg') }}");
            background-size: 560px 560px;
            background-repeat: repeat;
        }
    </style>
</head>
<body class="min-h-screen bg-[#020617] text-slate-100 antialiased selection:bg-emerald-500 selection:text-white flex items-center justify-center p-6 relative overflow-hidden">
    <!-- Grid Overlay -->
    <div class="fixed inset-0 auth-grid pointer-events-none z-0"></div>
    <div class="fixed inset-0 bg-batik opacity-[0.035] pointer-events-none z-0"></div>
    
    <!-- Background Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="w-full max-w-[420px] relative z-10">
        <!-- Logo -->
        <div class="text-center mb-12">
            <a href="/" class="inline-flex items-center space-x-3 group">
                <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center shadow-[0_0_20px_rgba(16,185,129,0.2)]">
                    <img src="{{ asset('images/neston-batik.svg') }}" alt="N" class="w-6 h-6">
                </div>
                <span class="text-2xl font-bold tracking-tight text-white uppercase">NESTON</span>
            </a>
        </div>

        <div class="bg-slate-900 border border-white/5 rounded-2xl p-10 shadow-2xl shadow-black/50">
            <!-- Header -->
            <div class="mb-10">
                <h2 class="text-2xl font-extrabold text-white tracking-tight mb-2">Welcome back</h2>
                <p class="text-slate-500 text-sm font-medium">Enter your credentials to access your account</p>
            </div>

            <!-- Alerts -->
            @if(session('info') || session('error') || session('success'))
                <div class="mb-8 p-4 rounded-xl text-xs font-semibold {{ session('error') ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' }}">
                    {{ session('info') ?? session('error') ?? session('success') }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.store') }}" id="loginForm" class="space-y-6">
                @csrf

                <!-- Email -->
                <div class="space-y-2">
                    <label for="email" class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                        Email Address
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder="name@company.com"
                        class="w-full px-4 py-3.5 bg-slate-950 border border-white/5 rounded-xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm"
                    >
                    @error('email')
                        <p class="mt-2 text-[11px] text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <label for="password" class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                            Password
                        </label>
                        <a href="{{ route('password.request') }}" class="text-[10px] font-bold text-emerald-500 hover:text-emerald-400 transition-colors uppercase tracking-widest">
                            Forgot?
                        </a>
                    </div>
                    <div class="relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            placeholder="••••••••"
                            class="w-full px-4 py-3.5 bg-slate-950 border border-white/5 rounded-xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm"
                        >
                        <button
                            type="button"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-600 hover:text-white transition-colors"
                            id="togglePassword"
                        >
                            <i id="eyeIcon" class="fa-solid fa-eye text-xs"></i>
                            <i id="eyeSlashIcon" class="fa-solid fa-eye-slash text-xs hidden"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-[11px] text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        {{ old('remember') ? 'checked' : '' }}
                        class="w-4 h-4 bg-slate-950 border-white/10 rounded text-emerald-500 focus:ring-emerald-500/20 cursor-pointer"
                    >
                    <label for="remember" class="ml-3 text-[11px] font-bold text-slate-500 uppercase tracking-widest cursor-pointer select-none">
                        Remember me
                    </label>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    id="submitBtn"
                    class="group relative w-full flex justify-center py-4 px-4 bg-emerald-500 text-slate-950 text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-emerald-400 transition-all duration-300 shadow-xl shadow-emerald-500/20 active:scale-[0.98]"
                >
                    <span id="buttonText">Sign in to Account</span>
                    <svg id="spinner" class="hidden animate-spin ml-3 h-4 w-4 text-slate-950" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>

            <!-- Register -->
            <div class="mt-10 pt-8 border-t border-white/5 text-center">
                <p class="text-xs text-slate-500 font-medium">
                    Don't have an account?
                    <a href="{{ route('register.create') }}" class="text-emerald-500 font-bold hover:text-emerald-400 transition-colors">
                        Create one now
                    </a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-10">
            <p class="text-[10px] font-bold text-slate-700 uppercase tracking-[0.2em]">© 2026 NESTON CORE SYSTEM</p>
        </div>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeSlashIcon = document.getElementById('eyeSlashIcon');

        togglePassword.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        });

        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        const spinner = document.getElementById('spinner');
        const buttonText = document.getElementById('buttonText');

        loginForm.addEventListener('submit', function() {
            submitBtn.disabled = true;
            spinner.classList.remove('hidden');
            buttonText.textContent = 'Memproses...';
        });
    </script>
</body>
</html>
