@extends('layouts.app')

@section('title', 'Kendaraan Saya - NESTON')

@section('content')
<div class="p-4 sm:p-8 relative z-10 animate-fade-in">
    <!-- Background Glows -->
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="max-w-5xl mx-auto relative z-10">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8 lg:mb-12">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-black uppercase tracking-widest rounded-full border border-emerald-500/20">
                        Asset Management
                    </span>
                </div>
                <h1 class="text-3xl lg:text-4xl font-black tracking-tight text-white uppercase">Kendaraan <span class="text-emerald-500">Saya</span></h1>
                <p class="text-slate-400 text-xs lg:text-sm mt-2 font-medium tracking-wide">Kelola daftar kendaraan yang Anda gunakan untuk parkir cerdas.</p>
            </div>

            <a href="{{ route('user.dashboard') }}"
               class="group w-full md:w-auto px-6 py-3.5 bg-white/5 border border-white/5 rounded-2xl text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-white hover:bg-white/10 transition-all flex items-center justify-center gap-3 active:scale-95">
                <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                Kembali ke Dashboard
            </a>
        </div>

        @if(session('success') || session('error'))
            <div class="mb-8 flex items-center gap-4 px-6 py-4 rounded-2xl border {{ session('success') ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400' : 'border-rose-500/20 bg-rose-500/10 text-rose-400' }} text-[10px] lg:text-xs font-black uppercase tracking-widest animate-fade-in">
                <i class="fa-solid {{ session('success') ? 'fa-circle-check' : 'fa-circle-exclamation' }} text-sm lg:text-base"></i>
                {{ session('success') ?? session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-8 lg:gap-10">
            <!-- Add Vehicle Form -->
            <div class="card-pro border-white/5 backdrop-blur-xl bg-slate-900/40 p-6 lg:p-8">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500 shadow-xl">
                            <i class="fa-solid fa-plus text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-[10px] lg:text-[11px] font-black text-white uppercase tracking-[0.2em]">Tambah Kendaraan Baru</h2>
                            <p class="text-[10px] text-slate-500 font-bold mt-1">Gunakan plat valid agar terdeteksi otomatis saat check-in RFID.</p>
                        </div>
                    </div>
                    @if($kendaraans->count() >= 2)
                        <div class="px-3 py-1.5 bg-rose-500/10 border border-rose-500/20 text-rose-500 text-[9px] font-black uppercase tracking-widest rounded-xl animate-pulse">
                            Limit Tercapai (Maks. 2)
                        </div>
                    @endif
                </div>

                @if($kendaraans->count() < 2)
                    <form method="POST" action="{{ route('user.vehicles.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-end">
                        @csrf
                        <div>
                            <label for="plat_nomor" class="block text-[9px] lg:text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Plat Nomor</label>
                            <input type="text" name="plat_nomor" id="plat_nomor"
                                   class="block w-full rounded-2xl border border-white/5 bg-slate-950/50 px-4 py-4 text-sm text-white placeholder:text-slate-700 focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/5 focus:outline-none transition-all font-black uppercase tracking-widest"
                                   placeholder="B 1234 XYZ" required>
                        </div>
                        <div>
                            <label for="jenis_kendaraan" class="block text-[9px] lg:text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Jenis</label>
                            <select name="jenis_kendaraan" id="jenis_kendaraan"
                                    class="block w-full rounded-2xl border border-white/5 bg-slate-950/50 px-4 py-4 text-sm text-white focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/5 focus:outline-none transition-all font-bold"
                                    required>
                                <option value="" class="bg-slate-900">Pilih...</option>
                                @foreach($vehicleTypes as $type)
                                    <option value="{{ $type }}" class="bg-slate-900">{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="warna" class="block text-[9px] lg:text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Warna</label>
                            <input type="text" name="warna" id="warna"
                                   class="block w-full rounded-2xl border border-white/5 bg-slate-950/50 px-4 py-4 text-sm text-white placeholder:text-slate-700 focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/5 focus:outline-none transition-all font-medium"
                                   placeholder="Hitam">
                        </div>
                        <div>
                            <button type="submit"
                                    class="w-full group relative flex items-center justify-center gap-3 py-4 bg-emerald-500 text-slate-950 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-emerald-400 transition-all shadow-xl shadow-emerald-500/20 active:scale-[0.98] overflow-hidden">
                                <div class="absolute inset-0 w-full h-full bg-linear-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                                <i class="fa-solid fa-floppy-disk text-sm"></i>
                                Simpan Data
                            </button>
                        </div>
                    </form>
                @else
                    <div class="px-6 py-12 text-center bg-slate-950/30 rounded-3xl border border-dashed border-white/5">
                        <div class="w-16 h-16 bg-rose-500/10 border border-rose-500/20 rounded-2xl flex items-center justify-center mx-auto mb-6 text-rose-500 shadow-2xl">
                            <i class="fa-solid fa-lock text-xl"></i>
                        </div>
                        <h3 class="text-xs font-black text-white uppercase tracking-widest mb-2">Batas Pendaftaran Tercapai</h3>
                        <p class="text-[10px] text-slate-500 font-bold max-w-sm mx-auto">Anda telah mencapai batas maksimal pendaftaran kendaraan (2 unit). Hapus salah satu kendaraan untuk mendaftarkan kendaraan baru.</p>
                    </div>
                @endif
            </div>

            <!-- List Vehicles -->
            <div class="card-pro p-0! overflow-hidden border-white/5 backdrop-blur-xl bg-slate-900/40 shadow-2xl">
                <div class="px-5 sm:px-8 py-5 sm:py-6 border-b border-white/5 bg-white/2 flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Daftar Kendaraan Terdaftar</h2>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-lg border border-emerald-500/20 bg-emerald-500/10 text-[9px] font-black text-emerald-400 uppercase tracking-widest">
                            {{ $kendaraans->count() }} kendaraan
                        </span>
                    </div>
                </div>

                @if($kendaraans->isEmpty())
                    <div class="px-8 py-24 text-center">
                        <div class="w-24 h-24 bg-slate-950 border border-white/5 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 text-slate-800 shadow-2xl">
                            <i class="fa-solid fa-car-side text-4xl opacity-10"></i>
                        </div>
                        <p class="text-[10px] text-slate-600 font-black uppercase tracking-[0.3em] italic">Belum ada kendaraan terdaftar</p>
                    </div>
                @else
                    {{-- Mobile cards --}}
                    <div class="grid grid-cols-1 gap-4 p-4 sm:p-6 lg:hidden">
                        @foreach($kendaraans as $k)
                            <div class="rounded-2xl border border-white/5 bg-slate-950/40 p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="text-sm font-black text-white uppercase tracking-widest truncate">{{ $k->plat_nomor }}</p>
                                        <div class="flex items-center gap-2 mt-2">
                                            <span class="px-2 py-0.5 bg-blue-500/10 text-blue-400 text-[9px] font-black uppercase rounded border border-blue-500/20 tracking-widest">{{ $k->jenis_kendaraan }}</span>
                                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">{{ $k->warna ?: 'Tanpa Warna' }}</span>
                                        </div>
                                        <p class="mt-2 text-[10px] text-slate-400 font-semibold">Pemilik: {{ $k->pemilik ?: $user->name }}</p>
                                    </div>
                                    <form action="{{ route('user.vehicles.destroy', $k) }}"
                                          method="POST"
                                          onsubmit="return confirm('Hapus kendaraan ini dari akun Anda?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-9 h-9 rounded-xl bg-rose-500/5 border border-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition-all flex items-center justify-center">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Desktop table --}}
                    <div class="hidden lg:block overflow-x-auto max-h-120">
                        <table class="w-full text-left border-collapse">
                            <thead class="sticky top-0 z-10">
                                <tr class="bg-slate-900/95 border-b border-white/5 backdrop-blur-xl">
                                    <th class="px-6 py-4 w-16 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">No</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Plat Nomor</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Jenis</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Warna</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Pemilik</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach($kendaraans as $index => $k)
                                    <tr class="group hover:bg-white/2 transition-all">
                                        <td class="px-6 py-4">
                                            <span class="inline-flex w-8 h-8 items-center justify-center rounded-lg bg-slate-950 border border-white/5 text-[11px] font-black text-slate-400">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-black text-white tracking-widest uppercase group-hover:text-emerald-400 transition-colors">{{ $k->plat_nomor }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 bg-blue-500/10 text-blue-400 text-[9px] font-black uppercase rounded-lg border border-blue-500/20 tracking-widest">
                                                {{ $k->jenis_kendaraan }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ $k->warna ?: 'Tanpa Warna' }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-xs font-bold text-slate-300">{{ $k->pemilik ?: $user->name }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <form action="{{ route('user.vehicles.destroy', $k) }}"
                                                  method="POST"
                                                  class="inline"
                                                  onsubmit="return confirm('Hapus kendaraan ini dari akun Anda?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="w-10 h-10 rounded-xl bg-rose-500/5 border border-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition-all flex items-center justify-center">
                                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
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
