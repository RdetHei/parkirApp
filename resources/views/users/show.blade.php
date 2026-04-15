@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
<div class="p-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-white">Detail Pengguna</h1>
            <a href="{{ route('users.index') }}" class="text-slate-400 hover:text-white transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Profile Card -->
            <div class="md:col-span-1">
                <div class="card-pro text-center">
                    <div class="relative inline-block mb-6">
                        @if($user->profile_photo_url)
                            <img src="{{ $user->profile_photo_url }}"
                                 alt=""
                                 class="w-32 h-32 rounded-3xl object-cover border-4 border-white/10 shadow-xl mx-auto">
                        @else
                            <div class="w-32 h-32 rounded-3xl border-4 border-white/10 shadow-xl mx-auto flex items-center justify-center bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-3xl font-bold text-emerald-400 uppercase">
                                {{ strtoupper(\Illuminate\Support\Str::substr($user->name, 0, 2)) }}
                            </div>
                        @endif
                        <span class="absolute -bottom-2 -right-2 px-3 py-1 bg-emerald-500 text-slate-950 text-[10px] font-bold uppercase tracking-widest rounded-lg border border-emerald-400/50">
                            {{ $user->role }}
                        </span>
                    </div>
                    <h2 class="text-xl font-bold text-white mb-1">{{ $user->name }}</h2>
                    <p class="text-slate-400 text-sm mb-6">{{ $user->email }}</p>

                    <div class="pt-6 border-t border-white/5">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">RFID UID</p>
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/5 rounded-xl border border-white/10">
                            <span class="w-2 h-2 rounded-full {{ $user->rfid_uid ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                            <span class="text-xs font-mono text-white">{{ $user->rfid_uid ?? 'Tidak Terdaftar' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details Card -->
            <div class="md:col-span-2 space-y-8">
                <div class="card-pro">
                    <h3 class="text-sm font-bold text-white uppercase tracking-widest mb-6 flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span>
                        Informasi Akun
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Nomor Telepon</p>
                            <p class="text-white font-medium">{{ $user->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Saldo Terakhir</p>
                            <div class="flex items-center gap-3">
                                <p class="text-white font-bold text-lg text-emerald-500">Rp {{ number_format($user->balance ?? $user->saldo ?? 0, 0, ',', '.') }}</p>
                                <button onclick="openTopupModal()" class="p-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-500 rounded-lg border border-emerald-500/20 transition-all" title="Top Up Saldo">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Top Up Modal (Admin/Petugas Only View) -->
                        <div id="topupModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
                            <div class="bg-slate-900 border border-white/10 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">
                                <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between">
                                    <h4 class="text-sm font-bold text-white uppercase tracking-widest">Top Up Saldo Manual</h4>
                                    <button onclick="closeTopupModal()" class="text-slate-500 hover:text-white transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                                <form action="{{ route('users.topup', $user->id) }}" method="POST" class="p-6 space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Jumlah Top Up (Rp)</label>
                                        <input type="number" name="amount" required min="1000" step="1000" placeholder="Contoh: 50000"
                                               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500/50 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Keterangan (Opsional)</label>
                                        <input type="text" name="description" placeholder="Contoh: Top up tunai di kasir"
                                               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500/50 transition-all">
                                    </div>
                                    <button type="submit" class="w-full py-4 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold rounded-xl transition-all uppercase tracking-widest text-xs">
                                        Konfirmasi Top Up
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Terdaftar Pada</p>
                            <p class="text-white font-medium">{{ $user->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    <div class="mt-10 pt-10 border-t border-white/5 flex flex-wrap gap-4">
                        <a href="{{ route('users.edit', $user->id) }}"
                           class="px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold rounded-xl transition-all flex items-center gap-2 text-xs uppercase tracking-widest">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openTopupModal() {
        document.getElementById('topupModal').classList.remove('hidden');
    }
    function closeTopupModal() {
        document.getElementById('topupModal').classList.add('hidden');
    }
    // Close on click outside
    window.onclick = function(event) {
        let modal = document.getElementById('topupModal');
        if (event.target == modal) {
            closeTopupModal();
        }
    }
</script>
@endpush
