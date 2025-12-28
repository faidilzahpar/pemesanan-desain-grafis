<x-guest-layout>
    <div class="mb-8 text-center">
        <span class="bg-indigo-50 text-indigo-600 px-4 py-1.5 rounded-full text-xs font-bold tracking-wide uppercase mb-4 inline-block">
            Mulai Project Anda
        </span>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Daftar Akun Baru</h2>
        <p class="text-slate-500 mt-2">Bergabunglah untuk mendapatkan layanan desain terbaik</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap</label>
            <input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                class="w-full px-5 py-3.5 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50/50 transition-all"
                placeholder="Masukkan nama lengkap Anda">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Alamat Email</label>
            <input id="email" type="email" name="email" :value="old('email')" required autocomplete="username"
                class="w-full px-5 py-3.5 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50/50 transition-all"
                placeholder="nama@email.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-bold text-slate-700 mb-2">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full px-5 py-3.5 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50/50 transition-all"
                placeholder="Minimal 8 karakter">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-2">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="w-full px-5 py-3.5 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50/50 transition-all"
                placeholder="Ulangi password Anda">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-4">
            <button class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 hover:shadow-indigo-300 transition-all duration-300 flex items-center justify-center gap-2 group">
                Buat Akun Sekarang
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </div>
    </form>

    <div class="mt-8 pt-6 border-t border-slate-100 text-center text-sm text-slate-500">
        Sudah punya akun? 
        <a href="{{ route('login') }}" class="text-indigo-600 font-bold hover:underline italic">Masuk di sini</a>
    </div>
</x-guest-layout>