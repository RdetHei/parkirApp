@extends('layouts.app')

@section('title', 'Edit Layout Peta Parkir')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('parking-maps.index'),
        'title' => 'Edit Layout Peta Parkir',
        'description' => 'Ubah konfigurasi floor plan seperti nama, kode, dan ukuran.',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14M3 7a2 2 0 012-2h14m-4-2v4m0 0L14 5m1 2l1 1m-4 2h.01M7 12h.01M7 16h.01M11 16h.01M15 16h.01"></path></svg>',
        'cardTitle' => 'Form Edit Layout Peta',
        'cardDescription' => 'Sesuaikan data layout untuk peta parkir.',
        'action' => route('parking-maps.update', $item),
        'method' => 'PUT',
        'submitText' => 'Update'
    ])
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Layout <span class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14M3 7a2 2 0 012-2h14m-4-2v4m0 0L14 5m1 2l1 1m-4 2h.01M7 12h.01M7 16h.01M11 16h.01M15 16h.01"></path></svg>
                </div>
                <input type="text" name="name" id="name" value="{{ old('name', $item->name) }}" required
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
                <input type="text" name="code" id="code" value="{{ old('code', $item->code) }}" required
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
                <input type="text" name="image_path" id="image_path" value="{{ old('image_path', $item->image_path) }}" required
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('image_path') border-red-500 @enderror">
            </div>
            <p class="mt-1 text-xs text-gray-500">Path relatif dari folder public. Contoh: <code>images/floor2.png</code>.</p>
            @error('image_path')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="width" class="block text-sm font-semibold text-gray-700 mb-2">Lebar Image (px) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h16v4H4zM4 16h16v4H4z"></path></svg>
                    </div>
                    <input type="number" name="width" id="width" value="{{ old('width', $item->width) }}" required min="1"
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
                    <input type="number" name="height" id="height" value="{{ old('height', $item->height) }}" required min="1"
                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('height') border-red-500 @enderror">
                </div>
                @error('height')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="is_default" value="0">
                <input type="checkbox" name="is_default" value="1" {{ old('is_default', $item->is_default) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                <span class="text-sm font-semibold text-gray-700">Jadikan layout default</span>
            </label>
            @error('is_default')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    @endcomponent

    <div class="mt-8 flex flex-col md:flex-row items-center gap-4">
        <a href="{{ route('parking-maps.slots.index', $item) }}" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-4 bg-emerald-600 text-white rounded-2xl text-sm font-bold hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            Kelola Slot Parkir
        </a>
        <a href="{{ route('parking-maps.cameras.index', $item) }}" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-4 bg-indigo-600 text-white rounded-2xl text-sm font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
            Kelola Kamera Peta
        </a>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const map = document.getElementById('camera-map-clickable');
            if (!map) return;

            const xInput = document.getElementById('cam_x');
            const yInput = document.getElementById('cam_y');
            const preview = document.getElementById('camera-preview');

            function updatePreview() {
                if (!preview || !map) return;
                const rect = map.getBoundingClientRect();
                const x = parseInt(xInput.value || '0', 10);
                const y = parseInt(yInput.value || '0', 10);

                preview.style.left = x + 'px';
                preview.style.top = y + 'px';
                preview.classList.remove('hidden');
            }

            map.addEventListener('click', function (e) {
                const rect = map.getBoundingClientRect();
                let x = e.clientX - rect.left;
                let y = e.clientY - rect.top;

                x = Math.round(Math.max(0, Math.min(x, rect.width)));
                y = Math.round(Math.max(0, Math.min(y, rect.height)));

                xInput.value = x;
                yInput.value = y;

                updatePreview();
            });

            [xInput, yInput].forEach(function (el) {
                el.addEventListener('input', updatePreview);
            });

            updatePreview();
        });
    </script>
@endpush

