        <!-- Sidebar -->
        <aside id="app-sidebar" class="sidebar-animate w-64 h-screen sticky top-0 bg-gradient-to-b from-gray-50 to-white border-r border-gray-200 flex-shrink-0 hidden lg:flex flex-col z-50 shadow-sm">
            <div class="sidebar-header h-16 border-b border-gray-200 flex items-center justify-between px-3 lg:px-4 bg-white">
                <div class="flex items-center gap-2 min-w-0 sidebar-header-brand">
                    <img src="{{ asset('images/parked-logo.png') }}" alt="Parked" class="h-8 w-auto object-contain">
                </div>
                <button id="sidebar-toggle" type="button"
                        class="sidebar-toggle-btn hidden lg:inline-flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-100 text-gray-600 shrink-0"
                        aria-label="Toggle sidebar">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            @php $role = auth()->user()->role ?? 'user'; @endphp
            <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto sidebar-nav">
                {{-- Dashboard: link sesuai role (sesuai SPK) --}}
                <a href="{{ $role === 'owner' ? route('owner.dashboard') : ($role === 'petugas' ? route('petugas.dashboard') : route('dashboard')) }}"
                   class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('dashboard', 'owner.dashboard', 'petugas.dashboard') ? 'bg-green-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="sidebar-label">Dashboard</span>
                </a>

                {{-- PETUGAS: Transaksi (Catat Masuk, Parkir Aktif, Riwayat, Pembayaran) --}}
                @if($role === 'petugas')
                <a href="{{ route('transaksi.create-check-in') }}"
                   class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('transaksi.create-check-in') ? 'bg-blue-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span class="sidebar-label">Catat Masuk</span>
                </a>
                <a href="{{ route('transaksi.parkir.index') }}"
                   class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('transaksi.parkir.index') ? 'bg-orange-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <span class="sidebar-label">Parkir Aktif</span>
                </a>
                <a href="{{ route('transaksi.index') }}"
                   class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('transaksi.index', 'transaksi.show', 'transaksi.edit', 'transaksi.create') ? 'bg-indigo-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    <span class="sidebar-label">Riwayat Transaksi</span>
                </a>
                <a href="{{ route('payment.select-transaction') }}"
                   class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('payment.select-transaction', 'payment.create', 'payment.manual-confirm', 'payment.qr-scan') ? 'bg-pink-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="sidebar-label">Proses Pembayaran</span>
                </a>
                <a href="{{ route('payment.index') }}"
                   class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('payment.index') ? 'bg-purple-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="sidebar-label">Riwayat Pembayaran</span>
                </a>
                @endif

                {{-- ADMIN: Riwayat Transaksi (lihat + cetak struk), Master Data, Log Aktivitas --}}
                @if($role === 'admin')
                <a href="{{ route('transaksi.index') }}"
                   class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('transaksi.index', 'transaksi.show', 'transaksi.edit', 'transaksi.create') ? 'bg-indigo-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="sidebar-label">Riwayat Transaksi & Cetak Struk</span>
                </a>
                <div class="pt-4 mt-4 border-t border-gray-200">
                    <p class="sidebar-section-title text-xs font-semibold text-gray-500 uppercase px-3 mb-3 tracking-wider">Master Data</p>
                    <a href="{{ route('users.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-cyan-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span class="sidebar-label">Kelola User</span>
                    </a>
                    <a href="{{ route('tarif.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('tarif.*') ? 'bg-yellow-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="sidebar-label">Tarif Parkir</span>
                    </a>
                    <a href="{{ route('area-parkir.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('area-parkir.*') ? 'bg-red-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="sidebar-label">Area Parkir</span>
                    </a>
                    <a href="{{ route('kendaraan.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('kendaraan.*') ? 'bg-amber-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                        <span class="sidebar-label">Kendaraan</span>
                    </a>
                    <a href="{{ route('log-aktivitas.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('log-aktivitas.*') ? 'bg-slate-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="sidebar-label">Log Aktivitas</span>
                    </a>
                </div>
                @endif

                {{-- OWNER: Rekap transaksi sesuai waktu --}}
                @if($role === 'owner')
                <a href="{{ route('report.transaksi') }}"
                   class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('report.transaksi') ? 'bg-indigo-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="sidebar-label">Rekap Transaksi</span>
                </a>
                <a href="{{ route('report.pembayaran') }}"
                   class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('report.pembayaran') ? 'bg-purple-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="sidebar-label">Rekap Pembayaran</span>
                </a>
                @endif
            </nav>

            <!-- User Profile Section -->
            <div class="relative p-4 border-t border-gray-200 bg-white sidebar-account-wrap">
                <button type="button" id="sidebar-account-toggle" class="w-full flex items-center gap-3 rounded-lg hover:bg-gray-50 transition-colors duration-200 text-left p-1 -m-1" aria-expanded="false" aria-haspopup="true" aria-label="Buka menu akun">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md shrink-0">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="sidebar-profile-details flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name ?? 'User' }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? 'user@email.com' }}</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 shrink-0 sidebar-profile-details" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Popup card -->
                <div id="sidebar-account-popup" class="sidebar-account-popup hidden absolute bottom-full left-4 right-4 mb-2 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50 opacity-0 scale-95 origin-bottom">
                    <a href="{{ $role === 'owner' ? route('owner.dashboard') : ($role === 'petugas' ? route('petugas.dashboard') : route('dashboard')) }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Pengaturan</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <script>
            (function () {
                var toggle = document.getElementById('sidebar-account-toggle');
                var popup = document.getElementById('sidebar-account-popup');
                var wrap = toggle && toggle.closest('.sidebar-account-wrap');

                if (!toggle || !popup || !wrap) return;

                function open() {
                    popup.classList.remove('hidden');
                    toggle.setAttribute('aria-expanded', 'true');
                    requestAnimationFrame(function () {
                        requestAnimationFrame(function () {
                            popup.classList.add('sidebar-account-popup-open');
                        });
                    });
                }

                function close() {
                    popup.classList.remove('sidebar-account-popup-open');
                    toggle.setAttribute('aria-expanded', 'false');
                    setTimeout(function () {
                        popup.classList.add('hidden');
                    }, 200);
                }

                function isOpen() {
                    return !popup.classList.contains('hidden');
                }

                toggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    if (isOpen()) close(); else open();
                });

                document.addEventListener('click', function () {
                    if (isOpen()) close();
                });

                popup.addEventListener('click', function (e) {
                    e.stopPropagation();
                });
            })();
        </script>
