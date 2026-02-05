@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Owner Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-2xl p-6 border border-gray-200">
                <p class="text-sm text-gray-600">Total Pendapatan</p>
                <p class="text-3xl font-bold text-green-700">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-200">
                <p class="text-sm text-gray-600">Pendapatan Hari Ini</p>
                <p class="text-3xl font-bold text-green-700">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-200">
                <p class="text-sm text-gray-600">Transaksi Berhasil</p>
                <p class="text-3xl font-bold text-gray-900">{{ $transaksiBerhasil }}</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-200">
                <p class="text-sm text-gray-600">Pembayaran Pending</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $pembayaranPending }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-gray-200 lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Ringkasan 7 Hari</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Pendapatan</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Transaksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($harian as $h)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $h['label'] }}</td>
                                    <td class="px-4 py-2 text-sm font-semibold text-green-700">Rp {{ number_format($h['nominal'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-sm font-semibold text-gray-900">{{ $h['transaksi'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Kapasitas Area</h2>
                </div>
                <div class="space-y-4">
                    @foreach($areaParkir as $area)
                        @php
                            $kap = $area->kapasitas ?: 0;
                            $terisi = $area->terisi ?: 0;
                            $pct = $kap > 0 ? round(($terisi / $kap) * 100) : 0;
                            $bar = $pct . '%';
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-semibold text-gray-900">{{ $area->nama_area }}</span>
                                <span class="text-gray-600">{{ $terisi }}/{{ $kap }} ({{ $pct }}%)</span>
                            </div>
                            <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-3 bg-green-600" style="width: {{ $bar }}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
