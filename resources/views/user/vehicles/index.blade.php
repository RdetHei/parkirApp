@extends('layouts.app')

@section('title', 'Kendaraan Saya')

@section('content')
<div class="p-8 relative z-10">
    <div class="max-w-5xl mx-auto space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 animate-fade-in-up">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-sky-500/10 text-sky-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-sky-500/20">
                        <i class="fa-solid fa-car mr-1"></i>
                        Profil Kendaraan
                    </span>
                </div>
                <h1 class="text-4xl font-bold tracking-tight text-white">Kendaraan Anda</h1>
                <p class="text-slate-400 text-sm mt-2">Tambah dan kelola kendaraan yang terdaftar di akun Anda.</p>
            </div>
            <a href="{{ route('user.dashboard') }}" class="px-6 py-3 bg-white/[0.03] hover:bg-white/[0.08] text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border border-white/10 flex items-center justify-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="px-4 py-3 bg-emerald-500/10 text-emerald-500 text-sm font-bold rounded-xl border border-emerald-500/20 flex items-center gap-3">
                    <i class="fa-solid fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="px-4 py-3 bg-rose-500/10 text-rose-500 text-sm font-bold rounded-xl border border-rose-500/20 flex items-center gap-3">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif
        @if($errors->any())
            <div class="animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="px-4 py-3 bg-rose-500/10 text-rose-500 text-sm font-bold rounded-xl border border-rose-500/20">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Add Vehicle Form -->
        <div class="card-pro animate-fade-in-up" style="animation-delay: 0.2s">
            <h2 class="text-sm font-black text-white uppercase tracking-widest mb-6">Tambah Kendaraan Baru</h2>
            <form method="POST" action="{{ route('user.vehicles.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @csrf
                <div class="md:col-span-2">
                    <label for="plat_nomor" class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Plat Nomor</label>
                    <input type="text" name="plat_nomor" id="plat_nomor"
                           class="form-input-dark"
                           placeholder="B 1234 XYZ" required>
                </div>
                <div>
                    <label for="jenis_kendaraan" class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Jenis</label>
                    <select name="jenis_kendaraan" id="jenis_kendaraan"
                            class="form-input-dark"
                            required>
                        <option value="">Pilih...</option>
                        <option value="motor">Motor</option>
                        <option value="mobil">Mobil</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="md:col-span-1">
                    <label for="warna" class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Warna <span class="text-slate-600 normal-case">(Opsional)</span></label>
                    <input type="text" name="warna" id="warna"
                           class="form-input-dark"
                           placeholder="Hitam">
                </div>
                <div class="md:col-span-3">
                    <label for="pemilik" class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Pemilik <span class="text-slate-600 normal-case">(Opsional)</span></label>
                    <input type="text" name="pemilik" id="pemilik"
                           class="form-input-dark"
                           placeholder="Kosongkan jika sama dengan nama akun">
                </div>
                <div class="md:col-span-1">
                    <button type="submit"
                            class="w-full px-6 py-3 bg-emerald-500 text-slate-950 font-black text-[10px] uppercase tracking-widest rounded-xl transition-all hover:bg-emerald-400 hover:shadow-[0_0_15px_rgba(16,185,129,0.3)]">
                        Simpan
                    </button>
                </div>
            </form>
        </div>

        <!-- Vehicle List -->
        <div class="card-pro !p-0 overflow-hidden animate-fade-in-up" style="animation-delay: 0.3s">
            <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                <h2 class="text-sm font-black text-white uppercase tracking-widest">Daftar Kendaraan</h2>
            </div>
            
            @if($kendaraans->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-white/5">
                            <tr>
                                <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Plat Nomor</th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Jenis</th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Warna</th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Pemilik</th>
                                <th class="px-6 py-4 text-center text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($kendaraans as $k)
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <form action="{{ route('user.vehicles.update', $k) }}" method="POST" class="contents">
                                        @csrf
                                        @method('PUT')
                                        <td class="px-6 py-4 font-bold text-white uppercase">
                                            <input type="text" name="plat_nomor" value="{{ old('plat_nomor', $k->plat_nomor) }}" class="form-input-dark !py-2 !text-xs" required>
                                        </td>
                                        <td class="px-6 py-4">
                                            <select name="jenis_kendaraan" class="form-input-dark !py-2 !text-xs" required>
                                                <option value="motor" {{ $k->jenis_kendaraan === 'motor' ? 'selected' : '' }}>Motor</option>
                                                <option value="mobil" {{ $k->jenis_kendaraan === 'mobil' ? 'selected' : '' }}>Mobil</option>
                                                <option value="lainnya" {{ $k->jenis_kendaraan === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="text" name="warna" value="{{ old('warna', $k->warna) }}" class="form-input-dark !py-2 !text-xs" placeholder="-">
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="text" name="pemilik" value="{{ old('pemilik', $k->pemilik) }}" class="form-input-dark !py-2 !text-xs" placeholder="-">
                                        </td>
                                        <td class="px-6 py-4 text-center space-x-2">
                                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white/5 text-white hover:bg-white/10 border border-white/10">Simpan</button>
                                        </td>
                                    </form>
                                    <td class="px-6 py-4 text-center">
                                        <form action="{{ route('user.vehicles.destroy', $k) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kendaraan ini dari akun Anda?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-rose-500/10 text-rose-500 hover:bg-rose-500/20 border border-rose-500/20">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-white/5 text-slate-500 flex items-center justify-center rounded-2xl mx-auto mb-4 border border-white/10">
                        <i class="fa-solid fa-car-side text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-white">Belum Ada Kendaraan</h3>
                    <p class="text-sm text-slate-400 mt-1">Anda belum mendaftarkan kendaraan apapun.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

