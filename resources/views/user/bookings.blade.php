@extends('layouts.app')

@section('title', 'Booking Slot Parkir')

@section('content')
    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto space-y-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">Booking slot</p>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                        Pilih area parkir
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Pilih satu area yang masih kosong untuk dibooking. Booking berlaku singkat dan akan otomatis hangus jika tidak digunakan.
                    </p>
                </div>
                <a href="{{ route('user.dashboard') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali ke dashboard
                </a>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kendaraan untuk booking</label>
                        <select id="booking_kendaraan_id"
                                class="block w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/70">
                            <option value="">— Pilih kendaraan —</option>
                            @foreach($kendaraans as $k)
                                <option value="{{ $k->id_kendaraan }}"
                                        data-jenis="{{ $k->jenis_kendaraan }}">
                                    {{ $k->plat_nomor }} • {{ $k->jenis_kendaraan }}{{ $k->pemilik ? ' • ' . $k->pemilik : '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-[11px] text-gray-500">Booking akan dibuat untuk kendaraan ini (dibutuhkan untuk integritas data transaksi).</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tarif</label>
                        <select id="booking_tarif_id"
                                class="block w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/70">
                            <option value="">— Otomatis dari jenis —</option>
                            @foreach($tarifs as $t)
                                <option value="{{ $t->id_tarif }}" data-jenis="{{ $t->jenis_kendaraan }}">
                                    {{ $t->jenis_kendaraan }} — Rp {{ number_format($t->tarif_perjam, 0, ',', '.') }}/jam
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-[11px] text-gray-500">Jika kosong, otomatis mengikuti jenis kendaraan.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($areas as $area)
                    @php
                        $status = $statusPerArea[$area->id_area] ?? 'empty';
                        $color = $status === 'empty' ? 'bg-emerald-50 border-emerald-200' :
                                  ($status === 'bookmarked-by-me' ? 'bg-blue-50 border-blue-200' :
                                  ($status === 'bookmarked' ? 'bg-yellow-50 border-yellow-200' : 'bg-red-50 border-red-200'));
                        $label = $status === 'empty' ? 'Tersedia' :
                                 ($status === 'bookmarked-by-me' ? 'Sudah Anda booking' :
                                 ($status === 'bookmarked' ? 'Dibooking orang lain' : 'Sedang terisi'));
                    @endphp
                    <div class="rounded-2xl border {{ $color }} p-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <h2 class="text-sm font-semibold text-gray-900">
                                    {{ $area->nama_area }}
                                </h2>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium
                                    {{ $status === 'empty' ? 'bg-emerald-100 text-emerald-800' :
                                       ($status === 'bookmarked-by-me' ? 'bg-blue-100 text-blue-800' :
                                       ($status === 'bookmarked' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                    {{ $label }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500">
                                Kapasitas: {{ $area->kapasitas }} slot • Terisi: {{ $area->terisi }}
                            </p>
                        </div>

                        <div class="mt-4">
                            @if($status === 'empty')
                                <form method="POST" action="{{ route('user.bookings.book', $area->id_area) }}" class="booking-area-form">
                                    @csrf
                                    <input type="hidden" name="id_kendaraan" value="">
                                    <input type="hidden" name="id_tarif" value="">
                                    <button type="submit"
                                            class="w-full inline-flex items-center justify-center rounded-xl bg-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
                                        Booking area ini
                                    </button>
                                </form>
                            @elseif($status === 'bookmarked-by-me')
                                @php
                                    $transId = $myBookingIds[$area->id_area] ?? null;
                                @endphp
                                @if($transId)
                                <form method="POST" action="{{ route('user.bookings.unbook', $transId) }}"
                                      onsubmit="return confirm('Batalkan booking untuk area ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full inline-flex items-center justify-center rounded-xl bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                                        Batalkan booking saya
                                    </button>
                                </form>
                                @else
                                    <button type="button"
                                            class="w-full inline-flex items-center justify-center rounded-xl bg-blue-100 px-3 py-2 text-sm font-semibold text-blue-700 cursor-not-allowed">
                                        Booking aktif
                                    </button>
                                @endif
                            @else
                                <button type="button"
                                        class="w-full inline-flex items-center justify-center rounded-xl bg-gray-200 px-3 py-2 text-sm font-semibold text-gray-500 cursor-not-allowed"
                                        disabled>
                                    Tidak tersedia
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada area parkir yang dikonfigurasi.</p>
                @endforelse
            </div>
        </div>

        @isset($map)
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">Peta slot (klik untuk booking)</h2>
                        <p class="text-xs text-gray-500">Hijau: kosong, merah: terisi, oranye: dibooking orang lain, biru: booking milik Anda.</p>
                    </div>
                </div>
                <div class="p-4">
                    <div id="parking-map"
                         class="w-full rounded-lg border border-gray-200"
                         style="height: 520px; background-color: #f3f4f6;"
                         data-image-url="{{ asset($map->image_path) }}"
                         data-width="{{ $map->width }}"
                         data-height="{{ $map->height }}"
                         data-map-id="{{ $map->id }}"
                         data-book-url-template="{{ route('user.bookings.book', 'AREA_ID_PLACEHOLDER') }}"
                         data-unbook-url-template="{{ route('user.bookings.unbook', 'TRANS_ID_PLACEHOLDER') }}"
                         data-csrf-token="{{ csrf_token() }}"
                    ></div>
                </div>
            </div>

            <link rel="stylesheet"
                  href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
                  integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
                  crossorigin=""/>

            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
                    crossorigin=""></script>

            <script src="{{ asset('js/parking-map.js') }}" defer></script>
        @endisset
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const kendaraanSelect = document.getElementById('booking_kendaraan_id');
            const tarifSelect = document.getElementById('booking_tarif_id');

            function autoTarifFromJenis() {
                if (!kendaraanSelect || !tarifSelect) return;
                if (tarifSelect.value) return;
                const opt = kendaraanSelect.options[kendaraanSelect.selectedIndex];
                const jenis = opt?.dataset?.jenis;
                if (!jenis) return;
                for (const tOpt of tarifSelect.options) {
                    if (tOpt.dataset && tOpt.dataset.jenis === jenis) {
                        tarifSelect.value = tOpt.value;
                        break;
                    }
                }
            }

            if (kendaraanSelect) {
                kendaraanSelect.addEventListener('change', autoTarifFromJenis);
            }

            document.querySelectorAll('form.booking-area-form').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    const kendaraanId = kendaraanSelect ? kendaraanSelect.value : '';
                    const tarifId = tarifSelect ? tarifSelect.value : '';
                    if (!kendaraanId) {
                        e.preventDefault();
                        alert('Pilih kendaraan terlebih dahulu sebelum booking.');
                        return;
                    }
                    const kendaraanInput = form.querySelector('input[name="id_kendaraan"]');
                    const tarifInput = form.querySelector('input[name="id_tarif"]');
                    if (kendaraanInput) kendaraanInput.value = kendaraanId;
                    if (tarifInput) tarifInput.value = tarifId || '';
                });
            });
        });
    </script>
@endpush
