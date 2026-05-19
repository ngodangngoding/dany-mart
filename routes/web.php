<?php

use App\Http\Controllers\AuthPreviewController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthPreviewController::class, 'showLogin'])->name('home');
Route::get('/login-preview', [AuthPreviewController::class, 'showLogin'])->name('login.preview');
Route::post('/login-preview', [AuthPreviewController::class, 'login'])->name('login.preview.store');
Route::post('/logout-preview', [AuthPreviewController::class, 'logout'])->name('logout.preview');

Route::redirect('/dashboard-preview', '/kasir/dashboard')->name('kasir.dashboard.preview');

Route::middleware('role.preview:kasir')->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [OrderController::class, 'create'])->name('dashboard');
    Route::post('/orders/calculate', [OrderController::class, 'calculate'])->name('orders.calculate');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
    Route::get('/history', [OrderController::class, 'index'])->name('history');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
});


Route::middleware('role.preview:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [OrderController::class, 'create'])->name('dashboard');
    Route::post('/orders/calculate', [OrderController::class, 'calculate'])->name('orders.calculate');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/barang', [ProductController::class, 'index'])->name('barang');
    Route::post('/barang', [ProductController::class, 'store'])->name('barang.store');
    Route::get('/barang/search', [ProductController::class, 'search'])->name('barang.search');
    Route::get('/barang/{id}', [ProductController::class, 'show'])->name('barang.show');
    Route::put('/barang/{id}', [ProductController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{id}', [ProductController::class, 'destroy'])->name('barang.destroy');
    Route::post('/barang/{id}/stock', [ProductController::class, 'addStock'])->name('barang.add-stock');
    Route::get('/barang/{id}/stock-histories', [ProductController::class, 'stockHistories'])->name('barang.stock-histories');
    Route::get('/riwayat', [OrderController::class, 'index'])->name('riwayat');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
    Route::get('/laporan', [ReportController::class, 'index'])->name('laporan');
    Route::get('/laporan/summary', [ReportController::class, 'summary'])->name('laporan.summary');
    Route::get('/laporan/sales-chart', [ReportController::class, 'salesChart'])->name('laporan.sales-chart');
    Route::get('/laporan/expense-chart', [ReportController::class, 'expenseChart'])->name('laporan.expense-chart');
    Route::get('/laporan/payment-method', [ReportController::class, 'paymentMethodChart'])->name('laporan.payment-method');
    Route::get('/laporan/top-products', [ReportController::class, 'topProducts'])->name('laporan.top-products');
    Route::get('/laporan/profit', [ReportController::class, 'profitReport'])->name('laporan.profit');
    Route::resource('pengeluaran', ExpenseController::class)->only([
        'index',
        'store',
        'show',
        'update',
        'destroy',
    ])->parameters(['pengeluaran' => 'id']);
    Route::resource('users', UserController::class)->only([
        'index',
        'store',
        'show',
        'update',
        'destroy',
    ])->parameters(['users' => 'id']);
    Route::put('/users/{id}/password', [UserController::class, 'updatePassword'])->name('users.update-password');
    Route::post('/users/{id}/photo', [UserController::class, 'updatePhoto'])->name('users.update-photo');
    Route::resource('kategori', CategoryController::class)->only([
        'index',
        'store',
        'show',
        'update',
        'destroy',
    ])->parameters(['kategori' => 'id']);
});
