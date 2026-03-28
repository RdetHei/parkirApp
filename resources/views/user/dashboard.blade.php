@extends('layouts.app')

@section('content')
<div class="p-8 relative z-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 animate-fade-in-up">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                    Personal Hub
                </span>
            </div>
            <h1 class="text-4xl font-bold tracking-tight text-white">Halo, <span class="text-emerald-500">{{ explode(' ', $user->name)[0] }}</span></h1>
            <p class="text-slate-400 text-sm mt-2">Manage your vehicles, balance, and parking activities.</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('user.saldo.topup') }}" class="group relative px-6 py-3 bg-emerald-500 text-slate-950 font-bold text-xs uppercase tracking-widest rounded-xl transition-all hover:bg-emerald-400 hover:shadow-[0_0_20px_rgba(16,185,129,0.4)] flex items-center gap-2">
                <i class="fa-solid fa-plus text-sm"></i>
                Top Up Balance
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
        <!-- Wallet Balance (Large) -->
        <div class="lg:col-span-2 card-pro group overflow-hidden relative border-emerald-500/20 animate-fade-in-up" style="animation-delay: 0.1s">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
            <div class="relative z-10 flex flex-col h-full justify-between min-h-[220px]">
                <div>
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-emerald-500/10 rounded-2xl border border-emerald-500/20">
                                <i class="fa-solid fa-wallet text-2xl text-emerald-500"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">NestonPay Balance</p>
                                <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest mt-1">Status: Active & Secure</p>
                            </div>
                        </div>
                        <div class="flex -space-x-2">
                            <div class="w-8 h-8 rounded-full border-2 border-[#0f172a] bg-emerald-500 flex items-center justify-center text-[10px] font-black text-slate-950">N</div>
                            <div class="w-8 h-8 rounded-full border-2 border-[#0f172a] bg-slate-800 flex items-center justify-center text-[10px] font-black text-white">P</div>
                        </div>
                    </div>
                    <h2 class="text-6xl font-black tracking-tighter text-white">
                        <span class="text-emerald-500 text-3xl font-medium mr-2">Rp</span>{{ number_format($user->saldo, 0, ',', '.') }}
                    </h2>
                </div>
                
                <div class="flex gap-4 mt-10">
                    <a href="{{ route('user.saldo.index') }}" class="px-8 py-3 bg-white/[0.03] hover:bg-white/[0.08] text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border border-white/10">
                        Transaction History
                    </a>
                </div>
            </div>
        </div>

        <div class="space-y-6 animate-fade-in-up" style="animation-delay: 0.2s">
            <!-- Active Parking Card -->
            <div class="card-pro group overflow-hidden relative border-amber-500/10">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-500/5 rounded-full blur-2xl group-hover:bg-amber-500/10 transition-all"></div>
                <div class="flex items-center justify-between mb-4 relative z-10">
                    <div class="p-2.5 bg-amber-500/10 rounded-xl border border-amber-500/20 text-amber-500">
                        <i class="fa-solid fa-clock text-lg"></i>
                    </div>
                    <span class="px-2.5 py-1 bg-amber-500/10 text-amber-500 text-[9px] font-black uppercase rounded-lg border border-amber-500/20">In Progress</span>
                </div>
                <p class="text-4xl font-black text-white tracking-tighter relative z-10">{{ $transaksiAktif }}</p>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1 relative z-10">Active Parking</p>
            </div>

            <!-- Unpaid Bills Card -->
            <div class="card-pro group overflow-hidden relative border-rose-500/10">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-rose-500/5 rounded-full blur-2xl group-hover:bg-rose-500/10 transition-all"></div>
                <div class="flex items-center justify-between mb-4 relative z-10">
                    <div class="p-2.5 bg-rose-500/10 rounded-xl border border-rose-500/20 text-rose-500">
                        <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
                    </div>
                    <span class="px-2.5 py-1 bg-rose-500/10 text-rose-500 text-[9px] font-black uppercase rounded-lg border border-rose-500/20">Pending</span>
                </div>
                <p class="text-4xl font-black text-white tracking-tighter relative z-10">{{ $transaksiBelumDibayar }}</p>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1 relative z-10">Unpaid Bills</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Active Sessions -->
        <div class="card-pro !p-0 overflow-hidden animate-fade-in-up" style="animation-delay: 0.3s">
            <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                <h2 class="text-sm font-black text-white uppercase tracking-widest">Live Parking Sessions</h2>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">Live Updates</span>
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
                            <div class="p-6 rounded-3xl bg-slate-950 border border-white/5 relative overflow-hidden group hover:border-emerald-500/30 transition-all">
                                <div class="absolute right-0 top-0 bottom-0 w-1.5 bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.5)]"></div>
                                <div class="flex items-center justify-between mb-8">
                                    <div class="flex items-center gap-5">
                                        <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-white/10 flex flex-col items-center justify-center font-black text-white group-hover:border-emerald-500/50 transition-all">
                                            <span class="text-[9px] text-slate-500 leading-none mb-1 uppercase tracking-tighter">{{ substr($trx->kendaraan->plat_nomor ?? '-', 0, 2) }}</span>
                                            <span class="text-lg leading-none tracking-tight">{{ substr($trx->kendaraan->plat_nomor ?? '-', 2, 4) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-base font-black text-white tracking-tight">{{ $trx->kendaraan->plat_nomor ?? '-' }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500/50"></div>
                                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">{{ $trx->area->nama_area ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xl font-black text-emerald-500 tracking-tighter">Rp {{ number_format($estimasiBiaya, 0, ',', '.') }}</p>
                                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">Running Cost</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-8 pt-6 border-t border-white/5">
                                    <div>
                                        <p class="text-[9px] text-slate-600 uppercase font-black tracking-widest mb-2">Check-in Time</p>
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-right-to-bracket text-[10px] text-slate-500"></i>
                                            <p class="text-xs font-black text-white uppercase tracking-widest">{{ $masuk->format('H:i') }} <span class="text-slate-500 ml-1">{{ $masuk->format('d M') }}</span></p>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-slate-600 uppercase font-black tracking-widest mb-2">Duration</p>
                                        <div class="flex items-center gap-2">
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
                    <div class="py-20 text-center bg-slate-950/50 rounded-[2.5rem] border border-dashed border-white/10">
                        <div class="w-20 h-20 rounded-[2.5rem] bg-slate-900 border border-white/5 flex items-center justify-center mx-auto mb-6 text-slate-800">
                            <i class="fa-solid fa-car-side text-3xl opacity-20"></i>
                        </div>
                        <p class="text-[10px] text-slate-500 font-black uppercase tracking-[0.3em]">No active parking sessions</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent History -->
        <div class="card-pro !p-0 overflow-hidden animate-fade-in-up" style="animation-delay: 0.4s">
            <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                <h2 class="text-sm font-black text-white uppercase tracking-widest">Recent Activity</h2>
                <a href="{{ route('transaksi.index') }}" class="text-[10px] font-black text-emerald-500 hover:text-emerald-400 uppercase tracking-widest transition-colors">Full History <i class="fa-solid fa-arrow-right ml-1"></i></a>
            </div>
            <div class="p-8">
                @if($riwayatTransaksi->count())
                    <div class="space-y-8">
                        @foreach($riwayatTransaksi as $trx)
                            <div class="flex items-center justify-between group">
                                <div class="flex items-center gap-5">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-950 border border-white/5 flex items-center justify-center text-slate-600 group-hover:border-emerald-500/30 transition-all shadow-xl">
                                        <i class="fa-solid fa-location-dot text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-white tracking-tight">{{ $trx->kendaraan->plat_nomor ?? '-' }}</p>
                                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">
                                            {{ \Carbon\Carbon::parse($trx->waktu_masuk)->translatedFormat('d M, H:i') }}
                                            @if($trx->waktu_keluar) — {{ \Carbon\Carbon::parse($trx->waktu_keluar)->format('H:i') }} @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($trx->biaya_total)
                                        <p class="text-sm font-black text-white tracking-tighter">Rp {{ number_format($trx->biaya_total, 0, ',', '.') }}</p>
                                        <span class="inline-flex items-center px-2 py-0.5 bg-emerald-500/10 text-emerald-500 text-[8px] font-black uppercase rounded border border-emerald-500/20 tracking-widest mt-1">Paid</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 bg-amber-500/10 text-amber-500 text-[8px] font-black uppercase rounded border border-amber-500/20 tracking-widest">Processing</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center">
                        <p class="text-[10px] text-slate-600 font-black uppercase tracking-widest">No transaction history detected</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@endsection
