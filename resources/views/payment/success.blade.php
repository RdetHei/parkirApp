@extends('layouts.app')

@section('title', 'Pembayaran Berhasil')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <!-- Success Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-green-600 mb-2">Pembayaran Berhasil!</h1>
            <p class="text-gray-600">Transaksi parkir telah selesai</p>
        </div>

        <!-- Detail Pembayaran -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Kiri: Info Kendaraan & Parkir -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-bold text-gray-800 mb-3">Informasi Parkir</h3>
                <div class="space-y-2 text-sm">
                    <p>
                        <span class="text-gray-600">Plat Nomor:</span><br>
                        <span class="font-bold text-lg">{{ $transaksi->kendaraan->plat_nomor }}</span>
                    </p>
                    <p>
                        <span class="text-gray-600">Jenis Kendaraan:</span><br>
                        <span class="font-bold">{{ $transaksi->kendaraan->jenis_kendaraan }}</span>
                    </p>
                    <p>
                        <span class="text-gray-600">Area Parkir:</span><br>
                        <span class="font-bold">{{ $transaksi->area->nama_area }}</span>
                    </p>
                </div>
            </div>

            <!-- Kanan: Info Pembayaran -->
            <div class="bg-green-50 p-4 rounded-lg border-2 border-green-300">
                <h3 class="text-lg font-bold text-green-800 mb-3">Ringkasan Pembayaran</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-700">Durasi Parkir:</span>
                        <span class="font-bold">{{ $transaksi->durasi_jam }} jam</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-700">Tarif per Jam:</span>
                        <span class="font-bold">Rp {{ number_format($transaksi->tarif->tarif_perjam, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t-2 border-green-300 pt-3 mt-3 flex justify-between text-lg">
                        <span class="font-bold text-green-800">Total Dibayar:</span>
                        <span class="font-bold text-green-600">Rp
                            @if($transaksi->pembayaran)
                                {{ number_format($transaksi->pembayaran->nominal, 0, ',', '.') }}
                            @else
                                {{ number_format($transaksi->biaya_total ?? 0, 0, ',', '.') }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Waktu -->
        <div class="bg-blue-50 p-4 rounded-lg mb-8 border-l-4 border-blue-500">
            <h3 class="text-lg font-bold text-blue-900 mb-3">Detail Waktu & Transaksi</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-gray-600">Waktu Masuk</p>
                    <p class="font-bold">{{ $transaksi->waktu_masuk->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Waktu Keluar</p>
                    <p class="font-bold">{{ $transaksi->waktu_keluar->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                        <p class="text-gray-600">Waktu Pembayaran</p>
                        <p class="font-bold">
                            @if($transaksi->pembayaran && $transaksi->pembayaran->waktu_pembayaran)
                                {{ $transaksi->pembayaran->waktu_pembayaran->format('d/m/Y H:i') }}
                            @else
                                -
                            @endif
                        </p>
                </div>
                <div class="md:col-span-3">
                    <p class="text-gray-600">Metode Pembayaran</p>
                    <p class="font-bold">
                        @if($transaksi->pembayaran && $transaksi->pembayaran->metode === 'manual')
                            <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded">Manual - Petugas</span>
                        @elseif($transaksi->pembayaran && $transaksi->pembayaran->metode)
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded">QR Scan - Otomatis</span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded">Belum Dibayar</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Keterangan Pembayaran -->
        @if($transaksi->pembayaran && $transaksi->pembayaran->keterangan)
        <div class="bg-gray-100 p-4 rounded-lg mb-8">
            <p class="text-sm text-gray-600">Keterangan:</p>
            <p class="text-gray-800">{{ $transaksi->pembayaran->keterangan }}</p>
        </div>
        @endif

        <!-- Tombol Aksi -->
        <div class="flex flex-col md:flex-row gap-3">
            <a href="{{ route('transaksi.print', $transaksi->id_parkir) }}"
               class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold text-center">
                Cetak Struk & Bukti Pembayaran
            </a>
            <a href="{{ route('transaksi.index', ['status' => 'masuk']) }}"
               class="flex-1 px-4 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-semibold text-center">
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
