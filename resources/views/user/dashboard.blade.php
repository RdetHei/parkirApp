@extends('layouts.app')

@section('title', 'Dashboard - NESTON')

@section('content')
<div class="p-8 relative z-10 animate-fade-in">
    <!-- Background Glows (Consistent with Auth) -->
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="relative z-10">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                        Personal Hub
                    </span>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-white uppercase">Halo, <span class="text-emerald-500">{{ explode(' ', $user->name)[0] }}</span></h1>
                <p class="text-slate-400 text-sm mt-2 font-medium tracking-wide">Kelola kendaraan, saldo, dan aktivitas parkir Anda dalam satu dasbor.</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('user.saldo.topup') }}" class="group relative px-8 py-4 bg-emerald-500 text-slate-950 font-black text-xs uppercase tracking-widest rounded-2xl transition-all hover:bg-emerald-400 hover:shadow-[0_0_30px_rgba(16,185,129,0.3)] flex items-center gap-3 overflow-hidden active:scale-[0.98]">
                    <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                    <i class="fa-solid fa-plus text-sm"></i>
                    Top Up Saldo
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
            <!-- Wallet Balance (Large Glass Card) -->
            <div class="lg:col-span-2 card-pro group overflow-hidden relative border-emerald-500/10 backdrop-blur-xl bg-slate-900/40">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
                <div class="relative z-10 flex flex-col h-full justify-between min-h-[240px]">
                    <div>
                        <div class="flex items-center justify-between mb-10">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 bg-emerald-500/10 rounded-[1.25rem] border border-emerald-500/20 flex items-center justify-center shadow-xl shadow-emerald-500/5">
                                    <i class="fa-solid fa-wallet text-2xl text-emerald-500"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em]">NestonPay Wallet</p>
                                    <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest mt-1">Status: Active & Secure</p>
                                </div>
                            </div>
                            <div class="flex -space-x-3">
                                <div class="w-10 h-10 rounded-2xl border-2 border-slate-900 bg-emerald-500 flex items-center justify-center text-[11px] font-black text-slate-950 shadow-xl">N</div>
                                <div class="w-10 h-10 rounded-2xl border-2 border-slate-900 bg-slate-800 flex items-center justify-center text-[11px] font-black text-white shadow-xl">P</div>
                            </div>
                        </div>
                        <h2 class="text-7xl font-black tracking-tighter text-white flex items-baseline gap-4">
                            <span class="text-emerald-500 text-3xl font-medium tracking-normal">Rp</span>{{ number_format($user->saldo, 0, ',', '.') }}
                        </h2>
                    </div>
                    
                    <div class="flex gap-4 mt-12">
                        <a href="{{ route('user.saldo.index') }}" class="px-8 py-3.5 bg-white/5 hover:bg-emerald-500 hover:text-slate-950 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all border border-white/5 hover:border-emerald-500 active:scale-95 shadow-xl">
                            Riwayat Transaksi
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <!-- Active Parking Card -->
                <div class="card-pro group overflow-hidden relative border-amber-500/10 backdrop-blur-xl bg-slate-900/40">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-500/5 rounded-full blur-2xl group-hover:bg-amber-500/10 transition-all"></div>
                    <div class="flex items-center justify-between mb-6 relative z-10">
                        <div class="p-3 bg-amber-500/10 rounded-2xl border border-amber-500/20 text-amber-500 shadow-xl">
                            <i class="fa-solid fa-clock text-xl"></i>
                        </div>
                        <span class="px-3 py-1 bg-amber-500/10 text-amber-500 text-[9px] font-black uppercase rounded-xl border border-amber-500/20 tracking-widest">In Progress</span>
                    </div>
                    <p class="text-5xl font-black text-white tracking-tighter relative z-10">{{ $transaksiAktif }}</p>
                    <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest mt-1 relative z-10">Sesi Parkir Aktif</p>
                </div>

                <!-- Unpaid Bills Card -->
                <div class="card-pro group overflow-hidden relative border-rose-500/10 backdrop-blur-xl bg-slate-900/40">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-rose-500/5 rounded-full blur-2xl group-hover:bg-rose-500/10 transition-all"></div>
                    <div class="flex items-center justify-between mb-6 relative z-10">
                        <div class="p-3 bg-rose-500/10 rounded-2xl border border-amber-500/20 text-rose-500 shadow-xl">
                            <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
                        </div>
                        <span class="px-3 py-1 bg-rose-500/10 text-rose-500 text-[9px] font-black uppercase rounded-xl border border-rose-500/20 tracking-widest">Pending</span>
                    </div>
                    <p class="text-5xl font-black text-white tracking-tighter relative z-10">{{ $transaksiBelumDibayar }}</p>
                    <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest mt-1 relative z-10">Tagihan Tertunda</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Live Sessions -->
            <div class="card-pro !p-0 overflow-hidden border-white/5 backdrop-blur-xl bg-slate-900/40">
                <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                    <h2 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Sesi Parkir Langsung</h2>
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></span>
                        <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">Live Monitor</span>
                    </div>
                </div>
                <div class="p-8">
                    @php $activeParkings = $riwayatTransaksi->where('status', 'masuk'); @endphp
                    @if($transaksiAktif > 0 && $activeParkings->count())
                        <div class="space-y-6">
                            @foreach($activeParkings as $trx)
                                @php
                                    $masuk = \Carbon\Carbon::parse($trx->waktu_masuk);
                                    $durasiMenit = now()->diffInMinutes($masuk);
                                    $durasiJam = ceil($durasiMenit / 60);
                                    $estimasiBiaya = $durasiJam * ($trx->tarif->tarif_perjam ?? 0);
                                @endphp
                                <div class="p-6 rounded-[2rem] bg-slate-950/50 border border-white/5 relative overflow-hidden group hover:border-emerald-500/30 transition-all">
                                    <div class="absolute right-0 top-0 bottom-0 w-1.5 bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.5)]"></div>
                                    <div class="flex items-center justify-between mb-8">
                                        <div class="flex items-center gap-6">
                                            <div class="w-16 h-16 rounded-[1.25rem] bg-slate-950 border border-white/10 flex flex-col items-center justify-center font-black text-white group-hover:border-emerald-500/50 transition-all shadow-xl">
                                                <span class="text-[8px] text-slate-600 leading-none mb-1.5 uppercase tracking-tighter">{{ substr($trx->kendaraan->plat_nomor ?? '-', 0, 2) }}</span>
                                                <span class="text-xl leading-none tracking-tight">{{ substr($trx->kendaraan->plat_nomor ?? '-', 2, 4) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-lg font-black text-white tracking-tight">{{ $trx->kendaraan->plat_nomor ?? '-' }}</p>
                                                <div class="flex items-center gap-2.5 mt-1.5">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                                    <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest">{{ $trx->area->nama_area ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-2xl font-black text-emerald-500 tracking-tighter">Rp {{ number_format($estimasiBiaya, 0, ',', '.') }}</p>
                                            <p class="text-[9px] text-slate-600 font-black uppercase tracking-widest mt-1">Estimasi Biaya</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-8 pt-6 border-t border-white/5">
                                        <div>
                                            <p class="text-[9px] text-slate-600 uppercase font-black tracking-widest mb-2.5">Waktu Masuk</p>
                                            <div class="flex items-center gap-2.5">
                                                <i class="fa-solid fa-right-to-bracket text-[10px] text-slate-500"></i>
                                                <p class="text-xs font-black text-white uppercase tracking-widest">{{ $masuk->format('H:i') }} <span class="text-slate-600 ml-1.5">{{ $masuk->format('d M') }}</span></p>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-[9px] text-slate-600 uppercase font-black tracking-widest mb-2.5">Durasi</p>
                                            <div class="flex items-center gap-2.5">
                                                <i class="fa-solid fa-hourglass-half text-[10px] text-slate-500"></i>
                                                <p class="text-xs font-black text-white uppercase tracking-widest">
                                                    {{ floor($durasiMenit / 60) }}H {{ $durasiMenit % 60 }}M
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-20 text-center bg-slate-950/30 rounded-[2.5rem] border border-dashed border-white/10">
                            <div class="w-24 h-24 rounded-[2.5rem] bg-slate-900 border border-white/5 flex items-center justify-center mx-auto mb-8 text-slate-800 shadow-2xl">
                                <i class="fa-solid fa-car-side text-4xl opacity-10"></i>
                            </div>
                            <p class="text-[10px] text-slate-600 font-black uppercase tracking-[0.3em]">Tidak ada sesi parkir aktif</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card-pro !p-0 overflow-hidden border-white/5 backdrop-blur-xl bg-slate-900/40">
                <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                    <h2 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Aktivitas Terakhir</h2>
                    <a href="{{ route('user.history') }}" class="text-[10px] font-black text-emerald-500 hover:text-emerald-400 uppercase tracking-widest transition-all hover:translate-x-1 flex items-center gap-2">
                        Semua Riwayat <i class="fa-solid fa-arrow-right text-[8px]"></i>
                    </a>
                </div>
                <div class="p-8">
                    @if($riwayatTransaksi->count())
                        <div class="space-y-8">
                            @foreach($riwayatTransaksi->take(5) as $trx)
                                <div class="flex items-center justify-between group">
                                    <div class="flex items-center gap-5">
                                        <div class="w-14 h-14 rounded-2xl bg-slate-950 border border-white/5 flex items-center justify-center text-slate-600 group-hover:border-emerald-500/30 transition-all shadow-xl">
                                            <i class="fa-solid fa-location-dot text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-base font-black text-white tracking-tight">{{ $trx->kendaraan->plat_nomor ?? '-' }}</p>
                                            <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest mt-1">
                                                {{ \Carbon\Carbon::parse($trx->waktu_masuk)->translatedFormat('d M, H:i') }}
                                                @if($trx->waktu_keluar) — {{ \Carbon\Carbon::parse($trx->waktu_keluar)->format('H:i') }} @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($trx->biaya_total)
                                            <p class="text-base font-black text-white tracking-tighter">Rp {{ number_format($trx->biaya_total, 0, ',', '.') }}</p>
                                            <span class="inline-flex items-center px-2.5 py-1 bg-emerald-500/10 text-emerald-500 text-[8px] font-black uppercase rounded-lg border border-emerald-500/20 tracking-widest mt-1.5">Lunas</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 bg-amber-500/10 text-amber-500 text-[8px] font-black uppercase rounded-lg border border-amber-500/20 tracking-widest">Proses</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-12 text-center">
                            <p class="text-[10px] text-slate-600 font-black uppercase tracking-widest italic">Belum ada riwayat transaksi</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes shimmer {
        100% { transform: translateX(100%); }
    }
</style>
@endsection
