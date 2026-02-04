@extends('layouts.app')

@section('title','Create Kendaraan')

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-200px)]">
    <div class="max-w-2xl w-full mx-auto p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Create Kendaraan</h2>

    <form action="{{ route('kendaraan.store') }}" method="POST" class="space-y-4">
        @csrf

        <x-form-input
            name="plat_nomor"
            label="Plat Nomor"
            type="text"
            :value="old('plat_nomor')"
            placeholder="Masukkan plat nomor kendaraan"
        />

        <x-form-input
            name="jenis_kendaraan"
            label="Jenis Kendaraan"
            type="text"
            :value="old('jenis_kendaraan')"
            placeholder="Masukkan jenis kendaraan"
        />

        <x-form-input
            name="warna"
            label="Warna"
            type="text"
            :value="old('warna')"
            placeholder="Masukkan warna kendaraan"
        />

        <x-form-input
            name="pemilik"
            label="Pemilik"
            type="text"
            :value="old('pemilik')"
            placeholder="Masukkan nama pemilik kendaraan"
        />

        <div class="mb-4">
            <label for="id_user" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                User (Opsional)
            </label>
            <select name="id_user" id="id_user" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">-- Select User --</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ old('id_user') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                @endforeach
            </select>
            @error('id_user')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('kendaraan.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-transparent rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Create
            </button>
        </div>
    </form>
    </div>
</div>
@endsection
