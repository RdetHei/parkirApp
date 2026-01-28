    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-gray-50 to-white border-r border-gray-200 flex-shrink-0 hidden lg:flex flex-col relative z-50 shadow-sm">
            <div class="h-16 border-b border-gray-200 flex items-center px-6 bg-white">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/parked-logo.png') }}" alt="Parked" class="h-8 w-auto object-contain">
                </div>
            </div>

            <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-green-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                    @if(request()->routeIs('dashboard'))
                        <div class="ml-auto w-2 h-2 bg-white rounded-full"></div>
                    @endif
                </a>

                <!-- Catat Masuk -->
                <a href="{{ route('transaksi.create-check-in') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('transaksi.checkIn.create') ? 'bg-blue-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Catat Masuk</span>
                    @if(request()->routeIs('transaksi.checkIn.create'))
                        <div class="ml-auto w-2 h-2 bg-white rounded-full"></div>
                    @endif
                </a>

                <!-- Parkir Aktif -->
                <a href="{{ route('transaksi.parkir.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('transaksi.parkir.index') ? 'bg-orange-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>Parkir Aktif</span>
                    @if(request()->routeIs('transaksi.parkir.index'))
                        <div class="ml-auto w-2 h-2 bg-white rounded-full"></div>
                    @endif
                </a>

                <!-- Riwayat Transaksi -->
                <a href="{{ route('transaksi.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('transaksi.index', 'transaksi.show', 'transaksi.edit', 'transaksi.create') ? 'bg-indigo-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <span>Riwayat Transaksi</span>
                    @if(request()->routeIs('transaksi.index', 'transaksi.show', 'transaksi.edit', 'transaksi.create'))
                        <div class="ml-auto w-2 h-2 bg-white rounded-full"></div>
                    @endif
                </a>

                <!-- Riwayat Pembayaran -->
                <a href="{{ route('payment.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('payment.index') ? 'bg-purple-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Riwayat Pembayaran</span>
                    @if(request()->routeIs('payment.index'))
                        <div class="ml-auto w-2 h-2 bg-white rounded-full"></div>
                    @endif
                </a>

                <!-- Proses Pembayaran -->
                <a href="{{ route('payment.select-transaction') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('payment.select-transaction', 'payment.create', 'payment.manual-confirm', 'payment.qr-scan') ? 'bg-pink-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Proses Pembayaran</span>
                    @if(request()->routeIs('payment.select-transaction', 'payment.create', 'payment.manual-confirm', 'payment.qr-scan'))
                        <div class="ml-auto w-2 h-2 bg-white rounded-full"></div>
                    @endif
                </a>

                <div class="pt-4 mt-4 border-t border-gray-200">
                    <p class="text-xs font-semibold text-gray-500 uppercase px-3 mb-3 tracking-wider">Master Data</p>

                    <!-- Kelola User -->
                    <a href="{{ route('users.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('users.index', 'users.create', 'users.edit', 'users.show') ? 'bg-cyan-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Kelola User</span>
                        @if(request()->routeIs('users.index', 'users.create', 'users.edit', 'users.show'))
                            <div class="ml-auto w-2 h-2 bg-white rounded-full"></div>
                        @endif
                    </a>

                    <!-- Tarif Parkir -->
                    <a href="{{ route('tarif.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('tarif.index', 'tarif.create', 'tarif.edit', 'tarif.show') ? 'bg-yellow-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Tarif Parkir</span>
                        @if(request()->routeIs('tarif.index', 'tarif.create', 'tarif.edit', 'tarif.show'))
                            <div class="ml-auto w-2 h-2 bg-white rounded-full"></div>
                        @endif
                    </a>

                    <!-- Area Parkir -->
                    <a href="{{ route('area-parkir.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('area-parkir.index', 'area-parkir.create', 'area-parkir.edit', 'area-parkir.show') ? 'bg-red-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Area Parkir</span>
                        @if(request()->routeIs('area-parkir.index', 'area-parkir.create', 'area-parkir.edit', 'area-parkir.show'))
                            <div class="ml-auto w-2 h-2 bg-white rounded-full"></div>
                        @endif
                    </a>

                    <!-- Kendaraan -->
                    <a href="{{ route('kendaraan.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('kendaraan.index', 'kendaraan.create', 'kendaraan.edit', 'kendaraan.show') ? 'bg-amber-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                        </svg>
                        <span>Kendaraan</span>
                        @if(request()->routeIs('kendaraan.index', 'kendaraan.create', 'kendaraan.edit', 'kendaraan.show'))
                            <div class="ml-auto w-2 h-2 bg-white rounded-full"></div>
                        @endif
                    </a>

                    <!-- Log Aktivitas -->
                    <a href="{{ route('log-aktivitas.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('log-aktivitas.index') ? 'bg-slate-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Log Aktivitas</span>
                        @if(request()->routeIs('log-aktivitas.index'))
                            <div class="ml-auto w-2 h-2 bg-white rounded-full"></div>
                        @endif
                    </a>
                </div>
            </nav>

            <!-- User Profile Section -->
            <div class="p-4 border-t border-gray-200 bg-white hover:bg-gray-50 transition-colors duration-200">
                <div class="flex items-center gap-3 cursor-pointer">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name ?? 'User' }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? 'user@email.com' }}</p>
                    </div>
                </div>
            </div>
        </aside>
