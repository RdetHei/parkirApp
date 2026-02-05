@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Detail Transaksi #{{ str_pad($item->id_parkir, 8, '0', STR_PAD_LEFT) }}</h2>
        <a href="{{ route('transaksi.index') }}" class="text-gray-600 hover:text-gray-800">Kembali</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Informasi Kendaraan -->
        <div class="bg-white p-6 rounded-lg shadow">
                <div>
                    <p class="text-sm text-gray-600">Plat Nomor</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $item->kendaraan->plat_nomor ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Jenis Kendaraan</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $item->kendaraan->jenis_kendaraan ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Warna</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $item->kendaraan->warna ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Pemilik</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $item->kendaraan->pemilik ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Informasi Area & Operator -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b-2 border-green-500">Informasi Lainnya</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Area Parkir</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $item->area->nama_area ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Operator</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $item->user->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="text-lg font-semibold text-gray-800 text-sm">{{ $item->user->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tarif per Jam</p>
                    <p class="text-lg font-semibold text-gray-800">Rp {{ number_format($item->tarif->tarif_perjam ?? 0, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Catatan</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $item->catatan ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Waktu Parkir -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b-2 border-purple-500">Waktu Parkir</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Waktu Masuk</p>
                    <p class="text-lg font-semibold text-gray-800">
                        {{ $item->waktu_masuk->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Waktu Keluar</p>
                    <p class="text-lg font-semibold text-gray-800">
                        @if($item->waktu_keluar)
                            {{ $item->waktu_keluar->format('d/m/Y H:i') }}
                        @else
                            <span class="text-red-600">Belum Keluar</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Durasi</p>
                    <p class="text-lg font-semibold text-gray-800">
                        @if($item->durasi_jam)
                            {{ $item->durasi_jam }} jam
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Biaya & Status -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b-2 border-orange-500">Biaya & Status</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Total Biaya</p>
                    <p class="text-2xl font-bold text-green-600">
                        Rp {{ number_format($item->biaya_total ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <p class="text-lg">
                        @if($item->status === 'masuk')
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-bold">MASUK</span>
                        @else
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-bold">KELUAR</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-end gap-3 mt-6">
        <a href="{{ route('transaksi.edit', $item->id_parkir) }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Edit
        </a>
        @if($item->status === 'keluar' && auth()->user()->role === 'admin')
            <a href="{{ route('transaksi.print', $item->id_parkir) }}" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                Cetak Struk
            </a>
        @endif
    </div>
</div>
@endsection
