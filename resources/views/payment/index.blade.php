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

    <!-- Filter Console -->
    <div class="card-pro mb-8 border-white/5 bg-white/[0.02]">
        <form action="{{ route('payment.index') }}" method="GET" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Cari Transaksi</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-700 group-focus-within:text-emerald-500">
                            <i class="fa-solid fa-search text-xs"></i>
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Plat / Order ID / Nama"
                               oninput="debounceSearch(this)"
                               class="block w-full pl-11 pr-4 py-3 bg-slate-950 border border-white/5 rounded-xl text-xs text-white placeholder:text-slate-700 focus:outline-none focus:border-emerald-500/50 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Status</label>
                    <select name="status" onchange="this.form.submit()"
                            class="block w-full px-4 py-3 bg-slate-950 border border-white/5 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500/50 transition-all">
                        <option value="" class="bg-slate-900">Semua Status</option>
                        <option value="berhasil" class="bg-slate-900" {{ request('status') == 'berhasil' ? 'selected' : '' }}>Berhasil</option>
                        <option value="pending" class="bg-slate-900" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="gagal" class="bg-slate-900" {{ request('status') == 'gagal' ? 'selected' : '' }}>Gagal</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Dari Tanggal</label>
                    <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" onchange="this.form.submit()"
                           class="block w-full px-4 py-3 bg-slate-950 border border-white/5 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500/50 transition-all">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Sampai Tanggal</label>
                    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" onchange="this.form.submit()"
                           class="block w-full px-4 py-3 bg-slate-950 border border-white/5 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500/50 transition-all">
                </div>
            </div>

            @if(request()->anyFilled(['q', 'status', 'tanggal_dari', 'tanggal_sampai']))
            <div class="mt-6 flex justify-end">
                <a href="{{ route('payment.index') }}" class="text-[9px] font-black text-rose-500 uppercase tracking-widest hover:text-rose-400 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-times-circle"></i> Bersihkan Semua Filter
                </a>
            </div>
            @endif
        </form>
    </div>

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
                                @elseif($pembayaran->metode === 'nestonpay')
                                    <span class="w-2 h-2 rounded-full bg-violet-500 shadow-[0_0_8px_rgba(139,92,246,0.5)]"></span>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">NestonPay</span>
                                @elseif($pembayaran->metode === 'cash')
                                    <span class="w-2 h-2 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></span>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tunai</span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $pembayaran->metode ?? '-' }}</span>
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
@push('scripts')
<script>
    let searchTimer;
    function debounceSearch(input) {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            input.form.submit();
        }, 800);
    }
</script>
@endpush
@endsection
