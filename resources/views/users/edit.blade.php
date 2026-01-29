
@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('users.index'),
        'title' => 'Edit User',
        'description' => 'Ubah data user yang sudah ada di sistem',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>',
        'cardTitle' => 'Form Edit User',
        'cardDescription' => 'Sesuaikan informasi user',
        'action' => route('users.update', $user),
        'method' => 'PUT',
        'submitText' => 'Update'
    ])
        <x-form-input
            name="name"
            label="Name"
            type="text"
            :value="old('name', $user->name)"
            placeholder="Masukkan nama user"
        />

        <x-form-input
            name="email"
            label="Email"
            type="email"
            :value="old('email', $user->email)"
            placeholder="Masukkan email user"
        />

        <x-form-input
            name="password"
            label="Password (leave blank to keep)"
            type="password"
            placeholder="Biarkan kosong jika tidak ingin mengubah password"
        />

        <x-form-input
            name="password_confirmation"
            label="Confirm Password"
            type="password"
            placeholder="Konfirmasi password baru"
        />

        <div class="mb-4">
            <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Role
            </label>
            <select name="role" id="role" class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm bg-gray-800 text-white">
                <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="petugas" {{ old('role', $user->role) == 'petugas' ? 'selected' : '' }}>Petugas</option>
            </select>
            @error('role')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>
    @endcomponent
@endsection

