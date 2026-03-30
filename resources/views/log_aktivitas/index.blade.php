@extends('layouts.app')

@section('title','Log Aktivitas')

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

    <!-- Filter Card -->
    <div class="card-pro mb-8 border-white/5 relative overflow-hidden group">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl group-hover:bg-emerald-500/10 transition-all duration-700"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-emerald-500/10 rounded-lg border border-emerald-500/20">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-white uppercase tracking-widest">Filter Audit Logs</h3>
            </div>

            <form action="{{ route('log-aktivitas.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
                    <!-- User Filter -->
                    <div class="space-y-2">
                        <label for="id_user" class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">User</label>
                        <select name="id_user" id="id_user" class="block w-full bg-slate-900/50 border border-white/10 rounded-xl text-xs font-semibold text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all px-4 py-2.5">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('id_user') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Type Filter -->
                    <div class="space-y-2">
                        <label for="tipe_aktivitas" class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">Category</label>
                        <select name="tipe_aktivitas" id="tipe_aktivitas" class="block w-full bg-slate-900/50 border border-white/10 rounded-xl text-xs font-semibold text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all px-4 py-2.5">
                            <option value="">All Categories</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ request('tipe_aktivitas') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date From -->
                    <div class="space-y-2">
                        <label for="tanggal_dari" class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">Date From</label>
                        <input type="date" name="tanggal_dari" id="tanggal_dari" value="{{ request('tanggal_dari') }}"
                               class="block w-full bg-slate-900/50 border border-white/10 rounded-xl text-xs font-semibold text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all px-4 py-2">
                    </div>

                    <!-- Date To -->
                    <div class="space-y-2">
                        <label for="tanggal_sampai" class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">Date To</label>
                        <input type="date" name="tanggal_sampai" id="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                               class="block w-full bg-slate-900/50 border border-white/10 rounded-xl text-xs font-semibold text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all px-4 py-2">
                    </div>

                    <!-- Search -->
                    <div class="space-y-2">
                        <label for="q" class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">Search Keywords</label>
                        <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Action..."
                               class="block w-full bg-slate-900/50 border border-white/10 rounded-xl text-xs font-semibold text-white placeholder:text-slate-600 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all px-4 py-2.5">
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-white/5">
                    <a href="{{ route('log-aktivitas.index') }}"
                       class="btn-pro-outline !py-2 !px-6 !text-[11px] uppercase tracking-widest">
                        Reset
                    </a>
                    <button type="submit"
                            class="btn-pro-primary !py-2 !px-6 !text-[11px] uppercase tracking-widest">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Card Container -->
    <div class="card-pro !p-0 border-white/5 overflow-hidden shadow-2xl">
        <!-- Card Header -->
        <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-white tracking-tight">Audit Trail</h2>
                <p class="text-xs text-slate-500 mt-1">Detailed history of all system activities.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 bg-white/5 border border-white/10 rounded-full text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    {{ $items->total() }} Records found
                </span>
            </div>
        </div>

        <!-- Table -->
        @if($items->count())
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/[0.01] text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        <th class="px-8 py-4">Ref ID</th>
                        <th class="px-8 py-4">User</th>
                        <th class="px-8 py-4">Category</th>
                        <th class="px-8 py-4">Activity Description</th>
                        <th class="px-8 py-4">Network Info</th>
                        <th class="px-8 py-4">Timestamp</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                        @foreach($items as $item)
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="px-8 py-5 text-sm whitespace-nowrap">
                                    <span class="text-xs font-bold text-slate-500">#{{ $item->id_log }}</span>
                                </td>
                                <td class="px-8 py-5 text-sm whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500 font-bold text-[10px] group-hover:bg-emerald-500 group-hover:text-slate-950 transition-all">
                                            {{ substr($item->user?->name ?? 'S', 0, 1) }}
                                        </div>
                                        <span class="text-xs font-bold text-white">{{ $item->user?->name ?? 'System' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-sm whitespace-nowrap">
                                    @php
                                        $typeColors = [
                                            'transaksi' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                                            'slot' => 'bg-purple-500/10 text-purple-500 border-purple-500/20',
                                            'auth' => 'bg-orange-500/10 text-orange-500 border-orange-500/20',
                                            'config' => 'bg-pink-500/10 text-pink-500 border-pink-500/20',
                                        ];
                                        $color = $typeColors[$item->tipe_aktivitas] ?? 'bg-slate-800 text-slate-400 border-white/5';
                                    @endphp
                                    <span class="px-2.5 py-1 text-[9px] font-black uppercase rounded-lg border {{ $color }} tracking-widest">
                                        {{ $item->tipe_aktivitas ?? 'General' }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-sm">
                                    <span class="text-xs font-medium text-slate-300 line-clamp-2 leading-relaxed" title="{{ $item->aktivitas }}">{{ $item->aktivitas }}</span>
                                </td>
                                <td class="px-8 py-5 text-sm whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-slate-700"></div>
                                        <code class="text-[10px] font-bold text-slate-500">{{ $item->ip_address ?? 'Local' }}</code>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-sm whitespace-nowrap">
                                    @if($item->waktu_aktivitas)
                                        <div class="text-xs font-bold text-white">{{ $item->waktu_aktivitas->format('d M Y') }}</div>
                                        <div class="text-[10px] text-slate-500 mt-0.5 font-semibold">{{ $item->waktu_aktivitas->format('H:i:s') }}</div>
                                    @else
                                        <span class="text-xs text-slate-600">-</span>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-sm whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- View Button -->
                                        <a href="{{ route('log-aktivitas.show', $item->id_log) }}"
                                           class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-slate-950 hover:border-emerald-500 transition-all group/btn"
                                           title="View Details">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        @if(auth()->user()->role === 'admin')
                                        <!-- Delete Button (Only Admin) -->
                                        <form action="{{ route('log-aktivitas.destroy', $item->id_log) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus log ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-rose-500 hover:text-white hover:border-rose-500 transition-all"
                                                    title="Delete Log">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-white/5">
            {{ $items->links() }}
        </div>
        @else
        <div class="px-6 py-8 text-center text-gray-500">
            <p class="text-lg">Tidak ada log aktivitas</p>
        </div>
        @endif
    </div>
</div>
@endsection
