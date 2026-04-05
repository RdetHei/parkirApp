<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NESTON - Modern Parking Ecosystem</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/neston.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/neston.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/neston.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

        [x-cloak] { display: none !important; }

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

        .bg-batik {
            background-image: url("{{ asset('images/batik-pattern.svg') }}");
            background-size: 560px 560px;
            background-repeat: repeat;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        /* Parking Animation */
        .parking-lot {
            position: relative;
            width: 100%;
            height: 100%;
            background: #0f172a;
            border: 2px solid rgba(255,255,255,0.05);
            border-radius: 1rem;
            overflow: hidden;
        }

        .parking-slot {
            position: absolute;
            width: 60px;
            height: 100px;
            border: 2px dashed rgba(255,255,255,0.1);
            border-top: none;
        }

        .car {
            position: absolute;
            width: 40px;
            height: 70px;
            border-radius: 8px;
            transition: all 1s ease-in-out;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        .car-light {
            position: absolute;
            width: 6px;
            height: 4px;
            background: #fff;
            border-radius: 2px;
            opacity: 0.8;
            top: 4px;
        }

        .car-light.left { left: 6px; }
        .car-light.right { right: 6px; }

        /* Animation Keyframes */
        @keyframes car1-entry {
            0% { left: -100px; top: 180px; transform: rotate(90deg); }
            20% { left: 150px; top: 180px; transform: rotate(90deg); }
            40% { left: 150px; top: 80px; transform: rotate(0deg); }
            100% { left: 150px; top: 80px; transform: rotate(0deg); }
        }

        @keyframes car1-exit {
            0% { left: 150px; top: 80px; transform: rotate(0deg); }
            20% { left: 150px; top: 180px; transform: rotate(180deg); }
            40% { left: 600px; top: 180px; transform: rotate(90deg); }
            100% { left: 1200px; top: 180px; transform: rotate(90deg); }
        }

        @keyframes car2-entry {
            0% { left: -100px; top: 180px; transform: rotate(90deg); }
            30% { left: 350px; top: 180px; transform: rotate(90deg); }
            60% { left: 350px; top: 80px; transform: rotate(0deg); }
            100% { left: 350px; top: 80px; transform: rotate(0deg); }
        }

        @keyframes car3-exit {
            0% { left: 550px; top: 80px; transform: rotate(0deg); }
            30% { left: 550px; top: 180px; transform: rotate(180deg); }
            60% { left: 800px; top: 180px; transform: rotate(90deg); }
            100% { left: 1200px; top: 180px; transform: rotate(90deg); }
        }

        .animate-car-1 { animation: car1-entry 5s forwards, car1-exit 5s 10s forwards; animation-iteration-count: infinite; }
        .animate-car-2 { animation: car2-entry 7s 2s infinite alternate; }
        .animate-car-3 { animation: car3-exit 8s infinite; }
    </style>
</head>
<body class="bg-[#020617] text-slate-100 antialiased selection:bg-emerald-500 selection:text-white">
    <!-- Grid & Glow Overlay -->
    <div class="fixed inset-0 pro-grid pointer-events-none z-0"></div>
    <div class="fixed inset-0 hero-glow pointer-events-none z-0"></div>
    <div class="fixed inset-0 bg-batik opacity-[0.035] pointer-events-none z-0"></div>

    <!-- Header -->
    <nav class="fixed w-full top-0 z-50 bg-[#020617]/70 backdrop-blur-xl border-b border-white/5" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="#" class="flex items-center space-x-3 group z-10">
                    <img src="{{ asset('images/neston.svg') }}" alt="NESTON" class="w-8 h-8 shrink-0">
                    <span class="text-lg font-bold tracking-tight text-white uppercase">NESTON</span>
                </a>

                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#about" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">About</a>
                    <a href="#workflow" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">Workflow</a>
                    <a href="#fitur" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">Features</a>
                    <a href="{{ route('docs') }}" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">Docs</a>
                    <a href="#contact" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">Contact</a>
                    <div class="h-4 w-px bg-white/10"></div>
                    <button onclick="openCardLogin()" class="group flex items-center gap-2 text-xs font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">
                        <i class="fa-solid fa-id-card"></i>
                        Card Login
                    </button>
                    <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">Sign in</a>
                    <a href="{{ route('register') }}" class="btn-pro-primary !py-2 !px-5 !text-[11px] uppercase tracking-wider">
                        Get Started
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Overlay -->
        <div x-show="mobileMenuOpen"
             x-cloak
             @click.away="mobileMenuOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="md:hidden absolute top-16 left-0 w-full bg-[#020617]/95 backdrop-blur-2xl border-b border-white/5 py-6 px-6 space-y-4 shadow-2xl">
            <a href="#about" @click="mobileMenuOpen = false" class="block text-sm font-semibold text-slate-400 hover:text-white transition-colors">About</a>
            <a href="#workflow" @click="mobileMenuOpen = false" class="block text-sm font-semibold text-slate-400 hover:text-white transition-colors">Workflow</a>
            <a href="#fitur" @click="mobileMenuOpen = false" class="block text-sm font-semibold text-slate-400 hover:text-white transition-colors">Features</a>
            <a href="{{ route('docs') }}" class="block text-sm font-semibold text-slate-400 hover:text-white transition-colors">Docs</a>
            <a href="#contact" @click="mobileMenuOpen = false" class="block text-sm font-semibold text-slate-400 hover:text-white transition-colors">Contact</a>
            <div class="h-px w-full bg-white/5"></div>
            <button onclick="openCardLogin(); mobileMenuOpen = false" class="flex items-center gap-3 text-sm font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">
                <i class="fa-solid fa-id-card"></i>
                Card Login
            </button>
            <a href="{{ route('login') }}" class="block text-sm font-semibold text-slate-400 hover:text-white transition-colors">Sign in</a>
            <a href="{{ route('register') }}" class="btn-pro-primary block text-center !py-3 !text-sm uppercase tracking-wider">
                Get Started
            </a>
        </div>
    </nav>

    <!-- Hero -->
    <section class="relative pt-40 pb-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 mb-8">
                    <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest">Enterprise Parking Solution</span>
                </div>
                <h1 class="text-5xl lg:text-6xl font-extrabold tracking-tight text-white mb-8 leading-[1.1]">
                    MANAGE YOUR PARKING<br/>
                    <span class="text-white">WITH</span>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-emerald-600"> INTELLIGENCE.</span>
                </h1>
                <p class="text-lg text-slate-400 leading-relaxed mb-12 max-w-2xl mx-auto font-medium">
                    Automate vehicle tracking, payment processing, and space management with our AI-powered ecosystem. Secure, scalable, and built for modern infrastructure.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="btn-pro-primary !px-8 !py-4 text-sm uppercase tracking-widest">
                        Start for Free
                    </a>
                    <a href="#fitur" class="btn-pro-outline !px-8 !py-4 text-sm uppercase tracking-widest">
                        View Demo
                    </a>
                </div>
            </div>

            <div class="mt-16 lg:mt-24 relative max-w-5xl mx-auto">
                <div class="aspect-auto lg:aspect-[16/9] min-h-[500px] lg:min-h-0 rounded-2xl bg-slate-900 border border-white/5 shadow-2xl overflow-hidden relative group">
                    <div class="absolute inset-0 bg-gradient-to-tr from-emerald-500/10 via-transparent to-indigo-500/5"></div>

                    <div class="absolute top-0 left-0 right-0 h-10 bg-white/5 border-b border-white/5 flex items-center px-4 gap-2 z-10">
                        <div class="w-3 h-3 rounded-full bg-red-500/50"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-500/50"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-500/50"></div>
                        <div class="ml-4 text-[10px] sm:text-xs text-slate-500 font-mono truncate">sipark-dashboard.io/monitoring</div>
                    </div>

                    <div class="p-4 sm:p-8 pt-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 h-full overflow-y-auto lg:overflow-hidden lg:absolute lg:inset-0">

                        <div class="h-28 rounded-xl bg-white/5 border border-white/10 p-5 flex flex-col justify-between backdrop-blur-sm shadow-lg">
                            <div class="text-slate-400 text-sm font-medium flex justify-between items-center">
                                <span>Slot Tersedia</span>
                                <span class="text-emerald-400 bg-emerald-400/10 px-2 py-0.5 rounded text-[10px]">Live</span>
                            </div>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl lg:text-4xl font-bold text-white">142</span>
                                <span class="text-slate-500 text-xs font-medium">/ 500 total</span>
                            </div>
                        </div>

                        <div class="h-28 rounded-xl bg-white/5 border border-white/10 p-5 flex flex-col justify-between backdrop-blur-sm shadow-lg">
                            <div class="text-slate-400 text-sm font-medium">Kendaraan Masuk</div>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl lg:text-4xl font-bold text-white">358</span>
                                <span class="text-emerald-400 text-xs font-medium">↑ 12%</span>
                            </div>
                        </div>

                        <div class="h-28 sm:col-span-2 lg:col-span-1 rounded-xl bg-white/5 border border-white/10 p-5 flex flex-col justify-between backdrop-blur-sm shadow-lg">
                            <div class="text-slate-400 text-sm font-medium">Pendapatan (Harian)</div>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-bold text-white">Rp 2.4M</span>
                            </div>
                        </div>

                        <div class="sm:col-span-2 rounded-xl bg-white/5 border border-white/10 p-5 flex flex-col backdrop-blur-sm shadow-lg h-full min-h-[200px]">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2">
                                <div class="text-slate-200 text-sm font-medium">Live Area Monitoring - Lantai 1</div>
                                <div class="flex gap-3 text-[10px]">
                                    <span class="flex items-center gap-1"><div class="w-2 h-2 rounded-full bg-emerald-400"></div> Kosong</span>
                                    <span class="flex items-center gap-1"><div class="w-2 h-2 rounded-full bg-rose-500"></div> Terisi</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-4 sm:grid-cols-8 gap-2 flex-1">
                                <div class="bg-rose-500/20 border border-rose-500/30 rounded-md aspect-square lg:aspect-auto"></div>
                                <div class="bg-rose-500/20 border border-rose-500/30 rounded-md aspect-square lg:aspect-auto"></div>
                                <div class="bg-emerald-400/20 border border-emerald-400/30 rounded-md aspect-square lg:aspect-auto"></div>
                                <div class="bg-rose-500/20 border border-rose-500/30 rounded-md relative flex items-center justify-center aspect-square lg:aspect-auto">
                                    <div class="w-2 h-2 bg-rose-500 rounded-full animate-ping"></div>
                                </div>
                                <div class="bg-emerald-400/20 border border-emerald-400/30 rounded-md aspect-square lg:aspect-auto"></div>
                                <div class="bg-rose-500/20 border border-rose-500/30 rounded-md aspect-square lg:aspect-auto"></div>
                                <div class="bg-emerald-400/20 border border-emerald-400/30 rounded-md aspect-square lg:aspect-auto"></div>
                                <div class="bg-emerald-400/20 border border-emerald-400/30 rounded-md aspect-square lg:aspect-auto"></div>

                                <div class="bg-rose-500/20 border border-rose-500/30 rounded-md aspect-square lg:aspect-auto"></div>
                                <div class="bg-emerald-400/20 border border-emerald-400/30 rounded-md aspect-square lg:aspect-auto"></div>
                                <div class="bg-emerald-400/20 border border-emerald-400/30 rounded-md aspect-square lg:aspect-auto"></div>
                                <div class="bg-rose-500/20 border border-rose-500/30 rounded-md aspect-square lg:aspect-auto"></div>
                                <div class="bg-rose-500/20 border border-rose-500/30 rounded-md aspect-square lg:aspect-auto"></div>
                                <div class="bg-emerald-400/20 border border-emerald-400/30 rounded-md aspect-square lg:aspect-auto"></div>
                                <div class="bg-rose-500/20 border border-rose-500/30 rounded-md aspect-square lg:aspect-auto"></div>
                                <div class="bg-rose-500/20 border border-rose-500/30 rounded-md aspect-square lg:aspect-auto"></div>
                            </div>
                        </div>

                        <div class="sm:col-span-2 lg:col-span-1 rounded-xl bg-white/5 border border-white/10 p-5 flex flex-col backdrop-blur-sm shadow-lg h-full">
                            <div class="text-slate-200 text-sm font-medium mb-4">Aktivitas Terakhir (ANPR)</div>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center pb-2 border-b border-white/5">
                                    <div>
                                        <div class="text-[10px] font-bold tracking-wider text-white bg-slate-800 border border-slate-700 px-2 py-0.5 rounded">B 1234 XYZ</div>
                                        <div class="text-[9px] text-emerald-400 mt-1">Masuk - Gate 1</div>
                                    </div>
                                    <div class="text-[9px] text-slate-500">Baru saja</div>
                                </div>
                                <div class="flex justify-between items-center pb-2 border-b border-white/5">
                                    <div>
                                        <div class="text-[10px] font-bold tracking-wider text-white bg-slate-800 border border-slate-700 px-2 py-0.5 rounded">D 5678 ABC</div>
                                        <div class="text-[9px] text-rose-400 mt-1">Keluar - Gate 2</div>
                                    </div>
                                    <div class="text-[9px] text-slate-500">2 mnt lalu</div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="text-[10px] font-bold tracking-wider text-white bg-slate-800 border border-slate-700 px-2 py-0.5 rounded">L 9999 OP</div>
                                        <div class="text-[9px] text-emerald-400 mt-1">Masuk - Gate 1</div>
                                    </div>
                                    <div class="text-[9px] text-slate-500">5 mnt lalu</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="absolute -top-12 -right-12 w-64 h-64 bg-emerald-500/20 rounded-full blur-[100px] -z-10"></div>
                <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-indigo-500/20 rounded-full blur-[100px] -z-10"></div>
            </div>
    </section>

    <!-- Stats -->
    <section class="py-20 border-y border-white/5 bg-slate-950/50 relative overflow-hidden">
        <div class="absolute inset-0 bg-emerald-500/5 blur-[120px] -z-10"></div>
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-12 text-center">
                <div class="animate-fade-in-up" style="animation-delay: 0.1s">
                    <p class="text-3xl font-extrabold text-white mb-1 tracking-tight">99.9%</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Accuracy</p>
                </div>
                <div class="animate-fade-in-up" style="animation-delay: 0.2s">
                    <p class="text-3xl font-extrabold text-white mb-1 tracking-tight">1.2M</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Scans/Year</p>
                </div>
                <div class="animate-fade-in-up" style="animation-delay: 0.3s">
                    <p class="text-3xl font-extrabold text-white mb-1 tracking-tight">500+</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Enterprises</p>
                </div>
                <div class="animate-fade-in-up" style="animation-delay: 0.4s">
                    <p class="text-3xl font-extrabold text-white mb-1 tracking-tight">24/7</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Support</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Trusted By -->
    <section class="py-12 border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <p class="text-center text-[9px] font-black text-slate-600 uppercase tracking-[0.4em] mb-10">Trusted by Forward-Thinking Infrastructure</p>
            <div class="flex flex-wrap justify-center items-center gap-12 md:gap-24 opacity-30 grayscale hover:grayscale-0 transition-all duration-700">
                <span class="text-xl font-black text-white tracking-tighter italic">MALL-CENTRAL</span>
                <span class="text-xl font-black text-white tracking-tighter italic">AIRPORT-PRO</span>
                <span class="text-xl font-black text-white tracking-tighter italic">HOSPITAL-CORE</span>
                <span class="text-xl font-black text-white tracking-tighter italic">GOV-SECTOR</span>
                <span class="text-xl font-black text-white tracking-tighter italic">OFFICE-HUB</span>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 lg:py-32 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div>
                    <h2 class="text-[10px] lg:text-xs font-bold text-emerald-500 uppercase tracking-[0.3em] mb-4">About Neston</h2>
                    <h3 class="text-3xl lg:text-5xl font-extrabold text-white tracking-tight mb-8">Ecosystem Parkir Pintar Masa Depan.</h3>
                    <p class="text-slate-400 text-base lg:text-lg leading-relaxed mb-6">
                        Neston adalah solusi manajemen parkir terintegrasi yang menggabungkan kecerdasan buatan (AI) dengan sistem pembayaran digital yang mulus. Kami hadir untuk menyelesaikan masalah antrian panjang, kehilangan data kendaraan, dan ketidakefisienan operasional.
                    </p>
                    <p class="text-slate-400 text-base lg:text-lg leading-relaxed mb-8">
                        Dengan teknologi ANPR (Automatic Number Plate Recognition) berbasis YOLOv8, sistem kami mampu mendeteksi plat nomor kendaraan secara real-time dengan akurasi yang sangat tinggi, bahkan dalam kondisi pencahayaan minim.
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 lg:gap-8">
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5">
                            <h4 class="text-white font-bold mb-2">Visi Kami</h4>
                            <p class="text-slate-500 text-xs">Mendigitalisasi infrastruktur parkir di seluruh Indonesia dengan AI.</p>
                        </div>
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5">
                            <h4 class="text-white font-bold mb-2">Misi Kami</h4>
                            <p class="text-slate-500 text-xs">Menyediakan pengalaman parkir tanpa hambatan (seamless) bagi semua orang.</p>
                        </div>
                    </div>
                </div>
                <div class="relative order-first lg:order-2">
                    <div class="aspect-square rounded-3xl bg-gradient-to-br from-emerald-500/20 to-indigo-500/20 border border-white/10 flex items-center justify-center p-8 lg:p-12">
                        <div class="text-center">
                            <i class="fa-solid fa-microchip text-5xl lg:text-7xl text-emerald-500 mb-6"></i>
                            <p class="text-lg lg:text-xl font-bold text-white uppercase tracking-widest">Neural Network Core</p>
                        </div>
                    </div>
                    <!-- Glow -->
                    <div class="absolute inset-0 bg-emerald-500/10 blur-[100px] -z-10"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Workflow Section -->
    <section id="workflow" class="py-20 lg:py-32 bg-slate-950/30">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-16 lg:mb-20">
                <h2 class="text-[10px] lg:text-xs font-bold text-emerald-500 uppercase tracking-[0.3em] mb-4">How it works</h2>
                <h3 class="text-3xl lg:text-5xl font-extrabold text-white tracking-tight">Alur Kerja Sistem Neston</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 relative">
                <!-- Line decoration -->
                <div class="hidden lg:block absolute top-12 left-0 right-0 h-0.5 bg-gradient-to-r from-emerald-500/50 via-indigo-500/50 to-emerald-500/50 -z-10 opacity-20"></div>

                <!-- Step 1 -->
                <div class="text-center group">
                    <div class="w-16 h-16 lg:w-20 lg:h-20 rounded-2xl bg-slate-900 border border-white/10 flex items-center justify-center mx-auto mb-6 group-hover:border-emerald-500 transition-all shadow-xl">
                        <i class="fa-solid fa-camera text-xl lg:text-2xl text-emerald-500"></i>
                    </div>
                    <h4 class="text-white font-bold mb-2">1. Deteksi</h4>
                    <p class="text-slate-500 text-xs leading-relaxed max-w-[200px] mx-auto">Kamera ANPR mendeteksi plat nomor kendaraan secara otomatis saat masuk.</p>
                </div>

                <!-- Step 2 -->
                <div class="text-center group">
                    <div class="w-16 h-16 lg:w-20 lg:h-20 rounded-2xl bg-slate-900 border border-white/10 flex items-center justify-center mx-auto mb-6 group-hover:border-emerald-500 transition-all shadow-xl">
                        <i class="fa-solid fa-map-location-dot text-xl lg:text-2xl text-emerald-500"></i>
                    </div>
                    <h4 class="text-white font-bold mb-2">2. Plot Slot</h4>
                    <p class="text-slate-500 text-xs leading-relaxed max-w-[200px] mx-auto">Sistem mengalokasikan slot parkir yang tersedia secara real-time pada peta digital.</p>
                </div>

                <!-- Step 3 -->
                <div class="text-center group">
                    <div class="w-16 h-16 lg:w-20 lg:h-20 rounded-2xl bg-slate-900 border border-white/10 flex items-center justify-center mx-auto mb-6 group-hover:border-emerald-500 transition-all shadow-xl">
                        <i class="fa-solid fa-wallet text-xl lg:text-2xl text-emerald-500"></i>
                    </div>
                    <h4 class="text-white font-bold mb-2">3. Pembayaran</h4>
                    <p class="text-slate-500 text-xs leading-relaxed max-w-[200px] mx-auto">Pengguna membayar biaya parkir via Midtrans (QRIS) atau Saldo NestonPay.</p>
                </div>

                <!-- Step 4 -->
                <div class="text-center group">
                    <div class="w-16 h-16 lg:w-20 lg:h-20 rounded-2xl bg-slate-900 border border-white/10 flex items-center justify-center mx-auto mb-6 group-hover:border-emerald-500 transition-all shadow-xl">
                        <i class="fa-solid fa-door-open text-xl lg:text-2xl text-emerald-500"></i>
                    </div>
                    <h4 class="text-white font-bold mb-2">4. Selesai</h4>
                    <p class="text-slate-500 text-xs leading-relaxed max-w-[200px] mx-auto">Palang pintu terbuka otomatis setelah validasi pembayaran sukses.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="fitur" class="py-20 lg:py-32">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="mb-16 lg:mb-20">
                <h2 class="text-[10px] lg:text-xs font-bold text-emerald-500 uppercase tracking-[0.3em] mb-4">Core Capabilities</h2>
                <h3 class="text-3xl lg:text-5xl font-extrabold text-white tracking-tight">Everything you need to <br class="hidden lg:block"/> scale your operations.</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
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

                <div class="card-pro card-pro-hover group md:col-span-2 lg:col-span-1">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mb-6 group-hover:bg-emerald-500 group-hover:text-slate-950 transition-all duration-300">
                        <i class="fa-solid fa-id-card text-sm"></i>
                    </div>
                    <h4 class="text-lg font-bold text-white mb-3">RFID Authentication</h4>
                    <p class="text-slate-400 text-sm leading-relaxed font-medium">
                        Contactless check-in/out for registered users using encrypted NFC technology.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Documentation Section -->
    <section id="docs" class="py-32 border-y border-white/5 bg-slate-950/50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="order-2 lg:order-1">
                    <div class="space-y-4">
                        <div class="p-6 rounded-2xl bg-slate-900 border border-white/10 flex gap-4">
                            <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center shrink-0">
                                <span class="text-emerald-500 font-bold text-sm">01</span>
                            </div>
                            <div>
                                <h4 class="text-white font-bold mb-1">User Registration</h4>
                                <p class="text-slate-500 text-xs">Daftarkan akun Anda, tambahkan kendaraan, dan isi saldo NestonPay untuk mulai menggunakan fitur otomatis.</p>
                            </div>
                        </div>
                        <div class="p-6 rounded-2xl bg-slate-900 border border-white/10 flex gap-4">
                            <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center shrink-0">
                                <span class="text-emerald-500 font-bold text-sm">02</span>
                            </div>
                            <div>
                                <h4 class="text-white font-bold mb-1">Vehicle Management</h4>
                                <p class="text-slate-500 text-xs">Kelola data kendaraan Anda (plat nomor, jenis, warna) untuk mempermudah deteksi sistem ANPR.</p>
                            </div>
                        </div>
                        <div class="p-6 rounded-2xl bg-slate-900 border border-white/10 flex gap-4">
                            <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center shrink-0">
                                <span class="text-emerald-500 font-bold text-sm">03</span>
                            </div>
                            <div>
                                <h4 class="text-white font-bold mb-1">Payment Guide</h4>
                                <p class="text-slate-500 text-xs">Pilih transaksi yang aktif, klik bayar, dan pilih metode Midtrans atau Saldo. Simpan struk digital Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="order-1 lg:order-2">
                    <h2 class="text-xs font-bold text-emerald-500 uppercase tracking-[0.3em] mb-4">Documentation</h2>
                    <h3 class="text-3xl lg:text-5xl font-extrabold text-white tracking-tight mb-8">Panduan Cepat Penggunaan.</h3>
                    <p class="text-slate-400 text-lg leading-relaxed mb-8">
                        Kami menyediakan dokumentasi lengkap untuk membantu pengguna dan operator memahami setiap fitur yang ada di ekosistem Neston. Mulai dari pendaftaran hingga manajemen laporan.
                    </p>
                    <a href="#" class="inline-flex items-center gap-2 text-emerald-400 font-bold hover:text-emerald-300 transition-colors group">
                        Baca Dokumentasi Lengkap
                        <i class="fa-solid fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-32">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-[2rem] p-12 lg:p-20 relative overflow-hidden">
                <!-- Background decoration -->
                <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-[80px] -translate-y-1/2 translate-x-1/2"></div>

                <div class="relative z-10 grid lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <h2 class="text-white/80 font-bold uppercase tracking-[0.2em] text-xs mb-4">Contact Us</h2>
                        <h3 class="text-4xl lg:text-5xl font-extrabold text-white tracking-tight mb-8">Ready to transform your parking space?</h3>
                        <p class="text-emerald-50/80 text-lg leading-relaxed mb-10">
                            Hubungi tim ahli kami untuk konsultasi gratis mengenai implementasi Neston di gedung atau fasilitas Anda.
                        </p>
                        <div class="space-y-6">
                            <div class="flex items-center gap-4 text-white">
                                <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>
                                <span class="font-bold">neston2026@gmail.com</span>
                            </div>
                            <div class="flex items-center gap-4 text-white">
                                <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <span class="font-bold">+62 85793344459</span>
                            </div>
                            <div class="flex items-center gap-4 text-white">
                                <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <span class="font-bold">Garut, Indonesia</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-8 shadow-2xl">
                        <form id="contactForm" action="{{ route('contact.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">First Name</label>
                                    <input type="text" name="first_name" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Last Name</label>
                                    <input type="text" name="last_name" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Email Address</label>
                                <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Message</label>
                                <textarea name="message" rows="4" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"></textarea>
                            </div>
                            <button type="submit" id="submitBtn" class="w-full py-4 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition-all shadow-lg flex items-center justify-center gap-2">
                                <span>Send Message</span>
                                <div id="btnSpinner" class="hidden w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-24 border-t border-white/5 bg-slate-950">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-12 mb-16">
                <div class="col-span-2">
                    <a href="#" class="flex items-center space-x-3 mb-6 group">
                        <img src="{{ asset('images/neston.svg') }}" alt="NESTON" class="h-8 w-auto shrink-0">
                        <span class="text-lg font-bold tracking-tight text-white uppercase">NESTON</span>
                    </a>
                    <p class="text-slate-500 text-sm leading-relaxed max-w-xs mb-8 font-medium">
                        Solusi ekosistem parkir modern berbasis AI untuk manajemen kendaraan yang lebih cerdas dan efisien.
                    </p>
                    <div class="flex gap-4">
                        <a href="https://x.com/neston2026" class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-slate-950 transition-all">
                            <i class="fa-brands fa-twitter text-xs"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-slate-950 transition-all">
                            <i class="fa-brands fa-linkedin-in text-xs"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-slate-950 transition-all">
                            <i class="fa-brands fa-github text-xs"></i>
                        </a>
                         <a href="https://www.youtube.com/@NESTON-2026" class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-slate-950 transition-all">
                            <i class="fab fa-youtube text-xs"></i>
                        </a>
                         <a href="#" class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-slate-950 transition-all">
                            <i class="fab fa-discord text-xs"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h5 class="text-white font-bold text-xs uppercase tracking-widest mb-6">Product</h5>
                    <ul class="space-y-4">
                        <li><a href="#fitur" class="text-slate-500 text-xs hover:text-emerald-400 transition-colors">Features</a></li>
                        <li><a href="#workflow" class="text-slate-500 text-xs hover:text-emerald-400 transition-colors">Workflow</a></li>
                        <li><a href="#" class="text-slate-500 text-xs hover:text-emerald-400 transition-colors">Pricing</a></li>
                        <li><a href="#" class="text-slate-500 text-xs hover:text-emerald-400 transition-colors">Mobile App</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="text-white font-bold text-xs uppercase tracking-widest mb-6">Resources</h5>
                    <ul class="space-y-4">
                        <li><a href="#docs" class="text-slate-500 text-xs hover:text-emerald-400 transition-colors">Documentation</a></li>
                        <li><a href="#" class="text-slate-500 text-xs hover:text-emerald-400 transition-colors">Help Center</a></li>
                        <li><a href="#" class="text-slate-500 text-xs hover:text-emerald-400 transition-colors">API Reference</a></li>
                        <li><a href="#" class="text-slate-500 text-xs hover:text-emerald-400 transition-colors">Status</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="text-white font-bold text-xs uppercase tracking-widest mb-6">Company</h5>
                    <ul class="space-y-4">
                        <li><a href="#about" class="text-slate-500 text-xs hover:text-emerald-400 transition-colors">About Us</a></li>
                        <li><a href="#contact" class="text-slate-500 text-xs hover:text-emerald-400 transition-colors">Contact</a></li>
                        <li><a href="#" class="text-slate-500 text-xs hover:text-emerald-400 transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-slate-500 text-xs hover:text-emerald-400 transition-colors">Terms of Service</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-[10px] font-bold text-slate-600 uppercase tracking-widest">
                    © 2026 NESTON CORE SYSTEM. ALL RIGHTS RESERVED.
                </p>
                <div class="flex gap-6">
                    <span class="text-[10px] font-bold text-slate-700 uppercase tracking-widest">Designed for Excellence</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Card Login Modal -->
    <div id="cardLoginModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-6">
        <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm" onclick="closeCardLogin()"></div>
        <div class="relative w-full max-w-md bg-slate-900 border border-white/10 rounded-3xl p-10 shadow-2xl overflow-hidden group">
            <!-- Glow -->
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl"></div>

            <div class="relative z-10 text-center">
                <div class="w-20 h-20 bg-emerald-500/10 rounded-2xl flex items-center justify-center mx-auto mb-8 border border-emerald-500/20 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-id-card text-3xl text-emerald-500"></i>
                </div>

                <h3 class="text-2xl font-extrabold text-white mb-3">Card Authentication</h3>
                <p class="text-slate-400 text-sm leading-relaxed mb-10">
                    Silakan tempelkan kartu Anda pada pembaca kartu (reader) untuk masuk secara otomatis.
                </p>

                <!-- Hidden Input for Scanner -->
                <input type="text" id="cardInput" class="opacity-0 absolute" autofocus>

                <div class="space-y-6">
                    <div id="scanningState" class="flex flex-col items-center">
                        <div class="w-12 h-12 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin mb-4"></div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.3em]">Waiting for card tap...</p>
                    </div>

                    <div id="loginStatus" class="hidden text-sm font-bold"></div>
                </div>

                <button onclick="closeCardLogin()" class="mt-12 text-xs font-bold text-slate-500 hover:text-white uppercase tracking-widest transition-colors">
                    Cancel and go back
                </button>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Contact Form Handler
        const contactForm = document.getElementById('contactForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnSpinner = document.getElementById('btnSpinner');

        if (contactForm) {
            contactForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Show loading state
                submitBtn.disabled = true;
                btnSpinner.classList.remove('hidden');

                const formData = new FormData(this);

                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: result.message,
                            icon: 'success',
                            confirmButtonColor: '#10b981',
                            background: '#0f172a',
                            color: '#fff'
                        });
                        contactForm.reset();
                    } else {
                        throw new Error(result.message || 'Gagal mengirim pesan.');
                    }
                } catch (error) {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'Terjadi kesalahan teknis. Silakan coba lagi nanti.',
                        icon: 'error',
                        confirmButtonColor: '#f43f5e',
                        background: '#0f172a',
                        color: '#fff'
                    });
                } finally {
                    submitBtn.disabled = false;
                    btnSpinner.classList.add('hidden');
                }
            });
        }

        let isCardLoginOpen = false;

        function openCardLogin() {
            const modal = document.getElementById('cardLoginModal');
            const input = document.getElementById('cardInput');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            input.focus();
            isCardLoginOpen = true;

            // Keep focus on input
            document.addEventListener('click', handleInputFocus);
        }

        function closeCardLogin() {
            const modal = document.getElementById('cardLoginModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            isCardLoginOpen = false;
            document.removeEventListener('click', handleInputFocus);
        }

        function handleInputFocus() {
            if (isCardLoginOpen) document.getElementById('cardInput').focus();
        }

        document.getElementById('cardInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const uid = this.value.trim();
                if (uid) {
                    processCardLogin(uid);
                }
                this.value = '';
            }
        });

        async function processCardLogin(uid) {
            const statusDiv = document.getElementById('loginStatus');
            const scanningDiv = document.getElementById('scanningState');

            scanningDiv.classList.add('hidden');
            statusDiv.classList.remove('hidden');
            statusDiv.className = 'text-emerald-400 font-bold';
            statusDiv.innerHTML = 'Verifying card...';

            try {
                const response = await fetch("{{ route('api.rfid.login') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ rfid_uid: uid })
                });

                const result = await response.json();

                if (result.ok) {
                    statusDiv.className = 'text-emerald-400 font-bold';
                    statusDiv.innerHTML = '<i class="fa-solid fa-circle-check mr-2"></i>Access Granted! Redirecting...';
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1000);
                } else {
                    statusDiv.className = 'text-rose-500 font-bold';
                    statusDiv.innerHTML = '<i class="fa-solid fa-circle-xmark mr-2"></i>' + (result.error || 'Failed to authenticate.');
                    setTimeout(() => {
                        statusDiv.classList.add('hidden');
                        scanningDiv.classList.remove('hidden');
                    }, 2000);
                }
            } catch (error) {
                statusDiv.className = 'text-rose-500 font-bold';
                statusDiv.innerHTML = 'Network error. Please try again.';
                setTimeout(() => {
                    statusDiv.classList.add('hidden');
                    scanningDiv.classList.remove('hidden');
                }, 2000);
            }
        }
    </script>
</body>
</html>
