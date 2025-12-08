@extends('layouts.admin')

@section('title', 'Kelola Pesanan')
@section('content')
<h1 class="text-3xl font-bold text-gray-800 mb-6">
    Kelola Pesanan
</h1>
<div x-data="{ tab: 'proses' }">

    {{-- TAB NAVIGATION --}}
    <div class="flex mb-0 border-b">

        {{-- Sedang Dikerjakan --}}
        <button 
            @click="tab = 'proses'"
            class="px-4 py-2 font-semibold transition -mb-[1px]"
            :class="tab === 'proses' 
                ? 'bg-white border border-b-0 text-blue-600 rounded-t-lg' 
                : 'bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-t-lg'">
            Sedang Dikerjakan
            <span class="ml-1 bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full">
                {{ $countProses }}
            </span>
        </button>

        {{-- Pesanan Baru --}}
        <button 
            @click="tab = 'baru'"
            class="px-4 py-2 font-semibold transition -mb-[1px]"
            :class="tab === 'baru' 
                ? 'bg-white border border-b-0 text-blue-600 rounded-t-lg' 
                : 'bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-t-lg'">
            Pesanan Baru
            <span class="ml-1 bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full">
                {{ $countBaru }}
            </span>
        </button>

    </div>

    {{-- CONTAINER (NYATU DENGAN TAB) --}}
    <div class="bg-white shadow-md rounded-b-xl p-4 md:p-8 border border-t-0">

        {{-- TABEL SEDANG DIKERJAKAN --}}
        <div x-show="tab === 'proses'">
            @if ($ordersProses->count() === 0)

                <p class="text-gray-500 text-center py-10">
                    Belum ada pesanan yang sedang dikerjakan.
                </p>

            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-700">
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
                            @foreach($ordersProses as $order)
                            <tr class="border-b border-gray-300 hover:bg-gray-100 transition">
                                <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">{{ $order->order_id }}</td>
                                <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">{{ $order->user->name }}</td>
                                <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">{{ $order->user->no_hp }}</td>
                                <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">{{ $order->designType->nama_jenis }}</td>
                                <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">{{ $order->status_pesanan }}</td>
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
            @endif
        </div>

        {{-- TABEL PESANAN BARU --}}
        <div x-show="tab === 'baru'">
            @if ($ordersBaru->count() === 0)

                <p class="text-gray-500 text-center py-10">
                    Belum ada pesanan baru.
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
                            @foreach($ordersBaru as $order)
                            <tr class="border-b border-gray-300 hover:bg-gray-100 transition">
                                <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">{{ $order->order_id }}</td>
                                <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">{{ $order->user->name }}</td>
                                <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">{{ $order->user->no_hp }}</td>
                                <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">{{ $order->designType->nama_jenis }}</td>
                                <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">{{ $order->status_pesanan }}</td>
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
            @endif
        </div>

    </div>
</div>

@endsection
