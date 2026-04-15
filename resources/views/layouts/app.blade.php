<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'NESTON - Smart Parking System');</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/neston.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/neston.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/neston.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Satu @vite saja: hindari load ganda app.js (Alpine.start 2x) yang membuat sidebar/toggle rusak di production --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        [x-cloak] { display: none !important; }

        :root {
            --bg-main: #020617;
            --bg-panel: #0f172a;
            --border-pro: rgba(255, 255, 255, 0.08);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-main) !important;
            color: #f8fafc !important;
        }

        .pro-grid {
            background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        /* Robust Dark Mode Overrides */
        .card-pro {
            background-color: var(--bg-panel) !important;
            border: 1px solid var(--border-pro) !important;
        }

        aside#app-sidebar {
            background-color: var(--bg-main) !important;
            border-right: 1px solid var(--border-pro) !important;
        }

        header.app-top-header {
            background-color: var(--bg-main) !important;
            border-bottom: 1px solid var(--border-pro) !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-[#020617] text-slate-100 antialiased selection:bg-emerald-500 selection:text-white overflow-x-hidden"
      x-data="{
         sidebarOpen: false,
         accountOpen: false,
         desktopCollapsed: localStorage.getItem('parkirapp.sidebar') === 'collapsed',
         isMobile: window.innerWidth < 1024
      }"
      x-init="
         $watch('desktopCollapsed', val => {
             localStorage.setItem('parkirapp.sidebar', val ? 'collapsed' : 'expanded');
         });
         window.addEventListener('resize', () => {
             $data.isMobile = window.innerWidth < 1024;
             if (!$data.isMobile) $data.sidebarOpen = false;
         });
      "
      :data-sidebar="desktopCollapsed ? 'collapsed' : 'expanded'">
    <!-- Background Accents -->
    <div class="fixed inset-0 pro-grid opacity-20 pointer-events-none z-0"></div>
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-4xl h-64 bg-emerald-500/5 blur-[120px] pointer-events-none z-0"></div>

    {{-- Layout wrapper: sidebar + main content --}}
    <div class="h-screen flex relative bg-[#020617]">
        <!-- Mobile Sidebar Backdrop -->
        <div x-cloak
             x-show="sidebarOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 z-[65] bg-slate-950/60 lg:hidden transition-opacity duration-300"></div>

        @include('components.sidebar')

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative transition-all duration-300 ease-in-out">
            @include('components.dheader')

            <main class="flex-1 overflow-y-auto overflow-x-hidden w-full relative">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
