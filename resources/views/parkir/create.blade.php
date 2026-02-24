@extends('layouts.app')

@section('title', 'Catat Kendaraan Masuk')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('transaksi.parkir.index'),
        'title' => 'Catat Kendaraan Masuk',
        'description' => 'Scan atau input plat nomor. Kendaraan terdaftar akan auto-fill, kendaraan baru bisa didaftarkan langsung.',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>',
        'cardTitle' => 'Form Kendaraan Masuk',
        'cardDescription' => 'Plat nomor terdaftar = auto-fill. Plat baru = isi data kendaraan.',
        'action' => route('transaksi.checkIn'),
        'method' => 'POST',
        'submitText' => 'Catat Masuk'
    ])
        <div x-data="checkInForm()" x-init="init()">
            <!-- Plate Scanner (kamera dari CRUD Kamera) -->
            <div class="mb-6 pb-6 border-b border-gray-200">
                <x-plate-scanner
                    target-input-id="plat_nomor"
                    target-input-type="text"
                    :cameras="$cameras ?? []"
                />
            </div>

            <!-- Plat Nomor Input -->
            <div class="mb-4">
                <label for="plat_nomor" class="block text-sm font-semibold text-gray-700 mb-2">Plat Nomor <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                    </div>
                    <input type="text"
                           id="plat_nomor"
                           x-model="platNomor"
                           @input.debounce.300ms="checkPlat()"
                           placeholder="Contoh: B 1234 XYZ"
                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           autocomplete="off">
                </div>
                <p x-show="isChecking" x-cloak class="mt-1 text-sm text-blue-600">Memeriksa...</p>
                <p x-show="vehicleFound && !isChecking" x-cloak class="mt-1 text-sm text-green-600">
                    <span x-text="'✓ Kendaraan terdaftar: ' + (selectedVehicle ? selectedVehicle.jenis_kendaraan + ' (' + (selectedVehicle.pemilik || 'Tanpa pemilik') + ')' : '')"></span>
                </p>
                <p x-show="!vehicleFound && platNomor.length >= 2 && !isChecking" x-cloak class="mt-1 text-sm text-amber-600">
                    Kendaraan belum terdaftar. Isi data di bawah.
                </p>
            </div>

            <!-- Hidden: id_kendaraan (hanya dikirim saat kendaraan terdaftar) -->
            <input type="hidden" :name="vehicleFound ? 'id_kendaraan' : ''" :value="selectedVehicle ? selectedVehicle.id_kendaraan : ''">

            <!-- Hidden: vehicle_mode -->
            <input type="hidden" name="vehicle_mode" :value="vehicleFound ? 'existing' : 'new'">

            <!-- Section: Kendaraan Baru (hanya tampil jika plat tidak terdaftar) -->
            <div x-show="!vehicleFound && platNomor.length >= 2"
                 x-cloak
                 x-transition
                 class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                <h4 class="text-sm font-bold text-amber-800 mb-3">Data Kendaraan Baru</h4>
                <input type="hidden" :name="vehicleFound ? '' : 'plat_nomor'" :value="platNomor">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="jenis_kendaraan" class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kendaraan <span class="text-red-500">*</span></label>
                        <select name="jenis_kendaraan" id="jenis_kendaraan"
                                :required="!vehicleFound"
                                x-ref="jenisKendaraan"
                                @change="autoSelectTarif()"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">-- Pilih --</option>
                            @foreach($tarifs as $t)
                                <option value="{{ $t->jenis_kendaraan }}">{{ $t->jenis_kendaraan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="warna" class="block text-sm font-semibold text-gray-700 mb-1">Warna (Opsional)</label>
                        <input type="text" name="warna" id="warna" placeholder="Contoh: Hitam"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                               value="{{ old('warna') }}">
                    </div>
                    <div class="md:col-span-2">
                        <label for="pemilik" class="block text-sm font-semibold text-gray-700 mb-1">Pemilik (Opsional)</label>
                        <input type="text" name="pemilik" id="pemilik" placeholder="Nama pemilik kendaraan"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                               value="{{ old('pemilik') }}">
                    </div>
                </div>
            </div>

            <!-- Tarif -->
            <div class="mb-4">
                <label for="id_tarif" class="block text-sm font-semibold text-gray-700 mb-2">Tarif <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <select name="id_tarif" id="id_tarif" required
                            x-ref="idTarif"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('id_tarif') border-red-500 @enderror">
                        <option value="">-- Pilih Tarif --</option>
                        @foreach($tarifs as $t)
                            <option value="{{ $t->id_tarif }}" data-jenis="{{ $t->jenis_kendaraan }}" {{ old('id_tarif') == $t->id_tarif ? 'selected' : '' }}>
                                {{ $t->jenis_kendaraan }} — Rp {{ number_format($t->tarif_perjam, 0, ',', '.') }}/jam
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('id_tarif')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Area Parkir -->
            <div class="mb-4">
                <label for="id_area" class="block text-sm font-semibold text-gray-700 mb-2">Area Parkir <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <select name="id_area" id="id_area" required
                            @change="loadSlots($event.target.value)"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('id_area') border-red-500 @enderror">
                        <option value="">-- Pilih Area --</option>
                        @foreach($areas as $a)
                            <option value="{{ $a->id_area }}" {{ old('id_area') == $a->id_area ? 'selected' : '' }}>
                                {{ $a->nama_area }} (Kapasitas: {{ $a->kapasitas ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('id_area')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Slot Parkir (opsional) -->
            <div class="mb-4">
                <label for="parking_map_slot_id" class="block text-sm font-semibold text-gray-700 mb-2">Slot Parkir (Opsional)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"></path></svg>
                    </div>
                    <select name="parking_map_slot_id" id="parking_map_slot_id"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('parking_map_slot_id') border-red-500 @enderror">
                        <option value="">— Pilih slot (opsional) —</option>
                        <template x-for="s in slotOptions" :key="s.id">
                            <option :value="s.id" :disabled="s.occupied" x-text="s.code + (s.occupied ? ' (Terisi)' : '')"></option>
                        </template>
                    </select>
                    <span x-show="loadingSlots" x-cloak class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Memuat...</span>
                </div>
                @error('parking_map_slot_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Catatan -->
            <div class="mb-4">
                <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-2">Catatan (Opsional)</label>
                <textarea name="catatan" id="catatan" rows="3" placeholder="Tambahkan catatan jika perlu"
                          class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('catatan') border-red-500 @enderror">{{ old('catatan') }}</textarea>
                @error('catatan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Validasi sebelum submit -->
            <div x-show="showSubmitError" x-cloak class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                <span x-text="submitError"></span>
            </div>
        </div>
    @endcomponent

    <script>
        function checkInForm() {
            const tarifs = @json($tarifs);

            return {
                platNomor: '{{ old('plat_nomor', '') }}',
                selectedVehicle: null,
                vehicleFound: false,
                isChecking: false,
                showSubmitError: false,
                submitError: '',
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                slotOptions: [],
                loadingSlots: false,

                init() {
                    if (this.platNomor.length >= 2) {
                        this.checkPlat();
                    }
                    // Restore old values for validation errors
                    @if(old('vehicle_mode') === 'new')
                        this.vehicleFound = false;
                        this.selectedVehicle = null;
                    @elseif(old('id_kendaraan'))
                        this.vehicleFound = true;
                        this.selectedVehicle = { id_kendaraan: '{{ old('id_kendaraan') }}', jenis_kendaraan: '', pemilik: '' };
                    @endif
                    const areaId = document.getElementById('id_area')?.value;
                    if (areaId) {
                        this.loadSlots(areaId);
                    }
                    // Attach form submit validation
                    const form = this.$el.closest('form');
                    if (form) {
                        form.addEventListener('submit', (e) => this.validateSubmit(e));
                    }
                },

                async loadSlots(areaId) {
                    const select = document.getElementById('parking_map_slot_id');
                    if (!select) return;
                    this.slotOptions = [];
                    if (!areaId) {
                        select.value = '';
                        return;
                    }
                    this.loadingSlots = true;
                    try {
                        const url = `{{ route('api.areas.slots', ['area' => '__ID__']) }}`.replace('__ID__', areaId);
                        const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
                        const data = await res.json();
                        this.slotOptions = Array.isArray(data) ? data : [];
                        const oldSlot = '{{ old('parking_map_slot_id') }}';
                        select.value = oldSlot && this.slotOptions.some(s => String(s.id) === oldSlot) ? oldSlot : '';
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.loadingSlots = false;
                    }
                },

                async checkPlat() {
                    const plat = this.platNomor.trim();
                    if (plat.length < 2) {
                        this.vehicleFound = false;
                        this.selectedVehicle = null;
                        return;
                    }

                    this.isChecking = true;
                    this.vehicleFound = false;
                    this.selectedVehicle = null;

                    try {
                        const res = await fetch(`{{ route('api.kendaraan.check-plat') }}?plat=${encodeURIComponent(plat)}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();

                        if (data.found && data.kendaraan) {
                            this.vehicleFound = true;
                            this.selectedVehicle = data.kendaraan;
                            this.autoSelectTarifByJenis(data.kendaraan.jenis_kendaraan);
                        } else {
                            this.vehicleFound = false;
                            this.selectedVehicle = null;
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.isChecking = false;
                    }
                },

                autoSelectTarif() {
                    const jenisSelect = this.$refs.jenisKendaraan;
                    const tarifSelect = this.$refs.idTarif;
                    if (!jenisSelect || !tarifSelect) return;
                    const jenis = jenisSelect.value;
                    if (!jenis) return;
                    for (let opt of tarifSelect.options) {
                        if (opt.dataset.jenis === jenis) {
                            tarifSelect.value = opt.value;
                            break;
                        }
                    }
                },

                autoSelectTarifByJenis(jenis) {
                    const tarifSelect = this.$refs.idTarif;
                    if (!tarifSelect || !jenis) return;
                    for (let opt of tarifSelect.options) {
                        if (opt.dataset.jenis === jenis) {
                            tarifSelect.value = opt.value;
                            break;
                        }
                    }
                },

                validateSubmit(event) {
                    const plat = this.platNomor.trim();
                    if (plat.length < 2) {
                        event.preventDefault();
                        this.showSubmitError = true;
                        this.submitError = 'Plat nomor minimal 2 karakter.';
                        return;
                    }
                    if (!this.vehicleFound) {
                        const jenisSelect = this.$refs.jenisKendaraan;
                        if (jenisSelect && !jenisSelect.value) {
                            event.preventDefault();
                            this.showSubmitError = true;
                            this.submitError = 'Pilih jenis kendaraan untuk kendaraan baru.';
                            return;
                        }
                    }
                    this.showSubmitError = false;
                }
            };
        }
    </script>
@endsection
