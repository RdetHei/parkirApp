@extends('layouts.app')

@section('title', 'Booking Slot Parkir - NESTON')

@section('content')
<div class="p-8 relative z-10 animate-fade-in">
    <!-- Background Glows -->
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="max-w-6xl mx-auto relative z-10">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-black uppercase tracking-widest rounded-full border border-emerald-500/20">
                        Smart Reservation
                    </span>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-white uppercase">Booking <span class="text-emerald-500">Slot</span></h1>
                <p class="text-slate-400 text-sm mt-2 font-medium tracking-wide">Pilih area parkir yang tersedia untuk reservasi instan.</p>
            </div>
            
            <a href="{{ route('user.dashboard') }}"
               class="group px-6 py-3.5 bg-white/5 border border-white/5 rounded-2xl text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-white hover:bg-white/10 transition-all flex items-center gap-3 active:scale-95">
                <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                Kembali ke Dashboard
            </a>
        </div>

        <!-- Configuration Section -->
        <div class="card-pro border-white/5 backdrop-blur-xl bg-slate-900/40 p-8 mb-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-end">
                <div class="md:col-span-2 space-y-3">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Kendaraan untuk Booking</label>
                    <select id="booking_kendaraan_id"
                            class="block w-full rounded-2xl border border-white/5 bg-slate-950/50 px-5 py-4 text-sm text-white focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/5 focus:outline-none transition-all font-bold">
                        <option value="" class="bg-slate-900">— Pilih kendaraan —</option>
                        @foreach($kendaraans as $k)
                            <option value="{{ $k->id_kendaraan }}" data-jenis="{{ $k->jenis_kendaraan }}" class="bg-slate-900">
                                {{ $k->plat_nomor }} • {{ strtoupper($k->jenis_kendaraan) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Tarif Layanan</label>
                    <select id="booking_tarif_id"
                            class="block w-full rounded-2xl border border-white/5 bg-slate-950/50 px-5 py-4 text-sm text-white focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/5 focus:outline-none transition-all font-bold">
                        <option value="" class="bg-slate-900">— Otomatis —</option>
                        @foreach($tarifs as $t)
                            <option value="{{ $t->id_tarif }}" data-jenis="{{ $t->jenis_kendaraan }}" class="bg-slate-900">
                                {{ strtoupper($t->jenis_kendaraan) }} • Rp {{ number_format($t->tarif_perjam, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Areas Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            @forelse($areas as $area)
                @php
                    $status = $statusPerArea[$area->id_area] ?? 'empty';
                    $isAvailable = $status === 'empty';
                    $isMine = $status === 'bookmarked-by-me';
                    
                    $themeClass = $isAvailable ? 'border-emerald-500/20 bg-emerald-500/5' : 
                                 ($isMine ? 'border-blue-500/20 bg-blue-500/5' : 'border-white/5 bg-slate-900/40 opacity-60');
                    $accentColor = $isAvailable ? 'emerald' : ($isMine ? 'blue' : 'slate');
                @endphp
                <div class="card-pro group overflow-hidden relative border backdrop-blur-xl transition-all hover:scale-[1.02] {{ $themeClass }}">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-black text-white tracking-tight uppercase">{{ $area->nama_area }}</h2>
                        <span class="px-2.5 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest border
                            {{ $isAvailable ? 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20' : 
                               ($isMine ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' : 'bg-white/5 text-slate-500 border-white/10') }}">
                            {{ $isAvailable ? 'Tersedia' : ($isMine ? 'Milik Anda' : 'Terisi') }}
                        </span>
                    </div>
                    
                    <div class="space-y-2 mb-8">
                        <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-widest">
                            <span class="text-slate-500">Kapasitas</span>
                            <span class="text-white">{{ $area->kapasitas }} Slot</span>
                        </div>
                        <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden">
                            <div class="h-full bg-{{ $accentColor }}-500" style="width: {{ ($area->terisi / $area->kapasitas) * 100 }}%"></div>
                        </div>
                        <p class="text-[9px] text-slate-600 font-black uppercase tracking-widest text-right">Terisi {{ $area->terisi }} Slot</p>
                    </div>

                    @if($isAvailable)
                        <form method="POST" action="{{ route('user.bookings.book', $area->id_area) }}" class="booking-area-form">
                            @csrf
                            <input type="hidden" name="id_kendaraan" value="">
                            <input type="hidden" name="id_tarif" value="">
                            <button type="submit"
                                    class="w-full group relative flex items-center justify-center gap-3 py-4 bg-emerald-500 text-slate-950 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-emerald-400 transition-all shadow-xl active:scale-[0.98] overflow-hidden">
                                <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                                Booking Sekarang
                            </button>
                        </form>
                    @elseif($isMine)
                        @php $transId = $myBookingIds[$area->id_area] ?? null; @endphp
                        @if($transId)
                        <form method="POST" action="{{ route('user.bookings.unbook', $transId) }}" onsubmit="return confirm('Batalkan booking?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-3 py-4 bg-blue-500 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-blue-400 transition-all shadow-xl active:scale-[0.98]">
                                Batalkan Booking
                            </button>
                        </form>
                        @endif
                    @else
                        <button disabled class="w-full py-4 bg-white/5 text-slate-600 text-[10px] font-black uppercase tracking-widest rounded-2xl border border-white/5 cursor-not-allowed">
                            Tidak Tersedia
                        </button>
                    @endif
                </div>
            @empty
                <div class="col-span-full py-20 text-center bg-slate-950/30 rounded-[2.5rem] border border-dashed border-white/10">
                    <p class="text-[10px] text-slate-600 font-black uppercase tracking-[0.3em]">Belum ada area parkir</p>
                </div>
            @endforelse
        </div>

        @isset($map)
            <div class="card-pro !p-0 overflow-hidden border-white/5 backdrop-blur-xl bg-slate-900/40 shadow-2xl">
                <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                    <div>
                        <h2 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Peta Slot Interaktif</h2>
                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-1">Klik pada slot untuk melakukan booking instan.</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            <span class="text-[8px] font-black text-slate-500 uppercase tracking-widest">Kosong</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                            <span class="text-[8px] font-black text-slate-500 uppercase tracking-widest">Terisi</span>
                        </div>
                    </div>
                </div>
                <div class="p-8">
                    <div id="parking-map"
                         class="w-full rounded-[2rem] border border-white/5 shadow-2xl overflow-hidden"
                         style="height: 600px; background-color: #020617;"
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

            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
            <script src="{{ asset('js/parking-map.js') }}" defer></script>
        @endisset
    </div>
</div>

<style>
    @keyframes shimmer {
        100% { transform: translateX(100%); }
    }
</style>
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
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pilih Kendaraan',
                            text: 'Silakan pilih kendaraan Anda sebelum melakukan booking.',
                            background: '#0f172a',
                            color: '#fff',
                            confirmButtonColor: '#10b981'
                        });
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
