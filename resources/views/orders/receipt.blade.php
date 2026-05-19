<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Struk {{ $order->order_number }}</title>
        <link rel="stylesheet" href="/css/kasir-static.css">
    </head>
    <body class="dashboard-shell">
        <main class="receipt-page">
            <section class="success-panel receipt-print">
                <div class="success-heading">
                    <span>Dany Mart</span>
                </div>
                <div class="success-receipt">
                    <p class="receipt-note">{{ $order->order_number }}</p>
                    <div class="receipt-summary">
                        <div class="receipt-line">
                            <span>Tanggal</span>
                            <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="receipt-line">
                            <span>Kasir</span>
                            <span>{{ $order->user ? $order->user->name : '-' }}</span>
                        </div>
                    </div>
                    <div class="receipt-items">
                        @foreach ($order->items as $item)
                            <div class="receipt-line">
                                <span>{{ $item->product ? $item->product->name : '-' }} ({{ $item->quantity }}x)</span>
                                <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="receipt-summary">
                        <div class="receipt-line">
                            <span>Total Tagihan</span>
                            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="receipt-line">
                            <span>{{ $order->payment_method }}</span>
                            <span>Rp {{ number_format($order->payment_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="receipt-line">
                            <span>Kembalian</span>
                            <span>Rp {{ number_format($order->change_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
