@extends('layouts.app')

@section('title','Edit Log Aktivitas')

@section('content')
@extends('layouts.app')

@section('title','Edit Log Aktivitas')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('log-aktivitas.index'),
        'title' => 'Edit Log Aktivitas',
        'description' => 'Ubah detail log aktivitas',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>',
        'cardTitle' => 'Form Edit Log Aktivitas',
        'cardDescription' => 'Sesuaikan informasi log aktivitas',
        'action' => route('log-aktivitas.update', $item->id_log),
        'method' => 'PUT',
        'submitText' => 'Simpan Perubahan'
    ])
        <div class="mb-4">
            <label for="id_user" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                User
            </label>
            <select name="id_user" id="id_user" class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm bg-gray-800 text-white">
                <option value="">-- Pilih User --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('id_user', $item->id_user) == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->role }})
                    </option>
                @endforeach
            </select>
            @error('id_user')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <x-form-input
            name="aktivitas"
            label="Aktivitas"
            type="textarea"
            :value="old('aktivitas', $item->aktivitas)"
            placeholder="Deskripsi aktivitas"
        />

        <x-form-input
            name="waktu_aktivitas"
            label="Waktu Aktivitas"
            type="datetime-local"
            :value="old('waktu_aktivitas', $item->waktu_aktivitas?->format('Y-m-d\TH:i'))"
        />
    @endcomponent
@endsection
