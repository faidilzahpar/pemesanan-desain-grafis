@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran')

@section('content')

<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
    <h1 class="text-3xl font-bold text-gray-800">
        Verifikasi Pembayaran
    </h1>

    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
        {{-- SEARCH FORM --}}
        <form method="GET" action="{{ route('admin.payments.index') }}" class="relative w-full sm:w-64">
            @if(request('tableSortColumn'))
                <input type="hidden" name="tableSortColumn" value="{{ request('tableSortColumn') }}">
                <input type="hidden" name="tableSortDirection" value="{{ request('tableSortDirection') }}">
            @endif

            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" 
                   name="tableSearch" 
                   value="{{ request('tableSearch') }}"
                   placeholder="Cari invoice/pesanan..." 
                   class="w-full py-2 pl-10 pr-4 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </form>
    </div>
</div>

@if($invoices->count() === 0)
    <p class="text-gray-500 text-center py-10">
        Tidak ada pembayaran yang perlu diverifikasi.
    </p>
@else
<div class="shadow-xl rounded-xl overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-500 whitespace-nowrap">
        <thead class="text-xs uppercase text-white bg-blue-600">
            <tr>
                @php
                    $sortCol = request('tableSortColumn');
                    $sortDir = request('tableSortDirection');
                    
                    // LOGIKA 3 TAHAP: ASC -> DESC -> DEFAULT (NULL)
                    $getSortUrl = function($col) use ($sortCol, $sortDir) {
                        
                        // 1. Jika kolom beda, atau belum ada sort, mulai dari ASC
                        if ($sortCol !== $col) {
                            return request()->fullUrlWithQuery(['tableSortColumn' => $col, 'tableSortDirection' => 'asc']);
                        }
                        
                        // 2. Jika kolom sama dan sedang ASC, ubah jadi DESC
                        if ($sortDir === 'asc') {
                            return request()->fullUrlWithQuery(['tableSortColumn' => $col, 'tableSortDirection' => 'desc']);
                        }
                        
                        // 3. Jika kolom sama dan sedang DESC, HAPUS param (kembali ke default)
                        if ($sortDir === 'desc') {
                            return request()->fullUrlWithQuery(['tableSortColumn' => null, 'tableSortDirection' => null]);
                        }
                    };
                    
                    // Fungsi icon panah (Tidak berubah)
                    $renderIcon = function($col) use ($sortCol, $sortDir) {
                        if ($sortCol !== $col) return null; // Tidak ada icon kalau default
                        
                        return $sortDir === 'asc' 
                            ? '<svg class="w-3 h-3 ml-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>'
                            : '<svg class="w-3 h-3 ml-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>';
                    };
                @endphp

                <th class="py-3 px-6 font-bold border-r border-blue-700 cursor-pointer hover:bg-blue-700 transition"
                    onclick="window.location='{{ $getSortUrl('order_id') }}'">
                    <div class="flex items-center">
                        ID Pesanan {!! $renderIcon('order_id') !!}
                    </div>
                </th>

                <th class="py-3 px-6 font-bold border-r border-blue-700 cursor-pointer hover:bg-blue-700 transition"
                    onclick="window.location='{{ $getSortUrl('jenis_invoice') }}'">
                    <div class="flex items-center">
                        Jenis Invoice {!! $renderIcon('jenis_invoice') !!}
                    </div>
                </th>

                <th class="py-3 px-6 font-bold border-r border-blue-700 cursor-pointer hover:bg-blue-700 transition"
                    onclick="window.location='{{ $getSortUrl('user_name') }}'">
                    <div class="flex items-center">
                        Customer {!! $renderIcon('user_name') !!}
                    </div>
                </th>

                <th class="py-3 px-6 font-bold border-r border-blue-700 cursor-pointer hover:bg-blue-700 transition"
                        onclick="window.location='{{ $getSortUrl('created_at') }}'">
                        <div class="flex items-center">
                            Tanggal {!! $renderIcon('created_at') !!}
                        </div>
                    </th>

                <th class="py-3 px-6 font-bold border-r border-blue-700 cursor-pointer hover:bg-blue-700 transition"
                    onclick="window.location='{{ $getSortUrl('jumlah_bayar') }}'">
                    <div class="flex items-center">
                        Jumlah {!! $renderIcon('jumlah_bayar') !!}
                    </div>
                </th>

                <th class="py-3 px-6 font-bold text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach($invoices as $invoice)
            <tr class="border-b border-gray-300 hover:bg-gray-100 transition">
                <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">
                    {{ $invoice->order->order_id }}
                </td>
                <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">
                    {{ $invoice->jenis_invoice }}
                </td>
                <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">
                    {{ $invoice->order->user->name }}
                </td>
                <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">
                    {{ $invoice->order->created_at->translatedFormat('d M Y') }}
                </td>
                <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">
                    Rp {{ number_format($invoice->jumlah_bayar, 0, ',', '.') }}
                </td>
                <td class="py-4 px-6 font-medium text-gray-900 text-center space-x-2">
                    <a href="{{ route('admin.orders.show', $invoice->order->order_id) }}"
                    class="px-3 py-1 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                        Detail
                    </a>

                    <a href="{{ route('invoices.show', $invoice->invoice_id) }}"
                    class="px-3 py-1 bg-gray-200 text-gray-800 text-sm font-semibold rounded-lg hover:bg-gray-300 transition">
                        Lihat Invoice
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- PAGINATION --}}
<div class="mt-6">
    {{ $invoices->links() }}
</div>
@endif
@endsection
