@extends('layouts.app')

@section('title', 'Write NFC')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Write NFC (Admin)</h2>
            <p class="text-sm text-gray-500 mt-1.5">Pilih user, lalu tulis encrypted user ID ke kartu.</p>
        </div>
        <div class="text-xs text-gray-500">
            Web NFC status: <span id="nfc-support" class="font-bold">cek...</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white shadow-lg rounded-lg p-5">
            <h3 class="font-bold text-gray-900 mb-3">Daftar User</h3>

            @if($users->count())
                <div class="space-y-3">
                    @foreach($users as $u)
                        <div class="flex items-center justify-between gap-3 border border-gray-200 rounded-xl p-3">
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-gray-900 truncate">{{ $u->name }}</div>
                                <div class="text-xs text-gray-600 truncate">#{{ $u->id }} | {{ $u->email }}</div>
                            </div>
                            <button class="write-btn px-3 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold"
                                    data-user-id="{{ $u->id }}">
                                Write
                            </button>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            @else
                <div class="text-sm text-gray-500">Tidak ada user.</div>
            @endif
        </div>

        <div class="bg-white shadow-lg rounded-lg p-5">
            <h3 class="font-bold text-gray-900 mb-3">Status Write</h3>

            <div class="p-4 rounded-xl border border-gray-200 bg-gray-50">
                <div class="text-xs text-gray-600 mb-2">Log</div>
                <pre id="log" class="text-xs text-gray-800 whitespace-pre-wrap"></pre>
            </div>

            <div class="mt-4">
                <div class="text-xs text-gray-600 mb-1">Instruksi</div>
                <ol class="list-decimal list-inside text-xs text-gray-700 space-y-1">
                    <li>Klik tombol <b>Write</b> untuk user tertentu</li>
                    <li>Tempelkan kartu NFC saat browser meminta</li>
                    <li>Jika sukses, kartu berisi encrypted_id user</li>
                </ol>
            </div>

            <div class="mt-4 p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-xs">
                Catatan: NFC ini hanya menyimpan identitas terenkripsi, bukan saldo.
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

    const nfcSupport = document.getElementById('nfc-support');

    if (!('NDEFReader' in window)) {
        nfcSupport.textContent = 'Web NFC tidak didukung';
        log('Browser tidak mendukung Web NFC.');
    } else {
        nfcSupport.textContent = 'Siap';
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

    async function writeForUser(userId) {
        try {
            if (!('NDEFReader' in window)) return;

            const {res, data} = await postJson('{{ url('/api/encrypt-id') }}', { user_id: userId });
            if (!res.ok) {
                log('Gagal encrypt: ' + (data.error || res.status));
                return;
            }

            const encryptedId = data.encrypted_id;
            log('encrypted_id didapat, mulai write...');

            const ndef = new NDEFReader();
            await ndef.write({
                records: [
                    { recordType: "text", data: encryptedId, lang: "en" }
                ]
            });

            log('Write berhasil untuk user #' + userId);

            // Logging opsional (tidak wajib sukses)
            await postJson('{{ url('/api/nfc-write') }}', { encrypted_id: encryptedId }).catch(() => {});

        } catch (e) {
            console.error(e);
            log('Write gagal: ' + e.message);
        }
    }

    document.querySelectorAll('.write-btn').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const userId = btn.getAttribute('data-user-id');
            log('Mulai write untuk user #' + userId);
            await writeForUser(userId);
        });
    });
</script>
@endpush

