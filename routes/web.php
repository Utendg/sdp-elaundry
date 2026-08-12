<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Student area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        // Browse laundry workers
        Route::get('/workers', [Student\WorkerBrowseController::class, 'index'])->name('workers.index');
        Route::get('/workers/{worker}', [Student\WorkerBrowseController::class, 'show'])->name('workers.show');

        // Orders
        Route::get('/orders', [Student\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [Student\OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [Student\OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [Student\OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/cancel', [Student\OrderController::class, 'cancel'])->name('orders.cancel');

        // Ratings & complaints
        Route::post('/orders/{order}/rate', [Student\RatingController::class, 'store'])->name('orders.rate');
        Route::get('/complaints', [Student\ComplaintController::class, 'index'])->name('complaints.index');
        Route::post('/complaints', [Student\ComplaintController::class, 'store'])->name('complaints.store');
    });

require __DIR__.'/auth.php';
