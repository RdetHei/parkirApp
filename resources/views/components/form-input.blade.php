@props(['name', 'label', 'type' => 'text', 'value' => '', 'placeholder' => '', 'required' => false])

<div class="space-y-2">
    @if(isset($label))
    <label for="{{ $name }}" class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">
        {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
    </label>
    @endif
    
    <div class="relative group">
        @if($type === 'textarea')
            <textarea
                name="{{ $name }}"
                id="{{ $name }}"
                placeholder="{{ $placeholder }}"
                @if($required) required @endif
                {{ $attributes->merge(['class' => 'block w-full px-4 py-3 bg-slate-900/50 border border-white/5 rounded-xl text-sm text-white placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500/50 transition-all duration-300 min-h-[100px]']) }}
            >{{ old($name, $value) }}</textarea>
        @else
            <input
                type="{{ $type }}"
                name="{{ $name }}"
                id="{{ $name }}"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
                @if($required) required @endif
                {{ $attributes->merge(['class' => 'block w-full px-4 py-3 bg-slate-900/50 border border-white/5 rounded-xl text-sm text-white placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500/50 transition-all duration-300']) }}
            >
        @endif
        
        <!-- Subtle Glow on Hover -->
        <div class="absolute inset-0 rounded-xl bg-emerald-500/5 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-300"></div>
    </div>

    @error($name)
        <p class="text-[10px] font-bold text-rose-500 uppercase tracking-widest mt-1.5 ml-1 animate-fade-in">
            {{ $message }}
        </p>
    @enderror
</div>

