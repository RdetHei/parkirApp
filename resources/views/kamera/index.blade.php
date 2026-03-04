@extends('layouts.app')

@section('title', 'Kamera')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manajemen Kamera</h1>
                <p class="mt-1 text-sm text-gray-500">Daftar perangkat kamera yang terhubung ke sistem parkir.</p>
            </div>
            <a href="{{ route('kamera.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-sm font-bold shadow-lg shadow-indigo-100 transition-all active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Kamera
            </a>
        </div>

        @if(session('success'))
            <div class="mb-8 bg-emerald-50 border border-emerald-100 rounded-2xl p-4 flex items-center gap-3 animate-fade-in">
                <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center text-white shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($items as $item)
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all group">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-6">
                            <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($item->is_default)
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full uppercase tracking-wider border border-emerald-200">Utama</span>
                                @endif
                                <span class="px-3 py-1 {{ $item->tipe === \App\Models\Camera::TIPE_SCANNER ? 'bg-blue-100 text-blue-700 border-blue-200' : 'bg-violet-100 text-violet-700 border-violet-200' }} text-[10px] font-bold rounded-full uppercase tracking-wider border">
                                    {{ $item->tipe }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $item->nama }}</h3>
                            <div class="flex items-center gap-2 text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                <code class="text-[10px] font-mono font-bold truncate">{{ $item->url }}</code>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-50 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('kamera.edit', $item) }}" class="w-10 h-10 bg-gray-50 text-gray-400 rounded-xl flex items-center justify-center hover:bg-amber-50 hover:text-amber-600 transition-all border border-transparent hover:border-amber-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('kamera.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kamera ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-10 h-10 bg-gray-50 text-gray-400 rounded-xl flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition-all border border-transparent hover:border-red-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Online</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 bg-white rounded-3xl border-2 border-dashed border-gray-100 flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Kamera</h3>
                    <p class="text-sm text-gray-500 max-w-xs mx-auto">Tambahkan perangkat kamera pertama Anda untuk mulai memantau area parkir.</p>
                </div>
            @endforelse
        </div>

        @if($items->hasPages())
            <div class="mt-8">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
