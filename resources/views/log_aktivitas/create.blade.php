@extends('layouts.app')

@section('title','Buat Log Aktivitas')

@section('content')
@extends('layouts.app')

@section('title','Buat Log Aktivitas')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('log-aktivitas.index'),
        'title' => 'Buat Log Aktivitas Baru',
        'description' => 'Tambahkan catatan aktivitas baru ke sistem',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>',
        'cardTitle' => 'Form Log Aktivitas',
        'cardDescription' => 'Lengkapi detail aktivitas',
        'action' => route('log-aktivitas.store'),
        'method' => 'POST',
        'submitText' => 'Buat Log'
    ])
        <div class="mb-4">
            <label for="id_user" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                User
            </label>
            <select name="id_user" id="id_user" class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm bg-gray-800 text-white">
                <option value="">-- Pilih User --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('id_user') == $user->id ? 'selected' : '' }}>
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
            type="textarea" {{-- Indicate this should render as a textarea --}}
            :value="old('aktivitas')"
            placeholder="Deskripsi aktivitas"
        />

        <x-form-input
            name="waktu_aktivitas"
            label="Waktu Aktivitas"
            type="datetime-local"
            :value="old('waktu_aktivitas')"
        />
    @endcomponent
@endsection
