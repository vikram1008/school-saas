<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pages\HomePage;
use App\Http\Controllers\SuperAdmin\DashboardController;

// Route::get('/', function () {
//     return redirect()->route('superadmin.dashboard');
// });

Route::get('/', [HomePage::class, 'index'])->name('home');

Route::prefix('superadmin')->group(function () {
    // Ab yahan folder wala controller use hoga
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('superadmin.dashboard');
});