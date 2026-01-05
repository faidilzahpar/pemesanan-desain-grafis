@extends('layouts.admin')

@section('title', 'Jenis Desain')
@section('content')

{{-- Header --}}
<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
    <h1 class="text-3xl font-bold text-gray-800">
        Kelola Jenis Desain
    </h1>

    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
        {{-- SEARCH FORM --}}
        <form method="GET" action="{{ route('design-types.index') }}" class="relative w-full sm:w-64">
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
                   placeholder="Cari jenis desain..." 
                   class="w-full py-2 pl-10 pr-4 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </form>

        {{-- TOMBOL TAMBAH --}}
        <a href="{{ route('design-types.create') }}"
           class="px-5 py-2 bg-blue-600 text-white font-medium rounded-lg 
                  hover:bg-blue-700 transition flex items-center justify-center gap-2 shadow-sm whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
            </svg>
            Tambah Jenis
        </a>
    </div>
</div>

@if ($types->count() === 0)

    <p class="text-gray-500 text-center py-10">
        Tidak ada Jenis Desain yang tersedia.
    </p>

@else
<div class="bg-white shadow-xl rounded-xl overflow-hidden ring-1 ring-gray-200/50">

    {{-- TABLE --}}
    <div class="hidden md:block overflow-x-auto relative">
        <table class="w-full text-sm text-left text-gray-500 whitespace-nowrap">
            <thead class="text-xs uppercase text-white bg-blue-600">
                <tr>
                    @php
                        $sortCol = request('tableSortColumn');
                        $sortDir = request('tableSortDirection');
                        
                        $getSortUrl = function($col) use ($sortCol, $sortDir) {
                            $nextDir = ($sortCol === $col && $sortDir === 'asc') ? 'desc' : 'asc';
                            return request()->fullUrlWithQuery(['tableSortColumn' => $col, 'tableSortDirection' => $nextDir]);
                        };
                        
                        $renderIcon = function($col) use ($sortCol, $sortDir) {
                            if ($sortCol !== $col) return null;
                            return $sortDir === 'asc' 
                                ? '<svg class="w-3 h-3 ml-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>'
                                : '<svg class="w-3 h-3 ml-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>';
                        };
                    @endphp

                    <th class="py-3 px-6 font-bold border-r border-blue-700 cursor-pointer hover:bg-blue-700 transition"
                        onclick="window.location='{{ $getSortUrl('nama_jenis') }}'">
                        <div class="flex items-center">
                            Jenis Desain {!! $renderIcon('nama_jenis') !!}
                        </div>
                    </th>

                    <th class="py-3 px-6 font-bold border-r border-blue-700 cursor-pointer hover:bg-blue-700 transition"
                        onclick="window.location='{{ $getSortUrl('harga') }}'">
                        <div class="flex items-center">
                            Harga {!! $renderIcon('harga') !!}
                        </div>
                    </th>

                    <th class="py-3 px-6 font-bold border-r border-blue-700 cursor-pointer hover:bg-blue-700 transition"
                        onclick="window.location='{{ $getSortUrl('durasi') }}'">
                        <div class="flex items-center">
                            Durasi {!! $renderIcon('durasi') !!}
                        </div>
                    </th>

                    <th class="py-3 px-6 font-bold border-r border-blue-700">
                        Deskripsi
                    </th>

                    <th class="py-3 px-6 font-bold border-r border-blue-700 cursor-pointer hover:bg-blue-700 transition text-center"
                        onclick="window.location='{{ $getSortUrl('is_active') }}'">
                        <div class="flex items-center justify-center">
                            Status {!! $renderIcon('is_active') !!}
                        </div>
                    </th>

                    <th class="py-3 px-6 text-center font-bold">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach($types as $item)
                <tr class="border-b border-gray-300 hover:bg-gray-100 transition">
                    <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">
                        {{ $item->nama_jenis }}
                    </td>

                    <td class="py-4 px-6 font-medium text-gray-900 border-r border-gray-300">
                        Rp {{ number_format($item->harga, 0, ',', '.') }}
                    </td>

                    <td class="py-4 px-6 font-medium text-gray-900 text-center border-r border-gray-300">
                        {{ $item->durasi }} hari
                    </td>

                    <td class="py-4 px-6 font-medium text-gray-900 max-w-xs border-r border-gray-300">
                        <span class="line-clamp-2">{{ $item->deskripsi }}</span>
                    </td>

                    <td class="py-4 px-6 text-center border-r border-gray-300">
                        {{-- Toggle Status --}}
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                class="sr-only peer toggle-status" 
                                data-id="{{ $item->design_type_id }}"
                                @checked($item->is_active)
                            >
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-green-500
                                        peer-checked:after:translate-x-full
                                        after:content-[''] after:absolute after:top-[2px] after:start-[2px]
                                        after:bg-white after:h-5 after:w-5 after:rounded-full after:transition-all">
                            </div>
                        </label>
                    </td>

                    <td class="py-4 px-6 flex justify-center space-x-4">
                        <a href="{{ route('design-types.edit', $item->design_type_id) }}"
                           class="text-blue-600 hover:text-blue-400 transition duration-150 flex">
                           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                            <path d="m5.433 13.917 1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z" />
                            <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z" />
                            </svg>Edit
                        </a>

                        <form method="POST" action="{{ route('design-types.destroy', $item->design_type_id) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:text-red-400 transition duration-150 flex"
                                    onclick="return confirm('Hapus jenis desain ini?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                    <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd" />
                                    </svg>Hapus
                            </button>
                        </form>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    <div class="p-6">
        {{ $types->links() }}
    </div>
</div>

<div class="mt-8 text-center text-sm text-gray-500">
    Total jenis desain: <span class="font-bold">{{ $types->total() }}</span>
</div>
@endif
@endsection
