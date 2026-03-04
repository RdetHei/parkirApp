@extends('layouts.app')

@section('title', 'Riwayat Parkir')

@section('content')
    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Riwayat Parkir</h1>
                    <p class="mt-1 text-sm text-gray-500">Seluruh riwayat transaksi parkir Anda.</p>
                </div>
                <a href="{{ route('user.dashboard') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali
                </a>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kendaraan</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Area</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu Masuk</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu Keluar</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Biaya</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($transactions as $trx)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $trx->kendaraan->plat_nomor ?? '-' }}</p>
                                            <p class="text-[10px] text-gray-500 uppercase">{{ $trx->kendaraan->jenis_kendaraan ?? 'Kendaraan' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $trx->area->nama_area ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <p class="text-gray-900 font-medium">{{ $trx->waktu_masuk->format('H:i') }}</p>
                                        <p class="text-[10px] text-gray-500">{{ $trx->waktu_masuk->format('d/m/Y') }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($trx->waktu_keluar)
                                            <p class="text-gray-900 font-medium">{{ $trx->waktu_keluar->format('H:i') }}</p>
                                            <p class="text-[10px] text-gray-500">{{ $trx->waktu_keluar->format('d/m/Y') }}</p>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Masih Parkir</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-gray-900">Rp {{ number_format($trx->biaya_total, 0, ',', '.') }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($trx->status === 'masuk')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                                Aktif
                                            </span>
                                        @elseif($trx->status === 'keluar')
                                            @if(($trx->pembayaran->status ?? '') === 'berhasil')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                    Selesai
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                                    Belum Dibayar
                                                </span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        Belum ada riwayat transaksi parkir.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($transactions->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
