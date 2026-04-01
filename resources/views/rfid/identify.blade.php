@extends('layouts.app')

@section('title', 'RFID Identifikasi')

@section('content')
<div class="p-8 relative z-10 animate-fade-in">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                        Identification Mode
                    </span>
                    <div id="loading_state" class="hidden">
                        <span class="flex items-center gap-2 px-3 py-1 bg-blue-500/10 text-blue-400 text-[10px] font-bold uppercase tracking-widest rounded-full border border-blue-500/20">
                            <i class="fa-solid fa-circle-notch animate-spin"></i>
                            Processing
                        </span>
                    </div>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-white uppercase">RFID <span class="text-emerald-500">IDENTIFY</span></h1>
                <p class="text-slate-400 text-sm mt-2 font-medium tracking-wide">Tap kartu untuk verifikasi identitas dan aset terdaftar.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="px-6 py-4 bg-slate-950 border border-white/5 rounded-2xl flex items-center gap-4 shadow-xl">
                    <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Scanner Ready</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Identification Card -->
            <div class="lg:col-span-2 card-pro group overflow-hidden relative border-emerald-500/10">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
                
                <input
                    id="rfid_uid_input"
                    type="text"
                    autocomplete="off"
                    inputmode="numeric"
                    autofocus
                    tabindex="0"
                    class="sr-only"
                    aria-label="RFID UID"
                />

                <div class="relative z-10 p-2">
                    <!-- User Identity Section -->
                    <div class="p-8 sm:p-10 rounded-[2.5rem] bg-slate-950/50 border border-white/5 mb-6">
                        <div class="flex flex-col sm:flex-row items-center gap-8">
                            <div class="relative group">
                                <div class="absolute -inset-4 bg-emerald-500/10 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <div class="w-32 h-32 rounded-[2.5rem] bg-slate-900 border-2 border-white/5 overflow-hidden shadow-2xl relative z-10 flex items-center justify-center">
                                    <img id="user-photo" src="" alt="foto user" class="w-full h-full object-cover hidden"/>
                                    <i id="user-photo-placeholder" class="fa-solid fa-user text-4xl text-slate-800"></i>
                                </div>
                            </div>
                            
                            <div class="flex-1 text-center sm:text-left">
                                <div class="mb-4">
                                    <h2 id="user-name" class="text-3xl font-black text-white tracking-tight">-</h2>
                                    <div class="flex items-center justify-center sm:justify-start gap-3 mt-1">
                                        <span id="user-role" class="px-2 py-0.5 bg-white/5 text-slate-500 text-[9px] font-black uppercase rounded-lg border border-white/10 tracking-widest">-</span>
                                        <span id="user-id-display" class="text-[9px] font-bold text-slate-700 uppercase tracking-widest">UID: ---</span>
                                    </div>
                                </div>
                                
                                <div class="inline-flex items-center gap-3 px-6 py-3 bg-emerald-500/5 border border-emerald-500/10 rounded-2xl">
                                    <i class="fa-solid fa-wallet text-emerald-500"></i>
                                    <span id="user-saldo" class="text-lg font-black text-emerald-400 tracking-tighter">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Asset & Activity Section -->
                    <div id="user-details" class="grid grid-cols-1 md:grid-cols-2 gap-6 opacity-40 grayscale transition-all duration-500">
                        <!-- Vehicles -->
                        <div class="card-pro !p-6 bg-slate-950/30 border-white/5">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="p-2 bg-blue-500/10 rounded-xl border border-blue-500/20 text-blue-400">
                                    <i class="fa-solid fa-car-side text-sm"></i>
                                </div>
                                <h3 class="text-[10px] font-black text-white uppercase tracking-widest">Registered Vehicles</h3>
                            </div>
                            <div id="user-vehicles" class="space-y-3">
                                <div class="py-4 text-center border border-dashed border-white/5 rounded-2xl">
                                    <p class="text-[9px] text-slate-600 font-bold uppercase tracking-widest italic">No data detected</p>
                                </div>
                            </div>
                        </div>

                        <!-- Active Parking -->
                        <div class="card-pro !p-6 bg-slate-950/30 border-white/5">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="p-2 bg-amber-500/10 rounded-xl border border-amber-500/20 text-amber-400">
                                    <i class="fa-solid fa-clock-rotate-left text-sm"></i>
                                </div>
                                <h3 class="text-[10px] font-black text-white uppercase tracking-widest">Active Parking</h3>
                            </div>
                            <div id="user-parking-status" class="space-y-3">
                                <div class="py-4 text-center border border-dashed border-white/5 rounded-2xl">
                                    <p class="text-[9px] text-slate-600 font-bold uppercase tracking-widest italic">No active session</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side Log Panel -->
            <div class="space-y-6">
                <div class="card-pro !p-0 overflow-hidden border-white/5 h-full flex flex-col min-h-[400px]">
                    <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                        <h2 class="text-[10px] font-black text-white uppercase tracking-widest">Activity Log</h2>
                        <i class="fa-solid fa-terminal text-[10px] text-slate-600"></i>
                    </div>
                    <div class="p-6 flex-1 font-mono text-[10px] overflow-y-auto max-h-[500px] scrollbar-hide">
                        <div id="log" class="space-y-2 text-slate-400">
                            <!-- log items -->
                        </div>
                        <div id="error_box" class="mt-4 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 font-bold hidden">
                            <!-- error msg -->
                        </div>
                    </div>
                    <div class="p-4 bg-slate-950 border-t border-white/5">
                        <button onclick="document.getElementById('log').innerHTML = ''" class="w-full py-2 text-[9px] font-black text-slate-600 uppercase tracking-widest hover:text-white transition-colors">
                            Clear Log
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        const $log = document.getElementById('log');
        const $errorBox = document.getElementById('error_box');
        const $loading = document.getElementById('loading_state');

        const $input = document.getElementById('rfid_uid_input');
        const $photo = document.getElementById('user-photo');
        const $photoPlaceholder = document.getElementById('user-photo-placeholder');
        const $name = document.getElementById('user-name');
        const $saldo = document.getElementById('user-saldo');
        const $role = document.getElementById('user-role');
        const $details = document.getElementById('user-details');
        const $vehicles = document.getElementById('user-vehicles');
        const $parkingStatus = document.getElementById('user-parking-status');
        const $uidDisplay = document.getElementById('user-id-display');

        const identifyUrl = @json(route('api.rfid.identify'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        let inFlight = false;
        let debounceTimer = null;

        const log = (msg, type = 'info') => {
            const time = new Date().toLocaleTimeString('id-ID', { hour12: false });
            const item = document.createElement('div');
            item.className = 'flex gap-3 leading-relaxed';
            
            let colorClass = 'text-slate-500';
            if (type === 'success') colorClass = 'text-emerald-500';
            if (type === 'error') colorClass = 'text-rose-500';
            if (type === 'uid') colorClass = 'text-blue-400';

            item.innerHTML = `
                <span class="text-slate-700 shrink-0">[${time}]</span>
                <span class="${colorClass}">${msg}</span>
            `;
            $log.appendChild(item);
            $log.parentElement.scrollTop = $log.parentElement.scrollHeight;
        };

        const focusInput = () => {
            $errorBox.classList.add('hidden');
            $input.focus();
        };

        const setLoading = (on) => {
            if (on) $loading.classList.remove('hidden');
            else $loading.classList.add('hidden');
        };

        async function sendIdentify(uid) {
            if (inFlight) return;

            const trimmed = (uid || '').trim();
            if (!trimmed) return;

            inFlight = true;
            setLoading(true);
            $errorBox.classList.add('hidden');
            log(trimmed, 'uid');

            const currentUid = trimmed;
            $input.value = '';

            try {
                const res = await fetch(identifyUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ rfid_uid: currentUid }),
                });

                const data = await res.json().catch(() => ({}));
                if (!res.ok || !data.ok) {
                    const msg = data.error || data.message || 'Identifikasi gagal.';
                    $errorBox.textContent = msg;
                    $errorBox.classList.remove('hidden');
                    log(msg, 'error');
                    
                    // Reset UI
                    $name.textContent = '-';
                    $saldo.textContent = 'Rp 0';
                    $role.textContent = '-';
                    $uidDisplay.textContent = 'UID: ---';
                    $photo.classList.add('hidden');
                    $photoPlaceholder.classList.remove('hidden');
                    $details.classList.add('grayscale', 'opacity-40');
                    return;
                }

                const u = data.user || {};
                $name.textContent = u.name || '-';
                $saldo.textContent = (Number(u.balance || u.saldo || 0).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }));
                $role.textContent = u.role || '-';
                $uidDisplay.textContent = 'UID: ' + currentUid;

                // Tampilkan detail tambahan
                $details.classList.remove('grayscale', 'opacity-40');

                // Render kendaraan
                if (u.kendaraans && u.kendaraans.length > 0) {
                    $vehicles.innerHTML = u.kendaraans.map(k => `
                        <div class="bg-slate-900/50 p-4 rounded-2xl border border-white/5 group/v hover:border-blue-500/30 transition-all">
                            <div class="flex items-center justify-between mb-2">
                                <div class="text-sm font-black text-white tracking-tighter">${k.plat_nomor}</div>
                                <span class="text-[8px] font-black px-1.5 py-0.5 bg-blue-500/10 text-blue-400 rounded-md border border-blue-500/20 uppercase tracking-widest">${k.jenis}</span>
                            </div>
                            <div class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">${k.warna}</div>
                        </div>
                    `).join('');
                } else {
                    $vehicles.innerHTML = `
                        <div class="py-6 text-center border border-dashed border-white/5 rounded-2xl">
                            <p class="text-[9px] text-slate-600 font-bold uppercase tracking-widest italic">Tidak ada kendaraan</p>
                        </div>
                    `;
                }

                // Render status parkir
                if (u.active_parking) {
                    $parkingStatus.innerHTML = `
                        <div class="bg-emerald-500/5 p-4 rounded-2xl border border-emerald-500/20 relative overflow-hidden group/p">
                            <div class="absolute right-0 top-0 bottom-0 w-1 bg-emerald-500"></div>
                            <div class="text-sm font-black text-white tracking-tighter mb-2">${u.active_parking.plat_nomor}</div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 text-[9px] text-slate-400 font-bold uppercase tracking-widest">
                                    <i class="fa-solid fa-location-dot text-emerald-500/50"></i>
                                    ${u.active_parking.area}
                                </div>
                                <div class="flex items-center gap-2 text-[9px] text-slate-400 font-bold uppercase tracking-widest">
                                    <i class="fa-solid fa-right-to-bracket text-emerald-500/50"></i>
                                    ${u.active_parking.waktu_masuk}
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    $parkingStatus.innerHTML = `
                        <div class="py-6 text-center border border-dashed border-white/5 rounded-2xl">
                            <p class="text-[9px] text-slate-600 font-bold uppercase tracking-widest italic">Sedang tidak parkir</p>
                        </div>
                    `;
                }

                if (u.photo) {
                    $photo.src = u.photo;
                    $photo.classList.remove('hidden');
                    $photoPlaceholder.classList.add('hidden');
                } else {
                    $photo.src = '';
                    $photo.classList.add('hidden');
                    $photoPlaceholder.classList.remove('hidden');
                }

                log('OK: User ' + (u.name ?? '-') + ' teridentifikasi.', 'success');
                
                // Success animation feedback
                $details.classList.add('scale-[1.01]');
                setTimeout(() => $details.classList.remove('scale-[1.01]'), 200);

            } catch (e) {
                const msg = e?.message || 'Error jaringan.';
                $errorBox.textContent = msg;
                $errorBox.classList.remove('hidden');
                log(msg, 'error');
            } finally {
                inFlight = false;
                setLoading(false);
                focusInput();
            }
        }

        $input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendIdentify($input.value);
            }
        });

        $input.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            const v = $input.value.trim();
            if (v.length < 4) return;
            debounceTimer = setTimeout(() => sendIdentify($input.value), 300);
        });

        document.addEventListener('click', () => focusInput());

        let globalBuffer = '';
        let globalTimer = null;
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey || e.altKey || e.metaKey) return;
            if (document.activeElement === $input) return;

            if (e.key === 'Enter') {
                if (globalBuffer.trim()) sendIdentify(globalBuffer);
                globalBuffer = '';
                return;
            }
            if (e.key.length === 1) {
                globalBuffer += e.key;
                clearTimeout(globalTimer);
                globalTimer = setTimeout(() => {
                    if (globalBuffer.trim()) sendIdentify(globalBuffer);
                    globalBuffer = '';
                }, 300);
            }
        });

        window.addEventListener('load', () => {
            setLoading(false);
            focusInput();
            log('Sistem Identifikasi Siap.');
        });
    </script>
@endpush
