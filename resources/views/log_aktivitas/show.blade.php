@extends('layouts.app')

@section('title','Detail Log Aktivitas')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('log-aktivitas.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Detail Log Aktivitas</h1>
        </div>
        <p class="text-sm text-gray-500 ml-9">Informasi lengkap log aktivitas #{{ $item->id_log }}</p>
    </div>

    <!-- Main Card -->
    <div class="max-w-4xl">
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <!-- Card Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-green-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Log #{{ $item->id_log }}</h2>
                            <p class="text-sm text-gray-600">{{ $item->aktivitas }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-6 space-y-4">
                <!-- ID Log -->
                <div class="flex items-start py-3 border-b border-gray-100">
                    <div class="w-1/3">
                        <p class="text-sm font-semibold text-gray-600">ID Log</p>
                    </div>
                    <div class="w-2/3">
                        <p class="text-sm font-bold text-gray-900">#{{ $item->id_log }}</p>
                    </div>
                </div>

                <!-- User -->
                <div class="flex items-start py-3 border-b border-gray-100">
                    <div class="w-1/3">
                        <p class="text-sm font-semibold text-gray-600">User</p>
                    </div>
                    <div class="w-2/3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $item->user?->name ?? 'N/A' }}</p>
                                @if($item->user?->role)
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                        {{ ucfirst($item->user->role) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aktivitas -->
                <div class="flex items-start py-3 border-b border-gray-100">
                    <div class="w-1/3">
                        <p class="text-sm font-semibold text-gray-600">Aktivitas</p>
                    </div>
                    <div class="w-2/3">
                        <p class="text-sm text-gray-900">{{ $item->aktivitas }}</p>
                    </div>
                </div>

                <!-- Waktu Aktivitas -->
                <div class="flex items-start py-3 border-b border-gray-100">
                    <div class="w-1/3">
                        <p class="text-sm font-semibold text-gray-600">Waktu Aktivitas</p>
                    </div>
                    <div class="w-2/3">
                        @if($item->waktu_aktivitas)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $item->waktu_aktivitas->format('d F Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->waktu_aktivitas->format('H:i:s') }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-400">-</p>
                        @endif
                    </div>
                </div>

                <!-- Dibuat -->
                <div class="flex items-start py-3 border-b border-gray-100">
                    <div class="w-1/3">
                        <p class="text-sm font-semibold text-gray-600">Dibuat</p>
                    </div>
                    <div class="w-2/3">
                        @if($item->created_at)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $item->created_at->format('d F Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->created_at->format('H:i:s') }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-400">-</p>
                        @endif
                    </div>
                </div>

                <!-- Diupdate -->
                <div class="flex items-start py-3">
                    <div class="w-1/3">
                        <p class="text-sm font-semibold text-gray-600">Terakhir Diupdate</p>
                    </div>
                    <div class="w-2/3">
                        @if($item->updated_at)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $item->updated_at->format('d F Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->updated_at->format('H:i:s') }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-400">-</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
                <a href="{{ route('log-aktivitas.index') }}" class="inline-flex items-center px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl border border-gray-200 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
                <a href="{{ route('log-aktivitas.edit', $item->id_log) }}" class="inline-flex items-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
