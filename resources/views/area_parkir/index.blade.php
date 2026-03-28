@extends('layouts.app')

@section('title','Parking Areas')

@section('content')
<div class="p-8 relative z-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                    Infrastructure Console
                </span>
            </div>
            <h1 class="text-4xl font-bold tracking-tight text-white">Parking <span class="text-emerald-500">Zones</span></h1>
            <p class="text-slate-400 text-sm mt-2">Manage physical parking locations and capacity limits.</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('area-parkir.create') }}" class="group relative px-6 py-3 bg-emerald-500 text-slate-950 font-bold text-xs uppercase tracking-widest rounded-xl transition-all hover:bg-emerald-400 hover:shadow-[0_0_20px_rgba(16,185,129,0.4)] flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create New Zone
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
            <h2 class="text-sm font-bold text-white uppercase tracking-widest">Zone Inventory <span class="text-slate-500 ml-2 font-medium">({{ $areas->total() }} total)</span></h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/[0.01] text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        <th class="px-8 py-4">ID</th>
                        <th class="px-8 py-4">Area Identity</th>
                        <th class="px-8 py-4">Total Capacity</th>
                        <th class="px-8 py-4">Current Load</th>
                        <th class="px-8 py-4">Utilization</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($areas as $area)
                        @php
                            $percentage = $area->kapasitas > 0 ? ($area->terisi / $area->kapasitas * 100) : 0;
                            $statusColor = $percentage >= 90 ? 'bg-rose-500' : ($percentage >= 70 ? 'bg-amber-500' : 'bg-emerald-500');
                            $statusText = $percentage >= 90 ? 'Critical' : ($percentage >= 70 ? 'High' : 'Normal');
                            $statusBadge = $percentage >= 90 ? 'bg-rose-500/10 text-rose-500 border-rose-500/20' : ($percentage >= 70 ? 'bg-amber-500/10 text-amber-500 border-amber-500/20' : 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20');
                        @endphp
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-8 py-5">
                                <span class="text-[10px] font-mono font-bold text-emerald-500/80">#{{ str_pad($area->id_area, 3, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-slate-800 border border-white/5 flex items-center justify-center text-emerald-500 group-hover:border-emerald-500/30 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-white tracking-tight">{{ $area->nama_area }}</p>
                                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">System Identifier: AREA-{{ $area->id_area }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-white">{{ $area->kapasitas }}</span>
                                    <span class="text-[10px] text-slate-500 font-bold uppercase">Slots</span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 {{ $statusColor }} rounded-full animate-pulse"></div>
                                    <span class="text-sm font-bold text-white">{{ $area->terisi ?? 0 }}</span>
                                    <span class="text-[10px] text-slate-500 font-bold uppercase">Occupied</span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="w-32">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase border {{ $statusBadge }}">
                                            {{ $statusText }}
                                        </span>
                                        <span class="text-[10px] font-bold text-slate-400">{{ number_format($percentage, 0) }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-800 rounded-full h-1 overflow-hidden">
                                        <div class="{{ $statusColor }} h-1 rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right space-x-2">
                                <div class="flex items-center justify-end gap-2">
                                    @if($area->parkingMap)
                                    <a href="{{ route('parking-maps.edit', $area->parkingMap) }}"
                                       class="p-2 bg-emerald-500/10 hover:bg-emerald-500 text-emerald-500 hover:text-slate-950 rounded-lg border border-emerald-500/20 transition-all"
                                       title="Edit layout peta">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h2l3 7 4-4 4 8 3-6h2"></path></svg>
                                    </a>
                                    @else
                                    <form action="{{ route('area-parkir.create-layout', $area) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 bg-slate-800 hover:bg-emerald-500 text-slate-500 hover:text-slate-950 rounded-lg border border-white/5 transition-all" title="Buat layout peta">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        </button>
                                    </form>
                                    @endif
                                    
                                    <a href="{{ route('area-parkir.edit', $area) }}"
                                       class="p-2 bg-amber-500/10 hover:bg-amber-500 text-amber-500 hover:text-slate-950 rounded-lg border border-amber-500/20 transition-all"
                                       title="Edit Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <form action="{{ route('area-parkir.destroy', $area) }}" method="POST" class="inline" onsubmit="return confirm('Archive this zone? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white rounded-lg border border-rose-500/20 transition-all"
                                                title="Archive Zone">
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
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-white mb-2">No zones defined</h3>
                                    <p class="text-slate-500 text-sm max-w-xs mx-auto">Create your first parking area to start monitoring capacity.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($areas->hasPages())
        <div class="px-8 py-6 border-t border-white/5 bg-white/[0.01]">
            {{ $areas->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
