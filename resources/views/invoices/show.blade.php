@php
    $layout = auth()->check() && auth()->user()->is_admin == 1
        ? 'layouts.admin'
        : 'layouts.main';
@endphp

@extends($layout)

@section('title', 'Invoice')


@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">
            #{{ $invoice->invoice_id }}
        </h1>

        @if($isExpired)
            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">
                Pembayaran Expired
            </span>
        @else
            <span class="px-3 py-1 text-sm rounded-full
                {{ $invoice->status_pembayaran === 'Menunggu Verifikasi'
                    ? 'bg-orange-100 text-orange-700'
                    : 'bg-green-100 text-green-700'
                }}">
                {{ $invoice->status_pembayaran }}
            </span>
        @endif
    </div>

    {{-- INFO CUSTOMER & ORDER --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-3">
                Informasi Pelanggan
            </h2>
            <p><span class="text-gray-500">Nama:</span> {{ $invoice->order->user->name }}</p>
            <p><span class="text-gray-500">No HP:</span> {{ $invoice->order->user->no_hp }}</p>
            <p><span class="text-gray-500">ID Pesanan:</span> {{ $invoice->order->order_id }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-3">
                Informasi Invoice
            </h2>

            <p>
                <span class="text-gray-500">Jenis Invoice:</span>
                {{ $invoice->jenis_invoice }}
            </p>

            <p>
                <span class="text-gray-500">Tanggal:</span>
                {{ $invoice->created_at->translatedFormat('d F Y, H:i') }}
            </p>

            {{-- DEADLINE / TANGGAL BAYAR --}}
            <p>
                @php
                    $isWaitingPayment = in_array(
                        $invoice->order->status_pesanan,
                        ['Menunggu DP', 'Menunggu Pelunasan']
                    );
                @endphp

                <span class="text-gray-500">
                    {{ $isWaitingPayment ? 'Batas Pembayaran:' : 'Tanggal Bayar:' }}
                </span>

                <span class="{{ $isExpired ? 'text-red-600 font-semibold' : 'text-gray-800' }}">
                    @if($isWaitingPayment)
                        {{ $paymentDeadline->translatedFormat('d F Y, H:i') }}
                    @else
                        {{ \Carbon\Carbon::parse($invoice->tgl_bayar)->translatedFormat('d F Y, H:i') }}
                    @endif
                </span>
            </p>
        </div>
    </div>

    {{-- RINCIAN PEMBAYARAN --}}
    <div class="bg-white rounded-xl shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-4">
            Rincian Pembayaran
        </h2>

        <div class="flex justify-between border-b pb-2 mb-2">
            <span>Jenis Desain</span>
            <span>{{ $invoice->order->designType->nama_jenis }}</span>
        </div>

        <div class="flex justify-between border-b pb-2 mb-2">
            <span>Harga</span>
            <span>
                Rp {{ number_format($invoice->jumlah_bayar * 2, 0, ',', '.') }}
            </span>
        </div>

        <div class="flex justify-between font-bold text-lg">
            <span>Total Dibayar</span>
            <span class="text-indigo-600">
                Rp {{ number_format($invoice->jumlah_bayar, 0, ',', '.') }}
            </span>
        </div>
    </div>

    {{-- METODE PEMBAYARAN --}}
    @if($invoice->order->paymentMethod)
    <div class="bg-white rounded-xl shadow p-5 space-y-4">
        <h2 class="font-semibold text-gray-700">
            Metode Pembayaran
        </h2>

        <div class="text-sm text-gray-700 space-y-2">
            <p class="font-semibold text-slate-900">
                {{ $invoice->order->paymentMethod->nama_metode }}
            </p>

            {{-- NOMOR AKUN --}}
            @if($invoice->order->paymentMethod->nomor_akun)
                <p>
                    Nomor:
                    <span class="font-medium">
                        {{ $invoice->order->paymentMethod->nomor_akun }}
                    </span>
                </p>
            @endif

            {{-- ATAS NAMA --}}
            @if($invoice->order->paymentMethod->atas_nama)
                <p>
                    Atas Nama:
                    <span class="font-medium">
                        {{ $invoice->order->paymentMethod->atas_nama }}
                    </span>
                </p>
            @endif
        </div>

        {{-- QR CODE --}}
        @if($invoice->order->paymentMethod->qr_path)
            <div class="pt-3 text-center">
                <p class="text-xs text-slate-500 mb-2">
                    Scan QR berikut untuk melakukan pembayaran
                </p>

                <img
                    src="{{ asset('storage/' . $invoice->order->paymentMethod->qr_path) }}"
                    alt="QR Pembayaran"
                    class="mx-auto max-w-[220px] rounded-xl border shadow"
                >
            </div>
        @endif
    </div>
    @endif

    {{-- BUKTI PEMBAYARAN --}}
    <div class="bg-white rounded-xl shadow p-5" x-data="{ open: false }">

        <h2 class="font-semibold text-gray-700 mb-4 text-center">
            Bukti Pembayaran
        </h2>

        {{-- TAMPILKAN BUKTI (JIKA ADA) --}}
        @if($invoice->bukti_path)
            @php
                $isPdf = str_ends_with(strtolower($invoice->bukti_path), '.pdf');
            @endphp

            <div class="flex flex-col items-center mb-6">

                {{-- THUMBNAIL AREA --}}
                <div class="relative group w-full max-w-xs cursor-pointer" @click="open = true">
                    
                    @if($isPdf)
                        {{-- TAMPILAN PDF (ICON) --}}
                        <div class="flex flex-col items-center justify-center h-40 w-full rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 hover:bg-gray-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm font-semibold text-gray-600">File Dokumen (PDF)</span>
                        </div>
                    @else
                        {{-- TAMPILAN GAMBAR (THUMBNAIL) --}}
                        {{-- PERHATIKAN: src diganti jadi route() --}}
                        <img src="{{ route('invoices.file', $invoice->invoice_id) }}"
                            alt="Bukti Pembayaran"
                            class="rounded-xl border-2 border-gray-100 shadow-sm object-cover h-40 w-full group-hover:opacity-90 transition">
                    @endif

                    {{-- Hover Overlay (Sama untuk PDF/Gambar) --}}
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition pointer-events-none">
                        <span class="bg-black/50 text-white px-3 py-1 rounded-full text-xs">
                            Klik untuk Lihat Detail
                        </span>
                    </div>
                </div>

                <button @click="open = true"
                    class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-semibold">
                    Lihat Bukti Pembayaran
                </button>
            </div>

            {{-- MODAL --}}
            <div x-show="open" x-cloak x-transition.opacity
                class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-gray-900/95 backdrop-blur-sm p-4"
                @click.self="open = false">

                {{-- Tombol Close --}}
                <button @click="open = false" class="absolute top-5 right-5 text-white/70 hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                {{-- KONTEN MODAL (Logika PDF vs Gambar) --}}
                <div class="w-full max-w-4xl max-h-[80vh] flex justify-center overflow-auto rounded-lg shadow-2xl bg-white border border-white/20">
                    @if($isPdf)
                        {{-- IFRAME UNTUK PDF --}}
                        <iframe src="{{ route('invoices.file', $invoice->invoice_id) }}" 
                                class="w-full h-[70vh] md:h-[80vh]" 
                                frameborder="0">
                        </iframe>
                    @else
                        {{-- IMAGE UNTUK GAMBAR --}}
                        <img src="{{ route('invoices.file', $invoice->invoice_id) }}" 
                            class="max-w-full max-h-[80vh] object-contain">
                    @endif
                </div>

                {{-- AKSI ADMIN (Tetap muncul di bawah modal) --}}
                @if(
                    $invoice->status_pembayaran === 'Menunggu Verifikasi'
                    && auth()->check()
                    && auth()->user()->is_admin == 1
                )
                    <div class="mt-6 flex space-x-4 z-[101]">
                        <form method="POST" action="{{ route('invoices.verify', $invoice->invoice_id) }}">
                            @csrf
                            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold transition shadow-lg">
                                Terima
                            </button>
                        </form>

                        <form method="POST" action="{{ route('invoices.reject', $invoice->invoice_id) }}">
                            @csrf
                            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition shadow-lg">
                                Tolak
                            </button>
                        </form>
                    </div>
                @endif

            </div>
        @endif

        {{-- FORM UPLOAD / REPLACE BUKTI --}}
        @if(
        !$isExpired
        && auth()->check()
        && auth()->user()->is_admin == 0
        && in_array($invoice->order->status_pesanan, ['Menunggu DP', 'Menunggu Pelunasan'])
        && in_array($invoice->status_pembayaran, ['Belum Dibayar', 'Pembayaran Ditolak'])
    )
        <form
            action="{{ route('invoices.upload', $invoice->invoice_id) }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-4"
            x-data="{ 
                file: null, 
                preview: null, 
                error: null,
                validateFile(event) {
                    const selected = event.target.files[0];
                    
                    // 1. Jika batal pilih file
                    if (!selected) return;

                    // 2. Cek Ukuran (10MB)
                    if (selected.size > 10485760) {
                        this.error = 'Ukuran file terlalu besar! Maksimal 10MB.';
                        this.file = null;
                        this.preview = null;
                        event.target.value = ''; // Reset input
                        return;
                    }

                    // 3. Cek Tipe File
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
                    if (!validTypes.includes(selected.type)) {
                        this.error = 'Format file tidak didukung! Hanya JPG, PNG, atau PDF.';
                        this.file = null;
                        this.preview = null;
                        event.target.value = ''; // Reset input
                        return;
                    }

                    // 4. Jika Lolos Validasi
                    this.error = null;
                    this.file = selected;

                    // Buat Preview hanya jika Gambar
                    if (selected.type.startsWith('image/')) {
                        this.preview = URL.createObjectURL(selected);
                    } else {
                        this.preview = null; // PDF tidak perlu preview gambar
                    }
                }
            }"
        >
            @csrf

            {{-- AREA UPLOAD --}}
            <div class="border border-dashed border-indigo-300 rounded-xl p-6 bg-indigo-50/40">
                
                {{-- Hidden Input --}}
                <input
                    type="file"
                    name="bukti_pembayaran"
                    x-ref="fileInput"
                    accept=".jpg,.jpeg,.png,.pdf"
                    class="hidden"
                    required
                    @change="validateFile($event)"
                >

                {{-- Dropzone UI --}}
                <div
                    @click="$refs.fileInput.click()"
                    class="cursor-pointer bg-white rounded-xl p-6 text-center
                        hover:bg-indigo-50 transition relative"
                >
                    {{-- KONDISI 1: BELUM ADA FILE --}}
                    <template x-if="!file">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-indigo-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <p class="font-semibold text-slate-700">
                                Klik untuk upload bukti pembayaran
                            </p>
                            <p class="text-xs text-slate-500 mt-1">
                                JPG, PNG, atau PDF (maks. 10MB)
                            </p>
                        </div>
                    </template>

                    {{-- KONDISI 2: FILE SUDAH DIPILIH --}}
                    <template x-if="file">
                        <div class="flex flex-col items-center space-y-3">
                            
                            {{-- A. Preview Gambar --}}
                            <template x-if="preview">
                                <img :src="preview" class="max-h-40 rounded-lg border shadow object-contain">
                            </template>

                            {{-- B. Ikon PDF (Jika PDF) --}}
                            <template x-if="!preview && file.type === 'application/pdf'">
                                <div class="h-20 w-20 bg-red-100 text-red-500 rounded-lg flex items-center justify-center border border-red-200">
                                    <span class="font-bold text-xl">PDF</span>
                                </div>
                            </template>

                            {{-- Info File --}}
                            <div class="text-center">
                                <p class="text-sm font-semibold text-slate-800" x-text="file.name"></p>
                                <p class="text-xs text-slate-500" x-text="(file.size / 1024 / 1024).toFixed(2) + ' MB'"></p>
                                <p class="text-xs text-indigo-600 mt-1 font-medium hover:underline">Klik untuk ganti file</p>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- PESAN ERROR (Muncul jika file ditolak) --}}
                <template x-if="error">
                    <div class="mt-3 p-3 bg-red-50 border border-red-100 rounded-lg flex items-center gap-2 text-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium" x-text="error"></span>
                    </div>
                </template>
            </div>

            {{-- TOMBOL SUBMIT --}}
            <div class="text-center">
                <button
                    type="submit"
                    :disabled="!file"
                    :class="!file ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-700'"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg transition font-semibold w-full sm:w-auto"
                >
                    Upload Bukti Pembayaran
                </button>
            </div>
        </form>

    @elseif($isExpired)
        <div class="p-4 bg-red-50 text-red-700 rounded-xl text-center border border-red-100">
            <p class="font-medium">Invoice telah kadaluarsa.</p>
            <p class="text-sm">Silakan buat pesanan baru untuk melanjutkan.</p>
        </div>
    @endif
    </div>

    {{-- BACK --}}
    <div class="flex justify-end space-x-3">
        <a href="{{ url()->previous() }}"
           class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
            Kembali
        </a>
    </div>

</div>

@endsection
