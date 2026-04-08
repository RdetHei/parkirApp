@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')

@section('content')
<div class="p-8 relative z-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                    Financial Records
                </span>
            </div>
            <h1 class="text-4xl font-bold tracking-tight text-white">Payment <span class="text-emerald-500">History</span></h1>
            <p class="text-slate-400 text-sm mt-2">Daftar riwayat transaksi pembayaran yang telah diproses oleh sistem.</p>
        </div>
    </div>

    @if($message = Session::get('success'))
        <div class="mb-8 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center gap-3 animate-fade-in">
            <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-500">
                <i class="fa-solid fa-check-circle text-sm"></i>
            </div>
            <p class="text-xs font-bold text-emerald-500 uppercase tracking-widest">{{ $message }}</p>
        </div>
    @endif

    <div class="card-pro !p-0 overflow-hidden animate-fade-in-up">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 bg-white/[0.02]">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">ID Transaksi</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Kendaraan</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Nominal</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Metode</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Timestamp</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Operator</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($pembayarans as $pembayaran)
                    <tr class="group hover:bg-white/[0.02] transition-colors">
                        <td class="px-8 py-6">
                            <span class="text-[11px] font-mono font-black text-indigo-400">#{{ str_pad($pembayaran->id_pembayaran, 8, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-900 border border-white/5 flex items-center justify-center text-slate-400 group-hover:text-white transition-colors">
                                    <i class="fa-solid fa-car-side text-xs"></i>
                                </div>
                                <span class="text-sm font-black text-white tracking-tight uppercase">{{ $pembayaran->transaksi->kendaraan->plat_nomor ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-sm font-black text-emerald-400 tracking-tight">Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-2">
                                @if($pembayaran->metode === 'midtrans')
                                    <span class="w-2 h-2 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.5)]"></span>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Midtrans Digital</span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $pembayaran->metode ?? 'Cash' }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            @if($pembayaran->status === 'berhasil')
                                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[9px] font-black uppercase tracking-widest rounded-lg border border-emerald-500/20">Success</span>
                            @elseif($pembayaran->status === 'pending')
                                <span class="px-3 py-1 bg-amber-500/10 text-amber-500 text-[9px] font-black uppercase tracking-widest rounded-lg border border-amber-500/20">Pending</span>
                            @else
                                <span class="px-3 py-1 bg-rose-500/10 text-rose-500 text-[9px] font-black uppercase tracking-widest rounded-lg border border-rose-500/20">Failed</span>
                            @endif
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col gap-0.5">
                                <span class="text-[11px] font-black text-white tracking-tight">{{ $pembayaran->waktu_pembayaran?->format('d M Y') ?? '-' }}</span>
                                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">{{ $pembayaran->waktu_pembayaran?->format('H:i:s') ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-slate-900 border border-white/10 flex items-center justify-center text-[10px] text-slate-500">
                                    <i class="fa-solid fa-user-shield text-[8px]"></i>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $pembayaran->petugas->name ?? 'System' }}</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center gap-4 opacity-20">
                                <i class="fa-solid fa-receipt text-6xl text-white"></i>
                                <p class="text-[11px] font-black text-white uppercase tracking-[0.4em]">No financial data detected</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pembayarans->hasPages())
            <div class="px-8 py-6 border-t border-white/5 bg-white/[0.01]">
                {{ $pembayarans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
