@extends('layouts.app')

@section('title','Create Log Aktivitas')

@section('content')
<div class="max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Create Log Aktivitas</h2>

    <form action="{{ route('log-aktivitas.store') }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">User ID</label>
            <input type="number" name="id_user" value="{{ old('id_user') }}" class="mt-1 block w-full border-gray-300 rounded-md">
            @error('id_user')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Aktivitas</label>
            <input type="text" name="aktivitas" value="{{ old('aktivitas') }}" class="mt-1 block w-full border-gray-300 rounded-md">
            @error('aktivitas')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Waktu Aktivitas</label>
            <input type="datetime-local" name="waktu_aktivitas" value="{{ old('waktu_aktivitas') }}" class="mt-1 block w-full border-gray-300 rounded-md">
            @error('waktu_aktivitas')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        <div class="flex justify-end">
            <a href="{{ route('log-aktivitas.index') }}" class="mr-2">Cancel</a>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Create</button>
        </div>
    </form>
</div>
@endsection
