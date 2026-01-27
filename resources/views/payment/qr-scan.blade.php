@extends('layouts.app')

@section('title', 'Pembayaran QR Scan')

@section('content')
<div class="max-w-md mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold mb-6 text-gray-800 text-center">Scan QR untuk Bayar</h2>

    <div class="bg-white p-6 rounded-lg shadow-lg">
        <!-- Info Singkat -->
        <div class="mb-6 pb-6 border-b-2">
            <p class="text-sm text-gray-600 mb-2"><strong>Plat Nomor:</strong> {{ $transaksi->kendaraan->plat_nomor }}</p>
            <p class="text-sm text-gray-600 mb-4"><strong>Total Bayar:</strong></p>
            <p class="text-3xl font-bold text-green-600 text-center">Rp {{ number_format($transaksi->biaya_total, 0, ',', '.') }}</p>
        </div>

        <!-- QR Code Scanner -->
        <div class="mb-6">
            <p class="text-center text-gray-700 mb-4 font-semibold">Gunakan aplikasi pembayaran untuk scan QR di bawah ini</p>
            
            <!-- Generate QR Code (signed, temporary URL) -->
            <div id="qrcode" class="flex justify-center">
                 <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($signedUrl) }}" 
                     alt="QR Code" class="border-4 border-gray-300 rounded-lg">
            </div>
            <p class="text-xs text-gray-500 text-center mt-2">QR valid sampai {{ now()->addMinutes(15)->format('H:i') }} (atau sesuai waktu server)</p>
        </div>

        <!-- Status -->
        <div id="payment-status" class="mb-6 hidden">
            <div class="bg-green-50 border-2 border-green-300 p-4 rounded-lg text-center">
                <p class="text-green-700 font-bold">Pembayaran Berhasil!</p>
                <p class="text-green-600 text-sm mt-2" id="status-message"></p>
            </div>
        </div>

        <!-- Info -->
        <div class="bg-blue-50 border border-blue-300 p-4 rounded mb-6">
            <p class="text-sm text-blue-900">
                <strong>📱 Petunjuk:</strong><br>
                1. Buka aplikasi e-wallet atau pembayaran<br>
                2. Pilih scan QR<br>
                3. Arahkan kamera ke QR di atas<br>
                4. Selesaikan pembayaran
            </p>
        </div>

        <!-- Polling untuk Konfirmasi -->
        <script>
            let checkCount = 0;
            function checkPaymentStatus() {
                fetch('{{ route('payment.confirm-qr', $transaksi->id_parkir) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('payment-status').classList.remove('hidden');
                        document.getElementById('status-message').textContent = 'Pembayaran Rp ' + 
                            new Intl.NumberFormat('id-ID').format({{ $transaksi->biaya_total }}) + ' telah diterima';
                        
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 2000);
                    }
                })
                .catch(error => console.error('Error:', error));
            }

            // Poll setiap 3 detik
            setInterval(checkPaymentStatus, 3000);
        </script>

        <!-- Tombol Kembali -->
        <div class="flex gap-3">
            <a href="{{ route('payment.create', $transaksi->id_parkir) }}" 
               class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold text-center">
                Ganti Metode
            </a>
        </div>
    </div>

    <!-- Info Manual Checkout -->
    <div class="mt-8 bg-yellow-50 border-2 border-yellow-300 p-4 rounded-lg">
        <p class="text-sm text-yellow-900 font-semibold mb-2">⚠️ Belum mendapat akses QR?</p>
        <p class="text-sm text-yellow-800 mb-3">Gunakan pembayaran manual melalui petugas parkir</p>
        <a href="{{ route('payment.manual-confirm', $transaksi->id_parkir) }}" 
           class="block px-4 py-2 bg-yellow-600 text-white rounded text-center hover:bg-yellow-700 font-semibold">
            Pilih Pembayaran Manual
        </a>
    </div>
</div>
@endsection
