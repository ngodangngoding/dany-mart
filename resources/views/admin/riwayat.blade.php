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
                                        <td class="fw-bold">PREVIEW-20260421162334</td>
                                        <td class="text-muted">22/12/2025 12:00</td>
                                        <td><span class="badge-item">1 Item</span></td>
                                        <td class="fw-bold text-success">Rp 18.000</td>
                                        <td>Tunai</td>
                                        <td><span class="status-lunas">Lunas</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">PREVIEW-20260421161506</td>
                                        <td class="text-muted">22/12/2025 14:00</td>
                                        <td><span class="badge-item">1 Item</span></td>
                                        <td class="fw-bold text-success">Rp 12.000</td>
                                        <td>QRIS</td>
                                        <td><span class="status-lunas">Lunas</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">PREVIEW-20260421161151</td>
                                        <td class="text-muted">22/12/2025 14:55</td>
                                        <td><span class="badge-item">1 Item</span></td>
                                        <td class="fw-bold text-success">Rp 15.000</td>
                                        <td>QRIS</td>
                                        <td><span class="status-lunas">Lunas</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">PREVIEW-202604211103</td>
                                        <td class="text-muted">22/12/2025 15:30</td>
                                        <td><span class="badge-item">4 Item</span></td>
                                        <td class="fw-bold text-success">Rp 50.000</td>
                                        <td>Tunai</td>
                                        <td><span class="status-lunas">Lunas</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
