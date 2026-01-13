<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Creative Design - @yield('title', 'CreativeDesign')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased ">
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

                {{-- Pesanan Link --}}
                @if (Auth::user())
                 <a href="{{ route('orders.index') }}" 
                class="{{ request()->routeIs('orders.index') ? 'text-indigo-600 font-bold' : 'text-slate-600 hover:text-indigo-600' }} transition">
                    Pesanan
                </a>   
                @endif
                
            </div>

            {{-- Desktop Auth / Hamburger --}}
            <div class="flex items-center gap-4">
                <div class="hidden md:flex items-center gap-4">
                    @auth
                        {{-- Tombol Admin Panel (Hanya Muncul untuk Admin) --}}
                        @if(Auth::user()->is_admin)
                            <a href="{{ route('admin') }}" 
                            class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 shadow-lg shadow-indigo-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 me-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                                </svg>
                                {{ __('Admin Panel') }}
                            </a>
                        @endif

                        {{-- Dropdown User (Breeze Style) --}}
                        <x-dropdown align="right" width="48" content_classes="py-1 bg-white rounded-2xl shadow-2xl ring-1 ring-black ring-opacity-5">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 text-base font-bold text-slate-700 hover:text-indigo-600 transition focus:outline-none">
                                    <div>{{ Auth::user()->name }}</div>

                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();" class="text-red-500 hover:text-red-700 hover:bg-red-50">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
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
            <a href="{{ route('orders.index') }}" @click="open = false"
            class="{{ request()->routeIs('orders.index') ? 'text-indigo-600 font-bold' : 'text-slate-600' }} text-lg">
                Pesanan
            </a>
            
            <hr class="border-slate-200">
            
            @auth
                {{-- Info User Mobile --}}
                <div class="px-4 pb-2 border-b border-slate-100 mb-2">
                    <div class="font-bold text-base text-slate-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-slate-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="space-y-1">
                    {{-- Admin Link Mobile --}}
                    @if(Auth::user()->is_admin)
                        <x-responsive-nav-link href="/admin/dashboard" :active="request()->is('admin/dashboard*')" class="text-indigo-600 font-bold border-l-4 border-indigo-600 bg-indigo-50">
                            {{ __('Admin Panel') }}
                        </x-responsive-nav-link>
                    @endif

                    {{-- Profile Link --}}
                    <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    {{-- Logout Link --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();" class="text-red-600">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
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