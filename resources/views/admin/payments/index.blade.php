@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran')

@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-6">
    Verifikasi Pembayaran
</h1>

@if($invoices->count() === 0)
    <p class="text-gray-500 text-center py-10 italic">
        Tidak ada pembayaran yang perlu diverifikasi.
    </p>
@else
<div class="shadow-xl rounded-xl overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-500 whitespace-nowrap">
        <thead class="text-xs uppercase text-white bg-blue-600">
            <tr>
                <th class="py-3 px-6 font-bold border-r border-blue-700">ID Pesanan</th>
                <th class="py-3 px-6 font-bold border-r border-blue-700">Jenis Invoice</th>
                <th class="py-3 px-6 font-bold border-r border-blue-700">Customer</th>
                <th class="py-3 px-6 font-bold border-r border-blue-700">Jumlah</th>
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
