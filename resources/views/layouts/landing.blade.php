<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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

    @keyframes marquee {
        0%   { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    #marquee {
        animation: marquee 18s linear infinite;
    }
    #marquee:hover {
        animation-play-state: paused;
    }
    </style>
</head>
<body class="bg-[#020617] text-slate-100 antialiased selection:bg-emerald-500 selection:text-white">
    <!-- Grid & Glow Overlay -->
    <div class="fixed inset-0 pro-grid pointer-events-none z-0"></div>
    <div class="fixed inset-0 hero-glow pointer-events-none z-0"></div>
    <div class="fixed inset-0 bg-batik opacity-[0.035] pointer-events-none z-0"></div>

    @include('components.header')

    <!-- Hero -->
    <section class="relative pt-40 pb-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 mb-8">
                    <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest">{{ __('Enterprise Parking Solution') }}</span>
                </div>
                <h1 class="text-5xl lg:text-6xl font-extrabold tracking-tight text-white mb-8 leading-[1.1]">
                    {{ __('MANAGE YOUR PARKING') }}<br/>
                    <span class="text-white">{{ __('WITH') }}</span>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-emerald-600"> {{ __('INTELLIGENCE.') }}</span>
                </h1>
                <p class="text-lg text-slate-400 leading-relaxed mb-12 max-w-2xl mx-auto font-medium">
                    {{ __('Automate vehicle tracking, payment processing, and space management with our AI-powered ecosystem. Secure, scalable, and built for modern infrastructure.') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="btn-pro-primary !px-8 !py-4 text-sm uppercase tracking-widest">
                        {{ __('Start for Free') }}
                    </a>
                    <a href="#fitur" class="btn-pro-outline !px-8 !py-4 text-sm uppercase tracking-widest">
                        {{ __('View Demo') }}
                    </a>
                </div>
            </div>

            <div class="mt-16 lg:mt-24 relative max-w-5xl mx-auto">
                <div class="aspect-auto lg:aspect-[16/9] min-h-[500px] lg:min-h-0 rounded-2xl bg-slate-900 border border-white/5 shadow-2xl overflow-hidden relative group">
                    <div class="absolute inset-0 bg-gradient-to-tr from-emerald-500/10 via-transparent to-indigo-500/5"></div>

                    <div class="absolute top-0 left-0 right-0 h-7 bg-white/5 border-b border-white/5 flex items-center px-4 gap-2 z-10">
                        <div class="w-3 h-3 rounded-full bg-red-500/50"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-500/50"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-500/50"></div>
                        <div class="ml-4 text-[10px] sm:text-xs text-slate-500 font-mono truncate">NESTON PARK DATA</div>
                    </div>

                    <div class="p-4 sm:p-12 pt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 h-full overflow-y-auto lg:overflow-hidden lg:absolute lg:inset-0">

                        <div class="h-28 rounded-xl bg-white/5 border border-white/10 p-5 flex flex-col justify-between backdrop-blur-sm shadow-lg">
                            <div class="text-slate-400 text-sm font-medium flex justify-between items-center">
                                <span>{{ __('Slot Tersedia') }}</span>
                                <span class="text-emerald-400 bg-emerald-400/10 px-2 py-0.5 rounded text-[10px]">{{ __('Live') }}</span>
                            </div>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl lg:text-4xl font-bold text-white">142</span>
                                <span class="text-slate-500 text-xs font-medium">/ 500 {{ __('total') }}</span>
                            </div>
                        </div>

                        <div class="h-28 rounded-xl bg-white/5 border border-white/10 p-5 flex flex-col justify-between backdrop-blur-sm shadow-lg">
                            <div class="text-slate-400 text-sm font-medium">{{ __('Kendaraan Masuk') }}</div>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl lg:text-4xl font-bold text-white">358</span>
                                <span class="text-emerald-400 text-xs font-medium">↑ 12%</span>
                            </div>
                        </div>

                        <div class="h-28 sm:col-span-2 lg:col-span-1 rounded-xl bg-white/5 border border-white/10 p-5 flex flex-col justify-between backdrop-blur-sm shadow-lg">
                            <div class="text-slate-400 text-sm font-medium">{{ __('Pendapatan (Harian)') }}</div>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-bold text-white">Rp 2.4M</span>
                            </div>
                        </div>

                        <div class="sm:col-span-2 rounded-xl bg-white/5 border border-white/10 p-5 flex flex-col backdrop-blur-sm shadow-lg h-full min-h-[200px]">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2">
                                <div class="text-slate-200 text-sm font-medium">{{ __('Live Area Monitoring - Lantai 1') }}</div>
                                <div class="flex gap-3 text-[10px]">
                                    <span class="flex items-center gap-1"><div class="w-2 h-2 rounded-full bg-emerald-400"></div> {{ __('Kosong') }}</span>
                                    <span class="flex items-center gap-1"><div class="w-2 h-2 rounded-full bg-rose-500"></div> {{ __('Terisi') }}</span>
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
                            <div class="text-slate-200 text-sm font-medium mb-4">{{ __('Aktivitas Terakhir (ANPR)') }}</div>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center pb-2 border-b border-white/5">
                                    <div>
                                        <div class="text-[10px] font-bold tracking-wider text-white bg-slate-800 border border-slate-700 px-2 py-0.5 rounded">B 1234 XYZ</div>
                                        <div class="text-[9px] text-emerald-400 mt-1">{{ __('Masuk - Gate 1') }}</div>
                                    </div>
                                    <div class="text-[9px] text-slate-500">{{ __('Baru saja') }}</div>
                                </div>
                                <div class="flex justify-between items-center pb-2 border-b border-white/5">
                                    <div>
                                        <div class="text-[10px] font-bold tracking-wider text-white bg-slate-800 border border-slate-700 px-2 py-0.5 rounded">D 5678 ABC</div>
                                        <div class="text-[9px] text-rose-400 mt-1">{{ __('Keluar - Gate 2') }}</div>
                                    </div>
                                    <div class="text-[9px] text-slate-500">2 {{ __('mnt lalu') }}</div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="text-[10px] font-bold tracking-wider text-white bg-slate-800 border border-slate-700 px-2 py-0.5 rounded">L 9999 OP</div>
                                        <div class="text-[9px] text-emerald-400 mt-1">{{ __('Masuk - Gate 1') }}</div>
                                    </div>
                                    <div class="text-[9px] text-slate-500">5 {{ __('mnt lalu') }}</div>
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
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Accuracy') }}</p>
                </div>
                <div class="animate-fade-in-up" style="animation-delay: 0.2s">
                    <p class="text-3xl font-extrabold text-white mb-1 tracking-tight">1.2M</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Scans/Year') }}</p>
                </div>
                <div class="animate-fade-in-up" style="animation-delay: 0.3s">
                    <p class="text-3xl font-extrabold text-white mb-1 tracking-tight">500+</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Enterprises') }}</p>
                </div>
                <div class="animate-fade-in-up" style="animation-delay: 0.4s">
                    <p class="text-3xl font-extrabold text-white mb-1 tracking-tight">24/7</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Support') }}</p>
                </div>
            </div>
        </div>
    </section>

<!-- Trusted By -->
<section class="py-12 border-b border-white/5">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <p class="text-center text-[9px] font-black text-slate-600 uppercase tracking-[0.4em] mb-10">
            {{ __('Trusted by Forward-Thinking Infrastructure') }}
        </p>

        <div class="overflow-hidden relative" style="mask-image: linear-gradient(to right, transparent 0%, black 12%, black 88%, transparent 100%); -webkit-mask-image: linear-gradient(to right, transparent 0%, black 12%, black 88%, transparent 100%);">
            <div id="marquee" class="flex items-center gap-16 w-max">
                <span class="text-xl font-black text-white tracking-tighter italic opacity-30 whitespace-nowrap">MALL-CENTRAL</span>
                <span class="text-xl font-black text-white tracking-tighter italic opacity-30 whitespace-nowrap">AIRPORT-PRO</span>
                <span class="text-xl font-black text-white tracking-tighter italic opacity-30 whitespace-nowrap">HOSPITAL-CORE</span>
                <span class="text-xl font-black text-white tracking-tighter italic opacity-30 whitespace-nowrap">GOV-SECTOR</span>
                <span class="text-xl font-black text-white tracking-tighter italic opacity-30 whitespace-nowrap">OFFICE-HUB</span>
                {{-- Duplikat untuk seamless loop --}}
                <span class="text-xl font-black text-white tracking-tighter italic opacity-30 whitespace-nowrap">MALL-CENTRAL</span>
                <span class="text-xl font-black text-white tracking-tighter italic opacity-30 whitespace-nowrap">AIRPORT-PRO</span>
                <span class="text-xl font-black text-white tracking-tighter italic opacity-30 whitespace-nowrap">HOSPITAL-CORE</span>
                <span class="text-xl font-black text-white tracking-tighter italic opacity-30 whitespace-nowrap">GOV-SECTOR</span>
                <span class="text-xl font-black text-white tracking-tighter italic opacity-30 whitespace-nowrap">OFFICE-HUB</span>
            </div>
        </div>
    </div>
</section>


    <!-- About Section -->
    <section id="about" class="py-20 lg:py-32 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div>
                    <h2 class="text-[10px] lg:text-xs font-bold text-emerald-500 uppercase tracking-[0.3em] mb-4">{{ __('About Neston') }}</h2>
                    <h3 class="text-3xl lg:text-5xl font-extrabold text-white tracking-tight mb-8">{{ __('Ecosystem Parkir Pintar Masa Depan.') }}</h3>
                    <p class="text-slate-400 text-base lg:text-lg leading-relaxed mb-6">
                        {{ __('Neston adalah solusi manajemen parkir terintegrasi yang menggabungkan kecerdasan buatan (AI) dengan sistem pembayaran digital yang mulus. Kami hadir untuk menyelesaikan masalah antrian panjang, kehilangan data kendaraan, dan ketidakefisienan operasional.') }}
                    </p>
                    <p class="text-slate-400 text-base lg:text-lg leading-relaxed mb-8">
                        {{ __('Dengan teknologi ANPR (Automatic Number Plate Recognition) berbasis YOLOv8, sistem kami mampu mendeteksi plat nomor kendaraan secara real-time dengan akurasi yang sangat tinggi, bahkan dalam kondisi pencahayaan minim.') }}
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 lg:gap-8">
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5">
                            <h4 class="text-white font-bold mb-2">{{ __('Visi Kami') }}</h4>
                            <p class="text-slate-500 text-xs">{{ __('Mendigitalisasi infrastruktur parkir di seluruh Indonesia dengan AI.') }}</p>
                        </div>
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5">
                            <h4 class="text-white font-bold mb-2">{{ __('Misi Kami') }}</h4>
                            <p class="text-slate-500 text-xs">{{ __('Menyediakan pengalaman parkir tanpa hambatan (seamless) bagi semua orang.') }}</p>
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
                <h2 class="text-[10px] lg:text-xs font-bold text-emerald-500 uppercase tracking-[0.3em] mb-4">{{ __('How it works') }}</h2>
                <h3 class="text-3xl lg:text-5xl font-extrabold text-white tracking-tight">{{ __('Alur Kerja Sistem Neston') }}</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 relative">
                <!-- Line decoration -->
                <div class="hidden lg:block absolute top-12 left-0 right-0 h-0.5 bg-gradient-to-r from-emerald-500/50 via-indigo-500/50 to-emerald-500/50 -z-10 opacity-20"></div>

                <!-- Step 1 -->
                <div class="text-center group">
                    <div class="w-16 h-16 lg:w-20 lg:h-20 rounded-2xl bg-slate-900 border border-white/10 flex items-center justify-center mx-auto mb-6 group-hover:border-emerald-500 transition-all shadow-xl">
                        <i class="fa-solid fa-camera text-xl lg:text-2xl text-emerald-500"></i>
                    </div>
                    <h4 class="text-white font-bold mb-2">{{ __('1. Deteksi') }}</h4>
                    <p class="text-slate-500 text-xs leading-relaxed max-w-[200px] mx-auto">{{ __('Kamera ANPR mendeteksi plat nomor kendaraan secara otomatis saat masuk.') }}</p>
                </div>

                <!-- Step 2 -->
                <div class="text-center group">
                    <div class="w-16 h-16 lg:w-20 lg:h-20 rounded-2xl bg-slate-900 border border-white/10 flex items-center justify-center mx-auto mb-6 group-hover:border-emerald-500 transition-all shadow-xl">
                        <i class="fa-solid fa-map-location-dot text-xl lg:text-2xl text-emerald-500"></i>
                    </div>
                    <h4 class="text-white font-bold mb-2">{{ __('2. Plot Slot') }}</h4>
                    <p class="text-slate-500 text-xs leading-relaxed max-w-[200px] mx-auto">{{ __('Sistem mengalokasikan slot parkir yang tersedia secara real-time pada peta digital.') }}</p>
                </div>

                <!-- Step 3 -->
                <div class="text-center group">
                    <div class="w-16 h-16 lg:w-20 lg:h-20 rounded-2xl bg-slate-900 border border-white/10 flex items-center justify-center mx-auto mb-6 group-hover:border-emerald-500 transition-all shadow-xl">
                        <i class="fa-solid fa-wallet text-xl lg:text-2xl text-emerald-500"></i>
                    </div>
                    <h4 class="text-white font-bold mb-2">{{ __('3. Pembayaran') }}</h4>
                    <p class="text-slate-500 text-xs leading-relaxed max-w-[200px] mx-auto">{{ __('Pengguna membayar biaya parkir via Midtrans (QRIS) atau Saldo NestonPay.') }}</p>
                </div>

                <!-- Step 4 -->
                <div class="text-center group">
                    <div class="w-16 h-16 lg:w-20 lg:h-20 rounded-2xl bg-slate-900 border border-white/10 flex items-center justify-center mx-auto mb-6 group-hover:border-emerald-500 transition-all shadow-xl">
                        <i class="fa-solid fa-door-open text-xl lg:text-2xl text-emerald-500"></i>
                    </div>
                    <h4 class="text-white font-bold mb-2">{{ __('4. Selesai') }}</h4>
                    <p class="text-slate-500 text-xs leading-relaxed max-w-[200px] mx-auto">{{ __('Palang pintu terbuka otomatis setelah validasi pembayaran sukses.') }}</p>
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
                                <h4 class="text-white font-bold mb-1">{{ __('User Registration') }}</h4>
                                <p class="text-slate-500 text-xs">{{ __('Daftarkan akun Anda, tambahkan kendaraan, dan isi saldo NestonPay untuk mulai menggunakan fitur otomatis.') }}</p>
                            </div>
                        </div>
                        <div class="p-6 rounded-2xl bg-slate-900 border border-white/10 flex gap-4">
                            <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center shrink-0">
                                <span class="text-emerald-500 font-bold text-sm">02</span>
                            </div>
                            <div>
                                <h4 class="text-white font-bold mb-1">{{ __('Vehicle Management') }}</h4>
                                <p class="text-slate-500 text-xs">{{ __('Kelola data kendaraan Anda (plat nomor, jenis, warna) untuk mempermudah deteksi sistem ANPR.') }}</p>
                            </div>
                        </div>
                        <div class="p-6 rounded-2xl bg-slate-900 border border-white/10 flex gap-4">
                            <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center shrink-0">
                                <span class="text-emerald-500 font-bold text-sm">03</span>
                            </div>
                            <div>
                                <h4 class="text-white font-bold mb-1">{{ __('Payment Guide') }}</h4>
                                <p class="text-slate-500 text-xs">{{ __('Pilih transaksi yang aktif, klik bayar, dan pilih metode Midtrans atau Saldo. Simpan struk digital Anda.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="order-1 lg:order-2">
                    <h2 class="text-xs font-bold text-emerald-500 uppercase tracking-[0.3em] mb-4">{{ __('Documentation') }}</h2>
                    <h3 class="text-3xl lg:text-5xl font-extrabold text-white tracking-tight mb-8">{{ __('Panduan Cepat Penggunaan.') }}</h3>
                    <p class="text-slate-400 text-lg leading-relaxed mb-8">
                        {{ __('Kami menyediakan dokumentasi lengkap untuk membantu pengguna dan operator memahami setiap fitur yang ada di ekosistem Neston. Mulai dari pendaftaran hingga manajemen laporan.') }}
                    </p>
                    <a href="{{ route('docs') }}" class="inline-flex items-center gap-2 text-emerald-400 font-bold hover:text-emerald-300 transition-colors group">
                        {{ __('Baca Dokumentasi Lengkap') }}
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
                        <h2 class="text-white/80 font-bold uppercase tracking-[0.2em] text-xs mb-4">{{ __('Contact Us') }}</h2>
                        <h3 class="text-4xl lg:text-5xl font-extrabold text-white tracking-tight mb-8">{{ __('Ready to transform your parking space?') }}</h3>
                        <p class="text-emerald-50/80 text-lg leading-relaxed mb-10">
                            {{ __('Hubungi tim ahli kami untuk konsultasi gratis mengenai implementasi Neston di gedung atau fasilitas Anda.') }}
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
                                <span class="font-bold">{{ __('Garut, Indonesia') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-8 shadow-2xl">
                        <form id="contactForm" action="{{ route('contact.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">{{ __('First Name') }}</label>
                                    <input type="text" name="first_name" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">{{ __('Last Name') }}</label>
                                    <input type="text" name="last_name" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">{{ __('Email Address') }}</label>
                                <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">{{ __('Message') }}</label>
                                <textarea name="message" rows="4" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"></textarea>
                            </div>
                            <button type="submit" id="submitBtn" class="w-full py-4 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition-all shadow-lg flex items-center justify-center gap-2">
                                <span>{{ __('Send Message') }}</span>
                                <div id="btnSpinner" class="hidden w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

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

    @include('components.footer')

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
