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

<div class="relative z-10" style="background:#020617;min-height:100vh;">

    {{-- Ambient bg --}}
    <div class="fixed top-0 left-1/4 w-[600px] h-[400px] bg-emerald-500/4 rounded-full blur-[160px] pointer-events-none z-0"></div>
    <div class="fixed bottom-0 right-0 w-[400px] h-[400px] bg-blue-600/3 rounded-full blur-[140px] pointer-events-none z-0"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-10">

        {{-- ── HEADER ── --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border mb-3"
                     style="background:rgba(16,185,129,0.08);border-color:rgba(16,185,129,0.18);">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[9px] font-black text-emerald-500 uppercase tracking-[0.25em]">Smart Reservation</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-white">
                    Booking <span class="text-emerald-500">Slot</span>
                </h1>
                <p class="text-slate-500 text-xs mt-1.5 font-medium">Reservasi slot parkir secara instan dan real-time.</p>
            </div>
            <a href="{{ route('user.dashboard') }}"
               class="self-start sm:self-auto inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-white hover:border-white/15 transition-all active:scale-95"
               style="background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.08);">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="mb-5 flex items-center gap-3 px-4 py-3.5 rounded-2xl border"
             style="background:rgba(16,185,129,0.08);border-color:rgba(16,185,129,0.2);">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-xs font-bold text-emerald-400">{{ session('success') }}</p>
        </div>
        @endif
        @if(session('error'))
        <div class="mb-5 flex items-center gap-3 px-4 py-3.5 rounded-2xl border"
             style="background:rgba(239,68,68,0.08);border-color:rgba(239,68,68,0.2);">
            <svg class="w-4 h-4 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <p class="text-xs font-bold text-rose-400">{{ session('error') }}</p>
        </div>
        @endif

        {{-- ── MAIN GRID ── --}}
        <div class="grid grid-cols-1 xl:grid-cols-[260px_1fr] gap-6 items-start">

            {{-- ────────── SIDEBAR ────────── --}}
            <div class="flex flex-col gap-4 xl:sticky xl:top-6">

                {{-- Konfigurasi --}}
                <div class="rounded-2xl border overflow-hidden" style="background:#0b1221;border-color:rgba(255,255,255,0.07);">
                    <div class="px-5 py-3.5 border-b flex items-center gap-2" style="border-color:rgba(255,255,255,0.05);">
                        <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Konfigurasi</p>
                    </div>
                    <div class="p-4 flex flex-col gap-4">
                        <div>
                            <label class="block text-[9px] font-black text-slate-600 uppercase tracking-widest mb-1.5">Daerah</label>
                            <select id="filter_daerah"
                                    class="w-full rounded-xl border px-3 py-2.5 text-xs text-white focus:border-emerald-500/50 focus:outline-none transition-all font-semibold"
                                    style="background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.08);">
                                <option value="" class="bg-slate-900">— Semua Daerah —</option>
                                @foreach($daerahs as $d)
                                <option value="{{ $d }}" {{ $selectedDaerah == $d ? 'selected' : '' }} class="bg-slate-900">{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-600 uppercase tracking-widest mb-1.5">Peta Area</label>
                            <select id="filter_area"
                                    class="w-full rounded-xl border px-3 py-2.5 text-xs text-white focus:border-emerald-500/50 focus:outline-none transition-all font-semibold"
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
                            <label class="block text-[9px] font-black text-slate-600 uppercase tracking-widest mb-1.5">Kendaraan</label>
                            <select id="booking_kendaraan_id"
                                    class="w-full rounded-xl border px-3 py-2.5 text-xs text-white focus:border-emerald-500/50 focus:outline-none transition-all font-semibold"
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
                            <label class="block text-[9px] font-black text-slate-600 uppercase tracking-widest mb-1.5">Tarif</label>
                            <select id="booking_tarif_id"
                                    class="w-full rounded-xl border px-3 py-2.5 text-xs text-white focus:border-emerald-500/50 focus:outline-none transition-all font-semibold"
                                    style="background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.08);">
                                <option value="" class="bg-slate-900">— Otomatis —</option>
                                @foreach($tarifs as $t)
                                <option value="{{ $t->id_tarif }}" data-jenis="{{ $t->jenis_kendaraan }}" class="bg-slate-900">
                                    {{ strtoupper($t->jenis_kendaraan) }} · Rp {{ number_format($t->tarif_perjam, 0, ',', '.') }}/jam
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Booking aktif --}}
                @if($activeBookingInfo)
                <div id="active-booking-card" class="rounded-2xl border overflow-hidden" style="background:#0b1221;border-color:rgba(59,130,246,0.25);">
                    <div class="px-5 py-3.5 border-b flex items-center justify-between" style="border-color:rgba(59,130,246,0.12);">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                            <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest">Booking Aktif</p>
                        </div>
                        <span class="text-[8px] font-black text-blue-400 bg-blue-500/10 border border-blue-500/20 px-2 py-0.5 rounded-full uppercase tracking-widest">Live</span>
                    </div>
                    <div class="p-4 flex flex-col gap-3">
                        <div>
                            <p class="text-xs font-black text-white">{{ $activeBookingInfo['area_name'] ?? '-' }}</p>
                            <p class="text-[10px] text-slate-500 mt-0.5 font-semibold">Slot {{ $activeBookingInfo['slot_code'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-xl border px-3 py-2.5 flex items-center justify-between"
                             style="background:rgba(59,130,246,0.06);border-color:rgba(59,130,246,0.15);">
                            <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">Sisa waktu</p>
                            <p id="booking-countdown" class="text-sm text-blue-300 font-black tabular-nums">--:--</p>
                        </div>
                        <form method="POST" action="{{ route('user.bookings.unbook', $activeBookingInfo['id']) }}"
                              onsubmit="return confirm('Batalkan booking aktif Anda sekarang?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all border hover:bg-rose-500 hover:text-white hover:border-rose-500"
                                    style="background:rgba(239,68,68,0.07);color:#f87171;border-color:rgba(239,68,68,0.2);">
                                Batalkan Booking
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                {{-- Ringkasan --}}
                @php
                    $totalArea = $areas->count();
                    $tersedia  = $areas->filter(fn($a) => ($statusPerArea[$a->id_area] ?? '') === 'empty')->count();
                    $milikku   = $areas->filter(fn($a) => ($statusPerArea[$a->id_area] ?? '') === 'bookmarked-by-me')->count();
                    $penuh     = $totalArea - $tersedia - $milikku;
                @endphp
                <div class="rounded-2xl border overflow-hidden" style="background:#0b1221;border-color:rgba(255,255,255,0.07);">
                    <div class="px-5 py-3.5 border-b" style="border-color:rgba(255,255,255,0.05);">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Ringkasan</p>
                    </div>
                    <div class="p-4 grid grid-cols-2 gap-3">
                        <div class="rounded-xl border p-3 text-center" style="background:rgba(255,255,255,0.02);border-color:rgba(255,255,255,0.06);">
                            <p class="text-xl font-black text-white">{{ $totalArea }}</p>
                            <p class="text-[9px] text-slate-600 uppercase tracking-widest mt-0.5">Total</p>
                        </div>
                        <div class="rounded-xl border p-3 text-center" style="background:rgba(16,185,129,0.05);border-color:rgba(16,185,129,0.12);">
                            <p class="text-xl font-black text-emerald-400">{{ $tersedia }}</p>
                            <p class="text-[9px] text-emerald-700 uppercase tracking-widest mt-0.5">Tersedia</p>
                        </div>
                        <div class="rounded-xl border p-3 text-center" style="background:rgba(59,130,246,0.05);border-color:rgba(59,130,246,0.12);">
                            <p class="text-xl font-black text-blue-400">{{ $milikku }}</p>
                            <p class="text-[9px] text-blue-800 uppercase tracking-widest mt-0.5">Milik Saya</p>
                        </div>
                        <div class="rounded-xl border p-3 text-center" style="background:rgba(255,255,255,0.02);border-color:rgba(255,255,255,0.06);">
                            <p class="text-xl font-black text-slate-500">{{ $penuh }}</p>
                            <p class="text-[9px] text-slate-600 uppercase tracking-widest mt-0.5">Penuh</p>
                        </div>
                    </div>
                    {{-- utilization bar --}}
                    @if($totalArea > 0)
                    <div class="px-4 pb-4">
                        @php $utilPct = round(($penuh / $totalArea) * 100); @endphp
                        <div class="flex items-center justify-between mb-1.5">
                            <p class="text-[9px] text-slate-600 uppercase tracking-widest">Utilisasi</p>
                            <p class="text-[9px] font-black text-slate-400">{{ $utilPct }}%</p>
                        </div>
                        <div class="h-1.5 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.05);">
                            <div class="h-full rounded-full transition-all duration-700
                                {{ $utilPct > 80 ? 'bg-rose-500' : ($utilPct > 50 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                 style="width:{{ $utilPct }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Legend --}}
                <div class="rounded-2xl border p-4" style="background:#0b1221;border-color:rgba(255,255,255,0.07);">
                    <p class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-3">Keterangan</p>
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_6px_rgba(16,185,129,0.6)]"></span>
                            <span class="text-[10px] text-slate-400 font-semibold">Tersedia</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="w-2 h-2 rounded-full bg-blue-500 shadow-[0_0_6px_rgba(59,130,246,0.6)]"></span>
                            <span class="text-[10px] text-slate-400 font-semibold">Milik Anda</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="w-2 h-2 rounded-full bg-slate-600"></span>
                            <span class="text-[10px] text-slate-500 font-semibold">Tidak Tersedia</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ────────── MAIN CONTENT ────────── --}}
            <div class="flex flex-col gap-6 min-w-0">

                {{-- Area Grid Card --}}
                <div class="rounded-2xl border overflow-hidden" style="background:#0b1221;border-color:rgba(255,255,255,0.07);">

                    {{-- Header + search + filter tab --}}
                    <div class="px-5 py-4 border-b" style="border-color:rgba(255,255,255,0.05);">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
                            <div>
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Semua Area Parkir</p>
                                <p class="text-[10px] text-slate-600 mt-0.5">{{ $areas->count() }} lokasi terdaftar</p>
                            </div>
                            <div class="flex items-center gap-2 flex-wrap">
                                {{-- Filter tab pills --}}
                                <div class="flex rounded-xl overflow-hidden border text-[9px] font-black uppercase tracking-widest"
                                     style="border-color:rgba(255,255,255,0.08);">
                                    <button id="tab-all" onclick="filterAreaTab('all')"
                                            class="area-tab-btn px-3 py-2 transition-all active-tab">
                                        Semua <span class="ml-1 opacity-60">{{ $totalArea }}</span>
                                    </button>
                                    <button id="tab-available" onclick="filterAreaTab('available')"
                                            class="area-tab-btn px-3 py-2 transition-all border-l" style="border-color:rgba(255,255,255,0.08);">
                                        Tersedia <span class="ml-1 opacity-60">{{ $tersedia }}</span>
                                    </button>
                                    <button id="tab-mine" onclick="filterAreaTab('mine')"
                                            class="area-tab-btn px-3 py-2 transition-all border-l" style="border-color:rgba(255,255,255,0.08);">
                                        Saya <span class="ml-1 opacity-60">{{ $milikku }}</span>
                                    </button>
                                </div>
                                {{-- Search --}}
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3 h-3 text-slate-600 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <input type="search" id="area-list-search" placeholder="Cari area…"
                                           class="w-44 rounded-xl border pl-8 pr-3 py-2 text-xs text-white placeholder:text-slate-700 focus:border-emerald-500/40 focus:outline-none font-semibold"
                                           style="background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.08);">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Grid Area --}}
                    @if($areas->isEmpty())
                    <div class="py-20 text-center">
                        <div class="w-12 h-12 rounded-2xl border mx-auto mb-4 flex items-center justify-center"
                             style="background:rgba(255,255,255,0.03);border-color:rgba(255,255,255,0.07);">
                            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                        </div>
                        <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Belum ada area parkir</p>
                    </div>
                    @else
                    <div id="area-grid" class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($areas as $area)
                        @php
                            $status      = $statusPerArea[$area->id_area] ?? 'empty';
                            $isAvailable = $status === 'empty';
                            $isMine      = $status === 'bookmarked-by-me';
                            $isSelected  = (string) $selectedAreaId === (string) $area->id_area;
                            $pct         = $area->kapasitas > 0 ? min(100, (int) round($area->terisi / $area->kapasitas * 100)) : 0;
                            $searchBlob  = strtolower(($area->nama_area ?? '') . ' ' . ($area->daerah ?? ''));
                            $tabAttr     = $isAvailable ? 'available' : ($isMine ? 'mine' : 'full');
                        @endphp
                        <div class="area-card group relative rounded-2xl border transition-all duration-200 overflow-hidden cursor-pointer
                                    {{ $isSelected ? 'ring-1 ring-emerald-500/40' : '' }}"
                             style="background:rgba(255,255,255,0.025);border-color:{{ $isAvailable ? 'rgba(16,185,129,0.15)' : ($isMine ? 'rgba(59,130,246,0.2)' : 'rgba(255,255,255,0.05)') }};"
                             data-area-search="{{ e($searchBlob) }}"
                             data-area-id="{{ $area->id_area }}"
                             data-selected="{{ $isSelected ? '1' : '0' }}"
                             data-status="{{ $tabAttr }}">

                            {{-- Top accent line --}}
                            <div class="absolute top-0 left-0 right-0 h-px
                                {{ $isAvailable ? 'bg-emerald-500/40' : ($isMine ? 'bg-blue-500/40' : 'bg-white/5') }}"></div>

                            <div class="p-4">
                                {{-- Status dot + badge --}}
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full shrink-0 mt-0.5
                                            {{ $isAvailable ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : ($isMine ? 'bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.5)]' : 'bg-slate-700') }}"></span>
                                        <span class="text-[8px] font-black uppercase tracking-[0.2em]
                                            {{ $isAvailable ? 'text-emerald-500' : ($isMine ? 'text-blue-400' : 'text-slate-600') }}">
                                            {{ $isAvailable ? 'Tersedia' : ($isMine ? 'Milik Anda' : 'Penuh') }}
                                        </span>
                                    </div>
                                    @if($isSelected)
                                    <span class="text-[7px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded-md"
                                          style="background:rgba(16,185,129,0.12);color:#34d399;border:1px solid rgba(16,185,129,0.2);">Peta Aktif</span>
                                    @endif
                                </div>

                                {{-- Nama area --}}
                                <button type="button" class="area-row-open text-left w-full focus:outline-none group/name mb-3">
                                    <h3 class="text-sm font-black text-white group-hover/name:text-emerald-400 transition-colors leading-tight line-clamp-2">
                                        {{ $area->nama_area }}
                                    </h3>
                                    <p class="text-[9px] text-slate-600 uppercase tracking-widest font-bold mt-1">
                                        {{ $area->daerah ?? '—' }}
                                    </p>
                                </button>

                                {{-- Capacity bar --}}
                                <div class="mb-3">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-[9px] text-slate-600 font-bold">Kapasitas</span>
                                        <span class="text-[9px] font-black tabular-nums"
                                              style="color:{{ $isAvailable ? '#34d399' : ($isMine ? '#60a5fa' : '#475569') }}">
                                            {{ $area->terisi }}/{{ $area->kapasitas }}
                                        </span>
                                    </div>
                                    <div class="h-1 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.05);">
                                        <div class="h-full rounded-full transition-all duration-500
                                            {{ $isAvailable ? 'bg-emerald-500' : ($isMine ? 'bg-blue-500' : 'bg-slate-600') }}"
                                             style="width:{{ $pct }}%"></div>
                                    </div>
                                </div>

                                {{-- Action button --}}
                                @if($isAvailable)
                                <form method="POST" action="{{ route('user.bookings.book', $area->id_area) }}" class="booking-area-form">
                                    @csrf
                                    <input type="hidden" name="id_kendaraan" value="">
                                    <input type="hidden" name="id_tarif" value="">
                                    <button type="submit"
                                            class="w-full py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all active:scale-[0.97] hover:shadow-[0_0_16px_rgba(16,185,129,0.25)]"
                                            style="background:rgba(16,185,129,0.15);color:#34d399;border:1px solid rgba(16,185,129,0.25);"
                                            onmouseover="this.style.background='rgba(16,185,129,0.25)'"
                                            onmouseout="this.style.background='rgba(16,185,129,0.15)'">
                                        Booking Sekarang
                                    </button>
                                </form>
                                @elseif($isMine)
                                    @php $transId = $myBookingIds[$area->id_area] ?? null; @endphp
                                    @if($transId)
                                    <form method="POST" action="{{ route('user.bookings.unbook', $transId) }}"
                                          onsubmit="return confirm('Batalkan booking?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="w-full py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all active:scale-[0.97]"
                                                style="background:rgba(59,130,246,0.12);color:#60a5fa;border:1px solid rgba(59,130,246,0.2);"
                                                onmouseover="this.style.background='rgba(59,130,246,0.22)'"
                                                onmouseout="this.style.background='rgba(59,130,246,0.12)'">
                                            Batalkan
                                        </button>
                                    </form>
                                    @endif
                                @else
                                <div class="w-full py-2 rounded-xl text-[9px] font-black uppercase tracking-widest text-center cursor-not-allowed"
                                     style="background:rgba(255,255,255,0.03);color:#334155;border:1px solid rgba(255,255,255,0.05);">
                                    Tidak Tersedia
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Empty state for filter --}}
                    <div id="no-area-result" class="hidden py-12 text-center px-5">
                        <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Tidak ada area yang cocok</p>
                        <p class="text-xs text-slate-700 mt-1">Coba kata kunci atau filter lain</p>
                    </div>

                    @endif
                </div>

                {{-- ── Interactive Map ── --}}
                @if($map && $map->map_image)
                <div class="rounded-2xl border overflow-hidden" style="background:#0b1221;border-color:rgba(255,255,255,0.07);">
                    <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:rgba(255,255,255,0.05);">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                            <div>
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Peta Interaktif</p>
                                <p class="text-xs font-black text-white">{{ $map->nama_area }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div id="util-pct" class="text-[9px] font-black text-emerald-500 bg-emerald-500/10 px-2.5 py-1 rounded-full border border-emerald-500/15 tabular-nums">0%</div>
                            <button onclick="if(window.refreshParkingMap) window.refreshParkingMap()"
                                    class="p-2 rounded-xl border text-slate-500 hover:text-white transition-all active:scale-95"
                                    style="background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.08);"
                                    title="Refresh">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="relative" style="height:580px;">
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

                        <div id="map-loader" class="absolute inset-0 z-50 flex flex-col items-center justify-center transition-opacity duration-500"
                             style="background:#020617;">
                            <div class="w-10 h-10 border-4 border-emerald-500/15 border-t-emerald-500 rounded-full animate-spin mb-4"></div>
                            <p class="text-[9px] font-black text-slate-600 uppercase tracking-widest">Memuat Peta...</p>
                        </div>

                        {{-- Summary overlay --}}
                        <div class="absolute bottom-5 right-5 z-20 rounded-2xl border p-4 min-w-[140px] shadow-2xl pointer-events-none"
                             style="background:rgba(7,15,33,0.9);backdrop-filter:blur(16px);border-color:rgba(255,255,255,0.08);">
                            <div id="parking-map-summary" class="flex flex-col gap-2"></div>
                            <div class="mt-3 pt-3 border-t" style="border-color:rgba(255,255,255,0.05);">
                                <div class="w-full h-1 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.06);">
                                    <div id="util-bar" class="h-full bg-emerald-500 transition-all duration-500" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </div>{{-- /main content --}}
        </div>{{-- /grid --}}
    </div>
</div>

<style>
    .area-tab-btn {
        background: rgba(255,255,255,0.03);
        color: #475569;
    }
    .area-tab-btn.active-tab {
        background: rgba(16,185,129,0.12);
        color: #34d399;
    }
    .area-tab-btn:hover:not(.active-tab) {
        background: rgba(255,255,255,0.06);
        color: #94a3b8;
    }
    .area-card:hover {
        background: rgba(255,255,255,0.04) !important;
        transform: translateY(-1px);
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
    .area-card { animation: fadeIn 0.3s ease-out forwards; }
</style>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .leaflet-container { background: #020617 !important; outline: none; }
    .modern-popup .leaflet-popup-content-wrapper { background: #0f172a; color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 0; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
    .modern-popup .leaflet-popup-tip { background: #0f172a; }
    .modern-popup .leaflet-popup-content { margin: 0; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="{{ asset('js/parking-map.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const kendaraanSelect = document.getElementById('booking_kendaraan_id');
    const tarifSelect     = document.getElementById('booking_tarif_id');
    const daerahSelect    = document.getElementById('filter_daerah');
    const areaSelect      = document.getElementById('filter_area');
    const bookingsBaseUrl = @json(route('user.bookings'));

    function goToBookings(areaId, daerah) {
        let url = bookingsBaseUrl.replace(/\/$/, '');
        if (areaId) url += '/' + encodeURIComponent(areaId);
        if (daerah) url += '?daerah=' + encodeURIComponent(daerah);
        window.location.href = url;
    }

    function autoTarifFromJenis() {
        if (!kendaraanSelect || !tarifSelect || tarifSelect.value) return;
        const jenis = kendaraanSelect.options[kendaraanSelect.selectedIndex]?.dataset?.jenis;
        if (!jenis) return;
        for (const opt of tarifSelect.options) {
            if (opt.dataset?.jenis === jenis) { tarifSelect.value = opt.value; break; }
        }
    }

    if (kendaraanSelect) kendaraanSelect.addEventListener('change', autoTarifFromJenis);
    if (daerahSelect) daerahSelect.addEventListener('change', () => goToBookings('', daerahSelect.value));
    if (areaSelect)   areaSelect.addEventListener('change',   () => goToBookings(areaSelect.value, daerahSelect?.value ?? ''));

    // ── Search + Tab Filter ──
    let currentTab = 'all';

    window.filterAreaTab = function(tab) {
        currentTab = tab;
        document.querySelectorAll('.area-tab-btn').forEach(b => b.classList.remove('active-tab'));
        document.getElementById('tab-' + tab)?.classList.add('active-tab');
        applyFilter();
    };

    const areaSearch = document.getElementById('area-list-search');
    if (areaSearch) areaSearch.addEventListener('input', applyFilter);

    function applyFilter() {
        const q = (areaSearch?.value ?? '').trim().toLowerCase();
        let visibleCount = 0;
        document.querySelectorAll('.area-card').forEach(function (card) {
            const blob   = (card.dataset.areaSearch || '').toLowerCase();
            const status = card.dataset.status || '';
            const matchQ   = !q || blob.includes(q);
            const matchTab = currentTab === 'all' ||
                             (currentTab === 'available' && status === 'available') ||
                             (currentTab === 'mine'      && status === 'mine');
            const show = matchQ && matchTab;
            card.classList.toggle('hidden', !show);
            if (show) visibleCount++;
        });
        const noResult = document.getElementById('no-area-result');
        if (noResult) noResult.classList.toggle('hidden', visibleCount > 0);
    }

    // ── Open map on card name click ──
    document.querySelectorAll('.area-row-open').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const card = btn.closest('.area-card');
            const id   = card?.dataset?.areaId ?? '';
            if (!id) return;
            goToBookings(id, daerahSelect?.value ?? '');
        });
    });

    // ── Scroll selected card into view ──
    const selectedCard = document.querySelector('.area-card[data-selected="1"]');
    if (selectedCard) selectedCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    // ── Inject kendaraan + tarif into booking forms ──
    document.querySelectorAll('form.booking-area-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const kendaraanId = kendaraanSelect?.value ?? '';
            if (!kendaraanId) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Kendaraan',
                    text: 'Silakan pilih kendaraan Anda sebelum booking.',
                    background: '#0f172a',
                    color: '#fff',
                    confirmButtonColor: '#10b981'
                });
                return;
            }
            const ki = form.querySelector('input[name="id_kendaraan"]');
            const ti = form.querySelector('input[name="id_tarif"]');
            if (ki) ki.value = kendaraanId;
            if (ti) ti.value = tarifSelect?.value ?? '';
        });
    });

    // ── Active booking countdown ──
    const bookingCountdown = document.getElementById('booking-countdown');
    const bookingExpiresAt = @json($activeBookingInfo['expires_at'] ?? null);
    const activeBookingCard = document.getElementById('active-booking-card');
    if (bookingCountdown && bookingExpiresAt) {
        const expires = new Date(bookingExpiresAt).getTime();
        const tick = () => {
            const remain = Math.max(0, Math.floor((expires - Date.now()) / 1000));
            const mm = String(Math.floor(remain / 60)).padStart(2, '0');
            const ss = String(remain % 60).padStart(2, '0');
            bookingCountdown.textContent = `${mm}:${ss}`;
            if (remain <= 0) {
                if (activeBookingCard) activeBookingCard.style.opacity = '0.5';
                clearInterval(timer);
            }
        };
        tick();
        const timer = setInterval(tick, 1000);
    }
});
</script>
@endpush
@endsection
