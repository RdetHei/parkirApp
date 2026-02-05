@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('transaksi.index'),
        'title' => 'Edit Transaksi #'.str_pad($item->id_parkir, 8, '0', STR_PAD_LEFT),
        'description' => 'Ubah detail transaksi parkir',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
        'cardTitle' => 'Form Edit Transaksi',
        'cardDescription' => 'Sesuaikan informasi transaksi',
        'action' => route('transaksi.update', $item->id_parkir),
        'method' => 'PUT',
        'submitText' => 'Update'
    ])
        <div>
            <label for="waktu_keluar" class="block text-sm font-semibold text-gray-700 mb-2">Waktu Keluar</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <input type="datetime-local" name="waktu_keluar" id="waktu_keluar" value="{{ old('waktu_keluar', optional($item->waktu_keluar)->format('Y-m-d\TH:i')) }}"
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('waktu_keluar') border-red-500 @enderror">
            </div>
            @error('waktu_keluar')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="id_kendaraan" class="block text-sm font-semibold text-gray-700 mb-2">Kendaraan</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                </div>
                <select name="id_kendaraan" id="id_kendaraan"
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('id_kendaraan') border-red-500 @enderror">
                    <option value="">-- Pilih Kendaraan --</option>
                    @foreach($kendaraans as $k)
                        <option value="{{ $k->id_kendaraan }}" {{ (old('id_kendaraan', $item->id_kendaraan) == $k->id_kendaraan) ? 'selected' : '' }}>{{ $k->plat_nomor }} — {{ $k->jenis_kendaraan }}</option>
                    @endforeach
                </select>
            </div>
            @error('id_kendaraan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="id_tarif" class="block text-sm font-semibold text-gray-700 mb-2">Tarif</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <select name="id_tarif" id="id_tarif"
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('id_tarif') border-red-500 @enderror">
                    <option value="">-- Pilih Tarif --</option>
                    @foreach($tarifs as $t)
                        <option value="{{ $t->id_tarif }}" {{ (old('id_tarif', $item->id_tarif) == $t->id_tarif) ? 'selected' : '' }}>{{ $t->jenis_kendaraan }} — Rp {{ number_format($t->tarif_perjam, 0, ',', '.') }}/jam</option>
                    @endforeach
                </select>
            </div>
            @error('id_tarif')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="id_user" class="block text-sm font-semibold text-gray-700 mb-2">User</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <select name="id_user" id="id_user"
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('id_user') border-red-500 @enderror">
                    <option value="">-- Pilih User --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ (old('id_user', $item->id_user) == $u->id) ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>
            @error('id_user')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="id_area" class="block text-sm font-semibold text-gray-700 mb-2">Area Parkir</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <select name="id_area" id="id_area"
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('id_area') border-red-500 @enderror">
                    <option value="">-- Pilih Area --</option>
                    @foreach($areas as $a)
                        <option value="{{ $a->id_area }}" {{ (old('id_area', $item->id_area) == $a->id_area) ? 'selected' : '' }}>{{ $a->nama_area ?? 'Area '.$a->id_area }}</option>
                    @endforeach
                </select>
            </div>
            @error('id_area')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="durasi_jam" class="block text-sm font-semibold text-gray-700 mb-2">Durasi (jam)</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <input type="number" name="durasi_jam" id="durasi_jam" value="{{ old('durasi_jam', $item->durasi_jam) }}" min="0" placeholder="Durasi dalam jam"
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('durasi_jam') border-red-500 @enderror">
            </div>
            @error('durasi_jam')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="biaya_total" class="block text-sm font-semibold text-gray-700 mb-2">Biaya Total (Rp)</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <input type="number" name="biaya_total" id="biaya_total" value="{{ old('biaya_total', $item->biaya_total) }}" min="0" placeholder="Biaya total"
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('biaya_total') border-red-500 @enderror">
            </div>
            @error('biaya_total')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <select name="status" id="status" required
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('status') border-red-500 @enderror">
                    <option value="masuk" {{ (old('status', $item->status) == 'masuk') ? 'selected' : '' }}>Masuk</option>
                    <option value="keluar" {{ (old('status', $item->status) == 'keluar') ? 'selected' : '' }}>Keluar</option>
                </select>
            </div>
            @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    @endcomponent
@endsection
