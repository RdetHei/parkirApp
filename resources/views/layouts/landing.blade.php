<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @vite('resources/css/app.css')
        <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>

        {{-- Slot kosong untuk judul, dengan judul default 'Parkir App' --}}
        <title>NESTON</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/neston.svg') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('images/neston.ico') }}">
        <link rel="icon" type="image/png" href="{{ asset('images/neston.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    </head>
    <body class="bg-emerald-950 text-emerald-50 antialiased selection:bg-amber-400 selection:text-black">
        <!-- Batik Overlay -->
        <div class="fixed inset-0 bg-batik opacity-[0.03] pointer-events-none z-0"></div>

        <style>
            .bg-batik {
                background-image: url("{{ asset('images/batik-pattern.svg') }}");
                background-size: 560px 560px;
                background-repeat: repeat;
            }
        </style>
        {{-- Kita masukkan header di sini agar semua halaman punya header yang sama --}}
        @include('components.header')

        <main>
            {{-- Ini adalah slot kosong utama untuk konten halaman --}}
            @yield('content')
        </main>

        {{-- Masukkan footer di sini agar semua halaman punya footer yang sama --}}
        @include('components.footer')
    </body>
</html>
