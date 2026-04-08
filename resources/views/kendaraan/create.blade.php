@extends('layouts.app')

@section('title', 'Tambah Kendaraan')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('kendaraan.index'),
        'title' => 'Tambah Kendaraan',
        'description' => 'Tambahkan data kendaraan baru ke sistem',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>',
        'cardTitle' => 'Form Kendaraan',
        'cardDescription' => 'Lengkapi data kendaraan',
        'action' => route('kendaraan.store'),
        'method' => 'POST',
        'submitText' => 'Simpan'
    ])
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-2">
                <label for="plat_nomor" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Plat Nomor <span class="text-rose-500">*</span></label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                        <i class="fa-solid fa-id-card-clip text-xs"></i>
                    </div>
                    <input type="text" name="plat_nomor" id="plat_nomor" value="{{ old('plat_nomor') }}" required placeholder="Contoh: B 1234 XYZ"
                           class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm @error('plat_nomor') border-rose-500 @enderror">
                </div>
                @error('plat_nomor')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2">
                <label for="jenis_kendaraan" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Jenis Kendaraan <span class="text-rose-500">*</span></label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                        <i class="fa-solid fa-car-side text-xs"></i>
                    </div>
                    <input type="text" name="jenis_kendaraan" id="jenis_kendaraan" value="{{ old('jenis_kendaraan') }}" required placeholder="Motor / Mobil / Lainnya"
                           class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm @error('jenis_kendaraan') border-rose-500 @enderror">
                </div>
                @error('jenis_kendaraan')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2">
                <label for="warna" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Warna</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                        <i class="fa-solid fa-palette text-xs"></i>
                    </div>
                    <input type="text" name="warna" id="warna" value="{{ old('warna') }}" placeholder="Warna kendaraan"
                           class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm @error('warna') border-rose-500 @enderror">
                </div>
                @error('warna')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2">
                <label for="pemilik" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Nama Pemilik</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                        <i class="fa-solid fa-user-tag text-xs"></i>
                    </div>
                    <input type="text" name="pemilik" id="pemilik" value="{{ old('pemilik') }}" placeholder="Nama pemilik"
                           class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm @error('pemilik') border-rose-500 @enderror">
                </div>
                @error('pemilik')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2">
                <label for="id_user" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Link ke User Account (Opsional)</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                        <i class="fa-solid fa-user-gear text-xs"></i>
                    </div>
                    <select name="id_user" id="id_user"
                            class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm appearance-none cursor-pointer @error('id_user') border-rose-500 @enderror">
                        <option value="" class="bg-slate-900">-- Pilih User --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ old('id_user') == $u->id ? 'selected' : '' }} class="bg-slate-900">{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                </div>
                @error('id_user')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2">
                <label for="foto" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Foto Kendaraan</label>
                <input type="file" name="foto" id="foto" accept="image/*"
                       class="block w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white file:bg-emerald-500 file:text-slate-950 file:border-0 file:rounded-lg file:px-4 file:py-2 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:mr-4 hover:file:bg-emerald-400 transition-all cursor-pointer text-xs @error('foto') border-rose-500 @enderror">
                <p class="mt-2 text-[9px] text-slate-600 italic font-bold uppercase tracking-widest ml-1">Format: JPG, PNG, WEBP. Maks: 2MB</p>
                @error('foto')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
            </div>
        </div>
    @endcomponent
@endsection
