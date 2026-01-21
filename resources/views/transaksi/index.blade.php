@extends('layouts.app')

@section('title','Transaksi')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Riwayat Transaksi Parkir</h2>
        <a href="{{ route('transaksi.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Tambah Manual
        </a>
    </div>

    @if($message = Session::get('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ $message }}
        </div>
    @endif

    @if($message = Session::get('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ $message }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($items->count())
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plat Nomor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Masuk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Keluar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durasi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Biaya</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($items as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-bold text-blue-600">
                        #{{ str_pad($item->id_parkir, 8, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                        {{ $item->kendaraan->plat_nomor ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $item->waktu_masuk->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        @if($item->waktu_keluar)
                            {{ $item->waktu_keluar->format('d/m/Y H:i') }}
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-700">
                        @if($item->durasi_jam)
                            {{ $item->durasi_jam }} jam
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-green-600">
                        @if($item->biaya_total)
                            Rp {{ number_format($item->biaya_total, 0, ',', '.') }}
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($item->status === 'masuk')
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold">MASUK</span>
                        @else
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">KELUAR</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm space-x-2">
                        <a href="{{ route('transaksi.show', $item->id_parkir) }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                            👁️ Lihat
                        </a>
                        <a href="{{ route('transaksi.edit', $item->id_parkir) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                            ✏️ Edit
                        </a>
                        @if($item->status === 'keluar')
                            <a href="{{ route('transaksi.print', $item->id_parkir) }}" class="text-purple-600 hover:text-purple-800 font-semibold">
                                🖨️ Struk
                            </a>
                        @endif
                        <form action="{{ route('transaksi.destroy', $item->id_parkir) }}" method="POST" class="inline"
                              onsubmit="return confirm('Yakin hapus transaksi ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:text-red-800 font-semibold">🗑️ Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $items->links() }}
        </div>
        @else
        <div class="px-6 py-8 text-center text-gray-500">
            <p class="text-lg">Belum ada data transaksi</p>
        </div>
        @endif
    </div>
</div>
@endsection
