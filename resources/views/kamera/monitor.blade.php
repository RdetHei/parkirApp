@extends('layouts.app')

@section('title', 'Camera Monitoring')

@section('content')
<div class="p-8 relative z-10">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                    Parking Ops
                </span>
            </div>
            <h1 class="text-4xl font-bold tracking-tight text-white">Multi-Camera <span class="text-emerald-500">Monitoring</span></h1>
            <p class="text-slate-400 text-sm mt-2">Pantau seluruh kamera scanner dan viewer dalam satu dashboard real-time.</p>
        </div>
        <div class="flex items-center gap-3">
            @if(!empty($activeArea))
                <span class="px-3 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-[10px] font-black uppercase tracking-widest text-emerald-400">
                    Area Aktif: {{ $activeArea->map_code ?? $activeArea->nama_area }}
                </span>
            @endif
            <span class="px-3 py-2 rounded-xl bg-slate-900 border border-white/10 text-[10px] font-black uppercase tracking-widest text-slate-300">
                Total Kamera: {{ $cameras->count() }}
            </span>
            @if((auth()->user()->role ?? null) === 'admin')
                <a href="{{ route('kamera.index') }}" class="px-4 py-2 rounded-xl border border-white/10 bg-white/5 text-xs font-black uppercase tracking-widest text-slate-300 hover:text-white hover:bg-white/10 transition-all">
                    Kelola Kamera
                </a>
            @endif
        </div>
    </div>

    @if(!empty($needsOperationalArea))
        <div class="card-pro !p-12 border-amber-500/20 bg-amber-500/5">
            <h4 class="text-sm font-black text-amber-400 uppercase tracking-widest mb-3">Area Operasional Belum Aktif</h4>
            <p class="text-sm text-slate-300 leading-relaxed">
                Untuk menampilkan kamera sesuai area, petugas harus mengaktifkan area terlebih dahulu dengan memasukkan
                <strong class="text-white">kode peta</strong> di dashboard petugas.
            </p>
            <div class="mt-6">
                <a href="{{ route('petugas.dashboard') }}" class="inline-flex px-4 py-2 rounded-xl border border-white/10 bg-white/5 text-xs font-black uppercase tracking-widest text-slate-300 hover:text-white hover:bg-white/10 transition-all">
                    Buka Dashboard Petugas
                </a>
            </div>
        </div>
    @elseif($cameras->isEmpty())
        <div class="card-pro !p-16 text-center border-dashed border-white/10">
            <div class="w-20 h-20 bg-slate-950 rounded-3xl flex items-center justify-center mx-auto mb-8 text-slate-800 border border-white/5">
                <i class="fa-solid fa-camera text-3xl"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-500 uppercase tracking-widest">Belum Ada Kamera</h4>
            <p class="text-xs text-slate-700 mt-2">
                @if(!empty($activeArea))
                    Belum ada kamera yang ditugaskan ke area ini.
                @else
                    Tambahkan perangkat kamera di menu Kamera untuk mulai monitoring.
                @endif
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($cameras as $cam)
                <div class="card-pro !p-0 overflow-hidden border-white/5">
                    <div class="px-5 py-4 border-b border-white/5 bg-white/[0.02] flex items-center justify-between">
                        <div class="min-w-0">
                            <h3 class="text-sm font-black text-white truncate">{{ $cam->nama }}</h3>
                            <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-1">
                                {{ $cam->tipe === \App\Models\Camera::TIPE_SCANNER ? 'Scanner Feed' : 'Viewer Feed' }}
                                @if($cam->is_default)
                                    • Default Source
                                @endif
                            </p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg border border-emerald-500/20 bg-emerald-500/10 text-[9px] font-black uppercase tracking-widest text-emerald-400">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                            Live
                        </span>
                    </div>

                    <div class="relative bg-slate-950 aspect-video">
                        <img
                            src="{{ $cam->url }}"
                            alt="Stream {{ $cam->nama }}"
                            loading="lazy"
                            class="w-full h-full object-cover"
                            onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
                        >
                        <div class="hidden absolute inset-0 flex flex-col items-center justify-center text-slate-500 bg-slate-950">
                            <i class="fa-solid fa-triangle-exclamation text-xl mb-3 text-amber-400"></i>
                            <p class="text-[10px] font-black uppercase tracking-widest">Stream Tidak Tersedia</p>
                            <p class="text-[9px] mt-1 px-4 text-center text-slate-600">Pastikan URL kamera aktif dan dapat diakses browser.</p>
                        </div>
                    </div>

                    <div class="px-5 py-4 border-t border-white/5 bg-white/[0.01]">
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-2">Stream URL</p>
                        <code class="block text-[10px] text-slate-400 break-all">{{ $cam->url }}</code>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
