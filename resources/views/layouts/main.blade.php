<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Creative Design - @yield('title', 'CreativeDesign')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    {{-- NAVBAR --}}
    <header class="fixed w-full z-50 glass border-b border-slate-200" 
        x-data="{ 
            open: false, 
            {{-- Hanya set 'home' sebagai default jika route-nya memang home --}}
            activeSection: '{{ request()->routeIs('home') ? 'home' : '' }}',
            
            spyScroll() {
                {{-- Jangan jalankan scroll spy jika bukan di halaman home --}}
                if (!window.location.href.includes('#') && !{{ request()->routeIs('home') ? 'true' : 'false' }}) return;

                const sections = ['home', 'layanan'];
                sections.forEach(id => {
                    const section = document.getElementById(id);
                    if (section) {
                        const offsetTop = section.offsetTop - 100;
                        const height = section.offsetHeight;
                        if (window.scrollY >= offsetTop && window.scrollY < offsetTop + height) {
                            this.activeSection = id;
                        }
                    }
                });
            }
        }" 
        x-init="spyScroll()"
        @scroll.window="spyScroll()"
    >
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

            {{-- Desktop Menu --}}
            <div class="hidden md:flex items-center gap-8 font-medium">
                {{-- Home Link --}}
                <a href="{{ route('home') }}#home" 
                :class="activeSection === 'home' ? 'text-indigo-600 font-bold' : 'text-slate-600 hover:text-indigo-600'"
                class="transition">
                    Home
                </a>
                
                {{-- Layanan Link --}}
                <a href="{{ route('home') }}#layanan" 
                :class="activeSection === 'layanan' ? 'text-indigo-600 font-bold' : 'text-slate-600 hover:text-indigo-600'"
                class="transition">
                    Layanan
                </a>

                {{-- Portofolio Link --}}
                <a href="{{ route('portfolio') }}" 
                class="{{ request()->routeIs('portfolio') ? 'text-indigo-600 font-bold' : 'text-slate-600 hover:text-indigo-600' }} transition">
                    Portofolio
                </a>

                {{-- Portofolio Link --}}
                <a href="{{ route('orders.index') }}" 
                class="{{ request()->routeIs('orders.index') ? 'text-indigo-600 font-bold' : 'text-slate-600 hover:text-indigo-600' }} transition">
                    Pesanan
                </a>
            </div>

            {{-- Desktop Auth / Hamburger --}}
            <div class="flex items-center gap-4">
                <div class="hidden md:flex items-center gap-4">
                    @auth
                        <a href="{{ route('profile.edit') }}" class="font-semibold text-indigo-600">Profile</a>
                    @else
                        <a href="{{ route('login') }}" class="text-slate-600 font-medium hover:text-indigo-600 transition">Masuk</a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-5 py-2.5 rounded-full font-semibold shadow-md hover:bg-indigo-700 transition transform hover:scale-105 active:scale-95">
                            Mulai Order
                        </a>
                    @endauth
                </div>

                {{-- Mobile Hamburger Button --}}
                <button @click="open = !open" class="md:hidden p-2 text-slate-600 hover:text-indigo-600 transition">
                    <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                    <svg x-show="open" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </nav>

        {{-- Mobile Menu --}}
        <div x-show="open" 
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="md:hidden glass border-b border-slate-200 absolute w-full left-0 p-6 flex flex-col gap-4 shadow-xl">
            
            <a href="{{ route('home') }}#home" @click="open = false"
            :class="activeSection === 'home' ? 'text-indigo-600 font-bold' : 'text-slate-600'" 
            class="text-lg">
                Home
            </a>
            <a href="{{ route('home') }}#layanan" @click="open = false"
            :class="activeSection === 'layanan' ? 'text-indigo-600 font-bold' : 'text-slate-600'" 
            class="text-lg">
                Layanan
            </a>
            <a href="{{ route('portfolio') }}" @click="open = false"
            class="{{ request()->routeIs('portfolio') ? 'text-indigo-600 font-bold' : 'text-slate-600' }} text-lg">
                Portofolio
            </a>
            
            <hr class="border-slate-200">
            
            @auth
                <a href="{{ route('profile.edit') }}" class="font-semibold text-indigo-600">Profile</a>
            @else
                <div class="flex flex-col gap-3">
                    <a href="{{ route('login') }}" class="text-slate-600 font-medium text-center py-2">Masuk</a>
                    <a href="{{ route('register') }}" class="bg-indigo-600 text-white text-center py-3 rounded-full font-semibold">
                        Mulai Order
                    </a>
                </div>
            @endauth
        </div>
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