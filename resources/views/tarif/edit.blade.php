@extends('layouts.app')

@section('title','Edit Tarif')

@section('content')
@extends('layouts.app')

@section('title','Edit Tarif')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md mt-10">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Edit Tarif</h2>

    <form action="{{ route('tarif.update', $item) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="jenis_kendaraan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Jenis Kendaraan
            </label>
            <select name="jenis_kendaraan" id="jenis_kendaraan" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="motor" {{ old('jenis_kendaraan', $item->jenis_kendaraan) == 'motor' ? 'selected' : '' }}>Motor</option>
                <option value="mobil" {{ old('jenis_kendaraan', $item->jenis_kendaraan) == 'mobil' ? 'selected' : '' }}>Mobil</option>
                <option value="lainnya" {{ old('jenis_kendaraan', $item->jenis_kendaraan) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
            @error('jenis_kendaraan')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <x-form-input
            name="tarif_perjam"
            label="Tarif per jam"
            type="number"
            :value="old('tarif_perjam', $item->tarif_perjam)"
            placeholder="Masukkan tarif per jam"
        />

        <div class="flex justify-end space-x-2">
            <a href="{{ route('tarif.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-transparent rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Update
            </button>
        </div>
    </form>
</div>
@endsection
