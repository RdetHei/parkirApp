@extends('layouts.app')

@section('title', 'Pembayaran Manual')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Pembayaran Manual - Verifikasi Petugas</h2>

    <div class="bg-white p-6 rounded-lg shadow-lg">
        <!-- Info Transaksi -->
        <div class="mb-6 pb-6 border-b-2">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Detail Kendaraan & Transaksi</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 p-3 rounded">
                    <p class="text-xs text-gray-600 uppercase">Plat Nomor</p>
                    <p class="text-lg font-bold text-gray-800">{{ $transaksi->kendaraan->plat_nomor }}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <p class="text-xs text-gray-600 uppercase">Jenis</p>
                    <p class="text-lg font-bold text-gray-800">{{ $transaksi->kendaraan->jenis_kendaraan }}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <p class="text-xs text-gray-600 uppercase">Durasi</p>
                    <p class="text-lg font-bold text-gray-800">{{ $transaksi->durasi_jam }} jam</p>
                </div>
                <div class="bg-green-50 p-3 rounded border-2 border-green-300">
                    <p class="text-xs text-green-700 uppercase font-bold">Total Bayar</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($transaksi->biaya_total, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Form Pembayaran -->
        <form action="{{ route('payment.manual-process', $transaksi->id_parkir) }}" method="POST">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nominal Pembayaran <span class="text-red-600">*</span></label>
                <div class="relative">
                    <span class="absolute left-4 top-3 text-gray-500 font-bold text-lg">Rp</span>
                    <input type="number" name="nominal" required 
                           value="{{ old('nominal', $transaksi->biaya_total) }}"
                           class="w-full px-4 py-3 pl-12 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-500 text-right text-lg font-semibold"
                           placeholder="0">
                </div>
                @error('nominal')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                <p class="text-xs text-gray-500 mt-1">💡 Dapat diubah jika ada diskon atau kebutuhan khusus</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan / Catatan</label>
                <textarea name="keterangan" rows="3" 
                          class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-500"
                          placeholder="Cth: Diskon khusus, pembayaran tunai, dsb...">{{ old('keterangan') }}</textarea>
                @error('keterangan')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <!-- Info Petugas -->
            <div class="bg-blue-50 border border-blue-300 p-4 rounded mb-6">
                <p class="text-sm text-blue-900">
                    <span class="font-bold">Petugas:</span> {{ auth()->user()->name }} 
                    <br>
                    <span class="font-bold">Waktu:</span> {{ now()->format('d/m/Y H:i:s') }}
                </p>
            </div>

            <!-- Tombol -->
            <div class="flex gap-3">
                <a href="{{ route('payment.create', $transaksi->id_parkir) }}" 
                   class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold text-center">
                    ← Kembali
                </a>
                <button type="submit" 
                        class="flex-1 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold"
                        onclick="return confirm('Konfirmasi pembayaran sebesar Rp ' + document.querySelector('input[name=nominal]').value + '?')">
                    ✓ Konfirmasi Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
