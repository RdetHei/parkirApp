@section('content')
@extends('layouts.app')

@section('title', 'Catat Kendaraan Masuk')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('transaksi.index', ['status' => 'masuk']),
        'title' => 'Catat Kendaraan Masuk',
        'description' => 'Isi data untuk mencatat kendaraan masuk',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>',
        'cardTitle' => 'Form Kendaraan Masuk',
        'cardDescription' => 'Lengkapi detail kendaraan dan area parkir',
        'action' => route('transaksi.checkIn'),
        'method' => 'POST',
        'submitText' => 'Catat Masuk'
    ])
        <div class="mb-4">
            <label for="id_kendaraan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Kendaraan <span class="text-red-600">*</span>
            </label>
            <select name="id_kendaraan" id="id_kendaraan" required class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm bg-gray-800 text-white">
                <option value="">-- Pilih Kendaraan --</option>
                @foreach($kendaraans as $k)
                    <option value="{{ $k->id_kendaraan }}" {{ old('id_kendaraan') == $k->id_kendaraan ? 'selected' : '' }}>
                        {{ $k->plat_nomor }} — {{ $k->jenis_kendaraan }} ({{ $k->pemilik ?? 'Tidak ada pemilik' }})
                    </option>
                @endforeach
            </select>
            @error('id_kendaraan')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="id_tarif" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Tarif <span class="text-red-600">*</span>
            </label>
            <select name="id_tarif" id="id_tarif" required class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm bg-gray-800 text-white">
                <option value="">-- Pilih Tarif --</option>
                @foreach($tarifs as $t)
                    <option value="{{ $t->id_tarif }}" {{ old('id_tarif') == $t->id_tarif ? 'selected' : '' }}>
                        {{ $t->jenis_kendaraan }} — Rp {{ number_format($t->tarif_perjam, 0, ',', '.') }}/jam
                    </option>
                @endforeach
            </select>
            @error('id_tarif')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="id_area" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Area Parkir <span class="text-red-600">*</span>
            </label>
            <select name="id_area" id="id_area" required class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm bg-gray-800 text-white">
                <option value="">-- Pilih Area --</option>
                @foreach($areas as $a)
                    <option value="{{ $a->id_area }}" {{ old('id_area') == $a->id_area ? 'selected' : '' }}>
                        {{ $a->nama_area }} (Kapasitas: {{ $a->kapasitas ?? '-' }})
                    </option>
                @endforeach
            </select>
            @error('id_area')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <x-form-input
            name="catatan"
            label="Catatan (Opsional)"
            type="textarea"
            :value="old('catatan')"
            placeholder="Tambahkan catatan jika perlu"
        />
    @endcomponent
@endsection
