@extends('layouts.app')

@section('title', 'Manajemen RFID Kendaraan')

@section('content')
<div class="p-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-blue-500/10 text-blue-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-blue-500/20">
                        Admin Only
                    </span>
                </div>
                <h1 class="text-4xl font-bold tracking-tight text-white">Manajemen RFID Kendaraan</h1>
                <p class="text-slate-400 text-sm mt-2">Hubungkan UID RFID ke kendaraan terdaftar untuk akses parkir otomatis.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-8 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-500 text-sm font-bold flex items-center gap-3 animate-fade-in">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="card-pro !p-0 overflow-hidden">
            <div class="p-6 border-b border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h3 class="text-sm font-bold text-white uppercase tracking-widest flex items-center gap-3">
                    <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span>
                    Daftar Kendaraan <span class="text-slate-500 ml-2 font-medium">({{ $vehicles->total() }} total)</span>
                </h3>
                
                <form action="{{ route('admin.rfid.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <div class="relative min-w-[240px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari plat, pemilik, UID..." 
                               class="block w-full pl-10 pr-3 py-2 bg-slate-900/50 border border-white/10 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all">
                    </div>
                    
                    <select name="status" onchange="this.form.submit()"
                            class="bg-slate-900/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all min-w-[120px]">
                        <option value="" class="bg-slate-900">Semua Status</option>
                        <option value="linked" class="bg-slate-900" {{ request('status') == 'linked' ? 'selected' : '' }}>Sudah Ada RFID</option>
                        <option value="unlinked" class="bg-slate-900" {{ request('status') == 'unlinked' ? 'selected' : '' }}>Belum Ada RFID</option>
                    </select>

                    @if(request()->anyFilled(['q', 'status']))
                        <a href="{{ route('admin.rfid.index') }}" class="p-2 text-slate-500 hover:text-white transition-colors" title="Clear Filters">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/[0.02]">
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Kendaraan</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Pemilik</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">RFID Identity</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicles as $vehicle)
                        <tr class="border-t border-white/5 hover:bg-white/[0.01] transition-colors group" id="row-{{ $vehicle->id_kendaraan }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-800 border border-white/10 flex flex-col items-center justify-center font-black text-white">
                                        <span class="text-[6px] text-slate-500 leading-none mb-0.5 uppercase tracking-tighter">{{ substr($vehicle->plat_nomor, 0, 2) }}</span>
                                        <span class="text-sm leading-none tracking-tight">{{ substr($vehicle->plat_nomor, 2, 4) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-white">{{ $vehicle->plat_nomor }}</p>
                                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">{{ $vehicle->jenis_kendaraan }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <p class="text-xs font-bold text-slate-300">{{ $vehicle->user->name ?? 'Guest' }}</p>
                                        <p class="text-[9px] text-slate-500 uppercase tracking-widest">{{ $vehicle->user->email ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($vehicle->rfidTag)
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/20 rounded-lg">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span class="text-xs font-mono text-emerald-500">{{ $vehicle->rfidTag->uid }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-600 italic font-medium">Belum terdaftar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <div class="flex items-center bg-slate-900 border border-white/5 rounded-xl p-1 gap-1">
                                        <button type="button"
                                                onclick="toggleExpand('{{ $vehicle->id_kendaraan }}')"
                                                class="w-9 h-9 flex items-center justify-center text-blue-500 hover:text-white hover:bg-blue-500 rounded-lg transition-all"
                                                id="toggle-{{ $vehicle->id_kendaraan }}"
                                                title="Hubungkan RFID">
                                            <i class="fa-solid fa-link text-xs transition-transform duration-200" id="icon-{{ $vehicle->id_kendaraan }}"></i>
                                        </button>
                                        
                                        @if($vehicle->rfidTag)
                                        <form action="{{ route('admin.rfid.unlink', $vehicle->id_kendaraan) }}" method="POST" class="inline" onsubmit="return confirm('Hapus hubungan RFID dari kendaraan ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-9 h-9 flex items-center justify-center text-rose-500 hover:text-white hover:bg-rose-500 rounded-lg transition-all">
                                                <i class="fa-solid fa-link-slash text-xs"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>

                        {{-- Inline expand form row --}}
                        <tr id="expand-{{ $vehicle->id_kendaraan }}" class="hidden border-t border-blue-500/20 bg-blue-500/5">
                            <td colspan="4" class="px-6 py-5">
                                <form action="{{ route('admin.rfid.store') }}" method="POST" class="flex items-center gap-4">
                                    @csrf
                                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id_kendaraan }}">

                                    <div class="flex items-center gap-3 mr-2">
                                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-500">
                                            <i class="fa-solid fa-car text-xs"></i>
                                        </div>
                                        <span class="text-sm font-bold text-white">{{ $vehicle->plat_nomor }}</span>
                                    </div>

                                    <div class="flex-1">
                                        <input type="text" name="rfid_uid" placeholder="Tap kartu RFID atau ketik UID..." autofocus
                                               class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder:text-slate-600">
                                    </div>

                                    <button type="submit"
                                            class="px-5 py-2.5 bg-blue-500 text-slate-950 font-bold rounded-xl shadow-xl shadow-blue-500/20 transition-all hover:scale-[1.02] active:scale-95 text-[10px] uppercase tracking-widest whitespace-nowrap">
                                        Hubungkan
                                    </button>

                                    <button type="button" onclick="toggleExpand('{{ $vehicle->id_kendaraan }}')"
                                            class="p-2 text-slate-500 hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-white/5">
                {{ $vehicles->links() }}
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    function toggleExpand(id) {
        const expandRow = document.getElementById('expand-' + id);
        const icon = document.getElementById('icon-' + id);
        const isOpen = !expandRow.classList.contains('hidden');

        // Close all
        document.querySelectorAll('[id^="expand-"]').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('[id^="icon-"]').forEach(el => {
            el.style.transform = '';
            el.classList.replace('fa-xmark', 'fa-link');
        });

        if (!isOpen) {
            expandRow.classList.remove('hidden');
            icon.classList.replace('fa-link', 'fa-xmark');
            expandRow.querySelector('input[name="rfid_uid"]').focus();
        }
    }
</script>
@endpush
@endsection
