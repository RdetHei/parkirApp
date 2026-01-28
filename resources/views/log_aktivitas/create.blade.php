@extends('layouts.app')

@section('title','Buat Log Aktivitas')

@section('content')
@extends('layouts.app')

@section('title','Buat Log Aktivitas')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md mt-10">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Buat Log Aktivitas Baru</h2>

    <form action="{{ route('log-aktivitas.store') }}" method="POST" class="space-y-4">
        @csrf

        <div class="mb-4">
            <label for="id_user" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                User
            </label>
            <select name="id_user" id="id_user" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
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

        <div class="flex justify-end space-x-2">
            <a href="{{ route('log-aktivitas.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-transparent rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Buat Log
            </button>
        </div>
    </form>
</div>
@endsection

@endsection
