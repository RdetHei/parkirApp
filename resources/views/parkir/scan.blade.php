@extends('layouts.app')

@section('title', 'RFID Terminal')

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
                <p class="text-slate-400 text-sm mt-2 font-medium tracking-wide">Sistem otomatis masuk & keluar menggunakan teknologi RFID.</p>
            </div>

            <div class="flex items-center gap-4">
                <div class="px-6 py-4 bg-slate-950 border border-white/5 rounded-2xl flex items-center gap-4 shadow-xl">
                    <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">System Online</span>
                </div>
            </div>
        </div>

        @php
            $isPetugas = auth()->user()->role === 'petugas';
        @endphp

        <div class="mb-8 rounded-3xl border border-white/10 bg-slate-950/80 p-6 sm:p-8">
            @if($operationalArea)
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Area terminal (auto slot)</p>
                        <p class="text-white font-bold text-lg">{{ $operationalArea->nama_area }} <span class="text-slate-500 font-mono text-sm font-normal">· {{ $operationalArea->map_code }}</span></p>
                    </div>
                    <form method="POST" action="{{ route('operational-area.clear') }}" class="shrink-0">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-xl border border-white/15 text-slate-300 text-[10px] font-black uppercase tracking-widest hover:bg-white/5 transition-colors">
                            Ganti kode peta
                        </button>
                    </form>
                </div>
            @else
                <div class="flex flex-col lg:flex-row lg:items-end gap-6 justify-between">
                    <div class="max-w-xl">
                        <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-2">Kode peta terminal</p>
                        @if($isPetugas)
                            <p class="text-slate-400 text-sm leading-relaxed">Wajib isi dulu: masukkan <strong class="text-white">Kode Peta</strong> yang diberikan admin (sama dengan di Area Parkir). Tanpa ini scan RFID tidak akan menempatkan kendaraan ke slot otomatis.</p>
                        @else
                            <p class="text-slate-400 text-sm leading-relaxed">Opsional jika akun Anda punya area default; jika tidak, masukkan <strong class="text-white">Kode Peta</strong> untuk check-in dengan auto slot.</p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('operational-area.set') }}" class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto shrink-0">
                        @csrf
                        <input type="text" name="kode_peta" value="{{ old('kode_peta') }}" required placeholder="Kode peta"
                               class="min-w-[220px] px-5 py-3.5 bg-slate-900 border border-white/10 rounded-xl text-white text-sm placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500/50">
                        <button type="submit" class="px-8 py-3.5 bg-blue-500 text-slate-950 font-black text-xs uppercase tracking-widest rounded-xl hover:bg-blue-400 transition-colors">
                            Terapkan
                        </button>
                    </form>
                </div>
                @if($errors->has('kode_peta'))
                    <p class="mt-4 text-sm text-rose-400 font-medium">{{ $errors->first('kode_peta') }}</p>
                @endif
            @endif
        </div>

        <div id="rfid-terminal-panel" class="grid grid-cols-1 lg:grid-cols-5 gap-8 @if($isPetugas && !$operationalArea) opacity-40 pointer-events-none select-none @endif">
            <!-- Left: Scanner State -->
            <div class="lg:col-span-2 card-pro group overflow-hidden relative border-blue-500/10 flex flex-col items-center justify-center p-12 text-center">
                <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>

                {{-- Animated Ring --}}
                <div class="relative w-32 h-32 mb-10">
                    <div class="absolute inset-0 rounded-full border-2 border-blue-500/20 animate-ping"></div>
                    <div class="absolute inset-4 rounded-full border-2 border-blue-500/30 animate-ping" style="animation-delay:0.3s"></div>
                    <div class="w-32 h-32 rounded-[2.5rem] flex items-center justify-center relative z-10 bg-slate-900 border border-white/10 shadow-2xl">
                        <i class="fa-solid fa-tower text-4xl text-blue-400"></i>
                    </div>
                </div>

                <h2 class="text-2xl font-black text-white tracking-tight mb-3">TAP YOUR CARD</h2>
                <p class="text-slate-500 text-xs leading-relaxed max-w-[200px] mx-auto font-medium">Dekatkan kartu RFID Anda pada area scanner terminal.</p>

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
                    <p class="mt-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Scan 1: Check-In</p>
                    <p class="mt-1 text-[10px] font-black text-slate-600 uppercase tracking-widest">Scan 2: Check-Out</p>
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
                    <div id="message-container" class="mt-6 w-full py-5 rounded-2xl text-xs font-black uppercase tracking-widest text-center shadow-xl transition-all">
                        --
                    </div>

                    <!-- Workflow Hint -->
                    <div id="workflow-hint"
                         class="mt-3 w-full py-3 px-4 rounded-2xl border border-white/10 bg-white/5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">
                        Siap scan kartu
                    </div>

                    <!-- Payment Actions (only when transaction needs payment) -->
                    <div id="payment-actions" class="hidden w-full mt-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div id="nestonpay-action" class="card-pro !p-0 overflow-hidden border-white/5 bg-slate-950/30">
                                <div class="px-4 py-3 border-b border-white/5 bg-white/[0.02]">
                                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">NestonPay (Saldo)</p>
                                </div>
                                <div class="p-4">
                                    <form id="nestonpay-payment-form" method="POST" action="{{ route('user.saldo.pay', ['id_parkir' => '__ID__']) }}">
                                        @csrf
                                        <button type="submit"
                                                class="w-full py-3 bg-emerald-500/10 hover:bg-emerald-500 text-emerald-500 hover:text-slate-950 border border-emerald-500/20 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all active:scale-[0.99]">
                                            Bayar NestonPay
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div id="midtrans-action" class="card-pro !p-0 overflow-hidden border-white/5 bg-slate-950/30">
                                <div class="px-4 py-3 border-b border-white/5 bg-white/[0.02]">
                                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Midtrans (Online)</p>
                                </div>
                                <div class="p-4">
                                    <a id="midtrans-payment-link"
                                       href="{{ route('payment.create', ['id_parkir' => '__ID__']) }}"
                                       class="w-full block text-center py-3 bg-blue-500/10 hover:bg-blue-500 text-blue-400 hover:text-slate-950 border border-blue-500/20 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all active:scale-[0.99]">
                                        Bayar Midtrans
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 text-center">
                            <p id="payment-amount-text" class="text-[9px] font-black text-slate-600 uppercase tracking-widest">Rp 0</p>
                        </div>
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
    const workflowHint = document.getElementById('workflow-hint');
    const countdownBar     = document.getElementById('countdown-bar');
    const lastScanTime     = document.getElementById('last-scan-time');
    const indicator        = document.getElementById('processing-indicator');
    const paymentActions   = document.getElementById('payment-actions');
    const paymentAmountText = document.getElementById('payment-amount-text');
    const nestonPayAction = document.getElementById('nestonpay-action');
    const midtransAction = document.getElementById('midtrans-action');
    const nestonPayPaymentForm = document.getElementById('nestonpay-payment-form');
    const midtransPaymentLink = document.getElementById('midtrans-payment-link');
    const nestonPayUrlTemplate = @json(route('user.saldo.pay', ['id_parkir' => '__ID__']));
    const midtransUrlTemplate = @json(route('payment.create', ['id_parkir' => '__ID__']));
    const accessScanUrl = @json(route('api.rfid.access.scan'));
    const mustEnterMapBeforeScan = @json($isPetugas && !$operationalArea);

    document.addEventListener('click', () => { if (!mustEnterMapBeforeScan) rfidInput.focus(); });
    if (!mustEnterMapBeforeScan) rfidInput.focus();

    let isProcessing = false;
    let resetTimer;
    let hideUiTimer;
    let timer;
    let keepResult = false;
    let lastUid = '';
    let lastUidAt = 0;

    function cancelHideUiAnimation() {
        if (hideUiTimer) {
            clearTimeout(hideUiTimer);
            hideUiTimer = null;
        }
    }

    rfidInput.addEventListener('input', function(e) {
        if (mustEnterMapBeforeScan) return;
        if (isProcessing) return;
        clearTimeout(timer);
        const uid = e.target.value.trim();
        // Debounce pendek: wedge RFID biasanya mengirim UID cepat; 300ms terasa “ngelag”.
        timer = setTimeout(() => {
            if (uid.length >= 4) { processScan(uid); e.target.value = ''; }
        }, 80);
    });

    rfidInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (mustEnterMapBeforeScan) return;
            if (isProcessing) return;
            clearTimeout(timer);
            const uid = e.target.value.trim();
            if (uid.length >= 4) { processScan(uid); e.target.value = ''; }
        }
    });

    async function processScan(uid) {
        if (mustEnterMapBeforeScan) {
            showResult({ success: false, message: 'Masukkan Kode Peta di atas terlebih dahulu agar auto slot berfungsi.', user: { name: 'Terminal', status: 'Kode peta wajib' } }, false);
            return;
        }
        const nowMs = Date.now();
        if (uid === lastUid && (nowMs - lastUidAt) < 1500) {
            showResult({ success: false, message: 'Scan terdeteksi ganda. Tunggu sebentar lalu scan ulang.', user: { name: 'Scanner', status: 'Anti double-scan' } }, false);
            return;
        }
        lastUid = uid;
        lastUidAt = nowMs;

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

            const raw = await response.text();
            let data;
            try {
                data = raw ? JSON.parse(raw) : {};
            } catch (e) {
                throw new Error('Sistem bermasalah (Server Error: respons tidak valid)');
            }

            // Best-effort: set session akses RFID (tanpa mengganggu alur check-in/out).
            fetch(accessScanUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ rfid_uid: uid })
            }).catch(() => {});

            showResult(data, response.ok);

        } catch (error) {
            showResult({ success: false, message: error.message || 'Koneksi error' }, false);
        } finally {
            indicator.classList.add('hidden');
            clearTimeout(resetTimer);
            if (keepResult) {
                isProcessing = false;
                rfidInput.disabled = false;
                rfidInput.focus();
                return;
            }

            resetTimer = setTimeout(() => {
                hideResult();
                isProcessing = false;
                rfidInput.disabled = false;
                rfidInput.focus();
            }, 5000);
        }
    }

    function showResult(data, isOk) {
        cancelHideUiAnimation();

        resultEmpty.classList.add('hidden');
        resultFilled.classList.remove('hidden');
        // Tampilkan langsung: timer hideResult yang tertunda tidak boleh mengalahkan scan baru.
        resultFilled.classList.remove('opacity-0', 'scale-95');
        countdownBar.style.width = '0%';

        keepResult = !!data.payment_required;
        if (paymentActions) paymentActions.classList.toggle('hidden', !keepResult);

        if (!keepResult) {
            if (nestonPayAction) nestonPayAction.classList.remove('hidden');
            if (midtransAction) midtransAction.classList.remove('hidden');
        }

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
                messageContainer.className = 'mt-6 w-full py-5 rounded-2xl text-xs font-black uppercase tracking-widest text-center shadow-xl transition-all bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
                if (workflowHint) {
                    workflowHint.innerText = 'Check-in sukses. Scan berikutnya untuk check-out.';
                }
            } else if (data.user?.type === 'OUT') {
                statusBadge.innerText = 'OUT';
                statusBadge.className = 'absolute -bottom-2 -right-2 px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl z-20 border border-white/10 bg-blue-500 text-white';
                resultTopbar.className = 'h-1.5 w-full bg-blue-500 transition-all duration-500 shadow-[0_0_15px_rgba(59,130,246,0.5)]';
                messageContainer.className = 'mt-6 w-full py-5 rounded-2xl text-xs font-black uppercase tracking-widest text-center shadow-xl transition-all bg-blue-500/10 text-blue-400 border border-blue-500/20';
                if (workflowHint) {
                    workflowHint.innerText = 'Check-out selesai. Scan berikutnya untuk check-in baru.';
                }
            } else {
                statusBadge.innerText = 'ERR';
                statusBadge.className = 'absolute -bottom-2 -right-2 px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl z-20 border border-white/10 bg-rose-500 text-white';
                resultTopbar.className = 'h-1.5 w-full bg-rose-500 transition-all duration-500 shadow-[0_0_15px_rgba(244,63,94,0.5)]';
                messageContainer.className = 'mt-6 w-full py-5 rounded-2xl text-xs font-black uppercase tracking-widest text-center shadow-xl transition-all bg-rose-500/10 text-rose-400 border border-rose-500/20';
                if (workflowHint) {
                    workflowHint.innerText = 'Periksa kartu atau data kendaraan user.';
                }
            }

            if (data.payment_required) {
                const p = data.payment_required;
                const amount = p.amount ?? data.amount ?? 0;
                const idParkir = p.id_parkir;

                if (paymentAmountText) {
                    paymentAmountText.innerText = amount.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });
                }

                const canNestonPay = p.methods?.nestonpay !== false;
                const canMidtrans = p.methods?.midtrans !== false;

                if (nestonPayAction) nestonPayAction.classList.toggle('hidden', !canNestonPay);
                if (midtransAction) midtransAction.classList.toggle('hidden', !canMidtrans);

                if (nestonPayPaymentForm) {
                    nestonPayPaymentForm.action = nestonPayUrlTemplate.replace('__ID__', idParkir);
                }
                if (midtransPaymentLink) {
                    midtransPaymentLink.href = midtransUrlTemplate.replace('__ID__', idParkir);
                }

                // Override visual state for "payment pending"
                statusBadge.innerText = 'PAY';
                statusBadge.className = 'absolute -bottom-2 -right-2 px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl z-20 border border-white/10 bg-amber-500 text-slate-950';
                resultTopbar.className = 'h-1.5 w-full bg-amber-500 transition-all duration-500 shadow-[0_0_15px_rgba(245,158,11,0.5)]';
                messageContainer.className = 'mt-6 w-full py-5 rounded-2xl text-xs font-black uppercase tracking-widest text-center shadow-xl transition-all bg-amber-500/10 text-amber-400 border border-amber-500/20';
                if (workflowHint) {
                    workflowHint.innerText = 'Check-out pending pembayaran. Selesaikan pembayaran dulu.';
                }

                // Notifikasi instan untuk operator/user agar pembayaran tidak terlewat.
                if (window.Notification && Notification.permission === 'granted') {
                    new Notification('Pembayaran Parkir Dibutuhkan', {
                        body: `Tagihan: ${amount.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })}`,
                        icon: '{{ asset('images/neston.png') }}'
                    });
                } else if (window.Notification && Notification.permission !== 'denied') {
                    Notification.requestPermission();
                }
            }
        } else {
            resultTopbar.className = 'h-1.5 w-full bg-rose-500 transition-all duration-500';
            messageContainer.className = 'mt-6 w-full py-5 rounded-2xl text-xs font-black uppercase tracking-widest text-center shadow-xl transition-all bg-rose-500/10 text-rose-400 border border-rose-500/20';
            userName.innerText = 'Unknown';
            userStatus.innerText = 'Error';
            if (workflowHint) {
                workflowHint.innerText = 'Scan ulang kartu untuk melanjutkan.';
            }
        }

        messageContainer.innerText = data.message;
    }

    function hideResult() {
        keepResult = false;
        cancelHideUiAnimation();
        if (paymentActions) paymentActions.classList.add('hidden');
        if (workflowHint) workflowHint.innerText = 'Siap scan kartu';
        resultFilled.classList.add('opacity-0', 'scale-95');
        hideUiTimer = setTimeout(() => {
            hideUiTimer = null;
            resultFilled.classList.add('hidden');
            resultEmpty.classList.remove('hidden');
            resultTopbar.className = 'h-1.5 w-full bg-white/5 transition-all duration-500';
            countdownBar.style.width = '100%';
        }, 500);
    }
</script>
@endpush
@endsection
