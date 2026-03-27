@extends('layouts.app')

@section('title', 'RFID Identifikasi')

@section('content')
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                <div class="text-center">
                    <div class="text-3xl font-extrabold tracking-tight text-white">
                        TAP KARTU UNTUK IDENTIFIKASI
                    </div>
                    <div class="text-sm text-slate-300 mt-2">
                        Mode identifikasi saja (tanpa transaksi parkir).
                    </div>
                </div>

                <div class="mt-6 flex items-start justify-between gap-4">
                    <div class="flex-1">
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

                        <div class="mt-4 rounded-xl border border-white/10 bg-slate-950/20 p-4">
                            <div class="flex items-center gap-3">
                                <img id="user-photo" src="" alt="foto user"
                                     class="w-14 h-14 rounded-xl bg-gray-200 object-cover hidden"/>
                                <div>
                                    <div class="text-sm font-bold text-white" id="user-name">-</div>
                                    <div class="text-xs text-slate-300" id="user-saldo">-</div>
                                    <div class="text-xs text-slate-300" id="user-role">-</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-sm font-semibold" id="loading_state" style="display:none;color:#93c5fd;">
                            Memproses...
                        </div>
                    </div>

                    <div class="w-72">
                        <div class="text-xs text-slate-300">Log</div>
                        <pre id="log" class="mt-2 text-xs text-slate-200 whitespace-pre-wrap min-h-[160px]"></pre>
                        <div class="mt-2 text-xs font-semibold" id="error_box" style="color:#f87171;"></div>
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
        const $name = document.getElementById('user-name');
        const $saldo = document.getElementById('user-saldo');
        const $role = document.getElementById('user-role');

        const identifyUrl = @json(route('api.rfid.identify'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        let inFlight = false;
        let debounceTimer = null;

        const log = (msg) => {
            $log.textContent = ($log.textContent ? $log.textContent + "\n" : "") + msg;
        };

        const focusInput = () => {
            $errorBox.textContent = '';
            $input.focus();
        };

        const setLoading = (on) => {
            $loading.style.display = on ? 'block' : 'none';
        };

        async function sendIdentify(uid) {
            if (inFlight) return;

            const trimmed = (uid || '').trim();
            if (!trimmed) return;

            inFlight = true;
            setLoading(true);
            $errorBox.textContent = '';
            log('UID terbaca: ' + trimmed);

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
                    log('Gagal: ' + msg);
                    return;
                }

                const u = data.user || {};
                $name.textContent = u.name || '-';
                $saldo.textContent = 'Saldo: ' + (Number(u.saldo || 0).toFixed(2));
                $role.textContent = 'Role: ' + (u.role || '-');

                if (u.photo) {
                    $photo.src = u.photo;
                    $photo.classList.remove('hidden');
                } else {
                    $photo.src = '';
                    $photo.classList.add('hidden');
                }

                log('OK: user #' + (u.user_id ?? '-') + ' teridentifikasi.');
            } catch (e) {
                const msg = e?.message || 'Error jaringan.';
                $errorBox.textContent = msg;
                log('Error: ' + msg);
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
            log('Siap identifikasi RFID.');
        });
    </script>
@endpush

