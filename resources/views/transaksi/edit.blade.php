@extends('layouts.app')

@section('title','Edit Transaksi')

@section('content')
<div class="max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Edit Transaksi</h2>

    <form action="{{ route('transaksi.update', $item) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Waktu Keluar</label>
            <input type="datetime-local" name="waktu_keluar" value="{{ old('waktu_keluar', optional($item->waktu_keluar)->format('Y-m-d\TH:i') ) }}" class="mt-1 block w-full border-gray-300 rounded-md">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Kendaraan</label>
            <select name="id_kendaraan" class="mt-1 block w-full border-gray-300 rounded-md">
                <option value="">-- Select Kendaraan --</option>
                @foreach($kendaraans as $k)
                    <option value="{{ $k->id }}" {{ (old('id_kendaraan', $item->id_kendaraan) == $k->id) ? 'selected' : '' }}>{{ $k->plat_nomor }} — {{ $k->jenis_kendaraan }}</option>
                @endforeach
            </select>
            @error('id_kendaraan')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Tarif</label>
            <select name="id_tarif" class="mt-1 block w-full border-gray-300 rounded-md">
                <option value="">-- Select Tarif --</option>
                @foreach($tarifs as $t)
                    <option value="{{ $t->id }}" {{ (old('id_tarif', $item->id_tarif) == $t->id) ? 'selected' : '' }}>{{ $t->jenis }} — {{ $t->harga }}</option>
                @endforeach
            </select>
            @error('id_tarif')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">User</label>
            <select name="id_user" class="mt-1 block w-full border-gray-300 rounded-md">
                <option value="">-- Select User --</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ (old('id_user', $item->id_user) == $u->id) ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                @endforeach
            </select>
            @error('id_user')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Area</label>
            <select name="id_area" class="mt-1 block w-full border-gray-300 rounded-md">
                <option value="">-- Select Area --</option>
                @foreach($areas as $a)
                    <option value="{{ $a->id }}" {{ (old('id_area', $item->id_area) == $a->id) ? 'selected' : '' }}>{{ $a->nama_area ?? 'Area '.$a->id }}</option>
                @endforeach
            </select>
            @error('id_area')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Durasi (jam)</label>
            <input type="number" name="durasi_jam" value="{{ old('durasi_jam', $item->durasi_jam) }}" class="mt-1 block w-full border-gray-300 rounded-md">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Biaya Total</label>
            <input type="number" name="biaya_total" value="{{ old('biaya_total', $item->biaya_total) }}" class="mt-1 block w-full border-gray-300 rounded-md">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" class="mt-1 block w-full border-gray-300 rounded-md">
                <option value="masuk" {{ $item->status=='masuk'?'selected':'' }}>Masuk</option>
                <option value="keluar" {{ $item->status=='keluar'?'selected':'' }}>Keluar</option>
            </select>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('transaksi.index') }}" class="mr-2">Cancel</a>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Update</button>
        </div>
    </form>
</div>
@endsection
