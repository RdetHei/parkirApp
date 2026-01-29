<!-- Sidebar -->
        <aside id="app-sidebar" class="w-64 h-screen sticky top-0 bg-white border-r border-gray-200 flex-shrink-0 hidden lg:flex flex-col z-50 shadow-sm">
            <!-- Sidebar Header -->
            <div class="sidebar-header h-16 border-b border-gray-100 flex items-center justify-between px-4 bg-gradient-to-r from-white to-gray-50">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <img id="logo-toggle"
                         src="{{ asset('images/parked-logo.png') }}"
                         alt="Parked"
                         class="parked-logo h-8 w-auto object-contain flex-shrink-0">
                    <div class="sidebar-brand-text">
                        <h1 class="text-lg font-bold text-gray-800">Parked</h1>
                    </div>
                </div>

                <!-- Sidebar Toggle Button -->
                <button id="sidebar-toggle"
                        type="button"
                        class="flex-shrink-0 w-9 h-9 rounded-lg hover:bg-gray-100 text-gray-600 transition-all duration-200 flex items-center justify-center group"
                        aria-label="Toggle sidebar">
                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto sidebar-nav scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-transparent">
                <!-- Main Menu Section -->
                <div class="space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                       class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-green-500 to-green-600 text-white shadow-lg shadow-green-500/30' : 'text-gray-700 hover:bg-gray-100 hover:translate-x-1' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? '' : 'group-hover:scale-110 transition-transform' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span class="sidebar-label flex-1">Dashboard</span>
                        @if(request()->routeIs('dashboard'))
                            <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>

                    <!-- Catat Masuk -->
                    <a href="{{ route('transaksi.create-check-in') }}"
                       class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('transaksi.create-check-in') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/30' : 'text-gray-700 hover:bg-gray-100 hover:translate-x-1' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('transaksi.create-check-in') ? '' : 'group-hover:scale-110 transition-transform' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span class="sidebar-label flex-1">Catat Masuk</span>
                        @if(request()->routeIs('transaksi.create-check-in'))
                            <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>

                    <!-- Parkir Aktif -->
                    <a href="{{ route('parking.map.index') }}"
                       class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('parking.map.index') ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-500/30' : 'text-gray-700 hover:bg-gray-100 hover:translate-x-1' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('parking.map.index') ? '' : 'group-hover:scale-110 transition-transform' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span class="sidebar-label flex-1">Parkir Aktif</span>
                        @if(request()->routeIs('parking.map.index'))
                            <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>

                    <!-- Riwayat Transaksi -->
                    <a href="{{ route('transaksi.index') }}"
                       class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('transaksi.index', 'transaksi.show', 'transaksi.edit', 'transaksi.create') ? 'bg-gradient-to-r from-indigo-500 to-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-700 hover:bg-gray-100 hover:translate-x-1' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('transaksi.index', 'transaksi.show', 'transaksi.edit', 'transaksi.create') ? '' : 'group-hover:scale-110 transition-transform' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        <span class="sidebar-label flex-1">Riwayat Transaksi</span>
                        @if(request()->routeIs('transaksi.index', 'transaksi.show', 'transaksi.edit', 'transaksi.create'))
                            <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>

                    <!-- Riwayat Pembayaran -->
                    <a href="{{ route('payment.index') }}"
                       class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('payment.index') ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-lg shadow-purple-500/30' : 'text-gray-700 hover:bg-gray-100 hover:translate-x-1' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('payment.index') ? '' : 'group-hover:scale-110 transition-transform' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="sidebar-label flex-1">Riwayat Pembayaran</span>
                        @if(request()->routeIs('payment.index'))
                            <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>

                    <!-- Proses Pembayaran -->
                    <a href="{{ route('payment.select-transaction') }}"
                       class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('payment.select-transaction', 'payment.create', 'payment.manual-confirm', 'payment.qr-scan') ? 'bg-gradient-to-r from-pink-500 to-pink-600 text-white shadow-lg shadow-pink-500/30' : 'text-gray-700 hover:bg-gray-100 hover:translate-x-1' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('payment.select-transaction', 'payment.create', 'payment.manual-confirm', 'payment.qr-scan') ? '' : 'group-hover:scale-110 transition-transform' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="sidebar-label flex-1">Proses Pembayaran</span>
                        @if(request()->routeIs('payment.select-transaction', 'payment.create', 'payment.manual-confirm', 'payment.qr-scan'))
                            <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                        @endif
                    </a>
                </div>

                <!-- Master Data Section -->
                <div class="pt-4 mt-4 border-t border-gray-200">
                    <div class="sidebar-section-title flex items-center gap-2 px-3 mb-3">
                        <div class="h-px flex-1 bg-gray-300"></div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Master Data</p>
                        <div class="h-px flex-1 bg-gray-300"></div>
                    </div>

                    <div class="space-y-1">
                        <!-- Kelola User -->
                        <a href="{{ route('users.index') }}"
                           class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('users.index', 'users.create', 'users.edit', 'users.show') ? 'bg-gradient-to-r from-cyan-500 to-cyan-600 text-white shadow-lg shadow-cyan-500/30' : 'text-gray-700 hover:bg-gray-100 hover:translate-x-1' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('users.index', 'users.create', 'users.edit', 'users.show') ? '' : 'group-hover:scale-110 transition-transform' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span class="sidebar-label flex-1">Kelola User</span>
                            @if(request()->routeIs('users.index', 'users.create', 'users.edit', 'users.show'))
                                <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                            @endif
                        </a>

                        <!-- Tarif Parkir -->
                        <a href="{{ route('tarif.index') }}"
                           class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('tarif.index', 'tarif.create', 'tarif.edit', 'tarif.show') ? 'bg-gradient-to-r from-yellow-500 to-yellow-600 text-white shadow-lg shadow-yellow-500/30' : 'text-gray-700 hover:bg-gray-100 hover:translate-x-1' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('tarif.index', 'tarif.create', 'tarif.edit', 'tarif.show') ? '' : 'group-hover:scale-110 transition-transform' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="sidebar-label flex-1">Tarif Parkir</span>
                            @if(request()->routeIs('tarif.index', 'tarif.create', 'tarif.edit', 'tarif.show'))
                                <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                            @endif
                        </a>

                        <!-- Area Parkir -->
                        <a href="{{ route('area-parkir.index') }}"
                           class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('area-parkir.index', 'area-parkir.create', 'area-parkir.edit', 'area-parkir.show') ? 'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' : 'text-gray-700 hover:bg-gray-100 hover:translate-x-1' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('area-parkir.index', 'area-parkir.create', 'area-parkir.edit', 'area-parkir.show') ? '' : 'group-hover:scale-110 transition-transform' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="sidebar-label flex-1">Area Parkir</span>
                            @if(request()->routeIs('area-parkir.index', 'area-parkir.create', 'area-parkir.edit', 'area-parkir.show'))
                                <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                            @endif
                        </a>

                        <!-- Kendaraan -->
                        <a href="{{ route('kendaraan.index') }}"
                           class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('kendaraan.index', 'kendaraan.create', 'kendaraan.edit', 'kendaraan.show') ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-white shadow-lg shadow-amber-500/30' : 'text-gray-700 hover:bg-gray-100 hover:translate-x-1' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('kendaraan.index', 'kendaraan.create', 'kendaraan.edit', 'kendaraan.show') ? '' : 'group-hover:scale-110 transition-transform' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                            </svg>
                            <span class="sidebar-label flex-1">Kendaraan</span>
                            @if(request()->routeIs('kendaraan.index', 'kendaraan.create', 'kendaraan.edit', 'kendaraan.show'))
                                <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                            @endif
                        </a>

                        <!-- Log Aktivitas -->
                        <a href="{{ route('log-aktivitas.index') }}"
                           class="sidebar-item group flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('log-aktivitas.index') ? 'bg-gradient-to-r from-slate-500 to-slate-600 text-white shadow-lg shadow-slate-500/30' : 'text-gray-700 hover:bg-gray-100 hover:translate-x-1' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('log-aktivitas.index') ? '' : 'group-hover:scale-110 transition-transform' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="sidebar-label flex-1">Log Aktivitas</span>
                            @if(request()->routeIs('log-aktivitas.index'))
                                <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                            @endif
                        </a>
                    </div>
                </div>
            </nav>

            <!-- User Profile Section -->
            <div class="sidebar-footer border-t border-gray-100 bg-gradient-to-r from-white to-gray-50">
                <div class="p-4">
                    <div class="flex items-center gap-3 cursor-pointer group hover:bg-gray-100 rounded-xl p-2 -m-2 transition-all duration-200">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-400 via-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md ring-2 ring-white group-hover:scale-105 transition-transform sidebar-profile-toggle">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <div class="sidebar-profile-details flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name ?? 'User' }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? 'user@email.com' }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-colors sidebar-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
                    <!-- Account Card (hidden until profile clicked) -->
                    <div id="sidebar-account-card" class="hidden mt-3 bg-white border border-gray-100 rounded-xl shadow-md p-3">
                        <p class="text-xs text-gray-500 mb-2">Akun</p>
                        <div class="space-y-2">
                            <a href="{{ route('users.edit', auth()->user()->id ?? 0) }}" class="block w-full text-left px-3 py-2 rounded-md hover:bg-gray-50 text-sm text-gray-700">Pengaturan</a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-3 py-2 rounded-md hover:bg-red-50 text-sm text-red-600 font-medium">Logout</button>
                            </form>
                        </div>
                    </div>
            </div>
        </aside>


<script>
    (function(){
        var profileToggle = document.querySelector('#app-sidebar .sidebar-profile-toggle');
        var accountCard = document.getElementById('sidebar-account-card');
        var chevron = document.querySelector('#app-sidebar .sidebar-chevron');

        if(!profileToggle || !accountCard) return;

        profileToggle.addEventListener('click', function(e){
            e.stopPropagation();
            accountCard.classList.toggle('hidden');
            if(chevron) chevron.classList.toggle('rotate-180');
        });

        // close when clicking outside
        document.addEventListener('click', function(){
            if(!accountCard.classList.contains('hidden')){
                accountCard.classList.add('hidden');
                if(chevron) chevron.classList.remove('rotate-180');
            }
        });

        // prevent clicks inside card from bubbling
        accountCard.addEventListener('click', function(e){ e.stopPropagation(); });
    })();
</script>
