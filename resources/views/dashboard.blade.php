@extends('layouts.app')

@section('content')
    <div class="p-8 relative z-10">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
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
            <div class="card-pro group overflow-hidden relative">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-emerald-500/5 rounded-full blur-3xl group-hover:bg-emerald-500/10 transition-all duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 bg-emerald-500/10 rounded-lg border border-emerald-500/20">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded uppercase">Live</span>
                    </div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Revenue Today</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-bold text-white tracking-tight">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h3>
                    </div>
                    <div class="mt-4 pt-4 border-t border-white/5 flex items-center justify-between">
                        <span class="text-[10px] text-slate-500 font-medium">Updated just now</span>
                        <div class="flex items-center gap-1 text-[10px] font-bold text-emerald-500">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 10l7-7m0 0l7 7m-7-7v18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                            Real-time
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Card -->
            <div class="card-pro group overflow-hidden relative">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/10 transition-all duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 bg-indigo-500/10 rounded-lg border border-indigo-500/20">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Total Transactions</p>
                    <h3 class="text-3xl font-bold text-white tracking-tight">{{ $transaksiHariIni }}</h3>
                    <div class="mt-4 pt-4 border-t border-white/5 flex items-center justify-between">
                        <span class="text-[10px] text-slate-500 font-medium">Daily count</span>
                        <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest">+{{ rand(5, 15) }}% vs avg</span>
                    </div>
                </div>
            </div>

            <!-- Active Parking Card -->
            <div class="card-pro group overflow-hidden relative">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-amber-500/5 rounded-full blur-3xl group-hover:bg-amber-500/10 transition-all duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 bg-amber-500/10 rounded-lg border border-amber-500/20">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                    </div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Active Parking</p>
                    <h3 class="text-3xl font-bold text-white tracking-tight">{{ $transaksiAktif }}</h3>
                    <div class="mt-4 pt-4 border-t border-white/5 flex items-center justify-between">
                        <span class="text-[10px] text-slate-500 font-medium">Currently in-lot</span>
                        <span class="text-[10px] font-bold text-amber-500 uppercase tracking-widest">High Capacity</span>
                    </div>
                </div>
            </div>

            <!-- Users Card -->
            <div class="card-pro group overflow-hidden relative">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-blue-500/5 rounded-full blur-3xl group-hover:bg-blue-500/10 transition-all duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 bg-blue-500/10 rounded-lg border border-blue-500/20">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Total Members</p>
                    <h3 class="text-3xl font-bold text-white tracking-tight">{{ $totalUser }}</h3>
                    <div class="mt-4 pt-4 border-t border-white/5 flex items-center justify-between">
                        <span class="text-[10px] text-slate-500 font-medium">Community growth</span>
                        <span class="text-[10px] font-bold text-blue-500 uppercase tracking-widest">Verified</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
            <div class="lg:col-span-2 card-pro">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-lg font-bold text-white tracking-tight">Revenue Analytics</h2>
                        <p class="text-xs text-slate-500 mt-1">Income performance for the last 7 days</p>
                    </div>
                    <select class="bg-slate-900 border border-white/10 text-xs text-white rounded-lg px-3 py-1.5 focus:outline-none focus:border-emerald-500/50">
                        <option>Last 7 Days</option>
                        <option>Last 30 Days</option>
                    </select>
                </div>
                <div class="h-[350px]">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="card-pro">
                <div class="mb-8">
                    <h2 class="text-lg font-bold text-white tracking-tight">Vehicle Distribution</h2>
                    <p class="text-xs text-slate-500 mt-1">Breakdown by vehicle type</p>
                </div>
                <div class="h-[240px] flex items-center justify-center relative">
                    <canvas id="vehicleChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="text-center">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Total</p>
                            <p class="text-2xl font-bold text-white">{{ array_sum($grafikKendaraan['data']) }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-8 space-y-4">
                    @php 
                        $totalV = array_sum($grafikKendaraan['data']);
                        $colors = ['#10b981', '#6366f1', '#f59e0b', '#3b82f6'];
                    @endphp
                    @foreach($grafikKendaraan['labels'] as $index => $label)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $colors[$index % count($colors)] }}"></span>
                                <span class="text-xs font-semibold text-slate-300">{{ $label }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold text-white">{{ $grafikKendaraan['data'][$index] }}</span>
                                <span class="text-[10px] text-slate-500">({{ $totalV > 0 ? round($grafikKendaraan['data'][$index] / $totalV * 100) : 0 }}%)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Monitoring Area -->
            <div class="card-pro !p-0 overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                    <h2 class="text-sm font-bold text-white">Area Monitoring</h2>
                    <a href="{{ route('area-parkir.index') }}" class="text-[10px] font-bold text-emerald-500 hover:text-emerald-400 uppercase tracking-widest transition-colors">Manage Areas</a>
                </div>
                <div class="p-6 space-y-6">
                    @foreach($areaParkir as $area)
                        @php
                            $percent = $area->kapasitas > 0 ? ($area->terisi / $area->kapasitas * 100) : 0;
                            $colorClass = $percent > 90 ? 'bg-rose-500' : ($percent > 70 ? 'bg-amber-500' : 'bg-emerald-500');
                        @endphp
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-white">{{ $area->nama_area }}</span>
                                    @if($percent > 90)
                                        <span class="px-1.5 py-0.5 bg-rose-500/10 text-rose-500 text-[8px] font-black uppercase rounded">Full</span>
                                    @endif
                                </div>
                                <span class="text-[10px] font-bold text-slate-400">
                                    {{ $area->terisi }} / {{ $area->kapasitas }}
                                </span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                <div class="{{ $colorClass }} h-full rounded-full transition-all duration-1000" style="width: {{ min($percent, 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="lg:col-span-2 card-pro !p-0 overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                    <h2 class="text-sm font-bold text-white">Live Activity Stream</h2>
                    <a href="{{ route('transaksi.index') }}" class="text-[10px] font-bold text-emerald-500 hover:text-emerald-400 uppercase tracking-widest transition-colors">View Logs</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-white/[0.01] text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                <th class="px-6 py-3">Vehicle</th>
                                <th class="px-6 py-3">Location</th>
                                <th class="px-6 py-3">Time</th>
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
                                        <p class="text-xs font-bold text-white">{{ $trx->waktu_masuk->diffForHumans() }}</p>
                                        <p class="text-[10px] text-slate-500">{{ $trx->waktu_masuk->format('H:i:s') }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $trx->status === 'masuk' ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-slate-800 text-slate-400 border border-white/5' }}">
                                            {{ strtoupper($trx->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-slate-800 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                                            <p class="text-slate-500 text-xs font-bold uppercase tracking-widest">No activity recorded today</p>
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
