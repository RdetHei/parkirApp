@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" style="background:#020617;min-height:100vh;">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Transaksi Aktif</h1>
            <p class="text-slate-500 text-sm mt-0.5">Kendaraan yang sedang parkir dan menunggu pembayaran.</p>
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg border" style="background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.07);">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $transaksis->count() }} aktif</span>
        </div>
    </div>

    {{--
        VARIANT 1: Card grid per transaksi
        Setiap transaksi = 1 card dengan info lengkap + tombol bayar yang mencolok.
        Cocok untuk operator yang butuh visual cepat per kendaraan.
    --}}

    @if($transaksis->isEmpty())
    <div class="flex flex-col items-center justify-center py-32 text-center rounded-2xl border" style="background:rgba(255,255,255,0.02);border-color:rgba(255,255,255,0.06);">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);">
            <svg class="w-7 h-7 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
        </div>
        <p class="text-white font-semibold mb-1">Tidak Ada Transaksi Aktif</p>
        <p class="text-sm text-slate-600">Belum ada transaksi parkir yang perlu dibayar.</p>
    </div>

    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($transaksis as $transaksi)
        @php
            $masuk = \Carbon\Carbon::parse($transaksi->waktu_masuk);
            $durasi = $masuk->diffForHumans(null, true);
            $jenis = $transaksi->tarif->jenis_kendaraan ?? 'lainnya';
            $jenisCls = match($jenis) {
                'motor' => 'bg-blue-500/10 border-blue-500/20 text-blue-400',
                'mobil' => 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400',
                default => 'bg-purple-500/10 border-purple-500/20 text-purple-400',
            };
            $jenisLabel = match($jenis) { 'motor' => 'Motor', 'mobil' => 'Mobil', default => 'Lainnya' };
        @endphp

        <div class="rounded-2xl overflow-hidden border flex flex-col" style="background:#0d1526;border-color:rgba(255,255,255,0.07);" id="row-{{ $transaksi->id_parkir }}" data-transaksi-id="{{ $transaksi->id_parkir }}">

            {{-- Card top --}}
            <div class="px-5 pt-5 pb-4 border-b flex items-start justify-between" style="border-color:rgba(255,255,255,0.05);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                    </div>
                    <div>
                        <p class="text-base font-bold text-white tracking-tight">{{ $transaksi->kendaraan->plat_nomor }}</p>
                        <p class="text-[9px] font-mono text-emerald-500/60">#{{ $transaksi->id_parkir }}</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full border text-[9px] font-bold uppercase tracking-widest {{ $jenisCls }}">{{ $jenisLabel }}</span>
            </div>

            {{-- Details --}}
            <div class="px-5 py-4 flex flex-col gap-3 flex-1">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Masuk</span>
                    <div class="text-right">
                        <p class="text-xs font-bold text-white">{{ $masuk->format('d M Y') }}</p>
                        <p class="text-[10px] text-slate-500 font-mono">{{ $masuk->format('H:i') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Durasi</span>
                    <span class="text-xs font-bold text-amber-400">{{ $durasi }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Area</span>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-xs font-bold text-white">{{ $transaksi->area->nama_area }}</span>
                    </div>
                </div>
            </div>

            {{-- Action --}}
            <div class="px-5 pb-5">
                <a href="{{ route('payment.create', $transaksi->id_parkir) }}"
                   class="w-full py-2.5 flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-emerald-900/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Proses Pembayaran
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<script>
    function refreshTransactionList() {
        fetch('{{ route('payment.select-transaction') }}', {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.text())
        .then(html => {
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(html, 'text/html');
            const newRows = newDoc.querySelectorAll('[data-transaksi-id]');
            const currentRows = document.querySelectorAll('[data-transaksi-id]');
            if (newRows.length < currentRows.length) location.reload();
        })
        .catch(e => console.log('Auto-refresh error (non-critical):', e));
    }
    setInterval(refreshTransactionList, 2000);
</script>
@endsection
