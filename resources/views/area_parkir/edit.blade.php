@extends('layouts.app')

@section('title','Edit Area Parkir')

@section('content')
@extends('layouts.app')

@section('title','Edit Area Parkir')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md mt-10">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Edit Area Parkir</h2>

    <form action="{{ route('area-parkir.update', $area) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <x-form-input
            name="nama_area"
            label="Nama Area"
            type="text"
            :value="old('nama_area', $area->nama_area)"
            placeholder="Masukkan nama area parkir"
        />

        <x-form-input
            name="kapasitas"
            label="Kapasitas"
            type="number"
            :value="old('kapasitas', $area->kapasitas)"
            placeholder="Masukkan kapasitas area parkir"
        />

        <div class="flex justify-end space-x-2">
            <a href="{{ route('area-parkir.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-transparent rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Update
            </button>
        </div>
    </form>
</div>
@endsection
