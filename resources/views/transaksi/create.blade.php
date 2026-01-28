@extends('layouts.app')

@section('title','Buat Transaksi')

@section('content')
@extends('layouts.app')

@section('title','Buat Transaksi')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md mt-10">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Buat Transaksi Baru</h2>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Oops!</strong>
            <span class="block sm:inline">Ada beberapa masalah dengan input Anda.</span>
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transaksi.store') }}" method="POST" class="space-y-4">
        @csrf

        <div class="mb-4">
            <label for="id_kendaraan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Kendaraan <span class="text-red-600">*</span>
            </label>
            <select name="id_kendaraan" id="id_kendaraan" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">-- Pilih Kendaraan --</option>
                @foreach($kendaraans as $k)
                    <option value="{{ $k->id_kendaraan }}" {{ old('id_kendaraan') == $k->id_kendaraan ? 'selected' : '' }}>
                        {{ $k->plat_nomor }} — {{ $k->jenis_kendaraan }}
                    </option>
                @endforeach
            </select>
            @error('id_kendaraan')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <x-form-input
            name="waktu_masuk"
            label="Waktu Masuk"
            type="datetime-local"
            :value="old('waktu_masuk')"
            required
        />

        <div class="mb-4">
            <label for="id_tarif" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Tarif <span class="text-red-600">*</span>
            </label>
            <select name="id_tarif" id="id_tarif" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
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
            <label for="id_user" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                User <span class="text-red-600">*</span>
            </label>
            <select name="id_user" id="id_user" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">-- Pilih User --</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ old('id_user') == $u->id ? 'selected' : '' }}>
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
                Area <span class="text-red-600">*</span>
            </label>
            <select name="id_area" id="id_area" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">-- Pilih Area --</option>
                @foreach($areas as $a)
                    <option value="{{ $a->id_area }}" {{ old('id_area') == $a->id_area ? 'selected' : '' }}>
                        {{ $a->nama_area ?? 'Area '.$a->id_area }}
                    </option>
                @endforeach
            </select>
            @error('id_area')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Status <span class="text-red-600">*</span>
            </label>
            <select name="status" id="status" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="masuk" {{ old('status') == 'masuk' ? 'selected' : '' }}>Masuk</option>
                <option value="keluar" {{ old('status') == 'keluar' ? 'selected' : '' }}>Keluar</option>
            </select>
            @error('status')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex justify-end space-x-2 pt-4 border-t border-gray-200">
            <a href="{{ route('transaksi.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-transparent rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Buat Transaksi
            </button>
        </div>
    </form>
</div>
@endsection

