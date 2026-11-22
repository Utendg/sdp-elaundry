<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student;
use App\Http\Controllers\Worker;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Show up to three real 5★/4★ student reviews with comments, newest first.
    $reviews = \App\Models\Rating::query()
        ->where('direction', \App\Models\Rating::STUDENT_TO_WORKER)
        ->whereNotNull('comment')
        ->where('stars', '>=', 4)
        ->with('ratee')
        ->latest()
        ->take(3)
        ->get();

    return view('welcome', ['reviews' => $reviews]);
})->name('home');

Route::view('/privacy', 'privacy')->name('privacy');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
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

/*
|--------------------------------------------------------------------------
| Worker area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:worker'])
    ->prefix('worker')
    ->name('worker.')
    ->group(function () {
        // Profile is viewable/editable while the account awaits admin approval.
        Route::get('/profile', [Worker\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [Worker\ProfileController::class, 'update'])->name('profile.update');

        // Everything else (availability, orders, notes, ratings) requires admin approval.
        Route::middleware('worker.approved')->group(function () {
            Route::post('/profile/availability', [Worker\ProfileController::class, 'toggleAvailability'])->name('profile.availability');
            Route::get('/orders', [Worker\OrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [Worker\OrderController::class, 'show'])->name('orders.show');
            Route::post('/orders/{order}/accept', [Worker\OrderController::class, 'accept'])->name('orders.accept');
            Route::post('/orders/{order}/reject', [Worker\OrderController::class, 'reject'])->name('orders.reject');
            Route::post('/orders/{order}/advance', [Worker\OrderController::class, 'advance'])->name('orders.advance');
            Route::post('/orders/{order}/rate', [Worker\OrderController::class, 'rateStudent'])->name('orders.rate');
            Route::post('/orders/{order}/notes', [Worker\OrderController::class, 'addNote'])->name('orders.notes.store');
        });
    });

/*
|--------------------------------------------------------------------------
| Admin area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Each area needs the matching responsibility, assigned by the Head of Residence Life.

        // Admin accounts and responsibilities (Head of Residence Life only)
        Route::middleware('admin.permission:admins')->group(function () {
            Route::get('/admins', [Admin\AdminAccountController::class, 'index'])->name('admins.index');
            Route::post('/admins', [Admin\AdminAccountController::class, 'store'])->name('admins.store');
            Route::patch('/admins/{admin}', [Admin\AdminAccountController::class, 'update'])->name('admins.update');
            Route::post('/admins/{admin}/toggle', [Admin\AdminAccountController::class, 'toggle'])->name('admins.toggle');
        });

        // Worker approvals
        Route::middleware('admin.permission:workers')->group(function () {
            Route::get('/workers', [Admin\WorkerController::class, 'index'])->name('workers.index');
            Route::post('/workers/{worker}/approve', [Admin\WorkerController::class, 'approve'])->name('workers.approve');
            Route::post('/workers/{worker}/decline', [Admin\WorkerController::class, 'decline'])->name('workers.decline');
            Route::post('/workers/{worker}/revoke', [Admin\WorkerController::class, 'revoke'])->name('workers.revoke');
        });

        // Price list
        Route::middleware('admin.permission:prices')->group(function () {
            Route::get('/service-items', [Admin\ServiceItemController::class, 'index'])->name('service-items.index');
            Route::post('/service-items', [Admin\ServiceItemController::class, 'store'])->name('service-items.store');
            Route::patch('/service-items/{serviceItem}', [Admin\ServiceItemController::class, 'update'])->name('service-items.update');
            Route::post('/service-items/{serviceItem}/toggle', [Admin\ServiceItemController::class, 'toggle'])->name('service-items.toggle');
            Route::delete('/service-items/{serviceItem}', [Admin\ServiceItemController::class, 'destroy'])->name('service-items.destroy');
        });

        // Dorms
        Route::middleware('admin.permission:dorms')->group(function () {
            Route::get('/dorms', [Admin\DormController::class, 'index'])->name('dorms.index');
            Route::post('/dorms', [Admin\DormController::class, 'store'])->name('dorms.store');
            Route::patch('/dorms/{dorm}', [Admin\DormController::class, 'update'])->name('dorms.update');
            Route::post('/dorms/{dorm}/toggle', [Admin\DormController::class, 'toggle'])->name('dorms.toggle');
        });

        // Orders (monitoring)
        Route::middleware('admin.permission:orders')->group(function () {
            Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
        });

        // Complaints
        Route::middleware('admin.permission:complaints')->group(function () {
            Route::get('/complaints', [Admin\ComplaintController::class, 'index'])->name('complaints.index');
            Route::patch('/complaints/{complaint}', [Admin\ComplaintController::class, 'update'])->name('complaints.update');
        });

    });

require __DIR__.'/auth.php';
