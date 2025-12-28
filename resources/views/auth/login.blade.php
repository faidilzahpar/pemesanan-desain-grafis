<x-guest-layout>
    <div class="mb-8 text-center">
        <span class="bg-indigo-50 text-indigo-600 px-4 py-1.5 rounded-full text-xs font-bold tracking-wide uppercase mb-4 inline-block">
            Akses Member
        </span>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Selamat Datang Kembali</h2>
        <p class="text-slate-500 mt-2">Masuk untuk mengelola pesanan desain Anda</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div>
            <label for="login" class="block text-sm font-bold text-slate-700 mb-2">Alamat Email</label>
            <input id="login" type="email" name="login" :value="old('login')" required autofocus 
                class="w-full px-5 py-4 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50/50 transition-all"
                placeholder="nama@email.com">
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
        </div>

        <div>
            <div class="flex justify-between mb-2">
                <label for="password" class="block text-sm font-bold text-slate-700">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-sm text-indigo-600 hover:text-indigo-500 font-semibold" href="{{ route('password.request') }}">
                        Lupa Password?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required 
                class="w-full px-5 py-4 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50/50 transition-all"
                placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded-md border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-slate-500 font-medium italic">Ingat perangkat ini</span>
            </label>
        </div>

        <div class="pt-2">
    <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold hover:bg-indigo-600 shadow-lg hover:shadow-indigo-200 transition-all duration-300 flex items-center justify-center gap-2 group">
        Masuk ke Akun
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
        </svg>
    </button>
    </div>
    </form>

    <div class="mt-8 pt-6 border-t border-slate-100 text-center text-sm text-slate-500">
        Belum punya akun? 
        <a href="{{ route('register') }}" class="text-indigo-600 font-bold hover:underline italic">Daftar sekarang gratis</a>
    </div>
</x-guest-layout>