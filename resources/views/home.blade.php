@extends('layouts.main')

@section('title', 'Jasa Desain Grafis Profesional')

@section('content')

<section id="home" class="pt-32 pb-20 px-6">
    <div class="max-w-7xl mx-auto text-center">
        <span class="bg-indigo-50 text-indigo-600 px-4 py-1.5 rounded-full
                     text-sm font-bold tracking-wide uppercase mb-6 inline-block">
            Solusi Visual No. 1
        </span>

        <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-8 leading-[1.1]">
            Ubah Ide Menjadi <br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r
                         from-indigo-600 to-violet-600">
                Desain Luar Biasa
            </span>
        </h1>

        <p class="text-lg text-slate-500 max-w-2xl mx-auto mb-10 leading-relaxed">
            Kami membantu bisnis Anda tampil lebih profesional dengan desain Logo,
            Branding, dan Media Sosial yang estetik serta pengerjaan cepat.
        </p>

       
            <a href="{{ route('portfolio') }}"
               class="bg-slate-900 text-white px-10 py-4 rounded-full
                      font-bold text-lg shadow-xl hover:shadow-2xl transition">
                Lihat Portofolio
            </a>
        
    </div>
</section>

{{-- SECTION LAYANAN --}}
<section id="layanan" class="py-24 px-6 bg-slate-50/50">
    <div class="max-w-7xl mx-auto">

        {{-- Heading --}}
        <div class="text-center mb-16">
            <span class="text-indigo-600 text-sm font-bold tracking-widest uppercase mb-3 block">
                Pilihan Layanan
            </span>
            <h2 class="text-4xl md:text-5xl font-extrabold text-slate-900 mb-4 tracking-tight">
                Solusi Desain <span class="text-indigo-600">Terbaik</span>
            </h2>
            <p class="text-slate-500 max-w-xl mx-auto text-lg">
                Kami menyediakan berbagai kategori desain untuk meningkatkan nilai visual brand dan bisnis Anda.
            </p>
        </div>

        {{-- LIST JENIS DESAIN --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($designTypes as $type)
                <div class="group relative bg-white p-8 rounded-[2rem] border border-slate-200 
                            hover:border-indigo-300 hover:shadow-2xl hover:shadow-indigo-100 
                            transition-all duration-300 transform hover:-translate-y-2 flex flex-col">
                    
                    {{-- Header Card --}}
                    <div class="flex justify-between items-start gap-4 mb-4">
                        <h3 class="text-2xl font-bold text-slate-900 group-hover:text-indigo-600 transition-colors leading-tight">
                            {{ $type->nama_jenis }}
                        </h3>
                        
                        <span class="shrink-0 inline-block bg-indigo-50 text-indigo-700 text-[10px] font-bold px-3 py-1.5 rounded-full uppercase tracking-wider border border-indigo-100">
                            {{ $type->durasi }} Hari
                        </span>
                    </div>

                    <p class="text-slate-500 leading-relaxed flex-grow">
                        {{ $type->deskripsi }}
                    </p>

                    {{-- Footer Card --}}
                    <div class="pt-3 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Harga</p>
                                <p class="text-2xl font-extrabold text-slate-900">
                                    <span class="text-sm font-medium mr-0.5">Rp</span>{{ number_format($type->harga, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        {{-- Tombol Order --}}
                        <button class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold 
                                       hover:bg-indigo-600 shadow-sm hover:shadow-indigo-200 
                                       transition-all duration-300 flex items-center justify-center gap-2 group/btn"
                                onclick="window.location.href='{{ route('register') }}'">
                            Pesan Sekarang
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover/btn:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
