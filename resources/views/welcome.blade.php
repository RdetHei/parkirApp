<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NESTON - Modern Parking Ecosystem</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/neston.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/neston.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            scroll-behavior: smooth;
        }

        .pro-grid {
            background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        .hero-glow {
            background: radial-gradient(circle at 50% 50%, rgba(16, 185, 129, 0.1) 0%, transparent 50%);
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body class="bg-[#020617] text-slate-100 antialiased selection:bg-emerald-500 selection:text-white">
    <!-- Grid & Glow Overlay -->
    <div class="fixed inset-0 pro-grid pointer-events-none z-0"></div>
    <div class="fixed inset-0 hero-glow pointer-events-none z-0"></div>
    
    <!-- Header -->
    <nav class="fixed w-full top-0 z-50 bg-[#020617]/70 backdrop-blur-xl border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="#" class="flex items-center space-x-3 group z-10">
                    <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center shadow-[0_0_15px_rgba(16,185,129,0.3)]">
                        <img src="{{ asset('images/neston.png') }}" alt="N" class="w-5 h-5 invert">
                    </div>
                    <span class="text-lg font-bold tracking-tight text-white uppercase">NESTON</span>
                </a>

                <!-- Nav -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#fitur" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">Features</a>
                    <a href="#teknologi" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">Technology</a>
                    <div class="h-4 w-px bg-white/10"></div>
                    <a href="{{ route('login.create') }}" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">Sign in</a>
                    <a href="{{ route('register.create') }}" class="btn-pro-primary !py-2 !px-5 !text-[11px] uppercase tracking-wider">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="relative pt-40 pb-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 mb-8">
                    <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest">Enterprise Parking Solution</span>
                </div>
                <h1 class="text-5xl lg:text-7xl font-extrabold tracking-tight text-white mb-8 leading-[1.1]">
                    Manage your parking <br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-emerald-600">with intelligence.</span>
                </h1>
                <p class="text-lg text-slate-400 leading-relaxed mb-12 max-w-2xl mx-auto font-medium">
                    Automate vehicle tracking, payment processing, and space management with our AI-powered ecosystem. Secure, scalable, and built for modern infrastructure.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register.create') }}" class="btn-pro-primary !px-8 !py-4 text-sm uppercase tracking-widest">
                        Start for Free
                    </a>
                    <a href="#fitur" class="btn-pro-outline !px-8 !py-4 text-sm uppercase tracking-widest">
                        View Demo
                    </a>
                </div>
            </div>

            <!-- Hero Visual (SaaS Style) -->
            <div class="mt-24 relative max-w-5xl mx-auto">
                <div class="aspect-[16/9] rounded-2xl bg-slate-900 border border-white/5 shadow-2xl overflow-hidden relative group">
                    <div class="absolute inset-0 bg-gradient-to-tr from-emerald-500/10 via-transparent to-indigo-500/5"></div>
                    <!-- Mockup UI elements -->
                    <div class="absolute top-0 left-0 right-0 h-10 bg-white/5 border-b border-white/5 flex items-center px-4 gap-2">
                        <div class="w-2 h-2 rounded-full bg-red-500/50"></div>
                        <div class="w-2 h-2 rounded-full bg-amber-500/50"></div>
                        <div class="w-2 h-2 rounded-full bg-emerald-500/50"></div>
                    </div>
                    <div class="p-12 pt-20 grid grid-cols-3 gap-6">
                        <div class="h-32 rounded-xl bg-white/5 border border-white/5 animate-pulse"></div>
                        <div class="h-32 rounded-xl bg-white/5 border border-white/5 animate-pulse" style="animation-delay: 0.2s"></div>
                        <div class="h-32 rounded-xl bg-white/5 border border-white/5 animate-pulse" style="animation-delay: 0.4s"></div>
                        <div class="col-span-2 h-48 rounded-xl bg-white/5 border border-white/5 animate-pulse" style="animation-delay: 0.6s"></div>
                        <div class="h-48 rounded-xl bg-white/5 border border-white/5 animate-pulse" style="animation-delay: 0.8s"></div>
                    </div>
                </div>
                <!-- Decoration -->
                <div class="absolute -top-12 -right-12 w-64 h-64 bg-emerald-500/20 rounded-full blur-[100px] -z-10"></div>
                <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-indigo-500/20 rounded-full blur-[100px] -z-10"></div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="py-20 border-y border-white/5 bg-slate-950/50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-12 text-center">
                <div>
                    <p class="text-3xl font-extrabold text-white mb-1 tracking-tight">99.9%</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Accuracy</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-white mb-1 tracking-tight">1.2M</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Scans/Year</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-white mb-1 tracking-tight">500+</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Enterprises</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-white mb-1 tracking-tight">24/7</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Support</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="fitur" class="py-32">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="mb-20">
                <h2 class="text-xs font-bold text-emerald-500 uppercase tracking-[0.3em] mb-4">Core Capabilities</h2>
                <h3 class="text-3xl lg:text-5xl font-extrabold text-white tracking-tight">Everything you need to <br/> scale your operations.</h3>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="card-pro card-pro-hover group">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mb-6 group-hover:bg-emerald-500 group-hover:text-slate-950 transition-all duration-300">
                        <i class="fa-solid fa-camera text-sm"></i>
                    </div>
                    <h4 class="text-lg font-bold text-white mb-3">AI Vision (ANPR)</h4>
                    <p class="text-slate-400 text-sm leading-relaxed font-medium">
                        Proprietary YOLOv8 model for lightning-fast license plate recognition with near-perfect accuracy.
                    </p>
                </div>

                <div class="card-pro card-pro-hover group">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mb-6 group-hover:bg-emerald-500 group-hover:text-slate-950 transition-all duration-300">
                        <i class="fa-solid fa-credit-card text-sm"></i>
                    </div>
                    <h4 class="text-lg font-bold text-white mb-3">Omni-Channel Payment</h4>
                    <p class="text-slate-400 text-sm leading-relaxed font-medium">
                        Seamlessly integrate with Midtrans, e-wallets, and internal NestonPay balance.
                    </p>
                </div>

                <div class="card-pro card-pro-hover group">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mb-6 group-hover:bg-emerald-500 group-hover:text-slate-950 transition-all duration-300">
                        <i class="fa-solid fa-shield-check text-sm"></i>
                    </div>
                    <h4 class="text-lg font-bold text-white mb-3">NFC Authentication</h4>
                    <p class="text-slate-400 text-sm leading-relaxed font-medium">
                        Contactless check-in/out for registered users using encrypted NFC technology.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-16 border-t border-white/5 bg-slate-950">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="flex items-center space-x-3">
                <div class="w-6 h-6 bg-slate-800 rounded flex items-center justify-center">
                    <img src="{{ asset('images/neston.png') }}" alt="N" class="w-3 h-3 opacity-50">
                </div>
                <span class="text-xs font-bold tracking-widest text-slate-500 uppercase">NESTON</span>
            </div>
            <p class="text-[10px] font-bold text-slate-600 uppercase tracking-widest">
                © 2026 NESTON CORE SYSTEM. ALL RIGHTS RESERVED.
            </p>
        </div>
    </footer>
</body>
</html>