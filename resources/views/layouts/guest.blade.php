<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-10 pb-10 bg-slate-50/50">
            <div class="transition-transform hover:scale-105 duration-300 mb-10">
                <a href="/">
                    <span class="text-3xl font-extrabold tracking-tight text-slate-900">
                        Creative<span class="text-indigo-600">Design</span>
                    </span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-0 px-8 py-10 bg-white shadow-2xl shadow-slate-200/50 rounded-[2.5rem] border border-slate-100 mx-4">
                {{ $slot }}
            </div>

            <p class="mt-12 text-slate-400 text-xs font-medium">
                &copy; {{ date('Y') }} CreativeDesign Studio. All rights reserved.
            </p>
        </div>
    </body>
</html>