<!-- Sidebar: jangan overflow-hidden di aside agar popup akun tidak terpotong -->
<aside id="app-sidebar"
       x-show="!isMobile || sidebarOpen"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-300"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       :class="[
           sidebarOpen ? 'translate-x-0 shadow-2xl' : (isMobile ? '-translate-x-full' : 'translate-x-0'),
           desktopCollapsed ? 'lg:w-[4.5rem]' : 'lg:w-64'
       ]"
       class="sidebar-animate w-64 max-w-[85vw] h-screen fixed lg:sticky top-0 left-0 bg-[#020617] border-r border-white/5 shrink-0 flex flex-col z-[100] overflow-hidden transition-all duration-300 ease-in-out pointer-events-auto">
    <div class="sidebar-header h-16 border-b border-white/5 flex items-center justify-between px-6 shrink-0 relative z-10">
        <div class="flex items-center gap-3 min-w-0 sidebar-header-brand">
            <img src="{{ asset('images/neston.svg') }}" alt="NESTON" class="h-8 w-auto shrink-0">
            <span class="text-sm font-bold tracking-tight text-white uppercase sidebar-label">NESTON</span>
        </div>

        <!-- Desktop Toggle -->
        <button id="sidebar-toggle" type="button"
                @click="desktopCollapsed = !desktopCollapsed"
                class="sidebar-toggle-btn hidden lg:inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-white/5 text-slate-500 hover:text-white shrink-0 transition-colors">
            <svg class="w-4 h-4 sidebar-toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Mobile Close -->
        <button @click="sidebarOpen = false" type="button"
                class="lg:hidden inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-white/5 text-slate-400 hover:text-white shrink-0 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    @php
        $user = auth()->user();
        $role = $user->role ?? 'user';
    @endphp

    <nav role="navigation" aria-label="Menu utama"
         @click="if (!isMobile) return; const link = $event.target.closest('a[href]'); if (!link) return; queueMicrotask(() => { sidebarOpen = false })"
         class="flex-1 min-h-0 min-w-0 px-4 py-6 space-y-1.5 overflow-y-auto overflow-x-hidden sidebar-nav relative z-10 custom-scrollbar">
        {{-- Dashboard (Shared) --}}
        <a href="{{ $role === 'owner' ? route('owner.dashboard') : ($role === 'petugas' ? route('petugas.dashboard') : ($role === 'admin' ? route('dashboard') : route('user.dashboard'))) }}"
           class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('dashboard', 'owner.dashboard', 'petugas.dashboard', 'user.dashboard') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="sidebar-label">{{ __('Dashboard') }}</span>
        </a>

        <a href="{{ route('parking.map.index') }}"
           class="sidebar-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('parking.map.index') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 9m0 8V9m0 0L9 7" /></svg>
            <span class="sidebar-label">{{ __('Parking Map') }}</span>
        </a>

        {{-- USER / Common Account Items --}}
        @if($role === 'user')
        <div class="pt-4 pb-2 px-3">
            <p class="text-[10px] font-bold text-slate-600 uppercase tracking-[0.2em] sidebar-label">{{ __('My Account') }}</p>
        </div>
        <a href="{{ route('user.saldo.index') }}"
           class="sidebar-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('user.saldo.*') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            <span class="sidebar-label">NestonPay</span>
        </a>
        <a href="{{ route('user.vehicles.index') }}"
           class="sidebar-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('user.vehicles.*') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V9a3 3 0 013-3h0a3 3 0 013 3v8m-9 0h9m-9 0a2 2 0 01-2-2v-3a2 2 0 012-2h9a2 2 0 012 2v3a2 2 0 01-2 2m-9 0v2m9-2v2"></path></svg>
            <span class="sidebar-label">{{ __('My Vehicles') }}</span>
        </a>
        <a href="{{ route('user.history') }}"
           class="sidebar-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('user.history') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="sidebar-label">{{ __('History') }}</span>
        </a>
        <a href="{{ route('user.bills') }}"
           class="sidebar-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('user.bills') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="sidebar-label">{{ __('My Bills') }}</span>
        </a>
        <a href="{{ route('user.bookings') }}"
           class="sidebar-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('user.bookings') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <span class="sidebar-label">{{ __('Booking Slot') }}</span>
        </a>
        @endif

        {{-- PETUGAS OPERATIONS --}}
        @if($role === 'petugas' || $role === 'admin')
        <div class="pt-4 pb-2 px-3">
            <p class="text-[10px] font-bold text-slate-600 uppercase tracking-[0.2em] sidebar-label">{{ __('Parking Ops') }}</p>
        </div>
        <a href="{{ route('transaksi.create-check-in') }}"
           class="sidebar-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('transaksi.create-check-in') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span class="sidebar-label">Check-in</span>
        </a>
        <a href="{{ route('transaksi.active') }}"
           class="sidebar-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('transaksi.active') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            <span class="sidebar-label">{{ __('Active Parking') }}</span>
        </a>
        <a href="{{ route('transaksi.bookings') }}"
           class="sidebar-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('transaksi.bookings') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
            <span class="sidebar-label">{{ __('Bookings') }}</span>
        </a>
        <a href="{{ route('transaksi.history') }}"
           class="sidebar-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('transaksi.history') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="sidebar-label">{{ __('History') }}</span>
        </a>
        <a href="{{ route('anpr.index') }}"
           class="sidebar-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('anpr.index') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
            <span class="sidebar-label">AI Scanner</span>
        </a>
        <a href="{{ route('parkir.scan') }}"
           class="sidebar-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('parkir.scan') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
            <span class="sidebar-label">RFID Terminal</span>
        </a>
        <a href="{{ route('payment.select-transaction') }}"
           class="sidebar-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('payment.select-transaction', 'payment.create') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            <span class="sidebar-label">{{ __('Payments') }}</span>
        </a>
        @endif

        {{-- ADMIN ONLY --}}
        @if($role === 'admin')
        <div class="pt-4 pb-2 px-3">
            <p class="text-[10px] font-bold text-slate-600 uppercase tracking-[0.2em] sidebar-label">{{ __('Administration') }}</p>
        </div>
        <a href="{{ route('users.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <span class="sidebar-label">{{ __('Team Management') }}</span>
        </a>
        <a href="{{ route('tarif.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('tarif.*') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="sidebar-label">{{ __('Tariff Rules') }}</span>
        </a>
        <a href="{{ route('area-parkir.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('area-parkir.*') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
            <span class="sidebar-label">{{ __('Zones & Areas') }}</span>
        </a>
        <a href="{{ route('admin.rfid.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('admin.rfid.*') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm5 0a2 2 0 100-4 2 2 0 000 4z" /></svg>
            <span class="sidebar-label">{{ __('RFID Management') }}</span>
        </a>
        <a href="{{ route('log-aktivitas.index') }}" class="sidebar-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('log-aktivitas.*') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="sidebar-label">{{ __('System Audit') }}</span>
        </a>
        @endif

        {{-- OWNER ONLY --}}
        @if($role === 'owner')
        <div class="pt-4 pb-2 px-3">
            <p class="text-[10px] font-bold text-slate-600 uppercase tracking-[0.2em] sidebar-label">{{ __('Analytics') }}</p>
        </div>
        <a href="{{ route('report.transaksi') }}"
           class="sidebar-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('report.transaksi') ? 'active' : '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="sidebar-label">{{ __('Revenue Reports') }}</span>
        </a>
        @endif
    </nav>

    <!-- Bottom Section (popup butuh overflow visible) -->
    <div class="sidebar-account-wrap p-4 border-t border-white/5 shrink-0 relative z-20 overflow-visible">
        <button type="button"
                id="sidebar-account-toggle"
                @click.stop="accountOpen = !accountOpen"
                class="w-full flex items-center gap-3 rounded-xl hover:bg-white/5 transition-all duration-200 p-2 group relative z-30">
            @if(auth()->user()->profile_photo_url)
                <img src="{{ auth()->user()->profile_photo_url }}"
                     alt="{{ auth()->user()->name }}"
                     class="w-8 h-8 rounded-lg object-cover border border-emerald-500/20 group-hover:border-emerald-500 transition-all">
            @else
                <div class="w-8 h-8 bg-emerald-500/10 border border-emerald-500/20 rounded-lg flex items-center justify-center text-emerald-500 font-bold text-xs shrink-0 group-hover:bg-emerald-500 group-hover:text-slate-950 transition-all">
                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                </div>
            @endif
            <div class="sidebar-profile-details flex-1 min-w-0 text-left">
                <p class="text-[11px] font-bold text-white truncate">{{ auth()->user()->name ?? 'User' }}</p>
                <p class="text-[9px] text-slate-500 truncate uppercase tracking-widest font-semibold">{{ auth()->user()->role ?? 'Role' }}</p>
            </div>
        </button>

        <!-- Account Popup -->
        <div x-show="accountOpen"
             x-cloak
             @click.away="accountOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             id="sidebar-account-popup"
             class="sidebar-account-popup absolute bottom-[calc(100%+0.5rem)] left-4 right-4 bg-slate-900 border border-white/10 rounded-xl shadow-2xl py-2 z-40">
            <a href="{{ route('user.profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-[11px] font-semibold text-slate-400 hover:text-white hover:bg-white/5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span>{{ __('My Profile') }}</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="border-t border-white/5 mt-1 pt-1">
                @csrf
                <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-[11px] font-semibold text-red-400 hover:bg-red-400/5 transition-all text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>{{ __('Logout') }}</span>
                </button>
            </form>
        </div>
    </div>
</aside>
