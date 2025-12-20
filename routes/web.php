<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\DesignTypeController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PortfolioController;

Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])
        ->name('invoices.show');
    Route::post('/invoices/{invoice}/verify', [InvoiceController::class, 'verify'])
        ->name('invoices.verify');
    Route::post('/invoices/{invoice}/reject', [InvoiceController::class, 'reject'])
        ->name('invoices.reject');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::get('/orders', [AdminOrderController::class, 'index'])
        ->name('admin.orders.index');

    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])
        ->name('admin.orders.show');

    Route::post('/orders/{order}/upload', [AdminOrderController::class, 'uploadFile'])
    ->name('admin.orders.upload');

    Route::get('/orders/check', [AdminOrderController::class, 'check'])
    ->name('admin.orders.check');

    Route::get('/payments', [PaymentController::class, 'index'])
        ->name('admin.payments.index');

    Route::post('/payments/{invoice_id}/approve', [PaymentController::class, 'approve'])
        ->name('admin.payments.approve');

    Route::post('/payments/{invoice_id}/reject', [PaymentController::class, 'reject'])
        ->name('admin.payments.reject');

    Route::resource('design-types', DesignTypeController::class);
    Route::patch('design-types/{id}/toggle', [DesignTypeController::class, 'toggle'])
    ->name('design-types.toggle');
});

Route::middleware('auth')->group(function () {
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])
        ->name('invoices.show');
});

require __DIR__.'/auth.php';
