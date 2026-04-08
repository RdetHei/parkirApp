@extends('layouts.app')

@section('title', 'Edit Tarif')

@section('content')
    @component('components.form-card', [
        'backUrl' => route('tarif.index'),
        'title' => 'Edit Tarif',
        'description' => 'Ubah data tarif parkir yang sudah ada di sistem',
        'cardIcon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        'cardTitle' => 'Form Edit Tarif',
        'cardDescription' => 'Sesuaikan informasi tarif',
        'action' => route('tarif.update', $item),
        'method' => 'PUT',
        'submitText' => 'Update'
    ])
        <div class="space-y-8">
            <div class="space-y-2">
                <label for="jenis_kendaraan" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Jenis Kendaraan <span class="text-rose-500">*</span></label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                        <i class="fa-solid fa-car-side text-sm"></i>
                    </div>
                    <input type="text" name="jenis_kendaraan" id="jenis_kendaraan" value="{{ old('jenis_kendaraan', $item->jenis_kendaraan) }}" required placeholder="Motor / Mobil / Bus / dll"
                            class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm @error('jenis_kendaraan') border-rose-500 @enderror">
                </div>
                <p class="mt-2 text-[9px] text-slate-600 italic font-bold uppercase tracking-widest ml-1">Masukkan jenis kendaraan secara manual untuk memperbarui tarif.</p>
                @error('jenis_kendaraan')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2">
                <label for="tarif_perjam" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Tarif per jam (IDR) <span class="text-rose-500">*</span></label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                        <span class="text-[10px] font-black uppercase">Rp</span>
                    </div>
                    <input type="number" name="tarif_perjam" id="tarif_perjam" value="{{ old('tarif_perjam', $item->tarif_perjam) }}" required min="0" placeholder="0"
                           class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 text-sm @error('tarif_perjam') border-rose-500 @enderror">
                </div>
                @error('tarif_perjam')<p class="mt-1 text-[11px] text-rose-400 font-medium ml-1">{{ $message }}</p>@enderror
            </div>
        </div>
    @endcomponent
@endsection
