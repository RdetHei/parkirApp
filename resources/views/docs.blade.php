<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - NESTON Parking Ecosystem</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/neston-batik.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; scroll-behavior: smooth; }
        .pro-grid { background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px); background-size: 32px 32px; }
        .hero-glow { background: radial-gradient(circle at 50% 50%, rgba(16, 185, 129, 0.1) 0%, transparent 50%); }

        [x-cloak] { display: none !important; }

        .doc-card { @apply bg-white/[0.02] border border-white/5 backdrop-blur-xl p-8 rounded-[2.5rem] transition-all duration-300; }
        .doc-card:hover { @apply border-emerald-500/20 bg-white/[0.04] -translate-y-1; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.05); border-radius: 10px; }
    </style>
</head>
<body class="bg-[#020617] text-slate-100 antialiased selection:bg-emerald-500 selection:text-white" x-data="{ activeTab: 'intro' }">
    <div class="fixed inset-0 pro-grid pointer-events-none z-0"></div>
    <div class="fixed inset-0 hero-glow pointer-events-none z-0"></div>

    <!-- Header -->
    <nav class="fixed w-full top-0 z-50 bg-[#020617]/70 backdrop-blur-xl border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-3 group z-10">
                    <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center shadow-[0_0_15px_rgba(16,185,129,0.3)]">
                        <img src="{{ asset('images/neston-batik.svg') }}" alt="N" class="w-5 h-5">
                    </div>
                    <span class="text-lg font-bold tracking-tight text-white uppercase">NESTON</span>
                </a>

                <!-- Nav -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/#about" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">About</a>
                    <a href="/#workflow" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">Workflow</a>
                    <a href="/#fitur" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">Features</a>
                    <a href="{{ route('docs') }}" class="text-xs font-semibold text-white transition-colors">Docs</a>
                    <a href="/#contact" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">Contact</a>
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

    <div class="max-w-7xl mx-auto px-6 lg:px-8 flex gap-10 lg:gap-14 pt-32 pb-24">

        <!-- Sidebar: ringan, tidak bersaing dengan konten chapter -->
        <aside class="hidden lg:block w-[13.5rem] flex-shrink-0">
            <div class="sticky top-32 rounded-2xl border border-white/[0.06] bg-[#020617]/60 backdrop-blur-md shadow-[inset_0_1px_0_0_rgba(255,255,255,0.04)] p-3.5">
                <div class="space-y-7">
                    <div>
                        <p class="mb-2.5 px-2.5 text-[10px] font-medium uppercase tracking-[0.12em] text-slate-500">Materi utama</p>
                        <nav class="flex flex-col gap-0.5" role="navigation" aria-label="Materi dokumentasi">
                            <button type="button" @click="activeTab = 'intro'"
                                :class="activeTab === 'intro'
                                    ? 'bg-emerald-500/[0.08] text-emerald-400/95'
                                    : 'text-slate-500 hover:bg-white/[0.04] hover:text-slate-300'"
                                class="group flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13px] font-medium leading-snug transition-colors duration-200">
                                <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-md text-[11px] transition-colors" :class="activeTab === 'intro' ? 'bg-emerald-500/15 text-emerald-500/90' : 'bg-white/[0.03] text-slate-500 group-hover:text-slate-400'">
                                    <i class="fa-solid fa-house-chimney"></i>
                                </span>
                                <span>Pengenalan</span>
                            </button>
                            <button type="button" @click="activeTab = 'features'"
                                :class="activeTab === 'features'
                                    ? 'bg-emerald-500/[0.08] text-emerald-400/95'
                                    : 'text-slate-500 hover:bg-white/[0.04] hover:text-slate-300'"
                                class="group flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13px] font-medium leading-snug transition-colors duration-200">
                                <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-md text-[11px] transition-colors" :class="activeTab === 'features' ? 'bg-emerald-500/15 text-emerald-500/90' : 'bg-white/[0.03] text-slate-500 group-hover:text-slate-400'">
                                    <i class="fa-solid fa-microchip"></i>
                                </span>
                                <span>Fitur utama</span>
                            </button>
                            <button type="button" @click="activeTab = 'benefits'"
                                :class="activeTab === 'benefits'
                                    ? 'bg-emerald-500/[0.08] text-emerald-400/95'
                                    : 'text-slate-500 hover:bg-white/[0.04] hover:text-slate-300'"
                                class="group flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13px] font-medium leading-snug transition-colors duration-200">
                                <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-md text-[11px] transition-colors" :class="activeTab === 'benefits' ? 'bg-emerald-500/15 text-emerald-500/90' : 'bg-white/[0.03] text-slate-500 group-hover:text-slate-400'">
                                    <i class="fa-solid fa-chart-line"></i>
                                </span>
                                <span>Manfaat</span>
                            </button>
                            <button type="button" @click="activeTab = 'tech'"
                                :class="activeTab === 'tech'
                                    ? 'bg-emerald-500/[0.08] text-emerald-400/95'
                                    : 'text-slate-500 hover:bg-white/[0.04] hover:text-slate-300'"
                                class="group flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13px] font-medium leading-snug transition-colors duration-200">
                                <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-md text-[11px] transition-colors" :class="activeTab === 'tech' ? 'bg-emerald-500/15 text-emerald-500/90' : 'bg-white/[0.03] text-slate-500 group-hover:text-slate-400'">
                                    <i class="fa-solid fa-code"></i>
                                </span>
                                <span>Teknologi</span>
                            </button>
                        </nav>
                    </div>

                    <div class="border-t border-white/[0.05] pt-5">
                        <p class="mb-2.5 px-2.5 text-[10px] font-medium uppercase tracking-[0.12em] text-slate-500">Organisasi</p>
                        <nav class="flex flex-col gap-0.5" role="navigation" aria-label="Organisasi">
                            <button type="button" @click="activeTab = 'team'"
                                :class="activeTab === 'team'
                                    ? 'bg-emerald-500/[0.08] text-emerald-400/95'
                                    : 'text-slate-500 hover:bg-white/[0.04] hover:text-slate-300'"
                                class="group flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13px] font-medium leading-snug transition-colors duration-200">
                                <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-md text-[11px] transition-colors" :class="activeTab === 'team' ? 'bg-emerald-500/15 text-emerald-500/90' : 'bg-white/[0.03] text-slate-500 group-hover:text-slate-400'">
                                    <i class="fa-solid fa-users-gear"></i>
                                </span>
                                <span>Tim pengembang</span>
                            </button>
                            <button type="button" @click="activeTab = 'license'"
                                :class="activeTab === 'license'
                                    ? 'bg-emerald-500/[0.08] text-emerald-400/95'
                                    : 'text-slate-500 hover:bg-white/[0.04] hover:text-slate-300'"
                                class="group flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13px] font-medium leading-snug transition-colors duration-200">
                                <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-md text-[11px] transition-colors" :class="activeTab === 'license' ? 'bg-emerald-500/15 text-emerald-500/90' : 'bg-white/[0.03] text-slate-500 group-hover:text-slate-400'">
                                    <i class="fa-solid fa-file-contract"></i>
                                </span>
                                <span>Legal & lisensi</span>
                            </button>
                        </nav>
                    </div>

                    <p class="border-t border-white/[0.05] pt-4 px-2.5 text-[10px] leading-relaxed text-slate-600">
                        <span class="text-slate-500">Docs</span>
                        <span class="mx-1.5 text-slate-700">·</span>
                        <span class="text-slate-500">v1.2</span>
                    </p>
                </div>
            </div>
        </aside>

        <!-- Content Area (Rich Content Restored) -->
        <main class="flex-1 min-w-0">

            <!-- Tab: Intro -->
            <div x-show="activeTab === 'intro'" x-cloak class="animate-fade-in">
                <div class="doc-card mb-8">
                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.4em] mb-4 block">Chapter 01</span>
                    <h1 class="text-5xl font-black tracking-tighter text-white mb-8 leading-tight">SELAMAT DATANG DI <br/><span class="text-emerald-500">NESTON ECOSYSTEM</span></h1>
                    <p class="text-slate-400 text-lg leading-relaxed font-medium mb-12">
                        NESTON adalah platform manajemen parkir modern yang mengintegrasikan teknologi IoT dan AI untuk menyederhanakan pengelolaan infrastruktur parkir secara menyeluruh.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-8 bg-white/[0.03] rounded-3xl border border-white/5">
                            <h3 class="text-emerald-500 font-bold mb-3 uppercase text-[10px] tracking-[0.2em]">Visi Kami</h3>
                            <p class="text-slate-400 text-sm leading-relaxed">Menjadikan manajemen parkir lebih cerdas, aman, dan transparan melalui digitalisasi total.</p>
                        </div>
                        <div class="p-8 bg-white/[0.03] rounded-3xl border border-white/5">
                            <h3 class="text-emerald-500 font-bold mb-3 uppercase text-[10px] tracking-[0.2em]">Target</h3>
                            <p class="text-slate-400 text-sm leading-relaxed">Gedung perkantoran, pusat perbelanjaan, dan fasilitas publik modern.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Features -->
            <div x-show="activeTab === 'features'" x-cloak class="animate-fade-in">
                <div class="space-y-8">
                    <div class="doc-card">
                        <span class="text-[10px] font-black text-blue-500 uppercase tracking-[0.4em] mb-4 block">Chapter 02</span>
                        <h1 class="text-5xl font-black tracking-tighter text-white mb-12">ARSITEKTUR <span class="text-blue-500">FITUR</span></h1>

                        <div class="space-y-6">
                            <div class="flex flex-col md:flex-row gap-8 p-8 bg-white/[0.03] rounded-3xl border border-white/5 group">
                                <div class="w-16 h-16 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500 flex-shrink-0 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-camera-retro text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white mb-2 uppercase tracking-wide">ANPR (Automatic Number Plate Recognition)</h3>
                                    <p class="text-slate-400 text-sm leading-relaxed font-medium">Pengenalan plat nomor otomatis menggunakan Computer Vision, mempercepat proses check-in hingga 70%.</p>
                                </div>
                            </div>

                            <div class="flex flex-col md:flex-row gap-8 p-8 bg-white/[0.03] rounded-3xl border border-white/5 group">
                                <div class="w-16 h-16 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 flex-shrink-0 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-map-location-dot text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white mb-2 uppercase tracking-wide">Visual Map Architect</h3>
                                    <p class="text-slate-400 text-sm leading-relaxed font-medium">Editor layout interaktif yang memungkinkan administrator memetakan slot parkir secara visual.</p>
                                </div>
                            </div>

                            <div class="flex flex-col md:flex-row gap-8 p-8 bg-white/[0.03] rounded-3xl border border-white/5 group">
                                <div class="w-16 h-16 bg-purple-500/10 rounded-2xl flex items-center justify-center text-purple-500 flex-shrink-0 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-wallet text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white mb-2 uppercase tracking-wide">Integrated NestonPay</h3>
                                    <p class="text-slate-400 text-sm leading-relaxed font-medium">Sistem pembayaran digital terintegrasi dengan gateway Midtrans untuk transaksi non-tunai.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Benefits -->
            <div x-show="activeTab === 'benefits'" x-cloak class="animate-fade-in">
                <div class="doc-card">
                    <span class="text-[10px] font-black text-purple-500 uppercase tracking-[0.4em] mb-4 block">Chapter 03</span>
                    <h1 class="text-5xl font-black tracking-tighter text-white mb-12 leading-tight">VALUE & <span class="text-purple-500">IMPACT</span></h1>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-10 bg-white/[0.03] rounded-[2.5rem] border border-white/5">
                            <div class="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 mb-6">
                                <i class="fa-solid fa-shield-check text-xl"></i>
                            </div>
                            <h3 class="text-white font-bold mb-4 uppercase tracking-widest text-xs">Keamanan Terjamin</h3>
                            <p class="text-slate-500 text-sm leading-relaxed font-medium">Verifikasi identitas kendaraan dan pencatatan log aktivitas yang komprehensif secara real-time.</p>
                        </div>
                        <div class="p-10 bg-white/[0.03] rounded-[2.5rem] border border-white/5">
                            <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500 mb-6">
                                <i class="fa-solid fa-bolt text-xl"></i>
                            </div>
                            <h3 class="text-white font-bold mb-4 uppercase tracking-widest text-xs">Kecepatan Layanan</h3>
                            <p class="text-slate-500 text-sm leading-relaxed font-medium">Otomatisasi sistem mengurangi antrian dan memberikan pengalaman yang lebih baik bagi pengguna.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Tech Stack -->
            <div x-show="activeTab === 'tech'" x-cloak class="animate-fade-in">
                <div class="doc-card">
                    <span class="text-[10px] font-black text-rose-500 uppercase tracking-[0.4em] mb-4 block">Chapter 04</span>
                    <h1 class="text-5xl font-black tracking-tighter text-white mb-12">TECHNOLOGY <span class="text-rose-500">STACK</span></h1>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
                        <div class="p-8 bg-white/[0.03] rounded-3xl border border-white/5 text-center group">
                            <i class="fa-brands fa-laravel text-4xl text-[#FF2D20] mb-4"></i>
                            <p class="text-xs font-black text-white uppercase tracking-widest">Laravel 10</p>
                        </div>
                        <div class="p-8 bg-white/[0.03] rounded-3xl border border-white/5 text-center group">
                            <i class="fa-brands fa-js text-4xl text-[#F7DF1E] mb-4"></i>
                            <p class="text-xs font-black text-white uppercase tracking-widest">Alpine.js</p>
                        </div>
                        <div class="p-8 bg-white/[0.03] rounded-3xl border border-white/5 text-center group">
                            <i class="fa-solid fa-wind text-4xl text-[#38BDF8] mb-4"></i>
                            <p class="text-xs font-black text-white uppercase tracking-widest">Tailwind CSS</p>
                        </div>
                        <div class="p-8 bg-white/[0.03] rounded-3xl border border-white/5 text-center group">
                            <i class="fa-solid fa-database text-4xl text-emerald-500 mb-4"></i>
                            <p class="text-xs font-black text-white uppercase tracking-widest">MySQL</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Team -->
            <div x-show="activeTab === 'team'" x-cloak class="animate-fade-in">
                <div class="doc-card">
                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.4em] mb-4 block">Our Team</span>
                    <h1 class="text-5xl font-black tracking-tighter text-white mb-12">ENGINEERING <span class="text-emerald-500">TEAM</span></h1>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                        <div class="p-10 bg-white/[0.03] rounded-[3rem] border border-white/5 flex items-center gap-8 group">
                            <div class="w-20 h-20 bg-emerald-500/10 rounded-[2rem] flex items-center justify-center text-emerald-500 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-user-tie text-3xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white mb-1 uppercase tracking-tight">Lead Developer</h3>
                                <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest">Architect & Backend</p>
                            </div>
                        </div>
                        <div class="p-10 bg-white/[0.03] rounded-[3rem] border border-white/5 flex items-center gap-8 group">
                            <div class="w-20 h-20 bg-blue-500/10 rounded-[2rem] flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-pen-nib text-3xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white mb-1 uppercase tracking-tight">UI Designer</h3>
                                <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest">Visual & Experience</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: License -->
            <div x-show="activeTab === 'license'" x-cloak class="animate-fade-in">
                <div class="doc-card">
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em] mb-4 block">Legal</span>
                    <h1 class="text-5xl font-black tracking-tighter text-white mb-8 leading-tight">LISENSI <span class="text-slate-500">& LEGAL</span></h1>
                    <div class="space-y-8 text-slate-400 text-lg leading-relaxed font-medium">
                        <p>Ekosistem NESTON dilisensikan di bawah standar lisensi komersial terbatas. Seluruh hak cipta dilindungi undang-undang.</p>
                        <div class="p-10 bg-rose-500/5 rounded-[2.5rem] border border-rose-500/10 mt-16">
                            <h3 class="text-rose-500 font-black mb-4 uppercase text-xs tracking-[0.2em]">Peringatan Keamanan</h3>
                            <p class="text-slate-500 text-sm leading-relaxed">Dilarang melakukan rekayasa balik (reverse engineering) atau mendistribusikan ulang kode sumber tanpa otorisasi resmi.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inline Footer -->
            <footer class="mt-20 pt-10 border-t border-white/5 text-center lg:text-left">
                <p class="text-slate-600 text-[9px] font-black uppercase tracking-[0.4em]">NESTON Parking Ecosystem • 2024 System Documentation</p>
            </footer>
        </main>
    </div>

    <!-- Mobile Nav Bar (Bottom) -->
    <div class="lg:hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-[60] bg-slate-900/90 backdrop-blur-xl border border-white/10 p-2 rounded-2xl flex items-center gap-1 shadow-2xl">
        <button @click="activeTab = 'intro'" :class="activeTab === 'intro' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/40' : 'text-slate-400'" class="p-3 rounded-xl transition-all"><i class="fa-solid fa-house-chimney"></i></button>
        <button @click="activeTab = 'features'" :class="activeTab === 'features' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/40' : 'text-slate-400'" class="p-3 rounded-xl transition-all"><i class="fa-solid fa-microchip"></i></button>
        <button @click="activeTab = 'benefits'" :class="activeTab === 'benefits' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/40' : 'text-slate-400'" class="p-3 rounded-xl transition-all"><i class="fa-solid fa-chart-line"></i></button>
        <button @click="activeTab = 'team'" :class="activeTab === 'team' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/40' : 'text-slate-400'" class="p-3 rounded-xl transition-all"><i class="fa-solid fa-users-gear"></i></button>
    </div>

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
                    this.value = '';
                    handleCardAuth(uid);
                }
            }
        });

        async function handleCardAuth(uid) {
            const status = document.getElementById('loginStatus');
            const scanning = document.getElementById('scanningState');

            scanning.classList.add('hidden');
            status.classList.remove('hidden');
            status.className = 'text-sm font-bold text-emerald-400 animate-pulse';
            status.innerText = 'Authenticating...';

            try {
                const response = await fetch('/login/card', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ rfid_uid: uid })
                });

                const data = await response.json();

                if (data.success) {
                    status.className = 'text-sm font-bold text-emerald-400';
                    status.innerText = 'Success! Redirecting...';
                    window.location.href = data.redirect;
                } else {
                    status.className = 'text-sm font-bold text-rose-500';
                    status.innerText = data.message || 'Authentication failed';
                    setTimeout(() => {
                        status.classList.add('hidden');
                        scanning.classList.remove('hidden');
                    }, 2000);
                }
            } catch (error) {
                status.className = 'text-sm font-bold text-rose-500';
                status.innerText = 'System error occurred';
                setTimeout(() => {
                    status.classList.add('hidden');
                    scanning.classList.remove('hidden');
                }, 2000);
            }
        }
    </script>

</body>
</html>
