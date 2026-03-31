@extends('layouts.app')

@section('title', 'Parking Scan RFID')

@section('content')
<div class="flex items-center justify-center min-h-[80vh] px-4" style="background:#020617;">

    {{--
        VARIANT 2: Split layout — kiri scanner state, kanan result panel
        Keduanya selalu visible, hasil muncul di kanan.
        Memberi kesan "terminal" / dashboard kiosk yang profesional.
    --}}
    <div class="w-full max-w-3xl grid grid-cols-2 gap-5">

        {{-- ── LEFT: Scanner panel ── --}}
        <div class="rounded-2xl flex flex-col items-center justify-center py-12 px-8 text-center"
             style="background:#0d1526;border:1px solid rgba(255,255,255,0.07);">

            {{-- Animated ring icon --}}
            <div class="relative w-20 h-20 mb-8">
                <div class="absolute inset-0 rounded-full border border-blue-500/20 animate-ping"></div>
                <div class="absolute inset-2 rounded-full border border-blue-500/30 animate-ping" style="animation-delay:0.3s"></div>
                <div class="w-20 h-20 rounded-full flex items-center justify-center relative z-10"
                     style="background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.25);">
                    <svg class="w-9 h-9 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-3.682A14.29 14.29 0 005.34 20M12 11c1.744 2.772 2.753 6.054 2.753 9.571m3.44-3.682c.535 1.1.883 2.267 1.023 3.49M12 11V3m0 0L9 6m3-3l3 3"/>
                    </svg>
                </div>
            </div>

            <h1 class="text-xl font-bold text-white tracking-tight mb-2">Tap Kartu RFID</h1>
            <p class="text-slate-500 text-xs leading-relaxed mb-8">Dekatkan kartu Anda pada scanner untuk masuk atau keluar dari area parkir</p>

            {{-- Scan bar --}}
            <div class="w-full relative h-px overflow-hidden rounded-full mb-2" style="background:rgba(255,255,255,0.05);">
                <div class="absolute top-0 h-full rounded-full bg-blue-500"
                     style="width:40%;animation:scanBar 2s infinite linear;"></div>
            </div>
            <p class="text-[10px] font-bold text-slate-600 uppercase tracking-widest">Scanner aktif</p>

            {{-- Last scan time --}}
            <div class="mt-8 pt-6 border-t w-full text-left" style="border-color:rgba(255,255,255,0.05);">
                <p class="text-[10px] font-bold text-slate-600 uppercase tracking-widest mb-1">Scan Terakhir</p>
                <p id="last-scan-time" class="text-xs text-slate-500">—</p>
            </div>
        </div>

        {{-- ── RIGHT: Result panel ── --}}
        <div class="rounded-2xl flex flex-col overflow-hidden"
             style="background:#0d1526;border:1px solid rgba(255,255,255,0.07);">

            {{-- Color accent top bar --}}
            <div id="result-topbar" class="h-1 w-full transition-all duration-500" style="background:rgba(255,255,255,0.05);"></div>

            {{-- Empty / waiting state --}}
            <div id="result-empty" class="flex-1 flex flex-col items-center justify-center py-12 px-8 text-center">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4"
                     style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);">
                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <p class="text-sm text-slate-600">Menunggu scan kartu...</p>
            </div>

            {{-- Result filled state (hidden) --}}
            <div id="result-filled" class="hidden flex-1 flex flex-col opacity-0 transition-all duration-500 scale-95">
                <div class="flex-1 p-6 flex flex-col">

                    {{-- User info --}}
                    <div class="flex items-center gap-4 mb-6">
                        <div class="relative shrink-0">
                            <img id="user-photo" src="" alt=""
                                 class="w-14 h-14 rounded-xl object-cover"
                                 style="border:1px solid rgba(255,255,255,0.1);">
                            <div id="status-badge"
                                 class="absolute -bottom-1.5 -right-1.5 px-2 py-px rounded-full text-[9px] font-bold uppercase tracking-widest">
                                IN
                            </div>
                        </div>
                        <div class="min-w-0">
                            <h2 id="user-name" class="text-base font-bold text-white leading-tight">—</h2>
                            <p id="user-status" class="text-xs text-slate-400 mt-0.5"></p>
                        </div>
                    </div>

                    {{-- Info cards --}}
                    <div class="grid grid-cols-2 gap-3 mb-5">
                        <div class="rounded-xl p-3.5" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.06);">
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">Saldo</p>
                            <p id="user-balance" class="text-base font-bold text-white">Rp 0</p>
                        </div>
                        <div class="rounded-xl p-3.5" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.06);">
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">Biaya</p>
                            <p id="parking-fee" class="text-base font-bold text-white">—</p>
                        </div>
                    </div>

                    {{-- Vehicle Info (New) --}}
                    <div id="vehicle-info-container" class="rounded-xl p-3.5 mb-5 hidden" style="background:rgba(59,130,246,0.05);border:1px solid rgba(59,130,246,0.1);">
                        <p class="text-[9px] font-bold text-blue-400 uppercase tracking-widest mb-1">Kendaraan Terdeteksi</p>
                        <p id="vehicle-name" class="text-sm font-bold text-white">—</p>
                    </div>

                    {{-- Message --}}
                    <div id="message-container"
                         class="w-full rounded-xl px-4 py-3 text-sm font-semibold text-center"></div>
                </div>

                {{-- Countdown bar --}}
                <div class="px-6 pb-5">
                    <div class="relative h-px rounded-full overflow-hidden" style="background:rgba(255,255,255,0.05);">
                        <div id="countdown-bar" class="absolute top-0 left-0 h-full bg-slate-600 rounded-full"
                             style="width:100%;transition:width 5s linear;"></div>
                    </div>
                    <p class="text-[10px] text-slate-600 uppercase tracking-widest mt-2">Direset otomatis dalam 5 detik</p>
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

        lastScanTime.innerText = new Date().toLocaleTimeString('id-ID');

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
            showResult({ success: false, message: error.message || 'Koneksi error atau sistem bermasalah' }, false);
        } finally {
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
        if (data.user) {
            userName.innerText    = data.user.name;
            userPhoto.src         = data.user.photo;
            userStatus.innerText  = data.user.status;
            userBalance.innerText = 'Rp ' + (data.user?.balance || 0).toLocaleString('id-ID');
            parkingFee.innerText  = data.amount ? 'Rp ' + data.amount.toLocaleString('id-ID') : '—';

            if (data.user?.vehicle) {
                vehicleName.innerText = data.user.vehicle;
                vehicleContainer.classList.remove('hidden');
            } else {
                vehicleContainer.classList.add('hidden');
            }

            if (data.user?.type === 'IN') {
                statusBadge.innerText = 'IN';
                statusBadge.style.background = '#10b981'; // emerald-500
                statusBadge.style.color = '#fff';
                resultTopbar.style.background = '#10b981';
            } else if (data.user?.type === 'OUT') {
                statusBadge.innerText = 'OUT';
                statusBadge.style.background = '#3b82f6'; // blue-500
                statusBadge.style.color = '#fff';
                resultTopbar.style.background = '#3b82f6';
            } else {
                statusBadge.innerText = 'ERR';
                statusBadge.style.background = '#ef4444'; // red-500
                statusBadge.style.color = '#fff';
                resultTopbar.style.background = '#ef4444';
            }
        } else {
            resultTopbar.style.background = '#ef4444';
        }

        messageContainer.innerText = data.message;
        messageContainer.className = 'w-full rounded-xl px-4 py-3 text-sm font-semibold text-center '
            + (isOk ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-400'
                    : 'bg-red-500/10 border border-red-500/20 text-red-400');

        resultEmpty.classList.add('hidden');
        resultFilled.classList.remove('hidden');
        setTimeout(() => {
            resultFilled.classList.remove('opacity-0', 'scale-95');
            resultFilled.classList.add('opacity-100', 'scale-100');
        }, 10);

        // Countdown bar
        countdownBar.style.width = '100%';
        setTimeout(() => { countdownBar.style.width = '0%'; }, 50);
    }

    function hideResult() {
        resultFilled.classList.remove('opacity-100', 'scale-100');
        resultFilled.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            resultFilled.classList.add('hidden');
            resultEmpty.classList.remove('hidden');
            resultTopbar.style.background = 'rgba(255,255,255,0.05)';
            countdownBar.style.transition = 'none';
            countdownBar.style.width = '100%';
            setTimeout(() => { countdownBar.style.transition = 'width 5s linear'; }, 50);
        }, 500);
    }
</script>
@endpush
@endsection
