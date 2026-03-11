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
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            scroll-behavior: smooth;
        }

        /* Metallic Grid Patterns */
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

        /* Specific Animations */
        @keyframes pulse-border {
            from { border-color: rgba(255,255,255,0.1); box-shadow: 0 0 20px rgba(255,255,255,0); }
            to { border-color: rgba(255,255,255,0.2); box-shadow: 0 0 40px rgba(255,255,255,0.05); }
        }
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 antialiased selection:bg-white selection:text-black">
    <!-- Grid Overlay -->
    <div class="fixed inset-0 metallic-grid pointer-events-none z-0"></div>
    <div class="fixed inset-0 metallic-grid-fine pointer-events-none z-0"></div>
    
    <!-- Navbar -->
    <nav class="fixed w-full top-0 z-50 bg-zinc-950/80 backdrop-blur-xl border-b border-white/10">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="#" class="flex items-center space-x-3 group relative z-10">
                    <img src="{{ asset('images/neston.png') }}" alt="NESTON" class="w-9 h-9 rounded-lg invert brightness-200">
                    <span class="text-xl font-bold tracking-tight text-white uppercase">NESTON</span>
                </a>

                <!-- Desktop Navigation -->
                <ul class="hidden md:flex space-x-10 relative z-10">
                    <li><a href="#fitur" class="text-[11px] font-bold text-zinc-400 hover:text-white transition-colors tracking-widest uppercase">FITUR</a></li>
                    <li><a href="#teknologi" class="text-[11px] font-bold text-zinc-400 hover:text-white transition-colors tracking-widest uppercase">TEKNOLOGI</a></li>
                    <li><a href="#faq" class="text-[11px] font-bold text-zinc-400 hover:text-white transition-colors tracking-widest uppercase">FAQ</a></li>
                </ul>

                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center space-x-6 relative z-10">
                    <a href="{{ route('login.create') }}" class="text-xs font-bold text-zinc-400 hover:text-white transition-all uppercase tracking-widest">
                        Masuk
                    </a>
                    <a href="{{ route('register.create') }}" class="bg-white text-black hover:bg-zinc-100 hover:shadow-[0_0_25px_rgba(255,255,255,0.2)] transition-all duration-300 hover:scale-[1.02] active:scale-95 px-6 py-2.5 rounded-full text-xs font-bold uppercase tracking-widest">
                        Mulai
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <button class="md:hidden p-2 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="relative pt-44 pb-32 overflow-hidden bg-[radial-gradient(circle_at_50%_50%,rgba(255,255,255,0.05)_0%,transparent_70%)]">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Hero Content -->
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-900/50 border border-white/10 mb-8 backdrop-blur-md">
                        <span class="flex h-1.5 w-1.5 rounded-full bg-white animate-pulse"></span>
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em]">Neston v2.0 Live</span>
                    </div>
                    <h1 class="text-6xl lg:text-8xl font-bold leading-[0.95] tracking-tighter mb-8 bg-gradient-to-br from-white via-white to-zinc-500 bg-clip-text text-transparent">
                        Parkir Masa <br/>
                        <span class="text-zinc-500 font-light italic">Depan.</span>
                    </h1>
                    <p class="text-lg text-zinc-400 leading-relaxed mb-12 max-w-lg font-medium">
                        Ekosistem manajemen parkir cerdas berbasis AI. Keamanan tingkat militer dengan pengalaman pengguna yang tanpa hambatan.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-5">
                        <a href="{{ route('register.create') }}" class="bg-white text-black hover:bg-zinc-100 hover:shadow-[0_0_25px_rgba(255,255,255,0.2)] transition-all duration-300 hover:scale-[1.02] active:scale-95 px-10 py-5 rounded-full font-bold text-sm uppercase tracking-widest flex items-center justify-center gap-3">
                            Mulai Sekarang
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                        <a href="#fitur" class="border border-white/20 text-white hover:bg-white/5 hover:border-white/40 transition-all duration-300 px-10 py-5 rounded-full font-bold text-sm uppercase tracking-widest flex items-center justify-center">
                            Pelajari
                        </a>
                    </div>
                    
                    <div class="mt-20 flex items-center gap-12">
                        <div>
                            <p class="text-3xl font-bold text-white tracking-tighter">99.9%</p>
                            <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-[0.2em] mt-1">Uptime AI</p>
                        </div>
                        <div class="w-px h-10 bg-white/10"></div>
                        <div>
                            <p class="text-3xl font-bold text-white tracking-tighter">150+</p>
                            <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-[0.2em] mt-1">Lokasi Aktif</p>
                        </div>
                    </div>
                </div>

                <!-- Hero Globe Visual -->
                <div class="relative flex items-center justify-center lg:justify-end">
                    <div class="relative w-full h-full flex items-center justify-center">
                        <div class="w-[500px] h-[500px] bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.03)_0%,transparent_70%)] rounded-full relative border border-white/10 [animation:pulse-border_4s_infinite_alternate]">
                            <!-- SVG Globe Visual -->
                            <svg viewBox="0 0 100 100" class="w-full h-full opacity-40 animate-[spin_60s_linear_infinite]">
                                <circle cx="50" cy="50" r="48" fill="none" stroke="white" stroke-width="0.1" stroke-dasharray="1 2" />
                                <circle cx="50" cy="50" r="38" fill="none" stroke="white" stroke-width="0.05" />
                                <path d="M50 2 A48 48 0 0 1 50 98" fill="none" stroke="white" stroke-width="0.1" />
                                <path d="M2 50 A48 48 0 0 1 98 50" fill="none" stroke="white" stroke-width="0.1" />
                                <ellipse cx="50" cy="50" rx="48" ry="20" fill="none" stroke="white" stroke-width="0.1" />
                                <ellipse cx="50" cy="50" rx="20" ry="48" fill="none" stroke="white" stroke-width="0.1" />
                            </svg>
                            <!-- Floating Data Points -->
                            <div class="absolute top-1/4 left-1/4 w-1.5 h-1.5 bg-white rounded-full shadow-[0_0_10px_white] animate-pulse"></div>
                            <div class="absolute bottom-1/3 right-1/4 w-1 h-1 bg-white rounded-full shadow-[0_0_8px_white] animate-pulse" style="animation-delay: 1s"></div>
                            <div class="absolute top-1/2 right-1/3 w-1.5 h-1.5 bg-white rounded-full shadow-[0_0_12px_white] animate-pulse" style="animation-delay: 2s"></div>
                        </div>
                        
                        <!-- Floating Card -->
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-72 p-6 rounded-3xl bg-zinc-800/40 border border-white/20 backdrop-blur-md transition-all duration-400 hover:bg-zinc-800/60 hover:-translate-y-[60%]">
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                                    <i class="fa-solid fa-microchip text-white text-xs"></i>
                                </div>
                                <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">System Status</span>
                            </div>
                            <div class="space-y-4">
                                <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                                    <div class="h-full bg-white w-3/4 animate-[shimmer_2s_infinite]"></div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[11px] text-zinc-400">ANPR Detection</span>
                                    <span class="text-[11px] text-white font-bold">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="fitur" class="py-32 relative z-10">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-28">
                <h2 class="text-[10px] font-bold text-zinc-500 uppercase tracking-[0.5em] mb-6">Capabilities</h2>
                <h3 class="text-4xl lg:text-6xl font-bold tracking-tighter leading-[1.1] bg-gradient-to-br from-white via-white to-zinc-500 bg-clip-text text-transparent">
                    Teknologi mutakhir untuk <br/> <span class="font-light italic">efisiensi total.</span>
                </h3>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="p-10 rounded-[2.5rem] bg-zinc-800/40 border border-white/10 backdrop-blur-md hover:bg-zinc-800/60 hover:border-white/20 transition-all duration-500 hover:-translate-y-2 group">
                    <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mb-8 group-hover:bg-white group-hover:text-black transition-all duration-500">
                        <i class="fa-solid fa-bolt-lightning text-xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-4 tracking-tight">Real-time ANPR</h4>
                    <p class="text-zinc-500 text-sm font-medium leading-relaxed">
                        Pengenalan plat nomor otomatis dengan akurasi 99% menggunakan model YOLOv8 terbaru.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="p-10 rounded-[2.5rem] bg-zinc-800/40 border border-white/10 backdrop-blur-md hover:bg-zinc-800/60 hover:border-white/20 transition-all duration-500 hover:-translate-y-2 group">
                    <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mb-8 group-hover:bg-white group-hover:text-black transition-all duration-500">
                        <i class="fa-solid fa-wallet text-xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-4 tracking-tight">NestonPay</h4>
                    <p class="text-zinc-500 text-sm font-medium leading-relaxed">
                        Sistem pembayaran digital terintegrasi. Transaksi instan tanpa antrian, tanpa repot.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="p-10 rounded-[2.5rem] bg-zinc-800/40 border border-white/10 backdrop-blur-md hover:bg-zinc-800/60 hover:border-white/20 transition-all duration-500 hover:-translate-y-2 group">
                    <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mb-8 group-hover:bg-white group-hover:text-black transition-all duration-500">
                        <i class="fa-solid fa-map-location-dot text-xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-4 tracking-tight">Smart Mapping</h4>
                    <p class="text-zinc-500 text-sm font-medium leading-relaxed">
                        Visualisasi slot parkir 2D/3D yang memudahkan navigasi pengguna di area parkir yang luas.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-20 border-t border-white/5 relative z-10">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-10">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/neston.png') }}" alt="NESTON" class="w-7 h-7 invert opacity-50">
                    <span class="text-sm font-bold tracking-widest text-zinc-500 uppercase">NESTON</span>
                </div>
                
                <div class="flex space-x-10">
                    <a href="#" class="text-[10px] font-bold text-zinc-500 hover:text-white uppercase tracking-widest transition-colors">Privacy</a>
                    <a href="#" class="text-[10px] font-bold text-zinc-500 hover:text-white uppercase tracking-widest transition-colors">Terms</a>
                    <a href="#" class="text-[10px] font-bold text-zinc-500 hover:text-white uppercase tracking-widest transition-colors">Status</a>
                </div>

                <p class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest">
                    © 2026 NESTON CORE. ALL RIGHTS RESERVED.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
