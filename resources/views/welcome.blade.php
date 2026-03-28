<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NESTON - Modern Parking Ecosystem</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/neston-batik.svg') }}">
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
    </style>
</head>
<body class="bg-[#020617] text-slate-100 antialiased selection:bg-emerald-500 selection:text-white">
    <!-- Grid & Glow Overlay -->
    <div class="fixed inset-0 pro-grid pointer-events-none z-0"></div>
    <div class="fixed inset-0 hero-glow pointer-events-none z-0"></div>
    <div class="fixed inset-0 bg-batik opacity-[0.035] pointer-events-none z-0"></div>

    <!-- Header -->
    <nav class="fixed w-full top-0 z-50 bg-[#020617]/70 backdrop-blur-xl border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="#" class="flex items-center space-x-3 group z-10">
                    <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center shadow-[0_0_15px_rgba(16,185,129,0.3)]">
                        <img src="{{ asset('images/neston-batik.svg') }}" alt="N" class="w-5 h-5">
                    </div>
                    <span class="text-lg font-bold tracking-tight text-white uppercase">NESTON</span>
                </a>

                <!-- Nav -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#about" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">About</a>
                    <a href="#workflow" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">Workflow</a>
                    <a href="#fitur" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">Features</a>
                    <a href="#docs" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">Docs</a>
                    <a href="#contact" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">Contact</a>
                    <div class="h-4 w-px bg-white/10"></div>
                    <button onclick="openCardLogin()" class="group flex items-center gap-2 text-xs font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">
                        <i class="fa-solid fa-id-card"></i>
                        Card Login
                    </button>
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
    <section id="about" class="py-32 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-20 items-center">
                <div>
                    <h2 class="text-xs font-bold text-emerald-500 uppercase tracking-[0.3em] mb-4">About Neston</h2>
                    <h3 class="text-3xl lg:text-5xl font-extrabold text-white tracking-tight mb-8">Ecosystem Parkir Pintar Masa Depan.</h3>
                    <p class="text-slate-400 text-lg leading-relaxed mb-6">
                        Neston adalah solusi manajemen parkir terintegrasi yang menggabungkan kecerdasan buatan (AI) dengan sistem pembayaran digital yang mulus. Kami hadir untuk menyelesaikan masalah antrian panjang, kehilangan data kendaraan, dan ketidakefisienan operasional.
                    </p>
                    <p class="text-slate-400 text-lg leading-relaxed mb-8">
                        Dengan teknologi ANPR (Automatic Number Plate Recognition) berbasis YOLOv8, sistem kami mampu mendeteksi plat nomor kendaraan secara real-time dengan akurasi yang sangat tinggi, bahkan dalam kondisi pencahayaan minim.
                    </p>
                    <div class="grid grid-cols-2 gap-8">
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
                <div class="relative">
                    <div class="aspect-square rounded-3xl bg-gradient-to-br from-emerald-500/20 to-indigo-500/20 border border-white/10 flex items-center justify-center p-12">
                        <div class="text-center">
                            <i class="fa-solid fa-microchip text-7xl text-emerald-500 mb-6"></i>
                            <p class="text-xl font-bold text-white uppercase tracking-widest">Neural Network Core</p>
                        </div>
                    </div>
                    <!-- Glow -->
                    <div class="absolute inset-0 bg-emerald-500/10 blur-[100px] -z-10"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Workflow Section -->
    <section id="workflow" class="py-32 bg-slate-950/30">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-xs font-bold text-emerald-500 uppercase tracking-[0.3em] mb-4">How it works</h2>
                <h3 class="text-3xl lg:text-5xl font-extrabold text-white tracking-tight">Alur Kerja Sistem Neston</h3>
            </div>

            <div class="grid md:grid-cols-4 gap-8 relative">
                <!-- Line decoration -->
                <div class="hidden md:block absolute top-12 left-0 right-0 h-0.5 bg-gradient-to-r from-emerald-500/50 via-indigo-500/50 to-emerald-500/50 -z-10 opacity-20"></div>

                <!-- Step 1 -->
                <div class="text-center group">
                    <div class="w-20 h-20 rounded-2xl bg-slate-900 border border-white/10 flex items-center justify-center mx-auto mb-6 group-hover:border-emerald-500 transition-all shadow-xl">
                        <i class="fa-solid fa-camera-viewfinder text-2xl text-emerald-500"></i>
                    </div>
                    <h4 class="text-white font-bold mb-2">1. Deteksi</h4>
                    <p class="text-slate-500 text-xs leading-relaxed">Kamera ANPR mendeteksi plat nomor kendaraan secara otomatis saat masuk.</p>
                </div>

                <!-- Step 2 -->
                <div class="text-center group">
                    <div class="w-20 h-20 rounded-2xl bg-slate-900 border border-white/10 flex items-center justify-center mx-auto mb-6 group-hover:border-emerald-500 transition-all shadow-xl">
                        <i class="fa-solid fa-map-location-dot text-2xl text-emerald-500"></i>
                    </div>
                    <h4 class="text-white font-bold mb-2">2. Plot Slot</h4>
                    <p class="text-slate-500 text-xs leading-relaxed">Sistem mengalokasikan slot parkir yang tersedia secara real-time pada peta digital.</p>
                </div>

                <!-- Step 3 -->
                <div class="text-center group">
                    <div class="w-20 h-20 rounded-2xl bg-slate-900 border border-white/10 flex items-center justify-center mx-auto mb-6 group-hover:border-emerald-500 transition-all shadow-xl">
                        <i class="fa-solid fa-wallet text-2xl text-emerald-500"></i>
                    </div>
                    <h4 class="text-white font-bold mb-2">3. Pembayaran</h4>
                    <p class="text-slate-500 text-xs leading-relaxed">Pengguna membayar biaya parkir via Midtrans (QRIS) atau Saldo NestonPay.</p>
                </div>

                <!-- Step 4 -->
                <div class="text-center group">
                    <div class="w-20 h-20 rounded-2xl bg-slate-900 border border-white/10 flex items-center justify-center mx-auto mb-6 group-hover:border-emerald-500 transition-all shadow-xl">
                        <i class="fa-solid fa-door-open text-2xl text-emerald-500"></i>
                    </div>
                    <h4 class="text-white font-bold mb-2">4. Selesai</h4>
                    <p class="text-slate-500 text-xs leading-relaxed">Palang pintu terbuka otomatis setelah validasi pembayaran sukses.</p>
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

    <!-- Documentation Section -->
    <section id="docs" class="py-32 border-y border-white/5 bg-slate-950/50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="order-2 lg:order-1">
                    <div class="space-y-4">
                        <div class="p-6 rounded-2xl bg-slate-900 border border-white/10 flex gap-4">
                            <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                                <span class="text-emerald-500 font-bold text-sm">01</span>
                            </div>
                            <div>
                                <h4 class="text-white font-bold mb-1">User Registration</h4>
                                <p class="text-slate-500 text-xs">Daftarkan akun Anda, tambahkan kendaraan, dan isi saldo NestonPay untuk mulai menggunakan fitur otomatis.</p>
                            </div>
                        </div>
                        <div class="p-6 rounded-2xl bg-slate-900 border border-white/10 flex gap-4">
                            <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                                <span class="text-emerald-500 font-bold text-sm">02</span>
                            </div>
                            <div>
                                <h4 class="text-white font-bold mb-1">Vehicle Management</h4>
                                <p class="text-slate-500 text-xs">Kelola data kendaraan Anda (plat nomor, jenis, warna) untuk mempermudah deteksi sistem ANPR.</p>
                            </div>
                        </div>
                        <div class="p-6 rounded-2xl bg-slate-900 border border-white/10 flex gap-4">
                            <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
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
                                <span class="font-bold">hello@neston.id</span>
                            </div>
                            <div class="flex items-center gap-4 text-white">
                                <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <span class="font-bold">+62 812 3456 7890</span>
                            </div>
                            <div class="flex items-center gap-4 text-white">
                                <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <span class="font-bold">Jakarta, Indonesia</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-8 shadow-2xl">
                        <form action="#" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">First Name</label>
                                    <input type="text" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Last Name</label>
                                    <input type="text" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Email Address</label>
                                <input type="email" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Message</label>
                                <textarea rows="4" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"></textarea>
                            </div>
                            <button type="submit" class="w-full py-4 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition-all shadow-lg">
                                Send Message
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
                        <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center shadow-[0_0_15px_rgba(16,185,129,0.3)]">
                            <img src="{{ asset('images/neston-batik.svg') }}" alt="N" class="w-5 h-5">
                        </div>
                        <span class="text-lg font-bold tracking-tight text-white uppercase">NESTON</span>
                    </a>
                    <p class="text-slate-500 text-sm leading-relaxed max-w-xs mb-8 font-medium">
                        Solusi ekosistem parkir modern berbasis AI untuk manajemen kendaraan yang lebih cerdas dan efisien.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-slate-950 transition-all">
                            <i class="fa-brands fa-twitter text-xs"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-slate-950 transition-all">
                            <i class="fa-brands fa-linkedin-in text-xs"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-slate-950 transition-all">
                            <i class="fa-brands fa-github text-xs"></i>
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

    <script>
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
