@extends('layouts.app')

@section('title', 'Parkir Masuk')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Kendaraan Parkir Aktif</h2>
        <a href="{{ route('transaksi.create-check-in') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Tambah Parkir
        </a>
    </div>

    @if($message = Session::get('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ $message }}
        </div>
    @endif

    @if($message = Session::get('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 mx-4 sm:mx-6 lg:mx-8">
            {{ $message }}
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        @if($transaksis->count())
        <table class="w-full table-auto divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No Transaksi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plat Nomor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Area</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Masuk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durasi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Operator</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($transaksis as $transaksi)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-bold text-blue-600">
                        #{{ str_pad($transaksi->id_parkir, 8, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-gray-800">
                        {{ $transaksi->kendaraan->plat_nomor ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $transaksi->kendaraan->jenis_kendaraan ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $transaksi->area->nama_area ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $transaksi->waktu_masuk->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @php
                            $durasi = now()->diffInMinutes($transaksi->waktu_masuk);
                            $jam = intdiv($durasi, 60);
                            $menit = $durasi % 60;
                        @endphp
                        <span class="font-semibold">{{ $jam }}j {{ $menit }}m</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $transaksi->user->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $transaksi->catatan ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm space-x-2">
                        <form action="{{ route('transaksi.checkOut', $transaksi->id_parkir) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs"
                                    onclick="return confirm('Catat kendaraan keluar?')">
                                Keluar
                            </button>
                        </form>
                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('transaksi.print', $transaksi->id_parkir) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs inline-block">
                            Struk
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $transaksis->links() }}
        </div>
        @else
        <div class="px-6 py-8 text-center text-gray-500">
            <p class="text-lg">Tidak ada kendaraan yang sedang parkir</p>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-2xl p-5 border border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full">+12%</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Total User</p>
                        <p class="text-2xl font-bold text-gray-900">0</p>
                        <p class="text-xs text-gray-400 mt-2">Admin: 0 • Petugas: 0</p>
                    </div>

                    <div class="bg-white rounded-2xl p-5 border border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Aktif</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Kendaraan</p>
                        <p class="text-2xl font-bold text-gray-900">0</p>
                        <p class="text-xs text-gray-400 mt-2">Motor: 0 • Mobil: 0</p>
                    </div>

                    <div class="bg-white rounded-2xl p-5 border border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-2 py-1 rounded-full">0% Full</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Area Parkir</p>
                        <p class="text-2xl font-bold text-gray-900">0</p>
                        <p class="text-xs text-gray-400 mt-2">Kapasitas: 0 • Terisi: 0</p>
                    </div>

                    <div class="bg-white rounded-2xl p-5 border border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-yellow-50 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full">Today</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Pendapatan</p>
                        <p class="text-2xl font-bold text-gray-900">Rp 0</p>
                        <p class="text-xs text-gray-400 mt-2">0 transaksi</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-900">Kendaraan Parkir</h2>
                            <button class="text-sm font-semibold text-green-600">Lihat Semua</button>
                        </div>
                        <div class="p-6 text-center py-16">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                </svg>
                            </div>
                            <p class="text-gray-900 font-semibold mb-1">Tidak Ada Kendaraan</p>
                            <p class="text-sm text-gray-500">Belum ada kendaraan parkir</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-900">Quick Actions</h2>
                        </div>
                        <div class="p-6 space-y-3">
                            <button class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-xl">Tambah User</button>
                            <button class="w-full bg-white hover:bg-gray-50 text-gray-700 font-semibold py-3 px-4 rounded-xl border border-gray-200">Daftar Kendaraan</button>
                            <button class="w-full bg-white hover:bg-gray-50 text-gray-700 font-semibold py-3 px-4 rounded-xl border border-gray-200">Kelola Area</button>
                            <button class="w-full bg-white hover:bg-gray-50 text-gray-700 font-semibold py-3 px-4 rounded-xl border border-gray-200">Atur Tarif</button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                    <div class="bg-white rounded-2xl border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-900">Log Aktivitas</h2>
                            <button class="text-sm font-semibold text-green-600">Lihat Semua</button>
                        </div>
                        <div class="p-6 text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-900 font-semibold mb-1">Belum Ada Aktivitas</p>
                            <p class="text-sm text-gray-500">Log aktivitas kosong</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-900">Tarif Parkir</h2>
                            <button class="text-sm font-semibold text-green-600">Edit</button>
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div>
                                    <p class="font-semibold text-gray-900">Motor</p>
                                    <p class="text-xs text-gray-500">Per jam</p>
                                </div>
                                <p class="text-lg font-bold text-gray-900">-</p>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div>
                                    <p class="font-semibold text-gray-900">Mobil</p>
                                    <p class="text-xs text-gray-500">Per jam</p>
                                </div>
                                <p class="text-lg font-bold text-gray-900">-</p>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div>
                                    <p class="font-semibold text-gray-900">Lainnya</p>
                                    <p class="text-xs text-gray-500">Per jam</p>
                                </div>
                                <p class="text-lg font-bold text-gray-900">-</p>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
@endsection
