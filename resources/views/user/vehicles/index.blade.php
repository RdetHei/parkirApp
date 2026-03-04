@extends('layouts.app')

@section('title', 'Kendaraan Saya')

@section('content')
    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">Profil kendaraan</p>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                        Kendaraan milik {{ $user->name }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Tambah dan kelola kendaraan yang akan Anda gunakan untuk parkir.
                    </p>
                </div>
                <a href="{{ route('user.dashboard') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali ke dashboard
                </a>
            </div>

            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-900 mb-3">Tambah kendaraan baru</h2>
                <form method="POST" action="{{ route('user.vehicles.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                    @csrf
                    <div class="md:col-span-2">
                        <label for="plat_nomor" class="block text-xs font-medium text-gray-600 mb-1">Plat nomor</label>
                        <input type="text" name="plat_nomor" id="plat_nomor"
                               class="block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/70"
                               placeholder="B 1234 XYZ" required>
                    </div>
                    <div>
                        <label for="jenis_kendaraan" class="block text-xs font-medium text-gray-600 mb-1">Jenis</label>
                        <select name="jenis_kendaraan" id="jenis_kendaraan"
                                class="block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/70"
                                required>
                            <option value="">Pilih...</option>
                            <option value="motor">Motor</option>
                            <option value="mobil">Mobil</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <label for="warna" class="block text-xs font-medium text-gray-600 mb-1">Warna (opsional)</label>
                        <input type="text" name="warna" id="warna"
                               class="block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/70"
                               placeholder="Hitam">
                    </div>
                    <div class="md:col-span-3">
                        <label for="pemilik" class="block text-xs font-medium text-gray-600 mb-1">Nama pemilik (opsional)</label>
                        <input type="text" name="pemilik" id="pemilik"
                               class="block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/70"
                               placeholder="Kosongkan jika sama dengan nama akun">
                    </div>
                    <div class="md:col-span-1">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center rounded-xl bg-emerald-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
                            Simpan kendaraan
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900">Daftar kendaraan terdaftar</h2>
                </div>
                <div class="p-4">
                    @if($kendaraans->count())
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-600 text-xs uppercase tracking-wide">Plat</th>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-600 text-xs uppercase tracking-wide">Jenis</th>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-600 text-xs uppercase tracking-wide">Warna</th>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-600 text-xs uppercase tracking-wide">Pemilik</th>
                                        <th class="px-4 py-2 text-right font-semibold text-gray-600 text-xs uppercase tracking-wide">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($kendaraans as $k)
                                        <tr>
                                            <form action="{{ route('user.vehicles.update', $k) }}" method="POST" class="contents">
                                                @csrf
                                                @method('PUT')
                                                <td class="px-4 py-2 font-semibold text-gray-900">
                                                    <input type="text"
                                                           name="plat_nomor"
                                                           value="{{ old('plat_nomor', $k->plat_nomor) }}"
                                                           class="w-full rounded-lg border border-gray-200 px-2 py-1.5 text-xs font-semibold text-gray-900 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/70"
                                                           required>
                                                </td>
                                                <td class="px-4 py-2 text-gray-700">
                                                    <select name="jenis_kendaraan"
                                                            class="w-full rounded-lg border border-gray-200 px-2 py-1.5 text-xs text-gray-800 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/70"
                                                            required>
                                                        <option value="motor" {{ $k->jenis_kendaraan === 'motor' ? 'selected' : '' }}>Motor</option>
                                                        <option value="mobil" {{ $k->jenis_kendaraan === 'mobil' ? 'selected' : '' }}>Mobil</option>
                                                        <option value="lainnya" {{ $k->jenis_kendaraan === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                                    </select>
                                                </td>
                                                <td class="px-4 py-2 text-gray-700">
                                                    <input type="text"
                                                           name="warna"
                                                           value="{{ old('warna', $k->warna) }}"
                                                           class="w-full rounded-lg border border-gray-200 px-2 py-1.5 text-xs text-gray-800 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/70"
                                                           placeholder="-">
                                                </td>
                                                <td class="px-4 py-2 text-gray-700">
                                                    <input type="text"
                                                           name="pemilik"
                                                           value="{{ old('pemilik', $k->pemilik) }}"
                                                           class="w-full rounded-lg border border-gray-200 px-2 py-1.5 text-xs text-gray-800 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/70"
                                                           placeholder="-">
                                                </td>
                                                <td class="px-4 py-2 text-right space-x-2">
                                                    <button type="submit"
                                                            class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200">
                                                        Simpan
                                                    </button>
                                                    <form action="{{ route('user.vehicles.destroy', $k) }}"
                                                          method="POST"
                                                          class="inline"
                                                          onsubmit="return confirm('Hapus kendaraan ini dari akun Anda?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-50 text-red-600 hover:bg-red-100">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </td>
                                            </form>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Belum ada kendaraan yang terdaftar di akun Anda.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

