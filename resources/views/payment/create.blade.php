
@extends('layouts.app')

@section('title', 'Pilih Metode Pembayaran')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('transaksi.index', ['status' => 'masuk']),
        'title' => 'Pilih Metode Pembayaran',
        'description' => 'Pilih metode pembayaran untuk transaksi ini',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>',
        'cardTitle' => 'Rincian Pembayaran',
        'cardDescription' => 'Ringkasan transaksi dan pilihan metode pembayaran',
        'action' => '#', // Dummy action as there's no single form submission here
        'method' => 'GET', // Dummy method
        'submitText' => '', // No primary submit button
        'cancelText' => 'Kembali ke Dashboard'
    ])
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
        <div class="grid grid-cols-1 gap-6">
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
    @endcomponent
@endsection


