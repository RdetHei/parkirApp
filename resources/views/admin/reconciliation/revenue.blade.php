@extends('layouts.app')

@section('title', 'Rekonsiliasi Pendapatan')

@section('content')
<div class="p-6 sm:p-8 relative z-10 animate-fade-in">
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight">Rekonsiliasi <span class="text-emerald-500">Pendapatan</span></h1>
                <p class="text-slate-400 text-sm mt-1">Bandingkan total transaksi berhasil vs tabel pembayaran.</p>
            </div>
            <form method="POST" action="{{ route('admin.reconciliation.revenue.sync') }}">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500 text-slate-950 text-xs font-black uppercase tracking-widest hover:bg-emerald-400 transition-all">
                    Sinkronkan Data Hilang
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 px-4 py-3 text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="card-pro border-white/5">
                <p class="text-[10px] uppercase tracking-widest text-slate-500 font-black">Transaksi Berhasil</p>
                <p class="mt-2 text-xl font-black text-white">Rp {{ number_format($totalFromTransaksi, 0, ',', '.') }}</p>
            </div>
            <div class="card-pro border-white/5">
                <p class="text-[10px] uppercase tracking-widest text-slate-500 font-black">Tabel Pembayaran</p>
                <p class="mt-2 text-xl font-black text-white">Rp {{ number_format($totalFromPembayaran, 0, ',', '.') }}</p>
            </div>
            <div class="card-pro border-white/5">
                <p class="text-[10px] uppercase tracking-widest text-slate-500 font-black">Selisih</p>
                <p class="mt-2 text-xl font-black {{ $delta == 0 ? 'text-emerald-400' : 'text-amber-400' }}">
                    Rp {{ number_format($delta, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="card-pro p-0! overflow-hidden border-white/5">
            <div class="px-5 py-4 border-b border-white/5 bg-white/2">
                <h2 class="text-xs font-black uppercase tracking-widest text-white">Transaksi Berhasil tanpa Pembayaran</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-white/1 border-b border-white/5">
                        <tr>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500">ID Parkir</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500">User</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500">Plat</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500">Biaya</th>
                            <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500">Waktu Keluar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($missing as $m)
                            <tr class="hover:bg-white/2">
                                <td class="px-4 py-3 text-xs text-white font-bold">#{{ $m->id_parkir }}</td>
                                <td class="px-4 py-3 text-xs text-slate-300">{{ $m->user?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs text-slate-300">{{ $m->kendaraan?->plat_nomor ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs text-slate-300">Rp {{ number_format((float) $m->biaya_total, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-xs text-slate-300">{{ optional($m->waktu_keluar)->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-xs text-slate-500 font-black uppercase tracking-widest">Tidak ada data yang perlu disinkronkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-white/5">
                {{ $missing->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
