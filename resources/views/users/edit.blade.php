@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('users.index'),
        'title' => 'Edit User',
        'description' => 'Ubah data user yang sudah ada di sistem',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>',
        'cardTitle' => 'Form Edit User',
        'cardDescription' => 'Sesuaikan informasi user',
        'action' => route('users.update', $user),
        'method' => 'PUT',
        'submitText' => 'Update'
    ])
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8" x-data="{ userRole: '{{ old('role', $user->role) }}' }">
            <div class="space-y-2">
                <label for="name" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                        <i class="fa-solid fa-user text-xs"></i>
                    </div>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required placeholder="Nama user"
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
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required placeholder="email@contoh.com"
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
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="08123456789"
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
                            class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm appearance-none cursor-pointer @error('role') border-rose-500 @enderror"
                            x-model="userRole">
                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }} class="bg-slate-900">User / Member</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }} class="bg-slate-900">Administrator</option>
                        <option value="petugas" {{ old('role', $user->role) == 'petugas' ? 'selected' : '' }} class="bg-slate-900">Petugas Lapangan</option>
                    </select>
                </div>
                @error('role')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2 rounded-2xl border border-emerald-500/20 bg-emerald-500/5 p-6" x-show="userRole === 'petugas'">
                <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-2">Petugas lapangan</p>
                <p class="text-sm text-slate-300 leading-relaxed">Area tugas tidak diatur di sini. Berikan petugas <span class="text-white font-semibold">Kode Peta</span> dari master Area Parkir; mereka mengaktifkannya saat bertugas.</p>
            </div>

            <div class="md:col-span-2 space-y-2" x-show="userRole === 'admin'">
                <label for="id_area" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Area default (opsional)</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                        <i class="fa-solid fa-location-dot text-xs"></i>
                    </div>
                    <select name="id_area" id="id_area"
                            x-bind:disabled="userRole !== 'admin'"
                            class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm appearance-none cursor-pointer @error('id_area') border-rose-500 @enderror">
                        <option value="" class="bg-slate-900">— Tidak ada —</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id_area }}" {{ old('id_area', $user->id_area) == $area->id_area ? 'selected' : '' }} class="bg-slate-900">{{ $area->nama_area }}</option>
                        @endforeach
                    </select>
                </div>
                <p class="text-[9px] text-slate-600 font-bold uppercase tracking-widest ml-1">Untuk akun admin: cadangan area jika terminal RFID dipakai tanpa memasukkan kode peta di sesi.</p>
                @error('id_area')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2 space-y-4">
                <label for="photo" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Foto Profil</label>
                <div class="flex items-center gap-6 p-4 bg-slate-950/50 rounded-2xl border border-white/5 group transition-all">
                    <div class="relative">
                        <x-user-avatar :user="$user" size="lg" round="xl" />
                        <div class="absolute -right-1 -bottom-1 w-6 h-6 bg-emerald-500 rounded-full border-2 border-slate-950 flex items-center justify-center text-slate-950 text-[10px]">
                            <i class="fa-solid fa-camera"></i>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-white uppercase tracking-widest">Current Identity</p>
                        <p class="text-[11px] text-slate-500 font-medium leading-relaxed">Unggah gambar baru (JPG/PNG/WEBP) untuk mengganti foto profil di Cloudinary.</p>
                    </div>
                </div>
                <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/gif,image/webp"
                       class="block w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white file:bg-emerald-500 file:text-slate-950 file:border-0 file:rounded-lg file:px-4 file:py-2 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:mr-4 hover:file:bg-emerald-400 transition-all cursor-pointer text-xs @error('photo') border-rose-500 @enderror">
                @error('photo')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2 p-6 bg-indigo-500/5 rounded-2xl border border-indigo-500/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-500/10 text-indigo-500 rounded-xl flex items-center justify-center border border-indigo-500/20 shadow-lg">
                        <i class="fa-solid fa-id-card text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Status Kartu RFID</p>
                        <p class="text-sm font-black {{ $user->rfid_uid ? 'text-emerald-500' : 'text-rose-500' }} tracking-tight">
                            {{ $user->rfid_uid ? 'Active: ' . $user->rfid_uid : 'Offline / Unregistered' }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('users.scan-rfid', $user->id) }}"
                   class="px-6 py-3 bg-indigo-500 text-slate-950 text-[10px] font-black rounded-xl hover:bg-indigo-400 transition-all uppercase tracking-widest shadow-lg shadow-indigo-500/20">
                    {{ $user->rfid_uid ? 'Update UID' : 'Pair New Card' }}
                </a>
            </div>

            <div class="space-y-2">
                <label for="password" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Password Baru</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                        <i class="fa-solid fa-lock text-xs"></i>
                    </div>
                    <input type="password" name="password" id="password" placeholder="Biarkan kosong jika tidak diubah"
                           class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm @error('password') border-rose-500 @enderror">
                </div>
                @error('password')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2">
                <label for="password_confirmation" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Konfirmasi Password</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                        <i class="fa-solid fa-circle-check text-xs"></i>
                    </div>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password baru"
                           class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                </div>
            </div>
        </div>
    @endcomponent
@endsection
