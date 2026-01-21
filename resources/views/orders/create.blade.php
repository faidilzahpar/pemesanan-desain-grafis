@extends('layouts.main')

@section('title', 'Buat Pesanan')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-20"
    x-data="{
        designs: @js($designTypes),
        selectedId: null,
        selected: null,
        selectedPayment: '',
        payments: @js($paymentMethods),
        file: null,
        preview: null
    }"
    x-init="
        selectedId = @js($selectedDesignId);
        if (selectedId) {
            selected = designs.find(d => d.design_type_id == selectedId);
        }
    "
    >

    <h1 class="text-3xl font-extrabold text-slate-900 mb-10">
        Buat Pesanan
    </h1>

    <form
        action="{{ route('orders.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-8"
    >
        @csrf
        <input type="hidden" name="design_type_id" :value="selectedId">
        {{-- PILIH JENIS DESAIN --}}
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">
                Jenis Desain
            </label>

            <select
                x-model="selectedId"
                @change="selected = designs.find(d => d.design_type_id == selectedId)"
                class="w-full border border-slate-300 rounded-xl px-4 py-3"
            >
                <option value="" disabled>
                    Pilih jenis desain
                </option>

                @foreach($designTypes as $type)
                    <option value="{{ $type->design_type_id }}">
                        {{ $type->nama_jenis }} - Rp {{ number_format($type->harga, 0, ',', '.') }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- INFO DESAIN TERPILIH --}}
        <template x-if="selected">
            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-5 space-y-2 text-sm">
                <p>
                    <span class="font-semibold">Jenis Desain:</span>
                    <span x-text="selected.nama_jenis"></span>
                </p>
                <p>
                    <span class="font-semibold">Harga:</span>
                    Rp <span x-text="Number(selected.harga).toLocaleString('id-ID')"></span>
                </p>
                <p>
                    <span class="font-semibold">Durasi:</span>
                    <span x-text="selected.durasi"></span> hari
                </p>
            </div>
        </template>

        {{-- DESKRIPSI --}}
        <textarea
            name="deskripsi"
            rows="5"
            class="w-full border border-slate-300 rounded-xl px-4 py-3"
            placeholder="Jelaskan kebutuhan desain Anda..."
            required></textarea>

        {{-- UPLOAD REFERENSI DESAIN --}}
        <div class="border border-dashed border-indigo-300 rounded-xl p-6 bg-indigo-50/40"
            x-data="{ 
                file: null, 
                preview: null, 
                error: null,
                validateFile(event) {
                    const selected = event.target.files[0];
                    
                    // Jika batal pilih file
                    if (!selected) return;

                    // Cek Ukuran 
                    if (selected.size > 10485760) {
                        this.error = 'Ukuran file terlalu besar! Maksimal 10MB.';
                        this.file = null;
                        this.preview = null;
                        event.target.value = ''; // Reset input
                        return;
                    }

                    // Cek Tipe File
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
                    if (!validTypes.includes(selected.type)) {
                        this.error = 'Format file tidak didukung! Hanya JPG, PNG, atau PDF.';
                        this.file = null;
                        this.preview = null;
                        event.target.value = ''; 
                        return;
                    }

                    // Jika Lolos Validasi
                    this.error = null;
                    this.file = selected;

                    // Buat Preview hanya jika Gambar
                    if (selected.type.startsWith('image/')) {
                        this.preview = URL.createObjectURL(selected);
                    } else {
                        this.preview = null; // PDF tidak perlu preview gambar
                    }
                }
            }">

            {{-- Hidden Input --}}
            <input type="file"
                name="referensi_desain"
                x-ref="fileInput"
                accept=".jpg,.jpeg,.png,.pdf"
                class="hidden"
                @change="validateFile($event)">

            {{-- Dropzone Area --}}
            <div @click="$refs.fileInput.click()"
                class="cursor-pointer bg-white rounded-xl p-6 text-center hover:bg-indigo-50 transition relative">

                {{-- TAMPILAN AWAL (Belum ada file) --}}
                <template x-if="!file">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="mx-auto h-10 w-10 text-indigo-500 mb-3"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M12 3v13.5m0 0l4.5-4.5M12 16.5l-4.5-4.5"/>
                        </svg>
                        <p class="font-semibold text-slate-700">
                            Klik untuk upload referensi desain
                        </p>
                        <p class="text-xs text-slate-500 mt-1">
                            JPG, PNG, atau PDF (Maks. 10MB)
                        </p>
                    </div>
                </template>

                {{-- TAMPILAN SETELAH UPLOAD (File Valid) --}}
                <template x-if="file">
                    <div class="flex flex-col items-center space-y-3">
                        
                        {{-- Preview Gambar --}}
                        <template x-if="preview">
                            <img :src="preview" class="max-h-40 rounded-lg border shadow object-contain">
                        </template>

                        {{-- Ikon PDF (Jika yang diupload PDF) --}}
                        <template x-if="!preview && file.type === 'application/pdf'">
                            <div class="h-20 w-20 bg-red-100 text-red-500 rounded-lg flex items-center justify-center">
                                <span class="font-bold text-xl">PDF</span>
                            </div>
                        </template>

                        <div class="text-center">
                            <p class="text-sm font-semibold text-slate-800" x-text="file.name"></p>
                            <p class="text-xs text-slate-500" x-text="(file.size / 1024 / 1024).toFixed(2) + ' MB'"></p>
                            <p class="text-xs text-indigo-600 mt-1 font-medium">Klik untuk ganti file</p>
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

        {{-- Metode Pembayaran --}}
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">
                Metode Pembayaran
            </label>

            <select
                name="payment_method_id"
                x-model="selectedPayment"
                class="w-full border border-slate-300 rounded-xl px-4 py-3
                    focus:ring focus:ring-indigo-200 focus:border-indigo-500"
                required
            >
                <option value="" disabled>
                    Pilih metode pembayaran
                </option>

                @foreach($paymentMethods as $method)
                    <option value="{{ $method->payment_method_id }}">
                        {{ $method->nama_metode }}
                    </option>
                @endforeach
            </select>

            {{-- INFO METODE TERPILIH --}}
            <template x-if="selectedPayment">
                <div class="mt-4 bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm">
                    <template
                        x-for="pm in payments.filter(p => p.payment_method_id === selectedPayment)"
                        :key="pm.payment_method_id"
                    >
                        <div class="space-y-1">
                            <p class="font-semibold text-slate-800" x-text="pm.nama_metode"></p>

                            <template x-if="pm.nomor_akun">
                                <p>
                                    Nomor:
                                    <span class="font-medium" x-text="pm.nomor_akun"></span>
                                </p>
                            </template>

                            <template x-if="pm.atas_nama">
                                <p>
                                    Atas Nama:
                                    <span class="font-medium" x-text="pm.atas_nama"></span>
                                </p>
                            </template>

                            <template x-if="pm.qr_path">
                                <p class="text-xs text-slate-500 italic">
                                    QR code akan ditampilkan di halaman invoice
                                </p>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        {{-- SUBMIT --}}
        <button
            type="submit"
            :disabled="!selectedId || !selectedPayment"
            class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold
                disabled:opacity-60 disabled:cursor-not-allowed
                hover:bg-indigo-600 transition"
        >
            Buat Pesanan
        </button>
    </form>
</div>
@endsection
