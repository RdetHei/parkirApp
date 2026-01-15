@extends('layouts.app')

@section('title','Create Kendaraan')

@section('content')
<div class="max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Create Kendaraan</h2>

    <form action="{{ route('kendaraan.store') }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Plat Nomor</label>
            <input type="text" name="plat_nomor" value="{{ old('plat_nomor') }}" class="mt-1 block w-full border-gray-300 rounded-md">
            @error('plat_nomor')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Jenis Kendaraan</label>
            <input type="text" name="jenis_kendaraan" value="{{ old('jenis_kendaraan') }}" class="mt-1 block w-full border-gray-300 rounded-md">
            @error('jenis_kendaraan')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Warna</label>
            <input type="text" name="warna" value="{{ old('warna') }}" class="mt-1 block w-full border-gray-300 rounded-md">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Pemilik</label>
            <input type="text" name="pemilik" value="{{ old('pemilik') }}" class="mt-1 block w-full border-gray-300 rounded-md">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">User</label>
            <select name="id_user" class="mt-1 block w-full border-gray-300 rounded-md">
                <option value="">-- Select User --</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ old('id_user') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                @endforeach
            </select>
            @error('id_user')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        <div class="flex justify-end">
            <a href="{{ route('kendaraan.index') }}" class="mr-2">Cancel</a>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Create</button>
        </div>
    </form>
</div>
@endsection
