@extends('layouts.app')

@section('title', 'Edit Area Parkir')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('area-parkir.index'),
        'title' => 'Edit Area Parkir',
        'description' => 'Ubah data area parkir dan layout peta di sistem',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
        'cardTitle' => 'Form Edit Area Parkir',
        'cardDescription' => 'Sesuaikan detail area parkir dan peta visual',
        'action' => route('area-parkir.update', $area->id_area),
        'method' => 'PUT',
        'submitText' => 'Update',
        'enctype' => 'multipart/form-data'
    ])
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest border-b pb-2">Informasi Dasar</h3>
                
                <div>
                    <label for="nama_area" class="block text-sm font-semibold text-gray-700 mb-2">Nama Area <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_area" id="nama_area" value="{{ old('nama_area', $area->nama_area) }}" required placeholder="Nama area parkir"
                           class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nama_area') border-red-500 @enderror">
                    @error('nama_area')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="kapasitas" class="block text-sm font-semibold text-gray-700 mb-2">Kapasitas <span class="text-red-500">*</span></label>
                    <input type="number" name="kapasitas" id="kapasitas" value="{{ old('kapasitas', $area->kapasitas) }}" required min="1" placeholder="Jumlah slot"
                           class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('kapasitas') border-red-500 @enderror">
                    @error('kapasitas')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <input type="checkbox" name="is_default_map" id="is_default_map" value="1" {{ old('is_default_map', $area->is_default_map) ? 'checked' : '' }}
                           class="w-5 h-5 text-green-500 border-gray-300 rounded focus:ring-green-500">
                    <label for="is_default_map" class="text-sm font-semibold text-gray-700">Jadikan Peta Utama (Dashboard)</label>
                </div>
            </div>

            <div class="space-y-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest border-b pb-2">Layout Peta Visual</h3>
                
                <div>
                    <label for="map_code" class="block text-sm font-semibold text-gray-700 mb-2">Kode Peta</label>
                    <input type="text" name="map_code" id="map_code" value="{{ old('map_code', $area->map_code) }}" placeholder="Contoh: MAP-A1"
                           class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <div>
                    <label for="map_image" class="block text-sm font-semibold text-gray-700 mb-2">Gambar Peta (Blueprint)</label>
                    @if($area->map_image)
                        <div class="mb-3 relative group">
                            <img src="{{ str_starts_with($area->map_image, 'http') ? $area->map_image : asset('storage/' . $area->map_image) }}" class="w-full h-32 object-cover rounded-xl border border-gray-200 shadow-sm">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-xl">
                                <span class="text-white text-[10px] font-bold uppercase tracking-widest">Ganti Gambar</span>
                            </div>
                        </div>
                    @endif
                    <input type="file" name="map_image" id="map_image" accept="image/*"
                           class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <p class="mt-2 text-[10px] text-gray-400 italic font-medium uppercase tracking-widest">Rekomendasi: PNG/JPG, Rasio 4:3 atau 16:9</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="map_width" class="block text-sm font-semibold text-gray-700 mb-2">Lebar Kanvas (px)</label>
                        <input type="number" name="map_width" id="map_width" value="{{ old('map_width', $area->map_width) }}" placeholder="1000"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="map_height" class="block text-sm font-semibold text-gray-700 mb-2">Tinggi Kanvas (px)</label>
                        <input type="number" name="map_height" id="map_height" value="{{ old('map_height', $area->map_height) }}" placeholder="800"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 p-6 bg-blue-50 rounded-2xl border border-blue-100 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-500 text-white rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 012-2V6a2 2 0 01-2-2H6a2 2 0 01-2 2v12a2 2 0 012 2z"></path></svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-blue-900 uppercase tracking-widest">Atur Posisi Slot & Kamera</h4>
                    <p class="text-xs text-blue-700/80">Sesuaikan titik koordinat slot parkir secara visual.</p>
                </div>
            </div>
            <a href="{{ route('area-parkir.design', $area->id_area) }}" class="px-6 py-3 bg-blue-600 text-white font-bold text-xs uppercase tracking-widest rounded-xl hover:bg-blue-700 transition-all shadow-md">
                Buka Peta Designer
            </a>
        </div>
    @endcomponent
@endsection
