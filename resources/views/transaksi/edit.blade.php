
@extends('layouts.app')

@section('title','Edit Transaksi')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('transaksi.index'),
        'title' => 'Edit Transaksi #'.str_pad($item->id_parkir, 8, '0', STR_PAD_LEFT),
        'description' => 'Ubah detail transaksi parkir',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>',
        'cardTitle' => 'Form Edit Transaksi',
        'cardDescription' => 'Sesuaikan informasi transaksi',
        'action' => route('transaksi.update', $item->id_parkir),
        'method' => 'PUT',
        'submitText' => 'Update'
    ])
        <x-form-input
            name="waktu_keluar"
            label="Waktu Keluar"
            type="datetime-local"
            :value="old('waktu_keluar', optional($item->waktu_keluar)->format('Y-m-d\TH:i'))"
            class="bg-gray-800 text-white border-gray-600 focus:ring-green-500 focus:border-green-500"
        />

        <div class="mb-4">
            <label for="id_kendaraan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Kendaraan
            </label>
            <select name="id_kendaraan" id="id_kendaraan" class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm bg-gray-800 text-white">
                <option value="">-- Pilih Kendaraan --</option>
                @foreach($kendaraans as $k)
                    <option value="{{ $k->id_kendaraan }}" {{ (old('id_kendaraan', $item->id_kendaraan) == $k->id_kendaraan) ? 'selected' : '' }}>
                        {{ $k->plat_nomor }} — {{ $k->jenis_kendaraan }}
                    </option>
                @endforeach
            </select>
            @error('id_kendaraan')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="id_tarif" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Tarif
            </label>
            <select name="id_tarif" id="id_tarif" class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm bg-gray-800 text-white">
                <option value="">-- Pilih Tarif --</option>
                @foreach($tarifs as $t)
                    <option value="{{ $t->id_tarif }}" {{ (old('id_tarif', $item->id_tarif) == $t->id_tarif) ? 'selected' : '' }}>
                        {{ $t->jenis_kendaraan }} — Rp {{ number_format($t->tarif_perjam, 0, ',', '.') }}/jam
                    </option>
                @endforeach
            </select>
            @error('id_tarif')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="id_user" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                User
            </label>
            <select name="id_user" id="id_user" class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm bg-gray-800 text-white">
                <option value="">-- Pilih User --</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ (old('id_user', $item->id_user) == $u->id) ? 'selected' : '' }}>
                        {{ $u->name }} ({{ $u->email }})
                    </option>
                @endforeach
            </select>
            @error('id_user')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="id_area" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Area
            </label>
            <select name="id_area" id="id_area" class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm bg-gray-800 text-white">
                <option value="">-- Pilih Area --</option>
                @foreach($areas as $a)
                    <option value="{{ $a->id_area }}" {{ (old('id_area', $item->id_area) == $a->id_area) ? 'selected' : '' }}>
                        {{ $a->nama_area ?? 'Area '.$a->id_area }}
                    </option>
                @endforeach
            </select>
            @error('id_area')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <x-form-input
            name="durasi_jam"
            label="Durasi (jam)"
            type="number"
            :value="old('durasi_jam', $item->durasi_jam)"
        />

        <x-form-input
            name="biaya_total"
            label="Biaya Total (Rp)"
            type="number"
            :value="old('biaya_total', $item->biaya_total)"
        />

        <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Status
            </label>
            <select name="status" id="status" required class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm bg-gray-800 text-white">
                <option value="masuk" {{ (old('status', $item->status) == 'masuk')?'selected':'' }}>Masuk</option>
                <option value="keluar" {{ (old('status', $item->status) == 'keluar')?'selected':'' }}>Keluar</option>
            </select>
            @error('status')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>
    @endcomponent
@endsection

