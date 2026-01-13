@extends('layouts.admin')

@section('title', 'Edit Karya')

@section('content')
<div class="bg-white shadow-lg rounded-xl p-4 md:p-8 min-h-[calc(100vh-6rem)]">
    {{-- Heading --}}
    <div class="flex items-center justify-between mb-5">
        <h1 class="text-3xl font-bold text-gray-800">Edit Karya</h1>

        <a href="{{ route('admin.portfolio.index') }}"
            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg shadow-sm transition">
            Kembali
        </a>
    </div>

    {{-- Divider --}}
    <div class="mt-8 pt-6 border-t border-gray-200"></div>

    {{-- Form Card --}}
    <div class="bg-white p-8 rounded-xl">

        <form action="{{ route('admin.portfolio.update', $portfolio->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') {{-- Penting untuk proses Update --}}

            {{-- Judul Karya --}}
            <div class="mb-6">
                <label for="judul" class="font-semibold text-gray-900 mb-2 block">
                    Judul Karya
                </label>
                <input type="text" name="judul" id="judul"
                    class="w-full px-4 py-3 border rounded-lg shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 transition"
                    placeholder="Masukkan judul portofolio"
                    value="{{ old('judul', $portfolio->judul) }}" required>

                @error('judul')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kategori (Ditambahkan sesuai Controller store Anda) --}}
            <div class="mb-6">
                <label for="kategori" class="font-semibold text-gray-900 mb-2 block">
                    Kategori
                </label>
                <input type="text" name="kategori" id="kategori"
                    class="w-full px-4 py-3 border rounded-lg shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 transition"
                    placeholder="Contoh: Logo, Web Design, dll"
                    value="{{ old('kategori', $portfolio->kategori) }}" required>

                @error('kategori')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Gambar Karya (Alpine JS Upload dengan Preview Gambar Lama) --}}
            <div class="mb-6">
                <label class="font-semibold text-gray-900 mb-2 block">
                    Gambar Karya
                </label>

                {{-- Inisialisasi Alpine dengan gambar lama --}}
                <div class="border border-dashed border-indigo-300 rounded-xl p-6 bg-indigo-50/40"
                    x-data="{ 
                        file: null, 
                        preview: '{{ asset('storage/' . $portfolio->gambar) }}' 
                    }">

                    <input type="file"
                        name="gambar"
                        x-ref="fileInput"
                        class="hidden"
                        @change="
                            file = $event.target.files[0];
                            if (file && file.type.startsWith('image/')) {
                                preview = URL.createObjectURL(file);
                            }
                        ">

                    {{-- Dropzone Area --}}
                    <div @click="$refs.fileInput.click()"
                        class="cursor-pointer bg-white rounded-xl p-6 text-center
                                hover:bg-indigo-50 transition border border-transparent hover:border-indigo-200">

                        {{-- State Preview (Selalu muncul karena defaultnya gambar lama) --}}
                        <div class="flex flex-col items-center space-y-2">
                            <template x-if="preview">
                                <img :src="preview"
                                    class="max-h-64 rounded-lg border shadow-sm object-cover">
                            </template>

                            {{-- Jika file belum diganti, tampilkan info gambar lama --}}
                            <template x-if="!file">
                                <p class="text-sm text-slate-500 mt-2">
                                    Gambar saat ini. Klik untuk mengganti.
                                </p>
                            </template>

                            {{-- Jika file baru dipilih, tampilkan nama file baru --}}
                            <template x-if="file">
                                <div class="mt-2">
                                    <p class="text-sm font-semibold text-gray-700" x-text="file.name"></p>
                                    <p class="text-xs text-slate-500"
                                    x-text="(file.size / 1024 / 1024).toFixed(2) + ' MB'"></p>
                                </div>
                            </template>
                        </div>

                    </div>
                </div>

                @error('gambar')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi (Ditambahkan sesuai Controller store Anda) --}}
            <div class="mb-6">
                <label for="deskripsi" class="font-semibold text-gray-900 mb-2 block">
                    Deskripsi
                </label>
                <textarea name="deskripsi" id="deskripsi" rows="4"
                    class="w-full px-4 py-3 border rounded-lg shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 transition"
                    placeholder="Deskripsi karya...">{{ old('deskripsi', $portfolio->deskripsi) }}</textarea>

                @error('deskripsi')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Divider --}}
            <div class="mt-8 pt-6 border-t border-gray-200"></div>

            {{-- Tombol Submit --}}
            <div class="flex justify-end">
                <button type="submit"
                    class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 hover:shadow-indigo-500/50 transition duration-300">
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection