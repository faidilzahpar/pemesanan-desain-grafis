@php
    $statusColor = match($order->status_pesanan) {
        'Menunggu DP', 'Menunggu Pelunasan' => 'bg-red-100 text-red-700',
        'Sedang Dikerjakan'                 => 'bg-yellow-100 text-yellow-700',
        'Menunggu Konfirmasi Pelanggan'     => 'bg-blue-100 text-blue-700',
        'Revisi'                            => 'bg-purple-100 text-purple-700',
        'Selesai'                           => 'bg-green-100 text-green-700',
        'Dibatalkan'                        => 'bg-gray-200 text-gray-600',
        default                             => 'bg-gray-100 text-gray-600',
    };
@endphp

<div class="flex flex-col items-end text-right gap-1">
    {{-- STATUS PESANAN --}}
    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
        {{ $order->status_pesanan }}
    </span>

    {{-- INFO PEMBAYARAN (Jika ada invoice aktif) --}}
    @if(isset($activeInvoice) && $activeInvoice)
        @php
             $paymentColor = match($activeInvoice->status_pembayaran) {
                'Belum Dibayar', 'Pembayaran Ditolak' => 'text-red-600',
                'Menunggu Verifikasi'                 => 'text-yellow-600',
                'Pembayaran Diterima'                 => 'text-green-600',
                default                               => 'text-gray-500',
            };
        @endphp
        
        {{-- Status Pembayaran --}}
        <span class="text-xs font-medium {{ $paymentColor }}">
            {{ $activeInvoice->status_pembayaran }}
        </span>

        {{-- DEADLINE BAYAR --}}
        {{-- Gunakan empty() atau isset() untuk mencegah error --}}
        @if(
            !empty($showPaymentAlert) 
            && !empty($paymentDeadline)
        )
            <span class="text-xs text-red-600 font-semibold mt-0.5">
                Bayar sebelum {{ $paymentDeadline->translatedFormat('d F Y, H:i') }}
            </span>
        @endif
    @endif
</div>