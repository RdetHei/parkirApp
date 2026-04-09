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
    $activeBookingInfo = $activeBookingInfo ?? null;
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

                {{-- Booking aktif saya --}}
                @if($activeBookingInfo)
                <div id="active-booking-card" class="rounded-2xl border overflow-hidden" style="background:#0d1526;border-color:rgba(59,130,246,0.22);">
                    <div class="px-5 py-3.5 border-b" style="border-color:rgba(255,255,255,0.05);">
                        <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest">Booking Aktif Saya</p>
                    </div>
                    <div class="p-5 flex flex-col gap-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-[10px] font-black text-white uppercase tracking-widest">{{ $activeBookingInfo['area_name'] ?? '-' }}</p>
                                <p class="text-[10px] text-slate-400 font-semibold mt-1">Slot: {{ $activeBookingInfo['slot_code'] ?? '-' }}</p>
                            </div>
                            <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest bg-blue-500/10 text-blue-400 border border-blue-500/20">Aktif</span>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-white/5 px-3 py-2">
                            <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">Sisa Waktu Booking</p>
                            <p id="booking-countdown" class="text-sm text-white font-black mt-1">--:--</p>
                        </div>
                        <form method="POST" action="{{ route('user.bookings.unbook', $activeBookingInfo['id']) }}" onsubmit="return confirm('Batalkan booking aktif Anda sekarang?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-2.5 rounded-xl bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/20 text-[10px] font-black uppercase tracking-widest transition-all">
                                Batalkan Booking Aktif
                            </button>
                        </form>
                    </div>
                </div>
                @endif

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

                {{-- Daftar area: kompak + scroll (banyak area tidak memenuhi layar) --}}
                <div class="rounded-2xl border overflow-hidden" style="background:#0d1526;border-color:rgba(255,255,255,0.07);">
                    <div class="px-4 sm:px-5 py-4 border-b flex flex-col sm:flex-row sm:items-center gap-3 justify-between" style="border-color:rgba(255,255,255,0.05);">
                        <div>
                            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Semua Area</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $areas->count() }} lokasi — klik nama untuk buka peta, atau pakai dropdown di panel kiri</p>
                        </div>
                        <div class="relative w-full sm:w-56 shrink-0">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-[10px] text-slate-500 pointer-events-none"></i>
                            <input type="search" id="area-list-search" autocomplete="off" placeholder="Cari nama / daerah…"
                                   class="w-full rounded-xl border pl-9 pr-3 py-2.5 text-xs text-white placeholder:text-slate-600 focus:border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/10 focus:outline-none font-bold"
                                   style="background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.08);">
                        </div>
                    </div>

                    @if($areas->isEmpty())
                    <div class="py-16 text-center">
                        <p class="text-[10px] text-slate-600 font-black uppercase tracking-[0.3em]">Belum ada area parkir</p>
                    </div>
                    @else
                    <div id="area-list-scroll" class="max-h-[min(52vh,26rem)] overflow-y-auto overscroll-contain">
                        <ul class="divide-y" style="border-color:rgba(255,255,255,0.06);">
                            @foreach($areas as $area)
                            @php
                                $status      = $statusPerArea[$area->id_area] ?? 'empty';
                                $isAvailable = $status === 'empty';
                                $isMine      = $status === 'bookmarked-by-me';
                                $isSelected  = (string) $selectedAreaId === (string) $area->id_area;
                                $pct         = $area->kapasitas > 0 ? min(100, (int) round($area->terisi / $area->kapasitas * 100)) : 0;
                                $searchBlob  = strtolower(($area->nama_area ?? '') . ' ' . ($area->daerah ?? ''));
                            @endphp
                            <li class="area-list-row transition-colors hover:bg-white/3 {{ $isSelected ? 'bg-emerald-500/6' : '' }} {{ !$isAvailable && !$isMine ? 'opacity-70' : '' }}"
                                data-area-search="{{ e($searchBlob) }}"
                                data-area-id="{{ $area->id_area }}"
                                data-selected="{{ $isSelected ? '1' : '0' }}">
                                <div class="px-4 sm:px-5 py-3 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">

                                    <button type="button"
                                            class="area-row-open text-left min-w-0 flex-1 flex items-start gap-3 rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40 -m-1 p-1"
                                            title="Buka peta area ini">
                                        <span class="mt-1 w-2 h-2 rounded-full shrink-0 {{ $isAvailable ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : ($isMine ? 'bg-blue-500' : 'bg-slate-600') }}"></span>
                                        <span class="min-w-0">
                                            <span class="flex flex-wrap items-center gap-2">
                                                <span class="text-sm font-black text-white truncate tracking-tight">{{ $area->nama_area }}</span>
                                                @if($isSelected)
                                                <span class="text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded-md bg-emerald-500/15 text-emerald-400 border border-emerald-500/25">Peta aktif</span>
                                                @endif
                                            </span>
                                            <span class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-1">
                                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $area->daerah ?? '—' }}</span>
                                                <span class="text-slate-700 hidden sm:inline">·</span>
                                                <span class="text-[10px] font-black uppercase tracking-widest
                                                    {{ $isAvailable ? 'text-emerald-400' : ($isMine ? 'text-blue-400' : 'text-slate-500') }}">
                                                    {{ $isAvailable ? 'Tersedia' : ($isMine ? 'Milik Anda' : 'Terisi / Direservasi') }}
                                                </span>
                                            </span>
                                            <span class="flex items-center gap-2 mt-2 max-w-md">
                                                <span class="text-[9px] font-bold text-slate-600 tabular-nums">{{ $area->terisi }}/{{ $area->kapasitas }}</span>
                                                <span class="flex-1 h-1 rounded-full overflow-hidden min-w-16 max-w-28" style="background:rgba(255,255,255,0.06);">
                                                    <span class="h-full rounded-full block {{ $isAvailable ? 'bg-emerald-500' : ($isMine ? 'bg-blue-500' : 'bg-slate-600') }}" style="width:{{ $pct }}%"></span>
                                                </span>
                                                <span class="text-[9px] text-slate-600 tabular-nums w-8">{{ $pct }}%</span>
                                            </span>
                                        </span>
                                    </button>

                                    <div class="shrink-0 flex sm:flex-col sm:items-end gap-2 sm:min-w-36">
                                        @if($isAvailable)
                                        <form method="POST" action="{{ route('user.bookings.book', $area->id_area) }}" class="booking-area-form w-full sm:w-auto">
                                            @csrf
                                            <input type="hidden" name="id_kendaraan" value="">
                                            <input type="hidden" name="id_tarif" value="">
                                            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-[9px] font-black uppercase tracking-widest rounded-xl transition-all active:scale-[0.98]">
                                                Booking
                                            </button>
                                        </form>
                                        @elseif($isMine)
                                            @php $transId = $myBookingIds[$area->id_area] ?? null; @endphp
                                            @if($transId)
                                            <form method="POST" action="{{ route('user.bookings.unbook', $transId) }}" class="w-full sm:w-auto" onsubmit="return confirm('Batalkan booking?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-blue-500 hover:bg-blue-400 text-white text-[9px] font-black uppercase tracking-widest rounded-xl transition-all active:scale-[0.98]">
                                                    Batal
                                                </button>
                                            </form>
                                            @endif
                                        @else
                                        <span class="inline-flex px-3 py-2 text-[9px] font-black uppercase tracking-widest text-slate-600 border rounded-xl cursor-default" style="background:rgba(255,255,255,0.03);border-color:rgba(255,255,255,0.06);">
                                            Penuh
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                {{-- Interactive map --}}
                @if($map && $map->map_image)
                <div class="mt-8 animate-fade-in" style="animation-delay: 0.2s">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                            <h3 class="text-sm font-black text-white uppercase tracking-widest">Pilih Slot di {{ $map->nama_area }}</h3>
                        </div>
                        <div class="flex items-center gap-3">
                            <div id="util-pct" class="text-[10px] font-black text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">0%</div>
                            <button onclick="if(window.refreshParkingMap) window.refreshParkingMap()" class="p-2 bg-white/5 border border-white/5 rounded-xl text-slate-400 hover:text-white transition-all active:scale-95" title="Refresh Map">
                                <i class="fa-solid fa-arrows-rotate text-[10px]"></i>
                            </button>
                        </div>
                    </div>

                    <div class="relative rounded-3xl overflow-hidden border border-white/5 bg-slate-950 shadow-2xl" style="height: 600px; min-height: 400px;">
                        <div id="parking-map"
                             class="w-full h-full"
                             style="z-index: 10;"
                             data-image-url="{{ $map->map_image_url }}"
                             data-width="{{ $map->map_width ?: 1000 }}"
                             data-height="{{ $map->map_height ?: 800 }}"
                             data-map-id="{{ $map->id_area }}"
                             data-book-url-template="{{ route('user.bookings.book', 'AREA_ID_PLACEHOLDER') }}"
                             data-unbook-url-template="{{ route('user.bookings.unbook', 'TRANS_ID_PLACEHOLDER') }}"
                             data-csrf-token="{{ csrf_token() }}">
                        </div>

                        <div id="map-loader" class="absolute inset-0 z-50 bg-slate-950 flex flex-col items-center justify-center transition-opacity duration-500">
                            <div class="w-10 h-10 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin mb-4"></div>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Memuat Peta...</p>
                        </div>

                        {{-- Summary overlay --}}
                        <div class="absolute bottom-6 right-6 z-20 bg-slate-900/90 backdrop-blur-xl border border-white/10 rounded-2xl p-4 min-w-[140px] shadow-2xl pointer-events-none">
                            <div id="parking-map-summary" class="flex flex-col gap-2">
                                {{-- JS will inject here --}}
                            </div>
                            <div class="mt-3 pt-3 border-t border-white/5">
                                <div class="w-full h-1 bg-slate-800 rounded-full overflow-hidden">
                                    <div id="util-bar" class="h-full bg-emerald-500 transition-all duration-500" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>{{-- /main --}}
        </div>
    </div>
</div>

<style>@keyframes shimmer { 100% { transform: translateX(100%); } }</style>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .leaflet-container { background: #020617 !important; outline: none; }
    .modern-popup .leaflet-popup-content-wrapper { background: #0f172a; color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 0; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
    .modern-popup .leaflet-popup-tip { background: #0f172a; border: 1px solid rgba(255,255,255,0.1); }
    .modern-popup .leaflet-popup-content { margin: 0; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="{{ asset('js/parking-map.js') }}"></script>
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

    const areaSearch = document.getElementById('area-list-search');
    if (areaSearch) {
        areaSearch.addEventListener('input', function () {
            const q = (areaSearch.value || '').trim().toLowerCase();
            document.querySelectorAll('.area-list-row').forEach(function (row) {
                const blob = (row.dataset.areaSearch || '').toLowerCase();
                row.classList.toggle('hidden', q.length > 0 && blob.indexOf(q) === -1);
            });
        });
    }

    document.querySelectorAll('.area-row-open').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const li = btn.closest('.area-list-row');
            const id = li && li.dataset ? li.dataset.areaId : '';
            if (!id) return;
            goToBookings(id, daerahSelect ? daerahSelect.value : '');
        });
    });

    var activeAreaRow = document.querySelector('.area-list-row[data-selected="1"]');
    var areaListEl = document.getElementById('area-list-scroll');
    if (activeAreaRow && areaListEl) {
        activeAreaRow.scrollIntoView({ block: 'nearest' });
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

    const bookingCountdown = document.getElementById('booking-countdown');
    const bookingExpiresAt = @json($activeBookingInfo['expires_at'] ?? null);
    const activeBookingCard = document.getElementById('active-booking-card');
    if (bookingCountdown && bookingExpiresAt) {
        const expires = new Date(bookingExpiresAt).getTime();
        const tick = () => {
            const now = Date.now();
            let remain = Math.max(0, Math.floor((expires - now) / 1000));
            const mm = String(Math.floor(remain / 60)).padStart(2, '0');
            const ss = String(remain % 60).padStart(2, '0');
            bookingCountdown.textContent = `${mm}:${ss}`;
            if (remain <= 0) {
                bookingCountdown.textContent = '00:00';
                if (activeBookingCard) activeBookingCard.classList.add('opacity-60');
                clearInterval(timerCountdown);
            }
        };
        tick();
        var timerCountdown = setInterval(tick, 1000);
    }
});
</script>
@endpush
@endsection
