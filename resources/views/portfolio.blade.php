@extends('layouts.main')

@section('title', 'Portofolio Kami')

@section('content')

<div class="py-16 px-6">
    <div class="max-w-7xl mx-auto">
        <div class="mb-12 text-center">
            <h2 class="text-3xl font-black text-slate-900">Karya Desain Kami</h2>
            <p class="text-slate-500 mt-2">Inspirasi kreatif untuk solusi visual bisnis Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            @forelse($portfolios as $item)
                {{-- WRAPPER CARD DENGAN ALPINE DATA --}}
                <div x-data="{ open: false }" 
                    class="bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-xl transition duration-300 flex flex-col h-full">
                    
                    {{-- 1. THUMBNAIL (Tinggi Tetap h-64 agar seragam) --}}
                    <div class="relative group h-64 w-full overflow-hidden cursor-zoom-in" 
                        @click="open = true">
                        
                        {{-- Gambar Thumbnail (Object Cover = Terpotong rapi agar memenuhi kotak) --}}
                        {{-- Pastikan pathnya sesuai folder Anda, misal: 'storage/portfolio/' atau 'storage/' saja --}}
                        <img src="{{ asset('storage/' . $item->gambar) }}" 
                            alt="{{ $item->judul }}" 
                            class="w-full h-full object-cover transition duration-500 group-hover:scale-105 group-hover:opacity-90">

                        {{-- Overlay "Klik untuk Perbesar" --}}
                        <div class="absolute inset-0 flex items-center justify-center
                                    opacity-0 group-hover:opacity-100
                                    transition duration-300 pointer-events-none bg-black/10">
                            <span class="bg-black/60 text-white px-4 py-2 rounded-full text-xs font-bold backdrop-blur-sm shadow-lg transform translate-y-2 group-hover:translate-y-0 transition">
                                <i class="fas fa-search-plus mr-1"></i> Perbesar
                            </span>
                        </div>
                    </div>
                    
                    {{-- 2. KONTEN TEKS --}}
                    <div class="p-6 flex-1 flex flex-col">
                        <span class="text-xs font-bold uppercase tracking-widest text-indigo-500 mb-2 block">
                            {{ $item->kategori }}
                        </span>
                        <h3 class="text-xl font-bold text-slate-900 mb-2 leading-tight">
                            {{ $item->judul }}
                        </h3>
                        <p class="text-slate-500 text-sm leading-relaxed line-clamp-4   ">
                            {{ $item->deskripsi }}
                        </p>
                    </div>

                    {{-- 3. MODAL POPUP (Gambar Full Size) --}}
                    <div x-show="open" 
                        style="display: none;" 
                        x-transition.opacity.duration.300ms
                        class="fixed inset-0 z-[999] flex items-center justify-center bg-gray-900/95 backdrop-blur-sm p-4 md:p-8"
                        @click.self="open = false">

                        {{-- WRAPPER KONTEN (Putih) --}}
                        <div class="bg-white w-full max-w-6xl rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row max-h-[90vh]"
                            @click.outside="open = false">

                            {{-- KOLOM 1: GAMBAR (Kiri / Atas) --}}
                            <div class="w-full md:w-2/3 bg-gray-200 flex items-center justify-center p-4 relative border-[12px] rounded-2xl border-white">
                                <img src="{{ asset('storage/' . $item->gambar) }}" 
                                    alt="{{ $item->judul }}" 
                                    class="max-w-full max-h-[40vh] md:max-h-[85vh] object-contain">
                                    
                                {{-- Tombol Close (Mobile Only - Pojok gambar) --}}
                                <button @click="open = false" 
                                        class="md:hidden absolute top-4 right-4 bg-black/50 text-white p-2 rounded-full hover:bg-black transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- KOLOM 2: DETAIL TEXT (Kanan / Bawah) --}}
                            <div class="w-full md:w-1/3 flex flex-col bg-white relative">
                                
                                {{-- Tombol Close (Desktop - Pojok kanan atas panel putih) --}}
                                <button @click="open = false" 
                                        class="hidden md:block absolute top-4 right-4 text-slate-400 hover:text-slate-800 transition z-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>

                                {{-- Isi Konten (Scrollable jika teks panjang) --}}
                                <div class="p-8 overflow-y-auto h-full">
                                    {{-- Judul --}}
                                    <h2 class="text-3xl font-extrabold text-slate-900 mb-6 leading-tight">
                                        {{ $item->judul }}
                                    </h2>

                                    {{-- Garis Pemisah --}}
                                    <div class="w-full h-1 bg-indigo-600 rounded mb-6"></div>

                                    {{-- Deskripsi Full --}}
                                    <div class="prose prose-slate text-slate-600 leading-relaxed text-sm md:text-base">
                                        {{-- nl2br agar enter/baris baru di textarea terbaca --}}
                                        {!! nl2br(e($item->deskripsi)) !!}
                                    </div>
                                </div>

                                {{-- Footer Card (Opsional: Tanggal Upload) --}}
                                <div class="p-6 border-t border-slate-100 bg-slate-50 mt-auto">
                                    <p class="text-xs text-slate-400 font-medium">
                                        Diunggah pada {{ $item->created_at->translatedFormat('d F Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-20">
                    <div class="bg-slate-50 rounded-3xl p-12 border-2 border-dashed border-slate-200">
                        <p class="text-slate-400 italic font-medium text-lg">
                            Belum ada karya yang diunggah.
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection