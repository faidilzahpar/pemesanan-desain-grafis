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
            {{-- 1. Mengubah nama variabel menjadi $portfolios agar sesuai dengan Controller --}}
            @forelse($portfolios as $item)
                <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-xl transition duration-300">
                    {{-- 2. Memperbaiki path asset ke 'storage/portfolios/' --}}
                    <img src="{{ asset('storage/portfolios/' . $item->gambar) }}" 
                         alt="{{ $item->judul }}" 
                         class="w-full h-64 object-cover">
                    
                    <div class="p-6">
                        <span class="text-xs font-bold uppercase tracking-widest text-indigo-500">{{ $item->kategori }}</span>
                        <h3 class="text-xl font-bold text-slate-900 mt-2">{{ $item->judul }}</h3>
                        <p class="text-slate-500 mt-2 text-sm leading-relaxed">{{ $item->deskripsi }}</p>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-20">
                    <div class="bg-slate-50 rounded-3xl p-12 border-2 border-dashed border-slate-200">
                        <p class="text-slate-400 italic font-medium text-lg">
                            Belum ada karya yang diunggah ke tabel portofolio.
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection