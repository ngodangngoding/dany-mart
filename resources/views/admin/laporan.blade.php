<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS System - Laporan</title>
    <link rel="stylesheet" href="/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/js/admin.js" defer></script>
</head>

<body>
    <div class="app-container">
        @include('layout.admin-sidebar')

        <main class="main-content">
            @include('layout.admin-topbar', ['title' => 'Laporan', 'subtitle' => 'Analisa keuangan dan performa toko', 'user' => 'Akbar Hidayat (Admin)'])

            <div class="report-toolbar">
                <button class="btn-filter" type="button">Filter periode</button>
                <button class="btn-print" type="button">Cetak Laporan</button>
            </div>

            <div class="stats-grid">
                <div class="stat-card purple">
                    <div class="stat-icon">TR</div>
                    <div class="stat-data">
                        <span>Total transaksi</span>
                        <h3>54</h3>
                    </div>
                </div>
                <div class="stat-card blue">
                    <div class="stat-icon">RP</div>
                    <div class="stat-data">
                        <span>Total pendapatan</span>
                        <h3>Rp 13.100.000</h3>
                    </div>
                </div>
                <div class="stat-card red">
                    <div class="stat-icon">PG</div>
                    <div class="stat-data">
                        <span>Total pengeluaran</span>
                        <h3>Rp 8.000.000</h3>
                    </div>
                </div>
                <div class="stat-card green">
                    <div class="stat-icon">LB</div>
                    <div class="stat-data">
                        <span>Laba kotor</span>
                        <h3>Rp 5.100.000</h3>
                    </div>
                </div>
            </div>

            <div class="chart-container main-chart shadow-sm">
                <div class="chart-header">
                    <button class="tab active" type="button">Pendapatan</button>
                    <button class="tab" type="button">Pengeluaran</button>
                    <button class="tab" type="button">Perbandingan</button>
                </div>
                <canvas id="lineChart" height="100"></canvas>
            </div>

            <div class="bottom-charts">
                <div class="chart-card shadow-sm">
                    <h4>Metode Pembayaran</h4>
                    <div class="pie-box">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
                <div class="chart-card shadow-sm">
                    <h4>Pendapatan vs Pengeluaran</h4>
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </main>
    </div>
</body>

</html>