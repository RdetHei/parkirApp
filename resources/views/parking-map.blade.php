@extends('layouts.app')

@section('title', 'Peta Parkir')

@section('content')
<div class="parking-map-page flex w-full min-w-0 flex-1 min-h-0 flex flex-col" style="background:#020617;">
<div class="w-full min-w-0 flex-1 min-h-0 p-6 lg:p-8">
<div class="max-w-7xl mx-auto w-full min-w-0">

    {{--
        VARIANT 2: Peta lebar kiri + sidebar kanan
        Sidebar: area selector, summary stats, legend, live indicator.
        Peta mengisi sisa ruang di sebelah kiri.
    --}}

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Peta Interaktif Parkir</h1>
                <p class="text-slate-500 text-xs mt-0.5">Real-time slot & kamera pemantau</p>
            </div>
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg border" style="background:rgba(255,255,255,0.03);border-color:rgba(255,255,255,0.07);">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Live</span>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-5 items-start">

        {{-- ── MAP AREA ── --}}
        <div class="flex-1 w-full min-w-0 bg-[#0d1526] rounded-2xl border border-white/5 shadow-2xl overflow-hidden">
            @if($area && $area->map_image_url)
            <div class="relative h-[450px] sm:h-[600px] lg:h-[720px] bg-[#020617]">
                <div id="parking-map"
                     class="w-full h-full relative z-10"
                     data-image-url="{{ $area->map_image_url }}"
                     data-width="{{ $area->map_width }}"
                     data-height="{{ $area->map_height }}"
                     data-map-id="{{ $area->id_area }}">
                </div>
                
                <!-- Loading Overlay -->
                <div id="map-loader" class="absolute inset-0 z-30 bg-slate-950/80 backdrop-blur-sm flex flex-col items-center justify-center transition-opacity duration-500">
                    <div class="w-12 h-12 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin mb-4"></div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Initializing Map Engine...</p>
                </div>

                {{-- Mini legend bottom-left --}}
                <div class="absolute bottom-6 left-6 flex items-center gap-4 px-4 py-2.5 rounded-2xl z-20 pointer-events-none shadow-2xl"
                     style="background:rgba(15,23,42,0.9); border:1px solid rgba(255,255,255,0.1); backdrop-blur:md;">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                        <span class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">Empty</span>
                    </div>
                    <div class="w-px h-3 bg-white/10"></div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-600"></span>
                        <span class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">Occupied</span>
                    </div>
                    <div class="w-px h-3 bg-white/10"></div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></span>
                        <span class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">Reserved</span>
                    </div>
                </div>
            </div>
            @else
            <div class="flex flex-col items-center justify-center" style="height:640px;">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);">
                    <svg class="w-7 h-7 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
                <h3 class="text-sm font-bold text-white mb-1">Peta Belum Tersedia</h3>
                <p class="text-xs text-slate-600 max-w-xs text-center">Pilih area lain atau hubungi admin.</p>
                @if(auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('area-parkir.index') }}" class="mt-5 px-4 py-2 text-xs font-bold uppercase tracking-widest rounded-xl text-white transition-all"
                   style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.1);">
                    Kelola Area
                </a>
                @endif
            </div>
            @endif
        </div>

        {{-- ── SIDEBAR ── --}}
        <div class="w-full lg:w-60 shrink-0 flex flex-col gap-4">

            {{-- Area selector --}}
            <div class="rounded-2xl overflow-hidden border" style="background:#0d1526;border-color:rgba(255,255,255,0.07);">
                <div class="px-4 py-3 border-b" style="border-color:rgba(255,255,255,0.05);">
                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Area Parkir</p>
                </div>
                @if(!empty($maps) && $maps->count())
                <div class="p-3 flex flex-col gap-1">
                    @foreach($maps as $m)
                    <a href="{{ route('parking.map.index', ['map_id' => $m->id_area]) }}"
                       class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ $area && $area->id_area === $m->id_area ? 'text-emerald-400' : 'text-slate-400 hover:text-white' }}"
                       style="{{ $area && $area->id_area === $m->id_area ? 'background:rgba(16,185,129,0.1);' : '' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $area && $area->id_area === $m->id_area ? 'bg-emerald-400' : 'bg-slate-700' }} shrink-0"></span>
                        {{ $m->nama_area }}
                    </a>
                    @endforeach
                </div>
                @else
                <p class="px-4 py-3 text-xs text-slate-600 italic">Tidak ada area.</p>
                @endif
            </div>

            {{-- Stats --}}
            <div class="rounded-2xl overflow-hidden border" style="background:#0d1526;border-color:rgba(255,255,255,0.07);">
                <div class="px-4 py-3 border-b flex items-center justify-between" style="border-color:rgba(255,255,255,0.05);">
                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Statistik</p>
                    <button id="parking-map-refresh-btn" type="button" class="text-slate-600 hover:text-emerald-400 transition-colors" title="Refresh">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>
                </div>
                <div id="parking-map-summary" class="p-4 flex flex-col gap-3">
                    @foreach(['Total','Tersedia','Terisi','Reserved'] as $s)
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500">{{ $s }}</span>
                        <div class="w-10 h-3 bg-white/5 rounded animate-pulse"></div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Utilization bar --}}
            <div class="rounded-2xl p-4 border" style="background:#0d1526;border-color:rgba(255,255,255,0.07);">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Utilisasi</p>
                    <span id="util-pct" class="text-xs font-bold text-white">—</span>
                </div>
                <div class="w-full h-1.5 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.06);">
                    <div id="util-bar" class="h-1.5 rounded-full bg-emerald-500 transition-all duration-700" style="width:0%;"></div>
                </div>
            </div>

            {{-- Legend detail --}}
            <div class="rounded-2xl p-4 border" style="background:#0d1526;border-color:rgba(255,255,255,0.07);">
                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-3">Keterangan</p>
                <div class="flex flex-col gap-2.5">
                    <div class="flex items-center gap-2.5">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <span class="text-xs text-white">Slot tersedia</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-3 h-3 rounded-full bg-slate-500"></span>
                        <span class="text-xs text-white">Slot terisi</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        <span class="text-xs text-white">Reserved</span>
                    </div>
                    <div class="flex items-center gap-2.5 pt-1.5 border-t" style="border-color:rgba(255,255,255,0.06);">
                        <div class="w-4 h-4 rounded-lg flex items-center justify-center" style="background:rgba(255,255,255,0.08);">
                            <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-xs text-white">CCTV Cam</span>
                    </div>
                </div>
            </div>

        </div>{{-- /sidebar --}}
    </div>

</div>
</div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="anonymous"/>
<style>
    /* Fix Leaflet Z-Index and layout */
    .leaflet-container {
        background: #020617 !important;
    }
    .leaflet-pane {
        z-index: 2 !important;
    }
    .leaflet-control-container .leaflet-top,
    .leaflet-control-container .leaflet-bottom {
        z-index: 3 !important;
    }
    .leaflet-popup-pane {
        z-index: 4 !important;
    }
    .modern-popup .leaflet-popup-content-wrapper {
        background: #0f172a !important;
        color: #f8fafc !important;
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 12px;
        padding: 0;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
    }
    .modern-popup .leaflet-popup-tip {
        background: #0f172a !important;
    }
    .modern-popup .leaflet-popup-content {
        margin: 0 !important;
        width: auto !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin="anonymous"></script>
<script src="{{ asset('js/parking-map.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const refreshBtn = document.getElementById('parking-map-refresh-btn');
        if (refreshBtn) {
            refreshBtn.onclick = function() {
                if (window.refreshParkingMap) window.refreshParkingMap();
            };
        }
    });
</script>
@endpush
