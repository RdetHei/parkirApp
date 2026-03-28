@extends('layouts.app')

@section('title', 'ANPR Scan - Sistem Parkir Neston')

@section('content')
<div class="p-8 relative z-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                    AI Vision Core
                </span>
            </div>
            <h1 class="text-4xl font-bold tracking-tight text-white">ANPR <span class="text-emerald-500">Scanner</span></h1>
            <p class="text-slate-400 text-sm mt-2">Real-time Automatic Number Plate Recognition powered by YOLOv8.</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Camera & Scanner Section -->
        <div class="flex-1">
            <div class="card-pro !p-0 overflow-hidden border-white/5 relative group">
                <div class="p-6 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-500 border border-emerald-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-white uppercase tracking-widest">Live Feed</h2>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">Frequency: 0.5Hz (2s)</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-3 py-1.5 bg-slate-950 rounded-lg border border-white/5">
                        <span id="scan-status" class="flex h-2 w-2 rounded-full bg-rose-500 animate-pulse"></span>
                        <span id="status-text" class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Idle</span>
                    </div>
                </div>

                <div class="relative bg-slate-950 aspect-video flex items-center justify-center">
                    <video id="video" autoplay playsinline muted class="w-full h-full object-cover"></video>
                    <img id="upload-preview" class="w-full h-full object-contain hidden" alt="Upload Preview">
                    <canvas id="overlay" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>
                    
                    <!-- Floating Vehicle Info -->
                    <div id="scanner-vehicle-info" class="absolute top-6 left-6 bg-slate-900/90 backdrop-blur-md text-white px-6 py-4 rounded-2xl border border-white/10 shadow-2xl z-10 hidden">
                        <div class="flex items-center gap-4">
                            <div class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                            <div>
                                <p id="scanner-plate" class="text-2xl font-black tracking-tight leading-none">-</p>
                                <p id="scanner-vehicle" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1.5">-</p>
                            </div>
                        </div>
                    </div>

                    <div id="loading-overlay" class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center z-20 hidden">
                        <div class="text-center">
                            <div class="w-12 h-12 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin mx-auto mb-6"></div>
                            <p class="text-white text-[10px] font-bold uppercase tracking-[0.3em]">Processing OCR Analytics</p>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-white/[0.01] border-t border-white/5 flex flex-wrap gap-4 items-center justify-between">
                    <div class="flex gap-4">
                        <button id="start-scan" class="btn-pro-primary !py-3 !px-8 flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path></svg>
                            Start Scan
                        </button>
                        <button id="stop-scan" class="px-8 py-3 bg-rose-500/10 border border-rose-500/20 text-rose-500 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all flex items-center gap-3 active:scale-95 hidden">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path></svg>
                            Stop
                        </button>
                        
                        <input type="file" id="file-input" class="hidden" accept="image/*">
                        <button id="upload-btn" class="btn-pro-outline !py-3 !px-8 flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Upload Foto
                        </button>
                    </div>
                    <div class="flex items-center gap-3 px-4 py-2 bg-slate-900 border border-white/5 rounded-full">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Confidence > 80% REQUIRED</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Sidebar -->
        <div class="w-full lg:w-[420px] space-y-8">
            <div id="result-card" class="card-pro !p-0 overflow-hidden border-emerald-500/20 hidden animate-fade-in-up">
                <div class="px-8 py-5 border-b border-white/5 bg-white/[0.02]">
                    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em]">Latest Recognition</h3>
                </div>
                
                <div class="p-8 space-y-8">
                    <div class="text-center p-8 bg-slate-950 rounded-[2.5rem] border border-white/5 shadow-inner">
                        <p id="result-plate" class="text-5xl font-black tracking-tighter text-white mb-2">-------</p>
                        <div class="h-1 w-12 bg-emerald-500 mx-auto rounded-full opacity-50 mb-3"></div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Plate Identifier</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-5 bg-white/[0.02] rounded-2xl border border-white/5 group hover:border-emerald-500/30 transition-all">
                            <p id="result-confidence" class="text-2xl font-black text-emerald-500">0%</p>
                            <p class="text-[9px] font-bold text-slate-600 uppercase tracking-widest mt-1">AI Confidence</p>
                        </div>
                        <div class="p-5 bg-white/[0.02] rounded-2xl border border-white/5 group hover:border-indigo-500/30 transition-all">
                            <p id="result-action" class="text-2xl font-black text-indigo-500 uppercase">N/A</p>
                            <p class="text-[9px] font-bold text-slate-600 uppercase tracking-widest mt-1">System Action</p>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-950/50 rounded-2xl border border-white/5 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Vehicle Spec</span>
                            <span id="result-vehicle" class="text-xs text-white font-bold">-</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Timestamp</span>
                            <span id="result-time" class="text-xs text-white font-bold">-</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Current Status</span>
                            <span id="result-status" class="px-2 py-0.5 bg-emerald-500/10 text-emerald-500 rounded text-[9px] font-black uppercase">-</span>
                        </div>
                    </div>

                    <div id="result-image-container" class="rounded-[2rem] overflow-hidden border border-white/5 bg-slate-950 group">
                        <img id="result-image" src="" alt="Scan Thumbnail" class="w-full h-auto object-cover opacity-60 group-hover:opacity-100 transition-opacity">
                    </div>
                </div>
            </div>

            <div id="no-result" class="card-pro !p-12 text-center border-dashed border-white/10 opacity-50">
                <div class="w-20 h-20 bg-slate-950 rounded-3xl flex items-center justify-center mx-auto mb-8 text-slate-800 border border-white/5">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                </div>
                <h4 class="text-sm font-bold text-slate-500 uppercase tracking-widest">Ready to Scan</h4>
                <p class="text-xs text-slate-700 mt-2">Position the vehicle plate within the frame to begin recognition.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/anpr.js') }}"></script>
@endpush
