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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div class="space-y-8">
                <div class="flex items-center gap-3 border-b border-white/5 pb-3">
                    <div class="w-1 h-4 bg-emerald-500 rounded-full"></div>
                    <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Informasi Dasar</h3>
                </div>
                
                <div class="space-y-2">
                    <label for="nama_area" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Nama Area <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_area" id="nama_area" value="{{ old('nama_area', $area->nama_area) }}" required placeholder="Nama area parkir"
                           class="block w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm @error('nama_area') border-rose-500 @enderror">
                    @error('nama_area')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label for="map_prefix" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Prefix Slot <span class="text-slate-700 font-medium lowercase ml-1">(contoh: S, Y, A)</span></label>
                    <input type="text" name="map_prefix" id="map_prefix" value="{{ old('map_prefix', $area->map_prefix) }}" maxlength="10" placeholder="Contoh: S"
                           class="block w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm">
                    <p class="mt-1 text-[9px] text-slate-600 font-medium ml-1 italic lowercase uppercase tracking-widest">Digunakan untuk format UID slot (Prefix-1, Prefix-2, dst)</p>
                </div>

                <div class="space-y-2">
                    <label for="daerah" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Daerah/Kota <span class="text-rose-500">*</span></label>
                    <input type="text" name="daerah" id="daerah" value="{{ old('daerah', $area->daerah) }}" required placeholder="Contoh: Garut, Bandung, Jakarta"
                           class="block w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm @error('daerah') border-rose-500 @enderror">
                    @error('daerah')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label for="kapasitas" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Kapasitas <span class="text-rose-500">*</span></label>
                    <input type="number" name="kapasitas" id="kapasitas" value="{{ old('kapasitas', $area->kapasitas) }}" required min="1" placeholder="Jumlah slot"
                           class="block w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm @error('kapasitas') border-rose-500 @enderror">
                    @error('kapasitas')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-4 p-5 bg-slate-950/50 rounded-2xl border border-white/5 group hover:border-emerald-500/30 transition-all">
                    <input type="checkbox" name="is_default_map" id="is_default_map" value="1" {{ old('is_default_map', $area->is_default_map) ? 'checked' : '' }}
                           class="w-5 h-5 bg-slate-900 border-white/10 text-emerald-500 rounded focus:ring-emerald-500/20 focus:ring-offset-0">
                    <label for="is_default_map" class="text-xs font-bold text-slate-400 group-hover:text-white transition-colors cursor-pointer uppercase tracking-widest">Jadikan Peta Utama (Dashboard)</label>
                </div>
            </div>

            <div class="space-y-8">
                <div class="flex items-center gap-3 border-b border-white/5 pb-3">
                    <div class="w-1 h-4 bg-indigo-500 rounded-full"></div>
                    <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Layout Peta Visual</h3>
                </div>
                
                <div class="space-y-2">
                    <label for="map_code" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Kode Peta</label>
                    <input type="text" name="map_code" id="map_code" value="{{ old('map_code', $area->map_code) }}" placeholder="Contoh: MAP-A1"
                           class="block w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm">
                </div>

                <div class="space-y-2">
                    <label for="map_image" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Gambar Peta (Blueprint)</label>
                    @if($area->map_image_url)
                        <div class="mb-4 relative group rounded-2xl overflow-hidden border border-white/10">
                            <img src="{{ $area->map_image_url }}" class="w-full h-32 object-cover opacity-60 group-hover:opacity-100 transition-all duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 to-transparent flex items-end p-4">
                                <span class="text-[9px] font-black text-white uppercase tracking-[0.2em] bg-emerald-500/80 px-2 py-1 rounded">Current Map</span>
                            </div>
                        </div>
                    @endif
                    <input type="file" name="map_image" id="map_image" accept="image/*"
                           class="block w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white file:bg-emerald-500 file:text-slate-950 file:border-0 file:rounded-lg file:px-4 file:py-2 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:mr-4 hover:file:bg-emerald-400 transition-all cursor-pointer text-xs">
                    <p class="mt-2 text-[9px] text-slate-600 italic font-bold uppercase tracking-widest">Rekomendasi: PNG/JPG, Rasio 4:3 atau 16:9</p>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="map_width" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Lebar Kanvas (px)</label>
                        <input type="number" name="map_width" id="map_width" value="{{ old('map_width', $area->map_width) }}" placeholder="1000"
                               class="block w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label for="map_height" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Tinggi Kanvas (px)</label>
                        <input type="number" name="map_height" id="map_height" value="{{ old('map_height', $area->map_height) }}" placeholder="800"
                               class="block w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm">
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-10 p-6 bg-indigo-500/5 border border-indigo-500/10 rounded-[2rem] flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 bg-indigo-500/10 text-indigo-500 rounded-2xl flex items-center justify-center border border-indigo-500/20 shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 012-2V6a2 2 0 01-2-2H6a2 2 0 01-2 2v12a2 2 0 012 2z"></path></svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-white uppercase tracking-widest">Atur Posisi Slot & Kamera</h4>
                    <p class="text-[11px] text-slate-500 font-medium mt-1">Sesuaikan titik koordinat slot parkir secara visual untuk area ini.</p>
                </div>
            </div>
            <a href="{{ route('area-parkir.design', $area->id_area) }}" class="px-8 py-3 bg-indigo-500 text-slate-950 font-black text-[10px] uppercase tracking-[0.2em] rounded-xl hover:bg-indigo-400 transition-all shadow-lg shadow-indigo-500/20">
                Buka Peta Designer
            </a>
        </div>
    @endcomponent
@endsection
