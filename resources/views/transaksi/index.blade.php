@extends('layouts.app')

@section('title', $title ?? 'Transactions')

@section('content')
<div class="p-8 relative z-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                    Parking Console
                </span>
            </div>
            <h1 class="text-4xl font-bold tracking-tight text-white">Management <span class="text-emerald-500">Parkir</span></h1>
            <p class="text-slate-400 text-sm mt-2">Pusat kendali operasional untuk memantau kendaraan aktif, reservasi, dan riwayat.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('transaksi.create-check-in') }}" class="group relative px-6 py-3 bg-emerald-500 text-slate-950 font-bold text-xs uppercase tracking-widest rounded-xl transition-all hover:bg-emerald-400 hover:shadow-[0_0_20px_rgba(16,185,129,0.4)] flex items-center gap-2">
                <i class="fa-solid fa-plus text-sm"></i>
                Check-In Baru
            </a>
        </div>
    </div>

    <!-- Success/Error Alerts -->
    @if($message = Session::get('success'))
        <div class="mb-8 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-4 flex items-center gap-4 animate-fade-in">
            <div class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center text-emerald-500">
                <i class="fa-solid fa-check"></i>
            </div>
            <p class="text-sm font-bold text-emerald-500 uppercase tracking-widest">{{ $message }}</p>
        </div>
    @endif

    <!-- Main Navigation Tabs -->
    <div class="flex items-center gap-2 mb-8 bg-slate-900/50 p-1.5 rounded-2xl border border-white/5 w-fit">
        <a href="{{ route('transaksi.index', ['status' => 'aktif']) }}"
           class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-3 {{ $currentStatus === 'aktif' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/20' : 'text-slate-500 hover:text-white hover:bg-white/5' }}">
            <i class="fa-solid fa-car-side"></i>
            Parkir Aktif
            <span class="px-1.5 py-0.5 rounded-md text-[8px] {{ $currentStatus === 'aktif' ? 'bg-slate-950/20 text-slate-950' : 'bg-slate-800 text-slate-500' }}">{{ $counts['aktif'] }}</span>
        </a>
        <a href="{{ route('transaksi.index', ['status' => 'booking']) }}"
           class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-3 {{ $currentStatus === 'booking' ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/20' : 'text-slate-500 hover:text-white hover:bg-white/5' }}">
            <i class="fa-solid fa-bookmark"></i>
            Reservasi
            <span class="px-1.5 py-0.5 rounded-md text-[8px] {{ $currentStatus === 'booking' ? 'bg-slate-950/20 text-slate-950' : 'bg-slate-800 text-slate-500' }}">{{ $counts['booking'] }}</span>
        </a>
        <a href="{{ route('transaksi.index', ['status' => 'riwayat']) }}"
           class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-3 {{ $currentStatus === 'riwayat' ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/20' : 'text-slate-500 hover:text-white hover:bg-white/5' }}">
            <i class="fa-solid fa-history"></i>
            Riwayat Selesai
        </a>
    </div>

    <!-- Filter Console -->
    <div class="card-pro mb-8 border-white/5 bg-white/[0.02]">
        <form action="{{ route('transaksi.index') }}" method="GET" id="filterForm">
            <input type="hidden" name="status" value="{{ $currentStatus }}">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ $currentStatus === 'riwayat' ? '5' : '3' }} gap-6">
                <div class="{{ $currentStatus === 'riwayat' ? 'lg:col-span-2' : 'lg:col-span-1' }}">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Cari Plat Nomor</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-search text-slate-700 text-xs"></i>
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Contoh: B 1234 ABC"
                               oninput="debounceSearch(this)"
                               class="block w-full pl-11 pr-4 py-3 bg-slate-950 border border-white/5 rounded-xl text-xs text-white placeholder:text-slate-700 focus:outline-none focus:border-emerald-500/50 transition-all">
                    </div>
                </div>

                @if($currentStatus === 'riwayat')
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
                @endif

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Area Parkir</label>
                    <select name="area" onchange="this.form.submit()"
                            class="block w-full px-4 py-3 bg-slate-950 border border-white/5 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500/50 transition-all">
                        <option value="" class="bg-slate-900">Semua Area</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id_area }}" class="bg-slate-900" {{ request('area') == $area->id_area ? 'selected' : '' }}>
                                {{ $area->nama_area }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if(request()->anyFilled(['q', 'area', 'tanggal_dari', 'tanggal_sampai']))
            <div class="mt-6 flex justify-end">
                <a href="{{ route('transaksi.index', ['status' => $currentStatus]) }}" class="text-[9px] font-black text-rose-500 uppercase tracking-widest hover:text-rose-400 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-times-circle"></i> Bersihkan Semua Filter
                </a>
            </div>
            @endif
        </form>
    </div>

    <!-- Data Table -->
    <div class="card-pro !p-0 overflow-hidden shadow-2xl">
        <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
            <h2 class="text-sm font-bold text-white uppercase tracking-widest">
                @if($currentStatus === 'aktif') Kendaraan Terparkir @elseif($currentStatus === 'booking') Reservasi Menunggu @else Riwayat Selesai @endif
                <span class="text-slate-500 ml-2 font-medium">({{ $transaksis->total() }})</span>
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/[0.01] text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        <th class="px-8 py-4">ID Transaksi</th>
                        <th class="px-8 py-4">Kendaraan</th>
                        <th class="px-8 py-4">Lokasi</th>
                        <th class="px-8 py-4">Waktu</th>
                        @if($currentStatus === 'riwayat')
                        <th class="px-8 py-4">Biaya</th>
                        @else
                        <th class="px-8 py-4">Petugas</th>
                        @endif
                        <th class="px-8 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($transaksis as $transaksi)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-8 py-5">
                            <span class="text-[10px] font-mono font-bold text-emerald-500/80">#{{ str_pad($transaksi->id_parkir, 8, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-slate-800 border border-white/5 flex flex-col items-center justify-center font-bold text-white group-hover:border-emerald-500/30 transition-colors">
                                    <span class="text-[8px] text-slate-500 leading-none mb-0.5">{{ substr($transaksi->kendaraan->plat_nomor ?? '-', 0, 2) }}</span>
                                    <span class="text-xs leading-none">{{ substr($transaksi->kendaraan->plat_nomor ?? '-', 2, 4) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white tracking-tight">{{ $transaksi->kendaraan->plat_nomor ?? '-' }}</p>
                                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">{{ $transaksi->kendaraan->jenis_kendaraan ?? 'Motor' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-white">{{ $transaksi->area->nama_area ?? '-' }}</span>
                                <span class="text-[10px] text-slate-500 font-medium">Slot: {{ $transaksi->parkingMapSlot->code ?? 'Umum' }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col gap-1">
                                @if($currentStatus === 'aktif')
                                    <span class="text-xs font-bold text-white">{{ $transaksi->waktu_masuk->format('H:i') }} <span class="text-slate-600 text-[10px] ml-1">{{ $transaksi->waktu_masuk->format('d/m') }}</span></span>
                                    @php
                                        $masuk = \Illuminate\Support\Carbon::parse($transaksi->waktu_masuk, config('app.timezone'));
                                        $now = \Illuminate\Support\Carbon::now(config('app.timezone'));
                                        $durasi = (int) $now->diffInMinutes($masuk, true);
                                    @endphp
                                    <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">{{ intdiv($durasi, 60) }}j {{ $durasi % 60 }}m berjalan</span>
                                @elseif($currentStatus === 'booking')
                                    <span class="text-xs font-bold text-amber-500 italic">Booking: {{ $transaksi->bookmarked_at->format('H:i') }}</span>
                                    <span class="text-[9px] text-slate-500 font-medium italic">Exp: {{ $transaksi->bookmarked_at->addMinutes(10)->format('H:i') }}</span>
                                @else
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-medium text-slate-300">{{ $transaksi->waktu_masuk->format('d/m H:i') }}</span>
                                        <i class="fa-solid fa-arrow-right text-[8px] text-slate-700"></i>
                                        <span class="text-xs font-bold text-white">{{ $transaksi->waktu_keluar->format('H:i') }}</span>
                                    </div>
                                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Durasi: {{ $transaksi->durasi_jam }} jam</span>
                                @endif
                            </div>
                        </td>
                        @if($currentStatus === 'riwayat')
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-emerald-500">Rp {{ number_format($transaksi->biaya_total, 0, ',', '.') }}</span>
                                <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest">{{ $transaksi->pembayaran->metode ?? 'Cash' }}</span>
                            </div>
                        </td>
                        @else
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-slate-300 font-medium">{{ explode(' ', $transaksi->user->name ?? '-')[0] }}</span>
                            </div>
                        </td>
                        @endif

                        <td class="px-8 py-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <div class="flex items-center bg-slate-900 border border-white/5 rounded-xl p-1 gap-1">
                                    @if($currentStatus === 'aktif')
                                        <form action="{{ route('transaksi.checkOut', $transaksi->id_parkir) }}" method="POST" class="inline">
                                            @csrf @method('PUT')
                                            <button type="submit" 
                                                    class="group relative w-9 h-9 flex items-center justify-center bg-amber-500 text-slate-950 rounded-lg hover:bg-amber-400 transition-all active:scale-90 shadow-lg shadow-amber-500/10 overflow-hidden" 
                                                    title="Proses Checkout"
                                                    onclick="return confirm('Proses keluar untuk kendaraan ini?')">
                                                <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                                                <i class="fa-solid fa-right-from-bracket text-xs"></i>
                                            </button>
                                        </form>
                                    @elseif($currentStatus === 'booking')
                                        <form action="{{ route('transaksi.accept-reservation', $transaksi->id_parkir) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="group relative w-9 h-9 flex items-center justify-center bg-emerald-500 text-slate-950 rounded-lg hover:bg-emerald-400 transition-all active:scale-90 shadow-lg shadow-emerald-500/10 overflow-hidden"
                                                    title="Konfirmasi Kedatangan">
                                                <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                                                <i class="fa-solid fa-check text-xs"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('transaksi.show', $transaksi->id_parkir) }}"
                                       class="w-9 h-9 flex items-center justify-center text-slate-500 hover:text-white hover:bg-white/5 rounded-lg transition-all"
                                       title="Lihat Detail">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>

                                    @if(auth()->user()->role === 'admin' && $currentStatus === 'riwayat')
                                        <a href="{{ route('transaksi.print', $transaksi->id_parkir) }}"
                                           class="w-9 h-9 flex items-center justify-center text-indigo-400 hover:text-white hover:bg-indigo-500 rounded-lg transition-all"
                                           title="Cetak Struk">
                                            <i class="fa-solid fa-print text-xs"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-slate-900 border border-white/5 rounded-2xl flex items-center justify-center text-slate-700 mb-4">
                                    <i class="fa-solid fa-folder-open text-2xl"></i>
                                </div>
                                <h3 class="text-white font-bold mb-1">Tidak ada data ditemukan</h3>
                                <p class="text-slate-500 text-xs">Coba ubah filter atau pencarian Anda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transaksis->hasPages())
        <div class="px-8 py-6 border-t border-white/5 bg-white/[0.01]">
            {{ $transaksis->links() }}
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
<style>
    @keyframes shimmer {
        100% { transform: translateX(100%); }
    }
</style>
@endpush
@endsection
