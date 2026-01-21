<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @vite('resources/css/app.css')
        <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>

        {{-- Slot kosong untuk judul, dengan judul default 'Parkir App' --}}
        <title>PARKED</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    </head>
    <body>
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
