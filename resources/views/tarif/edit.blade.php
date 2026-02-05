@extends('layouts.app')

@section('title', 'Edit Tarif')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('tarif.index'),
        'title' => 'Edit Tarif',
        'description' => 'Ubah data tarif parkir yang sudah ada di sistem',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        'cardTitle' => 'Form Edit Tarif',
        'cardDescription' => 'Sesuaikan informasi tarif',
        'action' => route('tarif.update', $item),
        'method' => 'PUT',
        'submitText' => 'Update'
    ])
        <div>
            <label for="jenis_kendaraan" class="block text-sm font-semibold text-gray-700 mb-2">Jenis Kendaraan <span class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                </div>
                <select name="jenis_kendaraan" id="jenis_kendaraan" required
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('jenis_kendaraan') border-red-500 @enderror">
                    <option value="motor" {{ old('jenis_kendaraan', $item->jenis_kendaraan) == 'motor' ? 'selected' : '' }}>Motor</option>
                    <option value="mobil" {{ old('jenis_kendaraan', $item->jenis_kendaraan) == 'mobil' ? 'selected' : '' }}>Mobil</option>
                    <option value="lainnya" {{ old('jenis_kendaraan', $item->jenis_kendaraan) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            @error('jenis_kendaraan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="tarif_perjam" class="block text-sm font-semibold text-gray-700 mb-2">Tarif per jam (Rp) <span class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <input type="number" name="tarif_perjam" id="tarif_perjam" value="{{ old('tarif_perjam', $item->tarif_perjam) }}" required min="0" placeholder="Tarif per jam"
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('tarif_perjam') border-red-500 @enderror">
            </div>
            @error('tarif_perjam')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    @endcomponent
@endsection
