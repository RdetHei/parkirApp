@extends('layouts.app')

@section('title', 'Top Up Saldo - NestonPay')

@section('content')
<div class="p-8 relative z-10">
    <div class="max-w-xl mx-auto">
        <!-- Header -->
        <div class="flex items-center gap-4 mb-8 animate-fade-in-up">
            <a href="{{ route('user.saldo.index') }}" class="w-11 h-11 bg-white/[0.03] hover:bg-white/[0.08] text-white rounded-xl flex items-center justify-center transition-all border border-white/10">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Top Up Saldo</h1>
                <p class="text-sm text-slate-400">Isi ulang saldo NestonPay Anda untuk kemudahan pembayaran.</p>
            </div>
        </div>

        <div class="card-pro !p-0 overflow-hidden animate-fade-in-up" style="animation-delay: 0.1s">
            <div class="p-8">
                <div class="space-y-8">
                    <div>
                        <label for="amount" class="block text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-4">1. Masukkan Nominal Top Up</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                                <span class="text-slate-500 font-bold text-xl">Rp</span>
                            </div>
                            <input type="number" name="amount" id="amount" required min="10000" step="1000"
                                   class="block w-full pl-16 pr-6 py-5 bg-slate-800/50 border border-white/10 rounded-2xl text-3xl font-extrabold text-white focus:ring-emerald-500 focus:border-emerald-500 transition-all placeholder:text-slate-700"
                                   placeholder="0">
                        </div>
                        <p class="mt-4 text-xs text-slate-500 font-medium">Minimal top up sebesar <span class="text-white">Rp 10.000</span>.</p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-4">2. Pilih Nominal Cepat</label>
                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" onclick="setAmount(20000)" class="py-4 px-4 bg-white/[0.03] hover:bg-emerald-500/10 hover:text-emerald-500 rounded-2xl text-sm font-bold text-slate-300 transition-all border border-white/10">
                                Rp 20.000
                            </button>
                            <button type="button" onclick="setAmount(50000)" class="py-4 px-4 bg-white/[0.03] hover:bg-emerald-500/10 hover:text-emerald-500 rounded-2xl text-sm font-bold text-slate-300 transition-all border border-white/10">
                                Rp 50.000
                            </button>
                            <button type="button" onclick="setAmount(100000)" class="py-4 px-4 bg-white/[0.03] hover:bg-emerald-500/10 hover:text-emerald-500 rounded-2xl text-sm font-bold text-slate-300 transition-all border border-white/10">
                                Rp 100.000
                            </button>
                            <button type="button" onclick="setAmount(200000)" class="py-4 px-4 bg-white/[0.03] hover:bg-emerald-500/10 hover:text-emerald-500 rounded-2xl text-sm font-bold text-slate-300 transition-all border border-white/10">
                                Rp 200.000
                            </button>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="button" id="pay-button" class="w-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-[11px] uppercase tracking-[0.2em] py-5 rounded-2xl transition-all hover:shadow-[0_0_20px_rgba(16,185,129,0.4)] flex items-center justify-center gap-3">
                            <i class="fa-solid fa-shield-halved"></i>
                            Lanjutkan ke Pembayaran Aman
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-8 border-t border-white/5 bg-white/[0.02]">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-emerald-500/10 text-emerald-500 rounded-xl flex items-center justify-center shrink-0 border border-emerald-500/20">
                        <i class="fa-solid fa-info"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-white mb-1">Informasi Pembayaran</h4>
                        <p class="text-xs text-slate-400 leading-relaxed font-medium">Pembayaran diproses secara aman melalui Midtrans. Saldo akan otomatis bertambah setelah status pembayaran menjadi <span class="font-bold text-emerald-500">Settlement</span>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    const payButton = document.getElementById('pay-button');
    const amountInput = document.getElementById('amount');

    function setAmount(value) {
        amountInput.value = value;
    }

    payButton.addEventListener('click', function (e) {
        e.preventDefault();

        const amount = amountInput.value;
        if (!amount || amount < 10000) {
            alert('Minimal top up Rp 10.000');
            return;
        }

        payButton.disabled = true;
        payButton.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...';

        fetch('{{ route("user.saldo.topup.token") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ amount: amount })
        })
        .then(response => response.json())
        .then(data => {
            if (data.snap_token) {
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        const oid = encodeURIComponent(data.order_id || '');
                        window.location.href = '{{ route("user.saldo.index") }}?success=1&order_id=' + oid;
                    },
                    onPending: function(result) {
                        const oid = encodeURIComponent(data.order_id || '');
                        window.location.href = '{{ route("user.saldo.index") }}?pending=1&order_id=' + oid;
                    },
                    onError: function(result) {
                        alert('Pembayaran gagal!');
                        payButton.disabled = false;
                        payButton.innerHTML = 'Lanjutkan ke Pembayaran Aman';
                    },
                    onClose: function() {
                        payButton.disabled = false;
                        payButton.innerHTML = 'Lanjutkan ke Pembayaran Aman';
                    }
                });
            } else {
                alert(data.error || 'Terjadi kesalahan');
                payButton.disabled = false;
                payButton.innerHTML = 'Lanjutkan ke Pembayaran Aman';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan koneksi');
            payButton.disabled = false;
            payButton.innerHTML = 'Lanjutkan ke Pembayaran Aman';
        });
    });
</script>
@endpush
