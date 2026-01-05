<section>
    <header>
        <h2 class="text-xl font-bold text-slate-900 italic">Informasi Akun</h2>
        <p class="mt-1 text-sm text-slate-500">Perbarui nama dan alamat email resmi Anda.</p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap</label>
            <input name="name" type="text" class="w-full px-5 py-4 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50/50 text-slate-900" value="{{ old('name', $user->name) }}" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Email</label>
            <input name="email" type="email" class="w-full px-5 py-4 rounded-2xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50/50 text-slate-900" value="{{ old('email', $user->email) }}" required />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="mt-6">
            <label for="no_hp" class="block text-sm font-bold text-slate-700 mb-2">
                {{ __('Nomor HP / WhatsApp') }}
            </label>
    
            <input id="no_hp" name="no_hp" type="text" 
                class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50/50 text-slate-900 font-medium transition-all" 
                value="{{ old('no_hp', $user->no_hp) }}" 
                required 
                autocomplete="tel">
        
            <x-input-error class="mt-2" :messages="$errors->get('no_hp')" />
        </div>

        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-10 rounded-2xl shadow-lg shadow-indigo-100 transition-all active:scale-95">
            Simpan Perubahan
        </button>
    </form>
</section>