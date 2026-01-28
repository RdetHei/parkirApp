@extends('layouts.app')

@section('title', 'Create User')

@section('content')
@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md mt-10">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Create User</h2>

    <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
        @csrf

        <x-form-input
            name="name"
            label="Name"
            type="text"
            :value="old('name')"
            placeholder="Masukkan nama user"
        />

        <x-form-input
            name="email"
            label="Email"
            type="email"
            :value="old('email')"
            placeholder="Masukkan email user"
        />

        <x-form-input
            name="password"
            label="Password"
            type="password"
            placeholder="Masukkan password"
        />

        <x-form-input
            name="password_confirmation"
            label="Confirm Password"
            type="password"
            placeholder="Konfirmasi password"
        />

        <div class="mb-4">
            <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Role
            </label>
            <select name="role" id="role" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="petugas" {{ old('role') == 'petugas' ? 'selected' : '' }}>Petugas</option>
            </select>
            @error('role')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('users.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-transparent rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Create
            </button>
        </div>
    </form>
</div>
@endsection
