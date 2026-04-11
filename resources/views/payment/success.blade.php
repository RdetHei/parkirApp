@extends('layouts.app')

@section('title', 'Pembayaran Berhasil - NESTON')

@section('content')
<div class="p-8 relative z-10 animate-fade-in">
    <!-- Background Glows -->
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="max-w-4xl mx-auto relative z-10">
        @if(session('success'))
            <div class="mb-8 rounded-2xl border border-emerald-500/25 bg-emerald-500/10 px-4 py-3 text-center text-sm font-bold text-emerald-400">{{ session('success') }}</div>
        @endif
        @if(session('info'))
            <div class="mb-8 rounded-2xl border border-amber-500/25 bg-amber-500/10 px-4 py-3 text-center text-sm font-medium text-amber-200">{{ session('info') }}</div>
        @endif
        <!-- Success Header -->
        <div class="text-center mb-12">
            <div class="w-24 h-24 bg-emerald-500 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 shadow-2xl shadow-emerald-500/40 animate-bounce">
                <i class="fa-solid fa-check text-4xl text-slate-950"></i>
            </div>
            <h1 class="text-4xl font-black tracking-tight text-white uppercase mb-2">Pembayaran <span class="text-emerald-500">Berhasil!</span></h1>
            <p class="text-slate-500 text-sm font-medium">Transaksi parkir Anda telah diverifikasi dan selesai.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
            <!-- Left: Info Kendaraan -->
            <div class="card-pro group overflow-hidden relative border-white/5 backdrop-blur-xl bg-slate-900/40 p-10">
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-500/5 rounded-full blur-2xl group-hover:scale-110 transition-transform"></div>
                <div class="relative z-10">
                    <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em] mb-8 flex items-center gap-3">
                        <i class="fa-solid fa-car-side text-blue-400"></i>
                        Informasi Parkir
                    </h3>
                    <div class="space-y-6">
                        <div class="flex items-center gap-6">
                            <div class="w-16 h-16 rounded-[1.25rem] bg-slate-950 border border-white/10 flex flex-col items-center justify-center font-black text-white shadow-xl">
                                <span class="text-[9px] text-slate-600 leading-none mb-1.5 uppercase tracking-tighter">{{ substr($transaksi->kendaraan->plat_nomor ?? '-', 0, 2) }}</span>
                                <span class="text-xl leading-none tracking-tight">{{ substr($transaksi->kendaraan->plat_nomor ?? '-', 2, 4) }}</span>
                            </div>
                            <div>
                                <p class="text-xl font-black text-white tracking-tight">{{ $transaksi->kendaraan->plat_nomor }}</p>
                                <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest mt-1">{{ $transaksi->kendaraan->jenis_kendaraan }}</p>
                            </div>
                        </div>
                        <div class="pt-6 border-t border-white/5 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Area Parkir</span>
                                <span class="text-xs font-bold text-white uppercase tracking-widest">{{ $transaksi->area->nama_area }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Durasi</span>
                                <span class="text-xs font-bold text-white uppercase tracking-widest">{{ $transaksi->durasi_jam }} Jam</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Ringkasan Pembayaran -->
            <div class="card-pro group overflow-hidden relative border-emerald-500/10 backdrop-blur-xl bg-slate-900/40 p-10">
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/5 rounded-full blur-2xl group-hover:scale-110 transition-transform"></div>
                <div class="relative z-10">
                    <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em] mb-8 flex items-center gap-3">
                        <i class="fa-solid fa-receipt text-emerald-500"></i>
                        Ringkasan Pembayaran
                    </h3>
                    <div class="space-y-6">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500 font-bold uppercase tracking-widest text-[10px]">Tarif per Jam</span>
                            <span class="text-white font-black">Rp {{ number_format($transaksi->tarif->tarif_perjam, 0, ',', '.') }}</span>
                        </div>
                        <div class="pt-6 border-t border-white/5">
                            <div class="flex justify-between items-end">
                                <div>
                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Total Dibayar</p>
                                    <p class="text-[9px] text-emerald-500 font-black uppercase tracking-widest">Metode: Midtrans Online</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-3xl font-black text-emerald-500 tracking-tighter">
                                        Rp {{ number_format($transaksi->pembayaran->nominal ?? $transaksi->biaya_total ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 rounded-2xl bg-emerald-500/5 border border-emerald-500/10 text-center">
                            <span class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.2em]">Status: LUNAS & TERVERIFIKASI</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Waktu Card -->
        <div class="card-pro group overflow-hidden relative border-white/5 backdrop-blur-xl bg-slate-900/40 p-10 mb-12">
            <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em] mb-8 flex items-center gap-3">
                <i class="fa-solid fa-clock-rotate-left text-slate-500"></i>
                Detail Waktu & Transaksi
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-6 rounded-2xl bg-slate-950/50 border border-white/5">
                    <p class="text-[9px] text-slate-600 font-black uppercase tracking-widest mb-2">Waktu Masuk</p>
                    <p class="text-sm font-black text-white tracking-tight uppercase">{{ $transaksi->waktu_masuk->format('d M Y') }}</p>
                    <p class="text-lg font-black text-emerald-500 tracking-tighter">{{ $transaksi->waktu_masuk->format('H:i') }}</p>
                </div>
                <div class="p-6 rounded-2xl bg-slate-950/50 border border-white/5">
                    <p class="text-[9px] text-slate-600 font-black uppercase tracking-widest mb-2">Waktu Keluar</p>
                    <p class="text-sm font-black text-white tracking-tight uppercase">{{ $transaksi->waktu_keluar->format('d M Y') }}</p>
                    <p class="text-lg font-black text-blue-500 tracking-tighter">{{ $transaksi->waktu_keluar->format('H:i') }}</p>
                </div>
                <div class="p-6 rounded-2xl bg-slate-950/50 border border-white/5">
                    <p class="text-[9px] text-slate-600 font-black uppercase tracking-widest mb-2">Waktu Bayar</p>
                    <p class="text-sm font-black text-white tracking-tight uppercase">
                        {{ $transaksi->pembayaran && $transaksi->pembayaran->waktu_pembayaran ? $transaksi->pembayaran->waktu_pembayaran->format('d M Y') : '-' }}
                    </p>
                    <p class="text-lg font-black text-emerald-500 tracking-tighter">
                        {{ $transaksi->pembayaran && $transaksi->pembayaran->waktu_pembayaran ? $transaksi->pembayaran->waktu_pembayaran->format('H:i') : '-' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <div class="flex justify-center">
            <a href="{{ route('user.dashboard') }}"
               class="group relative px-12 py-5 bg-white/5 text-white text-[10px] font-black uppercase tracking-widest rounded-[2rem] hover:bg-emerald-500 hover:text-slate-950 hover:border-emerald-500 transition-all border border-white/10 active:scale-[0.98] flex items-center gap-4 shadow-2xl overflow-hidden">
                <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                <i class="fa-solid fa-house text-lg"></i>
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

<style>
    @keyframes shimmer {
        100% { transform: translateX(100%); }
    }
</style>
@endsection
