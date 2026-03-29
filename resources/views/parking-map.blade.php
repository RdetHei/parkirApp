@extends('layouts.app')

@section('title', 'Peta Parkir')

@section('content')
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <div class="card-modern overflow-hidden">
                <div class="px-6 py-5 border-b border-zinc-100 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Peta Interaktif Parkir</h2>
                        <p class="text-sm text-zinc-500 mt-0.5 font-medium">Pantau ketersediaan slot dan kamera pemantau secara real-time.</p>
                    </div>

                    <div class="flex items-center gap-3">
                        @if(!empty($maps) && $maps->count())
                            <form method="GET" action="{{ route('parking.map.index') }}" class="flex items-center gap-2">
                                <select id="map-select" name="map_id"
                                        onchange="this.form.submit()"
                                        class="block w-full md:w-48 px-4 py-2 bg-zinc-50 border border-zinc-200 rounded-xl text-sm font-bold text-zinc-700 focus:ring-0 focus:border-zinc-900 transition-all">
                                    @foreach($maps as $m)
                                        <option value="{{ $m->id_area }}" {{ $area && $area->id_area === $m->id_area ? 'selected' : '' }}>
                                            {{ $m->nama_area }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        @endif
                        <button onclick="fetchData()" class="p-2.5 bg-zinc-50 hover:bg-zinc-900 hover:text-white text-zinc-600 rounded-xl transition-all border border-zinc-200" title="Refresh Data">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div id="parking-map-summary" class="px-6 py-4 bg-zinc-50/50 border-b border-zinc-100">
                    <div class="animate-pulse flex space-x-6">
                        <div class="h-4 bg-zinc-200 rounded-full w-32"></div>
                        <div class="h-4 bg-zinc-200 rounded-full w-32"></div>
                    </div>
                </div>

                <div class="p-6 bg-zinc-50/30">
                    <div class="relative group">
                        @if($area && $area->map_image)
                            <div id="parking-map"
                                 class="w-full rounded-[1.5rem] border-4 border-white shadow-2xl bg-zinc-200 overflow-hidden"
                                 style="height: 600px;"
                                 data-image-url="{{ asset('storage/' . $area->map_image) }}"
                                 data-width="{{ $area->map_width }}"
                                 data-height="{{ $area->map_height }}"
                                 data-map-id="{{ $area->id_area }}"
                            >
                            </div>
                            
                            {{-- Legend Floating (Modernized) --}}
                            <div class="absolute bottom-6 right-6 bg-zinc-900/90 backdrop-blur-md px-5 py-4 rounded-2xl shadow-2xl border border-zinc-800 z-[1000] pointer-events-none">
                                <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em] mb-3">Legend Status</p>
                                <div class="space-y-3">
                                    <div class="flex items-center gap-3">
                                        <span class="w-3.5 h-3.5 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.4)]"></span>
                                        <span class="text-xs font-bold text-zinc-100">Tersedia</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="w-3.5 h-3.5 rounded-full bg-zinc-600"></span>
                                        <span class="text-xs font-bold text-zinc-100">Terisi</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="w-3.5 h-3.5 rounded-full bg-amber-500 shadow-[0_0_10px_rgba(245,158,11,0.4)]"></span>
                                        <span class="text-xs font-bold text-zinc-100">Reserved</span>
                                    </div>
                                    <div class="flex items-center gap-3 pt-1">
                                        <div class="w-4 h-4 bg-white/20 rounded-md flex items-center justify-center">
                                            <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <span class="text-xs font-bold text-zinc-100">CCTV Cam</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="w-full h-[600px] rounded-[1.5rem] bg-zinc-100 border-2 border-dashed border-zinc-300 flex flex-col items-center justify-center text-center p-12">
                                <div class="w-20 h-20 bg-zinc-200 rounded-3xl flex items-center justify-center text-zinc-400 mb-6">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                </div>
                                <h3 class="text-xl font-bold text-zinc-900 mb-2">Peta Belum Tersedia</h3>
                                <p class="text-zinc-500 max-w-md mx-auto">Silakan pilih area lain atau hubungi administrator untuk mengunggah blueprint peta area ini.</p>
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('area-parkir.index') }}" class="mt-8 px-6 py-3 bg-zinc-900 text-white text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-zinc-800 transition-all">
                                        Kelola Area Parkir
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .modern-popup .leaflet-popup-content-wrapper {
            border-radius: 16px;
            padding: 4px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0,0,0,0.05);
        }
        .modern-popup .leaflet-popup-content {
            margin: 12px;
        }
        .modern-popup .leaflet-popup-tip {
            box-shadow: none;
        }
        .parking-slot-rect {
            transition: all 0.2s ease-in-out;
        }
    </style>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        let map, imageOverlay;
        const mapContainer = document.getElementById('parking-map');
        
        if (mapContainer) {
            const mapId = mapContainer.dataset.mapId;
            const imageUrl = mapContainer.dataset.imageUrl;
            const mapWidth = parseInt(mapContainer.dataset.width);
            const mapHeight = parseInt(mapContainer.dataset.height);

            // Layers
            let slotLayer = L.layerGroup();
            let cameraLayer = L.layerGroup();

            function initMap() {
                if (map) map.remove();

                map = L.map('parking-map', {
                    crs: L.CRS.Simple,
                    minZoom: -1,
                    maxZoom: 2,
                    zoomControl: false
                });

                const bounds = [[0, 0], [mapHeight, mapWidth]];
                imageOverlay = L.imageOverlay(imageUrl, bounds).addTo(map);
                map.fitBounds(bounds);

                L.control.zoom({ position: 'topright' }).addTo(map);
                
                slotLayer.addTo(map);
                cameraLayer.addTo(map);

                fetchData();
            }

            async function fetchData() {
                try {
                    const response = await fetch(`{{ route('api.parking-slots.index') }}?map_id=${mapId}`);
                    const data = await response.json();

                    updateSummary(data.summary);
                    renderSlots(data.slots);
                    renderCameras(data.cameras);
                } catch (error) {
                    console.error('Error fetching map data:', error);
                }
            }

            function updateSummary(summary) {
                const container = document.getElementById('parking-map-summary');
                if (!container) return;
                
                container.innerHTML = `
                    <div class="flex flex-wrap items-center gap-6">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Total Slot:</span>
                            <span class="text-sm font-black text-zinc-900">${summary.total}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Tersedia:</span>
                            <span class="text-sm font-black text-emerald-600">${summary.empty}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Terisi:</span>
                            <span class="text-sm font-black text-zinc-500">${summary.occupied}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Reserved:</span>
                            <span class="text-sm font-black text-amber-500">${summary.reserved}</span>
                        </div>
                    </div>
                `;
            }

            function renderSlots(slots) {
                slotLayer.clearLayers();

                slots.forEach(slot => {
                    const y = mapHeight - slot.y - slot.height;
                    const bounds = [[y, slot.x], [y + slot.height, slot.x + slot.width]];
                    
                    let color = '#10b981'; // emerald-500
                    if (slot.status === 'occupied') color = '#71717a'; // zinc-500
                    if (slot.status === 'reserved' || slot.status === 'reserved-by-me') color = '#f59e0b'; // amber-500

                    const rect = L.rectangle(bounds, {
                        color: color,
                        weight: 2,
                        fillColor: color,
                        fillOpacity: 0.3,
                        className: 'parking-slot-rect'
                    });

                    const popupContent = `
                        <div class="w-48">
                            <div class="flex items-center justify-between mb-3 pb-2 border-b border-zinc-100">
                                <span class="text-lg font-black text-zinc-900">Slot ${slot.code}</span>
                                <span class="px-2 py-0.5 bg-zinc-100 text-[9px] font-black text-zinc-500 uppercase rounded">${slot.status}</span>
                            </div>
                            <div class="space-y-2.5">
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Kendaraan</span>
                                    <span class="text-xs font-black text-zinc-800">${slot.vehicle_plate || '—'}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Catatan</span>
                                    <span class="text-xs font-medium text-zinc-500 leading-relaxed">${slot.notes || 'Tidak ada catatan'}</span>
                                </div>
                            </div>
                        </div>
                    `;

                    rect.bindPopup(popupContent, { className: 'modern-popup', offset: [0, -10] });
                    rect.on('mouseover', function() { this.setStyle({ fillOpacity: 0.6 }); });
                    rect.on('mouseout', function() { this.setStyle({ fillOpacity: 0.3 }); });

                    slotLayer.addLayer(rect);
                });
            }

            function renderCameras(cameras) {
                cameraLayer.clearLayers();

                cameras.forEach(cam => {
                    const y = mapHeight - cam.y;
                    const icon = L.divIcon({
                        className: 'camera-icon',
                        html: `
                            <div class="w-8 h-8 bg-zinc-900 text-white rounded-xl shadow-xl flex items-center justify-center border-2 border-white hover:scale-110 transition-transform cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            </div>
                        `,
                        iconSize: [32, 32],
                        iconAnchor: [16, 16]
                    });

                    const marker = L.marker([y, cam.x], { icon: icon });
                    
                    const popupContent = `
                        <div class="w-64 overflow-hidden rounded-xl">
                            <div class="p-3 bg-zinc-900 text-white flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-widest">Live Cam: ${cam.name}</span>
                                <span class="w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
                            </div>
                            <div class="aspect-video bg-black flex items-center justify-center">
                                ${cam.stream_url ? 
                                    `<img src="${cam.stream_url}" class="w-full h-full object-cover" onerror="this.src='https://via.placeholder.com/320x180?text=No+Signal'">` : 
                                    `<div class="text-center"><svg class="w-8 h-8 text-zinc-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg><span class="text-[10px] text-zinc-500 font-bold uppercase">No Video Feed</span></div>`
                                }
                            </div>
                        </div>
                    `;

                    marker.bindPopup(popupContent, { className: 'modern-popup !p-0 overflow-hidden', offset: [0, -10] });
                    cameraLayer.addLayer(marker);
                });
            }

            // Auto-refresh every 30 seconds
            setInterval(fetchData, 30000);

            // Initial load
            window.onload = initMap;
        } else {
            // No map container
            document.addEventListener('DOMContentLoaded', () => {
                const summaryContainer = document.getElementById('parking-map-summary');
                if (summaryContainer) {
                    summaryContainer.innerHTML = '<p class="text-sm text-zinc-500 italic">Pilih area parkir untuk melihat statistik ketersediaan.</p>';
                }
            });
        }
    </script>
@endpush
