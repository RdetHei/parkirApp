@extends('layouts.app')

@section('title', 'Report Transaksi')

@section('content')
<div class="p-8 relative z-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                    Operational Insights
                </span>
            </div>
            <h1 class="text-4xl font-bold tracking-tight text-white">Parking <span class="text-emerald-500">Reports</span></h1>
            <p class="text-slate-400 text-sm mt-2">Comprehensive analysis of all parking entries, exits, and duration metrics.</p>
        </div>
        <div class="flex items-center gap-4">
            <form action="{{ route('report.transaksi.export-csv') }}" method="GET" class="inline-flex">
                <input type="hidden" name="tanggal_dari" value="{{ request('tanggal_dari') }}">
                <input type="hidden" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="id_area" value="{{ request('id_area') }}">
                <button type="submit" class="group relative px-6 py-3 bg-emerald-500 text-slate-950 font-bold text-xs uppercase tracking-widest rounded-xl transition-all hover:bg-emerald-400 hover:shadow-[0_0_20px_rgba(16,185,129,0.4)] flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export CSV
                </button>
            </form>
        </div>
    </div>

    <!-- Filter Console -->
    <div class="card-pro mb-10 border-white/5 bg-white/[0.02]">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-6 h-6 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            </div>
            <h2 class="text-[10px] font-black text-white uppercase tracking-widest">Report Parameters</h2>
        </div>

        <form action="{{ route('report.transaksi') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Search Plate</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Plat nomor..."
                           class="block w-full px-4 py-3 bg-slate-950 border border-white/5 rounded-xl text-xs text-white placeholder:text-slate-700 focus:outline-none focus:border-emerald-500/50 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Start Date</label>
                    <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                           class="block w-full px-4 py-3 bg-slate-950 border border-white/5 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500/50 transition-all [color-scheme:dark]">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">End Date</label>
                    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                           class="block w-full px-4 py-3 bg-slate-950 border border-white/5 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500/50 transition-all [color-scheme:dark]">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Status</label>
                    <select name="status"
                            class="block w-full px-4 py-3 bg-slate-950 border border-white/5 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500/50 transition-all">
                        <option value="" class="bg-slate-900">All Statuses</option>
                        <option value="masuk" class="bg-slate-900" {{ request('status') === 'masuk' ? 'selected' : '' }}>Masuk</option>
                        <option value="keluar" class="bg-slate-900" {{ request('status') === 'keluar' ? 'selected' : '' }}>Keluar</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Area</label>
                    <select name="id_area"
                            class="block w-full px-4 py-3 bg-slate-950 border border-white/5 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500/50 transition-all">
                        <option value="" class="bg-slate-900">All Areas</option>
                        @foreach(\App\Models\AreaParkir::orderBy('nama_area')->get() as $area)
                            <option value="{{ $area->id_area }}" class="bg-slate-900" {{ request('id_area') == $area->id_area ? 'selected' : '' }}>
                                {{ $area->nama_area }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('report.transaksi') }}" class="px-6 py-2.5 bg-slate-800 text-slate-400 font-bold text-[10px] uppercase tracking-widest rounded-xl border border-white/5 hover:bg-slate-700 transition-all">Reset</a>
                <button type="submit" class="px-8 py-2.5 bg-emerald-500 text-slate-950 font-bold text-[10px] uppercase tracking-widest rounded-xl hover:bg-emerald-400 transition-all">Generate Report</button>
            </div>
        </form>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        <div class="card-pro border-white/5 bg-white/[0.02]">
            <p class="text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-2">Total Transactions</p>
            <p class="text-2xl font-bold text-white">{{ $total_transaksi }}</p>
        </div>
        <div class="card-pro border-white/5 bg-white/[0.02]">
            <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest mb-2">Total Revenue</p>
            <p class="text-2xl font-bold text-white">Rp {{ number_format($total_biaya, 0, ',', '.') }}</p>
        </div>
        <div class="card-pro border-white/5 bg-white/[0.02]">
            <p class="text-[10px] font-bold text-purple-400 uppercase tracking-widest mb-2">Avg Duration</p>
            <p class="text-2xl font-bold text-white">{{ number_format($durasi_rata, 1, ',', '.') }} <span class="text-xs font-bold text-slate-600 uppercase ml-1">Hrs</span></p>
        </div>
        <div class="card-pro border-white/5 bg-white/[0.02]">
            <p class="text-[10px] font-bold text-amber-400 uppercase tracking-widest mb-2">Active Period</p>
            <p class="text-sm font-bold text-white leading-snug">
                {{ request('tanggal_dari') ? \Illuminate\Support\Carbon::parse(request('tanggal_dari'))->format('d M Y') : 'Start' }}
                <span class="text-slate-500 font-normal">to</span>
                {{ request('tanggal_sampai') ? \Illuminate\Support\Carbon::parse(request('tanggal_sampai'))->format('d M Y') : 'Now' }}
            </p>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card-pro !p-0 overflow-hidden shadow-2xl">
        <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
            <h2 class="text-sm font-bold text-white uppercase tracking-widest">Transaction Audit Logs</h2>
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $transaksis->total() }} records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/[0.01] text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        <th class="px-8 py-4">Session ID</th>
                        <th class="px-8 py-4">Vehicle</th>
                        <th class="px-8 py-4">Area</th>
                        <th class="px-8 py-4">Entry Time</th>
                        <th class="px-8 py-4">Exit Time</th>
                        <th class="px-8 py-4">Cost</th>
                        <th class="px-8 py-4 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($transaksis as $item)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-8 py-5">
                                <span class="text-[10px] font-mono font-bold text-emerald-500/80">#{{ str_pad($item->id_parkir, 8, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-900 border border-white/5 flex items-center justify-center text-slate-400 group-hover:text-white transition-colors">
                                        <i class="fa-solid fa-car-side text-xs"></i>
                                    </div>
                                    <span class="text-sm font-black text-white tracking-tight uppercase">{{ $item->kendaraan?->plat_nomor ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-xs font-medium text-slate-300">
                                {{ $item->area?->nama_area ?? '—' }}
                            </td>
                            <td class="px-8 py-5">
                                <div class="text-xs font-bold text-white">{{ $item->waktu_masuk?->format('d M Y') ?? '—' }}</div>
                                <div class="text-[10px] text-slate-500 mt-0.5 font-semibold">{{ $item->waktu_masuk?->format('H:i') ?? '—' }}</div>
                            </td>
                            <td class="px-8 py-5">
                                @if($item->waktu_keluar)
                                    <div class="text-xs font-bold text-white">{{ $item->waktu_keluar->format('d M Y') }}</div>
                                    <div class="text-[10px] text-slate-500 mt-0.5 font-semibold">{{ $item->waktu_keluar->format('H:i') }}</div>
                                @else
                                    <span class="text-xs text-slate-600 italic">Still Parked</span>
                                @endif
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-sm font-bold text-emerald-400">Rp {{ number_format($item->biaya_total, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                @if($item->status === 'masuk')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[8px] font-black uppercase bg-blue-500/10 text-blue-500 border border-blue-500/20">Parked</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[8px] font-black uppercase bg-slate-800 text-slate-500 border border-white/5">Completed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-24 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-slate-900 border border-white/5 rounded-[2rem] flex items-center justify-center text-slate-700 mb-6">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-white mb-2">No transaction records</h3>
                                    <p class="text-slate-500 text-sm max-w-xs mx-auto">Try adjusting your filters or search criteria to find specific transactions.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transaksis->hasPages())
            <div class="px-8 py-6 border-t border-white/5 bg-white/[0.01]">
                {{ $transaksis->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
