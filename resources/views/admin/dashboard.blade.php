@extends('layouts.admin')

@section('title', 'Dashboard')
@section('content')
<div class="bg-white shadow-lg rounded-xl p-4 md:p-8 min-h-[calc(100vh-6rem)]">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard Admin</h1>

    {{-- Statistik Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        {{-- Card: Total Pesanan Masuk --}}
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Total Pesanan Masuk</p>
                <div class="p-2 bg-blue-100 text-blue-600 rounded-full">
                    {{-- Heroicon --}}
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 mt-2">1,250</p>
        </div>

        {{-- Card: Pesanan Sedang Dikerjakan --}}
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Sedang Dikerjakan</p>
                <div class="p-2 bg-yellow-100 text-yellow-600 rounded-full">
                    {{-- Heroicon --}}
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 mt-2">125</p>
        </div>

        {{-- Card: Pembayaran Menunggu Verifikasi --}}
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Verifikasi Pembayaran</p>
                <div class="p-2 bg-red-100 text-red-600 rounded-full">
                    {{-- Heroicon --}}
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 mt-2">15</p>
        </div>

        {{-- Card: Total Jenis Desain --}}
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Total Jenis Desain</p>
                <div class="p-2 bg-green-100 text-green-600 rounded-full">
                    {{-- Heroicon --}}
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-2.414-2.414A1 1 0 0015.586 6H7a2 2 0 00-2 2v11a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 mt-2">28</p>
        </div>
    </div>
</div>
@endsection