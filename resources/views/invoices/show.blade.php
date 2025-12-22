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
            Invoice {{ $invoice->invoice_id }}
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
            <span class="text-blue-600">
                Rp {{ number_format($invoice->jumlah_bayar, 0, ',', '.') }}
            </span>
        </div>
    </div>

    {{-- BUKTI PEMBAYARAN --}}
    <div class="bg-white rounded-xl shadow p-5"
        x-data="{ open: false }">

        <h2 class="font-semibold text-gray-700 mb-4 text-center">
            Bukti Pembayaran
        </h2>

        {{-- TAMPILKAN BUKTI (JIKA ADA) --}}
        @if($invoice->bukti_path)
            <div class="flex flex-col items-center mb-6">

                {{-- Thumbnail --}}
                <div class="relative group w-full max-w-xs">
                    <img src="{{ asset('storage/' . $invoice->bukti_path) }}"
                        alt="Bukti Pembayaran"
                        @click="open = true"
                        class="rounded-xl border-2 border-gray-100 shadow-sm
                                cursor-zoom-in group-hover:opacity-90
                                transition object-cover h-40 w-full">

                    <div class="absolute inset-0 flex items-center justify-center
                                opacity-0 group-hover:opacity-100
                                transition pointer-events-none">
                        <span class="bg-black/50 text-white px-3 py-1 rounded-full text-xs">
                            Klik untuk Perbesar
                        </span>
                    </div>
                </div>

                <button
                    @click="open = true"
                    class="mt-4 px-4 py-2 bg-blue-600 text-white
                        rounded-lg hover:bg-blue-700
                        transition text-sm font-semibold">
                    Lihat Bukti Pembayaran
                </button>
            </div>

            {{-- MODAL --}}
            <div x-show="open" x-cloak x-transition.opacity
                class="fixed inset-0 z-[100] flex items-center justify-center
                        bg-gray-900/95 backdrop-blur-sm p-4"
                @click.self="open = false">

                <button @click="open = false"
                        class="absolute top-5 right-5
                            text-white/70 hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-10 w-10"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <img src="{{ asset('storage/' . $invoice->bukti_path) }}"
                    class="max-w-full max-h-[85vh] rounded-lg
                            shadow-2xl border border-white/20 bg-white">

                {{-- AKSI ADMIN --}}
                @if(
                    $invoice->status_pembayaran === 'Menunggu Verifikasi'
                    && auth()->check()
                    && auth()->user()->is_admin == 1
                )
                    <div class="mt-6 flex space-x-4">
                        <form method="POST"
                            action="{{ route('invoices.verify', $invoice->invoice_id) }}">
                            @csrf
                            <button type="submit"
                                    class="px-6 py-2 bg-green-600 text-white
                                        rounded-lg hover:bg-green-700
                                        font-semibold transition">
                                Terima
                            </button>
                        </form>

                        <form method="POST"
                            action="{{ route('invoices.reject', $invoice->invoice_id) }}">
                            @csrf
                            <button type="submit"
                                    class="px-6 py-2 bg-red-600 text-white
                                        rounded-lg hover:bg-red-700
                                        font-semibold transition">
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
            && $invoice->status_pembayaran !== 'Pembayaran Diterima'
        )

            <form
                action="{{ route('invoices.upload', $invoice->invoice_id) }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-4"
                x-data="{ file: null, preview: null }"
            >
                @csrf

                <div class="border border-dashed border-indigo-300 rounded-xl p-6 bg-indigo-50/40">
                    <input
                        type="file"
                        name="bukti_pembayaran"
                        x-ref="fileInput"
                        accept="image/*,.pdf"
                        class="hidden"
                        required
                        @change="
                            file = $event.target.files[0];
                            preview = file && file.type.startsWith('image/')
                                ? URL.createObjectURL(file)
                                : null;
                        "
                    >

                    <div
                        @click="$refs.fileInput.click()"
                        class="cursor-pointer bg-white rounded-xl p-6 text-center
                            hover:bg-indigo-50 transition"
                    >
                        <template x-if="!file">
                            <div>
                                <p class="font-semibold text-slate-700">
                                    Klik untuk upload / ganti bukti pembayaran
                                </p>
                                <p class="text-xs text-slate-500 mt-1">
                                    JPG, PNG, atau PDF (maks. 10MB)
                                </p>
                            </div>
                        </template>

                        <template x-if="file">
                            <div class="flex flex-col items-center space-y-2">
                                <img x-show="preview"
                                    :src="preview"
                                    class="max-h-40 rounded-lg border shadow">
                                <p class="text-sm font-semibold" x-text="file.name"></p>
                                <p class="text-xs text-slate-500"
                                    x-text="(file.size / 1024 / 1024).toFixed(2) + ' MB'"></p>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="text-center">
                    <button
                        type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg
                            hover:bg-indigo-700 transition font-semibold"
                    >
                        Upload Bukti Pembayaran
                    </button>
                </div>
            </form>

        @elseif($isExpired)
            <p class="text-red-600 italic text-center">
                Invoice telah expired. Silakan buat pesanan baru.
            </p>
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
