@extends('layouts.app')

@section('title', 'Edit Slot: ' . $slot->code)

@section('content')
    @component('components.form-card', [
        'backUrl' => route('parking-maps.slots.index', $parkingMap),
        'title' => 'Edit Slot',
        'description' => 'Peta: ' . $parkingMap->name . ' — ' . $slot->code,
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>',
        'cardTitle' => 'Form Edit Slot',
        'cardDescription' => 'Ubah koordinat dan keterangan.',
        'action' => route('parking-maps.slots.update', [$parkingMap, $slot]),
        'method' => 'PUT',
        'submitText' => 'Update'
    ])
        <div>
            <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">Kode slot <span class="text-red-500">*</span></label>
            <input type="text" name="code" id="code" value="{{ old('code', $slot->code) }}" required
                   class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 @error('code') border-red-500 @enderror">
            @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="x" class="block text-sm font-semibold text-gray-700 mb-2">X (px) <span class="text-red-500">*</span></label>
                <input type="number" name="x" id="x" value="{{ old('x', $slot->x) }}" required min="0" class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 @error('x') border-red-500 @enderror">
                @error('x')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="y" class="block text-sm font-semibold text-gray-700 mb-2">Y (px) <span class="text-red-500">*</span></label>
                <input type="number" name="y" id="y" value="{{ old('y', $slot->y) }}" required min="0" class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 @error('y') border-red-500 @enderror">
                @error('y')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="width" class="block text-sm font-semibold text-gray-700 mb-2">Lebar (px) <span class="text-red-500">*</span></label>
                <input type="number" name="width" id="width" value="{{ old('width', $slot->width) }}" required min="1" class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 @error('width') border-red-500 @enderror">
                @error('width')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="height" class="block text-sm font-semibold text-gray-700 mb-2">Tinggi (px) <span class="text-red-500">*</span></label>
                <input type="number" name="height" id="height" value="{{ old('height', $slot->height) }}" required min="1" class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 @error('height') border-red-500 @enderror">
                @error('height')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label for="area_parkir_id" class="block text-sm font-semibold text-gray-700 mb-2">Area Parkir (opsional)</label>
            <select name="area_parkir_id" id="area_parkir_id" class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 @error('area_parkir_id') border-red-500 @enderror">
                <option value="">— Tidak dikaitkan —</option>
                @foreach($areas as $a)
                    <option value="{{ $a->id_area }}" {{ old('area_parkir_id', $slot->area_parkir_id) == $a->id_area ? 'selected' : '' }}>{{ $a->nama_area }}</option>
                @endforeach
            </select>
            @error('area_parkir_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="camera_id" class="block text-sm font-semibold text-gray-700 mb-2">Kamera (opsional)</label>
            <select name="camera_id" id="camera_id" class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 @error('camera_id') border-red-500 @enderror">
                <option value="">— Tidak ada —</option>
                @foreach($cameras as $c)
                    <option value="{{ $c->id }}" {{ old('camera_id', $slot->camera_id) == $c->id ? 'selected' : '' }}>{{ $c->nama }} ({{ $c->tipe }})</option>
                @endforeach
            </select>
            @error('camera_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
            <input type="text" name="notes" id="notes" value="{{ old('notes', $slot->notes) }}"
                   class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 @error('notes') border-red-500 @enderror">
            @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    @endcomponent
@endsection
