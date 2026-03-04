@extends('layouts.app')

@section('title', 'Tambah Kamera')

@section('content')
    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Tambah Kamera Baru</h1>
                    <p class="mt-1 text-sm text-gray-500">Hubungkan IP Webcam atau kamera CCTV ke sistem pemantauan.</p>
                </div>
                <a href="{{ route('kamera.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Batal
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="px-8 py-6 border-b border-gray-50 bg-gradient-to-r from-indigo-50/50 to-white flex items-center gap-4">
                            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-gray-900">Konfigurasi Perangkat</h2>
                                <p class="text-xs text-gray-500 font-medium">Lengkapi detail koneksi kamera Anda.</p>
                            </div>
                        </div>

                        <form action="{{ route('kamera.store') }}" method="POST" class="p-8 space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="nama" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nama Perangkat</label>
                                    <div class="relative">
                                        <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required placeholder="Contoh: Kamera Gate Masuk 01"
                                               class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('nama') border-red-500 @enderror">
                                    </div>
                                    @error('nama')<p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="tipe" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tipe Penggunaan</label>
                                    <select name="tipe" id="tipe" required
                                            class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                        @foreach(\App\Models\Camera::tipeOptions() as $value => $label)
                                            <option value="{{ $value }}" {{ old('tipe', \App\Models\Camera::TIPE_SCANNER) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('tipe')<p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                                </div>

                                <div class="flex items-end pb-1">
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="hidden" name="is_default" value="0">
                                        <div class="relative flex items-center">
                                            <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}
                                                   class="peer h-6 w-11 appearance-none rounded-full bg-gray-200 transition-all checked:bg-indigo-600 cursor-pointer">
                                            <span class="absolute left-1 h-4 w-4 rounded-full bg-white transition-all peer-checked:left-6 cursor-pointer"></span>
                                        </div>
                                        <span class="text-sm font-bold text-gray-600 group-hover:text-gray-900 transition-colors">Kamera Utama</span>
                                    </label>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="url" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">URL Stream (Endpoint)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                        </div>
                                        <input type="url" name="url" id="url" value="{{ old('url', 'http://localhost:8080/video') }}" required placeholder="http://192.168.1.5:8080/video"
                                               class="block w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-mono font-bold text-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('url') border-red-500 @enderror">
                                    </div>
                                    @error('url')<p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="pt-6">
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Simpan Perangkat
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-indigo-900 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden group">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-800 rounded-full opacity-50 transition-transform group-hover:scale-150"></div>
                        <div class="relative z-10">
                            <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Panduan Cepat
                            </h3>
                            <div class="space-y-4 text-sm text-indigo-100">
                                <div class="flex gap-3">
                                    <span class="w-6 h-6 rounded-lg bg-indigo-800 flex items-center justify-center text-[10px] font-bold shrink-0">1</span>
                                    <p>Pastikan aplikasi <strong>IP Webcam</strong> di HP sudah berjalan.</p>
                                </div>
                                <div class="flex gap-3">
                                    <span class="w-6 h-6 rounded-lg bg-indigo-800 flex items-center justify-center text-[10px] font-bold shrink-0">2</span>
                                    <p>Gunakan URL yang tampil di layar HP, tambahkan <code class="text-indigo-300">/video</code> di akhir.</p>
                                </div>
                                <div class="flex gap-3">
                                    <span class="w-6 h-6 rounded-lg bg-indigo-800 flex items-center justify-center text-[10px] font-bold shrink-0">3</span>
                                    <p>Pilih tipe <strong>Scanner</strong> jika kamera diletakkan di pintu masuk.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900 mb-4">Butuh Bantuan?</h3>
                        <p class="text-xs text-gray-500 leading-relaxed mb-6">Jika kamera tidak muncul di sistem, pastikan HP dan Server berada dalam satu jaringan WiFi yang sama.</p>
                        <a href="#" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                            Buka dokumentasi lengkap
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

