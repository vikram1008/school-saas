<?php

use App\Http\Controllers\SuperAdmin\SettingsController;
use App\Http\Controllers\SuperAdmin\SubscriptionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pages\HomePage;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\AuthController as SuperAdminAuth;
use App\Http\Controllers\SuperAdmin\SchoolController; 

Route::get('/', [HomePage::class, 'index'])->name('home');

Route::prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/login', [SuperAdminAuth::class, 'showLoginForm'])->name('login');
    Route::post('/login', [SuperAdminAuth::class, 'login'])->name('login.submit');

    Route::middleware(['auth', 'role:Super Admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [SuperAdminAuth::class, 'logout'])->name('logout');

        Route::resource('schools', SchoolController::class);


        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

        // Subscriptions
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])
            ->name('subscriptions.index');
        Route::get('/subscriptions/{school}', [SubscriptionController::class, 'show'])
            ->name('subscriptions.show');
        Route::post('/subscriptions/{subscription}/mark-paid', [SubscriptionController::class, 'markPaid'])
            ->name('subscriptions.mark-paid');
        Route::post('/subscriptions/{subscription}/waive', [SubscriptionController::class, 'waive'])
            ->name('subscriptions.waive');
        Route::post('/subscriptions/{school}/reactivate', [SubscriptionController::class, 'reactivate'])
            ->name('subscriptions.reactivate');
        Route::post('/subscriptions/run-check', [SubscriptionController::class, 'runCheck'])
            ->name('subscriptions.run-check');
    });
});

// Illuminate\Support\Facades\DB