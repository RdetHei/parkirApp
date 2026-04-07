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
    <div class="bg-slate-900/80 border border-white/[0.07] rounded-2xl p-5 mb-5">
        <form action="{{ route('report.transaksi') }}" method="GET">
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex flex-col gap-1.5 min-w-[200px]">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Cari Plat</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Plat nomor..."
                           class="px-3 py-2.5 bg-white/[0.05] border border-white/[0.08] rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/50 transition-all placeholder:text-slate-700">
                </div>
                <div class="flex flex-col gap-1.5 min-w-[140px]">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Tanggal Dari</label>
                    <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                           class="px-3 py-2.5 bg-white/[0.05] border border-white/[0.08] rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/50 transition-all [color-scheme:dark]">
                </div>
                <div class="flex flex-col gap-1.5 min-w-[140px]">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Tanggal Sampai</label>
                    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                           class="px-3 py-2.5 bg-white/[0.05] border border-white/[0.08] rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/50 transition-all [color-scheme:dark]">
                </div>
                <div class="flex flex-col gap-1.5 min-w-[120px]">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Status</label>
                    <select name="status"
                            class="px-3 py-2.5 bg-white/[0.05] border border-white/[0.08] rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/50 transition-all appearance-none">
                        <option value="" class="bg-slate-900">— Semua —</option>
                        <option value="masuk"  class="bg-slate-900" {{ request('status')==='masuk'  ? 'selected':'' }}>Masuk</option>
                        <option value="keluar" class="bg-slate-900" {{ request('status')==='keluar' ? 'selected':'' }}>Keluar</option>
                    </select>
                </div>
                <div class="flex flex-col gap-1.5 flex-1 min-w-[180px]">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Area Parkir</label>
                    <select name="id_area"
                            class="px-3 py-2.5 bg-white/[0.05] border border-white/[0.08] rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/50 transition-all appearance-none">
                        <option value="" class="bg-slate-900">— Semua Area —</option>
                        @foreach(\App\Models\AreaParkir::all() as $area)
                        <option value="{{ $area->id_area }}" class="bg-slate-900" {{ request('id_area')==$area->id_area ? 'selected':'' }}>
                            {{ $area->nama_area }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors shrink-0">
                    Terapkan Filter
                </button>
                @if(request()->hasAny(['q','tanggal_dari','tanggal_sampai','status','id_area']))
                <a href="{{ route('report.transaksi') }}"
                   class="px-4 py-2.5 bg-white/[0.05] border border-white/[0.08] text-slate-400 hover:text-white text-sm font-medium rounded-xl transition-colors shrink-0">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
        <div class="bg-slate-900/80 border border-white/[0.07] rounded-2xl p-5">
            <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mb-2">Total Transaksi</p>
            <p class="text-2xl font-bold text-white">{{ $total_transaksi }}</p>
        </div>
        <div class="bg-slate-900/80 border border-white/[0.07] rounded-2xl p-5">
            <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest mb-2">Total Biaya</p>
            <p class="text-2xl font-bold text-white">Rp {{ number_format($total_biaya, 0, ',', '.') }}</p>
        </div>
        <div class="bg-slate-900/80 border border-white/[0.07] rounded-2xl p-5">
            <p class="text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-2">Rata-rata Durasi</p>
            <p class="text-2xl font-bold text-white">{{ number_format($durasi_rata, 1, ',', '.') }} <span class="text-base font-medium text-slate-500">jam</span></p>
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
