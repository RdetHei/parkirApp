@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto px-4">


    @if($message = Session::get('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ $message }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($pembayarans->count())
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Pembayaran</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plat Nomor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nominal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Petugas</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($pembayarans as $pembayaran)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-bold text-blue-600">
                        #{{ str_pad($pembayaran->id_pembayaran, 8, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                        {{ $pembayaran->transaksi->kendaraan->plat_nomor ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-green-600">
                        Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($pembayaran->metode === 'manual')
                            <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-bold">Manual</span>
                        @else
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">QR Scan</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($pembayaran->status === 'berhasil')
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">Berhasil</span>
                        @elseif($pembayaran->status === 'pending')
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold">⏱ Pending</span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold">✗ Gagal</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $pembayaran->waktu_pembayaran?->format('d/m/Y H:i') ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $pembayaran->petugas->name ?? 'Sistem' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $pembayarans->links() }}
        </div>
        @else
        <div class="px-6 py-8 text-center text-gray-500">
            <p class="text-lg">Belum ada data pembayaran</p>
        </div>
        @endif
    </div>
</div>
@endsection
