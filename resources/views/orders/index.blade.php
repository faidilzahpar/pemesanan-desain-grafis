@extends('layouts.main')

@section('title', 'Pesanan Saya')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-16 space-y-8">

    <h1 class="text-3xl font-extrabold text-slate-900">
        Pesanan Saya
    </h1>

    {{-- TAB STATUS --}}
    <div class="flex flex-wrap gap-3 text-sm font-semibold">
        @php
            $tabs = [
                'all' => 'Semua',
                'unpaid' => 'Belum Dibayar',
                'process' => 'Sedang Diproses',
                'done' => 'Selesai',
                'cancel' => 'Dibatalkan',
            ];
        @endphp

        @foreach($tabs as $key => $label)
            <a href="{{ route('orders.index', ['status' => $key]) }}"
               class="px-4 py-2 rounded-full border transition
               {{ request('status', 'all') === $key
                    ? 'bg-indigo-600 text-white border-indigo-600'
                    : 'bg-white text-slate-600 hover:bg-slate-100'
               }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- LIST PESANAN --}}
    @if($orders->isEmpty())
        <p class="text-slate-500 italic text-center py-20">
            Belum ada pesanan.
        </p>
    @else
        <div class="space-y-4">
        @foreach($orders as $order)

        @php
            $activeInvoice = $order->invoices->sortByDesc('created_at')->first();
            $paymentDeadline = $activeInvoice
                ? $activeInvoice->created_at->copy()->addHours(24)
                : null;
            $showPaymentAlert =
                $activeInvoice
                && $activeInvoice->jenis_invoice === 'DP'
                && in_array($activeInvoice->status_pembayaran, ['Belum Dibayar', 'Pembayaran Ditolak']);
        @endphp

        <div class="bg-white rounded-2xl border shadow-sm p-6 space-y-4">

            {{-- HEADER --}}
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-bold text-slate-900">
                        {{ $order->designType->nama_jenis }}
                    </p>
                    <p class="text-sm text-slate-500">
                        ID Pesanan: {{ $order->order_id }}
                    </p>
                </div>

                {{-- STATUS --}}
                <div x-data="{
                    init() {
                        // Refresh status setiap 5 detik agar tidak terlalu membebani server
                        setInterval(() => {
                            this.refreshStatus();
                        }, 5000);
                    },
                    refreshStatus() {
                        fetch('{{ route('orders.status-html', $order->order_id) }}?t=' + new Date().getTime())
                            .then(response => {
                                if (!response.ok) throw new Error('Network response was not ok');
                                return response.text();
                            })
                            .then(html => {
                                $refs.statusContainer.innerHTML = html;
                            })
                            .catch(error => console.error('Error fetching status:', error));
                    }
                }" x-init="init()">
                    
                    <div x-ref="statusContainer">
                        {{-- Panggil partial view untuk tampilan awal --}}
                        @include('orders.partials.status-badge', [
                            'order' => $order,
                            'activeInvoice' => $activeInvoice,
                            'paymentDeadline' => $paymentDeadline,
                            'showPaymentAlert' => $showPaymentAlert
                        ])
                    </div>
                </div>
            </div>

            {{-- INFO --}}
            <div class="text-sm text-slate-600 space-y-1">
                <p>
                    <span class="font-medium">Total:</span>
                    Rp {{ number_format($order->designType->harga, 0, ',', '.') }}
                </p>

                <p>
                    <span class="font-medium">Metode Pembayaran:</span>
                    {{ $order->paymentMethod->nama_metode }}
                </p>

                <p>
                    <span class="font-medium">Tanggal Pesan:</span>
                    {{ $order->created_at->translatedFormat('d F Y') }}
                </p>

                {{-- DEADLINE PENGERJAAN --}}
                @if($order->deadline)
                    <p>
                        <span class="font-medium">Deadline Pengerjaan:</span>
                        <span class="{{ now()->greaterThan($order->deadline)
                            ? 'text-red-600 font-semibold'
                            : 'text-slate-700'
                        }}">
                            {{ \Carbon\Carbon::parse($order->deadline)->translatedFormat('d F Y') }}
                        </span>
                    </p>
                @endif
            </div>

            {{-- AKSI --}}
            <div class="flex flex-wrap items-center gap-3 pt-2">

                {{-- BAYAR SEKARANG --}}
                {{-- BELUM BAYAR --}}
                @if(
                    in_array($order->status_pesanan, ['Menunggu DP', 'Menunggu Pelunasan'])
                    && $activeInvoice
                    && in_array($activeInvoice->status_pembayaran, [
                        'Belum Dibayar',
                        'Pembayaran Ditolak'
                    ])
                )
                    <a href="{{ route('invoices.show', $activeInvoice->invoice_id) }}"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-xl
                            hover:bg-indigo-700 transition font-bold">
                        Bayar Sekarang
                    </a>
                @endif

                {{-- DETAIL --}}
                <a href="{{ route('orders.show', $order->order_id) }}"
                class="px-5 py-2.5 bg-slate-200 text-slate-700 rounded-xl text-sm font-bold
                        hover:bg-slate-300 transition">
                    Detail Pesanan
                </a>

                {{-- SELESAI --}}
                @if($order->status_pesanan === 'Selesai')
                    <span class="text-green-600 font-semibold text-sm flex items-center gap-1 ml-auto sm:ml-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Pesanan Selesai
                    </span>
                @endif
            </div>
        </div>
        @endforeach
        </div>
    @endif
</div>
@endsection
