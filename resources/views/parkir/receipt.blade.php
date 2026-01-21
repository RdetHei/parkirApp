@extends('layouts.app')

@section('title', 'Struk Parkir')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg p-8">
        <!-- Receipt Header -->
        <div class="text-center mb-8 pb-4 border-b-2 border-gray-300">
            <h1 class="text-3xl font-bold text-gray-800">STRUK PARKIR</h1>
            <p class="text-gray-600 mt-2">{{ optional($transaksi->area)->nama_area ?? 'Area Parkir' }}</p>
        </div>

        <!-- Transaction ID & Date -->
        <div class="grid grid-cols-2 gap-4 mb-6 pb-4 border-b border-gray-200">
            <div>
                <p class="text-sm text-gray-600">No. Transaksi</p>
                <p class="text-lg font-bold text-gray-800">#{{ str_pad($transaksi->id_parkir, 8, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Tanggal Cetak</p>
                <p class="text-lg font-bold text-gray-800">{{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <!-- Vehicle Information -->
        <div class="mb-6 pb-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-800 mb-3">Informasi Kendaraan</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Plat Nomor</p>
                    <p class="text-base font-semibold text-gray-800">{{ $transaksi->kendaraan->plat_nomor ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Jenis Kendaraan</p>
                    <p class="text-base font-semibold text-gray-800">{{ $transaksi->kendaraan->jenis_kendaraan ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Warna</p>
                    <p class="text-base font-semibold text-gray-800">{{ $transaksi->kendaraan->warna ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Pemilik</p>
                    <p class="text-base font-semibold text-gray-800">{{ $transaksi->kendaraan->pemilik ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Parking Time Details -->
        <div class="mb-6 pb-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-800 mb-3">Detail Parkir</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Waktu Masuk</p>
                    <p class="text-base font-semibold text-gray-800">
                        {{ $transaksi->waktu_masuk ? \Carbon\Carbon::parse($transaksi->waktu_masuk)->format('d/m/Y H:i') : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Waktu Keluar</p>
                    <p class="text-base font-semibold text-gray-800">
                        @if($transaksi->waktu_keluar)
                            {{ \Carbon\Carbon::parse($transaksi->waktu_keluar)->format('d/m/Y H:i') }}
                        @else
                            <span class="text-red-600">Belum Keluar</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Duration & Cost Calculation -->
        <div class="mb-6 pb-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-800 mb-3">Perhitungan Biaya</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-700">Durasi Parkir</span>
                    <span class="font-semibold text-gray-800">{{ $transaksi->durasi_jam ?? '-' }} jam</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700">Tarif per Jam</span>
                    <span class="font-semibold text-gray-800">Rp {{ number_format($transaksi->tarif->tarif_perjam ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t-2 border-gray-300 pt-3 mt-3">
                    <span class="text-gray-800">Total Biaya</span>
                    <span class="text-green-600">Rp {{ number_format($transaksi->biaya_total ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Parking Status -->
        <div class="mb-6 pb-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <span class="text-gray-700">Status Parkir</span>
                @if($transaksi->status === 'masuk')
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-bold">MASUK</span>
                @else
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-bold">KELUAR</span>
                @endif
            </div>
        </div>

        <!-- Operator Information -->
        <div class="mb-6 pb-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-800 mb-3">Informasi Operator</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Nama Operator</p>
                    <p class="text-base font-semibold text-gray-800">{{ $transaksi->user->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="text-base font-semibold text-gray-800 text-xs">{{ $transaksi->user->email ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center py-4 text-sm text-gray-600">
            <p>Terima kasih telah menggunakan layanan parkir kami</p>
            <p class="mt-2">{{ now()->format('d M Y') }}</p>
        </div>

        <!-- Print Button -->
        <div class="flex justify-between mt-8 pt-4 border-t border-gray-200">
            <a href="javascript:window.print()" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Cetak Struk
            </a>
            <a href="{{ route('transaksi.parkir.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700">
                Kembali
            </a>
        </div>
    </div>
</div>

<style>
    @media print {
        body {
            background: white;
        }
        .max-w-2xl {
            max-width: 58mm;
        }
        .shadow {
            box-shadow: none;
        }
        button, .flex.justify-between.mt-8 {
            display: none;
        }
        a[href*="print"] {
            display: none;
        }
    }
</style>
@endsection
