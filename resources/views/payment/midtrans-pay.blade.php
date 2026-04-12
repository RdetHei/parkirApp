@extends('layouts.app')

@section('title', 'Bayar dengan Midtrans - NESTON')

@section('content')
<div class="relative z-10 min-h-[calc(100vh-4rem)] flex items-center justify-center p-4 sm:p-6 lg:p-8">
    {{-- Ambient bg --}}
    <div class="fixed top-0 left-1/4 w-[600px] h-[400px] bg-emerald-500/5 rounded-full blur-[160px] pointer-events-none z-0"></div>
    <div class="fixed bottom-0 right-0 w-[400px] h-[400px] bg-blue-600/5 rounded-full blur-[140px] pointer-events-none z-0"></div>

    <div class="w-full max-w-xl animate-fade-in relative z-10">
        <div class="card-pro overflow-hidden border-white/5 backdrop-blur-xl bg-slate-900/40">
            {{-- Header --}}
            <div class="px-6 py-8 sm:px-10 border-b border-white/5 bg-white/[0.02] text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border mb-4"
                     style="background:rgba(16,185,129,0.08);border-color:rgba(16,185,129,0.18);">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[9px] font-black text-emerald-500 uppercase tracking-[0.25em]">Secure Payment</span>
                </div>
                <h1 class="text-2xl font-black text-white uppercase tracking-tight">Pembayaran <span class="text-emerald-500">Parkir</span></h1>
                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mt-2">Selesaikan transaksi Anda melalui Midtrans</p>
            </div>

            {{-- Details --}}
            <div class="p-6 sm:p-10">
                <div class="bg-slate-950/50 rounded-3xl border border-white/5 p-6 mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-slate-900 border border-white/10 flex items-center justify-center text-emerald-500">
                                <i class="fa-solid fa-car text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-0.5">Plat Nomor</p>
                                <p class="text-lg font-black text-white tracking-tight">{{ $transaksi->kendaraan->plat_nomor }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-0.5">Total Bayar</p>
                            <p class="text-2xl font-black text-emerald-500 tracking-tighter">Rp {{ number_format($transaksi->biaya_total, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-6 border-t border-white/5">
                        <div>
                            <p class="text-[8px] font-black text-slate-600 uppercase tracking-widest mb-1">Waktu Masuk</p>
                            <p class="text-[10px] font-bold text-slate-300 uppercase">{{ \Carbon\Carbon::parse($transaksi->waktu_masuk)->format('H:i · d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[8px] font-black text-slate-600 uppercase tracking-widest mb-1">Durasi</p>
                            <p class="text-[10px] font-bold text-slate-300 uppercase">{{ $transaksi->durasi_jam }} JAM</p>
                        </div>
                    </div>
                </div>

                {{-- Snap Container --}}
                <div id="snap-container" class="min-h-[300px] flex items-center justify-center rounded-3xl bg-slate-950/30 border border-dashed border-white/10">
                    <div class="text-center" id="loading-state">
                        <div class="w-10 h-10 border-4 border-emerald-500/15 border-t-emerald-500 rounded-full animate-spin mx-auto mb-4"></div>
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Menyiapkan Gerbang Pembayaran...</p>
                    </div>
                </div>

                @isset($cancelUrl)
                    <div class="mt-8 text-center">
                        <a href="{{ $cancelUrl }}" class="inline-flex items-center gap-2 text-[10px] font-black text-slate-500 hover:text-white uppercase tracking-widest transition-all">
                            <i class="fa-solid fa-arrow-left"></i>
                            Batalkan Pembayaran
                        </a>
                    </div>
                @endisset
            </div>
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
