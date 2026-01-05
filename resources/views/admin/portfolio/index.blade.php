<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-black text-slate-800">Daftar Portofolio</h2>
                <a href="{{ route('admin.portfolio.create') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-bold">
                    + Tambah Karya
                </a>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm rounded-3xl p-6">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="pb-4 font-black">Gambar</th>
                            <th class="pb-4 font-black">Judul</th>
                            <th class="pb-4 font-black">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($portfolios as $item)
                        <tr class="border-b last:border-0">
                            <td class="py-4">
                                <img src="{{ asset('storage/portfolios/' . $item->gambar) }}" class="w-20 h-20 object-cover rounded-2xl">
                            </td>
                            <td class="py-4 font-bold">{{ $item->judul }}</td>
                            <td class="py-4">
                                <form action="{{ route('admin.portfolio.destroy', $item->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 font-bold" onclick="return confirm('Hapus?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>