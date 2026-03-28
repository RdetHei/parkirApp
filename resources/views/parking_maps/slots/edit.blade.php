@extends('layouts.app')

@section('title', 'Edit Slot: ' . $slot->code)

@section('content')
    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto space-y-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">
                        Layout: {{ $parkingMap->code ?? '-' }}
                    </p>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                        Edit Slot: {{ $slot->code }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Seret posisi di peta atau klik titik baru, lalu perbarui detail di panel kanan.
                    </p>
                </div>
                <a href="{{ route('parking-maps.slots.index', $parkingMap) }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali ke daftar slot
                </a>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
                <div class="xl:col-span-2 space-y-4">
                    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gray-50">
                            <div>
                                <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wide">Peta parkir</p>
                                <p class="text-sm font-medium text-gray-800">{{ $parkingMap->name }}</p>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                                Klik untuk pindah posisi
                            </span>
                        </div>
                        <div class="p-3 sm:p-4">
                            @if($parkingMap->image_path)
                                <div class="overflow-auto rounded-xl border border-dashed border-gray-200 bg-slate-50/70 p-3">
                                    <div
                                        id="parking-map-clickable"
                                        class="relative bg-center bg-no-repeat rounded-lg shadow-inner cursor-crosshair transition-transform duration-150 hover:scale-[1.01]"
                                        style="
                                            width: {{ $parkingMap->width }}px;
                                            height: {{ $parkingMap->height }}px;
                                            background-image: url('{{ asset($parkingMap->image_path) }}');
                                            background-size: contain;
                                            background-color: #020617;
                                        "
                                    >
                                        <div
                                            id="slot-preview"
                                            class="absolute border-[2px] border-emerald-400 bg-emerald-400/25 ring-2 ring-emerald-500/30 shadow-lg shadow-emerald-500/40 backdrop-blur-[1px] pointer-events-none rounded-md"
                                            style="
                                                left: {{ $slot->x }}px;
                                                top: {{ $slot->y }}px;
                                                width: {{ $slot->width }}px;
                                                height: {{ $slot->height }}px;
                                            "
                                        ></div>
                                        <div class="pointer-events-none absolute inset-0 rounded-lg ring-1 ring-inset ring-white/5"></div>
                                    </div>
                                </div>
                                <p class="mt-3 text-[11px] text-gray-500">
                                    Titik <span class="font-mono text-xs text-gray-700">(0, 0)</span> berada di sudut kiri atas gambar.
                                    Klik di peta untuk memindahkan posisi slot; koordinat X/Y di panel kanan akan ikut berubah.
                                </p>
                            @else
                                <div class="flex flex-col items-center justify-center gap-3 rounded-xl border border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center">
                                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h2l3 7 4-4 4 8 3-6h2"></path>
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">Belum ada gambar peta</p>
                                        <p class="mt-1 text-xs text-gray-500">Setel <strong>Path Gambar</strong> di pengaturan layout peta untuk mengaktifkan mode klik.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gray-50/60">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Detail slot</p>
                                <p class="text-sm font-medium text-gray-900">Kode, posisi & relasi</p>
                            </div>
                        </div>

                        <form action="{{ route('parking-maps.slots.update', [$parkingMap, $slot]) }}" method="POST" class="px-4 py-5 space-y-5">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="code" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Kode slot <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="code"
                                    id="code"
                                    value="{{ old('code', $slot->code) }}"
                                    required
                                    class="block w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/70 @error('code') border-red-500 ring-red-200 @enderror"
                                >
                                @error('code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div class="rounded-xl bg-gray-50/80 border border-gray-200 px-3 py-3 space-y-3">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-xs font-semibold text-gray-600 tracking-wide">Koordinat pixel</p>
                                    <p class="text-[11px] text-gray-500">Klik peta untuk memperbarui X/Y</p>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label for="x" class="block text-xs font-medium text-gray-600 mb-1">X (px) <span class="text-red-500">*</span></label>
                                        <input
                                            type="number"
                                            name="x"
                                            id="x"
                                            value="{{ old('x', $slot->x) }}"
                                            required
                                            min="0"
                                            class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 @error('x') border-red-500 ring-red-200 @enderror"
                                        >
                                        @error('x')<p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label for="y" class="block text-xs font-medium text-gray-600 mb-1">Y (px) <span class="text-red-500">*</span></label>
                                        <input
                                            type="number"
                                            name="y"
                                            id="y"
                                            value="{{ old('y', $slot->y) }}"
                                            required
                                            min="0"
                                            class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 @error('y') border-red-500 ring-red-200 @enderror"
                                        >
                                        @error('y')<p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label for="width" class="block text-xs font-medium text-gray-600 mb-1">Lebar slot (px) <span class="text-red-500">*</span></label>
                                        <input
                                            type="number"
                                            name="width"
                                            id="width"
                                            value="{{ old('width', $slot->width) }}"
                                            required
                                            min="1"
                                            class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 @error('width') border-red-500 ring-red-200 @enderror"
                                        >
                                        @error('width')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label for="height" class="block text-xs font-medium text-gray-600 mb-1">Tinggi slot (px) <span class="text-red-500">*</span></label>
                                        <input
                                            type="number"
                                            name="height"
                                            id="height"
                                            value="{{ old('height', $slot->height) }}"
                                            required
                                            min="1"
                                            class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 @error('height') border-red-500 ring-red-200 @enderror"
                                        >
                                        @error('height')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <label for="area_parkir_id" class="block text-xs font-medium text-gray-600 mb-1.5">Area parkir (opsional)</label>
                                    <select
                                        name="area_parkir_id"
                                        id="area_parkir_id"
                                        class="block w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/70 @error('area_parkir_id') border-red-500 ring-red-200 @enderror"
                                    >
                                        <option value="">— Tidak dikaitkan —</option>
                                        @foreach($areas as $a)
                                            <option value="{{ $a->id_area }}" {{ (string) old('area_parkir_id', $slot->area_parkir_id ?? $parkingMap->area_parkir_id) === (string) $a->id_area ? 'selected' : '' }}>
                                                {{ $a->nama_area }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('area_parkir_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="camera_id" class="block text-xs font-medium text-gray-600 mb-1.5">Kamera (opsional)</label>
                                    <select
                                        name="camera_id"
                                        id="camera_id"
                                        class="block w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/70 @error('camera_id') border-red-500 ring-red-200 @enderror"
                                    >
                                        <option value="">— Tidak ada —</option>
                                        @foreach($cameras as $c)
                                            <option value="{{ $c->id }}" {{ old('camera_id', $slot->camera_id) == $c->id ? 'selected' : '' }}>
                                                {{ $c->nama }} ({{ $c->tipe }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('camera_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="notes" class="block text-xs font-medium text-gray-600 mb-1.5">Catatan</label>
                                    <input
                                        type="text"
                                        name="notes"
                                        id="notes"
                                        value="{{ old('notes', $slot->notes) }}"
                                        class="block w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/70 @error('notes') border-red-500 ring-red-200 @enderror"
                                    >
                                    @error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                                <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Update slot
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapEl = document.getElementById('parking-map-clickable');
            if (!mapEl) return;

            const xInput = document.getElementById('x');
            const yInput = document.getElementById('y');
            const wInput = document.getElementById('width');
            const hInput = document.getElementById('height');
            const preview = document.getElementById('slot-preview');

            function updatePreview() {
                if (!preview) return;

                const x = parseInt(xInput.value || '0', 10);
                const y = parseInt(yInput.value || '0', 10);
                const w = Math.max(1, parseInt(wInput.value || '0', 10));
                const h = Math.max(1, parseInt(hInput.value || '0', 10));

                preview.style.left = x + 'px';
                preview.style.top = y + 'px';
                preview.style.width = w + 'px';
                preview.style.height = h + 'px';
            }

            mapEl.addEventListener('click', function (e) {
                const rect = mapEl.getBoundingClientRect();
                const clickX = e.clientX - rect.left;
                const clickY = e.clientY - rect.top;

                const w = Math.max(1, parseInt(wInput.value || '0', 10));
                const h = Math.max(1, parseInt(hInput.value || '0', 10));

                let newX = Math.round(clickX - w / 2);
                let newY = Math.round(clickY - h / 2);

                newX = Math.max(0, Math.min(newX, rect.width - w));
                newY = Math.max(0, Math.min(newY, rect.height - h));

                xInput.value = newX;
                yInput.value = newY;

                updatePreview();
            });

            [xInput, yInput, wInput, hInput].forEach(function (el) {
                el.addEventListener('input', updatePreview);
            });

            updatePreview();
        });
    </script>
@endpush
