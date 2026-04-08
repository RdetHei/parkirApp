@extends('layouts.app')

@section('title', 'Report Transaksi')

@section('content')
<div class="min-h-screen" style="background:#020617;">
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Report Transaksi</h1>
            <p class="text-slate-500 text-sm mt-0.5">Data parkir masuk & keluar beserta rincian biaya.</p>
        </div>
        <form action="{{ route('report.transaksi.export-csv') }}" method="GET">
            <input type="hidden" name="tanggal_dari"   value="{{ request('tanggal_dari') }}">
            <input type="hidden" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}">
            <input type="hidden" name="status"         value="{{ request('status') }}">
            <input type="hidden" name="id_area"        value="{{ request('id_area') }}">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-lg shadow-emerald-900/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export CSV
            </button>
        </form>
    </div>

    {{--
        VARIANT 1: Horizontal filter bar (inline, compact)
        Stat cards langsung di bawah filter, tabel full-width
    --}}

    {{-- Filter bar --}}
    <div class="card-pro mb-8 border-white/5 bg-white/[0.02]">
        <form action="{{ route('report.transaksi') }}" method="GET">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex flex-col gap-2 min-w-[200px]">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Cari Plat</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Plat nomor..."
                           class="px-4 py-3 bg-slate-950/50 border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder:text-slate-700 font-semibold">
                </div>
                <div class="flex flex-col gap-2 min-w-[160px]">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Tanggal Dari</label>
                    <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                           class="px-4 py-3 bg-slate-950/50 border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all [color-scheme:dark] font-semibold">
                </div>
                <div class="flex flex-col gap-2 min-w-[160px]">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Tanggal Sampai</label>
                    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                           class="px-4 py-3 bg-slate-950/50 border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all [color-scheme:dark] font-semibold">
                </div>
                <div class="flex flex-col gap-2 min-w-[140px]">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Status</label>
                    <div class="relative">
                        <select name="status"
                                class="w-full px-4 py-3 bg-slate-950/50 border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all appearance-none font-semibold cursor-pointer">
                            <option value="" class="bg-slate-900">— Semua —</option>
                            <option value="masuk"  class="bg-slate-900" {{ request('status')==='masuk'  ? 'selected':'' }}>Masuk</option>
                            <option value="keluar" class="bg-slate-900" {{ request('status')==='keluar' ? 'selected':'' }}>Keluar</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-2 flex-1 min-w-[200px]">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Area Parkir</label>
                    <div class="relative">
                        <select name="id_area"
                                class="w-full px-4 py-3 bg-slate-950/50 border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all appearance-none font-semibold cursor-pointer">
                            <option value="" class="bg-slate-900">— Semua Area —</option>
                            @foreach(\App\Models\AreaParkir::all() as $area)
                            <option value="{{ $area->id_area }}" class="bg-slate-900" {{ request('id_area')==$area->id_area ? 'selected':'' }}>
                                {{ $area->nama_area }}
                            </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="px-8 py-3 bg-emerald-500 text-slate-950 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/20 shrink-0">
                        Filter
                    </button>
                    @if(request()->hasAny(['q','tanggal_dari','tanggal_sampai','status','id_area']))
                    <a href="{{ route('report.transaksi') }}"
                       class="px-6 py-3 bg-slate-900 border border-white/5 text-slate-400 hover:text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl transition-all shrink-0">
                        Reset
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="card-pro border-white/5 bg-white/[0.02] p-6 group hover:border-indigo-500/30 transition-all">
            <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.3em] mb-3 group-hover:text-indigo-400 transition-colors">Total Transaksi</p>
            <p class="text-3xl font-black text-white tracking-tighter">{{ $total_transaksi }}</p>
        </div>
        <div class="card-pro border-white/5 bg-white/[0.02] p-6 group hover:border-emerald-500/30 transition-all">
            <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.3em] mb-3 group-hover:text-emerald-400 transition-colors">Total Biaya</p>
            <p class="text-3xl font-black text-white tracking-tighter">Rp {{ number_format($total_biaya, 0, ',', '.') }}</p>
        </div>
        <div class="card-pro border-white/5 bg-white/[0.02] p-6 group hover:border-blue-500/30 transition-all">
            <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.3em] mb-3 group-hover:text-blue-400 transition-colors">Rata-rata Durasi</p>
            <p class="text-3xl font-black text-white tracking-tighter">{{ number_format($durasi_rata, 1, ',', '.') }} <span class="text-xs font-bold text-slate-600 uppercase ml-1">Jam</span></p>
        </div>
        <div class="bg-slate-900/80 border border-white/[0.07] rounded-2xl p-5">
            <p class="text-[10px] font-bold text-amber-400 uppercase tracking-widest mb-2">Periode</p>
            <p class="text-sm font-bold text-white leading-snug">
                {{ request('tanggal_dari') ?: 'Awal' }} <span class="text-slate-500 font-normal">s/d</span> {{ request('tanggal_sampai') ?: 'Akhir' }}
            </p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-slate-900/80 border border-white/[0.07] rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/[0.05] flex items-center justify-between">
            <h3 class="text-sm font-bold text-white flex items-center gap-3">
                <span class="w-1.5 h-5 bg-indigo-500 rounded-full"></span>
                Data Transaksi
            </h3>
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $transaksis->total() }} records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/[0.02]">
                        @foreach(['ID', 'Plat Nomor', 'Area', 'Waktu Masuk', 'Waktu Keluar', 'Durasi', 'Biaya', 'Status', 'Pembayaran'] as $h)
                        <th class="px-5 py-3.5 text-[10px] font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.04]">
                    @forelse($transaksis as $item)
                    <tr class="hover:bg-white/[0.015] transition-colors">
                        <td class="px-5 py-4 text-sm font-bold text-indigo-400 font-mono whitespace-nowrap">#{{ $item->id_parkir }}</td>
                        <td class="px-5 py-4 text-sm font-semibold text-white whitespace-nowrap">{{ $item->kendaraan?->plat_nomor ?? '—' }}</td>
                        <td class="px-5 py-4 text-sm text-slate-400 whitespace-nowrap">{{ $item->area?->nama_area ?? '—' }}</td>
                        <td class="px-5 py-4 text-sm text-slate-400 font-mono whitespace-nowrap">{{ $item->waktu_masuk?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="px-5 py-4 text-sm text-slate-400 font-mono whitespace-nowrap">{{ $item->waktu_keluar?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="px-5 py-4 text-sm text-slate-400 whitespace-nowrap">{{ $item->durasi_jam ?? '—' }}</td>
                        <td class="px-5 py-4 text-sm font-bold text-emerald-400 whitespace-nowrap">Rp {{ number_format($item->biaya_total, 0, ',', '.') }}</td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($item->status === 'masuk')
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400">Masuk</span>
                            @else
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-white/[0.06] border border-white/[0.08] text-slate-400">Keluar</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($item->status_pembayaran === 'berhasil')
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">Berhasil</span>
                            @elseif($item->status_pembayaran === 'pending')
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400">Pending</span>
                            @else
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-white/[0.06] border border-white/[0.08] text-slate-500">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-16 text-center">
                            <div class="w-12 h-12 rounded-2xl bg-white/[0.04] border border-white/[0.07] flex items-center justify-center mx-auto mb-4">
                                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="text-sm text-slate-600">Tidak ada data transaksi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transaksis->hasPages())
        <div class="px-6 py-4 border-t border-white/[0.05] bg-white/[0.01]">
            {{ $transaksis->links() }}
        </div>
        @endif
    </div>

</div>
</div>
@endsection
