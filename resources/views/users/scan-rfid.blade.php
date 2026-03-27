@extends('layouts.app')

@section('title', 'Scan RFID')

@section('content')
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-3xl font-extrabold tracking-tight text-white">SCAN KARTU SEKARANG</h2>
                        <p class="text-sm text-slate-300 mt-2">
                            Scan UID RFID (keyboard wedge). Setelah tersimpan, Anda akan diarahkan kembali ke detail user.
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-slate-300">User</div>
                        <div class="font-bold text-white">{{ $user->name }}</div>
                        <div class="text-xs text-slate-300">#{{ $user->id }}</div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between gap-4">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-slate-300 mb-2">UID RFID (auto-capture)</label>

                        {{-- Input ini sengaja dibuat fokusable meski secara visual tersembunyi. --}}
                        <input
                            id="rfid_uid_input"
                            name="rfid_uid"
                            type="text"
                            autocomplete="off"
                            inputmode="numeric"
                            autofocus
                            tabindex="0"
                            class="sr-only"
                            aria-label="RFID UID"
                        />
                    </div>

                    <div class="w-56">
                        <div class="text-xs text-slate-300">Status</div>
                        <div id="status_box" class="mt-1 text-sm font-semibold"></div>
                    </div>
                </div>

                <div class="mt-4 rounded-xl border border-white/10 bg-slate-950/20 p-4">
                    <div class="text-xs text-slate-300">Log</div>
                    <pre id="log" class="mt-2 text-xs text-slate-200 whitespace-pre-wrap"></pre>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const $log = document.getElementById('log');
        const $status = document.getElementById('status_box');
        const $input = document.getElementById('rfid_uid_input');

        const saveUrl = @json(route('admin.users.save-rfid', $user->id));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        let inFlight = false;
        let debounceTimer = null;

        const log = (msg) => {
            $log.textContent = ($log.textContent ? $log.textContent + "\n" : "") + msg;
        };

        const setStatus = (type, msg) => {
            const base = 'text-sm font-semibold ';
            if (type === 'success') {
                $status.className = base + 'text-emerald-400';
            } else if (type === 'error') {
                $status.className = base + 'text-red-400';
            } else {
                $status.className = base + 'text-slate-200';
            }
            $status.textContent = msg;
        };

        const focusInput = () => {
            // Wedge scanner akan mengetik ke field ini.
            $input.focus();
        };

        async function postRfid(uid) {
            if (inFlight) return;

            const trimmed = (uid || '').trim();
            if (!trimmed) return;

            inFlight = true;
            setStatus('loading', 'Menyimpan...');
            log('UID terbaca: ' + trimmed);

            // Reset input agar scan berikutnya bersih.
            $input.value = '';

            try {
                const res = await fetch(saveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ rfid_uid: trimmed }),
                });

                const data = await res.json().catch(() => ({}));

                if (!res.ok || !data.ok) {
                    const msg = data?.errors?.rfid_uid?.[0] || data.message || data.error || 'Gagal menyimpan RFID.';
                    setStatus('error', msg);
                    log('Gagal: ' + msg);
                    return;
                }

                setStatus('success', data.message || 'Berhasil.');
                log('Berhasil. Redirect...');

                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            } catch (e) {
                const msg = e?.message || 'Error jaringan.';
                setStatus('error', msg);
                log('Error: ' + msg);
            } finally {
                inFlight = false;
                focusInput();
            }
        }

        // Jika scan mengakhiri dengan Enter, event ini yang paling akurat.
        $input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                postRfid($input.value);
            }
        });

        // Fallback: jika scanner tidak kirim Enter, kirim setelah berhenti mengetik.
        $input.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            const v = $input.value.trim();
            if (v.length < 4) return;
            debounceTimer = setTimeout(() => postRfid($input.value), 250);
        });

        // Refocus: klik di mana pun tetap fokus ke input (scanner = keyboard)
        document.addEventListener('click', () => focusInput());

        // Fallback: kalau input tidak fokus, tetap tangkap ketikan scanner secara global
        let globalBuffer = '';
        let globalTimer = null;
        document.addEventListener('keydown', (e) => {
            // Abaikan kombinasi shortcut
            if (e.ctrlKey || e.altKey || e.metaKey) return;

            // Jika input sedang fokus, biarkan handler input yang bekerja.
            if (document.activeElement === $input) return;

            if (e.key === 'Enter') {
                if (globalBuffer.trim()) postRfid(globalBuffer);
                globalBuffer = '';
                return;
            }

            // Simpan karakter yang “normal”
            if (e.key.length === 1) {
                globalBuffer += e.key;
                clearTimeout(globalTimer);
                globalTimer = setTimeout(() => {
                    if (globalBuffer.trim()) postRfid(globalBuffer);
                    globalBuffer = '';
                }, 300);
            }
        });

        window.addEventListener('load', () => {
            setStatus('info', 'Siap scan...');
            setTimeout(focusInput, 100);
        });
    </script>
@endpush

