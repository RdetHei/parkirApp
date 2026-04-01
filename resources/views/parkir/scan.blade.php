@extends('layouts.app')

@section('title', 'Parking Scan RFID')

@section('content')
<div class="p-8 relative z-10 animate-fade-in">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-blue-500/10 text-blue-400 text-[10px] font-bold uppercase tracking-widest rounded-full border border-blue-500/20">
                        Terminal Operation
                    </span>
                    <div id="processing-indicator" class="hidden">
                        <span class="flex items-center gap-2 px-3 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                            <i class="fa-solid fa-circle-notch animate-spin"></i>
                            Processing Scan
                        </span>
                    </div>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-white uppercase">PARKING <span class="text-emerald-500">TERMINAL</span></h1>
                <p class="text-slate-400 text-sm mt-2 font-medium tracking-wide">Sistem otomatis masuk & keluar menggunakan teknologi RFID/NFC.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="px-6 py-4 bg-slate-950 border border-white/5 rounded-2xl flex items-center gap-4 shadow-xl">
                    <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">System Online</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
            <!-- Left: Scanner State -->
            <div class="lg:col-span-2 card-pro group overflow-hidden relative border-blue-500/10 flex flex-col items-center justify-center p-12 text-center">
                <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
                
                {{-- Animated Ring --}}
                <div class="relative w-32 h-32 mb-10">
                    <div class="absolute inset-0 rounded-full border-2 border-blue-500/20 animate-ping"></div>
                    <div class="absolute inset-4 rounded-full border-2 border-blue-500/30 animate-ping" style="animation-delay:0.3s"></div>
                    <div class="w-32 h-32 rounded-[2.5rem] flex items-center justify-center relative z-10 bg-slate-900 border border-white/10 shadow-2xl">
                        <i class="fa-solid fa-tower-broadcast text-4xl text-blue-400"></i>
                    </div>
                </div>

                <h2 class="text-2xl font-black text-white tracking-tight mb-3">TAP YOUR CARD</h2>
                <p class="text-slate-500 text-xs leading-relaxed max-w-[200px] mx-auto font-medium">Dekatkan kartu RFID/NFC Anda pada area scanner terminal.</p>

                <div class="w-full mt-10 space-y-4">
                    <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500 w-1/3 rounded-full animate-[scanBar_2s_infinite_linear]"></div>
                    </div>
                    <div class="flex justify-between items-center px-1">
                        <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest">Scanner Active</span>
                        <span id="last-scan-time" class="text-[9px] font-black text-slate-700 uppercase tracking-widest">Ready</span>
                    </div>
                </div>
            </div>

            <!-- Right: Result Panel -->
            <div class="lg:col-span-3 card-pro !p-0 overflow-hidden relative border-emerald-500/10 min-h-[500px] flex flex-col">
                <div id="result-topbar" class="h-1.5 w-full bg-white/5 transition-all duration-500"></div>
                
                {{-- Empty State --}}
                <div id="result-empty" class="flex-1 flex flex-col items-center justify-center p-12 text-center">
                    <div class="w-20 h-20 rounded-[2rem] bg-slate-950 border border-white/5 flex items-center justify-center mb-6 text-slate-800">
                        <i class="fa-solid fa-id-card text-3xl"></i>
                    </div>
                    <h3 class="text-xs font-black text-slate-600 uppercase tracking-[0.3em]">Menunggu Data...</h3>
                </div>

                {{-- Result Content --}}
                <div id="result-filled" class="hidden flex-1 flex flex-col p-8 sm:p-10 opacity-0 scale-95 transition-all duration-500">
                    <!-- User Header -->
                    <div class="flex items-center gap-8 mb-10">
                        <div class="relative shrink-0 group">
                            <div id="photo-glow" class="absolute -inset-3 bg-emerald-500/10 rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <img id="user-photo" src="" alt="" class="w-24 h-24 rounded-3xl object-cover relative z-10 border-2 border-white/10 shadow-2xl">
                            <div id="status-badge" class="absolute -bottom-2 -right-2 px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl z-20 border border-white/10">
                                --
                            </div>
                        </div>
                        <div class="min-w-0">
                            <h2 id="user-name" class="text-3xl font-black text-white tracking-tight truncate mb-1">—</h2>
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                <p id="user-status" class="text-[10px] font-black text-slate-500 uppercase tracking-widest"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Info Grid -->
                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div class="card-pro !p-6 bg-slate-950/50 border-white/5">
                            <p class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-3">Saldo NestonPay</p>
                            <p id="user-balance" class="text-2xl font-black text-white tracking-tighter">Rp 0</p>
                        </div>
                        <div class="card-pro !p-6 bg-slate-950/50 border-white/5">
                            <p class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-3">Biaya Layanan</p>
                            <p id="parking-fee" class="text-2xl font-black text-white tracking-tighter">—</p>
                        </div>
                    </div>

                    <!-- Vehicle Info -->
                    <div id="vehicle-info-container" class="hidden card-pro !p-6 bg-blue-500/5 border-blue-500/10 mb-8 relative overflow-hidden group">
                        <div class="absolute right-0 top-0 bottom-0 w-1.5 bg-blue-500 shadow-[0_0_15px_rgba(59,130,246,0.5)]"></div>
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-blue-500/10 rounded-2xl text-blue-400">
                                <i class="fa-solid fa-car-side text-lg"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest mb-1">Kendaraan Terdeteksi</p>
                                <p id="vehicle-name" class="text-lg font-black text-white tracking-tight">—</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Message -->
                    <div id="message-container" class="mt-auto w-full py-5 rounded-2xl text-xs font-black uppercase tracking-widest text-center shadow-xl transition-all">
                        --
                    </div>

                    <!-- Reset Progress -->
                    <div class="mt-8">
                        <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden">
                            <div id="countdown-bar" class="h-full bg-slate-700 w-full transition-all duration-[5000ms] linear"></div>
                        </div>
                        <p class="text-[9px] font-black text-slate-700 uppercase tracking-widest mt-3 text-center">Auto-reset dalam 5 detik</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden RFID input --}}
    <input type="text" id="rfid_uid" class="opacity-0 absolute w-0 h-0" autofocus autocomplete="off">
</div>

<style>
    @keyframes scanBar {
        0%   { left: -40%; }
        100% { left: 100%; }
    }
</style>

@push('scripts')
<script>
    const rfidInput        = document.getElementById('rfid_uid');
    const resultEmpty      = document.getElementById('result-empty');
    const resultFilled     = document.getElementById('result-filled');
    const resultTopbar     = document.getElementById('result-topbar');
    const userName         = document.getElementById('user-name');
    const userPhoto        = document.getElementById('user-photo');
    const userStatus       = document.getElementById('user-status');
    const userBalance      = document.getElementById('user-balance');
    const parkingFee       = document.getElementById('parking-fee');
    const vehicleName      = document.getElementById('vehicle-name');
    const vehicleContainer = document.getElementById('vehicle-info-container');
    const statusBadge      = document.getElementById('status-badge');
    const messageContainer = document.getElementById('message-container');
    const countdownBar     = document.getElementById('countdown-bar');
    const lastScanTime     = document.getElementById('last-scan-time');
    const indicator        = document.getElementById('processing-indicator');

    document.addEventListener('click', () => rfidInput.focus());
    rfidInput.focus();

    let isProcessing = false;
    let resetTimer;
    let timer;

    rfidInput.addEventListener('input', function(e) {
        if (isProcessing) return;
        clearTimeout(timer);
        const uid = e.target.value.trim();
        timer = setTimeout(() => {
            if (uid.length >= 4) { processScan(uid); e.target.value = ''; }
        }, 300);
    });

    rfidInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (isProcessing) return;
            clearTimeout(timer);
            const uid = e.target.value.trim();
            if (uid.length >= 4) { processScan(uid); e.target.value = ''; }
        }
    });

    async function processScan(uid) {
        isProcessing = true;
        rfidInput.disabled = true;
        indicator.classList.remove('hidden');

        lastScanTime.innerText = 'TAP: ' + new Date().toLocaleTimeString('id-ID');

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

            const ct = response.headers.get('content-type');
            if (!ct || !ct.includes('application/json')) throw new Error('Sistem bermasalah (Server Error)');
            const data = await response.json();
            showResult(data, response.ok);

        } catch (error) {
            showResult({ success: false, message: error.message || 'Koneksi error' }, false);
        } finally {
            indicator.classList.add('hidden');
            clearTimeout(resetTimer);
            resetTimer = setTimeout(() => {
                hideResult();
                isProcessing = false;
                rfidInput.disabled = false;
                rfidInput.focus();
            }, 5000);
        }
    }

    function showResult(data, isOk) {
        resultEmpty.classList.add('hidden');
        resultFilled.classList.remove('hidden');
        
        // Animation
        setTimeout(() => {
            resultFilled.classList.remove('opacity-0', 'scale-95');
            countdownBar.style.width = '0%';
        }, 50);

        if (data.user) {
            userName.innerText    = data.user.name;
            userPhoto.src         = data.user.photo || '{{ asset('images/default-user.png') }}';
            userStatus.innerText  = data.user.status;
            userBalance.innerText = (data.user?.balance || 0).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });
            parkingFee.innerText  = data.amount ? data.amount.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }) : '—';

            if (data.user?.vehicle) {
                vehicleName.innerText = data.user.vehicle;
                vehicleContainer.classList.remove('hidden');
            } else {
                vehicleContainer.classList.add('hidden');
            }

            if (data.user?.type === 'IN') {
                statusBadge.innerText = 'IN';
                statusBadge.className = 'absolute -bottom-2 -right-2 px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl z-20 border border-white/10 bg-emerald-500 text-slate-950';
                resultTopbar.className = 'h-1.5 w-full bg-emerald-500 transition-all duration-500 shadow-[0_0_15px_rgba(16,185,129,0.5)]';
                messageContainer.className = 'mt-auto w-full py-5 rounded-2xl text-xs font-black uppercase tracking-widest text-center shadow-xl transition-all bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
            } else if (data.user?.type === 'OUT') {
                statusBadge.innerText = 'OUT';
                statusBadge.className = 'absolute -bottom-2 -right-2 px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl z-20 border border-white/10 bg-blue-500 text-white';
                resultTopbar.className = 'h-1.5 w-full bg-blue-500 transition-all duration-500 shadow-[0_0_15px_rgba(59,130,246,0.5)]';
                messageContainer.className = 'mt-auto w-full py-5 rounded-2xl text-xs font-black uppercase tracking-widest text-center shadow-xl transition-all bg-blue-500/10 text-blue-400 border border-blue-500/20';
            } else {
                statusBadge.innerText = 'ERR';
                statusBadge.className = 'absolute -bottom-2 -right-2 px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl z-20 border border-white/10 bg-rose-500 text-white';
                resultTopbar.className = 'h-1.5 w-full bg-rose-500 transition-all duration-500 shadow-[0_0_15px_rgba(244,63,94,0.5)]';
                messageContainer.className = 'mt-auto w-full py-5 rounded-2xl text-xs font-black uppercase tracking-widest text-center shadow-xl transition-all bg-rose-500/10 text-rose-400 border border-rose-500/20';
            }
        } else {
            resultTopbar.className = 'h-1.5 w-full bg-rose-500 transition-all duration-500';
            messageContainer.className = 'mt-auto w-full py-5 rounded-2xl text-xs font-black uppercase tracking-widest text-center shadow-xl transition-all bg-rose-500/10 text-rose-400 border border-rose-500/20';
            userName.innerText = 'Unknown';
            userStatus.innerText = 'Error';
        }

        messageContainer.innerText = data.message;
    }

    function hideResult() {
        resultFilled.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            resultFilled.classList.add('hidden');
            resultEmpty.classList.remove('hidden');
            resultTopbar.className = 'h-1.5 w-full bg-white/5 transition-all duration-500';
            countdownBar.style.width = '100%';
        }, 500);
    }
</script>
@endpush
@endsection
