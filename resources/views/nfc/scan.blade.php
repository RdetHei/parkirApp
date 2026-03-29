@extends('layouts.app')

@section('title', 'NFC Scan')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Scan NFC</h2>
            <p class="text-sm text-gray-500 mt-1.5">Tap kartu untuk autentikasi user dan mulai parkir.</p>
        </div>
        <div class="text-xs text-gray-500">
            Status: <span id="nfc-support" class="font-bold">cek...</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white shadow-lg rounded-lg p-5">
            <h3 class="font-bold text-gray-900 mb-3">Kontrol</h3>
            <div class="space-y-3">
                <button id="btn-start-scan"
                        class="w-full px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold transition-colors">
                    Scan NFC
                </button>
                <button id="btn-in"
                        class="w-full px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-colors opacity-50"
                        disabled>
                    Tap Masuk
                </button>
                <button id="btn-out"
                        class="w-full px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold transition-colors opacity-50"
                        disabled>
                    Tap Keluar
                </button>
            </div>

            <div class="mt-4 p-3 rounded-lg border border-gray-200 bg-gray-50">
                <div class="text-xs text-gray-600 mb-1">Log</div>
                <pre id="log" class="text-xs text-gray-800 whitespace-pre-wrap"></pre>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-5">
            <h3 class="font-bold text-gray-900 mb-3">Hasil Tap</h3>

            <div id="popup" class="hidden p-4 rounded-xl border border-gray-200 bg-gray-50">
                <div class="flex items-center gap-3">
                    <img id="user-photo" src="" alt="foto user"
                         class="w-14 h-14 rounded-xl bg-gray-200 object-cover" />
                    <div>
                        <div class="text-sm font-bold text-gray-900" id="user-name">-</div>
                        <div class="text-xs text-gray-600" id="user-balance">-</div>
                        <div class="text-xs inline-flex px-2 py-1 rounded-lg font-bold mt-1"
                             id="user-status" style="background:#f4f4f5;color:#333">-</div>
                    </div>
                </div>

                <div class="mt-3 text-xs text-gray-600">
                    <div>User ID: <span id="user-id">-</span></div>
                </div>
            </div>

            <div id="no-popup" class="mt-4 text-sm text-gray-500">
                Belum ada NFC yang terbaca.
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const $log = document.getElementById('log');
    const log = (msg) => {
        $log.textContent = ($log.textContent ? $log.textContent + "\n" : "") + msg;
    };

    const nfcStatus = document.getElementById('nfc-support');
    const btnStart = document.getElementById('btn-start-scan');
    const btnIn = document.getElementById('btn-in');
    const btnOut = document.getElementById('btn-out');

    const popup = document.getElementById('popup');
    const noPopup = document.getElementById('no-popup');
    const photoEl = document.getElementById('user-photo');
    const nameEl = document.getElementById('user-name');
    const balanceEl = document.getElementById('user-balance');
    const statusEl = document.getElementById('user-status');
    const userIdEl = document.getElementById('user-id');

    let scannedToken = null;
    let currentUser = null;
    let reader = null;

    function setPopup(user) {
        currentUser = user;

        photoEl.src = user.photo ? user.photo : '';
        nameEl.textContent = user.name || '-';
        balanceEl.textContent = 'Saldo: ' + (Number(user.balance || 0).toFixed(2));

        const vip = user.status === 'VIP';
        statusEl.textContent = vip ? 'VIP' : 'Biasa';
        statusEl.style.background = vip ? '#f59e0b33' : '#f4f4f5';
        statusEl.style.color = vip ? '#92400e' : '#333';

        userIdEl.textContent = user.user_id;

        popup.classList.remove('hidden');
        noPopup.classList.add('hidden');

        btnIn.disabled = false;
        btnIn.classList.remove('opacity-50');
        btnOut.disabled = false;
        btnOut.classList.remove('opacity-50');
    }

    async function postJson(url, payload) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify(payload),
        });
        const data = await res.json().catch(() => ({}));
        return {res, data};
    }

    async function scanOnce() {
        if (!('NDEFReader' in window)) {
            nfcStatus.textContent = 'Web NFC tidak didukung';
            return;
        }

        reader = new NDEFReader();
        nfcStatus.textContent = 'Menunggu kartu...';
        log('Mulai scan NFC');

        await reader.scan();

        reader.onreading = async (event) => {
            try {
                log('NFC terbaca, memproses...');

                const message = event.message;
                const records = message?.records || [];

                // Cari record type text
                let text = null;
                for (const record of records) {
                    if (record.recordType === 'text') {
                        // record.data bisa ArrayBuffer
                        text = new TextDecoder().decode(record.data);
                        break;
                    }
                }

                if (!text) {
                    log('Data NFC tidak berupa teks.');
                    return;
                }

                encryptedId = text;
                scannedToken = text;
                log('encrypted_id didapat, kirim ke backend...');

                const {res, data} = await postJson('{{ url('/api/nfc-scan') }}', { nfc_uid: scannedToken, encrypted_id: scannedToken });
                if (!res.ok) {
                    log('Gagal scan: ' + (data.error || res.status));
                    return;
                }

                setPopup(data);
                nfcStatus.textContent = 'Kartu valid. Siap tap.';

            } catch (e) {
                console.error(e);
                log('Error: ' + e.message);
            }
        };
    }

    btnStart.addEventListener('click', async () => {
        await scanOnce();
    });

    btnIn.addEventListener('click', async () => {
        if (!currentUser || !scannedToken) return;
        log('Tap Masuk...');
        btnIn.disabled = true;

        const {res, data} = await postJson('{{ url('/api/parkir/masuk') }}', {
            nfc_uid: scannedToken,
            encrypted_id: scannedToken,
            user_id: currentUser.user_id,
        });

        if (!res.ok) {
            log('Gagal IN: ' + (data.error || res.status));
            btnIn.disabled = false;
            return;
        }

        log('Berhasil IN');
        btnIn.disabled = false;
    });

    btnOut.addEventListener('click', async () => {
        if (!currentUser || !scannedToken) return;
        log('Tap Keluar...');
        btnOut.disabled = true;

        const {res, data} = await postJson('{{ url('/api/parkir/keluar') }}', {
            nfc_uid: scannedToken,
            encrypted_id: scannedToken,
            user_id: currentUser.user_id,
        });

        if (!res.ok) {
            log('Gagal OUT: ' + (data.error || res.status));
            btnOut.disabled = false;
            return;
        }

        log('Berhasil OUT. Amount: ' + Number(data.amount || 0).toFixed(2));
        // update popup balance
        currentUser.balance = data.balance;
        balanceEl.textContent = 'Saldo: ' + Number(data.balance || 0).toFixed(2);
        btnOut.disabled = false;
    });

    // initial support check
    if (!('NDEFReader' in window)) {
        nfcStatus.textContent = 'Web NFC tidak didukung';
    } else {
        nfcStatus.textContent = 'Siap';
    }
</script>
@endpush

