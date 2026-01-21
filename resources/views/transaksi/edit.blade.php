@extends('layouts.app')

@section('title','Edit Transaksi')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Edit Transaksi #{{ str_pad($item->id_parkir, 8, '0', STR_PAD_LEFT) }}</h2>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transaksi.update', $item->id_parkir) }}" method="POST" class="bg-white p-6 rounded shadow-lg">
        @csrf
        @method('PUT')

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Waktu Keluar</label>
            <input type="datetime-local" name="waktu_keluar" 
                   value="{{ old('waktu_keluar', optional($item->waktu_keluar)->format('Y-m-d\TH:i') ) }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('waktu_keluar')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Kendaraan</label>
            <select name="id_kendaraan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Kendaraan --</option>
                @foreach($kendaraans as $k)
                    <option value="{{ $k->id_kendaraan }}" {{ (old('id_kendaraan', $item->id_kendaraan) == $k->id_kendaraan) ? 'selected' : '' }}>
                        {{ $k->plat_nomor }} — {{ $k->jenis_kendaraan }}
                    </option>
                @endforeach
            </select>
            @error('id_kendaraan')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Tarif</label>
            <select name="id_tarif" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Tarif --</option>
                @foreach($tarifs as $t)
                    <option value="{{ $t->id_tarif }}" {{ (old('id_tarif', $item->id_tarif) == $t->id_tarif) ? 'selected' : '' }}>
                        {{ $t->jenis_kendaraan }} — Rp {{ number_format($t->tarif_perjam, 0, ',', '.') }}/jam
                    </option>
                @endforeach
            </select>
            @error('id_tarif')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-2">User</label>
            <select name="id_user" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih User --</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ (old('id_user', $item->id_user) == $u->id) ? 'selected' : '' }}>
                        {{ $u->name }} ({{ $u->email }})
                    </option>
                @endforeach
            </select>
            @error('id_user')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Area</label>
            <select name="id_area" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Area --</option>
                @foreach($areas as $a)
                    <option value="{{ $a->id_area }}" {{ (old('id_area', $item->id_area) == $a->id_area) ? 'selected' : '' }}>
                        {{ $a->nama_area ?? 'Area '.$a->id_area }}
                    </option>
                @endforeach
            </select>
            @error('id_area')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Durasi (jam)</label>
            <input type="number" name="durasi_jam" value="{{ old('durasi_jam', $item->durasi_jam) }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('durasi_jam')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Biaya Total (Rp)</label>
            <input type="number" name="biaya_total" value="{{ old('biaya_total', $item->biaya_total) }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('biaya_total')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-600">*</span></label>
            <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="masuk" {{ $item->status=='masuk'?'selected':'' }}>Masuk</option>
                <option value="keluar" {{ $item->status=='keluar'?'selected':'' }}>Keluar</option>
            </select>
            @error('status')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t">
            <a href="{{ route('transaksi.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                ← Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                ✓ Update
            </button>
        </div>
    </form>
</div>
@endsection
