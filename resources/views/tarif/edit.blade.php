
@extends('layouts.app')

@section('title','Edit Tarif')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('tarif.index'),
        'title' => 'Edit Tarif',
        'description' => 'Ubah data tarif parkir yang sudah ada di sistem',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>',
        'cardTitle' => 'Form Edit Tarif',
        'cardDescription' => 'Sesuaikan informasi tarif',
        'action' => route('tarif.update', $item),
        'method' => 'PUT',
        'submitText' => 'Update'
    ])
        <div class="mb-4">
            <label for="jenis_kendaraan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Jenis Kendaraan
            </label>
            <select name="jenis_kendaraan" id="jenis_kendaraan" class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm bg-gray-800 text-white">
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
    @endcomponent
@endsection
