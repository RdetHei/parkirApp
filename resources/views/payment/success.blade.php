@extends('layouts.app')

@section('title', 'Pembayaran Berhasil')

@section('content')
<div class="p-8 relative z-10">
    <div class="max-w-4xl mx-auto">
        <!-- Success Header -->
        <div class="flex flex-col items-center text-center mb-12 animate-fade-in-up">
            <div class="relative mb-6">
                <div class="absolute inset-0 bg-emerald-500/20 blur-2xl rounded-full scale-150 animate-pulse"></div>
                <div class="relative w-24 h-24 bg-emerald-500 rounded-3xl flex items-center justify-center shadow-[0_0_30px_rgba(16,185,129,0.4)] rotate-3">
                    <svg class="w-12 h-12 text-slate-950 -rotate-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            <div class="space-y-2">
                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-[0.2em] rounded-full border border-emerald-500/20">
                    Payment Success
                </span>
                <h1 class="text-4xl font-black text-white tracking-tight">Pembayaran <span class="text-emerald-500">Berhasil!</span></h1>
                <p class="text-slate-400 text-sm">Transaksi parkir Anda telah selesai diproses dengan aman.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Left: Parking Info -->
            <div class="card-pro group overflow-hidden relative border-emerald-500/10 animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-all"></div>

                <div class="flex items-center gap-4 mb-6 relative z-10">
                    <div class="p-2.5 bg-emerald-500/10 rounded-xl border border-emerald-500/20 text-emerald-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-black text-white uppercase tracking-widest">Informasi Parkir</h3>
                </div>

                <div class="space-y-5 relative z-10">
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Plat Nomor</p>
                        <p class="text-2xl font-black text-white tracking-tight uppercase">{{ $transaksi->kendaraan->plat_nomor }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Jenis Kendaraan</p>
                            <p class="text-sm font-bold text-slate-200">{{ $transaksi->kendaraan->jenis_kendaraan }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Area Parkir</p>
                            <p class="text-sm font-bold text-slate-200">{{ $transaksi->area->nama_area }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Payment Summary -->
            <div class="card-pro group overflow-hidden relative border-emerald-500/20 animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl"></div>

                <div class="flex items-center gap-4 mb-6 relative z-10">
                    <div class="p-2.5 bg-emerald-500 text-slate-950 rounded-xl shadow-[0_0_15px_rgba(16,185,129,0.3)]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-black text-white uppercase tracking-widest">Ringkasan Pembayaran</h3>
                </div>

                <div class="space-y-4 relative z-10">
                    <div class="flex justify-between items-center py-2 border-b border-white/5">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Durasi Parkir</span>
                        <span class="text-sm font-black text-white">{{ $transaksi->durasi_jam }} jam</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-white/5">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Tarif per Jam</span>
                        <span class="text-sm font-black text-white">Rp {{ number_format($transaksi->tarif->tarif_perjam, 0, ',', '.') }}</span>
                    </div>
                    <div class="pt-4 flex justify-between items-end">
                        <div>
                            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.2em] mb-1">Total Dibayar</p>
                            <h2 class="text-4xl font-black text-white tracking-tighter">
                                <span class="text-emerald-500 text-xl font-medium mr-1">Rp</span>@if($transaksi->pembayaran){{ number_format($transaksi->pembayaran->nominal, 0, ',', '.') }}@else{{ number_format($transaksi->biaya_total ?? 0, 0, ',', '.') }}@endif
                            </h2>
                        </div>
                        <div class="px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-lg">
                            <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Lunas</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Time & Transaction Details -->
        <div class="card-pro overflow-hidden mb-12 animate-fade-in-up" style="animation-delay: 0.3s">
            <div class="px-8 py-5 border-b border-white/5 bg-white/[0.02]">
                <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Detail Waktu & Transaksi</h3>
            </div>
            <div class="p-8 grid grid-cols-2 md:grid-cols-4 gap-8">
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Waktu Masuk</p>
                    <p class="text-sm font-bold text-slate-200">{{ $transaksi->waktu_masuk->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Waktu Keluar</p>
                    <p class="text-sm font-bold text-slate-200">{{ $transaksi->waktu_keluar->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Waktu Pembayaran</p>
                    <p class="text-sm font-bold text-slate-200">
                        @if($transaksi->pembayaran && $transaksi->pembayaran->waktu_pembayaran)
                            {{ $transaksi->pembayaran->waktu_pembayaran->format('d/m/Y H:i') }}
                        @else
                            -
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Metode</p>
                    <div class="mt-1">
                        @if($transaksi->pembayaran && $transaksi->pembayaran->metode === 'midtrans')
                            <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-500 text-[9px] font-black uppercase rounded-lg border border-emerald-500/20 tracking-widest">
                                Midtrans Online
                            </span>
                        @else
                            <span class="px-2.5 py-1 bg-slate-800 text-slate-400 text-[9px] font-black uppercase rounded-lg border border-white/5 tracking-widest">
                                Tunai / Manual
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            @if($transaksi->pembayaran && $transaksi->pembayaran->keterangan)
            <div class="px-8 py-4 bg-white/[0.01] border-t border-white/5">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Keterangan</p>
                <p class="text-sm text-slate-400 italic">"{{ $transaksi->pembayaran->keterangan }}"</p>
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col md:flex-row items-center justify-center gap-4 animate-fade-in-up" style="animation-delay: 0.4s">
            @php
                $role = auth()->user()->role ?? null;
            @endphp

            @if(in_array($role, ['admin', 'petugas']))
                <a href="{{ route('transaksi.print', $transaksi->id_parkir) }}"
                   class="w-full md:w-auto px-8 py-4 bg-emerald-500 text-slate-950 font-black text-[11px] uppercase tracking-[0.2em] rounded-2xl transition-all hover:bg-emerald-400 hover:shadow-[0_0_20px_rgba(16,185,129,0.4)] flex items-center justify-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Cetak Struk & Bukti
                </a>
                <a href="{{ route('transaksi.index', ['status' => 'masuk']) }}"
                   class="w-full md:w-auto px-8 py-4 bg-white/[0.03] hover:bg-white/[0.08] text-white font-black text-[11px] uppercase tracking-[0.2em] rounded-2xl transition-all border border-white/10 flex items-center justify-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Dashboard
                </a>
            @else
                <button type="button"
                        onclick="window.print()"
                        class="w-full md:w-auto px-8 py-4 bg-emerald-500 text-slate-950 font-black text-[11px] uppercase tracking-[0.2em] rounded-2xl transition-all hover:bg-emerald-400 hover:shadow-[0_0_20px_rgba(16,185,129,0.4)] flex items-center justify-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Cetak / Simpan Struk
                </button>
                <a href="{{ route('user.bills') }}"
                   class="w-full md:w-auto px-8 py-4 bg-white/[0.03] hover:bg-white/[0.08] text-white font-black text-[11px] uppercase tracking-[0.2em] rounded-2xl transition-all border border-white/10 flex items-center justify-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Kembali ke Tagihan Saya
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
