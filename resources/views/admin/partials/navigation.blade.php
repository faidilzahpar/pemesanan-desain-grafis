{{-- MOBILE OVERLAY (muncul kalau sidebarOpen = true) --}}
<div
    class="fixed inset-0 bg-black bg-opacity-40 z-30 md:hidden"
    x-show="sidebarOpen"
    x-transition.opacity
    @click="sidebarOpen = false"
></div>

{{-- SIDEBAR --}}
<aside
    class="fixed top-0 left-0 z-40 h-screen w-64 bg-blue-600 text-white shadow-xl transform
           sidebar-transition md:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    <div class="p-6 h-20 flex items-center border-b border-blue-500">
        <span class="text-2xl font-semibold tracking-wider">Halaman Admin</span>
    </div>

    <nav class="mt-5 space-y-2 px-4">

        <a href="{{  route('admin.dashboard') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-500 transition
            {{ Route::is('admin.dashboard') ? 'bg-blue-700' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6m-6 0h-2"/></svg>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.orders') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-500 transition 
            {{ Route::is('admin.orders') ? 'bg-blue-700' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span>Pesanan</span>
        </a>

        <a href="{{ route('admin.payments') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-500 transition
            {{ Route::is('admin.payments') ? 'bg-blue-700' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3"/></svg>
            <span>Pembayaran</span>
        </a>

        <a href="{{ route('design-types.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-500 transition
            {{ Route::is('admin.design-types') ? 'bg-blue-700' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414l-2.707-2.707A1 1 0 0015.586 6H7a2 2 0 00-2 2v11a2 2 0 002 2"/></svg>
            <span>Jenis Desain</span>
        </a>

        <a href="{{ route('home') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-500 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            <span>Halaman Utama</span>
        </a>

        {{-- Logout --}}
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-500 transition mt-10 !pt-6 border-t border-blue-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            <span>Logout</span>
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </nav>
</aside>

{{-- TOPBAR --}}
<header class="sticky top-0 z-30 h-16 bg-white shadow-md flex items-center transition-all duration-300":class="sidebarOpen ? 'md:ml-64' : 'md:ml-64'">
    <div class="flex justify-between items-center w-full px-4 md:px-6">

        {{-- HAMBURGER --}}
        <button
            @click="sidebarOpen = !sidebarOpen"
            class="md:hidden text-gray-500 hover:text-blue-500 focus:outline-none"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div class="hidden md:block"></div>

        <div class="flex items-center space-x-2 text-gray-700">
            <span class="font-medium text-sm md:text-base">{{ auth()->user()->name }}</span>
            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0"/>
            </svg>
        </div>

    </div>
</header>
