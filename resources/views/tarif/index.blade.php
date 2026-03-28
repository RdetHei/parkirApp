@extends('layouts.app')

@section('title','Tariff Rules')

@section('content')
<div class="p-8 relative z-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                    Financial Policy
                </span>
            </div>
            <h1 class="text-4xl font-bold tracking-tight text-white">Tariff <span class="text-emerald-500">Structure</span></h1>
            <p class="text-slate-400 text-sm mt-2">Define hourly rates based on vehicle classification.</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('tarif.create') }}" class="group relative px-6 py-3 bg-emerald-500 text-slate-950 font-bold text-xs uppercase tracking-widest rounded-xl transition-all hover:bg-emerald-400 hover:shadow-[0_0_20px_rgba(16,185,129,0.4)] flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add New Rule
            </a>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="mb-8 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-4 flex items-center gap-4 animate-fade-in">
            <div class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center text-emerald-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <p class="text-sm font-bold text-emerald-500 uppercase tracking-widest">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Main Data Table -->
    <div class="card-pro !p-0 overflow-hidden shadow-2xl">
        <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
            <h2 class="text-sm font-bold text-white uppercase tracking-widest">Active Policies <span class="text-slate-500 ml-2 font-medium">({{ $items->total() }} total)</span></h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/[0.01] text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        <th class="px-8 py-4">ID</th>
                        <th class="px-8 py-4">Classification</th>
                        <th class="px-8 py-4">Hourly Rate</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($items as $item)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-8 py-5">
                                <span class="text-[10px] font-mono font-bold text-emerald-500/80">#{{ str_pad($item->id_tarif, 3, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    @php
                                        $vehicleIcons = [
                                            'motor' => ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-500'],
                                            'mobil' => ['icon' => 'M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2', 'bg' => 'bg-indigo-500/10', 'text' => 'text-indigo-500'],
                                            'lainnya' => ['icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z', 'bg' => 'bg-slate-800', 'text' => 'text-slate-400'],
                                        ];
                                        $vehicle = $vehicleIcons[$item->jenis_kendaraan] ?? $vehicleIcons['lainnya'];
                                    @endphp
                                    <div class="w-10 h-10 {{ $vehicle['bg'] }} rounded-xl flex items-center justify-center border border-white/5 group-hover:border-emerald-500/30 transition-colors">
                                        <svg class="w-5 h-5 {{ $vehicle['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $vehicle['icon'] }}"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-white tracking-tight">{{ ucfirst($item->jenis_kendaraan) }}</p>
                                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">{{ $item->jenis_kendaraan == 'motor' ? 'Two-Wheeled' : ($item->jenis_kendaraan == 'mobil' ? 'Passenger Car' : 'Special Vehicle') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="inline-flex flex-col">
                                    <span class="text-lg font-bold text-emerald-500">Rp {{ number_format($item->tarif_perjam, 0, ',', '.') }}</span>
                                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Base rate per hour</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right space-x-2">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('tarif.edit', $item) }}"
                                       class="p-2 bg-amber-500/10 hover:bg-amber-500 text-amber-500 hover:text-slate-950 rounded-lg border border-amber-500/20 transition-all"
                                       title="Edit Tariff">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <form action="{{ route('tarif.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Remove this tariff rule? This will affect future calculations.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white rounded-lg border border-rose-500/20 transition-all"
                                                title="Delete Rule">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-24 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-slate-900 border border-white/5 rounded-[2rem] flex items-center justify-center text-slate-700 mb-6">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-white mb-2">No tariffs defined</h3>
                                    <p class="text-slate-500 text-sm max-w-xs mx-auto">Set up your pricing rules to start generating revenue from parking.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
        <div class="px-8 py-6 border-t border-white/5 bg-white/[0.01]">
            {{ $items->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

