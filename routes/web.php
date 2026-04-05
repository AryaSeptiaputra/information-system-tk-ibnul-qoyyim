<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing.index');
})->name('landing.index');

// Authentication & Dashboard Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Registration Routes (Protected with auth middleware)
Route::middleware('auth')->prefix('registration')->group(function () {
    Route::get('/create', [RegistrationController::class, 'create'])->name('registration.create');
    Route::post('/', [RegistrationController::class, 'store'])->name('registration.store');
    Route::get('/{registration}', [RegistrationController::class, 'show'])->name('registration.show');
});

// Payment Routes (Protected with auth middleware)
Route::middleware('auth')->prefix('payment')->group(function () {
    Route::get('/student/{student}', [PaymentController::class, 'create'])->name('payment.create');
    Route::post('/', [PaymentController::class, 'store'])->name('payment.store');
    Route::get('/success/{payment}', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/failed/{payment}', [PaymentController::class, 'failed'])->name('payment.failed');
    Route::get('/invoice/{payment}', [PaymentController::class, 'invoice'])->name('payment.invoice');
});

require __DIR__.'/auth.php';
