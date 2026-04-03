@extends('layouts.app')

@section('title', 'NestonPay - Dompet Digital')

@section('content')
<div class="p-4 sm:p-8 relative z-10 animate-fade-in">
    <!-- Background Glows -->
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="max-w-4xl mx-auto relative z-10">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8 lg:mb-12">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-black uppercase tracking-widest rounded-full border border-emerald-500/20">
                        Digital Wallet
                    </span>
                </div>
                <h1 class="text-3xl lg:text-4xl font-black tracking-tight text-white uppercase">Neston<span class="text-emerald-500">Pay</span></h1>
                <p class="text-slate-400 text-xs lg:text-sm mt-2 font-medium tracking-wide">Kelola saldo dan pantau seluruh riwayat transaksi Anda.</p>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('user.saldo.topup') }}" class="group relative w-full md:w-auto px-6 lg:px-8 py-3.5 lg:py-4 bg-emerald-500 text-slate-950 font-black text-[10px] lg:text-xs uppercase tracking-widest rounded-2xl transition-all hover:bg-emerald-400 hover:shadow-[0_0_30px_rgba(16,185,129,0.3)] flex items-center justify-center gap-3 overflow-hidden active:scale-[0.98]">
                    <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                    <i class="fa-solid fa-plus text-sm"></i>
                    Top Up Saldo
                </a>
            </div>
        </div>

        <!-- Saldo Card (Premium Glass) -->
        <div class="card-pro group overflow-hidden relative border-emerald-500/10 backdrop-blur-xl bg-slate-900/40 p-6 lg:p-12 mb-8 lg:mb-10">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-4 mb-6 lg:mb-8">
                    <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-[1rem] bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center shadow-xl shadow-emerald-500/5">
                        <i class="fa-solid fa-wallet text-lg lg:text-xl text-emerald-500"></i>
                    </div>
                    <div>
                        <p class="text-[9px] lg:text-[10px] font-black text-slate-500 uppercase tracking-[0.3em]">Total Saldo Saat Ini</p>
                        <p class="text-[8px] lg:text-[9px] font-black text-emerald-500 uppercase tracking-widest mt-1">Ready for payments</p>
                    </div>
                </div>

                <h2 class="text-5xl lg:text-7xl font-black tracking-tighter text-white flex items-baseline gap-3 lg:gap-4 mb-2">
                    <span class="text-emerald-500 text-2xl lg:text-3xl font-medium tracking-normal">Rp</span>{{ number_format($user->saldo, 0, ',', '.') }}
                </h2>
                <div class="h-1 lg:h-1.5 w-16 lg:w-24 bg-gradient-to-r from-emerald-500 to-transparent rounded-full opacity-50"></div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-8 lg:mb-10 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-4 lg:p-6 flex items-center gap-4 animate-fade-in">
                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-slate-950 shrink-0 shadow-lg shadow-emerald-500/20">
                    <i class="fa-solid fa-circle-check text-base lg:text-lg"></i>
                </div>
                <p class="text-[10px] lg:text-xs font-black uppercase tracking-widest text-emerald-400">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Transaction History -->
        <div class="card-pro !p-0 overflow-hidden border-white/5 backdrop-blur-xl bg-slate-900/40 shadow-2xl">
            <div class="px-6 lg:px-8 py-5 lg:py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                <h3 class="text-[10px] lg:text-[11px] font-black text-white uppercase tracking-[0.2em]">Riwayat Transaksi</h3>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-[10px] text-slate-600"></i>
                    <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest">Recent Activity</span>
                </div>
            </div>

            <div class="divide-y divide-white/5">
                @forelse($histories as $history)
                    <div class="px-6 lg:px-8 py-5 lg:py-6 flex items-center justify-between hover:bg-white/[0.02] transition-all group">
                        <div class="flex items-center gap-4 lg:gap-6">
                            <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-2xl flex items-center justify-center border transition-all {{ $history->amount > 0 ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-500 group-hover:border-emerald-500/40' : 'bg-slate-950 border-white/5 text-slate-600 group-hover:border-white/10' }}">
                                @if($history->type === 'topup')
                                    <i class="fa-solid fa-plus-circle text-lg lg:text-xl"></i>
                                @elseif($history->type === 'payment')
                                    <i class="fa-solid fa-parking text-lg lg:text-xl"></i>
                                @else
                                    <i class="fa-solid fa-arrow-right-arrow-left text-lg lg:text-xl"></i>
                                @endif
                            </div>
                            <div>
                                <p class="text-xs lg:text-sm font-black text-white tracking-tight group-hover:text-emerald-400 transition-colors">{{ $history->description }}</p>
                                <p class="text-[9px] lg:text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">{{ $history->created_at->translatedFormat('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm lg:text-base font-black tracking-tighter {{ $history->amount > 0 ? 'text-emerald-400' : 'text-slate-400' }}">
                                {{ $history->amount > 0 ? '+' : '' }} Rp {{ number_format($history->amount, 0, ',', '.') }}
                            </p>
                            <span class="inline-flex px-1.5 py-0.5 bg-white/5 border border-white/5 rounded text-[7px] lg:text-[8px] font-black uppercase tracking-widest text-slate-600 mt-1">{{ $history->type }}</span>
                        </div>
                    </div>
                @empty
                    <div class="px-8 py-20 lg:py-24 text-center">
                        <div class="w-20 h-20 lg:w-24 lg:h-24 bg-slate-950 border border-white/5 rounded-[2rem] flex items-center justify-center mx-auto mb-6 lg:mb-8 text-slate-800 shadow-2xl">
                            <i class="fa-solid fa-receipt text-3xl lg:text-4xl opacity-10"></i>
                        </div>
                        <p class="text-[9px] lg:text-[10px] text-slate-600 font-black uppercase tracking-[0.3em] italic">Belum ada riwayat transaksi</p>
                    </div>
                @endforelse
            </div>

            @if($histories->hasPages())
                <div class="px-8 py-6 border-t border-white/5 bg-white/[0.01]">
                    {{ $histories->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
 