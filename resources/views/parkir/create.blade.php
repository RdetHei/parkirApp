@extends('layouts.app')

@section('title', 'Catat Kendaraan Masuk')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Catat Kendaraan Masuk</h2>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transaksi.checkIn') }}" method="POST" class="bg-white p-6 rounded shadow-lg">
        @csrf

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Kendaraan <span class="text-red-600">*</span></label>
            <select name="id_kendaraan" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">-- Pilih Kendaraan --</option>
                @foreach($kendaraans as $k)
                    <option value="{{ $k->id_kendaraan }}" {{ old('id_kendaraan') == $k->id_kendaraan ? 'selected' : '' }}>
                        {{ $k->plat_nomor }} — {{ $k->jenis_kendaraan }} ({{ $k->pemilik ?? 'Tidak ada pemilik' }})
                    </option>
                @endforeach
            </select>
            @error('id_kendaraan')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Tarif <span class="text-red-600">*</span></label>
            <select name="id_tarif" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">-- Pilih Tarif --</option>
                @foreach($tarifs as $t)
                    <option value="{{ $t->id_tarif }}" {{ old('id_tarif') == $t->id_tarif ? 'selected' : '' }}>
                        {{ $t->jenis_kendaraan }} — Rp {{ number_format($t->tarif_perjam, 0, ',', '.') }}/jam
                    </option>
                @endforeach
            </select>
            @error('id_tarif')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Area Parkir <span class="text-red-600">*</span></label>
            <select name="id_area" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">-- Pilih Area --</option>
                @foreach($areas as $a)
                    <option value="{{ $a->id_area }}" {{ old('id_area') == $a->id_area ? 'selected' : '' }}>
                        {{ $a->nama_area }} (Kapasitas: {{ $a->kapasitas ?? '-' }})
                    </option>
                @endforeach
            </select>
            @error('id_area')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t">
            <a href="{{ route('transaksi.parkir.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                ← Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                ✓ Catat Masuk
            </button>
        </div>
    </form>
</div>
@endsection
