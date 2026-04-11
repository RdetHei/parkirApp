@extends('layouts.app')

@section('title', 'Kamera Manajemen')

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
            <h1 class="text-4xl font-bold tracking-tight text-white">Device <span class="text-emerald-500">Cameras</span></h1>
            <p class="text-slate-400 text-sm mt-2">Manage and monitor all connected camera devices in the system.</p>
        </div>
        @if((auth()->user()->role ?? null) === 'admin')
            <div class="flex items-center gap-4">
                <a href="{{ route('kamera.create') }}" class="group relative px-6 py-3 bg-emerald-500 text-slate-950 font-bold text-xs uppercase tracking-widest rounded-xl transition-all hover:bg-emerald-400 hover:shadow-[0_0_20px_rgba(16,185,129,0.4)] flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add New Camera
                </a>
            </div>
        @endif
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

    <!-- Filter Console -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">
            Connected Devices — <span class="text-white">{{ $items->total() }}</span> total
        </p>

        <form action="{{ request()->routeIs('petugas.kamera.index') ? route('petugas.kamera.index') : route('kamera.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative min-w-[240px]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name..."
                       class="block w-full pl-10 pr-3 py-2 bg-slate-900/50 border border-white/10 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all">
            </div>

            <select name="tipe" onchange="this.form.submit()"
                    class="bg-slate-900/50 border border-white/10 rounded-xl px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all min-w-[120px]">
                <option value="" class="bg-slate-900">All Types</option>
                <option value="scanner" class="bg-slate-900" {{ request('tipe') == 'scanner' ? 'selected' : '' }}>Scanner</option>
                <option value="viewer" class="bg-slate-900" {{ request('tipe') == 'viewer' ? 'selected' : '' }}>Viewer</option>
            </select>

            @if(request()->anyFilled(['q', 'tipe']))
                <a href="{{ request()->routeIs('petugas.kamera.index') ? route('petugas.kamera.index') : route('kamera.index') }}" class="p-2 text-slate-500 hover:text-white transition-colors" title="Clear Filters">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
            @endif
        </form>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($items as $item)
            <div class="card-pro !p-0 overflow-hidden group hover:border-white/10 transition-colors">
                {{-- Card top header --}}
                <div class="h-24 relative overflow-hidden bg-slate-900">
                    <div class="absolute inset-0 bg-gradient-to-br from-slate-800 to-slate-900 opacity-50"></div>
                    <div class="absolute inset-0 flex items-center justify-center opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-20 h-20 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-slate-950/80"></div>

                    {{-- ID/Type badge --}}
                    <div class="absolute top-3 left-4">
                        <span class="text-[9px] font-mono font-bold text-emerald-400/80 uppercase tracking-widest">
                            #CAM-{{ str_pad($item->id_kamera, 3, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>

                    {{-- Status badge --}}
                    <div class="absolute top-3 right-4">
                        <div class="flex items-center gap-1.5 px-2 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-lg shadow-lg shadow-emerald-500/5">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                            <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest leading-none">Online</span>
                        </div>
                    </div>

                    {{-- Device name overlay --}}
                    <div class="absolute bottom-3 left-4 right-4 flex items-end justify-between">
                        <h3 class="text-sm font-bold text-white tracking-tight truncate mr-2">{{ $item->nama }}</h3>
                        @php
                            $tipeStyle = $item->tipe === \App\Models\Camera::TIPE_SCANNER ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20' : 'bg-purple-500/10 text-purple-400 border-purple-500/20';
                        @endphp
                        <span class="px-2 py-0.5 {{ $tipeStyle }} text-[8px] font-black uppercase tracking-widest rounded border leading-none">
                            {{ $item->tipe }}
                        </span>
                    </div>
                </div>

                {{-- Card body --}}
                <div class="p-5">
                    <div class="space-y-4">
                        <div>
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Stream Source URL</p>
                            <div class="flex items-center gap-2 p-2 bg-slate-950/50 rounded-lg border border-white/5 group/url">
                                <svg class="w-3 h-3 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                <code class="text-[10px] font-mono font-bold text-slate-400 truncate group-hover/url:text-emerald-400 transition-colors">{{ $item->url }}</code>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3 bg-slate-900/40 rounded-xl border border-white/5">
                                <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest mb-1">Configuration</p>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[10px] font-bold text-white">
                                        @if($item->is_default)
                                            <span class="text-emerald-400">Primary</span> Device
                                        @else
                                            Auxiliary
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="p-3 bg-slate-900/40 rounded-xl border border-white/5">
                                <p class="text-[8px] font-bold text-slate-500 uppercase tracking-widest mb-1">Assigned to</p>
                                <p class="text-[10px] font-bold text-white">Neston Park <span class="text-slate-500 font-medium">#1</span></p>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="pt-5 mt-5 border-t border-white/5 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            @if((auth()->user()->role ?? null) === 'admin')
                                <div class="flex items-center bg-slate-900 border border-white/5 rounded-xl p-1 gap-1">
                                    <a href="{{ route('kamera.edit', $item) }}"
                                       class="w-9 h-9 flex items-center justify-center text-slate-500 hover:text-white hover:bg-white/5 rounded-lg transition-all"
                                       title="Edit Kamera">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    
                                    <form action="{{ route('kamera.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Archive this camera? All associated parking maps will be updated.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-9 h-9 flex items-center justify-center text-rose-500 hover:text-white hover:bg-rose-500 rounded-lg transition-all" title="Archive Device">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Device Secured</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-1">
                            <i class="fa-solid fa-signal text-[8px] text-emerald-500"></i>
                            <span class="text-[8px] font-black text-slate-500 uppercase tracking-widest">Stable</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-24 card-pro border-dashed flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-slate-900 border border-white/5 rounded-[2rem] flex items-center justify-center text-slate-700 mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">No cameras connected</h3>
                <p class="text-slate-500 text-sm max-w-xs mx-auto">Start by adding your first camera device to monitor parking zones.</p>
            </div>
        @endforelse
    </div>

    @if($items->hasPages())
        <div class="mt-8">
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection
