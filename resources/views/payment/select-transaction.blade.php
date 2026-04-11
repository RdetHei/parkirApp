@extends('layouts.app')

@section('title', 'Pilih Transaksi - NESTON')

@section('content')
<div class="p-8 relative z-10 animate-fade-in">
    <!-- Background Glows -->
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="max-w-7xl mx-auto relative z-10">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-black uppercase tracking-widest rounded-full border border-emerald-500/20">
                        Payment Terminal
                    </span>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-white uppercase">Pilih <span class="text-emerald-500">Transaksi</span></h1>
                <p class="text-slate-400 text-sm mt-2 font-medium tracking-wide">Pilih sesi parkir yang sudah selesai untuk diproses pembayarannya.</p>
            </div>

            <div class="px-6 py-4 bg-slate-950 border border-white/5 rounded-2xl flex items-center gap-4 shadow-xl">
                <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">{{ $transaksis->count() }} Transaksi Menunggu</span>
            </div>
        </div>

        <!-- Filter Console -->
        <div class="card-pro mb-8 border-white/5 bg-white/[0.02] backdrop-blur-md">
            <form action="{{ route('payment.select-transaction') }}" method="GET" id="searchForm">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-colors group-focus-within:text-emerald-500 text-slate-600">
                        <i class="fa-solid fa-search text-xs"></i>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Cari Plat Nomor (Contoh: B 1234 ABC)"
                           oninput="debounceSearch(this)"
                           class="block w-full pl-12 pr-6 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-xs font-bold text-white placeholder:text-slate-700 focus:outline-none focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/5 transition-all">
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="card-pro !p-0 overflow-hidden border-white/5 backdrop-blur-xl bg-slate-900/40 shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/[0.02] border-b border-white/5">
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">ID Sesi</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Kendaraan</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Waktu Masuk</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Area Parkir</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($transaksis as $transaksi)
                            <tr class="group hover:bg-white/[0.02] transition-all" id="row-{{ $transaksi->id_parkir }}" data-transaksi-id="{{ $transaksi->id_parkir }}">
                                <td class="px-8 py-6">
                                    <span class="text-xs font-black text-slate-500 tracking-widest">#{{ str_pad($transaksi->id_parkir, 5, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-950 border border-white/5 flex flex-col items-center justify-center font-black text-white group-hover:border-emerald-500/30 transition-all shadow-xl">
                                            <span class="text-[8px] text-slate-600 leading-none mb-1 uppercase tracking-tighter">{{ substr($transaksi->kendaraan->plat_nomor ?? '-', 0, 2) }}</span>
                                            <span class="text-sm leading-none tracking-tight">{{ substr($transaksi->kendaraan->plat_nomor ?? '-', 2, 4) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-white tracking-tight group-hover:text-emerald-400 transition-colors uppercase">{{ $transaksi->kendaraan->plat_nomor }}</p>
                                            <span class="px-2 py-0.5 bg-blue-500/10 text-blue-400 text-[8px] font-black uppercase rounded border border-blue-500/20 tracking-widest mt-1 inline-block">
                                                {{ $transaksi->kendaraan->jenis_kendaraan }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="space-y-0.5">
                                        <p class="text-sm font-black text-white tracking-tighter">{{ \Carbon\Carbon::parse($transaksi->waktu_masuk)->format('H:i') }}</p>
                                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">{{ \Carbon\Carbon::parse($transaksi->waktu_masuk)->translatedFormat('d M Y') }}</p>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-2.5">
                                        <i class="fa-solid fa-location-dot text-[10px] text-slate-600"></i>
                                        <span class="text-xs font-bold text-slate-300 uppercase tracking-widest">{{ $transaksi->area->nama_area }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <a href="{{ route('payment.create', $transaksi->id_parkir) }}"
                                       class="group relative inline-flex items-center justify-center gap-3 px-6 py-3 bg-emerald-500 text-slate-950 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-emerald-400 transition-all shadow-xl shadow-emerald-500/10 active:scale-[0.98] overflow-hidden">
                                        <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                                        <i class="fa-solid fa-arrow-right-to-bracket text-sm"></i>
                                        Pilih Sesi
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-24 text-center">
                                    <div class="w-24 h-24 bg-slate-950 border border-white/5 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 text-slate-800 shadow-2xl">
                                        <i class="fa-solid fa-inbox text-4xl opacity-10"></i>
                                    </div>
                                    <p class="text-[10px] text-slate-600 font-black uppercase tracking-[0.3em] italic">Tidak ada transaksi aktif yang ditemukan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    let searchTimer;
    function debounceSearch(input) {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            input.form.submit();
        }, 800);
    }
</script>

<style>
    @keyframes shimmer {
        100% { transform: translateX(100%); }
    }
</style>
@endsection
