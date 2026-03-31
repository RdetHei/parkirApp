@props([
    'user' => null,
    'size' => 'md',
    'round' => 'xl',
    'initials' => null,
])

@php
    $name = $user->name ?? '';
    $computedInitials = $initials ?? strtoupper(\Illuminate\Support\Str::substr($name !== '' ? $name : '?', 0, 2));
    if ($computedInitials === '') {
        $computedInitials = '?';
    }
    $url = $user?->profile_photo_url;

    $sizeClasses = [
        'xs' => 'w-6 h-6 text-[10px]',
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-20 h-20 text-2xl',
        'xl' => 'w-32 h-32 text-3xl',
    ];
    $roundClasses = [
        'xl' => 'rounded-xl',
        '2xl' => 'rounded-2xl',
        'lg' => 'rounded-lg',
        'full' => 'rounded-full',
    ];
    $box = ($sizeClasses[$size] ?? $sizeClasses['md']).' '.($roundClasses[$round] ?? $roundClasses['xl']);
@endphp

@if($url)
    <img src="{{ $url }}" alt=""
         {{ $attributes->class([$box, 'shrink-0 object-cover border border-white/10']) }}>
@else
    <div {{ $attributes->class([$box, 'shrink-0 flex items-center justify-center font-bold uppercase border border-white/10 bg-slate-800 text-slate-300']) }}>
        {{ $computedInitials }}
    </div>
@endif
