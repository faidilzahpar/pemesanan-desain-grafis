<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Halaman Admin - @yield('title')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .sidebar-transition {
            transition: transform 0.3s ease-out;
        }
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div x-data="{ sidebarOpen: false }">

        @include('admin.partials.navigation')

        <main class="p-4 md:p-6 transition-all duration-300":class="'md:ml-64'">      
                @yield('content')
        </main>
    </div>
</body>
</html>
