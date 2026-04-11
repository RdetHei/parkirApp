@extends('layouts.app')

@section('title', 'Pilih Metode Pembayaran')

@section('content')
@php
    $viewer = Auth::user();
    $isStaffCheckout = $viewer && in_array($viewer->role ?? '', ['admin', 'petugas'], true);
    $nestonSaldo = ($isStaffCheckout && $transaksi->user)
        ? (float) ($transaksi->user->balance ?? $transaksi->user->saldo ?? 0)
        : (float) ($viewer->balance ?? $viewer->saldo ?? 0);
@endphp
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

    @if(session('success'))
        <div class="mb-6 rounded-2xl border border-emerald-500/25 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-300">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-6 rounded-2xl border border-rose-500/25 bg-rose-500/10 px-4 py-3 text-sm font-medium text-rose-300">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="mb-6 rounded-2xl border border-amber-500/25 bg-amber-500/10 px-4 py-3 text-sm font-medium text-amber-200">{{ session('info') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-6 rounded-2xl border border-rose-500/25 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                    <p class="text-[9px] font-bold text-indigo-400 uppercase tracking-widest mb-1">{{ $isStaffCheckout && $transaksi->user ? 'Saldo pemilik transaksi' : 'Saldo Anda' }}</p>
                    <p class="text-base font-bold text-indigo-300">Rp {{ number_format($nestonSaldo, 0, ',', '.') }}</p>
                </div>

                {{-- CTA --}}
                <div class="mt-auto">
                    @if($nestonSaldo < $transaksi->biaya_total)
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

    @if(!empty($canProcessCash) && $canProcessCash)
        <div class="rounded-2xl border mt-8 overflow-hidden" style="background:#0d1526;border-color:rgba(255,255,255,0.07);">
            <div class="h-1 w-full" style="background:linear-gradient(90deg,#f59e0b,#fbbf24);"></div>
            <div class="p-6 space-y-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-white tracking-tight">Pembayaran tunai</h2>
                        <p class="text-xs text-slate-500 mt-1">Shift kas wajib terbuka. Konfirmasi setelah uang diterima di loket.</p>
                    </div>
                    @if($openKasShift)
                        <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest border border-amber-500/30 bg-amber-500/10 text-amber-400">
                            Shift #{{ $openKasShift->id_kas_shift }} aktif
                        </span>
                    @endif
                </div>

                @if(!$openKasShift)
                    <div class="rounded-xl border border-white/10 bg-slate-950/40 p-4 space-y-3">
                        <p class="text-sm text-slate-400">Belum ada shift kas terbuka untuk area transaksi ini. Buka shift sebelum menerima tunai.</p>
                        <form action="{{ route('kas.shift.open') }}" method="post" class="flex flex-wrap items-end gap-3">
                            @csrf
                            @if($transaksi->id_area)
                                <input type="hidden" name="id_area" value="{{ $transaksi->id_area }}">
                            @endif
                            <button type="submit"
                                    class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest bg-amber-500 text-slate-950 hover:bg-amber-400 transition-colors">
                                Buka shift kas
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex flex-wrap gap-2">
                        <form action="{{ route('kas.shift.close', $openKasShift->id_kas_shift) }}" method="post" class="inline"
                              onsubmit="return confirm('Tutup shift? Pembayaran tunai pada shift ini tidak bisa diubah lagi.');">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-xl text-[9px] font-bold uppercase tracking-widest border border-white/10 text-slate-400 hover:text-white hover:border-white/20 transition-colors">
                                Tutup shift
                            </button>
                        </form>
                        <a href="{{ route('kas.report.harian') }}" target="_blank" rel="noopener"
                           class="inline-flex items-center px-4 py-2 rounded-xl text-[9px] font-bold uppercase tracking-widest border border-white/10 text-slate-400 hover:text-white hover:border-white/20 transition-colors">
                            Rekap harian (JSON)
                        </a>
                    </div>

                    @if($pendingCashPembayaran)
                        <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-4 space-y-4">
                            <p class="text-xs text-amber-200/90 font-medium">Intent tunai aktif — masukkan uang yang diterima pelanggan.</p>
                            <dl class="grid grid-cols-2 gap-2 text-xs">
                                <dt class="text-slate-500">Total tagihan</dt>
                                <dd class="text-white font-bold text-right">Rp {{ number_format($transaksi->biaya_total, 0, ',', '.') }}</dd>
                                <dt class="text-slate-500">ID pembayaran</dt>
                                <dd class="text-slate-300 font-mono text-right">#{{ $pendingCashPembayaran->id_pembayaran }}</dd>
                            </dl>
                            <form action="{{ route('kas.cash.confirm') }}" method="post" class="flex flex-col sm:flex-row flex-wrap gap-3 items-stretch sm:items-end">
                                @csrf
                                <input type="hidden" name="id_pembayaran" value="{{ $pendingCashPembayaran->id_pembayaran }}">
                                <div class="flex-1 min-w-[160px]">
                                    <label for="cash_received" class="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Uang diterima (Rp)</label>
                                    <input type="number" name="cash_received" id="cash_received" min="0" step="1" required
                                           value="{{ (int) $transaksi->biaya_total }}"
                                           class="w-full px-4 py-3 rounded-xl bg-slate-950/80 border border-white/10 text-white text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500/50">
                                </div>
                                <button type="submit"
                                        class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest bg-amber-500 text-slate-950 hover:bg-amber-400 transition-colors">
                                    Konfirmasi lunas
                                </button>
                            </form>
                            <form action="{{ route('kas.cash.cancel', $pendingCashPembayaran->id_pembayaran) }}" method="post"
                                  onsubmit="return confirm('Batalkan intent tunai?');">
                                @csrf
                                <button type="submit" class="text-[10px] font-bold uppercase tracking-widest text-slate-500 hover:text-rose-400 transition-colors">
                                    Batal intent (ganti metode lain)
                                </button>
                            </form>
                        </div>
                    @else
                        <form action="{{ route('kas.cash.intent', $transaksi->id_parkir) }}" method="post">
                            @csrf
                            <button type="submit"
                                    class="w-full sm:w-auto px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest border border-amber-500/40 text-amber-400 hover:bg-amber-500/10 transition-colors">
                                Tandai bayar tunai (pending)
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    @endif

</div>
</div>
@endsection
