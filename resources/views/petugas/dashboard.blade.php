@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 relative z-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8 lg:mb-12 animate-fade-in-up">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                    Operational Terminal
                </span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-white">Officer <span class="text-emerald-500">Console</span></h1>
            <p class="text-slate-400 text-sm mt-2">Managing parking operations and live vehicle tracking.</p>
        </div>
        <div class="flex items-center gap-3 sm:gap-4">
            <a href="{{ route('anpr.index') }}" class="flex-1 sm:flex-none group relative px-4 sm:px-6 py-3 bg-emerald-500 text-slate-950 font-bold text-xs uppercase tracking-widest rounded-xl transition-all hover:bg-emerald-400 hover:shadow-[0_0_20px_rgba(16,185,129,0.4)] flex items-center justify-center gap-2">
                <i class="fa-solid fa-camera-viewfinder text-sm"></i>
                Camera Monitor
            </a>
            <a href="{{ route('parkir.scan') }}" class="flex-1 sm:flex-none group relative px-4 sm:px-6 py-3 bg-slate-800 text-white font-bold text-xs uppercase tracking-widest rounded-xl border border-white/10 transition-all hover:bg-slate-700 flex items-center justify-center gap-2">
                <i class="fa-solid fa-id-card text-sm"></i>
                RFID Terminal
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-8 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if(!empty($needsOperationalArea))
        <div class="mb-10 p-8 rounded-3xl bg-amber-500/5 border border-amber-500/20 animate-fade-in-up">
            <div class="flex flex-col lg:flex-row lg:items-end gap-6 justify-between">
                <div class="max-w-xl">
                    <h4 class="text-white font-black uppercase tracking-widest text-xs mb-2 text-amber-400">Kode peta diperlukan</h4>
                    <p class="text-slate-400 text-sm leading-relaxed">Masukkan <strong class="text-white">Kode Peta</strong> dari admin — sama persis dengan kolom Kode Peta pada data Area Parkir. Tanpa ini, statistik dashboard, Camera Monitor, dan auto slot di terminal RFID tidak terikat ke area Anda.</p>
                </div>
                <form method="POST" action="{{ route('operational-area.set') }}" class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto shrink-0">
                    @csrf
                    <input type="text" name="kode_peta" value="{{ old('kode_peta') }}" required placeholder="Kode peta"
                           class="min-w-[200px] px-5 py-3.5 bg-slate-950 border border-white/10 rounded-xl text-white text-sm placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500/50 @error('kode_peta') border-rose-500 @enderror">
                    <button type="submit" class="px-8 py-3.5 bg-amber-500 text-slate-950 font-black text-xs uppercase tracking-widest rounded-xl hover:bg-amber-400 transition-colors">
                        Aktifkan area
                    </button>
                </form>
            </div>
            @error('kode_peta')
                <p class="mt-4 text-sm text-rose-400 font-medium">{{ $message }}</p>
            @enderror
        </div>
    @elseif(!empty($operationalArea))
        <div class="mb-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-6 rounded-3xl bg-emerald-500/5 border border-emerald-500/20">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 flex items-center justify-center text-emerald-400">
                    <i class="fa-solid fa-map-location-dot text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Area tugas aktif</p>
                    <p class="text-white font-bold">{{ $operationalArea->nama_area }} <span class="text-slate-500 font-mono text-sm">· {{ $operationalArea->map_code }}</span></p>
                </div>
            </div>
            <form method="POST" action="{{ route('operational-area.clear') }}" class="shrink-0">
                @csrf
                <button type="submit" class="px-5 py-2.5 rounded-xl border border-white/10 text-slate-400 text-[10px] font-black uppercase tracking-widest hover:bg-white/5 hover:text-white transition-colors">
                    Ganti area
                </button>
            </form>
        </div>
    @endif

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Active Parking -->
        <div class="card-pro group overflow-hidden relative animate-fade-in-up" style="animation-delay: 0.1s">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-emerald-500/5 rounded-full blur-3xl group-hover:bg-emerald-500/10 transition-all duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 bg-emerald-500/10 rounded-xl border border-emerald-500/20 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-car-side text-lg text-emerald-500"></i>
                    </div>
                    <span class="flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                </div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Active Parking</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-black text-white tracking-tighter">{{ $transaksiAktif }}</h3>
                </div>
                <div class="mt-6 pt-4 border-t border-white/5 flex items-center justify-between">
                    <span class="text-[10px] text-slate-500 font-bold uppercase">Live tracking</span>
                    <a href="{{ route('transaksi.active') }}" class="text-[10px] font-black text-emerald-500 uppercase tracking-widest hover:text-emerald-400 transition-colors">Manage <i class="fa-solid fa-arrow-right ml-1"></i></a>
                </div>
            </div>
        </div>

        <!-- Entries Today -->
        <div class="card-pro group overflow-hidden relative animate-fade-in-up" style="animation-delay: 0.2s">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/10 transition-all duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 bg-indigo-500/10 rounded-xl border border-indigo-500/20 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-arrow-right-to-bracket text-lg text-indigo-500"></i>
                    </div>
                </div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Entries Today</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-black text-white tracking-tighter">{{ $transaksiHariIni }}</h3>
                </div>
                <div class="mt-6 pt-4 border-t border-white/5 flex items-center justify-between">
                    <span class="text-[10px] text-slate-500 font-bold uppercase">Daily throughput</span>
                    <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">Processed</span>
                </div>
            </div>
        </div>

        <!-- Capacity Usage -->
        <div class="card-pro group overflow-hidden relative animate-fade-in-up" style="animation-delay: 0.3s">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-amber-500/5 rounded-full blur-3xl group-hover:bg-amber-500/10 transition-all duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 bg-amber-500/10 rounded-xl border border-amber-500/20 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-gauge-high text-lg text-amber-500"></i>
                    </div>
                </div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Total Capacity</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-black text-amber-500 tracking-tighter">{{ $totalTerisi }} <span class="text-slate-500 text-lg font-medium">/ {{ $totalKapasitas }}</span></h3>
                </div>
                <div class="mt-5 w-full bg-slate-800 rounded-full h-2 p-0.5 border border-white/5">
                    @php $percent = $totalKapasitas > 0 ? ($totalTerisi / $totalKapasitas) * 100 : 0; @endphp
                    <div class="bg-amber-500 h-full rounded-full transition-all duration-1000 relative" style="width: {{ min($percent, 100) }}%">
                        <div class="absolute inset-0 bg-white/20 animate-pulse rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Bookings -->
        <div class="card-pro group overflow-hidden relative animate-fade-in-up" style="animation-delay: 0.4s">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-blue-500/5 rounded-full blur-3xl group-hover:bg-blue-500/10 transition-all duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 bg-blue-500/10 rounded-xl border border-blue-500/20 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-bookmark text-lg text-blue-500"></i>
                    </div>
                    @if($bookingAktif > 0)
                        <span class="flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                        </span>
                    @endif
                </div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Active Bookings</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-black text-white tracking-tighter">{{ $bookingAktif }}</h3>
                </div>
                <div class="mt-6 pt-4 border-t border-white/5 flex items-center justify-between">
                    <span class="text-[10px] text-slate-500 font-bold uppercase">Pending arrival</span>
                    <a href="{{ route('transaksi.bookings') }}" class="text-[10px] font-black text-blue-500 uppercase tracking-widest hover:text-blue-400 transition-colors">Manage <i class="fa-solid fa-arrow-right ml-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Today (Moved to a separate row or integrated elsewhere if needed, but let's keep it clean) -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-10">
        <div class="lg:col-span-4 card-pro group overflow-hidden relative animate-fade-in-up" style="animation-delay: 0.5s">
            <div class="absolute right-0 top-0 w-64 h-full bg-emerald-500/5 blur-3xl"></div>
            <div class="p-8 flex flex-col md:flex-row items-center justify-between gap-8 relative z-10">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 border border-emerald-500/20">
                        <i class="fa-solid fa-sack-dollar text-3xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Total Revenue Today</p>
                        <h3 class="text-4xl font-black text-white tracking-tighter">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h3>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right hidden md:block">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">System Health</p>
                        <p class="text-xs font-black text-emerald-500 uppercase tracking-widest">Operational & Secure</p>
                    </div>
                    <div class="w-px h-12 bg-white/5 mx-4 hidden md:block"></div>
                    <a href="{{ route('payment.index') }}" class="px-8 py-4 bg-white/5 hover:bg-white/10 text-white font-bold text-xs uppercase tracking-widest rounded-2xl border border-white/10 transition-all active:scale-95">
                        View Detailed Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Info Section -->
        <div class="space-y-8 animate-fade-in-up" style="animation-delay: 0.5s">
            <!-- Area Monitoring -->
            <div class="card-pro !p-0 overflow-hidden">
                <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02]">
                    <h2 class="text-sm font-black text-white uppercase tracking-widest">Area Monitoring</h2>
                </div>
                <div class="p-8 space-y-8">
                    @foreach($areaParkir as $area)
                        @php
                            $percentArea = $area->kapasitas > 0 ? ($area->terisi / $area->kapasitas) * 100 : 0;
                            $colorClass = $percentArea >= 90 ? 'bg-rose-500' : ($percentArea >= 70 ? 'bg-amber-500' : 'bg-emerald-500');
                        @endphp
                        <div class="group">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-[10px] font-bold text-slate-400 group-hover:text-white transition-colors uppercase tracking-widest">{{ $area->nama_area }}</span>
                                <span class="text-[10px] font-black {{ $percentArea >= 90 ? 'text-rose-400' : 'text-emerald-500' }} tabular-nums">
                                    {{ $area->terisi }} <span class="text-slate-700">/</span> {{ $area->kapasitas }}
                                </span>
                            </div>
                            <div class="w-full bg-slate-950 rounded-full h-2 p-0.5 border border-white/5">
                                <div class="{{ $colorClass }} h-full rounded-full transition-all duration-1000" style="width: {{ min($percentArea, 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Navigation -->
            <div class="card-pro !p-0 overflow-hidden">
                <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02]">
                    <h2 class="text-sm font-black text-white uppercase tracking-widest">Quick Navigation</h2>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4">
                    <a href="{{ route('transaksi.index', ['status' => 'aktif']) }}" class="p-4 rounded-2xl bg-slate-950 border border-white/5 hover:border-emerald-500/50 transition-all group flex flex-col items-center text-center">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 group-hover:scale-110 transition-transform mb-3">
                            <i class="fa-solid fa-car-side text-base"></i>
                        </div>
                        <span class="text-[9px] font-black text-slate-500 group-hover:text-white uppercase tracking-widest transition-colors">Active</span>
                    </a>
                    <a href="{{ route('transaksi.index', ['status' => 'booking']) }}" class="p-4 rounded-2xl bg-slate-950 border border-white/5 hover:border-blue-500/50 transition-all group flex flex-col items-center text-center">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform mb-3">
                            <i class="fa-solid fa-bookmark text-base"></i>
                        </div>
                        <span class="text-[9px] font-black text-slate-500 group-hover:text-white uppercase tracking-widest transition-colors">Bookings</span>
                    </a>
                    <a href="{{ route('transaksi.index', ['status' => 'riwayat']) }}" class="p-4 rounded-2xl bg-slate-950 border border-white/5 hover:border-indigo-500/50 transition-all group flex flex-col items-center text-center">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 group-hover:scale-110 transition-transform mb-3">
                            <i class="fa-solid fa-clock-rotate-left text-base"></i>
                        </div>
                        <span class="text-[9px] font-black text-slate-500 group-hover:text-white uppercase tracking-widest transition-colors">History</span>
                    </a>
                    <a href="{{ route('parking.map.index') }}" class="p-4 rounded-2xl bg-slate-950 border border-white/5 hover:border-amber-500/50 transition-all group flex flex-col items-center text-center">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500 group-hover:scale-110 transition-transform mb-3">
                            <i class="fa-solid fa-map-location-dot text-base"></i>
                        </div>
                        <span class="text-[9px] font-black text-slate-500 group-hover:text-white uppercase tracking-widest transition-colors">Live Map</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity Stream -->
        <div class="lg:col-span-2 card-pro !p-0 overflow-hidden animate-fade-in-up" style="animation-delay: 0.6s">
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
                            <th class="px-8 py-5">Timestamp</th>
                            <th class="px-8 py-5 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($aktivitasTerbaru as $trx)
                            <tr class="hover:bg-white/[0.02] transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-5">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-950 border border-white/10 flex flex-col items-center justify-center font-black text-white group-hover:border-emerald-500/50 transition-all shadow-xl">
                                            <span class="text-[9px] text-slate-500 leading-none mb-1 uppercase tracking-tighter">{{ substr($trx->kendaraan?->plat_nomor ?? '-', 0, 2) }}</span>
                                            <span class="text-sm leading-none tracking-tight">{{ substr($trx->kendaraan?->plat_nomor ?? '-', 2, 4) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-white tracking-tight">{{ $trx->kendaraan?->plat_nomor ?? '-' }}</p>
                                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-[0.1em]">{{ $trx->kendaraan?->jenis_kendaraan ?? 'Vehicle' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500/30"></div>
                                        <span class="text-xs text-slate-300 font-bold uppercase tracking-wider">{{ $trx->area?->nama_area ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    @php
                                        $displayTime = $trx->status === 'bookmarked' ? $trx->bookmarked_at : $trx->waktu_masuk;
                                    @endphp
                                    <p class="text-xs font-black text-white">{{ $displayTime ? $displayTime->format('H:i') : '-' }}</p>
                                    <p class="text-[10px] text-slate-500 font-bold uppercase">{{ $displayTime ? $displayTime->translatedFormat('d M Y') : '-' }}</p>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    @if($trx->status === 'bookmarked')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[9px] font-black tracking-widest border bg-blue-500/10 text-blue-400 border-blue-500/20">
                                            BOOKING
                                        </span>
                                    @elseif($trx->status === 'masuk')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[9px] font-black tracking-widest border bg-emerald-500/10 text-emerald-500 border-emerald-500/20">
                                            IN-PARK
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[9px] font-black tracking-widest border bg-slate-800 text-slate-400 border-white/10">
                                            EXITED
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-24 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-slate-950 rounded-[2.5rem] flex items-center justify-center border border-white/5 mb-6">
                                            <svg class="w-10 h-10 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                                        </div>
                                        <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.3em]">No recent activities</p>
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


