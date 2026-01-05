@extends('layouts.admin')

@section('title', 'Riwayat Pesanan')

@section('content')
{{-- HEADER: Judul & Tombol Kembali --}}
<div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
    <h1 class="text-3xl font-bold text-gray-800">
        Riwayat Pesanan
    </h1>

    <a href="{{ route('admin.orders.index') }}" 
       class="px-5 py-2 bg-blue-600 text-white font-medium rounded-lg 
              hover:bg-blue-700 transition flex items-center gap-2 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Pesanan Aktif
    </a>
</div>

@if ($orders->count() === 0)

    <p class="text-gray-500 text-center py-10">
        Belum ada riwayat pesanan (Selesai / Dibatalkan).
    </p>

@else
    <div class="shadow-xl rounded-xl overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 whitespace-nowrap">
            <thead class="text-xs uppercase text-white bg-blue-600">
                <tr>
                    <th class="py-3 px-6 font-bold border-r border-blue-700">ID Pesanan</th>
                    <th class="py-3 px-6 font-bold border-r border-blue-700">Customer</th>
                    <th class="py-3 px-6 font-bold border-r border-blue-700">No HP</th>
                    <th class="py-3 px-6 font-bold border-r border-blue-700">Tgl Selesai</th>
                    <th class="py-3 px-6 font-bold border-r border-blue-700">Jenis Desain</th>
                    <th class="py-3 px-6 font-bold border-r border-blue-700">Total</th>
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
                        {{ $order->updated_at->translatedFormat('d M Y') }}
                    </td>
                    <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">
                        {{ $order->designType->nama_jenis }}
                    </td>
                    <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">
                        Rp {{ number_format($order->designType->harga, 0, ',', '.') }}
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