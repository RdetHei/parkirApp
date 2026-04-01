@extends('layouts.app')

@section('title', 'Top Up Saldo - NestonPay')

@section('content')
<div class="p-8 relative z-10 animate-fade-in">
    <!-- Background Glows -->
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="max-w-xl mx-auto relative z-10">
        <!-- Header -->
        <div class="flex items-center gap-6 mb-12">
            <a href="{{ route('user.saldo.index') }}" class="w-12 h-12 bg-white/5 border border-white/10 rounded-2xl flex items-center justify-center text-slate-400 hover:text-white hover:border-emerald-500/50 transition-all shadow-xl group">
                <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight uppercase">Top Up Saldo</h1>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">NestonPay Wallet System</p>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card-pro group overflow-hidden relative border-emerald-500/10">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
            
            <div class="p-8 sm:p-12 relative z-10">
                <form id="topupForm" method="POST" action="{{ route('user.saldo.topup.store') }}" class="space-y-10">
                    @csrf
                    
                    <!-- Amount Input -->
                    <div>
                        <label for="amount" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-6 ml-1">Nominal Top Up</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-6 flex items-center pointer-events-none">
                                <span class="text-emerald-500 font-black text-2xl tracking-tighter">Rp</span>
                            </div>
                            <input type="number" name="amount" id="amount" required min="10000" step="1000"
                                   class="block w-full pl-20 pr-8 py-8 bg-slate-950/50 border border-white/5 rounded-[2rem] text-5xl font-black text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder:text-slate-800 tracking-tighter"
                                   placeholder="0">
                        </div>
                        <div class="flex items-center gap-2 mt-6 ml-2">
                            <div class="w-1 h-1 rounded-full bg-emerald-500"></div>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Minimal top up sebesar <span class="text-white">Rp 10.000</span></p>
                        </div>
                    </div>

                    <!-- Quick Options -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach([20000, 50000, 100000, 250000] as $val)
                            <button type="button" onclick="document.getElementById('amount').value = {{ $val }}" 
                                    class="py-4 px-2 bg-white/5 hover:bg-emerald-500 hover:text-slate-950 rounded-2xl text-[10px] font-black text-slate-400 transition-all border border-white/5 hover:border-emerald-500 hover:scale-[1.05] active:scale-95 uppercase tracking-widest">
                                {{ number_format($val/1000, 0) }}K
                            </button>
                        @endforeach
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 space-y-4">
                        <!-- Midtrans Button -->
                        <button type="button" id="pay-button" class="group relative w-full bg-emerald-500 text-slate-950 font-black py-6 rounded-[2rem] shadow-2xl shadow-emerald-500/20 transition-all hover:bg-emerald-400 active:scale-[0.98] flex items-center justify-center gap-4 uppercase tracking-widest text-xs overflow-hidden">
                            <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                            <i class="fa-solid fa-credit-card text-lg"></i>
                            <span id="pay-button-text">Bayar via Midtrans</span>
                        </button>

                        <!-- Manual/Simulation Button (For Dev/Demo) -->
                        <button type="submit" class="w-full bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white font-bold py-4 rounded-2xl transition-all border border-white/5 uppercase tracking-widest text-[10px] flex items-center justify-center gap-3">
                            <i class="fa-solid fa-bolt-lightning text-amber-500"></i>
                            Simulasi Top Up (Instant)
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info Footer -->
            <div class="bg-white/[0.02] p-8 border-t border-white/5">
                <div class="flex items-start gap-5">
                    <div class="w-12 h-12 bg-slate-950 border border-white/5 rounded-2xl flex items-center justify-center text-emerald-500 shrink-0 shadow-xl">
                        <i class="fa-solid fa-shield-halved text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-[11px] font-black text-white uppercase tracking-[0.2em] mb-2">Secure Transaction</h4>
                        <p class="text-[10px] text-slate-500 leading-relaxed font-bold uppercase tracking-widest">Pembayaran diproses melalui <span class="text-slate-300">Midtrans Secure Gateway</span>. Saldo akan otomatis tersedia setelah pembayaran diverifikasi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes shimmer {
        100% { transform: translateX(100%); }
    }
</style>
@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    const payButton = document.getElementById('pay-button');
    const amountInput = document.getElementById('amount');
    const payButtonText = document.getElementById('pay-button-text');

    payButton.addEventListener('click', function (e) {
        e.preventDefault();

        const amount = amountInput.value;
        if (!amount || amount < 10000) {
            Swal.fire({
                icon: 'warning',
                title: 'Nominal Kurang',
                text: 'Minimal top up Rp 10.000',
                background: '#0f172a',
                color: '#fff',
                confirmButtonColor: '#10b981'
            });
            return;
        }

        payButton.disabled = true;
        payButtonText.textContent = 'Memproses...';

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
                        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Pembayaran gagal!', background: '#0f172a', color: '#fff' });
                        payButton.disabled = false;
                        payButtonText.textContent = 'Bayar via Midtrans';
                    },
                    onClose: function() {
                        payButton.disabled = false;
                        payButtonText.textContent = 'Bayar via Midtrans';
                    }
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Kesalahan', text: data.error || 'Terjadi kesalahan', background: '#0f172a', color: '#fff' });
                payButton.disabled = false;
                payButtonText.textContent = 'Bayar via Midtrans';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({ icon: 'error', title: 'Koneksi Gagal', text: 'Gagal menghubungi server', background: '#0f172a', color: '#fff' });
            payButton.disabled = false;
            payButtonText.textContent = 'Bayar via Midtrans';
        });
    });
</script>
@endpush
