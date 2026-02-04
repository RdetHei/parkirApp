<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PARKED</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
</head>
<body class="bg-gray-100" data-sidebar="expanded">
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
                const body = document.body;
                const toggleBtn = document.getElementById('sidebar-toggle');

                if (!toggleBtn) return;

                const STORAGE_KEY = 'parkirapp.sidebar';

                const applyState = (state) => {
                    body.setAttribute('data-sidebar', state);
                };

                const saved = localStorage.getItem(STORAGE_KEY);
                if (saved === 'collapsed' || saved === 'expanded') {
                    applyState(saved);
                }

                toggleBtn.addEventListener('click', function () {
                    const current = body.getAttribute('data-sidebar') || 'expanded';
                    const next = current === 'collapsed' ? 'expanded' : 'collapsed';
                    applyState(next);
                    localStorage.setItem(STORAGE_KEY, next);
                });
            })();
        </script>

</body>
</html>
