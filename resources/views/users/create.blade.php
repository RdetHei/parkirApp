@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('users.index'),
        'title' => 'Buat User Baru',
        'description' => 'Tambahkan data user baru ke sistem',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>',
        'cardTitle' => 'Form User',
        'cardDescription' => 'Lengkapi detail user',
        'action' => route('users.store'),
        'method' => 'POST',
        'submitText' => 'Buat'
    ])
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-2">
                <label for="name" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                        <i class="fa-solid fa-user text-xs"></i>
                    </div>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Nama user"
                           class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm @error('name') border-rose-500 @enderror">
                </div>
                @error('name')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2">
                <label for="email" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Email Address <span class="text-rose-500">*</span></label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                        <i class="fa-solid fa-envelope text-xs"></i>
                    </div>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="email@contoh.com"
                           class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm @error('email') border-rose-500 @enderror">
                </div>
                @error('email')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2">
                <label for="phone" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Nomor Telepon</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                        <i class="fa-solid fa-phone text-xs"></i>
                    </div>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="08123456789"
                           class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm @error('phone') border-rose-500 @enderror">
                </div>
                @error('phone')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2">
                <label for="role" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Akses Role <span class="text-rose-500">*</span></label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                        <i class="fa-solid fa-shield-halved text-xs"></i>
                    </div>
                    <select name="role" id="role" required
                            class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm appearance-none cursor-pointer @error('role') border-rose-500 @enderror">
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }} class="bg-slate-900">User / Member</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }} class="bg-slate-900">Administrator</option>
                        <option value="petugas" {{ old('role') == 'petugas' ? 'selected' : '' }} class="bg-slate-900">Petugas Lapangan</option>
                    </select>
                </div>
                @error('role')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2">
                <label for="id_area" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Area Tugas <span class="text-slate-700">(Khusus Petugas)</span></label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                        <i class="fa-solid fa-location-dot text-xs"></i>
                    </div>
                    <select name="id_area" id="id_area"
                            class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm appearance-none cursor-pointer @error('id_area') border-rose-500 @enderror">
                        <option value="" class="bg-slate-900">-- Pilih Area Tugas --</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id_area }}" {{ old('id_area') == $area->id_area ? 'selected' : '' }} class="bg-slate-900">{{ $area->nama_area }}</option>
                        @endforeach
                    </select>
                </div>
                @error('id_area')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2 space-y-2">
                <label for="photo" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Foto Profil</label>
                <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/gif,image/webp"
                       class="block w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white file:bg-emerald-500 file:text-slate-950 file:border-0 file:rounded-lg file:px-4 file:py-2 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:mr-4 hover:file:bg-emerald-400 transition-all cursor-pointer text-xs @error('photo') border-rose-500 @enderror">
                <p class="mt-2 text-[9px] text-slate-600 italic font-bold uppercase tracking-widest ml-1">Opsional: Foto akan disimpan di Cloudinary.</p>
                @error('photo')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2">
                <label for="password" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Password <span class="text-rose-500">*</span></label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                        <i class="fa-solid fa-lock text-xs"></i>
                    </div>
                    <input type="password" name="password" id="password" required placeholder="Min. 8 karakter"
                           class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm @error('password') border-rose-500 @enderror">
                </div>
                @error('password')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2">
                <label for="password_confirmation" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Konfirmasi Password <span class="text-rose-500">*</span></label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                        <i class="fa-solid fa-circle-check text-xs"></i>
                    </div>
                    <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Ulangi password"
                           class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                </div>
            </div>
        </div>
    @endcomponent
@endsection
