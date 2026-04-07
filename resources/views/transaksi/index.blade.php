@extends('layouts.app')

@section('title', $title ?? 'Transactions')

@section('content')
<div class="p-8 relative z-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                    Financial Ledger
                </span>
            </div>
            <h1 class="text-4xl font-bold tracking-tight text-white">{{ explode(' ', $title ?? 'Parking History')[0] }} <span class="text-emerald-500">{{ explode(' ', $title ?? 'Parking History')[1] ?? '' }}</span></h1>
            <p class="text-slate-400 text-sm mt-2">Comprehensive logs of all completed vehicle movements and payments.</p>
        </div>
    </div>

    <!-- Success/Error Alerts -->
    @if($message = Session::get('success'))
        <div class="mb-8 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-4 flex items-center gap-4 animate-fade-in">
            <div class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center text-emerald-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <p class="text-sm font-bold text-emerald-500 uppercase tracking-widest">{{ $message }}</p>
        </div>
    @endif

    @if($message = Session::get('error'))
        <div class="mb-8 bg-rose-500/10 border border-rose-500/20 rounded-2xl p-4 flex items-center gap-4 animate-fade-in">
            <div class="w-8 h-8 bg-rose-500/20 rounded-lg flex items-center justify-center text-rose-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="text-sm font-bold text-rose-500 uppercase tracking-widest">{{ $message }}</p>
        </div>
    @endif

    <!-- Filter Console -->
    <div class="card-pro mb-10 border-white/5 bg-white/[0.02]">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-6 h-6 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            </div>
            <h2 class="text-[10px] font-black text-white uppercase tracking-widest">Advanced Filtering</h2>
        </div>
        
        <form action="{{ route('transaksi.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <div class="lg:col-span-2">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Plate Number</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by plate..."
                           class="block w-full px-4 py-3 bg-slate-950 border border-white/5 rounded-xl text-xs text-white placeholder:text-slate-700 focus:outline-none focus:border-emerald-500/50 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Start Date</label>
                    <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                           class="block w-full px-4 py-3 bg-slate-950 border border-white/5 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500/50 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">End Date</label>
                    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                           class="block w-full px-4 py-3 bg-slate-950 border border-white/5 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500/50 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Parking Zone</label>
                    <select name="id_area"
                            class="block w-full px-4 py-3 bg-slate-950 border border-white/5 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500/50 transition-all">
                        <option value="">All Areas</option>
                        @foreach(\App\Models\AreaParkir::all() as $area)
                            <option value="{{ $area->id_area }}" {{ request('id_area') == $area->id_area ? 'selected' : '' }}>
                                {{ $area->nama_area }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('transaksi.index') }}" class="px-6 py-2.5 bg-slate-800 text-slate-400 font-bold text-[10px] uppercase tracking-widest rounded-xl border border-white/5 hover:bg-slate-700 transition-all">Reset</a>
                <button type="submit" class="px-8 py-2.5 bg-emerald-500 text-slate-950 font-bold text-[10px] uppercase tracking-widest rounded-xl hover:bg-emerald-400 transition-all">Apply Parameters</button>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="card-pro !p-0 overflow-hidden shadow-2xl">
        <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
            <h2 class="text-sm font-bold text-white uppercase tracking-widest">Transaction Records</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/[0.01] text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        <th class="px-8 py-4">Ref ID</th>
                        <th class="px-8 py-4">Vehicle</th>
                        <th class="px-8 py-4">Timeline</th>
                        <th class="px-8 py-4">Metrics</th>
                        <th class="px-8 py-4">Revenue</th>
                        <th class="px-8 py-4">Status</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($transaksis as $transaksi)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-8 py-5">
                            <span class="text-[10px] font-mono font-bold text-emerald-500/80">#{{ str_pad($transaksi->id_parkir, 8, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-slate-800 border border-white/5 flex flex-col items-center justify-center font-bold text-white group-hover:border-emerald-500/30 transition-colors">
                                    <span class="text-[8px] text-slate-500 leading-none mb-0.5">{{ substr($transaksi->kendaraan->plat_nomor ?? '-', 0, 2) }}</span>
                                    <span class="text-xs leading-none">{{ substr($transaksi->kendaraan->plat_nomor ?? '-', 2, 4) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white tracking-tight">{{ $transaksi->kendaraan->plat_nomor ?? '-' }}</p>
                                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">{{ $transaksi->area->nama_area ?? 'Zone' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span class="text-xs font-medium text-slate-300">{{ $transaksi->waktu_masuk->format('d M, H:i') }}</span>
                                </div>
                                @if($transaksi->waktu_keluar)
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    <span class="text-xs font-medium text-slate-300">{{ $transaksi->waktu_keluar->format('d M, H:i') }}</span>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            @if($transaksi->durasi_jam)
                                <div class="inline-flex flex-col">
                                    <span class="text-sm font-bold text-white">{{ $transaksi->durasi_jam }} <span class="text-[10px] text-slate-500 font-bold uppercase">Hours</span></span>
                                    <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest">Total Duration</span>
                                </div>
                            @else
                                <span class="text-[10px] font-black text-slate-700 uppercase italic tracking-widest">Active Session</span>
                            @endif
                        </td>
                        <td class="px-8 py-5">
                            @if($transaksi->biaya_total)
                                <div class="inline-flex flex-col">
                                    <span class="text-sm font-bold text-emerald-500">Rp {{ number_format($transaksi->biaya_total, 0, ',', '.') }}</span>
                                    <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest">Gross total</span>
                                </div>
                            @else
                                <span class="text-[10px] font-black text-slate-700 uppercase italic tracking-widest">Uncalculated</span>
                            @endif
                        </td>
                        <td class="px-8 py-5">
                            @if($transaksi->status === 'masuk')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[8px] font-black uppercase bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">Active</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[8px] font-black uppercase bg-slate-800 text-slate-500 border border-white/5">Archived</span>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-right space-x-1">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('transaksi.show', $transaksi->id_parkir) }}"
                                   class="p-2 bg-slate-800 hover:bg-emerald-500 text-slate-500 hover:text-slate-950 rounded-lg border border-white/5 transition-all"
                                   title="View Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>

                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('transaksi.edit', $transaksi->id_parkir) }}"
                                       class="p-2 bg-amber-500/10 hover:bg-amber-500 text-amber-500 hover:text-slate-950 rounded-lg border border-amber-500/20 transition-all"
                                       title="Edit Record">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>

                                    @if($transaksi->status === 'keluar')
                                        <a href="{{ route('transaksi.print', $transaksi->id_parkir) }}"
                                           class="p-2 bg-indigo-500/10 hover:bg-indigo-500 text-indigo-500 hover:text-white rounded-lg border border-indigo-500/20 transition-all"
                                           title="Print Receipt">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        </a>
                                    @endif

                                    <form action="{{ route('transaksi.destroy', $transaksi->id_parkir) }}" method="POST" class="inline" onsubmit="return confirm('Archive this transaction record? Financial data will be preserved but entry will be hidden.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white rounded-lg border border-rose-500/20 transition-all"
                                                title="Delete Record">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-slate-900 border border-white/5 rounded-[2rem] flex items-center justify-center text-slate-700 mb-6">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                                </div>
                                <h3 class="text-lg font-bold text-white mb-2">No records found</h3>
                                <p class="text-slate-500 text-sm max-w-xs mx-auto">Try adjusting your filters or search criteria to find specific logs.</p>
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
