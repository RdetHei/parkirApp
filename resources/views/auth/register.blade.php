<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - NESTON</title>
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

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .glass-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .input-glow:focus {
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.1);
        }
    </style>
</head>
<body class="min-h-screen bg-[#020617] text-slate-100 antialiased selection:bg-emerald-500 selection:text-white flex items-center justify-center p-6 sm:p-10 relative overflow-y-auto">
    <!-- Grid Overlay -->
    <div class="fixed inset-0 auth-grid pointer-events-none z-0"></div>
    <div class="fixed inset-0 bg-batik opacity-[0.035] pointer-events-none z-0"></div>

    <!-- Background Glows -->
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-emerald-500/10 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="w-full max-w-[550px] relative z-10 my-auto animate-fade-in">
        <!-- Logo -->
        <div class="text-center mb-10">
            <a href="/" class="inline-flex flex-col items-center group">
                <div class="w-14 h-14 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4">
                    <img src="{{ asset('images/neston.svg') }}" alt="NESTON" class="w-14 h-14">
                </div>
                <span class="text-3xl font-black tracking-[0.2em] text-white uppercase">NESTON</span>
                <span class="text-[10px] font-bold text-emerald-500/60 uppercase tracking-[0.4em] mt-1">Intelligent Parking</span>
            </a>
        </div>

        <div class="glass-card rounded-[2.5rem] p-8 sm:p-12 shadow-2xl shadow-black/50">
            <!-- Header -->
            <div class="mb-10 text-center">
                <h2 class="text-2xl font-bold text-white tracking-tight mb-2">Create New Account</h2>
                <p class="text-slate-500 text-sm">Join the intelligent parking ecosystem</p>
            </div>

            <!-- Alerts -->
            @if(session('success') || session('info') || session('error'))
                <div class="mb-8 p-4 rounded-2xl text-xs font-semibold {{ session('error') ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid {{ session('error') ? 'fa-circle-exclamation' : 'fa-circle-check' }}"></i>
                        <span>{{ session('success') ?? session('info') ?? session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Register Form -->
            <form method="POST" action="{{ route('register.store') }}" id="registerForm" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="space-y-2">
                        <label for="name" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">
                            Username
                        </label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            placeholder="John Doe"
                            class="w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm input-glow"
                        >
                        @error('name')
                            <p class="mt-2 text-[11px] text-red-400 font-medium ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label for="email" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">
                            Email
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            placeholder="name@example.com"
                            class="w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm input-glow"
                        >
                        @error('email')
                            <p class="mt-2 text-[11px] text-red-400 font-medium ml-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="phone" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">
                        WhatsApp / HP <span class="text-slate-700">(opsional, untuk notifikasi parkir)</span>
                    </label>
                    <input
                        id="phone"
                        type="text"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="628xxxxx atau 08xxxxx"
                        class="w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm input-glow"
                    >
                    @error('phone')
                        <p class="mt-2 text-[11px] text-red-400 font-medium ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- RFID -->
                    <div class="space-y-2">
                        <label for="rfid_uid" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">
                            RFID UID <span class="text-slate-700">(opsional)</span>
                        </label>
                        <div class="relative group">
                            <input
                                id="rfid_uid"
                                type="text"
                                name="rfid_uid"
                                value="{{ old('rfid_uid') }}"
                                placeholder="Scan Kartu RFID"
                                class="w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm input-glow"
                            >
                            <button type="button"
                                    id="btnScanRfidRegister"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-[9px] font-black text-slate-400 hover:text-white hover:bg-emerald-500 hover:border-emerald-500 hover:text-slate-950 transition-all uppercase tracking-widest">
                                Scan
                            </button>
                        </div>
                        @error('rfid_uid')
                            <p class="mt-2 text-[11px] text-red-400 font-medium ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <label for="password" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">
                            Password
                        </label>
                        <div class="relative group">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                placeholder="••••••••"
                                class="w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm input-glow"
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
                            <p class="mt-2 text-[11px] text-red-400 font-medium ml-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="space-y-2">
                    <label for="password_confirmation" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">
                        Confirm Password
                    </label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        placeholder="••••••••"
                        class="w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm input-glow"
                    >
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    id="submitBtn"
                    class="group relative w-full flex justify-center py-4 px-4 bg-emerald-500 text-slate-950 text-xs font-bold uppercase tracking-widest rounded-2xl hover:bg-emerald-400 transition-all duration-300 shadow-xl shadow-emerald-500/20 active:scale-[0.98] overflow-hidden"
                >
                    <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                    <span id="buttonText" class="relative z-10">Create new account</span>
                    <svg id="spinner" class="hidden animate-spin ml-3 h-4 w-4 text-slate-950 relative z-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>

            <!-- Login Link -->
            <div class="mt-10 pt-8 border-t border-white/5 text-center">
                <p class="text-xs text-slate-500 font-medium">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-emerald-500 font-bold hover:text-emerald-400 transition-colors">
                        Sign in
                    </a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-10">
            <p class="text-[10px] font-bold text-slate-700 uppercase tracking-[0.2em]">© 2026 NESTON CORE SYSTEM</p>
        </div>
    </div>

    <style>
        @keyframes shimmer {
            100% { transform: translateX(100%); }
        }
    </style>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirmation');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeSlashIcon = document.getElementById('eyeSlashIcon');

        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    passwordConfirmInput.type = 'text';
                    eyeIcon.classList.add('hidden');
                    eyeSlashIcon.classList.remove('hidden');
                } else {
                    passwordInput.type = 'password';
                    passwordConfirmInput.type = 'password';
                    eyeIcon.classList.remove('hidden');
                    eyeSlashIcon.classList.add('hidden');
                }
            });
        }

        const registerForm = document.getElementById('registerForm');
        const submitBtn = document.getElementById('submitBtn');
        const spinner = document.getElementById('spinner');
        const buttonText = document.getElementById('buttonText');

        if (registerForm) {
            registerForm.addEventListener('submit', function() {
                submitBtn.disabled = true;
                spinner.classList.remove('hidden');
                buttonText.textContent = 'Memproses...';
            });
        }

        // RFID Scanning Logic (Placeholder/Integration)
        const btnScanRfid = document.getElementById('btnScanRfidRegister');
        const rfidInput = document.getElementById('rfid_uid');

        if (btnScanRfid) {
            btnScanRfid.addEventListener('click', async () => {
                try {
                    btnScanRfid.textContent = 'Scanning...';
                    btnScanRfid.classList.add('animate-pulse');

                    // Check for Web RFID or other hardware integration
                    // For browser demo, we simulate the RFID input
                    setTimeout(() => {
                        const mockUid = 'RFID-' + Math.random().toString(36).substr(2, 9).toUpperCase();
                        rfidInput.value = mockUid;
                        btnScanRfid.textContent = 'Berhasil!';
                        btnScanRfid.classList.remove('animate-pulse');
                        setTimeout(() => btnScanRfid.textContent = 'Scan', 2000);
                    }, 1000);
                } catch (error) {
                    console.error(error);
                    btnScanRfid.textContent = 'Gagal';
                    btnScanRfid.classList.remove('animate-pulse');
                }
            });
        }
    </script>
</body>
</html>
