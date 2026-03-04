@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Petugas Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Halo {{ auth()->user()->name }}, monitor aktivitas parkir hari ini.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('transaksi.create-check-in') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Catat Masuk
                </a>
                <a href="{{ route('payment.select-transaction') }}" class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Pembayaran
                </a>
            </div>
        </div>

        <!-- Statistik Utama -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Parkir Aktif</p>
                <div class="mt-2 flex items-baseline gap-2">
                    <p class="text-2xl font-bold text-blue-600">{{ $transaksiAktif }}</p>
                    <p class="text-xs text-gray-400">kendaraan</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Transaksi Masuk Hari Ini</p>
                <div class="mt-2 flex items-baseline gap-2">
                    <p class="text-2xl font-bold text-gray-900">{{ $transaksiHariIni }}</p>
                    <p class="text-xs text-gray-400">transaksi</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Kapasitas Terisi</p>
                <div class="mt-2 flex items-baseline gap-2">
                    <p class="text-2xl font-bold text-emerald-600">{{ $totalTerisi }} / {{ $totalKapasitas }}</p>
                    <p class="text-xs text-gray-400">slot</p>
                </div>
                <div class="mt-3 w-full bg-gray-100 rounded-full h-1.5">
                    @php $percent = $totalKapasitas > 0 ? ($totalTerisi / $totalKapasitas) * 100 : 0; @endphp
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ min($percent, 100) }}%"></div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pendapatan Hari Ini</p>
                <div class="mt-2">
                    <p class="text-2xl font-bold text-indigo-600">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Menu Cepat & Area Monitoring -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-sm font-bold text-gray-900">Kapasitas Area</h2>
                    </div>
                    <div class="p-5 space-y-4">
                        @foreach($areaParkir as $area)
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-medium text-gray-700">{{ $area->nama_area }}</span>
                                    <span class="text-xs font-bold {{ $area->terisi >= $area->kapasitas ? 'text-red-500' : 'text-emerald-500' }}">
                                        {{ $area->terisi }}/{{ $area->kapasitas }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1">
                                    @php $percentArea = $area->kapasitas > 0 ? ($area->terisi / $area->kapasitas) * 100 : 0; @endphp
                                    <div class="{{ $percentArea >= 90 ? 'bg-red-500' : 'bg-emerald-500' }} h-1 rounded-full" style="width: {{ min($percentArea, 100) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-sm font-bold text-gray-900">Navigasi Cepat</h2>
                    </div>
                    <div class="p-2 grid grid-cols-2 gap-2">
                        <a href="{{ route('transaksi.parkir.index') }}" class="p-3 rounded-xl hover:bg-gray-50 flex flex-col items-center gap-2 text-center transition">
                            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-green-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-700">Monitor Parkir</span>
                        </a>
                        <a href="{{ route('transaksi.index') }}" class="p-3 rounded-xl hover:bg-gray-50 flex flex-col items-center gap-2 text-center transition">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-700">Riwayat Trx</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Aktivitas Terbaru -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h2 class="text-sm font-bold text-gray-900">Aktivitas Terbaru</h2>
                        <a href="{{ route('transaksi.index') }}" class="text-xs text-blue-600 font-medium hover:underline">Lihat Semua</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kendaraan</th>
                                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Area</th>
                                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu</th>
                                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($aktivitasTerbaru as $trx)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center font-bold text-gray-600 text-[10px]">
                                                    {{ $trx->kendaraan->plat_nomor ?? '-' }}
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-900">{{ $trx->kendaraan->plat_nomor ?? '-' }}</p>
                                                    <p class="text-[10px] text-gray-500 uppercase">{{ $trx->kendaraan->jenis_kendaraan ?? 'Kendaraan' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="text-xs text-gray-600">{{ $trx->area->nama_area ?? '-' }}</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-xs text-gray-900 font-medium">{{ $trx->waktu_masuk->format('H:i') }}</p>
                                            <p class="text-[10px] text-gray-500">{{ $trx->waktu_masuk->format('d/m/Y') }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            @if($trx->status === 'masuk')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                                    Masuk
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-700 border border-gray-100">
                                                    Keluar
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-5 py-8 text-center text-gray-500">Belum ada aktivitas hari ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

