@extends('layouts.main')

@section('title', 'Buat Pesanan')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-20"
        x-data="{
        designs: @js($designTypes),
        selectedId: null,
        selected: null,
        selectedPayment: '',
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
            required>
        </textarea>

        {{-- UPLOAD REFERENSI DESAIN --}}
        <div class="border border-dashed border-indigo-300 rounded-xl p-6 bg-indigo-50/40"
             x-data>

            {{-- Hidden Input --}}
            <input type="file"
                   name="referensi_desain"
                   x-ref="fileInput"
                   class="hidden"
                   @change="
                        file = $event.target.files[0];
                        if (file && file.type.startsWith('image/')) {
                            preview = URL.createObjectURL(file);
                        } else {
                            preview = null;
                        }
                   ">

            {{-- Dropzone --}}
            <div @click="$refs.fileInput.click()"
                 class="cursor-pointer bg-white rounded-xl p-6 text-center
                        hover:bg-indigo-50 transition">

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
                            JPG, PNG, atau PDF
                        </p>
                    </div>
                </template>

                {{-- PREVIEW --}}
                <template x-if="file">
                    <div class="flex flex-col items-center space-y-2">
                        <template x-if="preview">
                            <img :src="preview"
                                 class="max-h-40 rounded-lg border shadow">
                        </template>

                        <p class="text-sm font-semibold" x-text="file.name"></p>
                        <p class="text-xs text-slate-500"
                           x-text="(file.size / 1024 / 1024).toFixed(2) + ' MB'"></p>
                    </div>
                </template>

            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">
                Metode Pembayaran
            </label>

            <select
                name="metode_pembayaran"
                x-model="selectedPayment"
                class="w-full border border-slate-300 rounded-xl px-4 py-3"
                required
            >
                <option value="" disabled selected>
                    Pilih metode pembayaran
                </option>

                @foreach($paymentMethods as $method)
                    <option value="{{ $method }}">{{ $method }}</option>
                @endforeach
            </select>
        </div>

        {{-- SUBMIT (BELUM AKTIF) --}}
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
