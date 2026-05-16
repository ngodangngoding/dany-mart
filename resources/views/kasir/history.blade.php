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
                            <p>Daftar contoh transaksi statis.</p>
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
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold">ORD-STATIC-001</td>
                                        <td class="text-muted">06 Mei 2026, 09:18</td>
                                        <td><span class="badge-item">3 Item</span></td>
                                        <td class="fw-bold text-success">Rp 32.000</td>
                                        <td>Tunai</td>
                                        <td><span class="status-lunas">Lunas</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">ORD-STATIC-002</td>
                                        <td class="text-muted">06 Mei 2026, 10:42</td>
                                        <td><span class="badge-item">2 Item</span></td>
                                        <td class="fw-bold text-success">Rp 23.000</td>
                                        <td>QRIS</td>
                                        <td><span class="status-lunas">Lunas</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">ORD-STATIC-003</td>
                                        <td class="text-muted">06 Mei 2026, 13:05</td>
                                        <td><span class="badge-item">5 Item</span></td>
                                        <td class="fw-bold text-success">Rp 58.000</td>
                                        <td>Tunai</td>
                                        <td><span class="status-lunas">Lunas</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </body>
</html>
