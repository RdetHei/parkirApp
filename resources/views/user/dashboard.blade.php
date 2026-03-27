@extends('layouts.app')

@section('content')
    <div class="p-8 relative z-10">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                        Member Portal
                    </span>
                </div>
                <h1 class="text-4xl font-bold tracking-tight text-white">Halo, <span class="text-emerald-500">{{ explode(' ', $user->name)[0] }}</span></h1>
                <p class="text-slate-400 text-sm mt-2">Manage your vehicles, balance, and parking history.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('user.vehicles.index') }}" class="group relative px-5 py-2.5 bg-slate-800 text-white font-bold text-[10px] uppercase tracking-widest rounded-xl border border-white/10 transition-all hover:bg-slate-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                    My Vehicles
                </a>
                <a href="{{ route('user.bookings') }}" class="group relative px-5 py-2.5 bg-slate-800 text-white font-bold text-[10px] uppercase tracking-widest rounded-xl border border-white/10 transition-all hover:bg-slate-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                    Bookings
                </a>
            </div>
        </div>

        <!-- Main Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <!-- Wallet Balance (Large) -->
            <div class="md:col-span-2 card-pro group overflow-hidden relative border-emerald-500/20">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
                <div class="relative z-10 flex flex-col h-full justify-between min-h-[180px]">
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2.5 bg-emerald-500/10 rounded-xl border border-emerald-500/20">
                                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                            </div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">NestonPay Balance</span>
                        </div>
                        <h2 class="text-5xl font-bold tracking-tighter text-white">
                            <span class="text-emerald-500 text-3xl font-medium mr-1">Rp</span>{{ number_format($user->saldo, 0, ',', '.') }}
                        </h2>
                    </div>
                    
                    <div class="flex gap-4 mt-8">
                        <a href="{{ route('user.saldo.index') }}" class="px-5 py-2.5 bg-white/[0.03] hover:bg-white/[0.08] text-white rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all border border-white/10">
                            View History
                        </a>
                        <a href="{{ route('user.saldo.topup') }}" class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all shadow-xl shadow-emerald-500/20">
                            Top Up Balance
                        </a>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Active Parking Card -->
                <div class="card-pro group overflow-hidden relative">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-500/5 rounded-full blur-2xl group-hover:bg-amber-500/10 transition-all"></div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 bg-amber-500/10 rounded-lg border border-amber-500/20 text-amber-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                        </div>
                        <span class="px-2 py-0.5 bg-amber-500/10 text-amber-500 text-[8px] font-black uppercase rounded border border-amber-500/20">Active</span>
                    </div>
                    <p class="text-3xl font-bold text-white tracking-tight">{{ $transaksiAktif }}</p>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">Current Parking</p>
                </div>

                <!-- Unpaid Bills Card -->
                <div class="card-pro group overflow-hidden relative">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-rose-500/5 rounded-full blur-2xl group-hover:bg-rose-500/10 transition-all"></div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 bg-rose-500/10 rounded-lg border border-rose-500/20 text-rose-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                        </div>
                        <span class="px-2 py-0.5 bg-rose-500/10 text-rose-500 text-[8px] font-black uppercase rounded border border-rose-500/20">Pending</span>
                    </div>
                    <p class="text-3xl font-bold text-white tracking-tight">{{ $transaksiBelumDibayar }}</p>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">Unpaid Bills</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Active Sessions -->
            <div class="card-pro !p-0 overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02]">
                    <h2 class="text-sm font-bold text-white">Live Parking Sessions</h2>
                </div>
                <div class="p-6">
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
                                <div class="p-5 rounded-2xl bg-slate-900 border border-white/5 relative overflow-hidden group">
                                    <div class="absolute right-0 top-0 bottom-0 w-1 bg-emerald-500 opacity-50"></div>
                                    <div class="flex items-center justify-between mb-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-xl bg-slate-800 border border-white/5 flex flex-col items-center justify-center font-bold text-white">
                                                <span class="text-[8px] text-slate-500 leading-none mb-0.5">{{ substr($trx->kendaraan->plat_nomor ?? '-', 0, 2) }}</span>
                                                <span class="text-xs leading-none">{{ substr($trx->kendaraan->plat_nomor ?? '-', 2, 4) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-white">{{ $trx->kendaraan->plat_nomor ?? '-' }}</p>
                                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">{{ $trx->area->nama_area ?? '-' }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold text-emerald-500">Rp {{ number_format($estimasiBiaya, 0, ',', '.') }}</p>
                                            <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">Est. Cost</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/5">
                                        <div>
                                            <p class="text-[9px] text-slate-500 uppercase font-bold mb-1">Entry Time</p>
                                            <p class="text-xs font-bold text-white">{{ $masuk->format('H:i') }} <span class="text-[10px] text-slate-500 font-medium ml-1">{{ $masuk->format('d M Y') }}</span></p>
                                        </div>
                                        <div>
                                            <p class="text-[9px] text-slate-500 uppercase font-bold mb-1">Duration</p>
                                            <p class="text-xs font-bold text-white">
                                                {{ floor($durasiMenit / 60) }}h {{ $durasiMenit % 60 }}m
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-12 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-slate-900 border border-white/5 flex items-center justify-center mx-auto mb-4 text-slate-700">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                            </div>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">No active sessions</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent History -->
            <div class="card-pro !p-0 overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                    <h2 class="text-sm font-bold text-white">Recent Transactions</h2>
                    <a href="{{ route('user.history') }}" class="text-[10px] font-bold text-emerald-500 hover:text-emerald-400 uppercase tracking-widest transition-colors">View All</a>
                </div>
                <div class="p-6">
                    @if($riwayatTransaksi->count())
                        <div class="space-y-6">
                            @foreach($riwayatTransaksi as $trx)
                                <div class="flex items-center justify-between group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-slate-900 border border-white/5 flex items-center justify-center text-slate-500 group-hover:border-emerald-500/30 transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-white">{{ $trx->kendaraan->plat_nomor ?? '-' }}</p>
                                            <p class="text-[10px] text-slate-500 font-medium">
                                                {{ \Carbon\Carbon::parse($trx->waktu_masuk)->format('d M, H:i') }}
                                                @if($trx->waktu_keluar) — {{ \Carbon\Carbon::parse($trx->waktu_keluar)->format('H:i') }} @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($trx->biaya_total)
                                            <p class="text-xs font-bold text-white">Rp {{ number_format($trx->biaya_total, 0, ',', '.') }}</p>
                                            <p class="text-[8px] text-emerald-500 font-black uppercase">Paid</p>
                                        @else
                                            <p class="text-[8px] text-amber-500 font-black uppercase">Processing</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-12 text-center">
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">No transaction history</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
