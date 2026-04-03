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
        <div class="flex-1 w-full min-w-0 rounded-2xl border" style="background:#0d1526;border-color:rgba(255,255,255,0.07); overflow: visible;">
            @if($area && $area->map_image_url)
            <div class="relative bg-slate-900/50 rounded-2xl overflow-hidden">
                <div id="parking-map"
                     class="w-full h-[400px] sm:h-[500px] lg:h-[640px] relative z-10"
                     style="min-height: 400px;"
                     data-image-url="{{ $area->map_image_url }}"
                     data-width="{{ $area->map_width }}"
                     data-height="{{ $area->map_height }}"
                     data-map-id="{{ $area->id_area }}">
                </div>

                {{-- Mini legend bottom-left --}}
                <div class="absolute bottom-4 left-4 flex items-center gap-3 px-3 py-2 rounded-xl z-20 pointer-events-none"
                     style="background:rgba(2,6,23,0.85);border:1px solid rgba(255,255,255,0.08);">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span class="text-[10px] font-bold text-white">Kosong</span>
                    </div>
                    <div class="w-px h-3 bg-white/10"></div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-500"></span>
                        <span class="text-[10px] font-bold text-white">Isi</span>
                    </div>
                    <div class="w-px h-3 bg-white/10"></div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <span class="text-[10px] font-bold text-white">Rsvd</span>
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
                @if(auth()->user()->role === 'admin')
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
    /* Leaflet default z-index (400+) bisa menutupi header — turunkan di dalam halaman ini */
    .parking-map-page .leaflet-container,
    .parking-map-page .leaflet-pane,
    .parking-map-page .leaflet-map-pane,
    .parking-map-page .leaflet-tile-pane,
    .parking-map-page .leaflet-overlay-pane,
    .parking-map-page .leaflet-shadow-pane,
    .parking-map-page .leaflet-marker-pane,
    .parking-map-page .leaflet-tooltip-pane,
    .parking-map-page .leaflet-popup-pane {
        z-index: 20 !important;
    }
    .parking-map-page .leaflet-control {
        z-index: 30 !important;
    }
    .modern-popup .leaflet-popup-content-wrapper {
        border-radius:14px; padding:4px;
        background:#0d1526;
        border:1px solid rgba(255,255,255,0.1);
        box-shadow:0 20px 40px rgba(0,0,0,0.5);
    }
    .modern-popup .leaflet-popup-content { margin:12px; }
    .modern-popup .leaflet-popup-tip-container { display:none; }
    .parking-slot-rect { transition: all 0.2s; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin="anonymous"></script>
<script>
    (function() {
        const parkingSlotsUrl = @json(route('api.parking-slots.index'));
        let map, slotLayer, cameraLayer;
        const mapContainer = document.getElementById('parking-map');

        if (!mapContainer) return;

        const mapId     = mapContainer.dataset.mapId;
        const imageUrl  = mapContainer.dataset.imageUrl;
        const mapWidth  = parseInt(mapContainer.dataset.width, 10) || 1000;
        const mapHeight = parseInt(mapContainer.dataset.height, 10) || 800;

        function initMap() {
            try {
                if (typeof L === 'undefined') {
                    setTimeout(initMap, 500);
                    return;
                }

                if (map) map.remove();
                
                map = L.map('parking-map', { 
                    crs: L.CRS.Simple, 
                    minZoom: -2, 
                    maxZoom: 3, 
                    zoomControl: false,
                    attributionControl: false
                });
                
                const bounds = [[0,0],[mapHeight,mapWidth]];
                const overlay = L.imageOverlay(imageUrl, bounds);
                overlay.addTo(map);
                
                overlay.on('load', () => {
                    map.fitBounds(bounds);
                });
                
                L.control.zoom({ position:'topright' }).addTo(map);
                
                slotLayer = L.layerGroup().addTo(map);
                cameraLayer = L.layerGroup().addTo(map);
                
                fetchData();

                setTimeout(() => map.invalidateSize(), 500);
            } catch (err) {
                console.error('Map Error:', err);
            }
        }

        async function fetchData() {
            try {
                const r = await fetch(`${parkingSlotsUrl}?map_id=${encodeURIComponent(mapId)}`, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                if (!r.ok) {
                    console.error('parking-slots HTTP', r.status);
                    return;
                }
                const data = await r.json();
                updateSummary(data.summary);
                renderSlots(data.slots);
                renderCameras(data.cameras);
            } catch(e) { console.error('Fetch Error:', e); }
        }

        function updateSummary(s) {
            const c = document.getElementById('parking-map-summary');
            if (!c) return;
            c.innerHTML = [
                { label:'Total',    val:s.total,    color:'#fff' },
                { label:'Tersedia', val:s.empty,    color:'#10b981' },
                { label:'Terisi',   val:s.occupied, color:'#64748b' },
                { label:'Reserved', val:s.reserved, color:'#f59e0b' },
            ].map(r => `
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:12px;color:#64748b;">${r.label}</span>
                    <span style="font-size:16px;font-weight:900;color:${r.color};">${r.val}</span>
                </div>`).join('');

            const pct = s.total > 0 ? Math.round(s.occupied / s.total * 100) : 0;
            const bar = document.getElementById('util-bar');
            const txt = document.getElementById('util-pct');
            if (bar) { 
                bar.style.width = pct + '%'; 
                bar.style.background = pct >= 90 ? '#ef4444' : pct >= 70 ? '#f59e0b' : '#10b981'; 
            }
            if (txt) txt.textContent = pct + '%';
        }

        function renderSlots(slots) {
            if (!slotLayer) return;
            slotLayer.clearLayers();
            slots.forEach(slot => {
                const y = mapHeight - slot.y - slot.height;
                const bounds = [[y,slot.x],[y+slot.height,slot.x+slot.width]];
                let color = '#10b981';
                if (slot.status === 'occupied') color = '#475569';
                if (slot.status === 'reserved' || slot.status === 'reserved-by-me') color = '#f59e0b';
                
                const rect = L.rectangle(bounds, { color, weight:2, fillColor:color, fillOpacity:0.3, className:'parking-slot-rect' });
                rect.bindPopup(`
                    <div style="min-width:150px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;padding-bottom:8px;border-bottom:1px solid rgba(255,255,255,0.08);">
                            <span style="font-size:14px;font-weight:900;color:#fff;">Slot ${slot.code}</span>
                            <span style="padding:1px 7px;background:rgba(255,255,255,0.06);font-size:8px;font-weight:700;color:#94a3b8;border-radius:4px;text-transform:uppercase;">${slot.status}</span>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:7px;">
                            <div><p style="font-size:9px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.1em;">Plat</p><p style="font-size:12px;font-weight:700;color:#fff;margin-top:1px;">${slot.vehicle_plate || '—'}</p></div>
                            <div><p style="font-size:9px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.1em;">Catatan</p><p style="font-size:11px;color:#64748b;margin-top:1px;">${slot.notes || '—'}</p></div>
                        </div>
                    </div>`, { className:'modern-popup', offset:[0,-10] });
                slotLayer.addLayer(rect);
            });
        }

        function renderCameras(cameras) {
            if (!cameraLayer) return;
            cameraLayer.clearLayers();
            cameras.forEach(cam => {
                const y = mapHeight - cam.y;
                const icon = L.divIcon({
                    className:'camera-icon',
                    html:`<div style="width:30px;height:30px;background:#0d1526;border:1px solid rgba(255,255,255,0.15);border-radius:9px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.4);">
                        <svg width="14" height="14" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </div>`,
                    iconSize:[30,30], iconAnchor:[15,15]
                });
                const marker = L.marker([y,cam.x], { icon });
                marker.bindPopup(`
                    <div style="width:220px;overflow:hidden;border-radius:10px;">
                        <div style="padding:8px 12px;background:#0d1526;display:flex;align-items:center;justify-content:space-between;">
                            <span style="font-size:10px;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.1em;">Cam: ${cam.name}</span>
                        </div>
                        <div style="aspect-ratio:16/9;background:#000;display:flex;align-items:center;justify-content:center;">
                            ${cam.stream_url ? `<img src="${cam.stream_url}" style="width:100%;height:100%;object-fit:cover;">` : '<span style="color:#475569;font-size:9px;">NO SIGNAL</span>'}
                        </div>
                    </div>`, { className:'modern-popup !p-0', offset:[0,-10] });
                cameraLayer.addLayer(marker);
            });
        }

        const refreshBtn = document.getElementById('parking-map-refresh-btn');
        if (refreshBtn) refreshBtn.onclick = fetchData;

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMap);
        } else {
            initMap();
        }

        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => { if (map) map.invalidateSize(); }, 150);
        });

        setInterval(fetchData, 30000);
    })();
</script>
@endpush
