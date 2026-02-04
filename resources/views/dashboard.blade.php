@extends('layouts.app')

@section('content')
    <div class="p-6">

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Kendaraan -->
            <div class="bg-white rounded-2xl p-5 border border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mb-1">Total Kendaraan</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalKendaraan }}</p>
            </div>

            <!-- Total Transaksi -->
            <div class="bg-white rounded-2xl p-5 border border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mb-1">Total Transaksi</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalTransaksi }}</p>
            </div>

            <!-- Kendaraan Aktif -->
            <div class="bg-white rounded-2xl p-5 border border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-yellow-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full">Aktif</span>
                </div>
                <p class="text-sm text-gray-500 mb-1">Kendaraan Aktif</p>
                <p class="text-2xl font-bold text-gray-900">{{ $transaksiAktif }}</p>
            </div>

            <!-- Pembayaran Pending -->
            <div class="bg-white rounded-2xl p-5 border border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-1 rounded-full">Pending</span>
                </div>
                <p class="text-sm text-gray-500 mb-1">Pembayaran Pending</p>
                <p class="text-2xl font-bold text-gray-900">{{ $pembayaranPending }}</p>
            </div>
        </div>

        <!-- Revenue & Area Status -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Pendapatan Card -->
            <div class="bg-white rounded-2xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">Pendapatan</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
                            <p class="text-xs font-semibold text-green-700 uppercase mb-2">Hari Ini</p>
                            <p class="text-2xl font-bold text-green-900">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                            <p class="text-xs font-semibold text-blue-700 uppercase mb-2">Total</p>
                            <p class="text-2xl font-bold text-blue-900">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Area Parkir -->
            <div class="bg-white rounded-2xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">Status Area Parkir</h2>
                </div>
                <div class="p-6">
                    <div class="mb-3">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-semibold text-gray-700">Kapasitas Total</p>
                            <p class="text-sm font-bold text-gray-900">{{ $totalTerisi }} / {{ $totalKapasitas }}</p>
                        </div>
                        @php
                            $percentage = $totalKapasitas > 0 ? ($totalTerisi / $totalKapasitas * 100) : 0;
                            $colorClass = $percentage >= 80 ? 'bg-red-500' : ($percentage >= 60 ? 'bg-yellow-500' : 'bg-green-500');
                        @endphp
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="{{ $colorClass }} h-3 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ number_format($percentage, 1) }}% Terisi</p>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Tersedia</span>
                            <span class="font-semibold text-gray-900">{{ $totalKapasitas - $totalTerisi }} Slot</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Area Parkir -->
        <div class="bg-white rounded-2xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Detail Area Parkir</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Status ketersediaan per area</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Area</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kapasitas</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Terisi</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tersedia</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($areaParkir as $area)
                            @php
                                $percentage = $area->kapasitas > 0 ? ($area->terisi / $area->kapasitas * 100) : 0;
                                $badgeColor = $percentage >= 80 ? 'bg-red-500' : ($percentage >= 60 ? 'bg-yellow-500' : 'bg-green-500');
                                $statusText = $percentage >= 80 ? 'Penuh' : ($percentage >= 60 ? 'Hampir Penuh' : 'Tersedia');
                                $statusBg = $percentage >= 80 ? 'bg-red-100 text-red-800' : ($percentage >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 {{ $badgeColor }} rounded-full"></div>
                                        <span class="text-sm font-medium text-gray-900">{{ $area->nama_area }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $area->kapasitas }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900">{{ $area->terisi }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $area->kapasitas - $area->terisi }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1">
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="{{ $badgeColor }} h-2 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusBg }}">
                                            {{ $statusText }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12">
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-gray-900 font-semibold mb-1">Belum Ada Data Area Parkir</p>
                                        <p class="text-sm text-gray-500">Tambahkan area parkir untuk memulai</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
