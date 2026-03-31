@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="min-h-screen bg-[#020617] px-4 py-10 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">

        {{-- Header --}}
        <div class="mb-10">
            <h1 class="text-2xl font-bold text-white tracking-tight">Pengaturan Profil</h1>
            <p class="mt-1 text-sm text-slate-400">Kelola informasi akun dan keamanan Anda.</p>
        </div>

        @if(session('success'))
        <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-sm text-emerald-400">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
        @endif

        {{--
            VARIANT 1: Left sidebar info + right form card
            Sidebar: avatar placeholder + account meta
            Form: dua section (identitas & password) dalam satu card
        --}}
        <div class="flex flex-col lg:flex-row gap-6">

            {{-- ── LEFT: Account sidebar ── --}}
            <div class="lg:w-64 shrink-0 flex flex-col gap-4">

                {{-- Avatar card --}}
                <div class="bg-[#020617] border border-white/[0.07] rounded-2xl p-6 flex flex-col items-center gap-4">
                    <x-user-avatar :user="$user" size="lg" round="2xl"
                        class="!bg-gradient-to-br !from-emerald-500/20 !to-teal-500/20 !text-emerald-400 !border-white/10 select-none shadow-lg shadow-black/20" />
                    <div class="text-center">
                        <p class="text-sm font-bold text-white">{{ $user->name }}</p>
                        <p class="text-xs text-slate-500 mt-0.5 break-all">{{ $user->email }}</p>
                    </div>
                    @if($user->role ?? null)
                    <span class="px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-full text-[10px] font-bold text-emerald-400 uppercase tracking-widest">
                        {{ $user->role }}
                    </span>
                    @endif
                </div>

                {{-- Meta info --}}
                <div class="bg-[#020617] border border-white/[0.07] rounded-2xl p-5 flex flex-col gap-4">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Informasi Akun</p>
                    <div class="flex flex-col gap-3">
                        <div>
                            <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold mb-0.5">Bergabung</p>
                            <p class="text-xs text-slate-300">{{ $user->created_at->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold mb-0.5">Terakhir Diperbarui</p>
                            <p class="text-xs text-slate-300">{{ $user->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── RIGHT: Form ── --}}
            <div class="flex-1 min-w-0">
                <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col gap-4">

                        {{-- Section: Informasi Pribadi --}}
                        <div class="bg-[#020617] border border-white/[0.07] rounded-2xl overflow-hidden">
                            <div class="px-6 py-4 border-b border-white/[0.05] flex items-center gap-3">
                                <span class="w-1 h-5 bg-emerald-500 rounded-full"></span>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Informasi Pribadi</p>
                            </div>
                            <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div class="sm:col-span-2">
                                    <label for="photo" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Foto profil</label>
                                    <p class="text-[11px] text-slate-500 mb-2">JPG, PNG, GIF, atau WebP — disimpan di Cloudinary (maks. 4&nbsp;MB).</p>
                                    <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/gif,image/webp"
                                           class="block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-500/15 file:text-emerald-400 hover:file:bg-emerald-500/25 cursor-pointer @error('photo') border border-red-500/50 rounded-xl @enderror">
                                    @error('photo') <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="name" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Nama Lengkap</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                           class="block w-full rounded-xl border border-white/[0.08] bg-white/[0.04] px-4 py-3 text-sm text-white placeholder:text-slate-600 focus:border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-all @error('name') border-red-500/50 @enderror">
                                    @error('name') <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="email" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Alamat Email</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                           class="block w-full rounded-xl border border-white/[0.08] bg-white/[0.04] px-4 py-3 text-sm text-white placeholder:text-slate-600 focus:border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-all @error('email') border-red-500/50 @enderror">
                                    @error('email') <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Section: Kata Sandi --}}
                        <div class="bg-[#020617] border border-white/[0.07] rounded-2xl overflow-hidden">
                            <div class="px-6 py-4 border-b border-white/[0.05] flex items-center gap-3">
                                <span class="w-1 h-5 bg-slate-600 rounded-full"></span>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Ubah Kata Sandi</p>
                                <span class="text-[10px] text-slate-600 font-medium">— opsional</span>
                            </div>
                            <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="password" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Kata Sandi Baru</label>
                                    <input type="password" name="password" id="password"
                                           placeholder="Kosongkan jika tidak ingin mengubah"
                                           class="block w-full rounded-xl border border-white/[0.08] bg-white/[0.04] px-4 py-3 text-sm text-white placeholder:text-slate-600 focus:border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-all @error('password') border-red-500/50 @enderror">
                                    @error('password') <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="password_confirmation" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Konfirmasi Sandi</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                           class="block w-full rounded-xl border border-white/[0.08] bg-white/[0.04] px-4 py-3 text-sm text-white placeholder:text-slate-600 focus:border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-all">
                                </div>
                            </div>
                        </div>

                        {{-- Action --}}
                        <div class="flex justify-end">
                            <button type="submit"
                                    class="px-8 py-3 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-900/40">
                                Simpan Perubahan
                            </button>
                        </div>

                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
