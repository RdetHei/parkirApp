@extends('layouts.app')

@section('title', 'Peta Parkir')

@section('content')
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Peta Parkir (Leaflet)</h2>
                    <p class="text-xs text-gray-500">Indoor map dengan image overlay & multi-floor (floor1, floor2, outside)</p>
                </div>

                @if(!empty($maps) && $maps->count())
                    <form method="GET" action="{{ route('parking.map.index') }}" class="flex items-center gap-2">
                        <label for="map-select" class="text-xs font-medium text-gray-600">Pilih Peta:</label>
                        <select id="map-select" name="map"
                                onchange="this.form.submit()"
                                class="block px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500">
                            @foreach($maps as $m)
                                <option value="{{ $m->id }}" {{ $map && $map->id === $m->id ? 'selected' : '' }}>
                                    {{ $m->name }} ({{ $m->code }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif
            </div>

            <div id="parking-map-summary" class="px-6 py-2 text-sm text-gray-600 border-b border-gray-100">
                Memuat…
            </div>

            <div class="p-4">
                <div id="parking-map"
                     class="w-full rounded-lg border border-gray-200"
                     style="height: 520px; background-color: #f3f4f6;"
                     data-image-url="{{ $map ? asset($map->image_path) : asset('images/floor1.png') }}"
                     data-width="{{ $map ? $map->width : 1000 }}"
                     data-height="{{ $map ? $map->height : 800 }}"
                     data-map-id="{{ $map ? $map->id : '' }}"
                >
                </div>
            </div>
        </div>
    </div>

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

