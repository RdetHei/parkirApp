<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - NESTON</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            scroll-behavior: smooth;
        }

        .metallic-grid {
            background-image: linear-gradient(to right, rgba(255,255,255,0.05) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .metallic-grid-fine {
            background-image: linear-gradient(to right, rgba(255,255,255,0.02) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 10px 10px;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
    </style>
</head>
<body class="min-h-screen bg-zinc-950 text-zinc-100 antialiased selection:bg-white selection:text-black flex items-center justify-center p-6 relative overflow-hidden">
    <!-- Grid Overlay -->
    <div class="fixed inset-0 metallic-grid pointer-events-none z-0"></div>
    <div class="fixed inset-0 metallic-grid-fine pointer-events-none z-0"></div>
    
    <!-- Background Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-white/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="w-full max-w-[400px] relative z-10">
        <!-- Logo -->
        <div class="text-center mb-10">
            <a href="/" class="inline-flex items-center space-x-3 group">
                <img src="{{ asset('images/neston.png') }}" alt="NESTON" class="w-10 h-10 rounded-xl invert brightness-200">
                <span class="text-2xl font-bold tracking-tighter text-white uppercase">NESTON</span>
            </a>
        </div>

        <div class="bg-zinc-900/40 border border-white/10 backdrop-blur-xl rounded-[2.5rem] p-10 shadow-2xl">
            <!-- Header -->
            <div class="mb-10 text-center">
                <h2 class="text-2xl font-bold text-white tracking-tight mb-2">Selamat Datang</h2>
                <p class="text-zinc-500 text-sm font-medium">Masuk ke ekosistem parkir cerdas</p>
            </div>

            <!-- Alerts -->
            @if(session('info'))
                <div class="mb-6 p-4 bg-white/5 border border-white/10 text-zinc-300 rounded-2xl text-xs font-medium">
                    {{ session('info') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-2xl text-xs font-medium">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 text-green-400 rounded-2xl text-xs font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.store') }}" id="loginForm" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-3 px-1">
                        Email Address
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="name@example.com"
                        class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white placeholder:text-zinc-600 focus:outline-none focus:ring-1 focus:ring-white/20 focus:border-white/20 focus:bg-white/[0.08] transition-all duration-300 text-sm @error('email') border-red-500/50 @enderror"
                    >
                    @error('email')
                        <p class="mt-2 text-[11px] text-red-400 px-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <div class="flex justify-between items-center mb-3 px-1">
                        <label for="password" class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest">
                            Password
                        </label>
                        <a href="{{ route('password.request') }}" class="text-[10px] font-bold text-zinc-400 hover:text-white transition-colors uppercase tracking-widest">
                            Lupa?
                        </a>
                    </div>
                    <div class="relative group">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white placeholder:text-zinc-600 focus:outline-none focus:ring-1 focus:ring-white/20 focus:border-white/20 focus:bg-white/[0.08] transition-all duration-300 text-sm @error('password') border-red-500/50 @enderror"
                        >
                        <button
                            type="button"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-white focus:outline-none transition-colors"
                            id="togglePassword"
                        >
                            <i id="eyeIcon" class="fa-solid fa-eye text-xs"></i>
                            <i id="eyeSlashIcon" class="fa-solid fa-eye-slash text-xs hidden"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-[11px] text-red-400 px-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center px-1">
                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        {{ old('remember') ? 'checked' : '' }}
                        class="w-4 h-4 bg-white/5 border-white/10 rounded text-zinc-900 focus:ring-white/20 cursor-pointer"
                    >
                    <label for="remember" class="ml-3 text-[11px] font-bold text-zinc-500 uppercase tracking-widest cursor-pointer select-none">
                        Ingat Sesi Ini
                    </label>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    id="submitBtn"
                    class="w-full bg-white text-black hover:bg-zinc-100 hover:shadow-[0_0_20px_rgba(255,255,255,0.2)] transition-all duration-300 hover:scale-[1.02] active:scale-95 py-4 rounded-2xl text-xs font-bold uppercase tracking-widest flex items-center justify-center gap-3 relative overflow-hidden"
                >
                    <span id="spinner" class="hidden">
                        <svg class="animate-spin h-4 w-4 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    <span id="buttonText">Masuk Sekarang</span>
                </button>
            </form>

            <!-- Register Link -->
            <div class="mt-10 pt-10 border-t border-white/5 text-center">
                <p class="text-xs text-zinc-500 font-medium">
                    Belum punya akun?
                    <a href="{{ route('register.create') }}" class="text-white font-bold hover:underline transition-all">
                        Daftar Baru
                    </a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-10">
            <p class="text-[10px] font-bold text-zinc-600 uppercase tracking-[0.2em]">© 2026 NESTON CORE SYSTEM</p>
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
