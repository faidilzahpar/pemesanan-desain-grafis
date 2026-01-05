@extends('layouts.admin')

@section('title', 'Detail Pesanan')

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        Detail Pesanan
        <span class="text-blue-600">{{ $order->order_id }}</span>
    </h1>

    <a href="{{ route('admin.orders.index') }}"
       class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition">
        Kembali
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ================= LEFT COLUMN ================= --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Informasi Pesanan --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-bold text-gray-800 flex items-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Informasi Pesanan
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Jenis Desain</p>
                    <p class="font-medium">{{ $order->designType->nama_jenis }}</p>
                </div>

                <div>
                    <p class="text-gray-500">Status Pesanan</p>
                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                        {{ $order->status_pesanan }}
                    </span>
                </div>

                <div>
                    <p class="text-gray-500">Metode Pembayaran</p>
                    <p class="font-medium">{{ $order->metode_pembayaran ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-gray-500">Tanggal Pesan</p>
                    <p class="font-medium">{{ $order->created_at->translatedFormat('j F Y, H:i') }}</p>
                </div>

                <div>
                    <p class="text-gray-500">Harga</p>
                    <p class="font-medium">Rp {{ number_format($order->designType->harga, 0, ',', '.') }}</p>
                </div>
                @if($order->deadline)
                <div>
                    <p class="text-gray-500">Deadline</p>
                    <p class="font-medium">{{ $order->deadline->translatedFormat('j F Y, H:i') }}</p>
                </div>
                @endif
            </div>

            <div class="mt-4">
                <p class="text-gray-500 mb-1">Deskripsi Pesanan</p>
                <p>{{ $order->deskripsi }}</p>
            </div>

            {{-- Referensi Desain --}}
            <div class="mt-4" x-data="{ open: false }">
                <p class="text-gray-500 mb-2">Referensi Desain</p>

                @if($order->referensi_desain)
                    {{-- Thumbnail --}}
                    <div class="relative group w-full md:w-64">
                        <img src="{{ asset('storage/' . $order->referensi_desain) }}"
                                alt="Referensi Desain"
                                @click="open = true"
                                class="rounded-xl border-2 border-gray-100 shadow-sm cursor-zoom-in group-hover:opacity-90 transition object-cover h-40 w-full">
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition pointer-events-none">
                            <span class="bg-black/50 text-white px-3 py-1 rounded-full text-xs">Klik untuk Perbesar</span>
                        </div>
                    </div>

                    {{-- Modal Preview --}}
                    <div x-show="open" x-cloak x-transition.opacity
                            class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/95 backdrop-blur-sm p-4"
                            @click.self="open = false">
                        <button @click="open = false" class="absolute top-5 right-5 text-white/70 hover:text-white transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <img src="{{ asset('storage/' . $order->referensi_desain) }}" class="max-w-full max-h-[85vh] rounded-lg shadow-2xl border border-white/20">
                    </div>
                @else
                    <div class="p-4 border border-dashed border-gray-200 rounded-xl text-gray-400 text-sm italic">
                        Tidak ada referensi gambar yang dilampirkan.
                    </div>
                @endif
            </div>
        </div>

        {{-- Riwayat File Desain --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6">

            {{-- Header --}}
            <h2 class="text-lg font-bold text-gray-800 mb-4">
                File Desain
            </h2>
            

            {{-- List File --}}
            <div class="p-6 space-y-3">
                @forelse($order->orderFiles as $file)
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition gap-4">

                        {{-- KIRI: Info File --}}
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-500"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>

                            <div>
                                <p class="font-bold text-gray-900">
                                    File {{ $file->tipe_file }}
                                    @if($file->tipe_file === 'Revisi')
                                        ({{ $loop->iteration - 1 }}) @endif
                                </p>
                                <p class="text-xs text-gray-500 uppercase tracking-widest">
                                    {{ $file->created_at->format('d M Y H:i') }}
                                </p>
                            </div>
                        </div>

                        {{-- KANAN: Group Tombol (Lihat & Download) --}}
                        <div x-data="{ open: false }" class="flex gap-2">
                            
                            {{-- 1. Tombol Lihat (Membuka Modal) --}}
                            <button type="button" 
                                    @click="open = true"
                                    class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg border border-indigo-200
                                        hover:bg-indigo-100 transition text-sm font-semibold flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Lihat
                            </button>

                            {{-- 2. MODAL PREVIEW --}}
                            <div x-show="open" 
                                x-cloak 
                                x-transition.opacity
                                style="display: none;"
                                class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/95 backdrop-blur-sm p-4"
                                @keydown.escape.window="open = false">
                                
                                {{-- Tombol Close (X) --}}
                                <button @click="open = false" class="absolute top-5 right-5 text-white/70 hover:text-white transition cursor-pointer z-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                {{-- Klik Background untuk Tutup --}}
                                <div @click="open = false" class="absolute inset-0 z-0"></div>

                                {{-- Gambar Preview --}}
                                <img src="{{ asset('storage/' . $file->path_file) }}" 
                                    class="relative z-10 max-w-full max-h-[85vh] rounded-lg shadow-2xl border border-white/20 bg-white object-contain">
                            </div>

                            {{-- 3. Tombol Download --}}
                            {{-- Pastikan route 'orders.file.download' bisa diakses Admin juga, atau buat route khusus admin --}}
                            <a href="{{ route('orders.file.download', $file->file_id) }}" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg
                                    transition text-sm font-semibold flex items-center gap-2 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download
                            </a>
                        </div>

                    </div>
                @empty
                    <div class="text-center py-6">
                        <p class="text-gray-400 italic">
                            Belum ada file desain.
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Upload File Desain --}}
            @php
                $allowedStatus = ['Sedang Dikerjakan', 'Revisi', 'Menunggu File Final'];
                $revisiCount = $order->orderFiles()->where('tipe_file', 'Revisi')->count();
                $hasFinal = $order->orderFiles()->where('tipe_file', 'Final')->exists();
            @endphp

            @if(in_array($order->status_pesanan, $allowedStatus) && !$hasFinal)
            <div class="border-t border-gray-100 bg-blue-50/50 p-6"
                x-data="{ file: null, preview: null }">

                <form action="{{ route('admin.orders.upload', $order->order_id) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="space-y-4">
                    @csrf

                    {{-- Hidden Input --}}
                    <input
                        type="file"
                        name="file"
                        x-ref="fileInput"
                        @change="
                            file = $event.target.files[0];
                            if (file && file.type && file.type.startsWith('image/')) {
                                preview = URL.createObjectURL(file);
                            } else {
                                preview = null;
                            }
                        "
                        class="hidden"
                        required
                    >

                    {{-- Dropzone --}}
                    <div
                        @click="$refs.fileInput.click()"
                        class="cursor-pointer border-2 border-dashed border-blue-300
                            rounded-xl p-6 text-center bg-white
                            hover:bg-blue-50 transition">

                        <template x-if="!file">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" 
                                    class="mx-auto h-10 w-10 text-blue-500 mb-2" 
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" 
                                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                </svg>
                                <p class="text-sm font-semibold text-gray-700">
                                    Klik untuk memilih file
                                </p>
                            </div>
                        </template>

                        {{-- Preview / Info --}}
                        <template x-if="file">
                            <div class="flex flex-col items-center space-y-2">
                                <template x-if="preview">
                                    <img
                                        :src="preview"
                                        class="max-h-40 rounded-lg border shadow"
                                    >
                                </template>

                                <template x-if="!preview">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-12 w-12 text-gray-400"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12h6m-3-3v6"/>
                                    </svg>
                                </template>

                                <p class="text-sm font-semibold text-gray-800"
                                x-text="file.name"></p>
                                <p class="text-xs text-gray-500"
                                x-text="(file.size / 1024 / 1024).toFixed(2) + ' MB'"></p>
                            </div>
                        </template>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        :disabled="!file"
                        class="w-full px-4 py-2 rounded-lg font-semibold transition
                            text-white
                            bg-blue-600 hover:bg-blue-700
                            disabled:bg-gray-300 disabled:cursor-not-allowed">
                        Upload File
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    {{-- ================= RIGHT COLUMN ================= --}}
    <div class="space-y-6">

        {{-- Data Customer --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">
                Data Pelanggan
            </h2>

            <div class="text-sm space-y-2">
                <div>
                    <p class="text-gray-500">Nama</p>
                    <p class="font-medium">{{ $order->user->name }}</p>
                </div>

                <div>
                    <p class="text-gray-500">Email</p>
                    <p class="font-medium">{{ $order->user->email }}</p>
                </div>

                <div>
                    <p class="text-gray-500">No. HP</p>
                    <p class="font-medium">{{ $order->user->no_hp }}</p>
                </div>
            </div>
        </div>

        {{-- Informasi Pembayaran --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">
                Informasi Pembayaran
            </h2>

            <div class="space-y-4 text-sm">
                @foreach($order->invoices as $invoice)
                    <div class="border rounded-lg p-4">
                        <p class="font-semibold mb-1">
                            Invoice {{ $invoice->jenis_invoice }}
                        </p>

                        <p class="mb-2">
                            Status Pembayaran:
                            <span class="font-medium">
                                {{ $invoice->status_pembayaran }}
                            </span>
                        </p>

                        {{-- Tombol Invoice (SAMA, LABEL BEDA) --}}
                        <div class="mt-3">
                            <a
                                href="{{ route('invoices.show', $invoice->invoice_id) }}"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white
                                    rounded-lg transition text-sm w-full block text-center"
                                title="{{ $invoice->status_pembayaran === 'Menunggu Verifikasi'
                                    ? 'Verifikasi pembayaran invoice ini'
                                    : 'Lihat detail invoice'
                                }}"
                            >
                                {{ $invoice->status_pembayaran === 'Menunggu Verifikasi'
                                    ? 'Verifikasi Pembayaran'
                                    : 'Lihat Invoice'
                                }}
                            </a>

                            @if($invoice->bukti_path)
                                <p class="mt-2 text-xs text-gray-500 italic">
                                    Bukti pembayaran sudah diunggah oleh pelanggan.
                                </p>
                            @else
                                <p class="mt-2 text-xs text-gray-500 italic">
                                    Bukti pembayaran belum diunggah oleh pelanggan.
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
