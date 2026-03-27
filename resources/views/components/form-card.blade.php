<div class="p-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                    System Console
                </span>
            </div>
            <h1 class="text-4xl font-bold tracking-tight text-white">{{ $title ?? 'Form Title' }}</h1>
            <p class="text-slate-400 text-sm mt-2">{{ $description ?? 'Please fill out the form below.' }}</p>
        </div>
        @if(isset($backUrl))
        <div class="flex items-center gap-4">
            <a href="{{ $backUrl }}" class="group relative px-6 py-3 bg-slate-800 text-white font-bold text-xs uppercase tracking-widest rounded-xl border border-white/10 transition-all hover:bg-slate-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                Back to List
            </a>
        </div>
        @endif
    </div>

    <!-- Error Alert -->
    @if($errors->any())
        <div class="mb-10 bg-rose-500/10 border border-rose-500/20 rounded-2xl p-6 backdrop-blur-md animate-fade-in">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 bg-rose-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-bold text-rose-500 mb-2 uppercase tracking-widest">Validation Error</p>
                    <ul class="list-disc list-inside text-xs text-slate-400 space-y-1 font-medium">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Container -->
    <div class="max-w-4xl mx-auto relative group">
        <div class="absolute -inset-0.5 bg-gradient-to-r from-emerald-500/20 to-indigo-500/20 rounded-[2rem] blur opacity-30 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
        
        <div class="relative card-pro !p-0 overflow-hidden shadow-2xl">
            <!-- Card Header -->
            <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02]">
                <div class="flex items-center gap-5">
                    <div class="w-12 h-12 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center justify-center text-emerald-500">
                        {!! $cardIcon ?? '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>' !!}
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-white uppercase tracking-widest">{{ $cardTitle ?? 'Form Data' }}</h2>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">{{ $cardDescription ?? 'Please complete all required fields.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Form Body -->
            <div class="p-8">
                <form action="{{ $action ?? '#' }}" method="POST" class="space-y-8" enctype="multipart/form-data">
                    @csrf
                    @if(isset($method) && ($method == 'PUT' || $method == 'PATCH'))
                        @method($method)
                    @endif

                    <div class="space-y-6">
                        {{ $slot }}
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-5 pt-10 mt-10 border-t border-white/5">
                        <a href="{{ $backUrl ?? '#' }}"
                           class="px-8 py-3.5 bg-slate-900/50 hover:bg-slate-800 text-slate-400 font-bold rounded-xl border border-white/5 transition-all duration-300 text-[10px] uppercase tracking-widest">
                            {{ $cancelText ?? 'Cancel' }}
                        </a>
                        <button type="submit"
                                class="px-10 py-3.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold rounded-xl shadow-xl shadow-emerald-500/20 transition-all duration-300 hover:scale-[1.02] active:scale-95 text-[10px] uppercase tracking-widest">
                            {{ $submitText ?? 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

