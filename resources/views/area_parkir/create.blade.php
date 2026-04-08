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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div class="space-y-8">
                <div class="flex items-center gap-3 border-b border-white/5 pb-3">
                    <div class="w-1 h-4 bg-emerald-500 rounded-full"></div>
                    <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Informasi Dasar</h3>
                </div>
                
                <div class="space-y-2">
                    <label for="nama_area" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Nama Area <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_area" id="nama_area" value="{{ old('nama_area') }}" required placeholder="Contoh: Gedung A Lantai 1"
                           class="block w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm @error('nama_area') border-rose-500 @enderror">
                    @error('nama_area')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label for="daerah" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Daerah/Kota <span class="text-rose-500">*</span></label>
                    <input type="text" name="daerah" id="daerah" value="{{ old('daerah') }}" required placeholder="Contoh: Garut, Bandung, Jakarta"
                           class="block w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm @error('daerah') border-rose-500 @enderror">
                    @error('daerah')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label for="kapasitas" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Kapasitas <span class="text-rose-500">*</span></label>
                    <input type="number" name="kapasitas" id="kapasitas" value="{{ old('kapasitas', 20) }}" required min="1" placeholder="Jumlah slot"
                           class="block w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm @error('kapasitas') border-rose-500 @enderror">
                    @error('kapasitas')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-4 p-5 bg-slate-950/50 rounded-2xl border border-white/5 group hover:border-emerald-500/30 transition-all">
                    <input type="checkbox" name="is_default_map" id="is_default_map" value="1" {{ old('is_default_map') ? 'checked' : '' }}
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
                    <input type="text" name="map_code" id="map_code" value="{{ old('map_code') }}" placeholder="Contoh: MAP-A1"
                           class="block w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm">
                </div>

                <div class="space-y-2">
                    <label for="map_image" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Gambar Peta (Blueprint)</label>
                    <input type="file" name="map_image" id="map_image" accept="image/*"
                           class="block w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white file:bg-emerald-500 file:text-slate-950 file:border-0 file:rounded-lg file:px-4 file:py-2 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:mr-4 hover:file:bg-emerald-400 transition-all cursor-pointer text-xs">
                    <p class="mt-2 text-[9px] text-slate-600 italic font-bold uppercase tracking-widest">Rekomendasi: PNG/JPG, Rasio 4:3 atau 16:9</p>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="map_width" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Lebar Kanvas (px)</label>
                        <input type="number" name="map_width" id="map_width" value="{{ old('map_width', 1000) }}" placeholder="1000"
                               class="block w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm">
                    </div>
                    <div class="space-y-2">
                        <label for="map_height" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Tinggi Kanvas (px)</label>
                        <input type="number" name="map_height" id="map_height" value="{{ old('map_height', 800) }}" placeholder="800"
                               class="block w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-10 p-5 bg-amber-500/5 border border-amber-500/10 rounded-2xl flex items-start gap-4">
            <div class="w-10 h-10 bg-amber-500/10 rounded-xl flex items-center justify-center text-amber-500 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="text-xs text-amber-500/80 leading-relaxed font-medium">
                Setelah menyimpan area, Anda dapat mengatur posisi koordinat slot parkir secara visual melalui menu <strong class="text-amber-500">Desain Peta</strong> di daftar area.
            </p>
        </div>
    @endcomponent
@endsection
