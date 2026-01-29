<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PARKED</title>
    <link rel="icon" href="{{ asset('images/parked.ico') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
    {{-- This script prevents FOUC by setting the sidebar state before the page renders --}}
    <script>
        (function() {
            const STORAGE_KEY = 'parkirapp.sidebar';
            const savedState = localStorage.getItem(STORAGE_KEY);
            if (savedState === 'collapsed' || savedState === 'expanded') {
                document.documentElement.setAttribute('data-sidebar', savedState);
            } else {
                document.documentElement.setAttribute('data-sidebar', 'expanded');
            }
        })();
    </script>
</head>
<body class="bg-gray-100">
        {{-- Layout wrapper: sidebar + main content --}}
        <div class="min-h-screen flex">
            @include('components.sidebar')

            <div class="flex-1 flex flex-col min-w-0">
                @include('components.dheader')

                <main class="flex-1 overflow-y-auto w-full">
                    @yield('content')
                </main>
            </div>
        </div>

        <script>
            (function () {
                const burgerToggle = document.getElementById('sidebar-toggle');
                const logoToggle = document.getElementById('logo-toggle');
                const htmlEl = document.documentElement;

                if (!burgerToggle || !logoToggle) return;

                const STORAGE_KEY = 'parkirapp.sidebar';

                burgerToggle.addEventListener('click', function () {
                    htmlEl.setAttribute('data-sidebar', 'collapsed');
                    localStorage.setItem(STORAGE_KEY, 'collapsed');
                });

                logoToggle.addEventListener('click', function () {
                    const isCollapsed = htmlEl.getAttribute('data-sidebar') === 'collapsed';
                    if (isCollapsed) {
                        htmlEl.setAttribute('data-sidebar', 'expanded');
                        localStorage.setItem(STORAGE_KEY, 'expanded');
                    }
                });
            })();
        </script>

</body>
</html>
