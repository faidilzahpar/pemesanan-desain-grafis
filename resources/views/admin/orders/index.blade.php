@extends('layouts.admin')

@section('title', 'Pesanan Sedang Dikerjakan')

@section('content')
<h1 class="text-3xl font-bold text-gray-800 mb-6">
    Pesanan Sedang Dikerjakan
</h1>

@if ($orders->count() === 0)

    <p class="text-gray-500 text-center py-10">
        Tidak ada pesanan yang sedang dikerjakan.
    </p>

@else
    <div class="shadow-xl rounded-xl overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 whitespace-nowrap">
            <thead class="text-xs uppercase text-white bg-blue-600">
                <tr>
                    <th class="py-3 px-6 font-bold border-r border-blue-700">ID Pesanan</th>
                    <th class="py-3 px-6 font-bold border-r border-blue-700">Customer</th>
                    <th class="py-3 px-6 font-bold border-r border-blue-700">No HP</th>
                    <th class="py-3 px-6 font-bold border-r border-blue-700">Jenis Desain</th>
                    <th class="py-3 px-6 font-bold border-r border-blue-700">Status</th>
                    <th class="py-3 px-6 font-bold text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach($orders as $order)
                <tr class="border-b border-gray-300 hover:bg-gray-100 transition">
                    <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">
                        {{ $order->order_id }}
                    </td>
                    <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">
                        {{ $order->user->name }}
                    </td>
                    <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">
                        {{ $order->user->no_hp }}
                    </td>
                    <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">
                        {{ $order->designType->nama_jenis }}
                    </td>
                    <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">
                        {{ $order->status_pesanan }}
                    </td>
                    <td class="py-4 px-6 font-medium text-gray-900 text-center">
                        <a href="{{ route('admin.orders.show', $order->order_id) }}"
                           class="px-3 py-1 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                            Detail
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    {{-- PAGINATION --}}
    <div class="mt-6">
        {{ $orders->links() }}
    </div>
@endif
@endsection
