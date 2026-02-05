@extends('layouts.app')

@section('title', 'Buat Log Aktivitas')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('log-aktivitas.index'),
        'title' => 'Buat Log Aktivitas Baru',
        'description' => 'Tambahkan catatan aktivitas baru ke sistem',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
        'cardTitle' => 'Form Log Aktivitas',
        'cardDescription' => 'Lengkapi detail aktivitas',
        'action' => route('log-aktivitas.store'),
        'method' => 'POST',
        'submitText' => 'Buat Log'
    ])
        <div>
            <label for="id_user" class="block text-sm font-semibold text-gray-700 mb-2">User <span class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <select name="id_user" id="id_user" required
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('id_user') border-red-500 @enderror">
                    <option value="">-- Pilih User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('id_user') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->role }})</option>
                    @endforeach
                </select>
            </div>
            @error('id_user')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="aktivitas" class="block text-sm font-semibold text-gray-700 mb-2">Aktivitas <span class="text-red-500">*</span></label>
            <div class="relative">
                <textarea name="aktivitas" id="aktivitas" rows="4" required placeholder="Deskripsi aktivitas"
                          class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('aktivitas') border-red-500 @enderror">{{ old('aktivitas') }}</textarea>
            </div>
            @error('aktivitas')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="waktu_aktivitas" class="block text-sm font-semibold text-gray-700 mb-2">Waktu Aktivitas <span class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <input type="datetime-local" name="waktu_aktivitas" id="waktu_aktivitas" value="{{ old('waktu_aktivitas') }}" required
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('waktu_aktivitas') border-red-500 @enderror">
            </div>
            @error('waktu_aktivitas')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    @endcomponent
@endsection
