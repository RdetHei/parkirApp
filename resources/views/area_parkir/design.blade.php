@extends('layouts.app')

@section('title', 'Design Peta: ' . $area->nama_area)

@section('content')
<div class="p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 mb-10">
            <div>
                <a href="{{ route('area-parkir.index') }}" class="inline-flex items-center gap-2 text-[10px] font-black text-slate-500 hover:text-white uppercase tracking-[0.2em] transition-all mb-4 group">
                    <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Kembali ke Daftar Area
                </a>
                <h1 class="text-4xl font-black tracking-tighter text-white">MAP <span class="text-blue-500">ARCHITECT</span></h1>
                <p class="text-slate-400 text-sm mt-2 font-medium">Area: <span class="text-white font-bold">{{ $area->nama_area }}</span> • Kapasitas: <span class="text-blue-400 font-bold">{{ $area->kapasitas }} Slots</span></p>
            </div>

            <!-- Global Actions -->
            <div class="flex flex-wrap items-center gap-4">
                <!-- Mode Selector -->
                <div class="bg-slate-900/80 p-1.5 rounded-2xl border border-white/5 flex items-center gap-1 shadow-2xl backdrop-blur-xl">
                    <button onclick="setMode('select')" id="mode-select" class="mode-btn active px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"></path></svg>
                        Select
                    </button>
                    <button onclick="setMode('add-slot')" id="mode-slot" class="mode-btn px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Slot
                    </button>
                    <button onclick="setMode('add-camera')" id="mode-camera" class="mode-btn px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Add Cam
                    </button>
                </div>

                <button type="button" onclick="saveDesign()" class="px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-2xl shadow-blue-500/20 transition-all hover:scale-[1.02] active:scale-95">
                    Save Layout
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Designer Canvas -->
            <div class="lg:col-span-3 space-y-6">
                <div class="card-pro !p-0 overflow-hidden bg-slate-950 border-white/5 shadow-2xl relative group" id="canvas-wrapper">
                    <!-- Tooltip for current mode -->
                    <div id="mode-indicator" class="absolute top-6 left-6 z-50 px-4 py-2 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-full shadow-2xl pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity">
                        Mode: Select
                    </div>

                    <div id="canvas-container" class="overflow-auto min-h-[700px] p-12 bg-[radial-gradient(#ffffff05_1px,transparent_1px)] [background-size:20px_20px] flex items-center justify-center">
                        @if($area->map_image && $area->map_image !== 'parking_maps/default.png')
                            <div id="map-canvas" class="relative mx-auto shadow-[0_0_100px_rgba(0,0,0,0.5)] transition-all cursor-crosshair"
                                 onclick="handleCanvasClick(event)"
                                 style="width: {{ $area->map_width }}px; height: {{ $area->map_height }}px; background-image: url('{{ asset('storage/' . $area->map_image) }}'); background-size: cover; background-position: center;">

                                <!-- Slots -->
                                @foreach($area->slots as $slot)
                                <div class="parking-slot absolute cursor-move select-none border-2 border-blue-500/50 bg-blue-500/10 flex items-center justify-center text-[10px] font-black text-white hover:bg-blue-500/30 hover:border-blue-400 transition-all group"
                                     data-id="{{ $slot->id }}"
                                     data-code="{{ $slot->code }}"
                                     data-camera="{{ $slot->camera_id }}"
                                     data-notes="{{ $slot->notes }}"
                                     style="left: {{ $slot->x }}px; top: {{ $slot->y }}px; width: {{ $slot->width }}px; height: {{ $slot->height }}px;">
                                    <span class="pointer-events-none opacity-50 group-hover:opacity-100">{{ $slot->code }}</span>
                                    <div class="absolute -top-3 -right-3 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button onclick="removeElement(this, event)" class="w-6 h-6 bg-rose-500 text-white rounded-lg flex items-center justify-center shadow-xl hover:bg-rose-600">×</button>
                                    </div>
                                </div>
                                @endforeach

                                <!-- Cameras -->
                                @foreach($area->mapCameras as $mc)
                                <div class="parking-camera absolute cursor-move select-none bg-amber-500/10 border-2 border-amber-500/50 flex items-center justify-center text-amber-500 hover:bg-amber-500/30 transition-all group"
                                     data-camera-id="{{ $mc->camera_id }}"
                                     style="left: {{ $mc->x }}px; top: {{ $mc->y }}px; width: 44px; height: 44px; border-radius: 12px;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <div class="absolute -top-3 -right-3 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button onclick="removeElement(this, event)" class="w-6 h-6 bg-rose-500 text-white rounded-lg flex items-center justify-center shadow-xl hover:bg-rose-600">×</button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center p-12 max-w-md">
                                <div class="w-20 h-20 bg-white/5 rounded-[2rem] flex items-center justify-center mx-auto mb-8 border border-white/5">
                                    <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 012-2V6a2 2 0 01-2-2H6a2 2 0 01-2 2v12a2 2 0 012 2z"></path></svg>
                                </div>
                                <h3 class="text-xl font-bold text-white mb-4">Gambar Blueprint Belum Diunggah</h3>
                                <p class="text-slate-500 text-sm leading-relaxed mb-8">
                                    Anda perlu mengunggah gambar denah (blueprint) area ini terlebih dahulu untuk mulai mendesain tata letak slot parkir secara visual.
                                </p>
                                <label class="px-8 py-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all cursor-pointer inline-block">
                                    Unggah Gambar Denah
                                    <form action="{{ route('area-parkir.update', $area->id_area) }}" method="POST" enctype="multipart/form-data" class="hidden">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="nama_area" value="{{ $area->nama_area }}">
                                        <input type="hidden" name="kapasitas" value="{{ $area->kapasitas }}">
                                        <input type="file" name="map_image" onchange="this.form.submit()">
                                    </form>
                                </label>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Legend & Instructions -->
                <div class="flex flex-wrap items-center justify-between gap-6 p-6 bg-white/[0.02] rounded-3xl border border-white/5">
                    <div class="flex items-center gap-8">
                        <div class="flex items-center gap-3">
                            <span class="w-4 h-4 bg-blue-500/20 border-2 border-blue-500/50 rounded-lg"></span>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Parking Slot</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-4 h-4 bg-amber-500/20 border-2 border-amber-500/50 rounded-lg"></span>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Security Camera</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">
                        <span class="px-2 py-1 bg-white/5 rounded">L-Click</span> Place Element
                        <span class="px-2 py-1 bg-white/5 rounded ml-2">Drag</span> Reposition
                    </div>
                </div>
            </div>

            <!-- Properties Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Change Image Quick Action -->
                <div class="card-pro bg-gradient-to-br from-indigo-600/20 to-blue-600/20 border-blue-500/20">
                    <h3 class="text-[10px] font-black text-blue-400 uppercase tracking-[0.2em] mb-4">Background Blueprint</h3>
                    <form action="{{ route('area-parkir.update', $area->id_area) }}" method="POST" enctype="multipart/form-data" id="quick-image-form">
                        @csrf @method('PUT')
                        <input type="hidden" name="nama_area" value="{{ $area->nama_area }}">
                        <input type="hidden" name="kapasitas" value="{{ $area->kapasitas }}">
                        <label class="block group cursor-pointer">
                            <div class="p-4 bg-white/5 border-2 border-dashed border-white/10 rounded-2xl text-center group-hover:bg-white/10 group-hover:border-blue-500/50 transition-all">
                                <svg class="w-6 h-6 text-slate-500 mx-auto mb-2 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 012-2V6a2 2 0 01-2-2H6a2 2 0 01-2 2v12a2 2 0 012 2z"></path></svg>
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Update Image</span>
                            </div>
                            <input type="file" name="map_image" class="hidden" onchange="document.getElementById('quick-image-form').submit()">
                        </label>
                    </form>
                </div>

                <div class="card-pro sticky top-8 min-h-[400px]" id="properties-panel">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-2 h-6 bg-blue-500 rounded-full"></div>
                        <h3 class="text-xs font-black text-white uppercase tracking-widest">Inspector</h3>
                    </div>

                    <div id="no-selection" class="py-20 text-center">
                        <div class="w-16 h-16 bg-white/5 rounded-[2rem] flex items-center justify-center mx-auto mb-6 text-slate-700">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                        </div>
                        <p class="text-[10px] text-slate-500 font-black uppercase tracking-[0.2em]">Pilih atau Tambah<br>Elemen Baru</p>
                    </div>

                    <!-- Slot Properties -->
                    <div id="slot-properties" class="hidden space-y-8 animate-fade-in">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Slot Identification</label>
                            <input type="text" id="prop-slot-code" placeholder="e.g. A-01" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Dimensions (px)</label>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[9px] font-black text-slate-600 uppercase">W</span>
                                    <input type="number" id="prop-slot-width" class="w-full pl-10 pr-4 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                </div>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[9px] font-black text-slate-600 uppercase">H</span>
                                    <input type="number" id="prop-slot-height" class="w-full pl-10 pr-4 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Sensor/Camera Link</label>
                            <select id="prop-slot-camera" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all appearance-none">
                                <option value="">No Camera Attached</option>
                                @foreach($cameras as $cam)
                                <option value="{{ $cam->id }}">{{ $cam->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Technical Notes</label>
                            <textarea id="prop-slot-notes" rows="4" placeholder="Optional notes..." class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"></textarea>
                        </div>
                    </div>

                    <!-- Camera Properties -->
                    <div id="camera-properties" class="hidden space-y-8 animate-fade-in">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Select Hardware</label>
                            <select id="prop-camera-id" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all appearance-none">
                                @foreach($cameras as $cam)
                                <option value="{{ $cam->id }}">{{ $cam->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .mode-btn { color: #64748b; border: 1px solid transparent; }
    .mode-btn:hover { color: #fff; background: rgba(255,255,255,0.05); }
    .mode-btn.active { color: #fff; background: #3b82f6; border-color: #60a5fa; box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }

    .parking-slot.active { border-color: #3b82f6; background: rgba(59, 130, 246, 0.3); box-shadow: 0 0 30px rgba(59, 130, 246, 0.5); z-index: 100; }
    .parking-camera.active { border-color: #f59e0b; background: rgba(245, 158, 11, 0.3); box-shadow: 0 0 30px rgba(245, 158, 11, 0.5); z-index: 100; }

    #map-canvas { background-color: #020617; }

    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

@push('scripts')
<script>
    let currentMode = 'select';
    let activeElement = null;
    const canvas = document.getElementById('map-canvas');
    const modeIndicator = document.getElementById('mode-indicator');

    function setMode(mode) {
        currentMode = mode;
        document.querySelectorAll('.mode-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById('mode-' + (mode === 'select' ? 'select' : (mode === 'add-slot' ? 'slot' : 'camera'))).classList.add('active');

        modeIndicator.innerText = 'Mode: ' + mode.charAt(0).toUpperCase() + mode.slice(1).replace('-', ' ');

        if (mode !== 'select') {
            if (activeElement) activeElement.classList.remove('active');
            activeElement = null;
            showProperties(null);
        }
    }

    function handleCanvasClick(e) {
        if (e.target !== canvas) return; // Only trigger if clicking canvas itself

        const rect = canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        if (currentMode === 'add-slot') {
            createNewSlot(x - 30, y - 20); // Center the new slot on click
        } else if (currentMode === 'add-camera') {
            createNewCamera(x - 22, y - 22); // Center the new camera on click
        }
    }

    // Drag Logic
    document.addEventListener('mousedown', function(e) {
        if (currentMode !== 'select') return;

        const el = e.target.closest('.parking-slot, .parking-camera');
        if (el) {
            selectElement(el);
            let startX = e.clientX - el.offsetLeft;
            let startY = e.clientY - el.offsetTop;

            function move(e) {
                let x = e.clientX - startX;
                let y = e.clientY - startY;

                x = Math.max(0, Math.min(x, canvas.offsetWidth - el.offsetWidth));
                y = Math.max(0, Math.min(y, canvas.offsetHeight - el.offsetHeight));

                el.style.left = x + 'px';
                el.style.top = y + 'px';
            }

            function stop() {
                document.removeEventListener('mousemove', move);
                document.removeEventListener('mouseup', stop);
            }

            document.addEventListener('mousemove', move);
            document.addEventListener('mouseup', stop);
        }
    });

    function selectElement(el) {
        if (activeElement) activeElement.classList.remove('active');
        activeElement = el;
        activeElement.classList.add('active');
        showProperties(el);
    }

    function showProperties(el) {
        const noSel = document.getElementById('no-selection');
        const slotProps = document.getElementById('slot-properties');
        const camProps = document.getElementById('camera-properties');

        if (!el) {
            noSel.classList.remove('hidden');
            slotProps.classList.add('hidden');
            camProps.classList.add('hidden');
            return;
        }

        noSel.classList.add('hidden');
        if (el.classList.contains('parking-slot')) {
            slotProps.classList.remove('hidden');
            camProps.classList.add('hidden');
            document.getElementById('prop-slot-code').value = el.dataset.code || '';
            document.getElementById('prop-slot-width').value = parseInt(el.style.width);
            document.getElementById('prop-slot-height').value = parseInt(el.style.height);
            document.getElementById('prop-slot-camera').value = el.dataset.camera || '';
            document.getElementById('prop-slot-notes').value = el.dataset.notes || '';
        } else {
            slotProps.classList.add('hidden');
            camProps.classList.remove('hidden');
            document.getElementById('prop-camera-id').value = el.dataset.cameraId || '';
        }
    }

    // Real-time Property Binding
    document.getElementById('prop-slot-code').addEventListener('input', e => {
        if (activeElement) {
            activeElement.dataset.code = e.target.value;
            activeElement.querySelector('span').innerText = e.target.value;
        }
    });
    document.getElementById('prop-slot-width').addEventListener('input', e => {
        if (activeElement) activeElement.style.width = e.target.value + 'px';
    });
    document.getElementById('prop-slot-height').addEventListener('input', e => {
        if (activeElement) activeElement.style.height = e.target.value + 'px';
    });
    document.getElementById('prop-slot-camera').addEventListener('change', e => {
        if (activeElement) activeElement.dataset.camera = e.target.value;
    });
    document.getElementById('prop-slot-notes').addEventListener('input', e => {
        if (activeElement) activeElement.dataset.notes = e.target.value;
    });
    document.getElementById('prop-camera-id').addEventListener('change', e => {
        if (activeElement) activeElement.dataset.cameraId = e.target.value;
    });

    function createNewSlot(x, y) {
        const slot = document.createElement('div');
        slot.className = 'parking-slot absolute cursor-move select-none border-2 border-blue-500/50 bg-blue-500/10 flex items-center justify-center text-[10px] font-black text-white hover:bg-blue-500/30 hover:border-blue-400 transition-all group';
        slot.style.left = x + 'px';
        slot.style.top = y + 'px';
        slot.style.width = '60px';
        slot.style.height = '40px';
        slot.dataset.code = 'S-' + (canvas.querySelectorAll('.parking-slot').length + 1);
        slot.innerHTML = `<span class="pointer-events-none opacity-50 group-hover:opacity-100">${slot.dataset.code}</span><div class="absolute -top-3 -right-3 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity"><button onclick="removeElement(this, event)" class="w-6 h-6 bg-rose-500 text-white rounded-lg flex items-center justify-center shadow-xl hover:bg-rose-600">×</button></div>`;
        canvas.appendChild(slot);
        setMode('select');
        selectElement(slot);
    }

    function createNewCamera(x, y) {
        const cam = document.createElement('div');
        cam.className = 'parking-camera absolute cursor-move select-none bg-amber-500/10 border-2 border-amber-500/50 flex items-center justify-center text-amber-500 hover:bg-amber-500/30 transition-all group';
        cam.style.left = x + 'px';
        cam.style.top = y + 'px';
        cam.style.width = '44px';
        cam.style.height = '44px';
        cam.style.borderRadius = '12px';
        cam.dataset.cameraId = '';
        cam.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg><div class="absolute -top-3 -right-3 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity"><button onclick="removeElement(this, event)" class="w-6 h-6 bg-rose-500 text-white rounded-lg flex items-center justify-center shadow-xl hover:bg-rose-600">×</button></div>`;
        canvas.appendChild(cam);
        setMode('select');
        selectElement(cam);
    }

    function removeElement(btn, e) {
        e.stopPropagation();
        const el = btn.closest('.parking-slot, .parking-camera');
        if (confirm('Hapus elemen ini dari desain?')) {
            el.remove();
            activeElement = null;
            showProperties(null);
        }
    }

    async function saveDesign() {
        const slots = [];
        const cameras = [];

        canvas.querySelectorAll('.parking-slot').forEach(el => {
            slots.push({
                code: el.dataset.code,
                x: parseInt(el.style.left),
                y: parseInt(el.style.top),
                width: parseInt(el.style.width),
                height: parseInt(el.style.height),
                camera_id: el.dataset.camera,
                notes: el.dataset.notes
            });
        });

        canvas.querySelectorAll('.parking-camera').forEach(el => {
            if (el.dataset.cameraId) {
                cameras.push({
                    camera_id: el.dataset.cameraId,
                    x: parseInt(el.style.left),
                    y: parseInt(el.style.top)
                });
            }
        });

        try {
            const response = await fetch("{{ route('area-parkir.save-design', $area->id_area) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ slots, cameras })
            });

            const data = await response.json();
            if (response.ok) {
                alert('Layout peta berhasil diperbarui!');
            } else {
                throw new Error(data.message || 'Server error');
            }
        } catch (error) {
            alert('Gagal menyimpan: ' + error.message);
        }
    }
</script>
@endpush
@endsection
