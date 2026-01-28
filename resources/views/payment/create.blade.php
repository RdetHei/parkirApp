@extends('layouts.app')

@section('title', 'Pilih Metode Pembayaran')

@section('content')
@extends('layouts.app')

@section('title', 'Pilih Metode Pembayaran')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md mt-10">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Pilih Metode Pembayaran</h2>

    <!-- Ringkasan Transaksi -->
    <div class="bg-blue-50 border border-blue-300 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-bold text-blue-900 mb-4">Ringkasan Transaksi</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-sm text-blue-700">Plat Nomor</p>
                <p class="text-xl font-bold text-blue-900">{{ $transaksi->kendaraan->plat_nomor }}</p>
            </div>
            <div>
                <p class="text-sm text-blue-700">Durasi</p>
                <p class="text-xl font-bold text-blue-900">{{ $transaksi->durasi_jam }} jam</p>
            </div>
            <div>
                <p class="text-sm text-blue-700">Tarif/Jam</p>
                <p class="text-xl font-bold text-blue-900">Rp {{ number_format($transaksi->tarif->tarif_perjam, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-sm text-blue-700">Total Bayar</p>
                <p class="text-2xl font-bold text-green-600">Rp {{ number_format($transaksi->biaya_total, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Pilihan Metode -->
    <div class="grid grid-cols-1 gap-6"> {{-- Changed from md:grid-cols-2 to grid-cols-1 to fit max-w-2xl better --}}
        <!-- Pembayaran Manual -->
        <div class="bg-white border-2 border-gray-300 rounded-lg p-8 hover:border-indigo-500 hover:shadow-lg transition cursor-pointer"
             onclick="document.location.href='{{ route('payment.manual-confirm', $transaksi->id_parkir) }}'">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                </div>
                <span class="text-xs font-bold bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full">PETUGAS</span>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Pembayaran Manual</h3>
            <p class="text-gray-600 mb-4">Petugas parkir akan memproses pembayaran secara manual</p>
            <ul class="space-y-2 text-sm text-gray-700 mb-6">
                <li class="flex items-center gap-2">
                    <span class="text-green-600">•</span> Petugas input nominal pembayaran
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-green-600">•</span> Fleksibel dengan berbagai metode
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-green-600">•</span> Dapat diskon atau promosi
                </li>
            </ul>
            <div class="bg-indigo-50 p-3 rounded text-indigo-900 font-semibold text-center">
                Lanjut ke Pembayaran Manual
            </div>
        </div>

        <!-- Pembayaran QR Scan -->
        <div class="bg-white border-2 border-gray-300 rounded-lg p-8 hover:border-indigo-500 hover:shadow-lg transition cursor-pointer"
             onclick="document.location.href='{{ route('payment.qr-scan', $transaksi->id_parkir) }}'">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <span class="text-xs font-bold bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full">OTOMATIS</span>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Pembayaran QR Scan</h3>
            <p class="text-gray-600 mb-4">Pengendara langsung scan QR untuk membayar otomatis</p>
            <ul class="space-y-2 text-sm text-gray-700 mb-6">
                <li class="flex items-center gap-2">
                    <span class="text-green-600">•</span> Cepat & otomatis
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-green-600">•</span> Tidak perlu antri
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-green-600">•</span> Riwayat digital otomatis
                </li>
            </ul>
            <div class="bg-indigo-50 p-3 rounded text-indigo-900 font-semibold text-center">
                Lanjut ke QR Scan
            </div>
        </div>
    </div>

    <!-- Tombol Kembali -->
    <div class="flex justify-center mt-8">
        <a href="{{ route('transaksi.parkir.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-transparent rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection

