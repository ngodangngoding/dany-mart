<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>POS System - Riwayat Transaksi</title>
        <link rel="stylesheet" href="/css/admin.css">
    </head>
    <body>
        <div class="app-container">
            @include('layout.admin-sidebar')

            <main class="main-content bg-light">
                <div class="content-wrapper">
                    <div class="transaction-container shadow-sm">
                        <div class="transaction-header">
                            <h2>Riwayat Transaksi</h2>
                            <p>Daftar semua transaksi yang telah selesai</p>
                        </div>

                        <form class="table-tools" method="GET" action="{{ route('admin.riwayat') }}">
                            <div class="table-filters">
                                <input type="text" name="search" value="{{ $search }}" placeholder="Cari order ID...">
                                <select name="payment_method">
                                    <option value="">Semua metode</option>
                                    <option value="Tunai" {{ $paymentMethod === 'Tunai' ? 'selected' : '' }}>Tunai</option>
                                    <option value="QRIS" {{ $paymentMethod === 'QRIS' ? 'selected' : '' }}>QRIS</option>
                                </select>
                                <button class="btn-filter-date" type="submit">Filter</button>
                            </div>
                        </form>

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
                                                <a class="btn-reset-link" href="{{ route('admin.orders.receipt', $order->id) }}">Struk</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="empty-table">Belum ada transaksi.</td>
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
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
