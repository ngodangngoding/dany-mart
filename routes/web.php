<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KasirController;

Route::get('/', [KasirController::class, 'index'])->name('kasir.index');

Route::get('/login-preview', function () {
    return view('welcome');
})->name('login.preview');

Route::post('/login-preview', function () {
    return redirect()->route('kasir.dashboard');
})->name('login.preview.submit');

Route::get('/dashboard-preview', [KasirController::class, 'index'])->name('kasir.dashboard');

Route::post('/kasir/checkout', [KasirController::class, 'checkout'])->name('kasir.checkout');

Route::post('/kasir/orders/{order}/recommendations/apply', [KasirController::class, 'applyRecommendations'])
    ->name('kasir.recommendations.apply');

Route::get('/kasir/history', [KasirController::class, 'history'])->name('kasir.history');
Route::get('/kasir/profile', [KasirController::class, 'profile'])->name('kasir.profile');