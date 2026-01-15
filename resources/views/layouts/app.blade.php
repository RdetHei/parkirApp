<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @vite('resources/css/app.css')
        <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>


        @include('components.dheader')
        {{-- Layout wrapper: sidebar + main content --}}
        <div class="min-h-screen flex bg-gray-100">
            @include('components.sidebar')

            <div class="flex-1 flex flex-col">
                <main class="flex-1 overflow-y-auto">
                    <div class="container mx-auto px-6 py-6">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>

