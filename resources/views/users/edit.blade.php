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
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required placeholder="Nama user"
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('name') border-red-500 @enderror">
            </div>
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required placeholder="email@contoh.com"
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror">
            </div>
            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                </div>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="08123456789"
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('phone') border-red-500 @enderror">
            </div>
            @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="photo" class="block text-sm font-semibold text-gray-700 mb-2">Foto User (Ubah jika perlu)</label>
            <div class="relative">
                <input type="file" name="photo" id="photo" 
                       class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('photo') border-red-500 @enderror">
            </div>
            @error('photo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Status Kartu RFID</p>
                <p class="text-sm font-bold {{ $user->rfid_uid ? 'text-green-600' : 'text-red-600' }}">
                    {{ $user->rfid_uid ? 'Terdaftar (' . $user->rfid_uid . ')' : 'Belum Terdaftar' }}
                </p>
            </div>
            <a href="{{ route('users.scan-rfid', $user->id) }}" 
               class="px-4 py-2 bg-blue-500 text-white text-xs font-bold rounded-xl hover:bg-blue-600 transition-colors uppercase tracking-widest">
                {{ $user->rfid_uid ? 'Ubah RFID' : 'Daftarkan RFID' }}
            </a>
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password (kosongkan jika tidak diubah)</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <input type="password" name="password" id="password" placeholder="Biarkan kosong jika tidak ingin mengubah"
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('password') border-red-500 @enderror">
            </div>
            @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password baru"
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
        </div>

        <div>
            <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12 12 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <select name="role" id="role" required
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('role') border-red-500 @enderror">
                    <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="petugas" {{ old('role', $user->role) == 'petugas' ? 'selected' : '' }}>Petugas</option>
                </select>
            </div>
            @error('role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    @endcomponent
@endsection
