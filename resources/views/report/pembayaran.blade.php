@extends('layouts.app')

@section('title','Report Pembayaran')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Report Pembayaran</h2>
        <form action="{{ route('report.pembayaran.export-csv') }}" method="GET" class="inline-flex items-center space-x-2">
            <input type="hidden" name="tanggal_dari" value="{{ request('tanggal_dari') }}">
            <input type="hidden" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-xl inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Export CSV
            </button>
        </form>
    </div>

    <!-- Filter Card -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="p-6">
            <form action="{{ route('report.pembayaran') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                <div class="lg:col-span-1">
                    <label for="tanggal_dari" class="block text-sm font-medium text-gray-700">Tanggal Dari</label>
                    <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="tanggal_dari" value="{{ request('tanggal_dari') }}">
                </div>

                <div class="lg:col-span-1">
                    <label for="tanggal_sampai" class="block text-sm font-medium text-gray-700">Tanggal Sampai</label>
                    <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}">
                </div>

                <div class="lg:col-span-1">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="status">
                        <option value="">-- Semua --</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="berhasil" {{ request('status') === 'berhasil' ? 'selected' : '' }}>Berhasil</option>
                        <option value="gagal" {{ request('status') === 'gagal' ? 'selected' : '' }}>Gagal</option>
                    </select>
                </div>

                <div class="lg:col-span-1">
                    <label for="metode" class="block text-sm font-medium text-gray-700">Metode</label>
                    <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="metode">
                        <option value="">-- Semua --</option>
                        <option value="manual" {{ request('metode') === 'manual' ? 'selected' : '' }}>Manual</option>
                        <option value="qr_scan" {{ request('metode') === 'qr_scan' ? 'selected' : '' }}>QR Scan</option>
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-xl transition-colors">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg p-5 shadow">
            <div class="text-sm font-semibold text-indigo-700 uppercase mb-1">Total Pembayaran</div>
            <div class="text-xl font-bold text-gray-900">{{ $count_pembayaran }}</div>
        </div>

        <div class="bg-white rounded-lg p-5 shadow">
            <div class="text-sm font-semibold text-green-700 uppercase mb-1">Total Nominal</div>
            <div class="text-xl font-bold text-gray-900">Rp {{ number_format($total_nominal, 0, ',', '.') }}</div>
        </div>

        <div class="bg-white rounded-lg p-5 shadow">
            <div class="text-sm font-semibold text-blue-700 uppercase mb-1">Rata-rata Nominal</div>
            <div class="text-xl font-bold text-gray-900">Rp {{ number_format($avg_nominal, 0, ',', '.') }}</div>
        </div>

        <div class="bg-white rounded-lg p-5 shadow">
            <div class="text-sm font-semibold text-yellow-700 uppercase mb-1">Periode</div>
            <div class="text-xl font-bold text-gray-900">
                {{ request('tanggal_dari') ?: 'Awal' }}
                s/d
                {{ request('tanggal_sampai') ?: 'Akhir' }}
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nominal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Petugas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Pembayaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pembayarans as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600">
                                    #{{ $item->id_pembayaran }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->id_parkir }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                    Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($item->metode === 'manual')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Manual</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">QR Scan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($item->status === 'berhasil')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Berhasil</span>
                                    @elseif($item->status === 'pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Gagal</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->petugas?->name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->waktu_pembayaran?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">Tidak ada data pembayaran</td>
                            </tr>
                        @endforelse
                </tbody>
            </table>
        </div>

            @if($pembayarans->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $pembayarans->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
