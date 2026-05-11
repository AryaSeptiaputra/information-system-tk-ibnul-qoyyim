<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use App\Models\Facility;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    $facilities = null;
    if (Schema::hasTable('facilities')) {
        $facilities = Facility::query()
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();
    }

    return view('landing.index', [
        'facilities' => $facilities,
    ]);
})->name('landing.index');

// Authentication & Dashboard Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Guest pages (detail pages for dashboard sections)
    Route::get('/dashboard/info', [DashboardController::class, 'guestInfo'])->name('dashboard.info');
    Route::get('/dashboard/students', [DashboardController::class, 'guestStudents'])->name('dashboard.students');
    Route::get('/dashboard/bills', [DashboardController::class, 'guestBills'])->name('dashboard.bills');
    Route::post('/dashboard/bills/{studentPayment}/pay', [DashboardController::class, 'guestBillsPay'])->name('dashboard.bills.pay');
    Route::post('/dashboard/bills/{studentPayment}/installments/{installment}/pay', [DashboardController::class, 'guestBillsInstallmentPay'])->name('dashboard.bills.installments.pay');

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

// Admin Routes
require __DIR__.'/admin.php';

require __DIR__.'/auth.php';
