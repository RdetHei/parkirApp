@extends('layouts.app')

@section('title','Create Tarif')

@section('content')
<div class="max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Create Tarif</h2>

    <form action="{{ route('tarif.store') }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Jenis Kendaraan</label>
            <select name="jenis_kendaraan" class="mt-1 block w-full border-gray-300 rounded-md">
                <option value="motor">Motor</option>
                <option value="mobil">Mobil</option>
                <option value="lainnya">Lainnya</option>
            </select>
            @error('jenis_kendaraan')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Tarif per jam</label>
            <input type="number" name="tarif_perjam" value="{{ old('tarif_perjam') }}" class="mt-1 block w-full border-gray-300 rounded-md">
            @error('tarif_perjam')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        <div class="flex justify-end">
            <a href="{{ route('tarif.index') }}" class="mr-2">Cancel</a>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Create</button>
        </div>
    </form>
</div>
@endsection
