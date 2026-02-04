@php
    $headerTitle = 'Dashboard';
    $addButton = null; // ['label' => '...', 'route' => '...', 'routeName' => '...']

    if (request()->routeIs('dashboard')) {
        $headerTitle = 'Dashboard';
    } elseif (request()->routeIs('transaksi.create-check-in')) {
        $headerTitle = 'Catat Masuk';
    } elseif (request()->routeIs('transaksi.parkir.index')) {
        $headerTitle = 'Parkir Aktif';
        $addButton = ['label' => 'Tambah Parkir', 'route' => route('transaksi.create-check-in'), 'routeName' => 'transaksi.create-check-in'];
    } elseif (request()->routeIs('transaksi.index') || request()->routeIs('transaksi.show') || request()->routeIs('transaksi.edit')) {
        $headerTitle = 'Riwayat Transaksi';
        $addButton = ['label' => 'Tambah Transaksi', 'route' => route('transaksi.create'), 'routeName' => 'transaksi.create'];
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
    } elseif (request()->routeIs('area-parkir.index') || request()->routeIs('area-parkir.show') || request()->routeIs('area-parkir.edit')) {
        $headerTitle = 'Area Parkir';
        $addButton = ['label' => 'Tambah Area', 'route' => route('area-parkir.create'), 'routeName' => 'area-parkir.create'];
    } elseif (request()->routeIs('area-parkir.create')) {
        $headerTitle = 'Tambah Area Parkir';
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
    } elseif (request()->routeIs('payment.create') || request()->routeIs('payment.manual-confirm') || request()->routeIs('payment.qr-scan')) {
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
    }
@endphp
<header class="h-16 bg-white border-b border-gray-200 sticky top-0 z-40">
    <div class="h-16 w-full flex items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3 min-w-0">
            <h1 class="text-xl font-bold text-gray-900 truncate">{{ $headerTitle }}</h1>
        </div>

        <div class="flex items-center gap-4">
            @if($addButton)
                <a href="{{ $addButton['route'] }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>{{ $addButton['label'] }}</span>
                </a>
            @endif
        </div>
    </div>
</header>
