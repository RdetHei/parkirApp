<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $title ?? 'Neston')</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('images/neston.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/neston.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#020617] text-slate-100 antialiased selection:bg-emerald-500 selection:text-white" data-sidebar="expanded">
    <!-- Background Accents -->
    <div class="fixed inset-0 pro-grid opacity-20 pointer-events-none z-0"></div>
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-4xl h-64 bg-emerald-500/5 blur-[120px] pointer-events-none z-0"></div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .pro-grid {
            background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 32px 32px;
        }
    </style>
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

        @stack('scripts')

    <script>
        // Check for notifications
        function checkNotifications() {
            // Simulated real-time check for new activities
            // In a real app, this would use Pusher or Echo
            const hasNewNotify = localStorage.getItem('neston_new_activity');
            if (hasNewNotify) {
                if (window.Notification && Notification.permission === "granted") {
                    new Notification("Neston Update", {
                        body: "Ada aktivitas parkir baru pada akun Anda.",
                        icon: "/favicon.ico"
                    });
                    localStorage.removeItem('neston_new_activity');
                }
            }
        }

        if (window.Notification && Notification.permission !== "granted") {
            Notification.requestPermission();
        }

        setInterval(checkNotifications, 10000);
    </script>
</body>
</html>
