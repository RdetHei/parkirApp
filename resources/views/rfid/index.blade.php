@extends('layouts.app')

@section('title', 'Manajemen RFID')

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
                <h1 class="text-4xl font-bold tracking-tight text-white">Manajemen Kartu RFID</h1>
                <p class="text-slate-400 text-sm mt-2">Daftarkan, perbarui, atau hapus identitas kartu RFID user.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-8 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-500 text-sm font-bold flex items-center gap-3 animate-fade-in">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        {{--
            LAYOUT VARIANT 1: "Inline Expand"
            Tabel full-width. Ketika user dipilih, baris tersebut expand ke bawah
            menampilkan form register langsung di dalam tabel — tidak ada panel terpisah.
        --}}
        <div class="card-pro !p-0 overflow-hidden">
            <div class="p-6 border-b border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h3 class="text-sm font-bold text-white uppercase tracking-widest flex items-center gap-3">
                    <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span>
                    Daftar User <span class="text-slate-500 ml-2 font-medium">({{ $users->total() }} total)</span>
                </h3>
                
                <form action="{{ route('admin.rfid.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <div class="relative min-w-[240px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, UID..." 
                               class="block w-full pl-10 pr-3 py-2 bg-slate-900/50 border border-white/10 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all">
                    </div>
                    
                    <select name="status" onchange="this.form.submit()" 
                            class="bg-slate-900/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all min-w-[120px]">
                        <option value="">Semua Status</option>
                        <option value="linked" {{ request('status') == 'linked' ? 'selected' : '' }}>Sudah Ada RFID</option>
                        <option value="unlinked" {{ request('status') == 'unlinked' ? 'selected' : '' }}>Belum Ada RFID</option>
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
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">User</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">RFID Identity</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        {{-- User row --}}
                        <tr class="border-t border-white/5 hover:bg-white/[0.01] transition-colors group" id="row-{{ $user->id }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <x-user-avatar :user="$user" size="md" class="!bg-gradient-to-br !from-blue-500/20 !to-indigo-500/20 !text-blue-400 !border-white/10" />
                                    <div>
                                        <p class="text-sm font-bold text-white">{{ $user->name }}</p>
                                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->rfid_uid)
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/20 rounded-lg">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span class="text-xs font-mono text-emerald-500">{{ $user->rfid_uid }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-600 italic font-medium">Belum terdaftar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button"
                                            onclick="toggleExpand('{{ $user->id }}', '{{ $user->name }}', '{{ $user->rfid_uid }}')"
                                            class="p-2 bg-blue-500/10 text-blue-500 rounded-lg hover:bg-blue-500 hover:text-white transition-all border border-blue-500/20"
                                            id="toggle-{{ $user->id }}"
                                            title="Register RFID">
                                        <svg class="w-4 h-4 transition-transform duration-200" id="icon-{{ $user->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </button>
                                    @if($user->rfid_uid)
                                    <form action="{{ route('admin.rfid.unlink', $user->id) }}" method="POST" onsubmit="return confirm('Hapus identitas kartu RFID dari user ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 bg-rose-500/10 text-rose-500 rounded-lg hover:bg-rose-500 hover:text-white transition-all border border-rose-500/20">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Inline expand form row (hidden by default) --}}
                        <tr id="expand-{{ $user->id }}" class="hidden border-t border-blue-500/20 bg-blue-500/5">
                            <td colspan="3" class="px-6 py-5">
                                <form action="{{ route('admin.rfid.store') }}" method="POST" class="flex items-center gap-4">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">

                                    <div class="flex items-center gap-3 mr-2">
                                        <x-user-avatar :user="$user" size="sm" round="lg" class="!bg-gradient-to-br !from-blue-500/20 !to-indigo-500/20 !text-blue-400 !border-blue-500/20" />
                                        <span class="text-sm font-bold text-white">{{ $user->name }}</span>
                                    </div>

                                    <div class="flex-1">
                                        <input type="text" name="rfid_uid" placeholder="Tap kartu RFID sekarang..." autofocus
                                               class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder:text-slate-600">
                                    </div>

                                    <button type="submit"
                                            class="px-5 py-2.5 bg-blue-500 text-slate-950 font-bold rounded-xl shadow-xl shadow-blue-500/20 transition-all hover:scale-[1.02] active:scale-95 text-[10px] uppercase tracking-widest whitespace-nowrap">
                                        Simpan
                                    </button>

                                    <button type="button" onclick="toggleExpand('{{ $user->id }}')"
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
                {{ $users->links() }}
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    let activeId = null;

    function toggleExpand(id, name, rfid) {
        const expandRow = document.getElementById('expand-' + id);
        const icon = document.getElementById('icon-' + id);
        const isOpen = !expandRow.classList.contains('hidden');

        // Close all
        document.querySelectorAll('[id^="expand-"]').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('[id^="icon-"]').forEach(el => el.style.transform = '');

        if (!isOpen) {
            expandRow.classList.remove('hidden');
            icon.style.transform = 'rotate(45deg)';
            expandRow.querySelector('input[name="rfid_uid"]').focus();
            activeId = id;
        } else {
            activeId = null;
        }
    }
</script>
@endpush
@endsection
