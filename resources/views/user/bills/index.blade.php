@extends('layouts.app')

@section('title', 'Tagihan Saya - NESTON')

@section('content')
<div class="p-8 relative z-10 animate-fade-in">
    <!-- Background Glows -->
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="max-w-5xl mx-auto relative z-10">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-rose-500/10 text-rose-400 text-[10px] font-black uppercase tracking-widest rounded-full border border-rose-500/20">
                        Payment Center
                    </span>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-white uppercase">Tagihan <span class="text-emerald-500">Parkir</span></h1>
                <p class="text-slate-400 text-sm mt-2 font-medium tracking-wide">Selesaikan pembayaran untuk transaksi parkir yang telah selesai.</p>
            </div>
            
            <a href="{{ route('user.dashboard') }}"
               class="group px-6 py-3.5 bg-white/5 border border-white/5 rounded-2xl text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-white hover:bg-white/10 transition-all flex items-center gap-3 active:scale-95">
                <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                Kembali ke Dashboard
            </a>
        </div>

        @if(session('success') || session('error'))
            <div class="mb-8 flex items-center gap-4 px-6 py-4 rounded-2xl border {{ session('success') ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400' : 'border-rose-500/20 bg-rose-500/10 text-rose-400' }} text-xs font-black uppercase tracking-widest animate-fade-in">
                <i class="fa-solid {{ session('success') ? 'fa-circle-check' : 'fa-circle-exclamation' }} text-base"></i>
                {{ session('success') ?? session('error') }}
            </div>
        @endif

        <!-- Summary Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            <div class="card-pro group overflow-hidden relative border-white/5 backdrop-blur-xl bg-slate-900/40 p-8">
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-500/5 rounded-full blur-2xl group-hover:scale-110 transition-transform"></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Tagihan Aktif</p>
                        <p class="text-4xl font-black text-white tracking-tighter">{{ $transaksis->total() }} <span class="text-sm font-bold text-slate-600 tracking-normal ml-1">Transaksi</span></p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-400">
                        <i class="fa-solid fa-file-invoice-dollar text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="card-pro group overflow-hidden relative border-emerald-500/10 backdrop-blur-xl bg-slate-900/40 p-8">
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/5 rounded-full blur-2xl group-hover:scale-110 transition-transform"></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Total Tunggakan</p>
                        <p class="text-4xl font-black text-emerald-500 tracking-tighter">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500">
                        <i class="fa-solid fa-wallet text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- List Bills -->
        <div class="card-pro !p-0 overflow-hidden border-white/5 backdrop-blur-xl bg-slate-900/40 shadow-2xl">
            <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                <h2 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Daftar Tagihan Belum Dibayar</h2>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-[10px] text-slate-600"></i>
                    <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest">Waiting Payment</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/[0.01] border-b border-white/5">
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Kendaraan</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Area</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Waktu Keluar</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Total Tagihan</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($transaksis as $trx)
                            <tr class="group hover:bg-white/[0.02] transition-all">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-950 border border-white/5 flex flex-col items-center justify-center font-black text-white group-hover:border-emerald-500/30 transition-all">
                                            <span class="text-[8px] text-slate-600 leading-none mb-1 uppercase tracking-tighter">{{ substr($trx->kendaraan->plat_nomor ?? '-', 0, 2) }}</span>
                                            <span class="text-sm leading-none tracking-tight">{{ substr($trx->kendaraan->plat_nomor ?? '-', 2, 4) }}</span>
                                        </div>
                                        <p class="text-sm font-black text-white tracking-tight group-hover:text-emerald-400 transition-colors">{{ $trx->kendaraan->plat_nomor ?? '-' }}</p>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-xs font-bold text-slate-300">{{ $trx->area->nama_area ?? '-' }}</p>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="space-y-0.5">
                                        <p class="text-sm font-black text-white tracking-tighter">{{ optional($trx->waktu_keluar)->format('H:i') ?? '-' }}</p>
                                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">{{ optional($trx->waktu_keluar)->format('d/m/Y') ?? '-' }}</p>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-lg font-black text-white tracking-tighter">Rp {{ number_format($trx->biaya_total ?? 0, 0, ',', '.') }}</p>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <a href="{{ route('payment.midtrans', $trx->id_parkir) }}"
                                       class="group relative inline-flex items-center justify-center gap-2 px-6 py-3 bg-emerald-500 text-slate-950 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-emerald-400 transition-all shadow-xl shadow-emerald-500/10 active:scale-[0.98] overflow-hidden">
                                        <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                                        <i class="fa-solid fa-credit-card text-sm"></i>
                                        Bayar Sekarang
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-24 text-center">
                                    <div class="w-24 h-24 bg-slate-950 border border-white/5 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 text-slate-800 shadow-2xl">
                                        <i class="fa-solid fa-receipt text-4xl opacity-10"></i>
                                    </div>
                                    <p class="text-[10px] text-slate-600 font-black uppercase tracking-[0.3em] italic">Tidak ada tagihan tertunda</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transaksis->hasPages())
                <div class="px-8 py-6 bg-white/[0.01] border-t border-white/5">
                    {{ $transaksis->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    @keyframes shimmer {
        100% { transform: translateX(100%); }
    }
</style>
@endsection
