<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStockHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $categoryId = $request->query('category_id');
        $units = ['pcs', 'kg', 'L', 'ml', 'pack', 'dus', 'btl', 'sachet', 'renceng'];

        $categories = Category::query()
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->with('category')
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.barang', compact('products', 'categories', 'search', 'categoryId', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'unit' => 'required|in:pcs,kg,L,ml,pack,dus,btl,sachet,renceng',
            'purchase_price' => 'required|integer|min:0',
            'selling_price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
        ], [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori tidak ditemukan.',
            'name.required' => 'Nama barang wajib diisi.',
            'unit.required' => 'Satuan wajib dipilih.',
            'unit.in' => 'Satuan tidak valid.',
            'purchase_price.required' => 'Harga beli wajib diisi.',
            'selling_price.required' => 'Harga jual wajib diisi.',
            'stock.required' => 'Stok awal wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::create($validated);

            if ($product->stock > 0) {
                $userId = Auth::id() ?: User::where('role', 'admin')->value('id');

                if (!$userId) {
                    $userId = User::firstOrCreate(
                        ['username' => 'admin'],
                        [
                            'name' => 'Admin Utama',
                            'email' => 'admin@mail.com',
                            'password' => bcrypt('password'),
                            'role' => 'admin',
                        ]
                    )->id;
                }

                ProductStockHistory::create([
                    'product_id' => $product->id,
                    'user_id' => $userId,
                    'added_stock' => $product->stock,
                    'current_stock' => $product->stock,
                    'note' => 'Stok awal barang',
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.barang')
                ->with('success', 'Barang berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan barang: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        return response()->json([
            'id' => $product->id,
            'category_id' => $product->category_id,
            'category_name' => $product->category ? $product->category->name : null,
            'code' => $product->code,
            'name' => $product->name,
            'unit' => $product->unit,
            'purchase_price' => $product->purchase_price,
            'selling_price' => $product->selling_price,
            'stock' => $product->stock,
        ]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'unit' => 'required|in:pcs,kg,L,ml,pack,dus,btl,sachet,renceng',
            'purchase_price' => 'required|integer|min:0',
            'selling_price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
        ], [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori tidak ditemukan.',
            'name.required' => 'Nama barang wajib diisi.',
            'unit.required' => 'Satuan wajib dipilih.',
            'unit.in' => 'Satuan tidak valid.',
            'purchase_price.required' => 'Harga beli wajib diisi.',
            'selling_price.required' => 'Harga jual wajib diisi.',
            'stock.required' => 'Stok wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            $product->update($validated);

            DB::commit();

            return redirect()
                ->route('admin.barang')
                ->with('success', 'Barang berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui barang: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        try {
            DB::beginTransaction();

            $product->delete();

            DB::commit();

            return redirect()
                ->route('admin.barang')
                ->with('success', 'Barang berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus barang: ' . $e->getMessage());
        }
    }

    public function addStock(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'added_stock' => 'required|integer|min:1',
            'note' => 'nullable|string|max:1000',
        ], [
            'added_stock.required' => 'Jumlah stok wajib diisi.',
            'added_stock.min' => 'Jumlah stok minimal 1.',
        ]);

        try {
            DB::beginTransaction();

            $product->increment('stock', $validated['added_stock']);
            $product->refresh();

            $userId = Auth::id() ?: User::where('role', 'admin')->value('id');

            if (!$userId) {
                $userId = User::firstOrCreate(
                    ['username' => 'admin'],
                    [
                        'name' => 'Admin Utama',
                        'email' => 'admin@mail.com',
                        'password' => bcrypt('password'),
                        'role' => 'admin',
                    ]
                )->id;
            }

            ProductStockHistory::create([
                'product_id' => $product->id,
                'user_id' => $userId,
                'added_stock' => $validated['added_stock'],
                'current_stock' => $product->stock,
                'note' => $validated['note'] ?? null,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.barang')
                ->with('success', 'Stok barang berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan stok: ' . $e->getMessage());
        }
    }

    public function stockHistories($id)
    {
        $product = Product::findOrFail($id);

        $histories = $product->stockHistories()
            ->with('user')
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($history) {
                return [
                    'id' => $history->id,
                    'added_stock' => $history->added_stock,
                    'current_stock' => $history->current_stock,
                    'note' => $history->note,
                    'user' => $history->user ? $history->user->name : '-',
                    'created_at' => $history->created_at ? $history->created_at->format('d/m/Y H:i') : '-',
                ];
            });

        return response()->json([
            'product' => [
                'id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'stock' => $product->stock,
            ],
            'histories' => $histories,
        ]);
    }

    public function search(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $products = Product::query()
            ->with('category')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'code' => $product->code,
                    'name' => $product->name,
                    'category' => $product->category ? $product->category->name : '-',
                    'unit' => $product->unit,
                    'purchase_price' => $product->purchase_price,
                    'selling_price' => $product->selling_price,
                    'stock' => $product->stock,
                ];
            });

        return response()->json($products);
    }
}
