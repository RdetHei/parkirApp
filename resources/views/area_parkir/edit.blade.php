@extends('layouts.app')

@section('title', 'Edit Area Parkir')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('area-parkir.index'),
        'title' => 'Edit Area Parkir',
        'description' => 'Ubah data area parkir yang sudah ada di sistem',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
        'cardTitle' => 'Form Edit Area Parkir',
        'cardDescription' => 'Sesuaikan detail area parkir',
        'action' => route('area-parkir.update', $area),
        'method' => 'PUT',
        'submitText' => 'Update'
    ])
        <div>
            <label for="nama_area" class="block text-sm font-semibold text-gray-700 mb-2">Nama Area <span class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <input type="text" name="nama_area" id="nama_area" value="{{ old('nama_area', $area->nama_area) }}" required placeholder="Nama area parkir"
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nama_area') border-red-500 @enderror">
            </div>
            @error('nama_area')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="kapasitas" class="block text-sm font-semibold text-gray-700 mb-2">Kapasitas <span class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                </div>
                <input type="number" name="kapasitas" id="kapasitas" value="{{ old('kapasitas', $area->kapasitas) }}" required min="1" placeholder="Jumlah slot"
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('kapasitas') border-red-500 @enderror">
            </div>
            @error('kapasitas')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    @endcomponent
@endsection
