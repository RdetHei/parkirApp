@extends('layouts.app')

@section('content')
    <div class="p-8 relative z-10">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                        Officer Console
                    </span>
                </div>
                <h1 class="text-4xl font-bold tracking-tight text-white">Welcome back, <span class="text-emerald-500">{{ explode(' ', auth()->user()->name)[0] }}</span></h1>
                <p class="text-slate-400 text-sm mt-2">Managing parking operations and live vehicle tracking.</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('transaksi.create-check-in') }}" class="group relative px-6 py-3 bg-emerald-500 text-slate-950 font-bold text-xs uppercase tracking-widest rounded-xl transition-all hover:bg-emerald-400 hover:shadow-[0_0_20px_rgba(16,185,129,0.4)] flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Check-In Vehicle
                </a>
                <a href="{{ route('payment.select-transaction') }}" class="group relative px-6 py-3 bg-slate-800 text-white font-bold text-xs uppercase tracking-widest rounded-xl border border-white/10 transition-all hover:bg-slate-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Process Payment
                </a>
            </div>
        </div>

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Active Parking -->
            <div class="card-pro group overflow-hidden relative">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-emerald-500/5 rounded-full blur-3xl group-hover:bg-emerald-500/10 transition-all duration-500"></div>
                <div class="relative z-10">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Current Active</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-bold text-white tracking-tight">{{ $transaksiAktif }}</h3>
                        <span class="text-[10px] font-bold text-emerald-500 uppercase">Vehicles</span>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="text-[10px] text-slate-500 font-medium italic">Live tracking enabled</span>
                    </div>
                </div>
            </div>

            <!-- Entries Today -->
            <div class="card-pro group overflow-hidden relative">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/10 transition-all duration-500"></div>
                <div class="relative z-10">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Entries Today</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-bold text-white tracking-tight">{{ $transaksiHariIni }}</h3>
                        <span class="text-[10px] font-bold text-indigo-500 uppercase">Transactions</span>
                    </div>
                    <div class="mt-4 pt-4 border-t border-white/5">
                        <span class="text-[10px] text-slate-500 font-medium">Daily throughput</span>
                    </div>
                </div>
            </div>

            <!-- Capacity Usage -->
            <div class="card-pro group overflow-hidden relative">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-amber-500/5 rounded-full blur-3xl group-hover:bg-amber-500/10 transition-all duration-500"></div>
                <div class="relative z-10">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Total Capacity</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-bold text-amber-500 tracking-tight">{{ $totalTerisi }} <span class="text-slate-500 text-lg font-medium">/ {{ $totalKapasitas }}</span></h3>
                    </div>
                    <div class="mt-5 w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                        @php $percent = $totalKapasitas > 0 ? ($totalTerisi / $totalKapasitas) * 100 : 0; @endphp
                        <div class="bg-amber-500 h-1.5 rounded-full transition-all duration-1000" style="width: {{ min($percent, 100) }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Revenue Share -->
            <div class="card-pro group overflow-hidden relative">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-blue-500/5 rounded-full blur-3xl group-hover:bg-blue-500/10 transition-all duration-500"></div>
                <div class="relative z-10">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Revenue Today</p>
                    <h3 class="text-2xl font-bold text-white tracking-tight">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h3>
                    <div class="mt-4 pt-4 border-t border-white/5">
                        <span class="text-[10px] text-slate-500 font-medium">Accumulated earnings</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar Info Section -->
            <div class="space-y-6">
                <!-- Area Monitoring -->
                <div class="card-pro !p-0 overflow-hidden">
                    <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02]">
                        <h2 class="text-sm font-bold text-white">Area Monitoring</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        @foreach($areaParkir as $area)
                            @php 
                                $percentArea = $area->kapasitas > 0 ? ($area->terisi / $area->kapasitas) * 100 : 0; 
                                $colorClass = $percentArea >= 90 ? 'bg-rose-500' : ($percentArea >= 70 ? 'bg-amber-500' : 'bg-emerald-500');
                            @endphp
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $area->nama_area }}</span>
                                    <span class="text-[10px] font-bold {{ $percentArea >= 90 ? 'text-rose-400' : 'text-emerald-500' }}">
                                        {{ $area->terisi }} / {{ $area->kapasitas }}
                                    </span>
                                </div>
                                <div class="w-full bg-slate-800 rounded-full h-1 overflow-hidden">
                                    <div class="{{ $colorClass }} h-1 rounded-full transition-all duration-1000" style="width: {{ min($percentArea, 100) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Quick Navigation -->
                <div class="card-pro !p-0 overflow-hidden">
                    <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02]">
                        <h2 class="text-sm font-bold text-white">Quick Navigation</h2>
                    </div>
                    <div class="p-4 grid grid-cols-2 gap-3">
                        <a href="{{ route('transaksi.parkir.index') }}" class="p-4 rounded-xl bg-slate-900 border border-white/5 hover:border-emerald-500/30 hover:bg-slate-800 transition-all group">
                            <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500 group-hover:scale-110 transition-transform mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">Live Map</span>
                        </a>
                        <a href="{{ route('transaksi.index') }}" class="p-4 rounded-xl bg-slate-900 border border-white/5 hover:border-indigo-500/30 hover:bg-slate-800 transition-all group">
                            <div class="w-10 h-10 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-500 group-hover:scale-110 transition-transform mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">History</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Stream -->
            <div class="lg:col-span-2 card-pro !p-0 overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                    <h2 class="text-sm font-bold text-white">Recent Activity Stream</h2>
                    <a href="{{ route('transaksi.index') }}" class="text-[10px] font-bold text-emerald-500 hover:text-emerald-400 uppercase tracking-widest transition-colors">View All Logs</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-white/[0.01] text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                <th class="px-6 py-3">Vehicle</th>
                                <th class="px-6 py-3">Location</th>
                                <th class="px-6 py-3">Timestamp</th>
                                <th class="px-6 py-3 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($aktivitasTerbaru as $trx)
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-slate-800 border border-white/5 flex flex-col items-center justify-center font-bold text-white group-hover:border-emerald-500/30 transition-colors">
                                                <span class="text-[8px] text-slate-500 leading-none mb-0.5">{{ substr($trx->kendaraan->plat_nomor ?? '-', 0, 2) }}</span>
                                                <span class="text-xs leading-none">{{ substr($trx->kendaraan->plat_nomor ?? '-', 2, 4) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-white">{{ $trx->kendaraan->plat_nomor ?? '-' }}</p>
                                                <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">{{ $trx->kendaraan->jenis_kendaraan ?? 'Vehicle' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500/50"></div>
                                            <span class="text-xs text-slate-300 font-medium">{{ $trx->area->nama_area ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-xs font-bold text-white">{{ $trx->waktu_masuk->format('H:i') }}</p>
                                        <p class="text-[10px] text-slate-500 font-medium">{{ $trx->waktu_masuk->translatedFormat('d M Y') }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($trx->status === 'masuk')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                                CHECK-IN
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-800 text-slate-400 border border-white/5">
                                                CHECK-OUT
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-slate-800 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                                            <p class="text-slate-500 text-xs font-bold uppercase tracking-widest">No recent activities</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

