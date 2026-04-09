@extends('layouts.app')

@section('title', 'Detail Transaksi #' . str_pad($item->id_parkir, 6, '0', STR_PAD_LEFT))

@section('content')
<div class="p-4 sm:p-8 relative z-10 animate-fade-in" style="background:#020617;min-height:100vh;">
    {{-- Decorative Background --}}
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="max-w-4xl mx-auto relative z-10">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-black uppercase tracking-widest rounded-full border border-emerald-500/20">Transaction Detail</span>
                    @if($item->status === 'masuk')
                        <span class="px-3 py-1 bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest rounded-full border border-blue-500/20 animate-pulse">Active Session</span>
                    @else
                        <span class="px-3 py-1 bg-slate-500/10 text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-full border border-slate-500/20">Completed</span>
                    @endif
                </div>
                <h1 class="text-4xl font-black tracking-tight text-white uppercase">ID <span class="text-emerald-500">#{{ str_pad($item->id_parkir, 6, '0', STR_PAD_LEFT) }}</span></h1>
                <p class="text-slate-400 text-sm mt-2 font-medium tracking-wide">Rincian lengkap aktivitas parkir dan status pembayaran.</p>
            </div>
            <a href="{{ url()->previous() ?: route('transaksi.index') }}"
               class="group px-6 py-3.5 bg-white/5 border border-white/5 rounded-2xl text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-white hover:bg-white/10 transition-all flex items-center gap-3 active:scale-95">
                <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Primary Stats & Vehicle --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Main Card: Vehicle & Area --}}
                <div class="rounded-3xl border overflow-hidden p-8" style="background:#0d1526;border-color:rgba(255,255,255,0.07);">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 mb-8">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500 shadow-inner">
                                <i class="fa-solid fa-car text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-white tracking-tight uppercase">{{ $item->kendaraan->plat_nomor ?? 'No Plate' }}</h3>
                                <p class="text-slate-500 text-xs font-bold uppercase tracking-widest">{{ $item->kendaraan->jenis_kendaraan ?? 'Vehicle' }} · {{ $item->kendaraan->warna ?? 'Unknown Color' }}</p>
                            </div>
                        </div>
                        <div class="text-left sm:text-right">
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Area Lokasi</p>
                            <div class="flex items-center sm:justify-end gap-2 text-white font-bold">
                                <i class="fa-solid fa-location-dot text-emerald-500"></i>
                                <span>{{ $item->area->nama_area ?? 'Main Area' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 pt-8 border-t border-white/5">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">Waktu Masuk</label>
                                <p class="text-white font-bold flex items-center gap-2">
                                    <i class="fa-regular fa-calendar-check text-emerald-500/50"></i>
                                    {{ $item->waktu_masuk ? $item->waktu_masuk->format('d M Y · H:i') : '-' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">Waktu Keluar</label>
                                <p class="text-white font-bold flex items-center gap-2">
                                    <i class="fa-regular fa-calendar-xmark text-rose-500/50"></i>
                                    @if($item->waktu_keluar)
                                        {{ $item->waktu_keluar->format('d M Y · H:i') }}
                                    @else
                                        <span class="text-blue-400">Masih Parkir</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">Durasi Sesi</label>
                                <p class="text-white font-bold flex items-center gap-2">
                                    <i class="fa-regular fa-clock text-blue-500/50"></i>
                                    @if($item->waktu_masuk)
                                        @php
                                            $end = $item->waktu_keluar ?? now();
                                            $diff = $item->waktu_masuk->diff($end);
                                            $hours = ($diff->days * 24) + $diff->h;
                                        @endphp
                                        {{ $hours }} Jam {{ $diff->i }} Menit
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">Tarif Berlaku</label>
                                <p class="text-white font-bold flex items-center gap-2">
                                    <i class="fa-solid fa-tag text-emerald-500/50"></i>
                                    Rp {{ number_format($item->tarif->tarif_perjam ?? 0, 0, ',', '.') }} / jam
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Operator & System Info --}}
                <div class="rounded-3xl border overflow-hidden" style="background:#0d1526;border-color:rgba(255,255,255,0.07);">
                    <div class="px-6 py-4 border-b flex items-center gap-3" style="border-color:rgba(255,255,255,0.05);">
                        <i class="fa-solid fa-shield-halved text-slate-500 text-xs"></i>
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">System Audit Log</p>
                    </div>
                    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-slate-400">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <div>
                                <p class="text-[8px] font-black text-slate-500 uppercase tracking-widest">Operator In-charge</p>
                                <p class="text-xs font-bold text-white uppercase">{{ $item->user->name ?? 'System' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-slate-400">
                                <i class="fa-solid fa-fingerprint"></i>
                            </div>
                            <div>
                                <p class="text-[8px] font-black text-slate-500 uppercase tracking-widest">Slot Code</p>
                                <p class="text-xs font-bold text-emerald-500 uppercase tracking-widest">{{ $item->parkingSlot?->code ?? 'Unassigned' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Payment Summary --}}
            <div class="space-y-6">
                {{-- Payment Card --}}
                <div class="rounded-3xl border overflow-hidden relative group" style="background: linear-gradient(145deg, #10b981 0%, #059669 100%); border-color:rgba(255,255,255,0.1);">
                    <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform duration-700">
                        <i class="fa-solid fa-wallet text-8xl text-white"></i>
                    </div>

                    <div class="p-8 relative z-10">
                        <p class="text-[10px] font-black text-white/60 uppercase tracking-widest mb-1">Total Biaya Parkir</p>
                        <h2 class="text-4xl font-black text-white tracking-tighter mb-6">Rp {{ number_format($item->biaya_total ?? 0, 0, ',', '.') }}</h2>

                        <div class="space-y-4 pt-6 border-t border-white/10">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-white/70 uppercase tracking-wider">Status Bayar</span>
                                @if($item->status_pembayaran === 'berhasil')
                                    <span class="px-2.5 py-1 bg-white/20 text-white text-[9px] font-black uppercase tracking-widest rounded-lg border border-white/30">Paid</span>
                                @else
                                    <span class="px-2.5 py-1 bg-black/20 text-white text-[9px] font-black uppercase tracking-widest rounded-lg border border-white/10">Unpaid</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-white/70 uppercase tracking-wider">Metode</span>
                                <span class="text-xs font-black text-white uppercase">{{ $item->pembayaran->metode_pembayaran ?? 'Cash/NestonPay' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="px-8 py-4 bg-black/10 flex items-center justify-between border-t border-white/5">
                        <span class="text-[8px] font-black text-white/50 uppercase tracking-[0.2em]">Verified Payment</span>
                        <i class="fa-solid fa-circle-check text-white/50 text-xs"></i>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col gap-3">
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'petugas')
                        <a href="{{ route('transaksi.edit', $item->id_parkir) }}"
                           class="w-full py-4 bg-white/5 border border-white/5 rounded-2xl text-[10px] font-black text-white uppercase tracking-widest hover:bg-white/10 transition-all flex items-center justify-center gap-3 active:scale-[0.98]">
                            <i class="fa-solid fa-pen-to-square"></i>
                            Edit Transaksi
                        </a>
                    @endif

                    @if($item->status === 'keluar')
                        <a href="{{ route('transaksi.print', $item->id_parkir) }}" target="_blank"
                           class="w-full py-4 bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all flex items-center justify-center gap-3 active:scale-[0.98] shadow-lg shadow-emerald-500/20">
                            <i class="fa-solid fa-print"></i>
                            Cetak Struk Parkir
                        </a>
                    @endif
                </div>

                {{-- Note Card --}}
                @if($item->catatan)
                <div class="rounded-3xl border p-6" style="background:#0d1526;border-color:rgba(255,255,255,0.07);">
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-3">Catatan Tambahan</p>
                    <p class="text-xs text-slate-400 font-medium leading-relaxed italic">"{{ $item->catatan }}"</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }
</style>
@endsection
