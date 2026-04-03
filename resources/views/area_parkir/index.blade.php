@extends('layouts.app')

@section('title', 'Parking Areas')

@section('content')
<div class="p-8 relative z-10">

    {{-- Header --}}
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
        <a href="{{ route('area-parkir.create') }}"
           class="px-6 py-3 bg-emerald-500 text-slate-950 font-bold text-xs uppercase tracking-widest rounded-xl transition-all hover:bg-emerald-400 hover:shadow-[0_0_20px_rgba(16,185,129,0.4)] flex items-center gap-2 w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create New Zone
        </a>
    </div>

    @if(session('success'))
    <div class="mb-8 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-4 flex items-center gap-4 animate-fade-in">
        <div class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center text-emerald-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <p class="text-sm font-bold text-emerald-500 uppercase tracking-widest">{{ session('success') }}</p>
    </div>
    @endif

    {{--
        VARIANT 1: Card Grid
        Setiap area jadi card dengan visualisasi utilization yang lebih kaya.
        Header row menampilkan total zones.
    --}}

    {{-- Summary bar --}}
    <div class="flex items-center justify-between mb-6">
        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">
            Zone Inventory — <span class="text-white">{{ $areas->total() }}</span> total
        </p>
    </div>

    @if($areas->isEmpty())
    <div class="card-pro flex flex-col items-center py-24 text-center">
        <div class="w-20 h-20 bg-slate-900 border border-white/5 rounded-[2rem] flex items-center justify-center text-slate-700 mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <h3 class="text-lg font-bold text-white mb-2">No zones defined</h3>
        <p class="text-slate-500 text-sm max-w-xs">Create your first parking area to start monitoring capacity.</p>
    </div>
    @else

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($areas as $area)
        @php
            $percentage  = $area->kapasitas > 0 ? ($area->terisi / $area->kapasitas * 100) : 0;
            $barColor    = $percentage >= 90 ? 'bg-rose-500'   : ($percentage >= 70 ? 'bg-amber-500'   : 'bg-emerald-500');
            $badgeCls    = $percentage >= 90 ? 'bg-rose-500/10 text-rose-400 border-rose-500/20'
                         : ($percentage >= 70 ? 'bg-amber-500/10 text-amber-400 border-amber-500/20'
                                              : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20');
            $statusText  = $percentage >= 90 ? 'Critical' : ($percentage >= 70 ? 'High' : 'Normal');
            $dotColor    = $percentage >= 90 ? 'bg-rose-500' : ($percentage >= 70 ? 'bg-amber-500' : 'bg-emerald-500');
        @endphp

        <div class="card-pro !p-0 overflow-hidden group hover:border-white/10 transition-colors">

            {{-- Card top: image or gradient placeholder --}}
            <div class="h-24 relative overflow-hidden bg-slate-900">
                @if($area->map_image_url)
                    <img src="{{ $area->map_image_url }}"
                         class="w-full h-full object-cover opacity-40">
                @endif
                <div class="absolute inset-0 bg-gradient-to-b from-transparent to-slate-900/80"></div>

                {{-- ID badge --}}
                <div class="absolute top-3 left-4">
                    <span class="text-[9px] font-mono font-bold text-emerald-400/80">#{{ str_pad($area->id_area, 3, '0', STR_PAD_LEFT) }}</span>
                </div>

                {{-- Status badge --}}
                <div class="absolute top-3 right-4">
                    <span class="px-2 py-0.5 rounded border text-[9px] font-black uppercase tracking-widest {{ $badgeCls }}">
                        {{ $statusText }}
                    </span>
                </div>

                {{-- Utilization % overlay --}}
                <div class="absolute bottom-3 left-4">
                    <span class="text-2xl font-black text-white leading-none">{{ number_format($percentage, 0) }}<span class="text-base font-bold text-slate-400">%</span></span>
                </div>
            </div>

            {{-- Card body --}}
            <div class="p-5">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <h3 class="text-sm font-bold text-white tracking-tight">{{ $area->nama_area }}</h3>
                    <div class="flex items-center gap-1.5 px-2 py-0.5 bg-slate-800 rounded-full border border-white/5">
                        <i class="fa-solid fa-location-dot text-[8px] text-emerald-500"></i>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $area->daerah ?? 'Unknown' }}</span>
                    </div>
                </div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-4">AREA-{{ $area->id_area }}</p>

                {{-- Progress bar --}}
                <div class="w-full bg-slate-800 rounded-full h-1 overflow-hidden mb-4">
                    <div class="{{ $barColor }} h-1 rounded-full transition-all duration-1000" style="width:{{ $percentage }}%"></div>
                </div>

                {{-- Stats row --}}
                <div class="grid grid-cols-3 gap-3 mb-5">
                    <div>
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-0.5">Kapasitas</p>
                        <p class="text-sm font-bold text-white">{{ $area->kapasitas }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-0.5">Terisi</p>
                        <div class="flex items-center gap-1.5">
                            <div class="w-1.5 h-1.5 rounded-full {{ $dotColor }} animate-pulse"></div>
                            <p class="text-sm font-bold text-white">{{ $area->terisi ?? 0 }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-0.5">Mapped</p>
                        <p class="text-sm font-bold {{ $area->slots_count == $area->kapasitas ? 'text-emerald-400' : 'text-slate-400' }}">
                            {{ $area->slots_count }}/{{ $area->kapasitas }}
                        </p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 pt-4 border-t border-white/[0.05]">
                    <a href="{{ route('area-parkir.design', $area->id_area) }}"
                       class="flex-1 py-2 text-center text-[10px] font-bold uppercase tracking-widest rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/20 hover:bg-blue-500 hover:text-white transition-all">
                        Design
                    </a>
                    <a href="{{ route('area-parkir.edit', $area->id_area) }}"
                       class="flex-1 py-2 text-center text-[10px] font-bold uppercase tracking-widest rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500 hover:text-slate-950 transition-all">
                        Edit
                    </a>
                    <form action="{{ route('area-parkir.destroy', $area->id_area) }}" method="POST"
                          onsubmit="return confirm('Hapus area ini? Seluruh data slot terkait akan terhapus.')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="p-2 bg-rose-500/10 text-rose-400 rounded-lg border border-rose-500/20 hover:bg-rose-500 hover:text-white transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($areas->hasPages())
    <div class="mt-8">{{ $areas->links() }}</div>
    @endif

    @endif
</div>
@endsection
