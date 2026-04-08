@extends('layouts.app')

@section('title', 'Live Parking Map')

@section('content')
<div class="p-4 lg:p-8 h-[calc(100vh-64px)] flex flex-col overflow-hidden">
    <div class="max-w-[1600px] mx-auto w-full flex-1 flex flex-col min-h-0 gap-6">
        
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shrink-0">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                    <h1 class="text-2xl lg:text-3xl font-black tracking-tighter text-white uppercase">Live <span class="text-emerald-500">Map</span> Monitoring</h1>
                </div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-widest">Real-time occupancy & security status</p>
            </div>

            <div class="flex items-center gap-3 bg-slate-900/50 p-1.5 rounded-2xl border border-white/5 backdrop-blur-xl">
                <div class="flex items-center gap-2 px-4 py-2">
                    <i class="fa-solid fa-layer-group text-slate-500 text-xs"></i>
                    <select id="area-selector" onchange="window.location.href='{{ route('parking.map.index') }}?map_id=' + this.value"
                            class="bg-transparent text-white text-[10px] font-black uppercase tracking-widest focus:outline-none cursor-pointer">
                        @foreach($maps as $m)
                            <option value="{{ $m->id_area }}" {{ $area && $area->id_area == $m->id_area ? 'selected' : '' }} class="bg-slate-900">{{ $m->nama_area }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-px h-4 bg-white/10"></div>
                <button onclick="refreshMapData()" class="p-2.5 text-slate-400 hover:text-white transition-colors group" title="Manual Refresh">
                    <i class="fa-solid fa-arrows-rotate text-xs group-active:rotate-180 transition-transform duration-500"></i>
                </button>
            </div>
        </div>

        <div class="flex-1 flex flex-col lg:flex-row gap-6 min-h-0">
            <!-- Main Map Canvas Area -->
            <div class="flex-1 min-h-0 card-pro !p-0 bg-slate-950 border-white/5 shadow-2xl relative overflow-hidden group flex flex-col">
                
                <!-- Map Controls Overlay -->
                <div class="absolute top-6 right-6 z-20 flex flex-col gap-2">
                    <div class="bg-slate-900/90 backdrop-blur-xl border border-white/10 rounded-xl overflow-hidden flex flex-col shadow-2xl">
                        <button onclick="zoomMap(1.2)" class="p-3 text-white hover:bg-emerald-500 hover:text-slate-950 transition-all active:scale-90 border-b border-white/5">
                            <i class="fa-solid fa-plus text-xs"></i>
                        </button>
                        <button onclick="zoomMap(0.8)" class="p-3 text-white hover:bg-emerald-500 hover:text-slate-950 transition-all active:scale-90 border-b border-white/5">
                            <i class="fa-solid fa-minus text-xs"></i>
                        </button>
                        <button onclick="resetMapZoom()" class="p-3 text-white hover:bg-emerald-500 hover:text-slate-950 transition-all active:scale-90">
                            <i class="fa-solid fa-compress text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Legend Overlay -->
                <div class="absolute bottom-6 left-6 z-20 bg-slate-900/90 backdrop-blur-xl border border-white/10 rounded-2xl p-4 flex flex-wrap items-center gap-6 shadow-2xl pointer-events-none">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.4)]"></div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Available</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-rose-500 shadow-[0_0_10px_rgba(244,63,94,0.4)]"></div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Occupied</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-amber-500 shadow-[0_0_10px_rgba(245,158,11,0.4)]"></div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Reserved</span>
                    </div>
                </div>

                <!-- Map Container -->
                <div id="map-viewport" class="flex-1 w-full h-full cursor-grab active:cursor-grabbing overflow-hidden bg-[radial-gradient(#ffffff05_1px,transparent_1px)] [background-size:24px_24px]">
                    <div id="map-canvas-wrapper" class="relative origin-top-left transition-transform duration-300 ease-out"
                         data-image="{{ $area->map_image_url }}"
                         data-width="{{ $area->map_width ?: 1000 }}"
                         data-height="{{ $area->map_height ?: 800 }}"
                         data-id="{{ $area->id_area }}">
                        
                        <!-- Map Image -->
                        <img id="map-bg-image" src="{{ $area->map_image_url }}" class="absolute inset-0 w-full h-full object-fill select-none pointer-events-none opacity-80" alt="Parking Map">
                        
                        <!-- Dynamic Layers -->
                        <div id="slots-layer" class="absolute inset-0 pointer-events-none"></div>
                        <div id="cameras-layer" class="absolute inset-0 pointer-events-none"></div>
                    </div>
                </div>

                <!-- Loading Spinner -->
                <div id="map-loading-overlay" class="absolute inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex flex-col items-center justify-center transition-all duration-500">
                    <div class="relative w-20 h-20 mb-6">
                        <div class="absolute inset-0 border-4 border-emerald-500/10 rounded-full"></div>
                        <div class="absolute inset-0 border-4 border-emerald-500 rounded-full border-t-transparent animate-spin"></div>
                    </div>
                    <p class="text-[10px] font-black text-white uppercase tracking-[0.4em]">Optimizing Vision Engine</p>
                </div>
            </div>

            <!-- Stats Sidebar -->
            <div class="w-full lg:w-80 shrink-0 flex flex-col gap-6 overflow-y-auto lg:pr-1 custom-scrollbar min-h-0">
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="card-pro p-5 border-white/5 bg-white/[0.02] flex flex-col gap-3 group hover:border-emerald-500/30 transition-all">
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest group-hover:text-emerald-400 transition-colors">Available</p>
                        <h3 id="stat-empty" class="text-3xl font-black text-white tracking-tighter">--</h3>
                    </div>
                    <div class="card-pro p-5 border-white/5 bg-white/[0.02] flex flex-col gap-3 group hover:border-rose-500/30 transition-all">
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest group-hover:text-rose-400 transition-colors">Occupied</p>
                        <h3 id="stat-occupied" class="text-3xl font-black text-white tracking-tighter">--</h3>
                    </div>
                </div>

                <!-- Utilization Card -->
                <div class="card-pro p-6 border-white/5 bg-white/[0.02]">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Zone Utilization</p>
                        <span id="stat-util-percent" class="text-xs font-black text-emerald-500 tracking-tighter">--%</span>
                    </div>
                    <div class="w-full h-2 bg-slate-900 rounded-full overflow-hidden mb-2">
                        <div id="stat-util-bar" class="h-full bg-emerald-500 transition-all duration-1000 ease-out" style="width: 0%"></div>
                    </div>
                    <p class="text-[9px] font-bold text-slate-600 uppercase tracking-widest">Current capacity load</p>
                </div>

                <!-- Slot Detail Inspector -->
                <div id="slot-inspector" class="card-pro flex-1 min-h-[300px] border-white/5 bg-white/[0.02] flex flex-col">
                    <div class="flex items-center gap-3 mb-6 p-6 border-b border-white/5 bg-white/[0.01]">
                        <div class="w-2 h-6 bg-emerald-500 rounded-full"></div>
                        <h3 class="text-xs font-black text-white uppercase tracking-widest">Slot Inspector</h3>
                    </div>
                    
                    <div id="inspector-empty" class="flex-1 flex flex-col items-center justify-center p-10 text-center opacity-30">
                        <i class="fa-solid fa-fingerprint text-5xl mb-6"></i>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em]">Select a slot<br>to view details</p>
                    </div>

                    <div id="inspector-content" class="hidden flex-1 p-6 space-y-8 animate-fade-in">
                        <div class="flex items-center justify-between">
                            <h4 id="inspect-code" class="text-4xl font-black text-white tracking-tighter">--</h4>
                            <span id="inspect-status" class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border">--</span>
                        </div>
                        
                        <div class="space-y-6">
                            <div class="space-y-1">
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Plate Number</p>
                                <p id="inspect-plate" class="text-lg font-black text-white tracking-tight uppercase">--</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Status Duration</p>
                                <p id="inspect-time" class="text-sm font-bold text-slate-400">--</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Attached Hardware</p>
                                <p id="inspect-camera" class="text-xs font-bold text-indigo-400 uppercase tracking-widest">--</p>
                            </div>
                        </div>

                        <div id="inspect-actions" class="pt-6 border-t border-white/5 flex flex-col gap-3">
                            <!-- Actions will be injected here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #map-viewport { touch-action: none; }
    #map-canvas-wrapper { 
        box-shadow: 0 50px 100px -20px rgba(0,0,0,0.5);
        background-color: #020617;
    }
    .slot-node {
        position: absolute;
        pointer-events: auto;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', sans-serif;
        font-weight: 900;
        font-size: 10px;
        color: white;
        text-transform: uppercase;
    }
    .slot-node:hover {
        transform: scale(1.05);
        z-index: 10;
        box-shadow: 0 0 20px rgba(255,255,255,0.1);
    }
    .slot-node.active {
        border-color: white !important;
        box-shadow: 0 0 30px rgba(255,255,255,0.2) !important;
        z-index: 11;
    }
    .cam-node {
        position: absolute;
        pointer-events: auto;
        cursor: pointer;
        width: 32px;
        height: 32px;
        background: rgba(99, 102, 241, 0.1);
        border: 2px solid rgba(99, 102, 241, 0.5);
        color: #818cf8;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    .cam-node:hover {
        background: #6366f1;
        color: white;
        transform: scale(1.1);
    }

    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
</style>
@endsection

@push('scripts')
<script>
    // Configuration from PHP
    const MAP_DATA_URL = "{{ route('api.parking.map.data') }}";
    const CURRENT_MAP_ID = "{{ $area->id_area }}";
</script>
<script src="{{ asset('js/parking-map-new.js') }}"></script>
@endpush
