@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="bg-white shadow-lg rounded-xl p-4 md:p-8 min-h-[calc(100vh-6rem)]">

    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        Dashboard Admin
    </h1>

    {{-- Statistik Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">

        {{-- Sedang Dikerjakan --}}
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Sedang Dikerjakan</p>
                <div class="p-2 bg-blue-100 text-blue-600 rounded-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 mt-2">
                {{ number_format($ordersInProgress) }}
            </p>
        </div>

        {{-- Perlu Diverifikasi --}}
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Perlu Diverifikasi</p>
                <div class="p-2 bg-red-100 text-red-600 rounded-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 mt-2">
                {{ number_format($pendingPayments) }}
            </p>
        </div>

        {{-- Mendekati Deadline --}}
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Deadline &lt; 1 Hari</p>
                <div class="p-2 bg-orange-100 text-orange-600 rounded-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 mt-2">
                {{ number_format($nearDeadline) }}
            </p>
        </div>

        {{-- Proses Revisi --}}
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Proses Revisi</p>
                <div class="p-2 bg-purple-100 text-purple-600 rounded-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 mt-2">
                {{ number_format($ordersRevisi) }}
            </p>
        </div>

        {{-- Total Jenis Desain --}}
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">Total Jenis Desain</p>
                <div class="p-2 bg-green-100 text-green-600 rounded-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414
                                a1 1 0 00-.293-.707l-5.414-5.414
                                A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 mt-2">
                {{ number_format($totalDesignTypes) }}
            </p>
        </div>

    </div>
</div>
@endsection
