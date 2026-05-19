<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Riwayat Transaksi</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
        <link rel="stylesheet" href="/css/kasir-static.css">
    </head>
    <body class="dashboard-shell">
        <div class="app-layout two-column">
            @include('layout.kasir-sidebar')

            <main class="main-content bg-light">
                <div class="content-stack">
                    @include('layout.kasir-topbar')

                    <section class="transaction-container shadow-sm">
                        <div class="transaction-header">
                            <h2>Riwayat Transaksi</h2>
                            <p>Daftar transaksi dari kasir.</p>
                        </div>

                        <div class="table-responsive">
                            <table class="table-modern">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Waktu</th>
                                        <th>Total Item</th>
                                        <th>Total Tagihan</th>
                                        <th>Metode Bayar</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($orders as $order)
                                        <tr>
                                            <td class="fw-bold">{{ $order->order_number }}</td>
                                            <td class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                            <td><span class="badge-item">{{ $order->items->sum('quantity') }} Item</span></td>
                                            <td class="fw-bold text-success">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                            <td>{{ $order->payment_method }}</td>
                                            <td><span class="status-lunas">Lunas</span></td>
                                            <td>
                                                <a class="btn-secondary" href="{{ route('kasir.orders.receipt', $order->id) }}">Struk</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-muted">Belum ada transaksi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($orders->hasPages())
                            <div class="table-footer-info">
                                {{ $orders->links() }}
                            </div>
                        @endif
                    </section>
                </div>
            </main>
        </div>
    </body>
</html>
