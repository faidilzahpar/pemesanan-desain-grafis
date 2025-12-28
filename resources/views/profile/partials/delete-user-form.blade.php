<section>
    <header>
        <h2 class="text-xl font-bold text-red-600 italic">Area Berbahaya</h2>
        <p class="mt-1 text-sm text-slate-500">Semua data riwayat desain akan dihapus permanen.</p>
    </header>

    <button class="mt-6 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white font-bold py-4 px-8 rounded-2xl transition-all border border-red-100"
        x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        Hapus Akun Saya
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-10">
            @csrf
            @method('delete')
            <h2 class="text-2xl font-bold text-slate-900">Konfirmasi Hapus</h2>
            <p class="mt-2 text-slate-500">Masukkan password Anda untuk menghapus akun secara permanen.</p>
            <input name="password" type="password" class="mt-6 w-full px-5 py-4 rounded-2xl border-slate-200 focus:border-red-500 focus:ring-red-500 bg-slate-50" placeholder="Password Konfirmasi" />
            <div class="mt-8 flex justify-end gap-4">
                <button type="button" class="px-6 py-4 font-bold text-slate-500" x-on:click="$dispatch('close')">Batal</button>
                <button type="submit" class="bg-red-600 text-white font-bold py-4 px-8 rounded-2xl shadow-lg shadow-red-100">Hapus Permanen</button>
            </div>
        </form>
    </x-modal>
</section>