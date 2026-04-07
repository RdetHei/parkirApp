@extends('layouts.app')

@section('title', 'Booking Slot Parkir - NESTON')

@section('content')
@php
    $daerahs = $daerahs ?? collect();
    $selectedDaerah = $selectedDaerah ?? null;
    $areas = $areas ?? collect();
    $statusPerArea = $statusPerArea ?? [];
    $myBookingIds = $myBookingIds ?? [];
    $kendaraans = $kendaraans ?? collect();
    $tarifs = $tarifs ?? collect();
    $selectedAreaId = $selectedAreaId ?? ($map->id_area ?? null);
@endphp
<div class="p-8 relative z-10 animate-fade-in" style="background:#020617;min-height:100vh;">
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="max-w-6xl mx-auto relative z-10">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-black uppercase tracking-widest rounded-full border border-emerald-500/20">Smart Reservation</span>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-white uppercase">Booking <span class="text-emerald-500">Slot</span></h1>
                <p class="text-slate-400 text-sm mt-2 font-medium tracking-wide">Pilih area parkir yang tersedia untuk reservasi instan.</p>
            </div>
            <a href="{{ route('user.dashboard') }}"
               class="group px-6 py-3.5 bg-white/5 border border-white/5 rounded-2xl text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-white hover:bg-white/10 transition-all flex items-center gap-3 active:scale-95">
                <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                Kembali
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-500 text-sm font-bold flex items-center gap-3 animate-fade-in">
                <i class="fa-solid fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/20 rounded-2xl text-rose-500 text-sm font-bold flex items-center gap-3 animate-fade-in">
                <i class="fa-solid fa-triangle-exclamation"></i>
                {{ session('error') }}
            </div>
        @endif

        {{--
            VARIANT 1: Sidebar kiri sticky (kendaraan + tarif + summary slot)
            Kanan: area cards + map
        --}}
        <div class="flex flex-col lg:flex-row gap-6 items-start">

            {{-- ── SIDEBAR ── --}}
            <div class="w-full lg:w-64 shrink-0 lg:sticky lg:top-6 flex flex-col gap-4">

                {{-- Config card --}}
                <div class="rounded-2xl border overflow-hidden" style="background:#0d1526;border-color:rgba(255,255,255,0.07);">
                    <div class="px-5 py-3.5 border-b" style="border-color:rgba(255,255,255,0.05);">
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Konfigurasi</p>
                    </div>
                    <div class="p-5 flex flex-col gap-5">
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">Pilih Daerah</label>
                            <select id="filter_daerah"
                                    class="block w-full rounded-xl border px-4 py-3 text-sm text-white focus:border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/10 focus:outline-none transition-all font-bold"
                                    style="background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.08);">
                                <option value="" class="bg-slate-900">— Semua Daerah —</option>
                                @foreach($daerahs as $d)
                                <option value="{{ $d }}" {{ $selectedDaerah == $d ? 'selected' : '' }} class="bg-slate-900">
                                    {{ $d }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">Pilih Peta Area</label>
                            <select id="filter_area"
                                    class="block w-full rounded-xl border px-4 py-3 text-sm text-white focus:border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/10 focus:outline-none transition-all font-bold"
                                    style="background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.08);">
                                <option value="" class="bg-slate-900">— Pilih area —</option>
                                @foreach($areas as $areaItem)
                                <option value="{{ $areaItem->id_area }}" {{ (string) $selectedAreaId === (string) $areaItem->id_area ? 'selected' : '' }} class="bg-slate-900">
                                    {{ $areaItem->nama_area }}{{ $areaItem->daerah ? ' · ' . $areaItem->daerah : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">Kendaraan</label>
                            <select id="booking_kendaraan_id"
                                    class="block w-full rounded-xl border px-4 py-3 text-sm text-white focus:border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/10 focus:outline-none transition-all font-bold"
                                    style="background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.08);">
                                <option value="" class="bg-slate-900">— Pilih kendaraan —</option>
                                @foreach($kendaraans as $k)
                                <option value="{{ $k->id_kendaraan }}" data-jenis="{{ $k->jenis_kendaraan }}" class="bg-slate-900">
                                    {{ $k->plat_nomor }} · {{ strtoupper($k->jenis_kendaraan) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">Tarif</label>
                            <select id="booking_tarif_id"
                                    class="block w-full rounded-xl border px-4 py-3 text-sm text-white focus:border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/10 focus:outline-none transition-all font-bold"
                                    style="background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.08);">
                                <option value="" class="bg-slate-900">— Otomatis —</option>
                                @foreach($tarifs as $t)
                                <option value="{{ $t->id_tarif }}" data-jenis="{{ $t->jenis_kendaraan }}" class="bg-slate-900">
                                    {{ strtoupper($t->jenis_kendaraan) }} · Rp {{ number_format($t->tarif_perjam, 0, ',', '.') }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Slot summary --}}
                <div class="rounded-2xl border overflow-hidden" style="background:#0d1526;border-color:rgba(255,255,255,0.07);">
                    <div class="px-5 py-3.5 border-b" style="border-color:rgba(255,255,255,0.05);">
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Ringkasan Area</p>
                    </div>
                    <div class="p-5 flex flex-col gap-3">
                        @php
                            $totalArea = $areas->count();
                            $tersedia  = $areas->filter(fn($a) => ($statusPerArea[$a->id_area] ?? '') === 'empty')->count();
                            $milikku   = $areas->filter(fn($a) => ($statusPerArea[$a->id_area] ?? '') === 'bookmarked-by-me')->count();
                            $penuh     = $totalArea - $tersedia - $milikku;
                        @endphp
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500">Total Area</span>
                            <span class="text-sm font-black text-white">{{ $totalArea }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500">Tersedia</span>
                            <span class="text-sm font-black text-emerald-400">{{ $tersedia }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500">Milik Saya</span>
                            <span class="text-sm font-black text-blue-400">{{ $milikku }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500">Penuh</span>
                            <span class="text-sm font-black text-slate-500">{{ $penuh }}</span>
                        </div>
                    </div>
                </div>

                {{-- Legend --}}
                <div class="rounded-2xl border p-5" style="background:#0d1526;border-color:rgba(255,255,255,0.07);">
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-3">Keterangan</p>
                    <div class="flex flex-col gap-2.5">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-xs text-white">Tersedia</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <span class="text-xs text-white">Milik Anda</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-600"></span>
                            <span class="text-xs text-white">Tidak Tersedia</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── MAIN CONTENT ── --}}
            <div class="flex-1 min-w-0 flex flex-col gap-6">

                {{-- Area cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($areas as $area)
                    @php
                        $status      = $statusPerArea[$area->id_area] ?? 'empty';
                        $isAvailable = $status === 'empty';
                        $isMine      = $status === 'bookmarked-by-me';
                        $borderCls   = $isAvailable ? 'rgba(16,185,129,0.2)' : ($isMine ? 'rgba(59,130,246,0.2)' : 'rgba(255,255,255,0.06)');
                        $bgCls       = $isAvailable ? 'rgba(16,185,129,0.04)' : ($isMine ? 'rgba(59,130,246,0.04)' : 'rgba(255,255,255,0.02)');
                        $pct         = $area->kapasitas > 0 ? ($area->terisi / $area->kapasitas * 100) : 0;
                    @endphp
                    <div class="rounded-2xl overflow-hidden flex flex-col {{ !$isAvailable && !$isMine ? 'opacity-60' : '' }}"
                         style="background:{{ $bgCls }};border:1px solid {{ $borderCls }};">

                        <div class="px-5 pt-5 pb-1 flex items-start justify-between">
                            <h2 class="text-sm font-black text-white uppercase tracking-tight">{{ $area->nama_area }}</h2>
                            <span class="px-2.5 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest border
                                {{ $isAvailable ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' :
                                   ($isMine ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' : 'bg-white/5 text-slate-500 border-white/10') }}">
                                {{ $isAvailable ? 'Tersedia' : ($isMine ? 'Milik Anda' : 'Terisi') }}
                            </span>
                        </div>
                        <div class="px-5 pb-4">
                            <div class="flex items-center gap-1.5 px-2 py-0.5 bg-white/5 rounded-full border border-white/5 w-fit">
                                <i class="fa-solid fa-location-dot text-[8px] text-emerald-500"></i>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $area->daerah ?? 'Unknown' }}</span>
                            </div>
                        </div>

                        <div class="px-5 pb-4 flex flex-col gap-1.5 flex-1">
                            <div class="flex items-center justify-between text-[9px] font-bold uppercase tracking-widest">
                                <span class="text-slate-500">{{ $area->terisi }}/{{ $area->kapasitas }} slot terisi</span>
                                <span class="text-slate-400">{{ number_format($pct, 0) }}%</span>
                            </div>
                            <div class="h-1 w-full rounded-full overflow-hidden" style="background:rgba(255,255,255,0.06);">
                                <div class="h-full rounded-full {{ $isAvailable ? 'bg-emerald-500' : ($isMine ? 'bg-blue-500' : 'bg-slate-600') }}"
                                     style="width:{{ $pct }}%"></div>
                            </div>
                        </div>

                        <div class="px-5 pb-5">
                            @if($isAvailable)
                            <form method="POST" action="{{ route('user.bookings.book', $area->id_area) }}" class="booking-area-form">
                                @csrf
                                <input type="hidden" name="id_kendaraan" value="">
                                <input type="hidden" name="id_tarif" value="">
                                <button type="submit" class="w-full py-3 bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all active:scale-[0.98]">
                                    Booking Sekarang
                                </button>
                            </form>
                            @elseif($isMine)
                                @php $transId = $myBookingIds[$area->id_area] ?? null; @endphp
                                @if($transId)
                                <form method="POST" action="{{ route('user.bookings.unbook', $transId) }}" onsubmit="return confirm('Batalkan booking?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-full py-3 bg-blue-500 hover:bg-blue-400 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all active:scale-[0.98]">
                                        Batalkan Booking
                                    </button>
                                </form>
                                @endif
                            @else
                            <button disabled class="w-full py-3 text-slate-600 text-[10px] font-black uppercase tracking-widest rounded-xl border cursor-not-allowed" style="background:rgba(255,255,255,0.03);border-color:rgba(255,255,255,0.06);">
                                Tidak Tersedia
                            </button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-20 text-center rounded-2xl border border-dashed" style="border-color:rgba(255,255,255,0.08);">
                        <p class="text-[10px] text-slate-600 font-black uppercase tracking-[0.3em]">Belum ada area parkir</p>
                    </div>
                    @endforelse
                </div>

                {{-- Interactive map --}}
                @if(isset($map) && $map->map_image_url)
                <div class="rounded-2xl border overflow-hidden" style="background:#0d1526;border-color:rgba(255,255,255,0.07);">
                    <div class="px-6 py-4 border-b flex items-center justify-between" style="border-color:rgba(255,255,255,0.05);">
                        <div>
                            <p class="text-[10px] font-black text-white uppercase tracking-widest">Peta Slot Interaktif</p>
                            <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">Klik slot untuk booking instan</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span><span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Kosong</span></div>
                            <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500"></span><span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Terisi</span></div>
                        </div>
                    </div>
                    <div class="p-0 sm:p-5">
                        <div class="relative h-[400px] sm:h-[500px] lg:h-[600px] rounded-2xl overflow-hidden border bg-[#020617]" style="border-color:rgba(255,255,255,0.06);">
                            <div id="parking-map" class="w-full h-full relative z-10"
                                 data-image-url="{{ $map->map_image_url }}"
                                 data-width="{{ $map->map_width }}"
                                 data-height="{{ $map->map_height }}"
                                 data-map-id="{{ $map->id_area }}"
                                 data-book-url-template="{{ route('user.bookings.book', 'AREA_ID_PLACEHOLDER') }}"
                                 data-unbook-url-template="{{ route('user.bookings.unbook', 'TRANS_ID_PLACEHOLDER') }}"
                                 data-csrf-token="{{ csrf_token() }}">
                            </div>
                            
                            <!-- Loading Overlay -->
                            <div id="map-loader" class="absolute inset-0 z-30 bg-slate-950/80 backdrop-blur-sm flex flex-col items-center justify-center transition-opacity duration-500">
                                <div class="w-10 h-10 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin mb-4"></div>
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em]">Loading Map Layout...</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <style>
                    /* Fix Leaflet Z-Index and layout */
                    .leaflet-container {
                        background: #020617 !important;
                    }
                    .leaflet-pane {
                        z-index: 2 !important;
                    }
                    .leaflet-control-container .leaflet-top,
                    .leaflet-control-container .leaflet-bottom {
                        z-index: 3 !important;
                    }
                    .leaflet-popup-pane {
                        z-index: 4 !important;
                    }
                    .modern-popup .leaflet-popup-content-wrapper {
                        background: #0f172a !important;
                        color: #f8fafc !important;
                        border: 1px solid rgba(255,255,255,0.1);
                        border-radius: 12px;
                        padding: 0;
                        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
                    }
                    .modern-popup .leaflet-popup-tip {
                        background: #0f172a !important;
                    }
                    .modern-popup .leaflet-popup-content {
                        margin: 0 !important;
                        width: auto !important;
                    }
                </style>
                
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                <script src="{{ asset('js/parking-map.js') }}" defer></script>
                <script>
                    // Add a small script to hide loader after parking-map.js initializes
                    document.addEventListener('DOMContentLoaded', function() {
                        const checkMap = setInterval(() => {
                            const loader = document.getElementById('map-loader');
                            if (window.L && document.querySelector('.leaflet-container')) {
                                if (loader) {
                                    loader.style.opacity = '0';
                                    setTimeout(() => loader.remove(), 500);
                                }
                                clearInterval(checkMap);
                            }
                        }, 500);
                        // Fallback
                        setTimeout(() => {
                            const loader = document.getElementById('map-loader');
                            if (loader) {
                                loader.style.opacity = '0';
                                setTimeout(() => loader.remove(), 500);
                            }
                        }, 3000);
                    });
                </script>
                @else
                <div class="rounded-2xl border overflow-hidden p-10 text-center" style="background:#0d1526;border-color:rgba(255,255,255,0.07);">
                    <p class="text-sm font-bold text-white mb-2">Peta area belum tersedia</p>
                    <p class="text-xs text-slate-500">Admin perlu mengunggah gambar blueprint agar peta interaktif bisa ditampilkan.</p>
                </div>
                @endif

            </div>{{-- /main --}}
        </div>
    </div>
</div>

<style>@keyframes shimmer { 100% { transform: translateX(100%); } }</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kendaraanSelect = document.getElementById('booking_kendaraan_id');
    const tarifSelect     = document.getElementById('booking_tarif_id');
    const daerahSelect    = document.getElementById('filter_daerah');
    const areaSelect      = document.getElementById('filter_area');
    const bookingsBaseUrl = @json(route('user.bookings'));

    function goToBookings(areaId, daerah) {
        let url = bookingsBaseUrl.replace(/\/$/, '');
        if (areaId) {
            url += '/' + encodeURIComponent(areaId);
        }
        if (daerah) {
            url += '?daerah=' + encodeURIComponent(daerah);
        }
        window.location.href = url;
    }

    function autoTarifFromJenis() {
        if (!kendaraanSelect || !tarifSelect) return;
        if (tarifSelect.value) return;
        const opt   = kendaraanSelect.options[kendaraanSelect.selectedIndex];
        const jenis = opt?.dataset?.jenis;
        if (!jenis) return;
        for (const tOpt of tarifSelect.options) {
            if (tOpt.dataset && tOpt.dataset.jenis === jenis) { tarifSelect.value = tOpt.value; break; }
        }
    }

    if (kendaraanSelect) kendaraanSelect.addEventListener('change', autoTarifFromJenis);
    if (daerahSelect) {
        daerahSelect.addEventListener('change', function () {
            // Saat ganti zona, reset pemilihan area agar backend memilih area yang valid dalam zona itu.
            goToBookings('', daerahSelect.value);
        });
    }
    if (areaSelect) {
        areaSelect.addEventListener('change', function () {
            goToBookings(areaSelect.value, daerahSelect ? daerahSelect.value : '');
        });
    }

    document.querySelectorAll('form.booking-area-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const kendaraanId = kendaraanSelect ? kendaraanSelect.value : '';
            if (!kendaraanId) {
                e.preventDefault();
                Swal.fire({ icon:'warning', title:'Pilih Kendaraan', text:'Silakan pilih kendaraan Anda sebelum booking.', background:'#0f172a', color:'#fff', confirmButtonColor:'#10b981' });
                return;
            }
            const ki = form.querySelector('input[name="id_kendaraan"]');
            const ti = form.querySelector('input[name="id_tarif"]');
            if (ki) ki.value = kendaraanId;
            if (ti) ti.value = tarifSelect ? tarifSelect.value : '';
        });
    });
});
</script>
@endpush
@endsection
