@extends('layouts.app')

@section('title', 'Login RFID')

@section('content')
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                <div class="text-center">
                    <div class="text-3xl font-extrabold tracking-tight text-white">
                        LOGIN DENGAN RFID
                    </div>
                    <div class="text-sm text-slate-300 mt-2">
                        Tap kartu sekarang. Reader Anda akan mengetik UID seperti keyboard.
                    </div>
                </div>

                <div class="mt-6">
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
                        <div class="text-xs text-slate-300">Status</div>
                        <div id="status_box" class="mt-2 text-sm font-semibold text-slate-200">Siap scan...</div>
                        <div class="mt-3 text-xs text-slate-300">Log</div>
                        <pre id="log" class="mt-2 text-xs text-slate-200 whitespace-pre-wrap min-h-[120px]"></pre>
                        <div class="mt-2 text-xs font-semibold" id="error_box" style="color:#f87171;"></div>
                    </div>
                </div>

                <div class="mt-4 text-xs text-slate-400">
                    Catatan: ini “for fun”. Jika kartu hilang, orang lain bisa login.
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const $log = document.getElementById('log');
        const $errorBox = document.getElementById('error_box');
        const $status = document.getElementById('status_box');
        const $input = document.getElementById('rfid_uid_input');

        const loginUrl = @json(route('api.rfid.login'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        let inFlight = false;
        let debounceTimer = null;

        const log = (msg) => {
            $log.textContent = ($log.textContent ? $log.textContent + "\n" : "") + msg;
        };

        const setStatus = (type, msg) => {
            const base = 'mt-2 text-sm font-semibold ';
            if (type === 'success') $status.className = base + 'text-emerald-400';
            else if (type === 'error') $status.className = base + 'text-red-400';
            else $status.className = base + 'text-slate-200';
            $status.textContent = msg;
        };

        const focusInput = () => {
            $errorBox.textContent = '';
            $input.focus();
        };

        async function sendLogin(uid) {
            if (inFlight) return;
            const trimmed = (uid || '').trim();
            if (!trimmed) return;

            inFlight = true;
            setStatus('info', 'Memproses login...');
            $errorBox.textContent = '';
            log('UID terbaca: ' + trimmed);

            const currentUid = trimmed;
            $input.value = '';

            try {
                const res = await fetch(loginUrl, {
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
                    const msg = data.error || data.message || 'Login gagal.';
                    $errorBox.textContent = msg;
                    setStatus('error', msg);
                    log('Gagal: ' + msg);
                    return;
                }

                setStatus('success', data.message || 'Login berhasil.');
                log('Redirect ke dashboard...');
                window.location.href = data.redirect || '/dashboard';
            } catch (e) {
                const msg = e?.message || 'Error jaringan.';
                $errorBox.textContent = msg;
                setStatus('error', msg);
                log('Error: ' + msg);
            } finally {
                inFlight = false;
                focusInput();
            }
        }

        $input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendLogin($input.value);
            }
        });

        $input.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            const v = $input.value.trim();
            if (v.length < 4) return;
            debounceTimer = setTimeout(() => sendLogin($input.value), 300);
        });

        document.addEventListener('click', () => focusInput());

        let globalBuffer = '';
        let globalTimer = null;
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey || e.altKey || e.metaKey) return;
            if (document.activeElement === $input) return;

            if (e.key === 'Enter') {
                if (globalBuffer.trim()) sendLogin(globalBuffer);
                globalBuffer = '';
                return;
            }
            if (e.key.length === 1) {
                globalBuffer += e.key;
                clearTimeout(globalTimer);
                globalTimer = setTimeout(() => {
                    if (globalBuffer.trim()) sendLogin(globalBuffer);
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

