@extends('layouts.app')

@section('title','Kendaraan')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">

    <!-- Success Alert -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
                <button type="button" onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-green-600 hover:text-green-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Card Container -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Daftar Kendaraan</h2>
                <span class="text-sm text-gray-500">{{ $items->total() }} kendaraan</span>
            </div>
        </div>

        <!-- Table -->
        @if($items->count())
        <table class="w-full table-auto divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plat Nomor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Warna</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemilik</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm">
                                <span class="text-sm font-semibold text-gray-900">#{{ $item->id_kendaraan }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-3">
                                    @php
                                        $vehicleConfig = [
                                            'motor' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
                                            'mobil' => ['bg' => 'bg-green-100', 'text' => 'text-green-600'],
                                        ];
                                        $config = $vehicleConfig[strtolower($item->jenis_kendaraan)] ?? ['bg' => 'bg-purple-100', 'text' => 'text-purple-600'];
                                    @endphp
                                    <div class="w-10 h-10 {{ $config['bg'] }} rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 {{ $config['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $item->plat_nomor }}</p>
                                        <p class="text-xs text-gray-500">ID: {{ $item->id_kendaraan }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @php
                                    $jenisColors = [
                                        'motor' => 'bg-blue-100 text-blue-800',
                                        'mobil' => 'bg-green-100 text-green-800',
                                    ];
                                    $jenisColor = $jenisColors[strtolower($item->jenis_kendaraan)] ?? 'bg-purple-100 text-purple-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $jenisColor }}">
                                    {{ ucfirst($item->jenis_kendaraan) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($item->warna)
                                    <div class="flex items-center gap-2">
                                        <div class="w-4 h-4 rounded-full border-2 border-gray-300" style="background-color: {{ $item->warna }}"></div>
                                        <span class="text-sm text-gray-900">{{ ucfirst($item->warna) }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr($item->pemilik ?? 'N', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $item->pemilik ?? 'N/A' }}</p>
                                        @if($item->user)
                                            <p class="text-xs text-gray-500">User: {{ $item->user->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <div class="flex items-center gap-2">
                                    <!-- Edit Button -->
                                    <a href="{{ route('kendaraan.edit', $item) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 bg-yellow-50 hover:bg-yellow-100 text-yellow-600 rounded-lg transition-colors"
                                       title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <!-- Delete Button -->
                                    <form action="{{ route('kendaraan.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kendaraan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors"
                                                title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
        </table>

        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $items->links() }}
        </div>
        @else
        <div class="px-6 py-8 text-center text-gray-500">
            <p class="text-lg">Tidak ada kendaraan</p>
        </div>
        @endif
    </div>
</div>
@endsection
