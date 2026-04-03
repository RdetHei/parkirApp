@extends('layouts.app')

@section('title', 'Tambah Area Parkir')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('area-parkir.index'),
        'title' => 'Tambah Area Parkir',
        'description' => 'Daftarkan area parkir baru beserta layout petanya',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>',
        'cardTitle' => 'Form Area Parkir Baru',
        'cardDescription' => 'Lengkapi informasi dasar dan peta area',
        'action' => route('area-parkir.store'),
        'method' => 'POST',
        'submitText' => 'Simpan & Lanjutkan',
        'enctype' => 'multipart/form-data'
    ])
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest border-b pb-2">Informasi Dasar</h3>
                
                <div>
                    <label for="nama_area" class="block text-sm font-semibold text-gray-700 mb-2">Nama Area <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_area" id="nama_area" value="{{ old('nama_area') }}" required placeholder="Contoh: Gedung A Lantai 1"
                           class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nama_area') border-red-500 @enderror">
                    @error('nama_area')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="daerah" class="block text-sm font-semibold text-gray-700 mb-2">Daerah/Kota <span class="text-red-500">*</span></label>
                    <input type="text" name="daerah" id="daerah" value="{{ old('daerah') }}" required placeholder="Contoh: Garut, Bandung, Jakarta"
                           class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('daerah') border-red-500 @enderror">
                    @error('daerah')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="kapasitas" class="block text-sm font-semibold text-gray-700 mb-2">Kapasitas <span class="text-red-500">*</span></label>
                    <input type="number" name="kapasitas" id="kapasitas" value="{{ old('kapasitas', 20) }}" required min="1" placeholder="Jumlah slot"
                           class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('kapasitas') border-red-500 @enderror">
                    @error('kapasitas')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <input type="checkbox" name="is_default_map" id="is_default_map" value="1" {{ old('is_default_map') ? 'checked' : '' }}
                           class="w-5 h-5 text-green-500 border-gray-300 rounded focus:ring-green-500">
                    <label for="is_default_map" class="text-sm font-semibold text-gray-700">Jadikan Peta Utama (Dashboard)</label>
                </div>
            </div>

            <div class="space-y-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest border-b pb-2">Layout Peta Visual</h3>
                
                <div>
                    <label for="map_code" class="block text-sm font-semibold text-gray-700 mb-2">Kode Peta</label>
                    <input type="text" name="map_code" id="map_code" value="{{ old('map_code') }}" placeholder="Contoh: MAP-A1"
                           class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <div>
                    <label for="map_image" class="block text-sm font-semibold text-gray-700 mb-2">Gambar Peta (Blueprint)</label>
                    <input type="file" name="map_image" id="map_image" accept="image/*"
                           class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <p class="mt-2 text-[10px] text-gray-400 italic font-medium uppercase tracking-widest">Rekomendasi: PNG/JPG, Rasio 4:3 atau 16:9</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="map_width" class="block text-sm font-semibold text-gray-700 mb-2">Lebar Kanvas (px)</label>
                        <input type="number" name="map_width" id="map_width" value="{{ old('map_width', 1000) }}" placeholder="1000"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="map_height" class="block text-sm font-semibold text-gray-700 mb-2">Tinggi Kanvas (px)</label>
                        <input type="number" name="map_height" id="map_height" value="{{ old('map_height', 800) }}" placeholder="800"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6 p-4 bg-amber-50 rounded-xl border border-amber-100 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-xs text-amber-700 leading-relaxed">
                Setelah menyimpan area, Anda dapat mengatur posisi koordinat slot parkir secara visual melalui menu <strong>Desain Peta</strong> di daftar area.
            </p>
        </div>
    @endcomponent
@endsection
