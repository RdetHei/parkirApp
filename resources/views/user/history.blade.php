@extends('layouts.app')

@section('title', 'Riwayat Parkir')

@section('content')
<div class="p-8 relative z-10">
    <div class="max-w-6xl mx-auto space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 animate-fade-in-up">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-sky-500/10 text-sky-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-sky-500/20">
                        <i class="fa-solid fa-history mr-1"></i>
                        Riwayat Transaksi
                    </span>
                </div>
                <h1 class="text-4xl font-bold tracking-tight text-white">Riwayat Parkir Anda</h1>
                <p class="text-slate-400 text-sm mt-2">Seluruh catatan transaksi parkir yang pernah Anda lakukan.</p>
            </div>
            <a href="{{ route('user.dashboard') }}" class="px-6 py-3 bg-white/[0.03] hover:bg-white/[0.08] text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border border-white/10 flex items-center justify-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Dashboard
            </a>
        </div>

        <!-- History Table -->
        <div class="card-pro !p-0 overflow-hidden animate-fade-in-up" style="animation-delay: 0.1s">
            <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                <h2 class="text-sm font-black text-white uppercase tracking-widest">Daftar Transaksi</h2>
            </div>
            
            @if($transactions->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-white/5">
                        <tr>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Kendaraan</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Area</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Waktu</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Biaya</th>
                            <th class="px-6 py-4 text-center text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Status</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                        @forelse($transactions as $trx)
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-bold text-white uppercase">{{ $trx->kendaraan->plat_nomor ?? '-' }}</p>
                                        <p class="text-[10px] text-slate-400 uppercase">{{ $trx->kendaraan->jenis_kendaraan ?? 'Kendaraan' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-400">{{ $trx->area->nama_area ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-slate-300"><span class="font-bold text-sky-500">Masuk:</span> {{ $trx->waktu_masuk->format('d/m/y H:i') }}</p>
                                        @if($trx->waktu_keluar)
                                            <p class="text-slate-300"><span class="font-bold text-rose-500">Keluar:</span> {{ $trx->waktu_keluar->format('d/m/y H:i') }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <p class="font-bold text-white">Rp {{ number_format($trx->biaya_total, 0, ',', '.') }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($trx->status === 'masuk')
                                        <span class="px-2.5 py-1 bg-sky-500/10 text-sky-500 text-[9px] font-black uppercase rounded-lg border border-sky-500/20 tracking-widest">
                                            Aktif
                                        </span>
                                    @elseif($trx->status === 'keluar')
                                        @if(($trx->pembayaran->status ?? '') === 'berhasil')
                                            <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-500 text-[9px] font-black uppercase rounded-lg border border-emerald-500/20 tracking-widest">
                                                Selesai
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 bg-rose-500/10 text-rose-500 text-[9px] font-black uppercase rounded-lg border border-rose-500/20 tracking-widest">
                                                Belum Dibayar
                                            </span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="p-12 text-center">
                                        <div class="w-16 h-16 bg-white/5 text-slate-500 flex items-center justify-center rounded-2xl mx-auto mb-4 border border-white/10">
                                            <i class="fa-solid fa-receipt text-2xl"></i>
                                        </div>
                                        <h3 class="font-bold text-white">Belum Ada Riwayat</h3>
                                        <p class="text-sm text-slate-400 mt-1">Anda belum memiliki riwayat transaksi parkir.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($transactions->hasPages())
                <div class="px-8 py-6 border-t border-white/5 bg-white/[0.02]">
                    {{ $transactions->links() }}
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
