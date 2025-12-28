<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - CreativeDesign</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900">
        <div class="min-h-screen bg-slate-50">
            {{-- Navigasi Utama --}}
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white border-b border-slate-100 shadow-sm">
                    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                            {{ $header }}
                        </h1>
                    </div>
                </header>
            @endisset

            <main class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
            
            {{-- Footer Sederhana Dashboard --}}
            <footer class="py-8 text-center text-sm text-slate-400">
                <p>&copy; {{ date('Y') }} CreativeDesign Studio. Semua data layanan desain aman tersimpan.</p>
            </footer>
        </div>
    </body>
</html>