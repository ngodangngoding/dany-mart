<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Halaman utama laporan — render view dengan ringkasan & chart data awal.
     */
    public function index(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date|after_or_equal:date_from',
            'period'    => 'nullable|string|in:today,week,month,year',
        ]);

        [$dateFrom, $dateTo] = $this->resolveDateRange($request);

        // --- Summary ---
        $totalOrders   = Order::whereBetween('order_date', [$dateFrom, $dateTo])->count();
        $totalRevenue  = Order::whereBetween('order_date', [$dateFrom, $dateTo])->sum('total_amount');
        $totalExpenses = Expense::whereBetween('date', [$dateFrom, $dateTo])->sum('amount');
        $grossProfit   = $totalRevenue - $totalExpenses;

        // --- Sales chart (per hari, 30 titik terakhir dalam range) ---
        $salesChart = Order::selectRaw('DATE(order_date) as date, SUM(total_amount) as total')
            ->whereBetween('order_date', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => ['date' => $row->date, 'total' => (float) $row->total]);

        // --- Expense chart (per hari) ---
        $expenseChart = Expense::selectRaw('DATE(date) as date, SUM(amount) as total')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => ['date' => $row->date, 'total' => (float) $row->total]);

        // --- Payment method breakdown ---
        $paymentChart = Order::selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
            ->whereBetween('order_date', [$dateFrom, $dateTo])
            ->groupBy('payment_method')
            ->get()
            ->map(fn ($row) => [
                'method' => $row->payment_method ?? 'Lainnya',
                'count'  => (int) $row->count,
                'total'  => (float) $row->total,
            ]);

        // --- Top 5 produk terlaris ---
        $topProducts = OrderItem::with('product')
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('order', fn ($q) => $q->whereBetween('order_date', [$dateFrom, $dateTo]))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'name'          => $item->product->name ?? '-',
                'total_qty'     => (int) $item->total_qty,
                'total_revenue' => (float) $item->total_revenue,
            ]);

        $period   = $request->query('period', 'month');
        $dateFrom = $dateFrom->toDateString();
        $dateTo   = $dateTo->toDateString();

        return view('admin.laporan', compact(
            'totalOrders',
            'totalRevenue',
            'totalExpenses',
            'grossProfit',
            'salesChart',
            'expenseChart',
            'paymentChart',
            'topProducts',
            'period',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * JSON: ringkasan angka summary (untuk refresh dinamis via AJAX).
     */
    public function summary(Request $request)
    {
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);

        $totalOrders   = Order::whereBetween('order_date', [$dateFrom, $dateTo])->count();
        $totalRevenue  = Order::whereBetween('order_date', [$dateFrom, $dateTo])->sum('total_amount');
        $totalExpenses = Expense::whereBetween('date', [$dateFrom, $dateTo])->sum('amount');
        $grossProfit   = $totalRevenue - $totalExpenses;

        return response()->json([
            'total_orders'   => $totalOrders,
            'total_revenue'  => (float) $totalRevenue,
            'total_expenses' => (float) $totalExpenses,
            'gross_profit'   => (float) $grossProfit,
        ]);
    }

    /**
     * JSON: data penjualan per hari untuk line chart.
     */
    public function salesChart(Request $request)
    {
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);

        $data = Order::selectRaw('DATE(order_date) as date, SUM(total_amount) as total, COUNT(*) as count')
            ->whereBetween('order_date', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date'  => $row->date,
                'total' => (float) $row->total,
                'count' => (int) $row->count,
            ]);

        return response()->json($data);
    }

    /**
     * JSON: data pengeluaran per hari untuk chart.
     */
    public function expenseChart(Request $request)
    {
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);

        $data = Expense::selectRaw('DATE(date) as date, expense_category, SUM(amount) as total')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->groupBy('date', 'expense_category')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date'     => $row->date,
                'category' => $row->expense_category,
                'total'    => (float) $row->total,
            ]);

        return response()->json($data);
    }

    /**
     * JSON: breakdown metode pembayaran untuk pie chart.
     */
    public function paymentMethodChart(Request $request)
    {
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);

        $data = Order::selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
            ->whereBetween('order_date', [$dateFrom, $dateTo])
            ->groupBy('payment_method')
            ->get()
            ->map(fn ($row) => [
                'method' => $row->payment_method ?? 'Lainnya',
                'count'  => (int) $row->count,
                'total'  => (float) $row->total,
            ]);

        return response()->json($data);
    }

    /**
     * JSON: top produk terlaris berdasarkan kuantitas terjual.
     */
    public function topProducts(Request $request)
    {
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);

        $limit = min((int) $request->query('limit', 10), 50);

        $data = OrderItem::with('product')
            ->select(
                'product_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->whereHas('order', fn ($q) => $q->whereBetween('order_date', [$dateFrom, $dateTo]))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'product_id'    => $item->product_id,
                'name'          => $item->product->name ?? '-',
                'code'          => $item->product->code ?? '-',
                'total_qty'     => (int) $item->total_qty,
                'total_revenue' => (float) $item->total_revenue,
            ]);

        return response()->json($data);
    }

    /**
     * JSON: laporan laba rugi — pendapatan, HPP, dan laba bersih per produk.
     */
    public function profitReport(Request $request)
    {
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);

        // Pendapatan per produk
        $salesData = OrderItem::with('product')
            ->select(
                'product_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as revenue')
            )
            ->whereHas('order', fn ($q) => $q->whereBetween('order_date', [$dateFrom, $dateTo]))
            ->groupBy('product_id')
            ->get();

        $profitData = $salesData->map(function ($item) {
            $purchasePrice = $item->product ? (float) $item->product->purchase_price : 0;
            $qty           = (int) $item->total_qty;
            $revenue       = (float) $item->revenue;
            $hpp           = $purchasePrice * $qty;
            $profit        = $revenue - $hpp;

            return [
                'product_id'    => $item->product_id,
                'name'          => $item->product->name ?? '-',
                'code'          => $item->product->code ?? '-',
                'total_qty'     => $qty,
                'revenue'       => $revenue,
                'hpp'           => $hpp,
                'profit'        => $profit,
                'margin_pct'    => $revenue > 0 ? round(($profit / $revenue) * 100, 2) : 0,
            ];
        })->sortByDesc('profit')->values();

        $totalRevenue  = $profitData->sum('revenue');
        $totalHpp      = $profitData->sum('hpp');
        $totalExpenses = Expense::whereBetween('date', [$dateFrom, $dateTo])->sum('amount');
        $grossProfit   = $totalRevenue - $totalHpp;
        $netProfit     = $grossProfit - (float) $totalExpenses;

        return response()->json([
            'summary' => [
                'total_revenue'  => $totalRevenue,
                'total_hpp'      => $totalHpp,
                'total_expenses' => (float) $totalExpenses,
                'gross_profit'   => $grossProfit,
                'net_profit'     => $netProfit,
            ],
            'products' => $profitData,
        ]);
    }

    // -------------------------------------------------------------------------
    // Helper: resolve date range dari query string atau default 1 bulan terakhir
    // -------------------------------------------------------------------------
    private function resolveDateRange(Request $request): array
    {
        $period = $request->query('period', 'month');

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $dateFrom = \Carbon\Carbon::parse($request->query('date_from'))->startOfDay();
            $dateTo   = \Carbon\Carbon::parse($request->query('date_to'))->endOfDay();

            return [$dateFrom, $dateTo];
        }

        $now = \Carbon\Carbon::now();

        $dateFrom = match ($period) {
            'today' => $now->copy()->startOfDay(),
            'week'  => $now->copy()->startOfWeek(),
            'year'  => $now->copy()->startOfYear(),
            default => $now->copy()->startOfMonth(), // 'month'
        };

        $dateTo = $now->copy()->endOfDay();

        return [$dateFrom, $dateTo];
    }
}
