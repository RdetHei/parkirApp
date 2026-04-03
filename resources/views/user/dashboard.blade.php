@extends('layouts.app')

@section('title', 'Dashboard - NESTON')

@section('content')
<div class="p-4 sm:p-8 relative z-10 animate-fade-in">
    <!-- Background Glows (Consistent with Auth) -->
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="relative z-10">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8 lg:mb-12">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                        Personal Hub
                    </span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-white uppercase">Halo, <span class="text-emerald-500">{{ explode(' ', $user->name)[0] }}</span></h1>
                <p class="text-slate-400 text-xs sm:text-sm mt-2 font-medium tracking-wide max-w-lg">Kelola kendaraan, saldo, dan aktivitas parkir Anda dalam satu dasbor.</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('user.saldo.topup') }}" class="group relative w-full sm:w-auto px-6 sm:px-8 py-3.5 sm:py-4 bg-emerald-500 text-slate-950 font-black text-[10px] sm:text-xs uppercase tracking-widest rounded-2xl transition-all hover:bg-emerald-400 hover:shadow-[0_0_30px_rgba(16,185,129,0.3)] flex items-center justify-center gap-3 overflow-hidden active:scale-[0.98]">
                    <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                    <i class="fa-solid fa-plus text-sm"></i>
                    Top Up Saldo
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 mb-8 lg:mb-10">
            <!-- Wallet Balance (Large Glass Card) -->
            <div class="lg:col-span-2 card-pro group overflow-hidden relative border-emerald-500/10 backdrop-blur-xl bg-slate-900/40 p-5 sm:p-8">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
                <div class="relative z-10 flex flex-col h-full justify-between min-h-[180px] sm:min-h-[240px]">
                    <div class="flex items-center justify-between mb-4 sm:mb-0">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 border border-emerald-500/20 group-hover:rotate-12 transition-transform duration-500">
                                <i class="fa-solid fa-wallet text-xl sm:text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-[0.2em] mb-0.5 sm:mb-1">Wallet Balance</p>
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></span>
                                    <span class="text-[9px] sm:text-[10px] font-black text-emerald-500 uppercase tracking-widest">Active & Secure</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 sm:mt-0">
                        <h2 class="text-3xl sm:text-5xl md:text-7xl font-black tracking-tighter text-white flex items-baseline gap-2 sm:gap-4 break-all">
                            <span class="text-emerald-500 text-lg sm:text-3xl font-medium tracking-normal">Rp</span>{{ number_format($user->saldo, 0, ',', '.') }}
                        </h2>
                        <p class="text-[10px] sm:text-xs text-slate-500 mt-2 sm:mt-4 font-medium max-w-md leading-relaxed">Dana Anda aman di NestonPay. Gunakan untuk pembayaran parkir otomatis di seluruh area Neston.</p>
                    </div>
                    
                    <div class="flex gap-4 mt-8 sm:mt-12">
                        <a href="{{ route('user.saldo.index') }}" class="px-6 sm:px-8 py-3 sm:py-3.5 bg-white/5 hover:bg-emerald-500 hover:text-slate-950 rounded-2xl text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-all border border-white/5 hover:border-emerald-500 active:scale-95 shadow-xl">
                            Riwayat Transaksi
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-1 gap-4 lg:gap-6">
                <!-- Active Parking Card -->
                <div class="card-pro group overflow-hidden relative border-amber-500/10 backdrop-blur-xl bg-slate-900/40 p-5 sm:p-6">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-500/5 rounded-full blur-2xl group-hover:bg-amber-500/10 transition-all"></div>
                    <div class="flex items-center justify-between mb-4 sm:mb-6 relative z-10">
                        <div class="p-2.5 sm:p-3 bg-amber-500/10 rounded-2xl border border-amber-500/20 text-amber-500 shadow-xl">
                            <i class="fa-solid fa-clock text-lg sm:text-xl"></i>
                        </div>
                        <span class="px-2 py-0.5 sm:px-3 sm:py-1 bg-amber-500/10 text-amber-500 text-[8px] sm:text-[9px] font-black uppercase rounded-xl border border-amber-500/20 tracking-widest">In Progress</span>
                    </div>
                    <p class="text-4xl sm:text-5xl font-black text-white tracking-tighter relative z-10">{{ $transaksiAktif }}</p>
                    <p class="text-[9px] sm:text-[10px] text-slate-500 font-black uppercase tracking-widest mt-1 relative z-10">Sesi Parkir Aktif</p>
                </div>

                <!-- Unpaid Bills Card -->
                <div class="card-pro group overflow-hidden relative border-rose-500/10 backdrop-blur-xl bg-slate-900/40 p-5 sm:p-6">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-rose-500/5 rounded-full blur-2xl group-hover:bg-rose-500/10 transition-all"></div>
                    <div class="flex items-center justify-between mb-4 sm:mb-6 relative z-10">
                        <div class="p-2.5 sm:p-3 bg-rose-500/10 rounded-2xl border border-rose-500/20 text-rose-500 shadow-xl">
                            <i class="fa-solid fa-file-invoice-dollar text-lg sm:text-xl"></i>
                        </div>
                        <span class="px-2 py-0.5 sm:px-3 sm:py-1 bg-rose-500/10 text-rose-500 text-[8px] sm:text-[9px] font-black uppercase rounded-xl border border-rose-500/20 tracking-widest">Pending</span>
                    </div>
                    <p class="text-4xl sm:text-5xl font-black text-white tracking-tighter relative z-10">{{ $transaksiBelumDibayar }}</p>
                    <p class="text-[9px] sm:text-[10px] text-slate-500 font-black uppercase tracking-widest mt-1 relative z-10">Tagihan Tertunda</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
            <!-- Live Sessions -->
            <div class="card-pro !p-0 overflow-hidden border-white/5 backdrop-blur-xl bg-slate-900/40">
                <div class="px-6 sm:px-8 py-5 sm:py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                    <h2 class="text-[10px] sm:text-[11px] font-black text-white uppercase tracking-[0.2em]">Sesi Parkir Langsung</h2>
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></span>
                        <span class="text-[8px] sm:text-[9px] font-black text-emerald-500 uppercase tracking-widest">Live Monitor</span>
                    </div>
                </div>
                <div class="p-5 sm:p-8">
                    @php $activeParkings = $riwayatTransaksi->where('status', 'masuk'); @endphp
                    @if($transaksiAktif > 0 && $activeParkings->count())
                        <div class="space-y-4 sm:space-y-6">
                            @foreach($activeParkings as $trx)
                                @php
                                    $masuk = \Carbon\Carbon::parse($trx->waktu_masuk);
                                    $durasiMenit = now()->diffInMinutes($masuk);
                                    $durasiJam = ceil($durasiMenit / 60);
                                    $estimasiBiaya = $durasiJam * ($trx->tarif->tarif_perjam ?? 0);
                                @endphp
                                <div class="p-5 sm:p-6 rounded-[2rem] bg-slate-950/50 border border-white/5 relative overflow-hidden group hover:border-emerald-500/30 transition-all">
                                    <div class="absolute right-0 top-0 bottom-0 w-1.5 bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.5)]"></div>
                                    <div class="flex items-center justify-between mb-6 sm:mb-8">
                                        <div class="flex items-center gap-4 sm:gap-6">
                                            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-[1.25rem] bg-slate-950 border border-white/10 flex flex-col items-center justify-center font-black text-white group-hover:border-emerald-500/50 transition-all shadow-xl">
                                                <span class="text-[7px] sm:text-[8px] text-slate-600 leading-none mb-1 sm:mb-1.5 uppercase tracking-tighter">{{ substr($trx->kendaraan->plat_nomor ?? '-', 0, 2) }}</span>
                                                <span class="text-lg sm:text-xl leading-none tracking-tight">{{ substr($trx->kendaraan->plat_nomor ?? '-', 2, 4) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-base sm:text-lg font-black text-white tracking-tight">{{ $trx->kendaraan->plat_nomor ?? '-' }}</p>
                                                <div class="flex items-center gap-2.5 mt-1 sm:mt-1.5">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                                    <p class="text-[9px] sm:text-[10px] text-slate-500 font-black uppercase tracking-widest">{{ $trx->area->nama_area ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xl sm:text-2xl font-black text-emerald-500 tracking-tighter">Rp {{ number_format($estimasiBiaya, 0, ',', '.') }}</p>
                                            <p class="text-[8px] sm:text-[9px] text-slate-600 font-black uppercase tracking-widest mt-0.5 sm:mt-1">Estimasi Biaya</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-6 sm:gap-8 pt-5 sm:pt-6 border-t border-white/5">
                                        <div>
                                            <p class="text-[8px] sm:text-[9px] text-slate-600 uppercase font-black tracking-widest mb-2 sm:mb-2.5">Waktu Masuk</p>
                                            <div class="flex items-center gap-2 sm:gap-2.5">
                                                <i class="fa-solid fa-right-to-bracket text-[9px] sm:text-[10px] text-slate-500"></i>
                                                <p class="text-[10px] sm:text-xs font-black text-white uppercase tracking-widest">{{ $masuk->format('H:i') }} <span class="text-slate-600 ml-1 sm:ml-1.5">{{ $masuk->format('d M') }}</span></p>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-[8px] sm:text-[9px] text-slate-600 uppercase font-black tracking-widest mb-2 sm:mb-2.5">Durasi</p>
                                            <div class="flex items-center gap-2 sm:gap-2.5">
                                                <i class="fa-solid fa-hourglass-half text-[9px] sm:text-[10px] text-slate-500"></i>
                                                <p class="text-[10px] sm:text-xs font-black text-white uppercase tracking-widest">
                                                    {{ floor($durasiMenit / 60) }}H {{ $durasiMenit % 60 }}M
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-16 sm:py-20 text-center bg-slate-950/30 rounded-[2rem] sm:rounded-[2.5rem] border border-dashed border-white/10">
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-[2rem] sm:rounded-[2.5rem] bg-slate-900 border border-white/5 flex items-center justify-center mx-auto mb-6 sm:mb-8 text-slate-800 shadow-2xl">
                                <i class="fa-solid fa-car-side text-3xl sm:text-4xl opacity-10"></i>
                            </div>
                            <p class="text-[9px] sm:text-[10px] text-slate-600 font-black uppercase tracking-[0.3em]">Tidak ada sesi parkir aktif</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card-pro !p-0 overflow-hidden border-white/5 backdrop-blur-xl bg-slate-900/40">
                <div class="px-6 sm:px-8 py-5 sm:py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                    <h2 class="text-[10px] sm:text-[11px] font-black text-white uppercase tracking-[0.2em]">Aktivitas Terakhir</h2>
                    <a href="{{ route('user.history') }}" class="text-[9px] sm:text-[10px] font-black text-emerald-500 hover:text-emerald-400 uppercase tracking-widest transition-all hover:translate-x-1 flex items-center gap-2">
                        Semua Riwayat <i class="fa-solid fa-arrow-right text-[8px]"></i>
                    </a>
                </div>
                <div class="p-6 sm:p-8">
                    @if($riwayatTransaksi->count())
                        <div class="space-y-6 sm:space-y-8">
                            @foreach($riwayatTransaksi->take(5) as $trx)
                                <div class="flex items-center justify-between group">
                                    <div class="flex items-center gap-4 sm:gap-5">
                                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-slate-950 border border-white/5 flex items-center justify-center text-slate-600 group-hover:border-emerald-500/30 transition-all shadow-xl">
                                            <i class="fa-solid fa-location-dot text-lg sm:text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm sm:text-base font-black text-white tracking-tight">{{ $trx->kendaraan->plat_nomor ?? '-' }}</p>
                                            <p class="text-[9px] sm:text-[10px] text-slate-500 font-black uppercase tracking-widest mt-0.5 sm:mt-1">
                                                {{ \Carbon\Carbon::parse($trx->waktu_masuk)->translatedFormat('d M, H:i') }}
                                                @if($trx->waktu_keluar) — {{ \Carbon\Carbon::parse($trx->waktu_keluar)->format('H:i') }} @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($trx->biaya_total)
                                            <p class="text-sm sm:text-base font-black text-white tracking-tighter">Rp {{ number_format($trx->biaya_total, 0, ',', '.') }}</p>
                                            <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 sm:py-1 bg-emerald-500/10 text-emerald-500 text-[7px] sm:text-[8px] font-black uppercase rounded-lg border border-emerald-500/20 tracking-widest mt-1 sm:mt-1.5">Lunas</span>
                                        @else
                                            <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 sm:py-1 bg-amber-500/10 text-amber-500 text-[7px] sm:text-[8px] font-black uppercase rounded-lg border border-amber-500/20 tracking-widest">Proses</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-12 text-center">
                            <p class="text-[9px] sm:text-[10px] text-slate-600 font-black uppercase tracking-widest italic">Belum ada riwayat transaksi</p>
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
