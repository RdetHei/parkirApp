@extends('layouts.app')

@section('title', 'Tagihan Saya')

@section('content')
<div class="p-8 relative z-10">
    <div class="max-w-5xl mx-auto space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 animate-fade-in-up">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-rose-500/10 text-rose-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-rose-500/20">
                        <i class="fa-solid fa-file-invoice-dollar mr-1"></i>
                        Menunggu Pembayaran
                    </span>
                </div>
                <h1 class="text-4xl font-bold tracking-tight text-white">Tagihan Parkir Anda</h1>
                <p class="text-slate-400 text-sm mt-2">Daftar transaksi parkir yang sudah selesai dan menunggu pembayaran.</p>
            </div>
            <a href="{{ route('user.dashboard') }}" class="px-6 py-3 bg-white/[0.03] hover:bg-white/[0.08] text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border border-white/10 flex items-center justify-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="px-4 py-3 bg-emerald-500/10 text-emerald-500 text-sm font-bold rounded-xl border border-emerald-500/20 flex items-center gap-3">
                    <i class="fa-solid fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="px-4 py-3 bg-rose-500/10 text-rose-500 text-sm font-bold rounded-xl border border-rose-500/20 flex items-center gap-3">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 animate-fade-in-up" style="animation-delay: 0.2s">
            <div class="card-pro group overflow-hidden relative">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/5 rounded-full blur-2xl group-hover:bg-white/10 transition-all"></div>
                <div class="flex items-center justify-between mb-4 relative z-10">
                    <div class="p-2.5 bg-white/5 rounded-xl border border-white/10 text-white">
                        <i class="fa-solid fa-receipt text-lg"></i>
                    </div>
                    <span class="px-2.5 py-1 bg-white/5 text-white text-[9px] font-black uppercase rounded-lg border border-white/10">{{ $transaksis->total() }} Tagihan</span>
                </div>
                <p class="text-4xl font-black text-white tracking-tighter relative z-10">{{ $transaksis->total() }}</p>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1 relative z-10">Jumlah Tagihan Aktif</p>
            </div>
            <div class="card-pro group overflow-hidden relative border-emerald-500/20">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-all"></div>
                <div class="flex items-center justify-between mb-4 relative z-10">
                    <div class="p-2.5 bg-emerald-500/10 rounded-xl border border-emerald-500/20 text-emerald-500">
                        <i class="fa-solid fa-wallet text-lg"></i>
                    </div>
                </div>
                <p class="text-4xl font-black text-white tracking-tighter relative z-10">
                    <span class="text-emerald-500 text-2xl font-medium mr-1">Rp</span>{{ number_format($totalTagihan, 0, ',', '.') }}
                </p>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1 relative z-10">Total Perkiraan</p>
            </div>
        </div>

        <!-- Bills Table -->
        <div class="card-pro !p-0 overflow-hidden animate-fade-in-up" style="animation-delay: 0.3s">
            <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                <h2 class="text-sm font-black text-white uppercase tracking-widest">Daftar Tagihan</h2>
            </div>
            
            @if($transaksis->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-white/5">
                        <tr>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Kendaraan</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Area</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Waktu Keluar</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Total Tagihan</th>
                            <th class="px-6 py-4 text-center text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Aksi</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                        @foreach($transaksis as $trx)
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4 font-bold text-white uppercase">
                                    {{ $trx->kendaraan->plat_nomor ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-slate-400">
                                    {{ $trx->area->nama_area ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-slate-400">
                                    {{ optional($trx->waktu_keluar)->format('d M Y, H:i') ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-emerald-500">
                                    Rp {{ number_format($trx->biaya_total ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('payment.midtrans', $trx->id_parkir) }}"
                                       class="px-4 py-2 bg-emerald-500 text-slate-950 font-black text-[10px] uppercase tracking-widest rounded-lg transition-all hover:bg-emerald-400 hover:shadow-[0_0_15px_rgba(16,185,129,0.3)]">
                                        Bayar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                @if($transaksis->hasPages())
                <div class="px-8 py-6 border-t border-white/5 bg-white/[0.02]">
                    {{ $transaksis->links() }}
                </div>
                @endif
            @else
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-emerald-500/10 text-emerald-500 flex items-center justify-center rounded-2xl mx-auto mb-4 border border-emerald-500/20">
                        <i class="fa-solid fa-check-double text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-white">Tidak Ada Tagihan</h3>
                    <p class="text-sm text-slate-400 mt-1">Semua transaksi Anda sudah terbayar lunas.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

