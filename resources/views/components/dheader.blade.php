@php
    $headerTitle = __('Dashboard');
    $addButton = null; // ['label' => '...', 'route' => '...', 'routeName' => '...']

    if (request()->routeIs('dashboard', 'petugas.dashboard', 'owner.dashboard', 'user.dashboard')) {
        $headerTitle = __('Dashboard');
    } elseif (request()->routeIs('user.saldo.index') || request()->routeIs('user.saldo.topup')) {
        $headerTitle = 'NestonPay';
    } elseif (request()->routeIs('user.vehicles.index')) {
        $headerTitle = __('Kendaraan Saya');
    } elseif (request()->routeIs('user.history')) {
        $headerTitle = __('Riwayat Parkir');
    } elseif (request()->routeIs('user.bills')) {
        $headerTitle = __('Tagihan Saya');
    } elseif (request()->routeIs('user.bookings')) {
        $headerTitle = __('Booking Slot');
    } elseif (request()->routeIs('anpr.index')) {
        $headerTitle = 'Smart Scanner';
    } elseif (request()->routeIs('transaksi.create-check-in')) {
        $headerTitle = __('Catat Masuk');
    } elseif (request()->routeIs('transaksi.parkir.index')) {
        $headerTitle = __('Parkir Aktif');
        $addButton = ['label' => __('Tambah Parkir'), 'route' => route('transaksi.create-check-in'), 'routeName' => 'transaksi.create-check-in'];
    } elseif (request()->routeIs('transaksi.index') || request()->routeIs('transaksi.show') || request()->routeIs('transaksi.edit')) {
        $headerTitle = __('Riwayat Transaksi');
        if ((auth()->user()->role ?? null) === 'admin') {
            $addButton = ['label' => __('Tambah Transaksi'), 'route' => route('transaksi.create'), 'routeName' => 'transaksi.create'];
        }
    } elseif (request()->routeIs('transaksi.create')) {
        $headerTitle = __('Tambah Transaksi');
    } elseif (request()->routeIs('users.index') || request()->routeIs('users.show') || request()->routeIs('users.edit')) {
        $headerTitle = __('Kelola User');
        $addButton = ['label' => __('Tambah User'), 'route' => route('users.create'), 'routeName' => 'users.create'];
    } elseif (request()->routeIs('admin.rfid.index')) {
        $headerTitle = __('Kelola RFID');
    } elseif (request()->routeIs('rfid.identify.page', 'rfid.access.scan-page', 'parkir.scan')) {
        $headerTitle = __('RFID Terminal');
    } elseif (request()->routeIs('rfid.login.page')) {
        $headerTitle = __('Login RFID');
    } elseif (request()->routeIs('users.create')) {
        $headerTitle = __('Tambah User');
    } elseif (request()->routeIs('users.scan-rfid')) {
        $headerTitle = __('Registrasi RFID');
    } elseif (request()->routeIs('kendaraan.index') || request()->routeIs('kendaraan.show') || request()->routeIs('kendaraan.edit')) {
        $headerTitle = __('Kendaraan');
        $addButton = ['label' => __('Tambah Kendaraan'), 'route' => route('kendaraan.create'), 'routeName' => 'kendaraan.create'];
    } elseif (request()->routeIs('kendaraan.create')) {
        $headerTitle = __('Tambah Kendaraan');
    } elseif (request()->routeIs('parking-maps.index') || request()->routeIs('parking-maps.edit')) {
        $headerTitle = __('Layout Peta Parkir');
        $addButton = ['label' => __('Tambah Layout'), 'route' => route('parking-maps.create'), 'routeName' => 'parking-maps.create'];
    } elseif (request()->routeIs('parking-maps.create')) {
        $headerTitle = __('Tambah Layout Peta Parkir');
    } elseif (request()->routeIs('parking-maps.slots.index')) {
        $headerTitle = __('Slot Peta Parkir');
    } elseif (request()->routeIs('parking-maps.slots.create')) {
        $headerTitle = __('Tambah Slot');
    } elseif (request()->routeIs('parking-maps.slots.edit')) {
        $headerTitle = __('Edit Slot');
    } elseif (request()->routeIs('area-parkir.index') || request()->routeIs('area-parkir.show') || request()->routeIs('area-parkir.edit') || request()->routeIs('area-parkir.design')) {
        $headerTitle = __('Area Parkir');
        $addButton = ['label' => __('Tambah Area'), 'route' => route('area-parkir.create'), 'routeName' => 'area-parkir.create'];
    } elseif (request()->routeIs('area-parkir.create')) {
        $headerTitle = __('Tambah Area Parkir');
    } elseif (request()->routeIs('kamera.index') || request()->routeIs('kamera.edit')) {
        $headerTitle = __('Kamera');
        $addButton = ['label' => __('Tambah Kamera'), 'route' => route('kamera.create'), 'routeName' => 'kamera.create'];
    } elseif (request()->routeIs('kamera.create')) {
        $headerTitle = __('Tambah Kamera');
    } elseif (request()->routeIs('tarif.index') || request()->routeIs('tarif.show') || request()->routeIs('tarif.edit')) {
        $headerTitle = __('Tarif Parkir');
        $addButton = ['label' => __('Tambah Tarif'), 'route' => route('tarif.create'), 'routeName' => 'tarif.create'];
    } elseif (request()->routeIs('tarif.create')) {
        $headerTitle = __('Tambah Tarif');
    } elseif (request()->routeIs('log-aktivitas.index') || request()->routeIs('log-aktivitas.show') || request()->routeIs('log-aktivitas.edit')) {
        $headerTitle = __('Log Aktivitas');
        $addButton = ['label' => __('Tambah Log Aktivitas'), 'route' => route('log-aktivitas.create'), 'routeName' => 'log-aktivitas.create'];
    } elseif (request()->routeIs('log-aktivitas.create')) {
        $headerTitle = __('Tambah Log Aktivitas');
    } elseif (request()->routeIs('payment.select-transaction')) {
        $headerTitle = __('Proses Pembayaran');
    } elseif (request()->routeIs('payment.create')) {
        $headerTitle = __('Pembayaran');
    } elseif (request()->routeIs('payment.success')) {
        $headerTitle = __('Pembayaran Berhasil');
    } elseif (request()->routeIs('payment.index')) {
        $headerTitle = __('Riwayat Pembayaran');
    } elseif (request()->routeIs('report.pembayaran')) {
        $headerTitle = __('Laporan Pembayaran');
    } elseif (request()->routeIs('report.transaksi')) {
        $headerTitle = __('Laporan Transaksi');
    } elseif (request()->routeIs('user.profile')) {
        $headerTitle = __('Profil Saya');
    }
@endphp
<header class="app-top-header h-16 shrink-0 bg-[#020617] border-b border-white/10 sticky top-0 z-[55] lg:z-[55] isolate shadow-[0_1px_0_0_rgba(255,255,255,0.04)]">
    <div class="h-full w-full flex items-center justify-between px-4 lg:px-8">
        <div class="flex items-center gap-3 lg:gap-4 min-w-0">
            <!-- Mobile Toggle -->
            <button @click="sidebarOpen = !sidebarOpen" type="button"
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

            <!-- Language Switcher -->
            <div class="flex items-center bg-white/5 rounded-lg p-1 border border-white/5">
                <a href="{{ route('lang.switch', 'id') }}" class="px-2 py-0.5 text-[9px] font-bold rounded {{ App::getLocale() == 'id' ? 'bg-emerald-500 text-slate-950' : 'text-slate-500 hover:text-white' }} transition-all uppercase tracking-tighter">ID</a>
                <a href="{{ route('lang.switch', 'en') }}" class="px-2 py-0.5 text-[9px] font-bold rounded {{ App::getLocale() == 'en' ? 'bg-emerald-500 text-slate-950' : 'text-slate-500 hover:text-white' }} transition-all uppercase tracking-tighter">EN</a>
            </div>
        </div>
    </div>
</header>
