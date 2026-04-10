@extends('layouts.app')

@section('title', 'Check-In Vehicle')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('transaksi.parkir.index'),
        'title' => 'Vehicle Check-In',
        'description' => 'Register vehicle entry using manual input or ANPR scanner.',
        'cardIcon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>',
        'cardTitle' => 'Check-In Terminal',
        'cardDescription' => 'Verify vehicle details and assign parking location.',
        'action' => route('transaksi.checkIn'),
        'method' => 'POST',
        'submitText' => 'Confirm Entry'
    ])
        <div x-data="checkInForm()" x-init="init()" class="space-y-8">
            <!-- 1. Scanner Section -->
            <div class="p-6 bg-slate-900/50 border border-white/5 rounded-2xl">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/><path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                    </div>
                    <h3 class="text-xs font-bold text-white uppercase tracking-widest">ANPR Scanner <span class="text-slate-500 font-medium lowercase ml-1">(optional)</span></h3>
                </div>
                <x-plate-scanner
                    target-input-id="plat_nomor"
                    target-input-type="text"
                    :cameras="$cameras ?? []"
                />
            </div>

            <!-- 2. Primary Identification -->
            <div class="space-y-4">
                <label for="plat_nomor" class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">
                    License Plate <span class="text-rose-500">*</span>
                </label>
                <div class="relative group" x-data="{ suggestions: [], showSuggestions: false }">
                    <input type="text"
                           name="plat_nomor"
                           id="plat_nomor"
                           x-model="platNomor"
                           @input.debounce.400ms="searchVehicle(); fetchSuggestions()"
                           @focus="if(suggestions.length > 0) showSuggestions = true"
                           @click.away="showSuggestions = false"
                           placeholder="B 1234 XYZ"
                           class="block w-full px-6 py-5 bg-slate-900/80 border-2 border-white/5 rounded-2xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500/50 transition-all uppercase font-bold text-3xl tracking-widest text-white placeholder:text-slate-800 placeholder:font-bold"
                           autocomplete="off"
                           required>

                    <!-- Autocomplete Suggestions -->
                    <div x-show="showSuggestions && suggestions.length > 0"
                         x-cloak
                         class="absolute z-50 w-full mt-2 bg-slate-900 border border-white/10 rounded-2xl shadow-2xl overflow-hidden animate-fade-in">
                        <template x-for="s in suggestions" :key="s.id_kendaraan">
                            <button type="button"
                                    @click="platNomor = s.plat_nomor; showSuggestions = false; searchVehicle()"
                                    class="w-full px-6 py-4 text-left hover:bg-white/5 border-b border-white/5 last:border-0 flex items-center justify-between group transition-colors">
                                <div>
                                    <p class="text-lg font-black text-white group-hover:text-emerald-500 transition-colors" x-text="s.plat_nomor"></p>
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest" x-text="s.jenis_kendaraan + ' • ' + (s.pemilik || 'General Public')"></p>
                                </div>
                                <i class="fa-solid fa-chevron-right text-slate-700 group-hover:text-emerald-500 transition-all group-hover:translate-x-1"></i>
                            </button>
                        </template>
                    </div>

                    <div class="absolute right-6 top-1/2 -translate-y-1/2 flex items-center gap-3">
                        <template x-if="isSearching">
                            <svg class="animate-spin h-6 w-6 text-emerald-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <template x-if="!isSearching && vehicleFound">
                            <div class="w-8 h-8 bg-emerald-500/10 rounded-full flex items-center justify-center text-emerald-500 border border-emerald-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Status Indicators -->
                <div class="min-h-[48px]">
                    <template x-if="vehicleFound">
                        <div class="p-4 bg-emerald-500/5 border border-emerald-500/10 rounded-xl flex items-center gap-4 animate-fade-in">
                            <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center text-emerald-500">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-emerald-500 font-bold uppercase tracking-widest">Registered Member</p>
                                <p class="text-xs font-semibold text-slate-300" x-text="selectedVehicle.jenis_kendaraan + ' • ' + (selectedVehicle.pemilik || 'General Public')"></p>
                            </div>
                        </div>
                    </template>
                    <template x-if="!vehicleFound && platNomor.length >= 3 && !isSearching">
                        <div class="p-4 bg-amber-500/5 border border-amber-500/10 rounded-xl flex items-center gap-4 animate-fade-in">
                            <div class="w-8 h-8 bg-amber-500/10 rounded-lg flex items-center justify-center text-amber-500">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-amber-500 font-bold uppercase tracking-widest">New Vehicle</p>
                                <p class="text-xs font-semibold text-slate-300">New record will be created automatically.</p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 3. Dynamic Registration Fields -->
            <div x-show="!vehicleFound && platNomor.length >= 3" x-cloak x-transition class="p-6 bg-slate-900/30 border border-white/5 rounded-2xl space-y-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-1.5 h-1.5 rounded-full bg-amber-500"></div>
                    <h4 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Registration Details</h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Type <span class="text-rose-500">*</span></label>
                        <select name="jenis_kendaraan"
                                x-model="jenisKendaraan"
                                @change="syncTarifByJenis()"
                                class="block w-full px-4 py-3 bg-slate-900/50 border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500/50 transition-all">
                            <option value="">Select Type</option>
                            @foreach($tarifs->pluck('jenis_kendaraan')->unique() as $jk)
                                <option value="{{ $jk }}">{{ $jk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Color</label>
                        <input type="text" name="warna" placeholder="e.g. Black" class="block w-full px-4 py-3 bg-slate-900/50 border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500/50 transition-all">
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Owner Name</label>
                        <input type="text" name="pemilik" placeholder="e.g. John Doe" class="block w-full px-4 py-3 bg-slate-900/50 border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500/50 transition-all">
                    </div>
                </div>
            </div>

            <!-- 4. Logistics & Assignment -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Tariff -->
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Tariff Plan <span class="text-rose-500">*</span></label>
                    <select name="id_tarif"
                            x-model="idTarif"
                            required
                            class="block w-full px-4 py-3 bg-slate-900/50 border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500/50 transition-all">
                        <option value="">Select Plan</option>
                        @foreach($tarifs as $t)
                            <option value="{{ $t->id_tarif }}" data-jenis="{{ $t->jenis_kendaraan }}">
                                {{ $t->jenis_kendaraan }} (Rp {{ number_format($t->tarif_perjam, 0, ',', '.') }}/h)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Area -->
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Parking Area <span class="text-rose-500">*</span></label>
                    <select name="id_area"
                            x-model="idArea"
                            @change="filterSlots()"
                            required
                            class="block w-full px-4 py-3 bg-slate-900/50 border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500/50 transition-all">
                        <option value="">Select Area</option>
                        @foreach($areas as $a)
                            <option value="{{ $a->id_area }}">{{ $a->nama_area }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Slot: data dari server (json slots), filter di browser per id_area — tanpa fetch kedua --}}
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Assigned Slot</label>
                    <div class="relative">
                        {{-- Jangan pakai <template x-for> di dalam <select>: tidak valid HTML, browser tidak merender <option> di dalam select --}}
                        <select name="parking_map_slot_id"
                                x-ref="slotSelect"
                                x-model="idSlot"
                                :disabled="!idArea"
                                class="block w-full px-4 py-3 bg-slate-900/50 border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500/50 transition-all disabled:opacity-30 disabled:cursor-not-allowed">
                            <option value="">Optional Slot</option>
                        </select>
                    </div>
                    <p x-show="idArea && (!allSlots || allSlots.length === 0)" x-cloak class="text-[10px] text-amber-400/90 mt-1">
                        Belum ada slot di database untuk check-in (kolom area_parkir_id kosong atau belum ada baris slot).
                    </p>
                    <p x-show="idArea && allSlots.length && filteredSlots.length === 0" x-cloak class="text-[10px] text-amber-400/90 mt-1">
                        Tidak ada slot untuk area ini. Pastikan slot di peta sudah terikat ke area yang sama (area_parkir_id).
                    </p>
                </div>
            </div>

            <!-- 5. Additional Notes -->
            <div class="space-y-2">
                <label for="catatan" class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Additional Notes</label>
                <textarea name="catatan" id="catatan" rows="2" placeholder="e.g. Helmet storage, cargo details, etc." class="block w-full px-4 py-3 bg-slate-900/50 border border-white/5 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500/50 transition-all"></textarea>
            </div>

            <!-- Live Validation Error -->
            <div x-show="errorMessage" x-cloak class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-500 text-xs font-bold rounded-xl flex items-center gap-3 animate-fade-in">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                <span x-text="errorMessage"></span>
            </div>
        </div>
    @endcomponent
@endsection

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    @@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

@push('scripts')
<script>
    function checkInForm() {
        return {
            platNomor: '{{ old('plat_nomor', '') }}',
            isSearching: false,
            vehicleFound: false,
            selectedVehicle: null,

            jenisKendaraan: '',
            idTarif: '{{ old('id_tarif', '') }}',
            idArea: '{{ old('id_area', '') }}',
            idSlot: '{{ old('parking_map_slot_id', '') }}',

            allSlots: @json($slots ?? []),
            filteredSlots: [],
            errorMessage: '',

            init() {
                if (this.platNomor.length >= 3) this.searchVehicle();
                this.filterSlots();
            },

            async searchVehicle() {
                const plat = this.platNomor.trim().toUpperCase();
                if (plat.length < 3) {
                    this.resetVehicleState();
                    return;
                }

                this.isSearching = true;
                try {
                    const response = await fetch('/api/kendaraan/check-plat?plat=' + encodeURIComponent(plat), {
                        credentials: 'same-origin',
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    const data = await response.json();

                    if (data.found && data.kendaraan) {
                        this.vehicleFound = true;
                        this.selectedVehicle = data.kendaraan;
                        this.jenisKendaraan = data.kendaraan.jenis_kendaraan;
                        this.syncTarifByJenis();
                    } else {
                        this.resetVehicleState();
                    }
                } catch (e) {
                    console.error('Search error:', e);
                } finally {
                    this.isSearching = false;
                }
            },

            resetVehicleState() {
                this.vehicleFound = false;
                this.selectedVehicle = null;
            },

            async fetchSuggestions() {
                const plat = this.platNomor.trim();
                if (plat.length < 2) {
                    this.$data.suggestions = [];
                    this.$data.showSuggestions = false;
                    return;
                }

                try {
                    const response = await fetch('/api/kendaraan/search?q=' + encodeURIComponent(plat), {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    const data = await response.json();
                    this.$data.suggestions = data.results || [];
                    this.$data.showSuggestions = this.$data.suggestions.length > 0;
                } catch (e) {
                    console.error('Suggestions error:', e);
                }
            },

            syncTarifByJenis() {
                if (!this.jenisKendaraan) return;
                const selectTarif = document.querySelector('select[name="id_tarif"]');
                const options = Array.from(selectTarif.options);
                const match = options.find(opt => opt.dataset.jenis === this.jenisKendaraan);
                if (match) this.idTarif = match.value;
            },

            filterSlots() {
                const raw = this.idArea;
                if (raw === '' || raw === null || raw === undefined) {
                    this.filteredSlots = [];
                    this.idSlot = '';
                    this.syncSlotSelectOptions();
                    return;
                }
                const aid = parseInt(String(raw), 10);
                if (Number.isNaN(aid)) {
                    console.warn('[checkin] Invalid Area ID (NaN):', raw);
                    this.filteredSlots = [];
                    this.idSlot = '';
                    this.syncSlotSelectOptions();
                    return;
                }

                // Debug: Periksa data allSlots yang diterima dari server
                console.log('[checkin] Filtering slots for aid:', aid, 'from total slots:', (this.allSlots || []).length);

                this.filteredSlots = (this.allSlots || []).filter((s) => {
                    const sid = s.id_area != null ? parseInt(String(s.id_area), 10) : NaN;
                    const match = !Number.isNaN(sid) && sid === aid;
                    if (match) console.log('[checkin] Found matching slot:', s.code, 'for area:', sid);
                    return match;
                });

                console.log('[checkin] Final filteredSlots count:', this.filteredSlots.length);

                if (this.idSlot && !this.filteredSlots.some((s) => String(s.id) === String(this.idSlot))) {
                    this.idSlot = '';
                }
                this.syncSlotSelectOptions();
            },

            /** Isi <option> lewat DOM — satu-satunya cara yang konsisten di semua browser untuk <select> dinamis */
            syncSlotSelectOptions() {
                try {
                    const sel = this.$refs.slotSelect;
                    // Debug cepat: lihat apakah refs terbaca dan filteredSlots terisi
                    console.log('[checkin] syncSlotSelectOptions', {
                        hasRef: !!sel,
                        tag: sel?.tagName,
                        idArea: this.idArea,
                        allSlots: (this.allSlots || []).length,
                        filteredSlots: (this.filteredSlots || []).length,
                    });

                    if (!sel || sel.tagName !== 'SELECT') return;

                    while (sel.options.length > 1) {
                        sel.remove(1);
                    }

                    (this.filteredSlots || []).forEach((s) => {
                        if (s.occupied) return; // Hide occupied slots from dropdown

                        const opt = new Option(
                            s.code,
                            String(s.id),
                            false,
                            false
                        );
                        sel.add(opt);
                    });
                } catch (e) {
                    console.error('[checkin] syncSlotSelectOptions error', e);
                }
            }
        }
    }
</script>
@endpush
