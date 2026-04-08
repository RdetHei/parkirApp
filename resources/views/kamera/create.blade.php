@extends('layouts.app')

@section('title', 'Tambah Kamera')

@section('content')
    <div class="px-4 py-6 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 animate-fade-in">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <span class="px-3 py-1 bg-indigo-500/10 text-indigo-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-indigo-500/20">
                            Hardware Setup
                        </span>
                    </div>
                    <h1 class="text-4xl font-bold tracking-tight text-white">Tambah <span class="text-indigo-500">Kamera</span></h1>
                    <p class="text-slate-400 text-sm mt-2">Hubungkan IP Webcam atau kamera CCTV ke sistem pemantauan cerdas.</p>
                </div>
                <a href="{{ route('kamera.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 border border-white/5 text-slate-400 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 hover:text-white transition-all shadow-lg">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Batal
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <div class="lg:col-span-2">
                    <div class="card-pro !p-0 overflow-hidden animate-fade-in-up">
                        <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center gap-4">
                            <div class="w-12 h-12 bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-500 border border-indigo-500/20 shadow-lg shadow-indigo-500/5">
                                <i class="fa-solid fa-video text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-black text-white uppercase tracking-widest">Konfigurasi Perangkat</h2>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-tight mt-0.5">Lengkapi detail koneksi kamera Anda.</p>
                            </div>
                        </div>

                        <form action="{{ route('kamera.store') }}" method="POST" class="p-8 space-y-8">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="md:col-span-2 space-y-2">
                                    <label for="nama" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Nama Perangkat</label>
                                    <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required placeholder="Contoh: Kamera Gate Masuk 01"
                                           class="block w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-semibold @error('nama') border-rose-500 @enderror">
                                    @error('nama')<p class="mt-2 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="tipe" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Tipe Penggunaan</label>
                                    <select name="tipe" id="tipe" required
                                            class="block w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-semibold appearance-none cursor-pointer">
                                        @foreach(\App\Models\Camera::tipeOptions() as $value => $label)
                                            <option value="{{ $value }}" {{ old('tipe', \App\Models\Camera::TIPE_SCANNER) == $value ? 'selected' : '' }} class="bg-slate-900 text-white">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('tipe')<p class="mt-2 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
                                </div>

                                <div class="flex items-center gap-4 p-5 bg-slate-950/50 rounded-2xl border border-white/5 group hover:border-indigo-500/30 transition-all">
                                    <input type="hidden" name="is_default" value="0">
                                    <input type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}
                                           class="w-5 h-5 bg-slate-900 border-white/10 text-indigo-500 rounded focus:ring-indigo-500/20 focus:ring-offset-0">
                                    <label for="is_default" class="text-xs font-bold text-slate-400 group-hover:text-white transition-colors cursor-pointer uppercase tracking-widest">Jadikan Kamera Utama</label>
                                </div>

                                <div class="md:col-span-2 space-y-2">
                                    <label for="url" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">URL Stream (Endpoint)</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-indigo-500 transition-colors">
                                            <i class="fa-solid fa-link text-xs"></i>
                                        </div>
                                        <input type="url" name="url" id="url" value="{{ old('url', 'http://localhost:8080/video') }}" required placeholder="http://192.168.1.5:8080/video"
                                               class="block w-full pl-11 pr-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-sm font-mono font-bold text-indigo-400 placeholder:text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all @error('url') border-rose-500 @enderror">
                                    </div>
                                    @error('url')<p class="mt-2 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="pt-6">
                                <button type="submit" class="w-full bg-indigo-500 hover:bg-indigo-400 text-slate-950 font-black py-5 rounded-2xl shadow-xl shadow-indigo-500/10 transition-all active:scale-[0.98] flex items-center justify-center gap-3 uppercase text-[11px] tracking-[0.2em]">
                                    <i class="fa-solid fa-check text-base"></i>
                                    Simpan Perangkat
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="space-y-8 animate-fade-in-up" style="animation-delay: 0.2s">
                    <div class="card-pro bg-indigo-500/5 border-indigo-500/10 p-8 text-white relative overflow-hidden group">
                        <div class="absolute -right-8 -top-8 w-32 h-32 bg-indigo-500/10 rounded-full blur-3xl transition-transform group-hover:scale-150"></div>
                        <div class="relative z-10">
                            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-6 flex items-center gap-3 text-indigo-400">
                                <i class="fa-solid fa-circle-info text-xs"></i>
                                Panduan Cepat
                            </h3>
                            <div class="space-y-6">
                                <div class="flex gap-4">
                                    <span class="w-8 h-8 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-[10px] font-black text-indigo-400 shrink-0">1</span>
                                    <div class="space-y-1">
                                        <p class="text-xs font-bold text-white uppercase tracking-tight">Koneksi IP</p>
                                        <p class="text-[11px] text-slate-400 leading-relaxed">Gunakan alamat IP statis agar koneksi tidak terputus saat router restart.</p>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <span class="w-8 h-8 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-[10px] font-black text-indigo-400 shrink-0">2</span>
                                    <div class="space-y-1">
                                        <p class="text-xs font-bold text-white uppercase tracking-tight">Format URL</p>
                                        <p class="text-[11px] text-slate-400 leading-relaxed">Pastikan URL berakhir dengan endpoint video stream (misal: /video, /stream.mjpg).</p>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <span class="w-8 h-8 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-[10px] font-black text-indigo-400 shrink-0">3</span>
                                    <div class="space-y-1">
                                        <p class="text-xs font-bold text-white uppercase tracking-tight">Keamanan</p>
                                        <p class="text-[11px] text-slate-400 leading-relaxed">Jika kamera diproteksi, sertakan auth di URL: http://user:pass@ip:port/video.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-pro border-white/5 bg-white/[0.02] p-8">
                        <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Butuh Bantuan?</h4>
                        <p class="text-[11px] text-slate-600 leading-relaxed">Jika kamera tidak terdeteksi, pastikan server dan perangkat berada dalam jaringan (LAN/WiFi) yang sama.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection