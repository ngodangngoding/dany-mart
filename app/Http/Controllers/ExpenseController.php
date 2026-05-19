<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $category = $request->query('category', '');
        $dateFrom = $request->query('date_from', '');
        $dateTo = $request->query('date_to', '');

        $expenses = Expense::with('user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('description', 'like', "%{$search}%");
            })
            ->when($category !== '', function ($query) use ($category) {
                $query->where('expense_category', $category);
            })
            ->when($dateFrom !== '', function ($query) use ($dateFrom) {
                $query->whereDate('date', '>=', $dateFrom);
            })
            ->when($dateTo !== '', function ($query) use ($dateTo) {
                $query->whereDate('date', '<=', $dateTo);
            })
            ->orderBy('date', 'desc')
            ->paginate(10)
            ->withQueryString();

        $totalAmount = Expense::when($category !== '', function ($query) use ($category) {
            $query->where('expense_category', $category);
        })
            ->when($dateFrom !== '', function ($query) use ($dateFrom) {
                $query->whereDate('date', '>=', $dateFrom);
            })
            ->when($dateTo !== '', function ($query) use ($dateTo) {
                $query->whereDate('date', '<=', $dateTo);
            })
            ->sum('amount');

        $categories = ['Listrik', 'Gaji', 'Perlengkapan', 'Sewa'];

        return view('admin.pengeluaran', compact('expenses', 'totalAmount', 'categories', 'search', 'category', 'dateFrom', 'dateTo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'expense_category' => 'required|string|in:Listrik,Gaji,Perlengkapan,Sewa',
            'description' => 'nullable|string|max:500',
            'amount' => 'required|numeric|min:1',
        ], [
            'date.required' => 'Tanggal wajib diisi.',
            'expense_category.required' => 'Kategori pengeluaran wajib dipilih.',
            'expense_category.in' => 'Kategori pengeluaran tidak valid.',
            'amount.required' => 'Nominal pengeluaran wajib diisi.',
            'amount.min' => 'Nominal pengeluaran harus lebih dari 0.',
        ]);

        try {
            DB::beginTransaction();

            Expense::create([
                'user_id' => Auth::id(),
                'date' => $request->date,
                'expense_category' => $request->expense_category,
                'description' => $request->description,
                'amount' => $request->amount,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.pengeluaran.index')
                ->with('success', 'Pengeluaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan pengeluaran: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $expense = Expense::with('user')->findOrFail($id);

        return response()->json([
            'id' => $expense->id,
            'date' => $expense->date,
            'expense_category' => $expense->expense_category,
            'description' => $expense->description,
            'amount' => $expense->amount,
            'user_name' => $expense->user->name ?? '-',
        ]);
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $request->validate([
            'date' => 'required|date',
            'expense_category' => 'required|string|in:Listrik,Gaji,Perlengkapan,Sewa',
            'description' => 'nullable|string|max:500',
            'amount' => 'required|numeric|min:1',
        ], [
            'date.required' => 'Tanggal wajib diisi.',
            'expense_category.required' => 'Kategori pengeluaran wajib dipilih.',
            'expense_category.in' => 'Kategori pengeluaran tidak valid.',
            'amount.required' => 'Nominal pengeluaran wajib diisi.',
            'amount.min' => 'Nominal pengeluaran harus lebih dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $expense->update([
                'date' => $request->date,
                'expense_category' => $request->expense_category,
                'description' => $request->description,
                'amount' => $request->amount,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.pengeluaran.index')
                ->with('success', 'Pengeluaran berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui pengeluaran: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);

        try {
            DB::beginTransaction();

            $expense->delete();

            DB::commit();

            return redirect()
                ->route('admin.pengeluaran.index')
                ->with('success', 'Pengeluaran berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus pengeluaran: ' . $e->getMessage());
        }
    }
}
