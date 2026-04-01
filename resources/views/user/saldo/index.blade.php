@extends('layouts.app')

@section('title', 'NestonPay - Dompet Digital')

@section('content')
<div class="p-8 relative z-10">
    <div class="max-w-4xl mx-auto space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 animate-fade-in-up">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                        <i class="fa-solid fa-wallet mr-1"></i>
                        NestonPay
                    </span>
                </div>
                <h1 class="text-4xl font-bold tracking-tight text-white">Dompet Digital Anda</h1>
                <p class="text-slate-400 text-sm mt-2">Lihat saldo, riwayat transaksi, dan lakukan top up dengan mudah.</p>
            </div>
        </div>

        <!-- Saldo Card -->
        <div class="card-pro group overflow-hidden relative border-emerald-500/20 animate-fade-in-up" style="animation-delay: 0.1s">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-2">Saldo Aktif</p>
                    <h2 class="text-6xl font-black tracking-tighter text-white">
                        <span class="text-emerald-500 text-3xl font-medium mr-2">Rp</span>{{ number_format($user->saldo, 0, ',', '.') }}
                    </h2>
                </div>
                <a href="{{ route('user.saldo.topup') }}" class="w-full md:w-auto px-8 py-4 bg-emerald-500 text-slate-950 font-black text-[11px] uppercase tracking-[0.2em] rounded-2xl transition-all hover:bg-emerald-400 hover:shadow-[0_0_20px_rgba(16,185,129,0.4)] flex items-center justify-center gap-3">
                    <i class="fa-solid fa-plus"></i>
                    Top Up Saldo
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="px-4 py-3 bg-emerald-500/10 text-emerald-500 text-sm font-bold rounded-xl border border-emerald-500/20 flex items-center gap-3">
                    <i class="fa-solid fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Transaction History -->
        <div class="card-pro !p-0 overflow-hidden animate-fade-in-up" style="animation-delay: 0.3s">
            <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                <h2 class="text-sm font-black text-white uppercase tracking-widest">Riwayat Transaksi Saldo</h2>
            </div>
            
            @if($histories->count())
                <div class="divide-y divide-white/5">
                    @foreach($histories as $history)
                        <div class="px-8 py-5 flex items-center justify-between hover:bg-white/[0.02] transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center {{ $history->amount > 0 ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-500 border border-rose-500/20' }}">
                                    @if($history->type === 'topup')
                                        <i class="fa-solid fa-arrow-down"></i>
                                    @elseif($history->type === 'payment')
                                        <i class="fa-solid fa-arrow-up"></i>
                                    @else
                                        <i class="fa-solid fa-exchange-alt"></i>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">{{ $history->description }}</p>
                                    <p class="text-xs text-slate-400 mt-1">{{ $history->created_at->translatedFormat('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold {{ $history->amount > 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                    {{ $history->amount > 0 ? '+' : '-' }} Rp {{ number_format(abs($history->amount), 0, ',', '.') }}
                                </p>
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mt-1">{{ $history->type }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($histories->hasPages())
                    <div class="px-8 py-6 border-t border-white/5 bg-white/[0.02]">
                        {{ $histories->links() }}
                    </div>
                @endif
            @else
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-white/5 text-slate-500 flex items-center justify-center rounded-2xl mx-auto mb-4 border border-white/10">
                        <i class="fa-solid fa-receipt text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-white">Belum Ada Riwayat</h3>
                    <p class="text-sm text-slate-400 mt-1">Anda belum memiliki riwayat transaksi saldo.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
