@extends('layouts.app')

@section('title','Edit Tarif')

@section('content')
<div class="max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Edit Tarif</h2>

    <form action="{{ route('tarif.update', $item) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Jenis Kendaraan</label>
            <select name="jenis_kendaraan" class="mt-1 block w-full border-gray-300 rounded-md">
                <option value="motor" {{ $item->jenis_kendaraan=='motor'?'selected':'' }}>Motor</option>
                <option value="mobil" {{ $item->jenis_kendaraan=='mobil'?'selected':'' }}>Mobil</option>
                <option value="lainnya" {{ $item->jenis_kendaraan=='lainnya'?'selected':'' }}>Lainnya</option>
            </select>
            @error('jenis_kendaraan')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Tarif per jam</label>
            <input type="number" name="tarif_perjam" value="{{ old('tarif_perjam', $item->tarif_perjam) }}" class="mt-1 block w-full border-gray-300 rounded-md">
            @error('tarif_perjam')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        <div class="flex justify-end">
            <a href="{{ route('tarif.index') }}" class="mr-2">Cancel</a>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Update</button>
        </div>
    </form>
</div>
@endsection
