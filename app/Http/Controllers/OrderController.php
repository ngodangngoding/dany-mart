<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function create()
    {
        $products = Product::query()
            ->with('category')
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->code,
                    'category' => $product->category ? $product->category->name : 'Lainnya',
                    'price' => (int) $product->selling_price,
                    'stock' => (int) $product->stock,
                ];
            });

        $categories = collect(['Semua produk'])
            ->merge($products->pluck('category')->filter()->unique()->values())
            ->values();

        if (request()->routeIs('admin.*')) {
            return view('admin.dashboard', compact('products', 'categories'));
        }

        return view('kasir.index', compact('products', 'categories'));
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $totalAmount = 0;
        $items = [];

        foreach ($validated['items'] as $row) {
            $product = Product::findOrFail($row['product_id']);
            $quantity = (int) $row['quantity'];
            $subtotal = $product->selling_price * $quantity;
            $totalAmount += $subtotal;

            $items[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'unit_price' => (int) $product->selling_price,
                'quantity' => $quantity,
                'subtotal' => (int) $subtotal,
                'stock' => (int) $product->stock,
            ];
        }

        return response()->json([
            'total_amount' => (int) $totalAmount,
            'items' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:Tunai,QRIS',
            'payment_amount' => 'required|integer|min:0',
        ], [
            'items.required' => 'Item pesanan wajib diisi.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_amount.required' => 'Jumlah bayar wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $orderItems = [];

            foreach ($validated['items'] as $row) {
                $product = Product::query()
                    ->lockForUpdate()
                    ->findOrFail($row['product_id']);

                $quantity = (int) $row['quantity'];

                if ($product->stock < $quantity) {
                    throw new \Exception('Stok ' . $product->name . ' tidak mencukupi.');
                }

                $subtotal = $product->selling_price * $quantity;
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => (int) $product->selling_price,
                    'subtotal' => (int) $subtotal,
                ];
            }

            if ($validated['payment_method'] === 'Tunai' && $validated['payment_amount'] < $totalAmount) {
                throw new \Exception('Jumlah bayar kurang dari total pembayaran.');
            }

            $userId = Auth::id() ?: User::where('role', 'kasir')->value('id');

            if (!$userId) {
                $userId = User::firstOrCreate(
                    ['username' => 'kasir'],
                    [
                        'name' => 'Kasir Utama',
                        'email' => 'kasir@mail.com',
                        'password' => bcrypt('password'),
                        'role' => 'kasir',
                    ]
                )->id;
            }

            $paymentAmount = $validated['payment_method'] === 'QRIS' ? $totalAmount : $validated['payment_amount'];

            $order = Order::create([
                'user_id' => $userId,
                'order_number' => 'ORD-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4)),
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'payment_amount' => $paymentAmount,
                'change_amount' => max(0, $paymentAmount - $totalAmount),
                'order_date' => now()->toDateString(),
            ]);

            foreach ($orderItems as $row) {
                $order->items()->create([
                    'product_id' => $row['product']->id,
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'subtotal' => $row['subtotal'],
                ]);

                $row['product']->decrement('stock', $row['quantity']);
            }

            DB::commit();

            $order = Order::with('items.product', 'user')->findOrFail($order->id);

            $receiptRoute = $request->routeIs('admin.*') ? 'admin.orders.receipt' : 'kasir.orders.receipt';

            return response()->json([
                'message' => 'Pembayaran berhasil.',
                'order' => $this->formatOrder($order),
                'receipt_url' => route($receiptRoute, $order->id),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menyimpan order: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $paymentMethod = $request->query('payment_method');

        $orders = Order::query()
            ->with('items', 'user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('order_number', 'like', "%{$search}%");
            })
            ->when($paymentMethod, function ($query) use ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->routeIs('admin.*')) {
            return view('admin.riwayat', compact('orders', 'search', 'paymentMethod'));
        }

        return view('kasir.history', compact('orders', 'search', 'paymentMethod'));
    }

    public function show($id)
    {
        $order = Order::with('items.product', 'user')->findOrFail($id);

        return response()->json($this->formatOrder($order));
    }

    public function receipt($id)
    {
        $order = Order::with('items.product', 'user')->findOrFail($id);

        return view('orders.receipt', compact('order'));
    }

    private function formatOrder($order)
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'total_amount' => (int) $order->total_amount,
            'payment_method' => $order->payment_method,
            'payment_amount' => (int) $order->payment_amount,
            'change_amount' => (int) $order->change_amount,
            'order_date' => $order->order_date,
            'created_at' => $order->created_at ? $order->created_at->format('d/m/Y H:i') : '-',
            'user' => $order->user ? $order->user->name : '-',
            'items' => $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'name' => $item->product ? $item->product->name : '-',
                    'unit_price' => (int) $item->unit_price,
                    'price' => (int) $item->unit_price,
                    'quantity' => (int) $item->quantity,
                    'qty' => (int) $item->quantity,
                    'subtotal' => (int) $item->subtotal,
                ];
            })->values()->all(),
        ];
    }
}
