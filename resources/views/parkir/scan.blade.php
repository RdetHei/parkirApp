@extends('layouts.app')

@section('title', 'Parking Scan RFID')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[70vh] px-4">
    <div class="bg-white p-10 rounded-[2.5rem] shadow-2xl w-full max-w-2xl text-center border border-gray-100">
        <div class="mb-10">
            <div class="w-28 h-28 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                <svg class="w-14 h-14 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-3.682A14.29 14.29 0 005.34 20M12 11c1.744 2.772 2.753 6.054 2.753 9.571m3.44-3.682c.535 1.1.883 2.267 1.023 3.49M12 11V3m0 0L9 6m3-3l3 3"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-black text-gray-900 mb-3 tracking-tight">TAP KARTU UNTUK MASUK / KELUAR</h1>
            <p class="text-gray-500 text-lg font-medium">Dekatkan kartu Anda pada scanner</p>
        </div>

        <!-- Hidden Input for RFID -->
        <input type="text" id="rfid_uid" class="opacity-0 absolute" autofocus autocomplete="off">

        <!-- Result Container -->
        <div id="result-container" class="hidden transform transition-all duration-500 scale-95 opacity-0">
            <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 flex flex-col items-center">
                <div class="mb-6 relative">
                    <img id="user-photo" src="" alt="User Photo" 
                         class="w-32 h-32 rounded-3xl object-cover border-4 border-white shadow-lg">
                    <div id="status-badge" class="absolute -bottom-3 -right-3 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-md">
                        STATUS
                    </div>
                </div>
                
                <h2 id="user-name" class="text-2xl font-bold text-gray-900 mb-1">NAMA USER</h2>
                <p id="user-status" class="text-blue-600 font-semibold mb-4"></p>
                
                <div class="grid grid-cols-2 gap-4 w-full max-w-sm mt-4">
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-50">
                        <p class="text-xs text-gray-400 uppercase font-bold mb-1">Saldo</p>
                        <p id="user-balance" class="text-xl font-bold text-gray-900">Rp 0</p>
                    </div>
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-50">
                        <p class="text-xs text-gray-400 uppercase font-bold mb-1">Biaya</p>
                        <p id="parking-fee" class="text-xl font-bold text-gray-900">-</p>
                    </div>
                </div>

                <div id="message-container" class="mt-6 p-4 w-full rounded-2xl text-center font-bold">
                    Pesan Berhasil/Gagal
                </div>
            </div>
        </div>

        <!-- Default State / Scanner Waiting -->
        <div id="scanner-waiting" class="mt-8 flex flex-col items-center">
            <div class="relative w-full max-w-xs h-1 bg-gray-100 rounded-full overflow-hidden">
                <div class="absolute top-0 left-0 h-full bg-blue-500 w-1/3 animate-[scan_2s_infinite_linear]"></div>
            </div>
            <p class="text-sm text-gray-400 mt-4 font-medium uppercase tracking-widest">Scanning active...</p>
        </div>
    </div>
</div>

<style>
    @keyframes scan {
        0% { left: -30%; }
        100% { left: 100%; }
    }
</style>

@push('scripts')
<script>
    const rfidInput = document.getElementById('rfid_uid');
    const resultContainer = document.getElementById('result-container');
    const scannerWaiting = document.getElementById('scanner-waiting');
    const userName = document.getElementById('user-name');
    const userPhoto = document.getElementById('user-photo');
    const userStatus = document.getElementById('user-status');
    const userBalance = document.getElementById('user-balance');
    const parkingFee = document.getElementById('parking-fee');
    const statusBadge = document.getElementById('status-badge');
    const messageContainer = document.getElementById('message-container');

    // Auto focus input
    document.addEventListener('click', () => rfidInput.focus());
    rfidInput.focus();

    let isProcessing = false;

    let timer;
    rfidInput.addEventListener('input', function(e) {
        if (isProcessing) return;
        
        clearTimeout(timer);
        const uid = e.target.value.trim();
        
        // Sebagian besar scanner mengirim "Enter" di akhir.
        // Tapi untuk jaga-jaga, kita pakai debounce 300ms 
        // untuk mendeteksi akhir input dari keyboard wedge.
        timer = setTimeout(() => {
            if (uid.length >= 4) {
                processScan(uid);
                e.target.value = '';
            }
        }, 300);
    });

    // Juga tangani jika scanner mengirim "Enter"
    rfidInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (isProcessing) return;
            
            clearTimeout(timer);
            const uid = e.target.value.trim();
            if (uid.length >= 4) {
                processScan(uid);
                e.target.value = '';
            }
        }
    });

    async function processScan(uid) {
        isProcessing = true;
        rfidInput.disabled = true;
        
        // UI Reset & Loading State
        scannerWaiting.classList.add('opacity-50');

        try {
            const response = await fetch("{{ route('api.parkir.rfid-scan') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ rfid_uid: uid })
            });

            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                throw new Error("Sistem bermasalah (Server Error)");
            }

            const data = await response.json();

            // Display Results
            showResult(data, response.ok);

        } catch (error) {
            console.error('Error:', error);
            showResult({ 
                success: false, 
                message: error.message || 'Koneksi error atau sistem bermasalah' 
            }, false);
        } finally {
            setTimeout(() => {
                isProcessing = false;
                rfidInput.disabled = false;
                rfidInput.focus();
                scannerWaiting.classList.remove('opacity-50');
            }, 3000); // Wait 3 seconds before allowing next scan
        }
    }

    function showResult(data, isOk) {
        // Reset animation
        resultContainer.classList.remove('scale-100', 'opacity-100');
        resultContainer.classList.add('scale-95', 'opacity-0', 'hidden');

        if (data.user) {
            userName.innerText = data.user.name;
            userPhoto.src = data.user.photo;
            userStatus.innerText = data.user.status;
            userBalance.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.user.balance);
            parkingFee.innerText = data.amount ? 'Rp ' + new Intl.NumberFormat('id-ID').format(data.amount) : '-';
            
            // Status Badge
            if (data.user.status.includes('Masuk')) {
                statusBadge.className = 'absolute -bottom-3 -right-3 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-md bg-green-500 text-white';
                statusBadge.innerText = 'IN';
            } else {
                statusBadge.className = 'absolute -bottom-3 -right-3 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-md bg-blue-500 text-white';
                statusBadge.innerText = 'OUT';
            }
        }

        // Message Container
        messageContainer.innerText = data.message;
        if (isOk) {
            messageContainer.className = 'mt-6 p-4 w-full rounded-2xl text-center font-bold bg-green-100 text-green-700';
        } else {
            messageContainer.className = 'mt-6 p-4 w-full rounded-2xl text-center font-bold bg-red-100 text-red-700';
        }

        // Show container
        resultContainer.classList.remove('hidden');
        setTimeout(() => {
            resultContainer.classList.remove('scale-95', 'opacity-0');
            resultContainer.classList.add('scale-100', 'opacity-100');
        }, 10);

        // Auto hide after 5 seconds
        setTimeout(() => {
            resultContainer.classList.add('scale-95', 'opacity-0');
            setTimeout(() => resultContainer.classList.add('hidden'), 500);
        }, 5000);
    }
</script>
@endpush
@endsection
