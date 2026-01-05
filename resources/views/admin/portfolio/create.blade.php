<form action="{{ route('admin.portfolio.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="text" name="judul" placeholder="Judul Karya" class="rounded-2xl border-slate-200 w-full mb-4">
    <input type="file" name="gambar" class="w-full mb-4">
    <button type="submit" class="bg-indigo-600 text-white w-full py-3 rounded-2xl font-black">Simpan Karya</button>
</form>