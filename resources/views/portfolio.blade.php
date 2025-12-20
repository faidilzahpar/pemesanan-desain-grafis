<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portofolio Kami - CreativeDesign</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,600,700" rel="stylesheet" />
</head>
<body class="bg-slate-50 font-['Plus_Jakarta_Sans']">

    <nav class="bg-white border-b border-slate-200 py-6">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
            <a href="/" class="text-xl font-bold text-indigo-600">← Kembali ke Utama</a>
            <h1 class="text-2xl font-extrabold text-slate-900">Portofolio Desain</h1>
        </div>
    </nav>

    <div class="py-16 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                @forelse($portofolios as $item)
                    <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-xl transition duration-300">
                        <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->judul }}" class="w-full h-64 object-cover">
                        
                        <div class="p-6">
                            <span class="text-xs font-bold uppercase tracking-widest text-indigo-500">{{ $item->kategori }}</span>
                            <h3 class="text-xl font-bold text-slate-900 mt-2">{{ $item->judul }}</h3>
                            <p class="text-slate-500 mt-2 text-sm leading-relaxed">{{ $item->deskripsi }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-20">
                        <p class="text-slate-400 italic font-medium">Belum ada karya yang diunggah ke tabel portofolio.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</body>
</html>