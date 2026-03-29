@extends('layouts.app')

@section('title', 'Scan Kartu RFID')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh]">
    <div class="bg-white p-8 rounded-3xl shadow-xl w-full max-w-md text-center">
        <div class="mb-6">
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12 12 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">SCAN KARTU SEKARANG</h1>
            <p class="text-gray-500 text-sm">Mendaftarkan RFID untuk: <strong>{{ $user->name }}</strong></p>
        </div>

        <div id="status-container" class="hidden mb-6 p-4 rounded-xl transition-all duration-300">
            <p id="status-message" class="text-sm font-semibold"></p>
        </div>

        <form id="rfid-form">
            @csrf
            <input type="text" id="rfid_uid" name="rfid_uid" class="opacity-0 absolute" autofocus autocomplete="off">
            <div class="mt-4">
                <div class="animate-pulse flex space-x-4 justify-center">
                    <div class="h-2 w-2 bg-green-400 rounded-full"></div>
                    <div class="h-2 w-2 bg-green-400 rounded-full"></div>
                    <div class="h-2 w-2 bg-green-400 rounded-full"></div>
                </div>
                <p class="text-[10px] text-gray-400 mt-2 uppercase tracking-widest font-bold">Menunggu input dari scanner...</p>
            </div>
        </form>

        <div class="mt-8">
            <a href="{{ route('users.show', $user->id) }}" class="text-xs font-bold text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest">
                Lewati pendaftaran RFID
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const rfidInput = document.getElementById('rfid_uid');
    const statusContainer = document.getElementById('status-container');
    const statusMessage = document.getElementById('status-message');

    // Auto focus input
    document.addEventListener('click', () => rfidInput.focus());
    rfidInput.focus();
    
    let timer;
    rfidInput.addEventListener('input', function(e) {
        clearTimeout(timer);
        const uid = e.target.value.trim();
        
        timer = setTimeout(() => {
            if (uid.length >= 4) {
                saveRfid(uid);
                e.target.value = '';
            }
        }, 300);
    });

    rfidInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(timer);
            const uid = e.target.value.trim();
            if (uid.length >= 4) {
                saveRfid(uid);
                e.target.value = '';
            }
        }
    });

    async function saveRfid(uid) {
        statusContainer.classList.remove('hidden', 'bg-red-100', 'bg-green-100', 'bg-blue-100');
        statusContainer.classList.add('bg-blue-100', 'block');
        statusMessage.innerText = 'Memproses...';
        statusMessage.className = 'text-blue-700 text-sm font-semibold';

        try {
            const response = await fetch("{{ route('users.save-rfid', $user->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ rfid_uid: uid })
            });

            // Handle non-JSON responses (like 500 errors or redirects)
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                throw new Error("Sistem bermasalah (Server Error)");
            }

            const data = await response.json();

            if (response.ok) {
                statusContainer.classList.replace('bg-blue-100', 'bg-green-100');
                statusMessage.innerText = data.message;
                statusMessage.className = 'text-green-700 text-sm font-semibold';
                
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            } else {
                throw new Error(data.message || data.error || 'Terjadi kesalahan');
            }
        } catch (error) {
            statusContainer.classList.replace('bg-blue-100', 'bg-red-100');
            statusMessage.innerText = error.message;
            statusMessage.className = 'text-red-700 text-sm font-semibold';
            rfidInput.focus();
        }
    }
@endpush
@endsection
