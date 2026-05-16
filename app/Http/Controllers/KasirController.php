<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class KasirController extends Controller
{
    public function index()
    {
        if ($this->isDatabaseReady(['products'])) {
            $products = Product::query()
                ->select('id', 'name', 'category_id', 'selling_price', 'stock')
                ->orderBy('name')
                ->get();

            $categories = collect(['Semua produk'])
                ->merge(
                    Product::query()
                        ->whereNotNull('category_id')
                        ->distinct()
                        ->orderBy('category_id')
                        ->pluck('category_id')
                )
                ->values();

            return view('kasir.index', compact('products', 'categories'));
        }

        $products = $this->getPreviewProducts();

        $categories = collect(['Semua produk'])
            ->merge(
                $products->pluck('category')
                    ->filter()
                    ->unique()
                    ->values()
            )
            ->values();

        return view('kasir.index', compact('products', 'categories'));
    }

    public function checkout(Request $request)
    {
        if (!$this->isDatabaseReady(['products', 'orders', 'order_items'])) {
            return $this->previewCheckout($request);
        }

        $payload = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'string', 'in:Tunai,QRIS'],
        ]);

        $order = DB::transaction(function () use ($payload) {
            $order = Order::create([
                'order_number' => 'ORD-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4)),
                'subtotal' => 0,
                'total' => 0,
                'status' => 'paid',
                'payment_method' => $payload['payment_method'],
                'paid_at' => now(),
            ]);

            $subtotal = 0;

            foreach ($payload['items'] as $row) {
                $product = Product::query()
                    ->lockForUpdate()
                    ->findOrFail($row['product_id']);

                if ($product->stock < $row['qty']) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak mencukupi.",
                    ]);
                }

                $lineSubtotal = $product->price * $row['qty'];
                $subtotal += $lineSubtotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'qty' => $row['qty'],
                    'subtotal' => $lineSubtotal,
                ]);

                $product->decrement('stock', $row['qty']);
                $product->increment('sold_count', $row['qty']);
            }

            $order->update([
                'subtotal' => $subtotal,
                'total' => $subtotal,
            ]);

            return $order->fresh('items.product');
        });

        $recommendations = $this->getRecommendations($order);

        return response()->json([
            'message' => 'Pembayaran berhasil.',
            'order' => $this->transformOrder($order),
            'recommendations' => $this->transformRecommendationProducts($recommendations),
        ]);
    }

    public function applyRecommendations(Request $request, $order)
    {
        if (!$this->isDatabaseReady(['products', 'orders', 'order_items'])) {
            return $this->previewApplyRecommendations($request, $order);
        }

        $payload = $request->validate([
            'product_ids' => ['nullable', 'array', 'max:2'],
            'product_ids.*' => ['integer', 'distinct', 'exists:products,id'],
        ]);

        $order = DB::transaction(function () use ($payload, $order) {
            $order = Order::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($order);

            $productIds = collect($payload['product_ids'] ?? [])->values();

            if ($productIds->isNotEmpty()) {
                $products = Product::query()
                    ->whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($productIds as $productId) {
                    $product = $products->get($productId);

                    if (!$product || $product->stock < 1) {
                        continue;
                    }

                    $item = $order->items()
                        ->where('product_id', $product->id)
                        ->first();

                    if ($item) {
                        $item->qty += 1;
                        $item->subtotal = $item->qty * $item->price;
                        $item->save();
                    } else {
                        $order->items()->create([
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'price' => $product->price,
                            'qty' => 1,
                            'subtotal' => $product->price,
                        ]);
                    }

                    $product->decrement('stock', 1);
                    $product->increment('sold_count', 1);
                }

                $order->load('items');
                $subtotal = $order->items->sum('subtotal');

                $order->update([
                    'subtotal' => $subtotal,
                    'total' => $subtotal,
                ]);
            }

            $order->update([
                'status' => 'paid',
            ]);

            return $order->fresh('items.product');
        });

        return response()->json([
            'message' => 'Order berhasil diperbarui.',
            'order' => $this->transformOrder($order),
        ]);
    }

    protected function getRecommendations(Order $order)
    {
        $orderedProductIds = $order->items->pluck('product_id')->filter()->values();

        $orderedCategories = Product::query()
            ->whereIn('id', $orderedProductIds)
            ->whereNotNull('category')
            ->pluck('category')
            ->unique()
            ->values();

        $relevant = Product::query()
            ->when($orderedCategories->isNotEmpty(), function ($query) use ($orderedCategories) {
                $query->whereIn('category', $orderedCategories);
            })
            ->where('stock', '>', 0)
            ->whereNotIn('id', $orderedProductIds)
            ->orderByDesc('sold_count')
            ->limit(2)
            ->get();

        if ($relevant->count() < 2) {
            $fallback = Product::query()
                ->where('stock', '>', 0)
                ->whereNotIn('id', $orderedProductIds->merge($relevant->pluck('id'))->all())
                ->orderByDesc('sold_count')
                ->limit(2 - $relevant->count())
                ->get();

            $relevant = $relevant->concat($fallback);
        }

        return $relevant->take(2)->values();
    }

    protected function transformOrder(Order $order): array
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'subtotal' => (int) $order->subtotal,
            'total' => (int) $order->total,
            'status' => $order->status,
            'payment_method' => $order->payment_method ?? 'Tunai',
            'items' => $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'name' => $item->product_name,
                    'price' => (int) $item->price,
                    'qty' => (int) $item->qty,
                    'subtotal' => (int) $item->subtotal,
                ];
            })->values()->all(),
        ];
    }

    protected function transformRecommendationProducts($products): array
    {
        return $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (int) $product->price,
                'stock' => (int) $product->stock,
                'sold_count' => (int) $product->sold_count,
                'category' => $product->category,
            ];
        })->values()->all();
    }

    protected function isDatabaseReady(array $tables = []): bool
    {
        try {
            DB::connection()->getPdo();

            foreach ($tables as $table) {
                if (!Schema::hasTable($table)) {
                    return false;
                }
            }

            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    protected function getPreviewProducts(): Collection
    {
        return collect([
            ['id' => 1, 'name' => 'Piatos', 'BRG' => 'BRG-0S1', 'category' => 'Snack', 'price' => 12000, 'stock' => 120, 'sold_count' => 50],
            ['id' => 2, 'name' => 'Pulpen', 'BRG' => 'BRG-0S2', 'category' => 'Alat Tulis Kantor', 'price' => 18000, 'stock' => 33, 'sold_count' => 15],
            ['id' => 3, 'name' => 'Paracetamol', 'BRG' => 'BRG-0B3', 'category' => 'Medicine', 'price' => 15000, 'stock' => 32, 'sold_count' => 80],
            ['id' => 4, 'name' => 'Le Mineral', 'BRG' => 'BRG-0A1', 'category' => 'Minuman', 'price' => 15000, 'stock' => 3, 'sold_count' => 99],
            ['id' => 5, 'name' => 'Tehpucuk', 'BRG' => 'BRG-001', 'category' => 'Minuman', 'price' => 5000, 'stock' => 120, 'sold_count' => 85],
            ['id' => 6, 'name' => 'Roma kelapa', 'BRG' => 'BRG-002', 'category' => 'Snack', 'price' => 8000, 'stock' => 81, 'sold_count' => 34],
            ['id' => 7, 'name' => 'Kecap', 'BRG' => 'BRG-003', 'category' => 'Lainnya', 'price' => 8000, 'stock' => 32, 'sold_count' => 25],
        ])->map(function ($product) {
            $product['id'] = (int) $product['id'];
            $product['price'] = (int) $product['price'];
            $product['stock'] = (int) $product['stock'];
            $product['sold_count'] = (int) $product['sold_count'];

            return $product;
        })->values();
    }

    protected function getPreviewCatalogFromSession(): Collection
    {
        $products = session('preview_products', $this->getPreviewProducts()->all());

        return collect($products)
            ->map(function ($product) {
                return [
                    'id' => (int) $product['id'],
                    'name' => $product['name'],
                    'BRG' => $product['BRG'],
                    'category' => $product['category'] ?? 'Lainnya',
                    'price' => (int) $product['price'],
                    'stock' => (int) $product['stock'],
                    'sold_count' => (int) ($product['sold_count'] ?? 0),
                ];
            })
            ->keyBy('id');
    }

    protected function previewCheckout(Request $request)
    {
        $payload = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'string'],
        ]);

        $catalog = $this->getPreviewCatalogFromSession();
        $itemMap = [];
        $subtotal = 0;

        foreach ($payload['items'] as $row) {
            $productId = (int) $row['product_id'];
            $qty = (int) $row['qty'];

            $product = $catalog->get($productId);

            if (!$product) {
                throw ValidationException::withMessages([
                    'items' => "Produk dengan ID {$productId} tidak ditemukan.",
                ]);
            }

            if ($product['stock'] < $qty) {
                throw ValidationException::withMessages([
                    'items' => "Stok {$product['name']} tidak mencukupi.",
                ]);
            }

            if (isset($itemMap[$productId])) {
                $itemMap[$productId]['qty'] += $qty;
                $itemMap[$productId]['subtotal'] = $itemMap[$productId]['qty'] * $itemMap[$productId]['price'];
            } else {
                $itemMap[$productId] = [
                    'id' => $productId,
                    'product_id' => $productId,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'qty' => $qty,
                    'subtotal' => $product['price'] * $qty,
                ];
            }

            $subtotal += $product['price'] * $qty;

            $product['stock'] -= $qty;
            $product['sold_count'] += $qty;
            $catalog->put($productId, $product);
        }

        $order = [
            'id' => (int) now()->format('YmdHis'),
            'order_number' => 'PREVIEW-' . now()->format('YmdHis'),
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'status' => 'paid',
            'payment_method' => $payload['payment_method'],
            'items' => array_values($itemMap),
        ];

        session([
            'preview_order' => $order,
            'preview_products' => $catalog->values()->all(),
        ]);

        $orders = session('preview_orders', []);
        $orders[$order['id']] = $order;
        session(['preview_orders' => $orders]);

        $recommendations = $this->getPreviewRecommendations($order, $catalog);

        return response()->json([
            'message' => 'Pembayaran berhasil.',
            'order' => $order,
            'recommendations' => $recommendations,
        ]);
    }

    protected function previewApplyRecommendations(Request $request, $orderId)
    {
        $payload = $request->validate([
            'product_ids' => ['nullable', 'array', 'max:2'],
            'product_ids.*' => ['integer', 'distinct'],
        ]);

        $order = session('preview_order');

        if (!$order || (string) $order['id'] !== (string) $orderId) {
            return response()->json([
                'message' => 'Order preview tidak ditemukan.',
            ], 404);
        }

        $catalog = $this->getPreviewCatalogFromSession();
        $itemMap = collect($order['items'])->keyBy('product_id')->map(function ($item) {
            return [
                'id' => $item['id'] ?? $item['product_id'],
                'product_id' => (int) $item['product_id'],
                'name' => $item['name'],
                'price' => (int) $item['price'],
                'qty' => (int) $item['qty'],
                'subtotal' => (int) $item['subtotal'],
            ];
        });

        foreach (collect($payload['product_ids'] ?? [])->take(2) as $productId) {
            $productId = (int) $productId;
            $product = $catalog->get($productId);

            if (!$product || $product['stock'] < 1) {
                continue;
            }

            if ($itemMap->has($productId)) {
                $item = $itemMap->get($productId);
                $item['qty'] += 1;
                $item['subtotal'] = $item['qty'] * $item['price'];
                $itemMap->put($productId, $item);
            } else {
                $itemMap->put($productId, [
                    'id' => $productId,
                    'product_id' => $productId,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'qty' => 1,
                    'subtotal' => $product['price'],
                ]);
            }

            $product['stock'] -= 1;
            $product['sold_count'] += 1;
            $catalog->put($productId, $product);
        }

        $items = $itemMap->values()->all();
        $subtotal = collect($items)->sum('subtotal');

        $order['items'] = $items;
        $order['subtotal'] = $subtotal;
        $order['total'] = $subtotal;
        $order['status'] = 'paid';

        session([
            'preview_order' => $order,
            'preview_products' => $catalog->values()->all(),
        ]);

        $orders = session('preview_orders', []);
        $orders[$order['id']] = $order;
        session(['preview_orders' => $orders]);

        return response()->json([
            'message' => 'Order berhasil diperbarui.',
            'order' => $order,
        ]);
    }

    protected function getPreviewRecommendations(array $order, Collection $catalog): array
    {
        $orderedProductIds = collect($order['items'])
            ->pluck('product_id')
            ->map(fn($id) => (int) $id)
            ->values();

        $orderedCategories = $catalog
            ->whereIn('id', $orderedProductIds)
            ->pluck('category')
            ->filter()
            ->unique()
            ->values();

        $relevant = $catalog
            ->when($orderedCategories->isNotEmpty(), function ($items) use ($orderedCategories) {
                return $items->whereIn('category', $orderedCategories);
            })
            ->whereNotIn('id', $orderedProductIds)
            ->filter(fn($product) => (int) $product['stock'] > 0)
            ->sortByDesc('sold_count')
            ->take(2)
            ->values();

        if ($relevant->count() < 2) {
            $fallback = $catalog
                ->whereNotIn('id', $orderedProductIds->merge($relevant->pluck('id'))->all())
                ->filter(fn($product) => (int) $product['stock'] > 0)
                ->sortByDesc('sold_count')
                ->take(2 - $relevant->count())
                ->values();

            $relevant = $relevant->concat($fallback)->values();
        }

        return $relevant
            ->take(2)
            ->map(function ($product) {
                return [
                    'id' => (int) $product['id'],
                    'name' => $product['name'],
                    'price' => (int) $product['price'],
                    'stock' => (int) $product['stock'],
                    'sold_count' => (int) $product['sold_count'],
                    'category' => $product['category'],
                ];
            })
            ->all();
    }

    public function history()
    {
        if ($this->isDatabaseReady(['orders', 'order_items'])) {
            $orders = Order::with('items')->orderBy('created_at', 'desc')->get();
        } else {
            $orders = collect(session('preview_orders', []))->sortByDesc('id')->values();
        }

        return view('kasir.history', compact('orders'));
    }

    public function profile()
    {
        // Mocked or static kasir user for demonstration as requested
        $user = auth()->user() ?? (object) [
            'name' => 'Kasir Utama',
            'email' => 'kasir@danymart.com',
            'usercode' => 'KSR-001'
        ];

        return view('kasir.profile', compact('user'));
    }
}