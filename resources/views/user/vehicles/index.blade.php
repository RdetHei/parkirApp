@extends('layouts.app')

@section('title', 'Kendaraan Saya - NESTON')

@section('content')
<div class="p-8 relative z-10 animate-fade-in">
    <!-- Background Glows -->
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500/5 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="max-w-5xl mx-auto relative z-10">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-black uppercase tracking-widest rounded-full border border-emerald-500/20">
                        Asset Management
                    </span>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-white uppercase">Kendaraan <span class="text-emerald-500">Saya</span></h1>
                <p class="text-slate-400 text-sm mt-2 font-medium tracking-wide">Kelola daftar kendaraan yang Anda gunakan untuk parkir cerdas.</p>
            </div>

            <a href="{{ route('user.dashboard') }}"
               class="group px-6 py-3.5 bg-white/5 border border-white/5 rounded-2xl text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-white hover:bg-white/10 transition-all flex items-center gap-3 active:scale-95">
                <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                Kembali ke Dashboard
            </a>
        </div>

        @if(session('success') || session('error'))
            <div class="mb-8 flex items-center gap-4 px-6 py-4 rounded-2xl border {{ session('success') ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400' : 'border-rose-500/20 bg-rose-500/10 text-rose-400' }} text-xs font-black uppercase tracking-widest animate-fade-in">
                <i class="fa-solid {{ session('success') ? 'fa-circle-check' : 'fa-circle-exclamation' }} text-base"></i>
                {{ session('success') ?? session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-10">
            <!-- Add Vehicle Form -->
            <div class="card-pro border-white/5 backdrop-blur-xl bg-slate-900/40">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500 shadow-xl">
                        <i class="fa-solid fa-plus text-sm"></i>
                    </div>
                    <h2 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Tambah Kendaraan Baru</h2>
                </div>

                <form method="POST" action="{{ route('user.vehicles.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                    @csrf
                    <div class="md:col-span-1">
                        <label for="plat_nomor" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Plat Nomor</label>
                        <input type="text" name="plat_nomor" id="plat_nomor"
                               class="block w-full rounded-2xl border border-white/5 bg-slate-950/50 px-4 py-4 text-sm text-white placeholder:text-slate-700 focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/5 focus:outline-none transition-all font-black uppercase tracking-widest"
                               placeholder="B 1234 XYZ" required>
                    </div>
                    <div class="md:col-span-1">
                        <label for="jenis_kendaraan" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Jenis</label>
                        <select name="jenis_kendaraan" id="jenis_kendaraan"
                                class="block w-full rounded-2xl border border-white/5 bg-slate-950/50 px-4 py-4 text-sm text-white focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/5 focus:outline-none transition-all font-bold"
                                required>
                            <option value="" class="bg-slate-900">Pilih...</option>
                            <option value="motor" class="bg-slate-900">Motor</option>
                            <option value="mobil" class="bg-slate-900">Mobil</option>
                            <option value="lainnya" class="bg-slate-900">Lainnya</option>
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <label for="warna" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Warna</label>
                        <input type="text" name="warna" id="warna"
                               class="block w-full rounded-2xl border border-white/5 bg-slate-950/50 px-4 py-4 text-sm text-white placeholder:text-slate-700 focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/5 focus:outline-none transition-all font-medium"
                               placeholder="Hitam">
                    </div>
                    <div class="md:col-span-1">
                        <button type="submit"
                                class="w-full group relative flex items-center justify-center gap-3 py-4 bg-emerald-500 text-slate-950 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-emerald-400 transition-all shadow-xl shadow-emerald-500/20 active:scale-[0.98] overflow-hidden">
                            <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                            <i class="fa-solid fa-floppy-disk text-sm"></i>
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>

            <!-- List Vehicles -->
            <div class="card-pro !p-0 overflow-hidden border-white/5 backdrop-blur-xl bg-slate-900/40 shadow-2xl">
                <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                    <h2 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Daftar Kendaraan Terdaftar</h2>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-car-side text-[10px] text-slate-600"></i>
                        <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest">Total: {{ $kendaraans->count() }}</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white/[0.01] border-b border-white/5">
                                <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Identitas Visual</th>
                                <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Plat Nomor</th>
                                <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Jenis & Warna</th>
                                <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Pemilik</th>
                                <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($kendaraans as $k)
                                <tr class="group hover:bg-white/[0.02] transition-all">
                                    <td class="px-8 py-6">
                                        <div class="w-16 h-16 rounded-[1.25rem] bg-slate-950 border border-white/5 flex flex-col items-center justify-center font-black text-white group-hover:border-emerald-500/30 transition-all shadow-xl">
                                            <span class="text-[8px] text-slate-600 leading-none mb-1.5 uppercase tracking-tighter">{{ substr($k->plat_nomor ?? '-', 0, 2) }}</span>
                                            <span class="text-xl leading-none tracking-tight">{{ substr($k->plat_nomor ?? '-', 2, 4) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <p class="text-base font-black text-white tracking-widest uppercase group-hover:text-emerald-400 transition-colors">{{ $k->plat_nomor }}</p>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="px-2 py-0.5 bg-blue-500/10 text-blue-400 text-[9px] font-black uppercase rounded border border-blue-500/20 tracking-widest">{{ $k->jenis_kendaraan }}</span>
                                        </div>
                                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">{{ $k->warna ?: 'Tanpa Warna' }}</p>
                                    </td>
                                    <td class="px-8 py-6">
                                        <p class="text-xs font-bold text-slate-300">{{ $k->pemilik ?: $user->name }}</p>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex items-center justify-end gap-3">
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
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-24 text-center">
                                        <div class="w-24 h-24 bg-slate-950 border border-white/5 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 text-slate-800 shadow-2xl">
                                            <i class="fa-solid fa-car-side text-4xl opacity-10"></i>
                                        </div>
                                        <p class="text-[10px] text-slate-600 font-black uppercase tracking-[0.3em] italic">Belum ada kendaraan terdaftar</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
