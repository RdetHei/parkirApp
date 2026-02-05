@extends('layouts.app')

@section('title','Transaksi')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">

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

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="p-6 border-b bg-gray-50">
            <form action="{{ route('transaksi.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Plat Nomor</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Dari</label>
                    <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Sampai</label>
                    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-200">
                </div>
                <div class="lg:col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Area Parkir</label>
                    <select name="id_area" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-200">
                        <option value="">Semua Area</option>
                        @foreach(\App\Models\AreaParkir::all() as $area)
                            <option value="{{ $area->id_area }}" {{ request('id_area') == $area->id_area ? 'selected' : '' }}>{{ $area->nama_area }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-1">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-xl">Filter</button>
                </div>
            </form>
        </div>
        @if($transaksis->count())
        <table class="w-full table-auto divide-y divide-gray-200">
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
                @foreach($transaksis as $transaksi)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-bold text-blue-600">
                        #{{ str_pad($transaksi->id_parkir, 8, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                        {{ $transaksi->kendaraan->plat_nomor ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $transaksi->waktu_masuk->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        @if($transaksi->waktu_keluar)
                            {{ $transaksi->waktu_keluar->format('d/m/Y H:i') }}
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-700">
                        @if($transaksi->durasi_jam)
                            {{ $transaksi->durasi_jam }} jam
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-green-600">
                        @if($transaksi->biaya_total)
                            Rp {{ number_format($transaksi->biaya_total, 0, ',', '.') }}
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($transaksi->status === 'masuk')
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold">MASUK</span>
                        @else
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">KELUAR</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm space-x-2">
                        <a href="{{ route('transaksi.show', $transaksi->id_parkir) }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                            Lihat
                        </a>
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('transaksi.edit', $transaksi->id_parkir) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                                Edit
                            </a>
                        @endif
                        @if($transaksi->status === 'keluar' && auth()->user()->role === 'admin')
                            <a href="{{ route('transaksi.print', $transaksi->id_parkir) }}" class="text-purple-600 hover:text-purple-800 font-semibold">
                            Struk
                            </a>
                        @endif
                        @if(auth()->user()->role === 'admin')
                            <form action="{{ route('transaksi.destroy', $transaksi->id_parkir) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Yakin hapus transaksi ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 font-semibold">Hapus</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $transaksis->links() }}
        </div>
        @else
        <div class="px-6 py-8 text-center text-gray-500">
            <p class="text-lg">Belum ada data transaksi</p>
        </div>
        @endif
    </div>
</div>
@endsection
