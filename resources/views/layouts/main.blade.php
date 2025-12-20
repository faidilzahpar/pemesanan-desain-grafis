<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'CreativeDesign')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased">

    {{-- NAVBAR --}}
    <header class="fixed w-full z-50 glass border-b border-slate-200">
        <nav class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200">
                    <span class="text-white font-bold text-xl">D</span>
                </div>
                <span class="font-extrabold text-xl tracking-tight">
                    Creative<span class="text-indigo-600">Design</span>
                </span>
            </a>

            {{-- Menu --}}
            <div class="hidden md:flex items-center gap-8 font-medium text-slate-600">
                <a href="{{ route('home') }}#layanan" class="hover:text-indigo-600 transition">
                    Layanan
                </a>
                <a href="#" class="hover:text-indigo-600 transition">
                    Portofolio
                </a>
            </div>

            {{-- Auth --}}
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('profile.edit') }}"
                       class="font-semibold text-indigo-600">
                        Profile
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="text-slate-600 font-medium hover:text-indigo-600 transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                       class="bg-indigo-600 text-white px-5 py-2.5 rounded-full font-semibold shadow-md
                              hover:bg-indigo-700 transition transform hover:scale-105 active:scale-95">
                        Mulai Order
                    </a>
                @endauth
            </div>
        </nav>
    </header>

    {{-- CONTENT --}}
    <main class="pt-28">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="py-12 px-6 text-center text-slate-400 border-t border-slate-100">
        <p class="text-sm uppercase font-bold tracking-widest mb-4">
            CreativeDesign Studio
        </p>
        <p>&copy; {{ date('Y') }} Projek Pemesanan Desain Grafis. All rights reserved.</p>
    </footer>

</body>
</html>
