@extends('layouts.app')

@section('title', 'Bayar dengan Midtrans')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sm:p-8">
        <div class="text-center mb-6">
            <h1 class="text-xl font-bold text-gray-800">Pembayaran Parkir</h1>
            <p class="text-gray-500 text-sm mt-1">Plat {{ $transaksi->kendaraan->plat_nomor }} &middot; Rp {{ number_format($transaksi->biaya_total, 0, ',', '.') }}</p>
        </div>

        <div id="snap-container" class="min-h-[400px] flex items-center justify-center">
            <div class="text-center text-gray-500" id="loading-state">
                <svg class="animate-spin h-10 w-10 text-green-600 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p>Memuat halaman pembayaran...</p>
            </div>
        </div>

        @isset($cancelUrl)
            <div class="mt-6 text-center">
                <a href="{{ $cancelUrl }}" class="text-sm text-gray-500 hover:text-gray-700">
                    &larr; Kembali
                </a>
            </div>
        @endisset
    </div>
</div>

@php
    $snapJs = $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp
@if(!empty($clientKey))
<script type="text/javascript" src="{{ $snapJs }}" data-client-key="{{ $clientKey }}"></script>
@else
<div class="max-w-2xl mx-auto mt-4">
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm">
        <p class="font-semibold mb-1">Konfigurasi Midtrans belum lengkap</p>
        <p>Isi MIDTRANS_CLIENT_KEY di file .env dan pastikan sesuai lingkungan (Sandbox/Production). Setelah itu jalankan: <span class="font-mono">php artisan config:clear</span> dan uji dengan <span class="font-mono">php artisan midtrans:check</span>.</p>
    </div>
</div>
@endif
<script>
(function() {
    var idParkir = {{ $transaksi->id_parkir }};
    var tokenUrl = '{{ route("payment.midtrans.token", ["id_parkir" => $transaksi->id_parkir]) }}';
    var successUrl = '{{ route("payment.success", $transaksi->id_parkir) }}';
    var cancelUrl = @json($cancelUrl ?? route('payment.create', $transaksi->id_parkir));

    fetch(tokenUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({})
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        document.getElementById('loading-state').style.display = 'none';
        if (data.error) {
            document.getElementById('snap-container').innerHTML = '<div class="text-center"><p class="text-red-600 mb-4">' + (data.error || 'Gagal memuat pembayaran') + '</p><a href="' + cancelUrl + '" class="text-green-600 font-medium">Kembali</a></div>';
            return;
        }
        if (!data.snap_token) {
            document.getElementById('snap-container').innerHTML = '<div class="text-center text-red-600">Token tidak diterima.</div>';
            return;
        }
        window.snap.pay(data.snap_token, {
            onSuccess: function() {
                window.location.href = successUrl;
            },
            onPending: function() {
                window.location.href = successUrl + '?pending=1';
            },
            onError: function() {
                window.location.href = cancelUrl + '?error=payment_cancelled';
            },
            onClose: function() {
                window.location.href = cancelUrl;
            }
        });
    })
    .catch(function(err) {
        document.getElementById('loading-state').innerHTML = '<p class="text-red-600">Gagal memuat. <a href="' + cancelUrl + '" class="text-green-600 underline">Kembali</a></p>';
    });
})();
</script>
@endsection
