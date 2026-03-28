@extends('layouts.app')

@section('title', 'Active Parking')

@section('content')
    <div class="p-8 relative z-10">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                        Operational Live
                    </span>
                </div>
                <h1 class="text-4xl font-bold tracking-tight text-white">Active <span class="text-emerald-500">Vehicles</span></h1>
                <p class="text-slate-400 text-sm mt-2">Real-time monitoring of vehicles currently within the premises.</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('transaksi.create-check-in') }}" class="group relative px-6 py-3 bg-emerald-500 text-slate-950 font-bold text-xs uppercase tracking-widest rounded-xl transition-all hover:bg-emerald-400 hover:shadow-[0_0_20px_rgba(16,185,129,0.4)] flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    New Entry
                </a>
            </div>
        </div>

        <!-- Notification Alerts -->
        @if($message = Session::get('success'))
            <div class="mb-8 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-4 flex items-center gap-4 animate-fade-in">
                <div class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center text-emerald-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <p class="text-sm font-bold text-emerald-500 uppercase tracking-widest">{{ $message }}</p>
            </div>
        @endif

        @if($message = Session::get('error'))
            <div class="mb-8 bg-rose-500/10 border border-rose-500/20 rounded-2xl p-4 flex items-center gap-4 animate-fade-in">
                <div class="w-8 h-8 bg-rose-500/20 rounded-lg flex items-center justify-center text-rose-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-sm font-bold text-rose-500 uppercase tracking-widest">{{ $message }}</p>
            </div>
        @endif

        <!-- Main Data Table -->
        <div class="card-pro !p-0 overflow-hidden shadow-2xl">
            <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                <h2 class="text-sm font-bold text-white uppercase tracking-widest">Live Inventory <span class="text-slate-500 ml-2 font-medium">({{ $transaksis->total() }} total)</span></h2>
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <input type="text" placeholder="Search plate..." class="bg-slate-900/50 border border-white/5 rounded-lg px-4 py-1.5 text-[10px] text-white focus:outline-none focus:border-emerald-500/50 min-w-[200px]">
                        <svg class="w-3 h-3 text-slate-600 absolute right-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/[0.01] text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                            <th class="px-8 py-4">Transaction ID</th>
                            <th class="px-8 py-4">Vehicle Identity</th>
                            <th class="px-8 py-4">Location</th>
                            <th class="px-8 py-4">Session Info</th>
                            <th class="px-8 py-4">Operator</th>
                            <th class="px-8 py-4 text-right">Actions</th>
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
                                    <div class="w-12 h-12 rounded-xl bg-slate-800 border border-white/5 flex flex-col items-center justify-center font-bold text-white group-hover:border-emerald-500/30 transition-colors">
                                        <span class="text-[8px] text-slate-500 leading-none mb-0.5">{{ substr($transaksi->kendaraan->plat_nomor ?? '-', 0, 2) }}</span>
                                        <span class="text-sm leading-none">{{ substr($transaksi->kendaraan->plat_nomor ?? '-', 2, 4) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-white tracking-tight">{{ $transaksi->kendaraan->plat_nomor ?? '-' }}</p>
                                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">{{ $transaksi->kendaraan->jenis_kendaraan ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-white">{{ $transaksi->area->nama_area ?? '-' }}</span>
                                    <span class="text-[10px] text-slate-500 font-medium">Slot: {{ $transaksi->slot->code ?? 'Not Assigned' }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-white">{{ $transaksi->waktu_masuk->format('H:i') }} <span class="text-slate-600 text-[10px] ml-1">{{ $transaksi->waktu_masuk->format('d/m/y') }}</span></span>
                                    @php
                                        $durasi = now()->diffInMinutes($transaksi->waktu_masuk);
                                        $jam = intdiv($durasi, 60);
                                        $menit = $durasi % 60;
                                    @endphp
                                    <span class="text-[10px] font-bold text-amber-500 uppercase tracking-widest mt-1">Duration: {{ $jam }}h {{ $menit }}m</span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-800 border border-white/5 flex items-center justify-center text-[10px] text-slate-400 font-bold">
                                        {{ substr($transaksi->user->name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="text-xs text-slate-300 font-medium">{{ explode(' ', $transaksi->user->name ?? '-')[0] }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right space-x-2">
                                <form action="{{ route('transaksi.checkOut', $transaksi->id_parkir) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" 
                                            class="px-4 py-2 bg-amber-500/10 hover:bg-amber-500 text-amber-500 hover:text-slate-950 font-bold rounded-lg border border-amber-500/20 transition-all text-[10px] uppercase tracking-widest"
                                            onclick="return confirm('Process check-out for this vehicle?')">
                                        Check-Out
                                    </button>
                                </form>
                                @if(auth()->user()->role === 'admin')
                                <a href="{{ route('transaksi.print', $transaksi->id_parkir) }}"
                                   class="px-4 py-2 bg-indigo-500/10 hover:bg-indigo-500 text-indigo-500 hover:text-white font-bold rounded-lg border border-indigo-500/20 transition-all text-[10px] uppercase tracking-widest inline-block">
                                    Receipt
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-8 py-24 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-slate-900 border border-white/5 rounded-[2rem] flex items-center justify-center text-slate-700 mb-6">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-white mb-2">No active sessions found</h3>
                                    <p class="text-slate-500 text-sm max-w-xs mx-auto">Currently there are no vehicles parked within the monitoring system.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Container -->
            @if($transaksis->hasPages())
            <div class="px-8 py-6 border-t border-white/5 bg-white/[0.01]">
                {{ $transaksis->links() }}
            </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
<style>
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush
