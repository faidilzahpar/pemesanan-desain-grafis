<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CreativeDesign - Jasa Desain Grafis Profesional</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">

    <header class="fixed w-full z-50 glass border-b border-slate-200">
        <nav class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200">
                    <span class="text-white font-bold text-xl">D</span>
                </div>
                <span class="font-extrabold text-xl tracking-tight">Creative<span class="text-indigo-600">Design</span></span>
            </div>

            <div class="hidden md:flex items-center gap-8 font-medium text-slate-600">
                <a href="#layanan" class="hover:text-indigo-600 transition">Layanan</a>
                <a href="#harga" class="hover:text-indigo-600 transition">Harga</a>
            </div>

            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-semibold text-indigo-600">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-slate-600 font-medium hover:text-indigo-600 transition">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-5 py-2.5 rounded-full font-semibold shadow-md hover:bg-indigo-700 transition transform hover:scale-105 active:scale-95">Mulai Order</a>
                        @endif
                    @endauth
                @endif
            </div>
        </nav>
    </header>

    <section class="pt-40 pb-20 px-6">
        <div class="max-w-7xl mx-auto text-center">
            <span class="bg-indigo-50 text-indigo-600 px-4 py-1.5 rounded-full text-sm font-bold tracking-wide uppercase mb-6 inline-block">Solusi Visual No. 1</span>
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-slate-900 mb-8 leading-[1.1]">
                Ubah Ide Menjadi <br> <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-violet-600">Desain Luar Biasa</span>
            </h1>
            <p class="text-lg text-slate-500 max-w-2xl mx-auto mb-10 leading-relaxed">
                Kami membantu bisnis Anda tampil lebih profesional dengan desain Logo, Branding, dan Media Sosial yang estetik serta pengerjaan cepat.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('register') }}" class="bg-slate-900 text-white px-10 py-4 rounded-full font-bold text-lg hover:shadow-2xl transition shadow-xl">Konsultasi Gratis</a>
                <a href="portfolio" class="bg-white border border-slate-200 text-slate-900 px-10 py-4 rounded-full font-bold text-lg hover:bg-slate-50 transition">Lihat Portofolio</a>
            </div>
        </div>
    </section>

    <section id="layanan" class="py-24 px-6 bg-white border-y border-slate-100">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold mb-4">Layanan Unggulan Kami</h2>
                <p class="text-slate-500">Semua kebutuhan desain Anda dalam satu tempat.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="group p-8 rounded-3xl border border-slate-100 hover:border-indigo-100 hover:bg-indigo-50/30 transition">
                    <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition">💎</div>
                    <h3 class="text-xl font-bold mb-3">Branding & Logo</h3>
                    <p class="text-slate-500 leading-relaxed">Membangun identitas brand yang kuat dan ikonik untuk bisnis Anda.</p>
                </div>

                <div class="group p-8 rounded-3xl border border-slate-100 hover:border-indigo-100 hover:bg-indigo-50/30 transition">
                    <div class="w-14 h-14 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition">📸</div>
                    <h3 class="text-xl font-bold mb-3">Social Media Design</h3>
                    <p class="text-slate-500 leading-relaxed">Konten Instagram dan TikTok yang estetik untuk meningkatkan engagement.</p>
                </div>

                <div class="group p-8 rounded-3xl border border-slate-100 hover:border-indigo-100 hover:bg-indigo-50/30 transition">
                    <div class="w-14 h-14 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition">🚀</div>
                    <h3 class="text-xl font-bold mb-3">Marketing Kit</h3>
                    <p class="text-slate-500 leading-relaxed">Desain banner, poster, dan brosur untuk promosi yang lebih maksimal.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-12 px-6 text-center text-slate-400 border-t border-slate-100">
        <p class="text-sm uppercase font-bold tracking-widest mb-4">CreativeDesign Studio</p>
        <p>&copy; {{ date('Y') }} Projek Pemesanan Desain Grafis. All rights reserved.</p>
    </footer>

</body>
</html>