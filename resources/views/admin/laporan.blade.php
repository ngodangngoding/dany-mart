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
            @include('layout.admin-topbar', [
                'title'    => 'Laporan',
                'subtitle' => 'Analisa keuangan dan performa toko',
                'user'     => auth()->user()->name . ' (Admin)',
            ])

            {{-- Filter Toolbar --}}
            <div class="report-toolbar">
                <form method="GET" action="{{ route('admin.laporan') }}" class="filter-group" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                    <div style="display:flex;gap:4px;">
                        @foreach(['today' => 'Hari ini', 'week' => 'Minggu ini', 'month' => 'Bulan ini', 'year' => 'Tahun ini'] as $val => $label)
                            <button type="submit" name="period" value="{{ $val }}"
                                class="{{ $period === $val ? 'btn-filter active' : 'btn-filter' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    <span style="color:#aaa;font-size:13px;">atau</span>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-select-sm">
                    <span style="font-size:13px;color:#666;">s/d</span>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-select-sm">
                    <button type="submit" class="btn-filter-date">Terapkan</button>
                    <a href="{{ route('admin.laporan') }}" class="btn-reset-link">Reset</a>
                </form>
                <button class="btn-print" type="button" onclick="window.print()">Cetak Laporan</button>
            </div>

            {{-- Stat Cards --}}
            <div class="stats-grid">
                <div class="stat-card purple">
                    <div class="stat-icon">TR</div>
                    <div class="stat-data">
                        <span>Total transaksi</span>
                        <h3>{{ number_format($totalOrders) }}</h3>
                    </div>
                </div>
                <div class="stat-card blue">
                    <div class="stat-icon">RP</div>
                    <div class="stat-data">
                        <span>Total pendapatan</span>
                        <h3>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                    </div>
                </div>
                <div class="stat-card red">
                    <div class="stat-icon">PG</div>
                    <div class="stat-data">
                        <span>Total pengeluaran</span>
                        <h3>Rp {{ number_format($totalExpenses, 0, ',', '.') }}</h3>
                    </div>
                </div>
                <div class="stat-card green">
                    <div class="stat-icon">LB</div>
                    <div class="stat-data">
                        <span>Laba kotor</span>
                        <h3 style="{{ $grossProfit < 0 ? 'color:#ef4444;' : '' }}">
                            Rp {{ number_format($grossProfit, 0, ',', '.') }}
                        </h3>
                    </div>
                </div>
            </div>

            {{-- Main Chart: Pendapatan vs Pengeluaran --}}
            <div class="chart-container main-chart shadow-sm">
                <div class="chart-header">
                    <h4 style="font-size:14px;font-weight:600;color:#374151;">Pendapatan & Pengeluaran Harian</h4>
                    <span style="font-size:12px;color:#9ca3af;">
                        {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} –
                        {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
                    </span>
                </div>
                <canvas id="lineChart" height="100"></canvas>
            </div>

            {{-- Bottom Charts --}}
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

            {{-- Top Produk Terlaris --}}
            <div class="table-container shadow-sm" style="margin-top:20px;">
                <div style="padding:14px 16px 8px;border-bottom:1px solid #f3f4f6;">
                    <h4 style="font-size:14px;font-weight:600;color:#374151;margin:0;">Top Produk Terlaris</h4>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Produk</th>
                            <th>Terjual (qty)</th>
                            <th>Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topProducts as $i => $product)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><strong>{{ $product['name'] }}</strong></td>
                                <td>{{ number_format($product['total_qty']) }}</td>
                                <td>Rp {{ number_format($product['total_revenue'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-table">Belum ada data penjualan pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </main>
    </div>

    <script>
        // ── Data dari controller (PHP → JS) ──────────────────────────
        const salesData   = @json($salesChart);
        const expenseData = @json($expenseChart);
        const paymentData = @json($paymentChart);

        // ── Merge tanggal sales & expense untuk sumbu X bersama ──────
        const allDates = [...new Set([
            ...salesData.map(d => d.date),
            ...expenseData.map(d => d.date),
        ])].sort();

        const salesMap   = Object.fromEntries(salesData.map(d => [d.date, d.total]));
        const expenseMap = {};
        expenseData.forEach(d => {
            expenseMap[d.date] = (expenseMap[d.date] || 0) + d.total;
        });

        const salesValues   = allDates.map(d => salesMap[d]   || 0);
        const expenseValues = allDates.map(d => expenseMap[d] || 0);

        const labels = allDates.map(d => {
            const [y, m, day] = d.split('-');
            return `${day}/${m}`;
        });

        // ── Line Chart ────────────────────────────────────────────────
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: salesValues,
                        borderColor: '#4ade80',
                        backgroundColor: 'rgba(74,222,128,0.08)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                    },
                    {
                        label: 'Pengeluaran',
                        data: expenseValues,
                        borderColor: '#f87171',
                        backgroundColor: 'rgba(248,113,113,0.08)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y: {
                        ticks: {
                            callback: v => 'Rp ' + (v / 1000).toLocaleString('id') + 'k',
                        },
                    },
                },
            },
        });

        // ── Pie Chart: Metode Pembayaran ──────────────────────────────
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: paymentData.length ? paymentData.map(d => d.method) : ['Belum ada data'],
                datasets: [{
                    data: paymentData.length ? paymentData.map(d => d.count) : [1],
                    backgroundColor: ['#4ade80', '#60a5fa', '#fbbf24', '#f87171', '#a78bfa'],
                    borderWidth: 2,
                    borderColor: '#fff',
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 12 } } },
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                if (!paymentData.length) return 'Belum ada data';
                                const d = paymentData[ctx.dataIndex];
                                return ` ${d.method}: ${d.count} transaksi`;
                            },
                        },
                    },
                },
            },
        });

        // ── Bar Chart: Pendapatan vs Pengeluaran per bulan ────────────
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: salesValues,
                        backgroundColor: 'rgba(74,222,128,0.7)',
                        borderRadius: 4,
                    },
                    {
                        label: 'Pengeluaran',
                        data: expenseValues,
                        backgroundColor: 'rgba(248,113,113,0.7)',
                        borderRadius: 4,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y: {
                        ticks: {
                            callback: v => 'Rp ' + (v / 1000).toLocaleString('id') + 'k',
                        },
                    },
                },
            },
        });
    </script>

</body>

</html>