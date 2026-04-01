@extends('layouts.app')

@section('title', 'Bayar dengan Midtrans')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-10" style="background:#020617;">
<div class="w-full max-w-md">

    {{--
        VARIANT 1: Dark centered — satu card terpusat dengan loading state animasi.
        Snap Midtrans tetap berjalan di background, UI terasa premium saat menunggu.
    --}}
    <div class="rounded-2xl border overflow-hidden" style="background:#0d1526;border-color:rgba(255,255,255,0.07);">

        {{-- Accent bar --}}
        <div class="h-1" style="background:linear-gradient(90deg,#10b981,#6366f1);"></div>

        <div class="p-8">
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-5" style="background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.2);">
                    <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h1 class="text-lg font-bold text-white">Pembayaran Parkir</h1>
                <p class="text-slate-500 text-sm mt-1">
                    {{ $transaksi->kendaraan->plat_nomor }}
                    <span class="text-slate-600">·</span>
                    <span class="text-emerald-400 font-semibold">Rp {{ number_format($transaksi->biaya_total, 0, ',', '.') }}</span>
                </p>
            </div>

            {{-- Snap container --}}
            <div id="snap-container">
                <div id="loading-state" class="flex flex-col items-center gap-5 py-4">
                    {{-- Spinner --}}
                    <div class="relative w-14 h-14">
                        <svg class="w-14 h-14 animate-spin text-emerald-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-10" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                            <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-semibold text-white">Memuat halaman pembayaran...</p>
                        <p class="text-xs text-slate-500 mt-1">Mohon tunggu, jangan tutup halaman ini</p>
                    </div>

                    {{-- Progress steps --}}
                    <div class="w-full flex items-center gap-2 mt-2">
                        <div class="flex-1 h-0.5 rounded-full bg-emerald-500"></div>
                        <div class="flex-1 h-0.5 rounded-full" style="background:rgba(255,255,255,0.08);" id="step2"></div>
                        <div class="flex-1 h-0.5 rounded-full" style="background:rgba(255,255,255,0.08);" id="step3"></div>
                    </div>
                    <div class="flex justify-between w-full">
                        <span class="text-[9px] font-bold text-emerald-400 uppercase tracking-widest">Koneksi</span>
                        <span class="text-[9px] font-bold text-slate-600 uppercase tracking-widest" id="step2-label">Token</span>
                        <span class="text-[9px] font-bold text-slate-600 uppercase tracking-widest" id="step3-label">Gateway</span>
                    </div>
                </div>
            </div>

            {{-- Midtrans config error --}}
            @if(empty($clientKey))
            <div class="mt-4 p-4 rounded-xl border text-sm" style="background:rgba(239,68,68,0.08);border-color:rgba(239,68,68,0.2);">
                <p class="font-bold text-red-400 mb-1">Konfigurasi Midtrans belum lengkap</p>
                <p class="text-red-400/70 text-xs">Isi <code class="bg-red-500/10 px-1 rounded">MIDTRANS_CLIENT_KEY</code> di file .env dan jalankan <code class="bg-red-500/10 px-1 rounded">php artisan config:clear</code>.</p>
            </div>
            @endif

            @isset($cancelUrl)
            <div class="mt-6 text-center">
                <a href="{{ $cancelUrl }}" class="text-xs text-slate-600 hover:text-slate-400 transition-colors">
                    ← Kembali ke pilihan pembayaran
                </a>
            </div>
            @endisset
        </div>
    </div>

    {{-- Trust note --}}
    <div class="flex items-center justify-center gap-2 mt-5">
        <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        <span class="text-[10px] text-slate-600">Diproses aman oleh Midtrans · SSL Encrypted</span>
    </div>
</div>
</div>

@php
    $snapJs = $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp
@if(!empty($clientKey))
<script type="text/javascript" src="{{ $snapJs }}" data-client-key="{{ $clientKey }}"></script>
@endif

<script>
(function() {
    var tokenUrl  = '{{ route("payment.midtrans.token", ["id_parkir" => $transaksi->id_parkir]) }}';
    var successUrl= '{{ route("payment.success", $transaksi->id_parkir) }}';
    var cancelUrl = @json($cancelUrl ?? route('payment.create', $transaksi->id_parkir));

    // Animate step indicators
    setTimeout(function() {
        var s2 = document.getElementById('step2');
        var s2l = document.getElementById('step2-label');
        if (s2) { s2.style.background = '#10b981'; s2l.style.color = '#34d399'; }
    }, 600);

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
        var s3 = document.getElementById('step3');
        var s3l = document.getElementById('step3-label');
        if (s3) { s3.style.background = '#10b981'; s3l.style.color = '#34d399'; }

        document.getElementById('loading-state').style.display = 'none';

        if (data.error) {
            document.getElementById('snap-container').innerHTML =
                '<div class="text-center py-4"><p class="text-red-400 text-sm mb-4">' + (data.error || 'Gagal memuat pembayaran') + '</p>' +
                '<a href="' + cancelUrl + '" class="text-xs text-slate-500 hover:text-white transition-colors">← Kembali</a></div>';
            return;
        }
        if (!data.snap_token) {
            document.getElementById('snap-container').innerHTML =
                '<div class="text-center py-4 text-red-400 text-sm">Token tidak diterima dari server.</div>';
            return;
        }
        window.snap.pay(data.snap_token, {
            onSuccess: function() { window.location.href = successUrl; },
            onPending:  function() { window.location.href = successUrl + '?pending=1'; },
            onError:    function() { window.location.href = cancelUrl + '?error=payment_cancelled'; },
            onClose:    function() { window.location.href = cancelUrl; }
        });
    })
    .catch(function() {
        var ls = document.getElementById('loading-state');
        if (ls) ls.innerHTML = '<p class="text-red-400 text-sm text-center">Gagal memuat. <a href="' + cancelUrl + '" class="text-emerald-400 underline">Kembali</a></p>';
    });
})();
</script>
@endsection
