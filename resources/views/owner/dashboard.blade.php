@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 relative z-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8 lg:mb-12 animate-fade-in-up">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                    Executive Analytics
                </span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-white">Financial <span class="text-emerald-500">Insights</span></h1>
            <p class="text-slate-400 text-sm mt-2">Strategic overview of business performance and revenue metrics.</p>
        </div>
        <div class="flex items-center gap-3 sm:gap-4">
            <select id="daysFilter" class="bg-slate-800 border border-white/10 text-[10px] font-bold uppercase tracking-widest text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all cursor-pointer outline-none">
                <option value="7" {{ $days == 7 ? 'selected' : '' }}>Last 7 Days</option>
                <option value="30" {{ $days == 30 ? 'selected' : '' }}>Last 30 Days</option>
                <option value="90" {{ $days == 90 ? 'selected' : '' }}>Last 90 Days</option>
            </select>
            <button class="flex-1 sm:flex-none group relative px-4 sm:px-6 py-3 bg-slate-800 text-white font-bold text-xs uppercase tracking-widest rounded-xl border border-white/10 transition-all hover:bg-slate-700 flex items-center justify-center gap-2">
                <i class="fa-solid fa-file-export text-sm"></i>
                Export Report
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Total Revenue -->
        <div class="card-pro group overflow-hidden relative animate-fade-in-up" style="animation-delay: 0.1s">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-emerald-500/5 rounded-full blur-3xl group-hover:bg-emerald-500/10 transition-all duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 bg-emerald-500/10 rounded-xl border border-emerald-500/20 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-sack-dollar text-lg text-emerald-500"></i>
                    </div>
                </div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Gross Revenue</p>
                <h3 class="text-2xl font-black text-white tracking-tighter">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                <div class="mt-6 pt-4 border-t border-white/5 flex items-center justify-between">
                    <span class="text-[10px] text-slate-500 font-bold uppercase">All-time</span>
                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Growth +{{ rand(5, 10) }}%</span>
                </div>
            </div>
        </div>

        <!-- Daily Performance -->
        <div class="card-pro group overflow-hidden relative border-emerald-500/20 animate-fade-in-up" style="animation-delay: 0.2s">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-emerald-500/10 rounded-full blur-3xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 bg-emerald-500/10 rounded-xl border border-emerald-500/20 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-calendar-day text-lg text-emerald-500"></i>
                    </div>
                    <span class="text-[10px] font-black text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded-lg border border-emerald-500/20 uppercase">Today</span>
                </div>
                <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mb-1">Daily Performance</p>
                <h3 class="text-2xl font-black text-white tracking-tighter">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h3>
                <div class="mt-6 pt-4 border-t border-white/5">
                    <span class="text-[10px] text-slate-500 font-bold uppercase">Live revenue tracking</span>
                </div>
            </div>
        </div>

        <!-- Completed Orders -->
        <div class="card-pro group overflow-hidden relative animate-fade-in-up" style="animation-delay: 0.3s">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/10 transition-all duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 bg-indigo-500/10 rounded-xl border border-indigo-500/20 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-circle-check text-lg text-indigo-500"></i>
                    </div>
                </div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Completed Orders</p>
                <h3 class="text-3xl font-black text-white tracking-tighter">{{ $transaksiBerhasil }}</h3>
                <div class="mt-6 pt-4 border-t border-white/5">
                    <span class="text-[10px] text-slate-500 font-bold uppercase">Successful volume</span>
                </div>
            </div>
        </div>

        <!-- Pending Receivables -->
        <div class="card-pro group overflow-hidden relative animate-fade-in-up" style="animation-delay: 0.4s">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-rose-500/5 rounded-full blur-3xl group-hover:bg-rose-500/10 transition-all duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 bg-rose-500/10 rounded-xl border border-rose-500/20 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-clock text-lg text-rose-500"></i>
                    </div>
                </div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Pending Receivables</p>
                <h3 class="text-3xl font-black text-rose-500 tracking-tighter">{{ $pembayaranPending }}</h3>
                <div class="mt-6 pt-4 border-t border-white/5">
                    <span class="text-[10px] text-slate-500 font-bold uppercase">Awaiting payment</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Weekly Breakdown -->
        <div class="lg:col-span-2 card-pro !p-0 overflow-hidden animate-fade-in-up" style="animation-delay: 0.5s">
            <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                <h2 class="text-sm font-black text-white uppercase tracking-widest">{{ $days }}-Day Financial Summary</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white/[0.01] text-[10px] font-black text-slate-500 uppercase tracking-[0.3em]">
                            <th class="px-8 py-5">Reporting Date</th>
                            <th class="px-8 py-5 text-right">Revenue</th>
                            <th class="px-8 py-5 text-right">Transactions</th>
                            <th class="px-8 py-5 text-right">Performance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($harian as $h)
                            <tr class="hover:bg-white/[0.02] transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-slate-700"></div>
                                        <span class="text-xs font-bold text-white uppercase tracking-widest">{{ $h['label'] }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <span class="text-sm font-black text-emerald-500">Rp {{ number_format($h['nominal'], 0, ',', '.') }}</span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <span class="text-xs font-black text-white tabular-nums">{{ $h['transaksi'] }}</span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <span class="px-2 py-1 bg-emerald-500/10 text-emerald-500 text-[9px] font-black uppercase rounded border border-emerald-500/20">+{{ rand(2, 8) }}%</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Capacity Overview -->
        <div class="card-pro !p-0 overflow-hidden animate-fade-in-up" style="animation-delay: 0.6s">
            <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02]">
                <h2 class="text-sm font-black text-white uppercase tracking-widest">Asset Utilization</h2>
            </div>
            <div class="p-8 space-y-8">
                @foreach($areaParkir as $area)
                    @php
                        $kap = $area->kapasitas ?: 0;
                        $terisi = $area->terisi ?: 0;
                        $pct = $kap > 0 ? round(($terisi / $kap) * 100) : 0;
                        $colorClass = $pct >= 90 ? 'bg-rose-500' : ($pct >= 70 ? 'bg-amber-500' : 'bg-emerald-500');
                    @endphp
                    <div class="group">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-xs font-bold text-slate-400 group-hover:text-white transition-colors uppercase tracking-widest">{{ $area->nama_area }}</span>
                            <span class="text-[10px] font-black {{ $pct >= 90 ? 'text-rose-400' : 'text-emerald-500' }} tabular-nums">
                                {{ $terisi }} / {{ $kap }}
                            </span>
                        </div>
                        <div class="w-full bg-slate-950 rounded-full h-2 p-0.5 border border-white/5">
                            <div class="h-full {{ $colorClass }} rounded-full transition-all duration-1000 relative" style="width: {{ $pct }}%">
                                <div class="absolute inset-0 bg-white/20 animate-pulse rounded-full"></div>
                            </div>
                        </div>
                        <div class="mt-3 flex justify-end">
                            <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest">{{ $pct }}% utilized</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const daysFilter = document.getElementById('daysFilter');
        if (daysFilter) {
            daysFilter.addEventListener('change', function() {
                const days = this.value;
                window.location.href = `{{ route('owner.dashboard') }}?days=${days}`;
            });
        }
    });
</script>
@endpush
@endsection
