@extends('layouts.app')

@section('title','Create Area Parkir')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('area-parkir.index'),
        'title' => 'Buat Area Parkir Baru',
        'description' => 'Tambahkan data area parkir baru ke sistem',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>',
        'cardTitle' => 'Form Area Parkir',
        'cardDescription' => 'Lengkapi detail area parkir',
        'action' => route('area-parkir.store'),
        'method' => 'POST',
        'submitText' => 'Buat'
    ])
        <x-form-input
            name="nama_area"
            label="Nama Area"
            type="text"
            :value="old('nama_area')"
            placeholder="Masukkan nama area parkir"
        />

        <x-form-input
            name="kapasitas"
            label="Kapasitas"
            type="number"
            :value="old('kapasitas')"
            placeholder="Masukkan kapasitas area parkir"
        />
    @endcomponent
@endsection
