@extends('layouts.app')

@section('title','Create Area Parkir')

@section('content')
<div class="max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Create Area Parkir</h2>

    <form action="{{ route('area-parkir.store') }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nama Area</label>
            <input type="text" name="nama_area" value="{{ old('nama_area') }}" class="mt-1 block w-full border-gray-300 rounded-md">
            @error('nama_area')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Kapasitas</label>
            <input type="number" name="kapasitas" value="{{ old('kapasitas') }}" class="mt-1 block w-full border-gray-300 rounded-md">
            @error('kapasitas')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        <div class="flex justify-end">
            <a href="{{ route('area-parkir.index') }}" class="mr-2">Cancel</a>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Create</button>
        </div>
    </form>
</div>
@endsection
