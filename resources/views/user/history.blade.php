@extends('layouts.app')

@section('title', 'Riwayat Parkir - NESTON')

@section('content')
<div class="p-8 relative z-10 animate-fade-in">
    <!-- Background Glows -->
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="max-w-6xl mx-auto relative z-10">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest rounded-full border border-blue-500/20">
                        Transaction Logs
                    </span>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-white uppercase">Riwayat <span class="text-emerald-500">Parkir</span></h1>
                <p class="text-slate-400 text-sm mt-2 font-medium tracking-wide">Daftar lengkap seluruh sesi parkir Anda di ekosistem NESTON.</p>
            </div>

            <a href="{{ route('user.dashboard') }}"
               class="group px-6 py-3.5 bg-white/5 border border-white/5 rounded-2xl text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-white hover:bg-white/10 transition-all flex items-center gap-3 active:scale-95">
                <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                Kembali ke Dashboard
            </a>
        </div>

        <!-- Table Card -->
        <div class="card-pro !p-0 overflow-hidden border-white/5 backdrop-blur-xl bg-slate-900/40 shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/[0.02] border-b border-white/5">
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Kendaraan</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Area Parkir</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Check-in</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Check-out</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Total Biaya</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($transactions as $trx)
                            <tr class="group hover:bg-white/[0.02] transition-all">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-950 border border-white/5 flex flex-col items-center justify-center font-black text-white group-hover:border-emerald-500/30 transition-all shadow-xl">
                                            <span class="text-[8px] text-slate-600 leading-none mb-1 uppercase tracking-tighter">{{ substr($trx->kendaraan->plat_nomor ?? '-', 0, 2) }}</span>
                                            <span class="text-sm leading-none tracking-tight">{{ substr($trx->kendaraan->plat_nomor ?? '-', 2, 4) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-white tracking-tight group-hover:text-emerald-400 transition-colors">{{ $trx->kendaraan->plat_nomor ?? '-' }}</p>
                                            <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">{{ $trx->kendaraan->jenis_kendaraan ?? 'Kendaraan' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-2.5">
                                        <i class="fa-solid fa-location-dot text-[10px] text-slate-600"></i>
                                        <span class="text-xs font-bold text-slate-300">{{ $trx->area->nama_area ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    @if($trx->waktu_masuk)
                                        <div class="space-y-0.5">
                                            <p class="text-sm font-black text-white tracking-tighter">{{ $trx->waktu_masuk->format('H:i') }}</p>
                                            <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">{{ $trx->waktu_masuk->format('d/m/Y') }}</p>
                                        </div>
                                    @else
                                        <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest italic">— Belum Masuk —</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6">
                                    @if($trx->waktu_keluar)
                                        <div class="space-y-0.5">
                                            <p class="text-sm font-black text-white tracking-tighter">{{ $trx->waktu_keluar->format('H:i') }}</p>
                                            <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">{{ $trx->waktu_keluar->format('d/m/Y') }}</p>
                                        </div>
                                    @else
                                        <span class="flex items-center gap-2 text-[10px] font-black text-emerald-500 uppercase tracking-widest">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            In Session
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-base font-black text-white tracking-tighter">
                                        @if($trx->biaya_total)
                                            Rp {{ number_format($trx->biaya_total, 0, ',', '.') }}
                                        @else
                                            <span class="text-slate-600">—</span>
                                        @endif
                                    </p>
                                </td>
                                <td class="px-8 py-6">
                                    @if($trx->status === 'masuk')
                                        <span class="inline-flex items-center px-3 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                            Parkir
                                        </span>
                                    @elseif($trx->status === 'bookmarked')
                                        <span class="inline-flex items-center px-3 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            Booked
                                        </span>
                                    @elseif($trx->status === 'selesai' || $trx->status === 'keluar')
                                        @if(($trx->status_pembayaran ?? '') === 'lunas' || ($trx->pembayaran->status ?? '') === 'berhasil')
                                            <span class="inline-flex items-center px-3 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                Selesai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                                Pending
                                            </span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-24 text-center">
                                    <div class="w-24 h-24 bg-slate-950 border border-white/5 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 text-slate-800 shadow-2xl">
                                        <i class="fa-solid fa-clock-rotate-left text-4xl opacity-10"></i>
                                    </div>
                                    <p class="text-[10px] text-slate-600 font-black uppercase tracking-[0.3em] italic">Belum ada riwayat parkir</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="px-8 py-6 bg-white/[0.01] border-t border-white/5">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
