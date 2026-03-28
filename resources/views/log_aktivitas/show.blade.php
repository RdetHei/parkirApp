@extends('layouts.app')

@section('title','Detail Log Aktivitas')

@section('content')
<div class="p-8 relative z-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <a href="{{ route('log-aktivitas.index') }}" class="group flex items-center gap-2 px-3 py-1 bg-white/5 text-slate-400 text-[10px] font-bold uppercase tracking-widest rounded-full border border-white/10 hover:bg-emerald-500 hover:text-slate-950 hover:border-emerald-500 transition-all">
                    <svg class="w-3 h-3 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Logs
                </a>
            </div>
            <h1 class="text-4xl font-bold tracking-tight text-white">Log <span class="text-emerald-500">Details</span></h1>
            <p class="text-slate-400 text-sm mt-2">Technical breakdown of system event #{{ $item->id_log }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Detail Card -->
        <div class="lg:col-span-2 space-y-8">
            <div class="card-pro relative overflow-hidden group border-white/5">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-8 pb-6 border-b border-white/5">
                        <div class="p-2 bg-emerald-500/10 rounded-lg border border-emerald-500/20">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-white uppercase tracking-widest">Event Description</h3>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Activity Message</p>
                            <p class="text-xl font-bold text-white leading-relaxed">{{ $item->aktivitas }}</p>
                        </div>

                        @if($item->metadata)
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-4">Event Metadata (JSON)</p>
                            <div class="bg-slate-950/50 rounded-2xl border border-white/5 p-6 font-mono text-xs overflow-x-auto">
                                <pre class="text-emerald-500/80 leading-relaxed"><code>{{ json_encode($item->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Network Info Card -->
            <div class="card-pro border-white/5">
                <div class="flex items-center gap-3 mb-8 pb-6 border-b border-white/5">
                    <div class="p-2 bg-blue-500/10 rounded-lg border border-blue-500/20">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-widest">Session & Network</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">IP Address</p>
                            <code class="text-sm font-bold text-white">{{ $item->ip_address ?? 'Local/Unknown' }}</code>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">User Agent</p>
                            <p class="text-xs font-medium text-slate-400 leading-relaxed">{{ $item->user_agent ?? 'System Internal' }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Source Model</p>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 bg-slate-800 border border-white/5 rounded text-[10px] font-bold text-slate-400 font-mono">
                                    {{ $item->model_type ?? 'None' }}
                                </span>
                                @if($item->model_id)
                                <span class="text-[10px] font-bold text-emerald-500">ID: {{ $item->model_id }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-8">
            <!-- Timeline Card -->
            <div class="card-pro border-white/5">
                <div class="space-y-6">
                    <div class="flex flex-col">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Timestamp</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-900 border border-white/5 flex items-center justify-center text-white font-bold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-white">{{ $item->waktu_aktivitas?->format('d M Y') }}</p>
                                <p class="text-xs font-medium text-slate-500">{{ $item->waktu_aktivitas?->format('H:i:s') }} (Server Time)</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col pt-6 border-t border-white/5">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Category</p>
                        @php
                            $typeColors = [
                                'transaksi' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                                'slot' => 'bg-purple-500/10 text-purple-500 border-purple-500/20',
                                'auth' => 'bg-orange-500/10 text-orange-500 border-orange-500/20',
                                'config' => 'bg-pink-500/10 text-pink-500 border-pink-500/20',
                            ];
                            $color = $typeColors[$item->tipe_aktivitas] ?? 'bg-slate-800 text-slate-400 border-white/5';
                        @endphp
                        <span class="w-fit px-3 py-1 text-[10px] font-black uppercase rounded-lg border {{ $color }} tracking-widest">
                            {{ $item->tipe_aktivitas ?? 'General' }}
                        </span>
                    </div>

                    <div class="flex flex-col pt-6 border-t border-white/5">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Initiated By</p>
                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/[0.02] border border-white/5">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500 font-bold text-xs">
                                {{ substr($item->user?->name ?? 'S', 0, 1) }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white">{{ $item->user?->name ?? 'System' }}</p>
                                <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">{{ $item->user?->role ?? 'Internal' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(auth()->user()->role === 'admin')
            <div class="card-pro border-rose-500/20 bg-rose-500/5">
                <p class="text-[10px] font-bold text-rose-500 uppercase tracking-widest mb-4">Danger Zone</p>
                <form action="{{ route('log-aktivitas.destroy', $item->id_log) }}" method="POST" onsubmit="return confirm('Archive this log forever?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-3 bg-rose-500 text-white font-bold text-[10px] uppercase tracking-widest rounded-xl hover:bg-rose-600 transition-all shadow-lg shadow-rose-500/20">
                        Archive Log Entry
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
