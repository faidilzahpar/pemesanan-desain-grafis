<section>
    <header>
        <h2 class="text-xl font-bold text-slate-900 italic">Keamanan Akun</h2>
        <p class="mt-1 text-sm text-slate-500">Gunakan password yang kuat untuk menjaga pesanan desain Anda.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Password Saat Ini</label>
            <input name="current_password" type="password" class="w-full px-5 py-4 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50/50" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Password Baru</label>
            <input name="password" type="password" class="w-full px-5 py-4 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50/50" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <button type="submit" class="bg-slate-900 hover:bg-indigo-600 text-white font-bold py-4 px-10 rounded-2xl shadow-lg transition-all active:scale-95">
            Perbarui Password
        </button>
    </form>
</section>