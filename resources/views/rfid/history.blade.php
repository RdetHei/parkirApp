@extends('layouts.app')

@section('title', 'Riwayat Scan RFID')

@section('content')
<div class="p-6 sm:p-8 relative z-10 animate-fade-in">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight">Riwayat <span class="text-emerald-500">Scan RFID</span></h1>
                <p class="text-slate-400 text-sm mt-1">Log semua event scan terminal terbaru.</p>
            </div>
            <a href="{{ route('parkir.scan') }}" class="px-4 py-2 rounded-xl border border-white/10 bg-white/5 text-xs font-black uppercase tracking-widest text-slate-300 hover:text-white hover:bg-white/10 transition-all">
                Kembali Terminal
            </a>
        </div>

        <div class="card-pro p-0! overflow-hidden border-white/5">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-white/2 border-b border-white/5">
                        <tr>
                            <th class="px-5 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500">Waktu</th>
                            <th class="px-5 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500">User</th>
                            <th class="px-5 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500">Tipe</th>
                            <th class="px-5 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($items as $row)
                            <tr class="hover:bg-white/2">
                                <td class="px-5 py-3 text-xs text-slate-300 font-semibold">{{ optional($row->created_at)->format('d/m/Y H:i:s') }}</td>
                                <td class="px-5 py-3 text-xs text-white font-bold">{{ $row->user?->name ?? '-' }}</td>
                                <td class="px-5 py-3">
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest border
                                        {{ $row->type === 'IN' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : ($row->type === 'OUT' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' : 'bg-amber-500/10 text-amber-400 border-amber-500/20') }}">
                                        {{ $row->type }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-xs text-slate-300 font-semibold">
                                    {{ is_null($row->amount) ? '-' : 'Rp ' . number_format((float) $row->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center text-xs font-black uppercase tracking-widest text-slate-600">Belum ada riwayat scan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-white/5">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
