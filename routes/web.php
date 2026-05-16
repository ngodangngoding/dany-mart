<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthPreviewController;
use App\Http\Controllers\KasirController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthPreviewController::class, 'showLogin'])->name('home');
Route::get('/login-preview', [AuthPreviewController::class, 'showLogin'])->name('login.preview');
Route::post('/login-preview', [AuthPreviewController::class, 'login'])->name('login.preview.store');
Route::post('/logout-preview', [AuthPreviewController::class, 'logout'])->name('logout.preview');

Route::redirect('/dashboard-preview', '/kasir/dashboard')->name('kasir.dashboard.preview');

Route::middleware('role.preview:kasir')->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [KasirController::class, 'index'])->name('dashboard');
    Route::get('/history', [KasirController::class, 'history'])->name('history');
    Route::get('/profile', [KasirController::class, 'profile'])->name('profile');
});

Route::middleware('role.preview:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/barang', [AdminController::class, 'barang'])->name('barang');
    Route::get('/riwayat', [AdminController::class, 'riwayat'])->name('riwayat');
    Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
    Route::get('/pengeluaran', [AdminController::class, 'pengeluaran'])->name('pengeluaran');
    Route::get('/pengaturan', [AdminController::class, 'pengaturan'])->name('pengaturan');
    Route::get('/manajemen-akun', [AdminController::class, 'manajemenAkun'])->name('manajemen-akun');
    Route::get('/kategori', [AdminController::class, 'kategori'])->name('kategori');
});
