@extends('layouts.app')

@section('content')
<div class="p-8 relative z-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 animate-fade-in-up">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                    Enterprise Console
                </span>
            </div>
            <h1 class="text-4xl font-bold tracking-tight text-white">System <span class="text-emerald-500">Overview</span></h1>
            <p class="text-slate-400 text-sm mt-2">Monitoring operational performance and real-time analytics.</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex flex-col items-end">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Current Date</span>
                <div class="inline-flex items-center gap-3 px-4 py-2 bg-slate-900/50 border border-white/5 rounded-xl text-xs font-bold text-white shadow-xl backdrop-blur-md">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></span>
                    {{ now()->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Main Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Revenue Card -->
        <div class="card-pro group overflow-hidden relative animate-fade-in-up" style="animation-delay: 0.1s">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-emerald-500/5 rounded-full blur-3xl group-hover:bg-emerald-500/10 transition-all duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 bg-emerald-500/10 rounded-xl border border-emerald-500/20 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-black text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded-lg border border-emerald-500/20 uppercase">Live</span>
                </div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-1">Revenue Today</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-black text-white tracking-tighter">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h3>
                </div>
                <div class="mt-6 pt-4 border-t border-white/5 flex items-center justify-between">
                    <span class="text-[10px] text-slate-500 font-bold uppercase">Updated now</span>
                    <div class="flex items-center gap-1 text-[10px] font-black text-emerald-500 uppercase tracking-widest">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 10l7-7m0 0l7 7m-7-7v18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                        +{{ rand(2, 5) }}%
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Card -->
        <div class="card-pro group overflow-hidden relative animate-fade-in-up" style="animation-delay: 0.2s">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/10 transition-all duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 bg-indigo-500/10 rounded-xl border border-indigo-500/20 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                </div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-1">Total Transactions</p>
                <h3 class="text-3xl font-black text-white tracking-tighter">{{ $transaksiHariIni }}</h3>
                <div class="mt-6 pt-4 border-t border-white/5 flex items-center justify-between">
                    <span class="text-[10px] text-slate-500 font-bold uppercase">Daily throughput</span>
                    <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">+{{ rand(5, 15) }}% vs avg</span>
                </div>
            </div>
        </div>

        <!-- Active Parking Card -->
        <div class="card-pro group overflow-hidden relative animate-fade-in-up" style="animation-delay: 0.3s">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-amber-500/5 rounded-full blur-3xl group-hover:bg-amber-500/10 transition-all duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 bg-amber-500/10 rounded-xl border border-amber-500/20 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                </div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-1">Active Parking</p>
                <h3 class="text-3xl font-black text-white tracking-tighter">{{ $transaksiAktif }}</h3>
                <div class="mt-6 pt-4 border-t border-white/5 flex items-center justify-between">
                    <span class="text-[10px] text-slate-500 font-bold uppercase">Currently in-lot</span>
                    <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest">High Load</span>
                </div>
            </div>
        </div>

        <!-- Users Card -->
        <div class="card-pro group overflow-hidden relative animate-fade-in-up" style="animation-delay: 0.4s">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-blue-500/5 rounded-full blur-3xl group-hover:bg-blue-500/10 transition-all duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 bg-blue-500/10 rounded-xl border border-blue-500/20 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-1">Total Members</p>
                <h3 class="text-3xl font-black text-white tracking-tighter">{{ $totalUser }}</h3>
                <div class="mt-6 pt-4 border-t border-white/5 flex items-center justify-between">
                    <span class="text-[10px] text-slate-500 font-bold uppercase">Verified accounts</span>
                    <span class="text-[10px] font-black text-blue-500 uppercase tracking-widest">Active</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
        <div class="lg:col-span-2 card-pro !p-0 overflow-hidden animate-fade-in-up" style="animation-delay: 0.5s">
            <div class="p-8 flex items-center justify-between border-b border-white/5 bg-white/[0.01]">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-tight">Revenue Analytics</h2>
                    <p class="text-xs text-slate-500 mt-1 font-medium">Income performance for the last 7 days</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/20 rounded-lg">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">Live Sync</span>
                    </div>
                    <select class="bg-slate-950 border border-white/10 text-[10px] font-bold uppercase tracking-widest text-white rounded-xl px-4 py-2 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all cursor-pointer">
                        <option>Last 7 Days</option>
                        <option>Last 30 Days</option>
                    </select>
                </div>
            </div>
            <div class="p-8">
                <div class="h-[380px]">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card-pro !p-0 overflow-hidden animate-fade-in-up" style="animation-delay: 0.6s">
            <div class="p-8 border-b border-white/5 bg-white/[0.01]">
                <h2 class="text-xl font-bold text-white tracking-tight">Vehicle Distribution</h2>
                <p class="text-xs text-slate-500 mt-1 font-medium">Breakdown by vehicle category</p>
            </div>
            <div class="p-8">
                <div class="h-[260px] flex items-center justify-center relative">
                    <canvas id="vehicleChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="text-center">
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1">Total</p>
                            <p class="text-4xl font-black text-white tracking-tighter">{{ array_sum($grafikKendaraan['data']) }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-10 space-y-5">
                    @php 
                        $totalV = array_sum($grafikKendaraan['data']);
                        $colors = ['#10b981', '#6366f1', '#f59e0b', '#3b82f6'];
                    @endphp
                    @foreach($grafikKendaraan['labels'] as $index => $label)
                        <div class="flex items-center justify-between group cursor-default">
                            <div class="flex items-center gap-4">
                                <div class="w-2.5 h-2.5 rounded-full shadow-[0_0_10px_rgba(0,0,0,0.5)] transition-transform group-hover:scale-125" style="background-color: {{ $colors[$index % count($colors)] }}"></div>
                                <span class="text-xs font-bold text-slate-400 group-hover:text-white transition-colors uppercase tracking-widest">{{ $label }}</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-xs font-black text-white tracking-tight">{{ $grafikKendaraan['data'][$index] }}</span>
                                <div class="w-12 h-1.5 bg-slate-800 rounded-full overflow-hidden">
                                    <div class="h-full transition-all duration-1000" style="background-color: {{ $colors[$index % count($colors)] }}; width: {{ $totalV > 0 ? round($grafikKendaraan['data'][$index] / $totalV * 100) : 0 }}%"></div>
                                </div>
                                <span class="text-[10px] font-black text-slate-600 w-8 text-right">{{ $totalV > 0 ? round($grafikKendaraan['data'][$index] / $totalV * 100) : 0 }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-fade-in-up" style="animation-delay: 0.7s">
        <!-- Monitoring Area -->
        <div class="card-pro !p-0 overflow-hidden">
            <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                <h2 class="text-sm font-black text-white uppercase tracking-widest">Area Monitoring</h2>
                <a href="{{ route('area-parkir.index') }}" class="text-[10px] font-black text-emerald-500 hover:text-emerald-400 uppercase tracking-[0.2em] transition-all">Manage <i class="fa-solid fa-arrow-right ml-1"></i></a>
            </div>
            <div class="p-8 space-y-8">
                @foreach($areaParkir as $area)
                    @php
                        $percent = $area->kapasitas > 0 ? ($area->terisi / $area->kapasitas * 100) : 0;
                        $colorClass = $percent > 90 ? 'bg-rose-500' : ($percent > 70 ? 'bg-amber-500' : 'bg-emerald-500');
                        $glowClass = $percent > 90 ? 'shadow-[0_0_15px_rgba(244,63,94,0.3)]' : ($percent > 70 ? 'shadow-[0_0_15px_rgba(245,158,11,0.3)]' : 'shadow-[0_0_15px_rgba(16,185,129,0.3)]');
                    @endphp
                    <div class="group">
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-1.5 h-1.5 rounded-full {{ $colorClass }}"></div>
                                <span class="text-xs font-bold text-slate-300 group-hover:text-white transition-colors uppercase tracking-widest">{{ $area->nama_area }}</span>
                                @if($percent > 90)
                                    <span class="px-2 py-0.5 bg-rose-500/10 text-rose-500 text-[8px] font-black uppercase rounded border border-rose-500/20 animate-pulse">Critical</span>
                                @endif
                            </div>
                            <span class="text-[10px] font-black text-slate-500 tabular-nums">
                                {{ $area->terisi }} <span class="text-slate-700">/</span> {{ $area->kapasitas }}
                            </span>
                        </div>
                        <div class="w-full bg-slate-950 rounded-full h-2.5 p-0.5 border border-white/5 shadow-inner">
                            <div class="{{ $colorClass }} {{ $glowClass }} h-full rounded-full transition-all duration-1000 relative" style="width: {{ min($percent, 100) }}%">
                                <div class="absolute inset-0 bg-white/20 animate-pulse rounded-full"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Activity Table -->
        <div class="lg:col-span-2 card-pro !p-0 overflow-hidden">
            <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                <h2 class="text-sm font-black text-white uppercase tracking-widest">Live Activity Stream</h2>
                <a href="{{ route('transaksi.index') }}" class="text-[10px] font-black text-emerald-500 hover:text-emerald-400 uppercase tracking-[0.2em] transition-all">Full Logs <i class="fa-solid fa-arrow-right ml-1"></i></a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white/[0.01] text-[10px] font-black text-slate-500 uppercase tracking-[0.3em]">
                            <th class="px-8 py-5">Vehicle Entity</th>
                            <th class="px-8 py-5">Access Point</th>
                            <th class="px-8 py-5">Event Time</th>
                            <th class="px-8 py-5 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($aktivitasTerbaru as $trx)
                            <tr class="hover:bg-white/[0.02] transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-5">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-950 border border-white/10 flex flex-col items-center justify-center font-black text-white group-hover:border-emerald-500/50 transition-all shadow-xl">
                                            <span class="text-[9px] text-slate-500 leading-none mb-1 uppercase tracking-tighter">{{ substr($trx->kendaraan->plat_nomor ?? '-', 0, 2) }}</span>
                                            <span class="text-sm leading-none tracking-tight">{{ substr($trx->kendaraan->plat_nomor ?? '-', 2, 4) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-white tracking-tight">{{ $trx->kendaraan->plat_nomor ?? '-' }}</p>
                                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-[0.1em]">{{ $trx->kendaraan->jenis_kendaraan ?? 'Vehicle' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500/30"></div>
                                        <span class="text-xs text-slate-300 font-bold uppercase tracking-wider">{{ $trx->area->nama_area ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-xs font-black text-white">{{ $trx->waktu_masuk->diffForHumans() }}</p>
                                    <p class="text-[10px] text-slate-500 font-bold uppercase">{{ $trx->waktu_masuk->format('H:i:s') }}</p>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[9px] font-black tracking-widest border {{ $trx->status === 'masuk' ? 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20' : 'bg-slate-800 text-slate-400 border-white/10' }}">
                                        {{ strtoupper($trx->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-24 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-slate-950 rounded-[2.5rem] flex items-center justify-center border border-white/5 mb-6">
                                            <svg class="w-10 h-10 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                                        </div>
                                        <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.3em]">No activity detected in the last 24h</p>
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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Global Chart Defaults for Dark Theme
            Chart.defaults.font.family = "'Plus Jakarta Sans', 'Inter', sans-serif";
            Chart.defaults.color = '#94a3b8';
            Chart.defaults.plugins.tooltip.backgroundColor = '#0f172a';
            Chart.defaults.plugins.tooltip.borderColor = 'rgba(255,255,255,0.1)';
            Chart.defaults.plugins.tooltip.borderWidth = 1;
            Chart.defaults.plugins.tooltip.padding = 12;
            Chart.defaults.plugins.tooltip.titleColor = '#fff';
            Chart.defaults.plugins.tooltip.titleFont = { size: 13, weight: '700' };
            Chart.defaults.plugins.tooltip.bodyFont = { size: 12 };
            Chart.defaults.plugins.tooltip.cornerRadius = 8;

            // Revenue Chart
            const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
            const revenueGradient = ctxRevenue.createLinearGradient(0, 0, 0, 400);
            revenueGradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
            revenueGradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

            new Chart(ctxRevenue, {
                type: 'line',
                data: {
                    labels: @json($grafikPendapatan['labels']),
                    datasets: [{
                        label: 'Revenue',
                        data: @json($grafikPendapatan['data']),
                        borderColor: '#10b981',
                        backgroundColor: revenueGradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#020617',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255, 255, 255, 0.03)', drawBorder: false },
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: { display: false, drawBorder: false }
                        }
                    }
                }
            });

            // Vehicle Distribution Chart
            const ctxVehicle = document.getElementById('vehicleChart').getContext('2d');
            new Chart(ctxVehicle, {
                type: 'doughnut',
                data: {
                    labels: @json($grafikKendaraan['labels']),
                    datasets: [{
                        data: @json($grafikKendaraan['data']),
                        backgroundColor: ['#10b981', '#6366f1', '#f59e0b', '#3b82f6'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '80%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        });
    </script>
@endpush
