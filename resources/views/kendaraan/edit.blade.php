
@extends('layouts.app')

@section('title','Edit Kendaraan')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('kendaraan.index'),
        'title' => 'Edit Kendaraan',
        'description' => 'Ubah data kendaraan yang sudah ada di sistem',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19H5a2 2 0 01-2-2V7a2 2 0 012-2h4m0 14v-5h12v5M9 19L21 3"></path>
                        </svg>',
        'cardTitle' => 'Form Edit Kendaraan',
        'cardDescription' => 'Sesuaikan detail kendaraan',
        'action' => route('kendaraan.update', $item),
        'method' => 'PUT',
        'submitText' => 'Update'
    ])
        <x-form-input
            name="plat_nomor"
            label="Plat Nomor"
            type="text"
            :value="old('plat_nomor', $item->plat_nomor)"
            placeholder="Masukkan plat nomor kendaraan"
        />

        <x-form-input
            name="jenis_kendaraan"
            label="Jenis Kendaraan"
            type="text"
            :value="old('jenis_kendaraan', $item->jenis_kendaraan)"
            placeholder="Masukkan jenis kendaraan"
        />

        <x-form-input
            name="warna"
            label="Warna"
            type="text"
            :value="old('warna', $item->warna)"
            placeholder="Masukkan warna kendaraan"
        />

        <x-form-input
            name="pemilik"
            label="Pemilik"
            type="text"
            :value="old('pemilik', $item->pemilik)"
            placeholder="Masukkan nama pemilik kendaraan"
        />

        <div class="mb-4">
            <label for="id_user" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                User (Opsional)
            </label>
            <select name="id_user" id="id_user" class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm bg-gray-800 text-white">
                <option value="">-- Select User --</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ (old('id_user', $item->id_user) == $u->id) ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                @endforeach
            </select>
            @error('id_user')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>
    @endcomponent
@endsection
