@extends('layouts.admin')

@section('title', 'Tambah Jenis Desain')
@section('content')
    {{-- Heading --}}
    <div class="flex items-center justify-between mb-5">
        <h1 class="text-3xl font-bold text-gray-800">Tambah Jenis Desain</h1>

        <a href="{{ route('design-types.index') }}"
            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg shadow-sm transition">
            Kembali
        </a>
    </div>
    {{-- Divider --}}
    <div class="mt-8 pt-6 border-t border-gray-200"></div>

    {{-- Form Card --}}
    <div class="bg-white p-8 rounded-xl">

        <form action="{{ route('design-types.store') }}" method="POST">
            @csrf

            {{-- Nama Jenis --}}
            <div class="mb-6">
                <label for="nama_jenis" class="font-semibold text-gray-900 mb-2">
                    Nama Jenis Desain
                </label>
                <input type="text" name="nama_jenis" id="nama_jenis"
                    class="w-full px-4 py-3 border rounded-lg shadow-sm border-gray-300"
                    value="{{ old('nama_jenis') }}" required>

                @error('nama_jenis')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Harga --}}
            <div class="mb-6">
                <label for="harga" class="font-semibold text-gray-900 mb-2">
                    Harga (Rp)
                </label>
                <input type="number" name="harga" id="harga"
                    class="w-full px-4 py-3 border rounded-lg shadow-sm border-gray-300"
                    value="{{ old('harga') }}" required>

                @error('harga')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Durasi --}}
            <div class="mb-6">
                <label for="durasi" class="font-semibold text-gray-900 mb-2">
                    Durasi Pengerjaan (hari)
                </label>
                <input type="number" name="durasi" id="durasi"
                    class="w-full px-4 py-3 border rounded-lg shadow-sm border-gray-300"
                    value="{{ old('durasi') }}" required>

                @error('durasi')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div class="mb-6">
                <label for="deskripsi" class="font-semibold text-gray-900 mb-2">
                    Deskripsi
                </label>
                <textarea name="deskripsi" id="deskripsi" rows="4"
                    class="w-full px-4 py-3 border rounded-lg shadow-sm border-gray-300"
                    required>{{ old('deskripsi') }}</textarea>

                @error('deskripsi')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Divider --}}
            <div class="mt-8 pt-6 border-t border-gray-200"></div>
            {{-- Tombol Submit --}}
            <div class="flex justify-end">
                <button type="submit"
                    class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 hover:shadow-blue-500/50 transition duration-300">
                    <span>Simpan</span>
                </button>
            </div>

        </form>

    </div>
@endsection
