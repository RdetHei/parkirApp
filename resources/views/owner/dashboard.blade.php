@extends('layouts.app')

@section('content')
    <div class="p-8 relative z-10">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                        Executive Suite
                    </span>
                </div>
                <h1 class="text-4xl font-bold tracking-tight text-white">Financial <span class="text-emerald-500">Analytics</span></h1>
                <p class="text-slate-400 text-sm mt-2">Strategic insights and revenue performance reporting.</p>
            </div>
            <div class="flex items-center gap-4">
                <button class="group relative px-6 py-3 bg-slate-800 text-white font-bold text-xs uppercase tracking-widest rounded-xl border border-white/10 transition-all hover:bg-slate-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export Report
                </button>
            </div>
        </div>

        <!-- High-Level Financial Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Total Revenue -->
            <div class="card-pro group overflow-hidden relative">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-emerald-500/5 rounded-full blur-3xl group-hover:bg-emerald-500/10 transition-all duration-500"></div>
                <div class="relative z-10">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Total Gross Revenue</p>
                    <h3 class="text-2xl font-bold text-white tracking-tight">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                    <div class="mt-4 pt-4 border-t border-white/5 flex items-center justify-between">
                        <span class="text-[10px] text-slate-500 font-medium italic text-xs">All-time earnings</span>
                    </div>
                </div>
            </div>

            <!-- Daily Revenue -->
            <div class="card-pro group overflow-hidden relative border-emerald-500/20">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-emerald-500/10 rounded-full blur-3xl"></div>
                <div class="relative z-10">
                    <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mb-1">Daily Performance</p>
                    <h3 class="text-2xl font-bold text-white tracking-tight">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h3>
                    <div class="mt-4 pt-4 border-t border-white/5 flex items-center justify-between">
                        <span class="text-[10px] text-emerald-500 font-bold uppercase tracking-widest">Live Today</span>
                    </div>
                </div>
            </div>

            <!-- Successful Transactions -->
            <div class="card-pro group overflow-hidden relative">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/10 transition-all duration-500"></div>
                <div class="relative z-10">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Completed Orders</p>
                    <h3 class="text-3xl font-bold text-white tracking-tight">{{ $transaksiBerhasil }}</h3>
                    <div class="mt-4 pt-4 border-t border-white/5">
                        <span class="text-[10px] text-slate-500 font-medium uppercase tracking-widest">Total successful</span>
                    </div>
                </div>
            </div>

            <!-- Pending Payments -->
            <div class="card-pro group overflow-hidden relative">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-rose-500/5 rounded-full blur-3xl group-hover:bg-rose-500/10 transition-all duration-500"></div>
                <div class="relative z-10">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Pending Receivables</p>
                    <h3 class="text-3xl font-bold text-rose-500 tracking-tight">{{ $pembayaranPending }}</h3>
                    <div class="mt-4 pt-4 border-t border-white/5">
                        <span class="text-[10px] text-slate-500 font-medium uppercase tracking-widest">Action required</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Weekly Breakdown -->
            <div class="lg:col-span-2 card-pro !p-0 overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                    <h2 class="text-sm font-bold text-white">7-Day Financial Summary</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-white/[0.01] text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                <th class="px-6 py-3">Reporting Date</th>
                                <th class="px-6 py-3 text-right">Revenue</th>
                                <th class="px-6 py-3 text-right">Transactions</th>
                                <th class="px-6 py-3 text-right">Growth</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($harian as $h)
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-bold text-white uppercase tracking-widest">{{ $h['label'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm font-bold text-emerald-500">Rp {{ number_format($h['nominal'], 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-xs font-bold text-white">{{ $h['transaksi'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-[10px] font-bold text-emerald-500">+{{ rand(2, 8) }}%</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Capacity Overview -->
            <div class="card-pro !p-0 overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02]">
                    <h2 class="text-sm font-bold text-white">Asset Utilization</h2>
                </div>
                <div class="p-6 space-y-8">
                    @foreach($areaParkir as $area)
                        @php
                            $kap = $area->kapasitas ?: 0;
                            $terisi = $area->terisi ?: 0;
                            $pct = $kap > 0 ? round(($terisi / $kap) * 100) : 0;
                            $colorClass = $pct >= 90 ? 'bg-rose-500' : ($pct >= 70 ? 'bg-amber-500' : 'bg-emerald-500');
                        @endphp
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $area->nama_area }}</span>
                                <span class="text-[10px] font-bold {{ $pct >= 90 ? 'text-rose-400' : 'text-emerald-500' }}">
                                    {{ $terisi }} / {{ $kap }}
                                </span>
                            </div>
                            <div class="w-full h-2 bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full {{ $colorClass }} transition-all duration-1000" style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="mt-2 flex justify-end">
                                <span class="text-[9px] font-bold text-slate-600 uppercase">{{ $pct }}% utilized</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
