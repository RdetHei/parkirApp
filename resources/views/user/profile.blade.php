@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Pengaturan Profil</h1>
                <p class="mt-1 text-sm text-gray-500">Kelola informasi akun dan keamanan Anda.</p>
            </div>

            @if(session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="md:col-span-1">
                    <h2 class="text-lg font-semibold text-gray-900">Informasi Pribadi</h2>
                    <p class="mt-1 text-sm text-gray-500">Perbarui nama dan alamat email akun Anda.</p>
                </div>
                
                <div class="md:col-span-2">
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <form method="POST" action="{{ route('user.profile.update') }}" class="p-6 space-y-4">
                            @csrf
                            @method('PUT')
                            
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                                       class="block w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/70" required>
                                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                                       class="block w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/70" required>
                                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="pt-4 border-t border-gray-100 mt-6">
                                <h3 class="text-sm font-semibold text-gray-900 mb-4">Ubah Kata Sandi (Opsional)</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi Baru</label>
                                        <input type="password" name="password" id="password" 
                                               class="block w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/70"
                                               placeholder="Kosongkan jika tidak ingin mengubah">
                                        @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation" 
                                               class="block w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/70">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-6">
                                <button type="submit" class="rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
