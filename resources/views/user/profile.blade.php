@extends('layouts.app')

@section('title', 'Profil Saya - NESTON')

@section('content')
<div class="p-4 sm:p-8 relative z-10 animate-fade-in">
    <!-- Background Glows (Consistent with Auth & Dashboard) -->
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="max-w-5xl mx-auto relative z-10">
        {{-- Header --}}
        <div class="mb-8 lg:mb-12">
            <div class="flex items-center gap-3 mb-3">
                <span class="px-3 py-1 bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest rounded-full border border-blue-500/20">
                    Account Settings
                </span>
            </div>
            <h1 class="text-3xl lg:text-4xl font-black tracking-tight text-white uppercase">Pengaturan <span class="text-emerald-500">Profil</span></h1>
            <p class="mt-2 text-xs lg:text-sm text-slate-400 font-medium tracking-wide">Kelola informasi identitas, foto, dan keamanan akun Anda.</p>
        </div>

        @if(session('success'))
        <div class="mb-8 flex items-center gap-4 px-6 py-4 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 text-[10px] lg:text-xs font-black uppercase tracking-widest text-emerald-400 animate-fade-in">
            <i class="fa-solid fa-circle-check text-sm lg:text-base"></i>
            {{ session('success') }}
        </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">
            {{-- ── LEFT: Account sidebar ── --}}
            <div class="w-full lg:w-80 shrink-0 flex flex-col gap-6">
                {{-- Avatar card --}}
                <div class="card-pro group overflow-hidden relative border-white/5 backdrop-blur-xl bg-slate-900/40 flex flex-col items-center p-6 lg:p-8">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl group-hover:scale-110 transition-transform"></div>

                    <div class="relative mb-6">
                        <div class="absolute -inset-4 bg-emerald-500/10 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        @if($user->profile_photo_url)
                            <div class="relative w-32 h-32 lg:w-40 lg:h-40 rounded-3xl overflow-hidden border-2 border-emerald-500/30 shadow-2xl transition-all group-hover:border-emerald-500 z-10">
                                <img src="{{ $user->profile_photo_url }}"
                                     alt="{{ $user->name }}"
                                     class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-4">
                                    <span class="text-[8px] font-black text-white uppercase tracking-widest">Active Photo</span>
                                </div>
                            </div>
                        @else
                            <div class="w-32 h-32 lg:w-40 lg:h-40 bg-slate-950 border-2 border-white/10 rounded-3xl flex items-center justify-center text-emerald-500 text-4xl lg:text-5xl font-black shadow-2xl relative z-10">
                                {{ substr($user->name ?? 'U', 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <div class="text-center relative z-10">
                        <p class="text-xl lg:text-2xl font-black text-white tracking-tight leading-tight">{{ $user->name }}</p>
                        <p class="text-[10px] lg:text-[11px] text-slate-500 font-bold mt-2 uppercase tracking-widest">{{ $user->email }}</p>
                    </div>

                    @if($user->role ?? null)
                    <div class="mt-6 px-5 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-[9px] lg:text-[10px] font-black text-emerald-500 uppercase tracking-[0.2em] relative z-10">
                        {{ $user->role }}
                    </div>
                    @endif
                </div>

                {{-- Wallet card --}}
                <div class="card-pro group overflow-hidden relative border-emerald-500/10 backdrop-blur-xl bg-slate-900/40 p-6 lg:p-8">
                    <p class="text-[9px] lg:text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-6">NestonPay Wallet</p>
                    <div class="flex flex-col gap-6">
                        <div>
                            <p class="text-[8px] lg:text-[9px] text-slate-600 uppercase tracking-widest font-black mb-2">Saldo Aktif</p>
                            <p class="text-2xl lg:text-3xl font-black text-emerald-500 tracking-tighter">Rp {{ number_format($user->balance ?? $user->saldo ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <a href="{{ route('user.saldo.index') }}" class="w-full py-3.5 lg:py-4 px-4 bg-emerald-500/10 hover:bg-emerald-500 text-emerald-500 hover:text-slate-950 border border-emerald-500/20 rounded-2xl text-[9px] lg:text-[10px] font-black uppercase tracking-widest text-center transition-all active:scale-[0.98]">
                            Kelola Saldo
                        </a>
                    </div>
                </div>

                {{-- Account info --}}
                <div class="card-pro group overflow-hidden relative border-white/5 backdrop-blur-xl bg-slate-900/40 p-6 lg:p-8">
                    <p class="text-[9px] lg:text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-6 text-center">Data Akun</p>
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <p class="text-[8px] lg:text-[9px] text-slate-600 uppercase font-black tracking-widest">Bergabung</p>
                            <p class="text-[9px] lg:text-[10px] text-slate-300 font-bold uppercase">{{ $user->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-[8px] lg:text-[9px] text-slate-600 uppercase font-black tracking-widest">Update</p>
                            <p class="text-[9px] lg:text-[10px] text-slate-300 font-bold uppercase">{{ $user->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── RIGHT: Form ── --}}
            <div class="flex-1 w-full min-w-0">
                <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data" class="space-y-6 lg:space-y-8">
                    @csrf
                    @method('PUT')

                    {{-- Section: Informasi Pribadi --}}
                    <div class="card-pro !p-0 overflow-hidden border-white/5 backdrop-blur-xl bg-slate-900/40">
                        <div class="px-6 lg:px-8 py-5 lg:py-6 border-b border-white/5 bg-white/[0.02] flex items-center gap-4">
                            <div class="w-1.5 lg:w-2 h-5 lg:h-6 bg-emerald-500 rounded-full"></div>
                            <h2 class="text-[10px] lg:text-[11px] font-black text-white uppercase tracking-[0.2em]">Informasi Pribadi</h2>
                        </div>
                        <div class="p-6 lg:p-10 space-y-6 lg:space-y-8">
                            <div>
                                <label for="photo" class="block text-[9px] lg:text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Foto Profil</label>
                                <div class="flex flex-col sm:flex-row items-center gap-6 p-5 lg:p-6 rounded-[2rem] bg-slate-950/50 border border-white/5 group hover:border-emerald-500/30 transition-all">
                                    <div class="shrink-0">
                                        <label for="photo"
                                               class="relative block cursor-pointer rounded-2xl overflow-hidden border border-white/10 bg-slate-900 shadow-xl transition-all group-hover:border-emerald-500/40 focus:outline-none focus:ring-4 focus:ring-emerald-500/10">
                                            <div class="w-16 h-16 lg:w-20 lg:h-20">
                                                @if($user->profile_photo_url)
                                                    <img id="profile-photo-preview"
                                                         src="{{ $user->profile_photo_url }}"
                                                         alt="{{ $user->name }}"
                                                         class="w-full h-full object-cover">
                                                @else
                                                    <div id="profile-photo-preview-fallback"
                                                         class="w-full h-full flex items-center justify-center text-emerald-500 text-2xl lg:text-3xl font-black">
                                                        {{ substr($user->name ?? 'U', 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="absolute inset-0 bg-slate-950/60 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity"></div>
                                            <div class="absolute inset-0 flex items-center justify-center opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                                <div class="px-3 py-2 rounded-xl bg-emerald-500/15 border border-emerald-500/25 text-[8px] lg:text-[9px] font-black uppercase tracking-widest text-emerald-400 flex items-center gap-2">
                                                    <i class="fa-solid fa-camera"></i>
                                                    Ubah
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="flex-1 w-full text-center sm:text-left">
                                        <input type="file"
                                               name="photo"
                                               id="photo"
                                               accept="image/jpeg,image/png,image/gif,image/webp"
                                               class="sr-only">
                                        <div class="space-y-2">
                                            <p class="text-[9px] lg:text-[10px] text-slate-300 font-black uppercase tracking-widest">Klik foto untuk upload</p>
                                            <p class="text-[8px] lg:text-[9px] text-slate-600 font-bold uppercase tracking-widest">JPG, PNG, GIF, atau WebP (Maks. 4MB)</p>
                                        </div>
                                    </div>
                                </div>
                                @error('photo') <p class="mt-3 text-[10px] lg:text-[11px] text-rose-500 font-bold ml-1 uppercase tracking-widest">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 lg:gap-8">
                                <div class="space-y-3">
                                    <label for="name" class="block text-[9px] lg:text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Nama Lengkap</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                           class="block w-full rounded-2xl border border-white/5 bg-slate-950/50 px-4 lg:px-5 py-3.5 lg:py-4 text-sm text-white placeholder:text-slate-700 focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/5 focus:outline-none transition-all font-medium">
                                    @error('name') <p class="mt-2 text-[10px] lg:text-[11px] text-rose-500 font-bold ml-1 uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-3">
                                    <label for="email" class="block text-[9px] lg:text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Alamat Email</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                           class="block w-full rounded-2xl border border-white/5 bg-slate-950/50 px-4 lg:px-5 py-3.5 lg:py-4 text-sm text-white placeholder:text-slate-700 focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/5 focus:outline-none transition-all font-medium">
                                    @error('email') <p class="mt-2 text-[10px] lg:text-[11px] text-rose-500 font-bold ml-1 uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-3 sm:col-span-2">
                                    <label for="phone" class="block text-[9px] lg:text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">WhatsApp / HP <span class="text-slate-600 normal-case font-medium">(notifikasi parkir)</span></label>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                           placeholder="628xxxxx"
                                           class="block w-full rounded-2xl border border-white/5 bg-slate-950/50 px-4 lg:px-5 py-3.5 lg:py-4 text-sm text-white placeholder:text-slate-700 focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/5 focus:outline-none transition-all font-medium">
                                    @error('phone') <p class="mt-2 text-[10px] lg:text-[11px] text-rose-500 font-bold ml-1 uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Keamanan --}}
                    <div class="card-pro !p-0 overflow-hidden border-white/5 backdrop-blur-xl bg-slate-900/40">
                        <div class="px-6 lg:px-8 py-5 lg:py-6 border-b border-white/5 bg-white/[0.02] flex items-center gap-4">
                            <div class="w-1.5 lg:w-2 h-5 lg:h-6 bg-blue-500 rounded-full"></div>
                            <h2 class="text-[10px] lg:text-[11px] font-black text-white uppercase tracking-[0.2em]">Keamanan Akun</h2>
                        </div>
                        <div class="p-6 lg:p-10">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 lg:gap-8">
                                <div class="space-y-3">
                                    <label for="password" class="block text-[9px] lg:text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Sandi Baru</label>
                                    <input type="password" name="password" id="password"
                                           placeholder="Kosongkan jika tidak berubah"
                                           class="block w-full rounded-2xl border border-white/5 bg-slate-950/50 px-4 lg:px-5 py-3.5 lg:py-4 text-sm text-white placeholder:text-slate-700 focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/5 focus:outline-none transition-all font-medium">
                                    @error('password') <p class="mt-2 text-[10px] lg:text-[11px] text-rose-500 font-bold ml-1 uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>
                                <div class="space-y-3">
                                    <label for="password_confirmation" class="block text-[9px] lg:text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Konfirmasi Sandi</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                           placeholder="Ulangi sandi baru"
                                           class="block w-full rounded-2xl border border-white/5 bg-slate-950/50 px-4 lg:px-5 py-3.5 lg:py-4 text-sm text-white placeholder:text-slate-700 focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/5 focus:outline-none transition-all font-medium">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="flex justify-end pt-4">
                        <button type="submit"
                                class="w-full sm:w-auto group relative px-8 lg:px-10 py-4 lg:py-5 bg-emerald-500 text-slate-950 text-[10px] lg:text-xs font-black uppercase tracking-widest rounded-[1.5rem] lg:rounded-[2rem] hover:bg-emerald-400 transition-all shadow-2xl shadow-emerald-500/20 active:scale-[0.98] overflow-hidden flex items-center justify-center gap-3">
                            <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                            <i class="fa-solid fa-cloud-arrow-up text-base lg:text-lg"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes shimmer {
        100% { transform: translateX(100%); }
    }
</style>

@push('scripts')
<script>
    (function () {
        const input = document.getElementById('photo');
        if (!input) return;

        input.addEventListener('change', function () {
            const file = input.files && input.files[0];
            if (!file) return;
            if (!file.type || !file.type.startsWith('image/')) return;

            const img = document.getElementById('profile-photo-preview');
            const fallback = document.getElementById('profile-photo-preview-fallback');

            const url = URL.createObjectURL(file);

            if (img) {
                img.src = url;
                return;
            }

            if (fallback) {
                const newImg = document.createElement('img');
                newImg.id = 'profile-photo-preview';
                newImg.alt = 'Preview';
                newImg.className = 'w-full h-full object-cover';
                newImg.src = url;
                fallback.replaceWith(newImg);
            }
        });
    })();
</script>
@endpush
@endsection
