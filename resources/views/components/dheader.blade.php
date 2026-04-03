@php
    $headerTitle = 'Dashboard';
    $addButton = null; // ['label' => '...', 'route' => '...', 'routeName' => '...']

    if (request()->routeIs('dashboard', 'petugas.dashboard', 'owner.dashboard')) {
        $headerTitle = 'Dashboard';
    } elseif (request()->routeIs('transaksi.create-check-in')) {
        $headerTitle = 'Catat Masuk';
    } elseif (request()->routeIs('transaksi.parkir.index')) {
        $headerTitle = 'Parkir Aktif';
        $addButton = ['label' => 'Tambah Parkir', 'route' => route('transaksi.create-check-in'), 'routeName' => 'transaksi.create-check-in'];
    } elseif (request()->routeIs('transaksi.index') || request()->routeIs('transaksi.show') || request()->routeIs('transaksi.edit')) {
        $headerTitle = 'Riwayat Transaksi';
        if ((auth()->user()->role ?? null) === 'admin') {
            $addButton = ['label' => 'Tambah Transaksi', 'route' => route('transaksi.create'), 'routeName' => 'transaksi.create'];
        }
    } elseif (request()->routeIs('transaksi.create')) {
        $headerTitle = 'Tambah Transaksi';
    } elseif (request()->routeIs('users.index') || request()->routeIs('users.show') || request()->routeIs('users.edit')) {
        $headerTitle = 'Kelola User';
        $addButton = ['label' => 'Tambah User', 'route' => route('users.create'), 'routeName' => 'users.create'];
    } elseif (request()->routeIs('users.create')) {
        $headerTitle = 'Tambah User';
    } elseif (request()->routeIs('kendaraan.index') || request()->routeIs('kendaraan.show') || request()->routeIs('kendaraan.edit')) {
        $headerTitle = 'Kendaraan';
        $addButton = ['label' => 'Tambah Kendaraan', 'route' => route('kendaraan.create'), 'routeName' => 'kendaraan.create'];
    } elseif (request()->routeIs('kendaraan.create')) {
        $headerTitle = 'Tambah Kendaraan';
    } elseif (request()->routeIs('parking-maps.index') || request()->routeIs('parking-maps.edit')) {
        $headerTitle = 'Layout Peta Parkir';
        $addButton = ['label' => 'Tambah Layout', 'route' => route('parking-maps.create'), 'routeName' => 'parking-maps.create'];
    } elseif (request()->routeIs('parking-maps.create')) {
        $headerTitle = 'Tambah Layout Peta Parkir';
    } elseif (request()->routeIs('parking-maps.slots.index')) {
        $headerTitle = 'Slot Peta Parkir';
    } elseif (request()->routeIs('parking-maps.slots.create')) {
        $headerTitle = 'Tambah Slot';
    } elseif (request()->routeIs('parking-maps.slots.edit')) {
        $headerTitle = 'Edit Slot';
    } elseif (request()->routeIs('area-parkir.index') || request()->routeIs('area-parkir.show') || request()->routeIs('area-parkir.edit')) {
        $headerTitle = 'Area Parkir';
        $addButton = ['label' => 'Tambah Area', 'route' => route('area-parkir.create'), 'routeName' => 'area-parkir.create'];
    } elseif (request()->routeIs('area-parkir.create')) {
        $headerTitle = 'Tambah Area Parkir';
    } elseif (request()->routeIs('kamera.index') || request()->routeIs('kamera.edit')) {
        $headerTitle = 'Kamera';
        $addButton = ['label' => 'Tambah Kamera', 'route' => route('kamera.create'), 'routeName' => 'kamera.create'];
    } elseif (request()->routeIs('kamera.create')) {
        $headerTitle = 'Tambah Kamera';
    } elseif (request()->routeIs('tarif.index') || request()->routeIs('tarif.show') || request()->routeIs('tarif.edit')) {
        $headerTitle = 'Tarif Parkir';
        $addButton = ['label' => 'Tambah Tarif', 'route' => route('tarif.create'), 'routeName' => 'tarif.create'];
    } elseif (request()->routeIs('tarif.create')) {
        $headerTitle = 'Tambah Tarif';
    } elseif (request()->routeIs('log-aktivitas.index') || request()->routeIs('log-aktivitas.show') || request()->routeIs('log-aktivitas.edit')) {
        $headerTitle = 'Log Aktivitas';
        $addButton = ['label' => 'Tambah Log Aktivitas', 'route' => route('log-aktivitas.create'), 'routeName' => 'log-aktivitas.create'];
    } elseif (request()->routeIs('log-aktivitas.create')) {
        $headerTitle = 'Tambah Log Aktivitas';
    } elseif (request()->routeIs('payment.select-transaction')) {
        $headerTitle = 'Proses Pembayaran';
    } elseif (request()->routeIs('payment.create')) {
        $headerTitle = 'Pembayaran';
    } elseif (request()->routeIs('payment.success')) {
        $headerTitle = 'Pembayaran Berhasil';
    } elseif (request()->routeIs('payment.index')) {
        $headerTitle = 'Riwayat Pembayaran';
    } elseif (request()->routeIs('report.pembayaran')) {
        $headerTitle = 'Laporan Pembayaran';
    } elseif (request()->routeIs('report.transaksi')) {
        $headerTitle = 'Laporan Transaksi';
    } elseif (request()->routeIs('parking.map.index')) {
        $headerTitle = 'Peta Parkir';
    } elseif (request()->routeIs('user.profile')) {
        $headerTitle = 'Profil Saya';
    }
@endphp
<header class="app-top-header h-16 shrink-0 bg-[#020617] border-b border-white/10 sticky top-0 z-[55] lg:z-[55] isolate shadow-[0_1px_0_0_rgba(255,255,255,0.04)]">
    <div class="h-full w-full flex items-center justify-between px-4 lg:px-8">
        <div class="flex items-center gap-3 lg:gap-4 min-w-0">
            <!-- Mobile Toggle -->
            <button @click="sidebarOpen = true" type="button"
                    class="lg:hidden inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white/5 text-slate-400 hover:text-white transition-all active:scale-95 border border-white/5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <div class="h-8 w-1 bg-emerald-500 rounded-full shadow-[0_0_15px_rgba(16,185,129,0.5)] hidden sm:block"></div>
            <h1 class="text-[10px] lg:text-xs font-bold text-white uppercase tracking-[0.2em] lg:tracking-[0.4em] truncate">{{ $headerTitle }}</h1>
        </div>

        <div class="flex items-center gap-2 lg:gap-6">
            @if($addButton)
                <a href="{{ $addButton['route'] }}"
                   class="btn-pro-primary !py-2 !px-3 lg:!py-2.5 lg:!px-5 !text-[9px] lg:!text-[10px] !rounded-lg flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span class="hidden sm:inline">{{ $addButton['label'] }}</span>
                </a>
            @endif

            <div class="h-6 w-px bg-white/10 mx-1 lg:mx-2"></div>

            <button class="relative p-2 text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <span class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full border-2 border-[#020617]"></span>
            </button>
        </div>
    </div>
</header>
