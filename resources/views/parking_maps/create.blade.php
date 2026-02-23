@extends('layouts.app')

@section('title', 'Tambah Layout Peta Parkir')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('parking-maps.index'),
        'title' => 'Tambah Layout Peta Parkir',
        'description' => 'Definisikan floor plan seperti floor1, floor2, atau outside.',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14M3 7a2 2 0 012-2h14m-4-2v4m0 0L14 5m1 2l1 1m-4 2h.01M7 12h.01M7 16h.01M11 16h.01M15 16h.01"></path></svg>',
        'cardTitle' => 'Form Layout Peta',
        'cardDescription' => 'Isi nama, kode, path gambar, dan ukuran image overlay.',
        'action' => route('parking-maps.store'),
        'method' => 'POST',
        'submitText' => 'Simpan'
    ])
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Layout <span class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14M3 7a2 2 0 012-2h14m-4-2v4m0 0L14 5m1 2l1 1m-4 2h.01M7 12h.01M7 16h.01M11 16h.01M15 16h.01"></path></svg>
                </div>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Lantai 1, Outside"
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('name') border-red-500 @enderror">
            </div>
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">Kode Layout <span class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <input type="text" name="code" id="code" value="{{ old('code') }}" required placeholder="Contoh: floor1, floor2, outside"
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('code') border-red-500 @enderror">
            </div>
            @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="image_path" class="block text-sm font-semibold text-gray-700 mb-2">Path Gambar <span class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h2l3 7 4-4 4 8 3-6h2"></path></svg>
                </div>
                <input type="text" name="image_path" id="image_path" value="{{ old('image_path', 'images/floor1.png') }}" required placeholder="Contoh: images/floor1.png"
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('image_path') border-red-500 @enderror">
            </div>
            <p class="mt-1 text-xs text-gray-500">Path relatif dari folder public. Pastikan file gambar sudah ada, misalnya <code>public/images/floor1.png</code>.</p>
            @error('image_path')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="width" class="block text-sm font-semibold text-gray-700 mb-2">Lebar Image (px) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h16v4H4zM4 16h16v4H4z"></path></svg>
                    </div>
                    <input type="number" name="width" id="width" value="{{ old('width', 1000) }}" required min="1"
                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('width') border-red-500 @enderror">
                </div>
                @error('width')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="height" class="block text-sm font-semibold text-gray-700 mb-2">Tinggi Image (px) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h4v16H4zM16 4h4v16h-4z"></path></svg>
                    </div>
                    <input type="number" name="height" id="height" value="{{ old('height', 800) }}" required min="1"
                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('height') border-red-500 @enderror">
                </div>
                @error('height')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="is_default" value="0">
                <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}
                       class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                <span class="text-sm font-semibold text-gray-700">Jadikan layout default (dipakai saat membuka Peta Parkir pertama kali)</span>
            </label>
            @error('is_default')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    @endcomponent
@endsection

