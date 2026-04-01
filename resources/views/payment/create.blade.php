@extends('layouts.app')

@section('title', 'Pilih Metode Pembayaran')

@section('content')
<div class="min-h-screen flex items-start justify-center py-10 px-4" style="background:#020617;">
<div class="w-full max-w-3xl">

    {{-- Back --}}
    <a href="{{ route('transaksi.index', ['status' => 'masuk']) }}"
       class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-white transition-colors mb-8">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Dashboard
    </a>

    {{-- Page title --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white tracking-tight">Pilih Metode Pembayaran</h1>
        <p class="text-slate-500 text-sm mt-0.5">Selesaikan transaksi parkir dengan metode pembayaran pilihan Anda.</p>
    </div>

    {{--
        VARIANT 1: Summary strip di atas, dua card metode berdampingan di bawah.
        Lebih "checkout page" — ringkasan kecil, fokus utama di pilihan metode.
    --}}

    {{-- Transaction summary strip --}}
    <div class="rounded-2xl border mb-6 overflow-hidden" style="background:#0d1526;border-color:rgba(255,255,255,0.07);">
        <div class="px-5 py-3 border-b flex items-center gap-2" style="border-color:rgba(255,255,255,0.05);">
            <span class="w-1.5 h-4 bg-blue-400 rounded-full"></span>
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Ringkasan Transaksi</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 divide-x" style="divide-color:rgba(255,255,255,0.05);">
            <div class="px-5 py-4">
                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Plat Nomor</p>
                <p class="text-lg font-bold text-white tracking-widest">{{ $transaksi->kendaraan->plat_nomor }}</p>
            </div>
            <div class="px-5 py-4">
                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Durasi</p>
                <p class="text-lg font-bold text-white">{{ $transaksi->durasi_jam }} <span class="text-sm font-medium text-slate-400">jam</span></p>
            </div>
            <div class="px-5 py-4">
                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Tarif / Jam</p>
                <p class="text-base font-bold text-white">Rp {{ number_format($transaksi->tarif->tarif_perjam, 0, ',', '.') }}</p>
            </div>
            <div class="px-5 py-4" style="background:rgba(16,185,129,0.04);">
                <p class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest mb-1.5">Total Bayar</p>
                <p class="text-xl font-bold text-emerald-400">Rp {{ number_format($transaksi->biaya_total, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- Payment method cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- NestonPay --}}
        <div class="rounded-2xl border flex flex-col overflow-hidden cursor-pointer group transition-all hover:border-indigo-500/50"
             style="background:#0d1526;border-color:rgba(255,255,255,0.07);"
             onclick="document.getElementById('form-nestonpay').submit()">
            <form id="form-nestonpay" action="{{ route('user.saldo.pay', $transaksi->id_parkir) }}" method="POST" class="hidden">@csrf</form>

            {{-- Card header accent --}}
            <div class="h-1 w-full" style="background:linear-gradient(90deg,#6366f1,#818cf8);"></div>

            <div class="p-6 flex flex-col flex-1 gap-4">
                {{-- Icon + badge --}}
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(99,102,241,0.15);border:1px solid rgba(99,102,241,0.25);">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-widest" style="background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.2);color:#818cf8;">Instant</span>
                </div>

                <div>
                    <h3 class="text-base font-bold text-white mb-1">NestonPay Wallet</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Bayar instan menggunakan saldo dompet digital Anda.</p>
                </div>

                {{-- Saldo display --}}
                <div class="rounded-xl px-4 py-3" style="background:rgba(99,102,241,0.06);border:1px solid rgba(99,102,241,0.12);">
                    <p class="text-[9px] font-bold text-indigo-400 uppercase tracking-widest mb-1">Saldo Anda</p>
                    <p class="text-base font-bold text-indigo-300">Rp {{ number_format(Auth::user()->saldo, 0, ',', '.') }}</p>
                </div>

                {{-- CTA --}}
                <div class="mt-auto">
                    @if(Auth::user()->saldo < $transaksi->biaya_total)
                        <div class="w-full py-3 rounded-xl text-center text-xs font-bold uppercase tracking-widest"
                             style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.2);color:#fbbf24;">
                            Saldo tidak cukup — Silakan Top Up
                        </div>
                    @else
                        <div class="w-full py-3 rounded-xl text-center text-xs font-bold uppercase tracking-widest bg-indigo-600 group-hover:bg-indigo-500 text-white transition-colors">
                            Bayar Sekarang
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Midtrans --}}
        <div class="rounded-2xl border flex flex-col overflow-hidden cursor-pointer group transition-all hover:border-emerald-500/50"
             style="background:#0d1526;border-color:rgba(255,255,255,0.07);"
             onclick="document.location.href='{{ route('payment.midtrans', $transaksi->id_parkir) }}'">

            <div class="h-1 w-full" style="background:linear-gradient(90deg,#10b981,#34d399);"></div>

            <div class="p-6 flex flex-col flex-1 gap-4">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.25);">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-widest" style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);color:#34d399;">Online</span>
                </div>

                <div>
                    <h3 class="text-base font-bold text-white mb-1">Midtrans Online</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">GoPay, OVO, Dana, QRIS, dan Transfer Bank.</p>
                </div>

                {{-- Method list --}}
                <div class="flex flex-col gap-2">
                    @foreach(['GoPay & QRIS', 'Virtual Account (BCA, BRI, dll)', 'Kartu Kredit / Debit'] as $item)
                    <div class="flex items-center gap-2.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                        <span class="text-xs text-slate-400">{{ $item }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="mt-auto">
                    <div class="w-full py-3 rounded-xl text-center text-xs font-bold uppercase tracking-widest bg-emerald-600 group-hover:bg-emerald-500 text-white transition-colors">
                        Pilih Metode Online
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
</div>
@endsection
