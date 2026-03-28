@extends('layouts.app')

@section('title', 'Vehicle Inventory')

@section('content')
<div class="p-8 relative z-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                    Asset Registry
                </span>
            </div>
            <h1 class="text-4xl font-bold tracking-tight text-white">Vehicle <span class="text-emerald-500">Database</span></h1>
            <p class="text-slate-400 text-sm mt-2">Manage and monitor all registered vehicles in the system.</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('kendaraan.create') }}" class="group relative px-6 py-3 bg-emerald-500 text-slate-950 font-bold text-xs uppercase tracking-widest rounded-xl transition-all hover:bg-emerald-400 hover:shadow-[0_0_20px_rgba(16,185,129,0.4)] flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Register Vehicle
            </a>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="mb-8 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-4 flex items-center gap-4 animate-fade-in">
            <div class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center text-emerald-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <p class="text-sm font-bold text-emerald-500 uppercase tracking-widest">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Main Data Table -->
    <div class="card-pro !p-0 overflow-hidden shadow-2xl">
        <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
            <h2 class="text-sm font-bold text-white uppercase tracking-widest">Registered Assets <span class="text-slate-500 ml-2 font-medium">({{ $items->total() }} total)</span></h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/[0.01] text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        <th class="px-8 py-4">ID</th>
                        <th class="px-8 py-4">Vehicle Identity</th>
                        <th class="px-8 py-4">Classification</th>
                        <th class="px-8 py-4">Visual Props</th>
                        <th class="px-8 py-4">Owner Association</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($items as $item)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-8 py-5">
                                <span class="text-[10px] font-mono font-bold text-emerald-500/80">#{{ str_pad($item->id_kendaraan, 5, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-slate-800 border border-white/5 flex flex-col items-center justify-center font-bold text-white group-hover:border-emerald-500/30 transition-colors">
                                        <span class="text-[8px] text-slate-500 leading-none mb-0.5">{{ substr($item->plat_nomor ?? '-', 0, 2) }}</span>
                                        <span class="text-sm leading-none">{{ substr($item->plat_nomor ?? '-', 2, 4) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-white tracking-tight">{{ $item->plat_nomor }}</p>
                                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">Asset: KND-{{ $item->id_kendaraan }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                @php
                                    $jenisStyles = [
                                        'motor' => 'bg-indigo-500/10 text-indigo-500 border-indigo-500/20',
                                        'mobil' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                                    ];
                                    $jenisStyle = $jenisStyles[strtolower($item->jenis_kendaraan)] ?? 'bg-slate-800 text-slate-400 border-white/5';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black uppercase border {{ $jenisStyle }}">
                                    {{ $item->jenis_kendaraan }}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                @if($item->warna)
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full border border-white/10" style="background-color: {{ $item->warna }}"></div>
                                        <span class="text-xs font-medium text-slate-300">{{ ucfirst($item->warna) }}</span>
                                    </div>
                                @else
                                    <span class="text-[10px] font-bold text-slate-600 uppercase tracking-widest">Undefined</span>
                                @endif
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-900 border border-white/5 flex items-center justify-center text-emerald-500 font-bold text-[10px]">
                                        {{ strtoupper(substr($item->pemilik ?? 'N', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-white">{{ $item->pemilik ?? 'General Public' }}</p>
                                        @if($item->user)
                                            <p class="text-[9px] text-slate-500 font-medium italic">Account: {{ $item->user->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right space-x-2">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('kendaraan.edit', $item) }}"
                                       class="p-2 bg-amber-500/10 hover:bg-amber-500 text-amber-500 hover:text-slate-950 rounded-lg border border-amber-500/20 transition-all"
                                       title="Edit Record">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <form action="{{ route('kendaraan.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Archive this vehicle? Historic transaction data will be preserved.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white rounded-lg border border-rose-500/20 transition-all"
                                                title="Archive Asset">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-24 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-slate-900 border border-white/5 rounded-[2rem] flex items-center justify-center text-slate-700 mb-6">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-white mb-2">No vehicles registered</h3>
                                    <p class="text-slate-500 text-sm max-w-xs mx-auto">Start building your database by registering vehicles for automated tracking.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
        <div class="px-8 py-6 border-t border-white/5 bg-white/[0.01]">
            {{ $items->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
