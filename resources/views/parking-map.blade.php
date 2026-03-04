@extends('layouts.app')

@section('title', 'Peta Parkir')

@section('content')
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200">
                <div class="px-6 py-5 border-b border-gray-100 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Peta Interaktif Parkir</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Pantau ketersediaan slot dan kamera pemantau secara real-time.</p>
                    </div>

                    <div class="flex items-center gap-3">
                        @if(!empty($maps) && $maps->count())
                            <form method="GET" action="{{ route('parking.map.index') }}" class="flex items-center gap-2">
                                <select id="map-select" name="map"
                                        onchange="this.form.submit()"
                                        class="block w-full md:w-48 px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                                    @foreach($maps as $m)
                                        <option value="{{ $m->id }}" {{ $map && $map->id === $m->id ? 'selected' : '' }}>
                                            {{ $m->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        @endif
                        <button onclick="fetchData()" class="p-2 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-xl transition-colors border border-gray-200" title="Refresh Data">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div id="parking-map-summary" class="px-6 py-3 bg-slate-50 border-b border-gray-100">
                    <div class="animate-pulse flex space-x-4">
                        <div class="h-4 bg-slate-200 rounded w-1/4"></div>
                        <div class="h-4 bg-slate-200 rounded w-1/4"></div>
                    </div>
                </div>

                <div class="p-6 bg-slate-50">
                    <div class="relative group">
                        <div id="parking-map"
                             class="w-full rounded-2xl border-4 border-white shadow-inner bg-slate-200"
                             style="height: 600px;"
                             data-image-url="{{ $map ? asset($map->image_path) : asset('images/floor1.png') }}"
                             data-width="{{ $map ? $map->width : 1000 }}"
                             data-height="{{ $map ? $map->height : 800 }}"
                             data-map-id="{{ $map ? $map->id : '' }}"
                        >
                        </div>
                        
                        {{-- Legend Floating --}}
                        <div class="absolute bottom-6 right-6 bg-white/90 backdrop-blur px-4 py-3 rounded-2xl shadow-xl border border-white/20 z-[1000] pointer-events-none">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Keterangan</p>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded bg-emerald-500 border border-emerald-600"></span>
                                    <span class="text-xs font-semibold text-gray-700">Tersedia</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded bg-red-500 border border-red-600"></span>
                                    <span class="text-xs font-semibold text-gray-700">Terisi</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded bg-orange-500 border border-orange-600"></span>
                                    <span class="text-xs font-semibold text-gray-700">Reserved</span>
                                </div>
                                <div class="flex items-center gap-2 pt-1">
                                    <div class="w-3 h-3 bg-indigo-600 rounded-sm flex items-center justify-center">
                                        <svg class="w-2 h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-700">Kamera</span>
                                </div>
                            </div>
                        </div>
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
            cursor: pointer;
        }
        .parking-map-camera-marker {
            transition: transform 0.2s ease-in-out;
        }
        .parking-map-camera-marker:hover {
            transform: scale(1.1);
            z-index: 1000 !important;
        }
    </style>

    {{-- Leaflet CSS & JS (CDN) --}}
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>

    {{-- Script khusus peta parkir --}}
    <script src="{{ asset('js/parking-map.js') }}" defer></script>
@endsection

