<header class="h-16 bg-white border-b border-gray-200 sticky top-0 z-40">
    <div class="h-16 w-full flex items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3 min-w-0">

            <h1 class="text-xl font-bold text-gray-900 truncate">{{ $title ?? 'Dashboard' }}</h1>
        </div>

        <div class="flex items-center gap-4">
            @if(request()->routeIs('users.index'))
                <a href="{{ route('users.create') }}" class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Tambah User
                </a>
            @elseif(request()->routeIs('kendaraan.index'))
                <a href="{{ route('kendaraan.create') }}" class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Tambah Kendaraan
                </a>
            @elseif(request()->routeIs('area-parkir.index'))
                <a href="{{ route('area-parkir.create') }}" class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Tambah Area Parkir
                </a>
            @elseif(request()->routeIs('tarif.index'))
                <a href="{{ route('tarif.create') }}" class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Tambah Tarif
                </a>
            @elseif(request()->routeIs('log-aktivitas.index'))
                <a href="{{ route('log-aktivitas.create') }}" class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Tambah Log Aktivitas
                </a>
            @elseif(request()->routeIs('transaksi.index'))
                <a href="{{ route('transaksi.create') }}" class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Tambah Transaksi
                </a>
            @endif
        </div>
    </div>
</header>
