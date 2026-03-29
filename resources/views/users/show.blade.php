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
                        <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('images/default-user.png') }}" 
                             class="w-32 h-32 rounded-3xl object-cover border-4 border-white/10 shadow-xl mx-auto">
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
                            <p class="text-white font-bold text-lg text-emerald-500">Rp {{ number_format($user->balance, 0, ',', '.') }}</p>
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
                        <a href="{{ route('users.scan-rfid', $user->id) }}" 
                           class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white font-bold rounded-xl border border-white/10 transition-all flex items-center gap-2 text-xs uppercase tracking-widest">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-3.682A14.29 14.29 0 005.34 20M12 11c1.744 2.772 2.753 6.054 2.753 9.571m3.44-3.682c.535 1.1.883 2.267 1.023 3.49M12 11V3m0 0L9 6m3-3l3 3"></path></svg>
                            Ubah RFID
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
