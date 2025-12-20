<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Invoice')</title>

    {{-- Tailwind --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="bg-gray-100 text-gray-800">

    {{-- HEADER --}}
    <header class="bg-white shadow-sm">
        <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-lg font-bold text-blue-600">
                {{ config('app.name', 'Website') }}
            </h1>

            @auth
                <div class="text-sm text-gray-600">
                    {{ auth()->user()->name }}
                </div>
            @endauth
        </div>
    </header>

    {{-- PAGE CONTENT --}}
    <main class="max-w-6xl mx-auto px-6 py-8">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-white border-t mt-10">
        <div class="max-w-6xl mx-auto px-6 py-4 text-center text-sm text-gray-500">
            © {{ date('Y') }} {{ config('app.name', 'Website') }}. All rights reserved.
        </div>
    </footer>

</body>
</html>
