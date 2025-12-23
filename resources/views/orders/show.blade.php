@extends('layouts.main')

@section('title', 'Detail Pesanan')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-16 space-y-8">

    {{-- HEADER --}}
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">
                Detail Pesanan
            </h1>
            <p class="text-slate-500 text-sm">
                ID Pesanan: {{ $order->order_id }}
            </p>
        </div>

        {{-- STATUS --}}
        <div x-data="{
            init() {
                // Refresh status setiap 3 detik (lebih cepat karena ini halaman detail)
                setInterval(() => {
                    this.refreshStatus();
                }, 3000);
            },
            refreshStatus() {
                fetch('{{ route('orders.status-html', $order->order_id) }}')
                    .then(response => response.text())
                    .then(html => {
                        $refs.statusBadge.innerHTML = html;
                    });
            }
        }" x-init="init()">
            
            <div x-ref="statusBadge">
                {{-- Hitung ulang variabel untuk include pertama kali --}}
                @php
                    $activeInvoice = $order->invoices->sortByDesc('created_at')->first();
                    $paymentDeadline = $activeInvoice ? $activeInvoice->created_at->copy()->addHours(24) : null;
                    
                    $showPaymentAlert = $activeInvoice
                        && $activeInvoice->jenis_invoice === 'DP'
                        && in_array($activeInvoice->status_pembayaran, ['Belum Dibayar', 'Pembayaran Ditolak']);
                @endphp

                @include('orders.partials.status-badge', [
                    'order' => $order,
                    'activeInvoice' => $activeInvoice,
                    'paymentDeadline' => $paymentDeadline,
                    'showPaymentAlert' => $showPaymentAlert
                ])
            </div>
        </div>
    </div>

    {{-- INFO PESANAN --}}
    <div class="bg-white rounded-2xl border shadow-sm p-6 space-y-3">
        <p>
            <span class="font-semibold">Jenis Desain:</span>
            {{ $order->designType->nama_jenis }}
        </p>

        <p>
            <span class="font-semibold">Total:</span>
            Rp {{ number_format($order->designType->harga, 0, ',', '.') }}
        </p>
        
        <p>
            <span class="font-semibold">Metode Pembayaran:</span>
            {{ $order->paymentMethod->nama_metode}}
        </p>

        
        <p>
            <span class="font-semibold">Tanggal Pesan:</span>
            {{ $order->created_at->translatedFormat('d F Y, H:i') }}
        </p>

        {{-- DEADLINE PENGERJAAN --}}
        @if($order->deadline)
            <p>
                <span class="font-semibold">Deadline Pengerjaan:</span>
                <span class="text-red-600 font-semibold">
                    {{ \Carbon\Carbon::parse($order->deadline)->translatedFormat('d F Y, H:i') }}
                </span>
            </p>
        @endif
    </div>

    {{-- DESKRIPSI PESANAN --}}
    <div class="bg-white rounded-2xl border shadow-sm p-6 space-y-2">
        <h2 class="text-lg font-bold text-slate-900">
            Deskripsi Kebutuhan
        </h2>

        <p class="text-slate-700 whitespace-pre-line">
            {{ $order->deskripsi }}
        </p>
    </div>

    {{-- REFERENSI DESAIN --}}
    @if($order->referensi_desain)
    <div class="bg-white rounded-2xl border shadow-sm p-6 space-y-4"
        x-data="{ open: false }">

        <h2 class="text-lg font-bold text-slate-900">
            Referensi Desain
        </h2>

        {{-- Thumbnail --}}
        <div class="relative group w-full md:w-64">
            <img src="{{ asset('storage/' . $order->referensi_desain) }}"
                alt="Referensi Desain"
                @click="open = true"
                class="rounded-xl border-2 border-gray-100 shadow-sm
                        cursor-zoom-in group-hover:opacity-90
                        transition object-cover h-40 w-full">

            <div class="absolute inset-0 flex items-center justify-center
                        opacity-0 group-hover:opacity-100
                        transition pointer-events-none">
                <span class="bg-black/50 text-white px-3 py-1 rounded-full text-xs">
                    Klik untuk Perbesar
                </span>
            </div>
        </div>

        {{-- MODAL PREVIEW --}}
        <div x-show="open" x-cloak x-transition.opacity
            class="fixed inset-0 z-[100] flex items-center justify-center
                    bg-gray-900/95 backdrop-blur-sm p-4"
            @click.self="open = false">

            <button @click="open = false"
                    class="absolute top-5 right-5
                        text-white/70 hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-10 w-10"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <img src="{{ asset('storage/' . $order->referensi_desain) }}"
                class="max-w-full max-h-[85vh] rounded-lg
                        shadow-2xl border border-white/20 bg-white">
        </div>
    </div>
    @endif

    {{-- FILE DESAIN --}}
    @if(!in_array($order->status_pesanan, ['Menunggu DP', 'Dibatalkan']))
    <div class="bg-white rounded-2xl border shadow-sm p-6 space-y-4">
        <h2 class="text-xl font-bold text-slate-900">
            File Desain
        </h2>

        @if($order->orderFiles->isEmpty())
            <p class="text-slate-500 italic">
                Belum ada file yang dikirim.
            </p>
        @else
            <div class="space-y-3">
                @foreach($order->orderFiles as $index => $file)
                    <div class="flex justify-between items-center border rounded-xl p-4">
                        <div>
                            <p class="font-semibold text-slate-800">
                                {{ $file->tipe_file }}

                                @if($file->tipe_file === 'Revisi')
                                    ({{ $loop->iteration - 1 }})
                                @endif
                            </p>

                            <p class="text-xs text-slate-500">
                                {{ $file->created_at->translatedFormat('d F Y, H:i') }}
                            </p>
                        </div>

                        <a href="{{ asset('storage/' . $file->path_file) }}"
                        target="_blank"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg
                                hover:bg-indigo-700 transition text-sm font-semibold">
                            Lihat File
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    @endif

    {{-- INVOICE --}}
    @if($visibleInvoices->isNotEmpty())
    <div class="bg-white rounded-2xl border shadow-sm p-6 space-y-4">
        <h2 class="text-lg font-bold text-slate-900">Invoice</h2>

        <div class="flex flex-wrap gap-3">
            @foreach($visibleInvoices as $invoice)
                <a href="{{ route('invoices.show', $invoice->invoice_id) }}"
                class="px-4 py-2 rounded-lg text-sm font-semibold
                        {{ $invoice->jenis_invoice === 'DP'
                            ? 'bg-indigo-600 text-white hover:bg-indigo-700'
                            : 'bg-emerald-600 text-white hover:bg-emerald-700'
                        }}">
                    Lihat Invoice {{ $invoice->jenis_invoice }}
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- AKSI --}}
    <div class="flex flex-wrap gap-3">

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


        {{-- AJUKAN REVISI --}}
        @if(in_array($order->status_pesanan, [
            'Menunggu Konfirmasi Pelanggan',
            'Revisi'
        ]))
            <a href="https://wa.me/6288706468109?text={{ urlencode(
                'ID Pesanan : '.$order->order_id.'
Jenis Desain : '.$order->designType->nama_jenis.'
Saya ingin mengajukan revisi sebagai berikut:
Catatan Revisi:
- '
            ) }}"
               target="_blank"
               class="px-6 py-3 bg-green-600 text-white rounded-xl
                      hover:bg-green-700 transition font-bold">
                Ajukan Revisi
            </a>
        @endif

        {{-- SETUJUI DESAIN --}}
        @if(
            $order->status_pesanan === 'Menunggu Konfirmasi Pelanggan'
            && !$order->orderFiles->where('tipe_file', 'Final')->count()
        )
            <form method="POST"
                action="{{ route('orders.approve', $order->order_id) }}">
                @csrf
                <button type="submit"
                        class="px-6 py-3 bg-indigo-600 text-white rounded-xl
                            hover:bg-indigo-700 transition font-bold">
                    Setujui Desain
                </button>
            </form>
        @endif

        {{-- KEMBALI --}}
        <a href="{{ route('orders.index') }}"
           class="px-6 py-3 bg-slate-200 text-slate-700 rounded-xl
                  hover:bg-slate-300 transition font-bold">
            Kembali
        </a>
    </div>

</div>
@endsection
