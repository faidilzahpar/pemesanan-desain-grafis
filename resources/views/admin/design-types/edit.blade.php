@extends('layouts.admin')

@section('title', 'Edit Jenis Desain')
@section('content')
<div class="bg-white shadow-lg rounded-xl p-4 md:p-8 min-h-[calc(100vh-6rem)]">
    {{-- Heading --}}
    <div class="flex items-center justify-between mb-5">
        <h1 class="text-3xl font-bold text-gray-800">Edit Jenis Desain</h1>

        <a href="{{ route('design-types.index') }}"
            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg shadow-sm transition">
            Kembali
        </a>
    </div>

    {{-- Divider --}}
    <div class="mt-8 pt-6 border-t border-gray-200"></div>

    {{-- Form Card --}}
    <div class="bg-white p-8 rounded-xl">

        <form action="{{ route('design-types.update', $type->design_type_id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Nama Jenis --}}
            <div class="mb-6">
                <label for="nama_jenis" class="font-semibold text-gray-900 mb-2">
                    Nama Jenis Desain
                </label>
                <input type="text" name="nama_jenis" id="nama_jenis"
                    class="w-full px-4 py-3 border rounded-lg shadow-sm border-gray-300"
                    value="{{ old('nama_jenis', $type->nama_jenis) }}" required>

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
                    value="{{ old('harga', $type->harga) }}" required>

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
                    value="{{ old('durasi', $type->durasi) }}" required>

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
                    required>{{ old('deskripsi', $type->deskripsi) }}</textarea>

                @error('deskripsi')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status Aktif --}}
            <div class="mb-6">
                <label class="font-semibold text-gray-900 mb-2 block">
                    Status
                </label>

                <label class="relative inline-flex items-center cursor-pointer gap-2">
                    <input type="checkbox" name="is_active" id="statusToggle" class="sr-only peer"
                        {{ old('is_active', $type->is_active) ? 'checked' : '' }}>
                    
                    <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-green-500
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:h-5 after:w-5 after:rounded-full after:transition-all
                                peer-checked:after:translate-x-full"></div>

                    <span id="statusText" class="text-gray-700">
                        {{ $type->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </label>
            </div>

            {{-- Divider --}}
            <div class="mt-8 pt-6 border-t border-gray-200"></div>

            {{-- Tombol Submit --}}
            <div class="flex justify-end">
                <button type="submit"
                    class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 hover:shadow-indigo-500/50 transition duration-300">
                    <span>Perbarui</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
