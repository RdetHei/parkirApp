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

    {{-- Posisi kamera di peta --}}
    <div class="mt-8 bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-900">Lokasi kamera di peta</h3>
            <a href="{{ route('parking-maps.slots.index', $item) }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                Kelola slot →
            </a>
        </div>
        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800">{{ session('error') }}</div>
            @endif

            <form action="{{ route('parking-maps.cameras.store', $item) }}" method="POST" class="flex flex-wrap items-end gap-4 mb-6">
                @csrf
                <div>
                    <label for="cam_camera_id" class="block text-xs font-medium text-gray-600 mb-1">Kamera</label>
                    <select name="camera_id" id="cam_camera_id" required class="block w-56 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500">
                        <option value="">-- Pilih kamera --</option>
                        @foreach($cameras as $c)
                            <option value="{{ $c->id }}">{{ $c->nama }} ({{ $c->tipe }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="cam_x" class="block text-xs font-medium text-gray-600 mb-1">X (px)</label>
                    <input type="number" name="x" id="cam_x" value="100" min="0" class="block w-24 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label for="cam_y" class="block text-xs font-medium text-gray-600 mb-1">Y (px)</label>
                    <input type="number" name="y" id="cam_y" value="100" min="0" class="block w-24 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg">Tambah posisi</button>
            </form>

            @if($item->mapCameras && $item->mapCameras->count())
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-600">Kamera</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-600">X</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-600">Y</th>
                            <th class="px-4 py-2 text-right font-medium text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($item->mapCameras as $pmc)
                            <tr>
                                <td class="px-4 py-2">{{ $pmc->camera->nama ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $pmc->x }}</td>
                                <td class="px-4 py-2">{{ $pmc->y }}</td>
                                <td class="px-4 py-2 text-right">
                                    <form action="{{ route('parking-maps.cameras.destroy', [$item, $pmc]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus posisi kamera ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 text-sm">Belum ada kamera ditempatkan di peta ini. Tambah posisi (X, Y) sesuai koordinat pixel di floor plan.</p>
            @endif
        </div>
    </div>
@endsection

