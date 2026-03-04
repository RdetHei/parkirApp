@extends('layouts.app')

@section('title', 'Kelola Kamera Peta')

@section('content')
    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <a href="{{ route('parking-maps.index') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-400 hover:text-gray-600 transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Penempatan Kamera</h1>
                        <p class="mt-1 text-sm text-gray-500">Peta: <span class="font-bold text-indigo-600">{{ $parkingMap->name }}</span></p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('parking-maps.edit', $parkingMap) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Informasi Peta
                    </a>
                    <a href="{{ route('parking-maps.slots.index', $parkingMap) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 rounded-xl text-sm font-bold hover:bg-emerald-100 transition-all border border-emerald-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        Kelola Slot
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                {{-- Editor Section --}}
                <div class="lg:col-span-8">
                    <div class="bg-white shadow-xl rounded-3xl overflow-hidden border border-gray-100">
                        <div class="px-8 py-6 border-b border-gray-50 bg-gradient-to-r from-slate-50 to-white flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="flex h-2 w-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Canvas Editor</span>
                            </div>
                            <div class="flex items-center gap-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                <span>Size: {{ $parkingMap->width }}x{{ $parkingMap->height }}px</span>
                            </div>
                        </div>

                        <div class="p-8">
                            <div class="overflow-auto max-h-[600px] rounded-2xl border-2 border-slate-100 bg-slate-50 p-6 shadow-inner custom-scrollbar">
                                <div
                                    id="camera-map-clickable"
                                    class="relative bg-center bg-no-repeat rounded-xl shadow-2xl cursor-crosshair transition-all duration-300 ring-8 ring-white"
                                    style="
                                        width: {{ $parkingMap->width }}px;
                                        height: {{ $parkingMap->height }}px;
                                        background-image: url('{{ asset($parkingMap->image_path) }}');
                                        background-size: contain;
                                        background-color: #0f172a;
                                    "
                                >
                                    @foreach($parkingMap->mapCameras as $pmc)
                                        <div
                                            class="absolute -translate-x-1/2 -translate-y-1/2 group/marker"
                                            style="left: {{ $pmc->x }}px; top: {{ $pmc->y }}px;"
                                        >
                                            <div class="relative">
                                                <div class="h-8 w-8 rounded-xl bg-indigo-600 shadow-xl shadow-indigo-500/40 ring-2 ring-white flex items-center justify-center text-white transition-transform hover:scale-125">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover/marker:block z-50">
                                                    <div class="bg-slate-900 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg whitespace-nowrap shadow-2xl">
                                                        {{ $pmc->camera->nama ?? 'Kamera' }}
                                                        <div class="text-[9px] text-slate-400 font-normal">Pos: {{ $pmc->x }}, {{ $pmc->y }}</div>
                                                    </div>
                                                    <div class="w-2 h-2 bg-slate-900 rotate-45 absolute -bottom-1 left-1/2 -translate-x-1/2"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    <div id="camera-preview" class="absolute -translate-x-1/2 -translate-y-1/2 hidden z-40">
                                        <div class="relative">
                                            <div class="h-10 w-10 rounded-2xl bg-sky-500 shadow-2xl shadow-sky-500/40 ring-4 ring-white flex items-center justify-center text-white animate-bounce">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </div>
                                            <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3">
                                                <span class="bg-sky-600 text-white text-[10px] font-bold px-2 py-1 rounded shadow-lg whitespace-nowrap">TITIK BARU</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100 flex items-start gap-3">
                                <svg class="w-5 h-5 text-indigo-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-xs text-indigo-700 leading-relaxed font-medium">Klik pada gambar peta untuk mengisi koordinat X & Y secara instan pada form di samping.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar Control Section --}}
                <div class="lg:col-span-4 space-y-8">
                    @if(session('success'))
                        <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3 animate-fade-in">
                            <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center text-white shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="p-4 bg-red-50 border border-red-100 rounded-2xl flex items-center gap-3 animate-fade-in">
                            <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center text-white shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </div>
                            <p class="text-sm font-bold text-red-800">{{ session('error') }}</p>
                        </div>
                    @endif

                    <div class="bg-white shadow-xl rounded-3xl p-6 border border-gray-100">
                        <h4 class="text-sm font-bold text-slate-900 mb-6 flex items-center gap-2">
                            <span class="w-6 h-6 bg-indigo-50 rounded-lg flex items-center justify-center border border-indigo-100">
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </span>
                            Tambah Posisi Baru
                        </h4>
                        
                        <form action="{{ route('parking-maps.cameras.store', $parkingMap) }}" method="POST" class="space-y-5">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="cam_camera_id" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Pilih Kamera</label>
                                    <select name="camera_id" id="cam_camera_id" required class="block w-full px-4 py-3 bg-gray-50 border border-slate-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm">
                                        <option value="">-- Cari kamera --</option>
                                        @foreach($cameras as $c)
                                            <option value="{{ $c->id }}">{{ $c->nama }} ({{ $c->tipe }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="cam_x" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Koordinat X</label>
                                        <div class="relative">
                                            <input type="number" name="x" id="cam_x" value="0" min="0" class="block w-full pl-4 pr-10 py-3 bg-gray-50 border border-slate-200 rounded-xl text-sm font-mono font-bold focus:ring-2 focus:ring-indigo-500 shadow-sm transition-all">
                                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-300">PX</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="cam_y" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Koordinat Y</label>
                                        <div class="relative">
                                            <input type="number" name="y" id="cam_y" value="0" min="0" class="block w-full pl-4 pr-10 py-3 bg-gray-50 border border-slate-200 rounded-xl text-sm font-mono font-bold focus:ring-2 focus:ring-indigo-500 shadow-sm transition-all">
                                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-300">PX</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="w-full justify-center px-6 py-4 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-2xl flex items-center gap-3 transition-all shadow-lg shadow-indigo-100 active:scale-95">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Simpan Posisi</span>
                            </button>
                        </form>
                    </div>

                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                        <h4 class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50 border-b border-slate-100">
                            Kamera Terpasang ({{ $parkingMap->mapCameras->count() }})
                        </h4>
                        <div class="divide-y divide-slate-50 max-h-[300px] overflow-auto custom-scrollbar">
                            @forelse($parkingMap->mapCameras as $pmc)
                                <div class="px-6 py-4 flex items-center justify-between hover:bg-slate-50 transition-colors group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400 group-hover:bg-indigo-100 group-hover:text-indigo-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-900">{{ $pmc->camera->nama ?? '-' }}</p>
                                            <p class="text-[10px] text-slate-400 font-medium">X: {{ $pmc->x }}, Y: {{ $pmc->y }}</p>
                                        </div>
                                    </div>
                                    <form action="{{ route('parking-maps.cameras.destroy', [$parkingMap, $pmc]) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity" onsubmit="return confirm('Hapus posisi kamera ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="px-6 py-10 text-center">
                                    <p class="text-xs text-slate-400 italic">Belum ada kamera.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
        #camera-map-clickable {
            transform-origin: center;
            image-rendering: pixelated;
        }
    </style>
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
                const x = parseInt(xInput.value || '0', 10);
                const y = parseInt(yInput.value || '0', 10);

                preview.style.left = x + 'px';
                preview.style.top = y + 'px';
                preview.classList.remove('hidden');
            }

            map.addEventListener('click', function (e) {
                const rect = map.getBoundingClientRect();
                
                // Get click coordinates relative to the element's actual display size
                let displayX = e.clientX - rect.left;
                let displayY = e.clientY - rect.top;

                // Scale to natural image size if displayed size is different
                const scaleX = {{ $parkingMap->width }} / rect.width;
                const scaleY = {{ $parkingMap->height }} / rect.height;

                let x = Math.round(displayX * scaleX);
                let y = Math.round(displayY * scaleY);

                x = Math.max(0, Math.min(x, {{ $parkingMap->width }}));
                y = Math.max(0, Math.min(y, {{ $parkingMap->height }}));

                xInput.value = x;
                yInput.value = y;
                updatePreview();
            });

            [xInput, yInput].forEach(el => {
                el.addEventListener('input', updatePreview);
            });
        });
    </script>
@endpush
