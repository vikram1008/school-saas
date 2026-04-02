<?php

use App\Http\Middleware\TenantAssetUrl;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Middleware\CheckSubscriptionStatus;
use App\Http\Controllers\Tenant\AuthController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\AcademicYearController;
use App\Http\Controllers\Tenant\ClassController;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    TenantAssetUrl::class,
    CheckSubscriptionStatus::class,
])->group(function () {

    // Auth routes — no auth required
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('tenant.login');
    Route::post('/login', [AuthController::class, 'login'])
        ->name('tenant.login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('tenant.logout');

    // Authenticated tenant routes
    Route::middleware(['auth:tenant'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('tenant.dashboard');

        // Academic Years — Admin only
        Route::middleware(\App\Http\Middleware\TenantAdminOnly::class)->group(function () {

            Route::get('/academic-years', [AcademicYearController::class, 'index'])
                ->name('tenant.academic-years.index');
            Route::post('/academic-years', [AcademicYearController::class, 'store'])
                ->name('tenant.academic-years.store');
            Route::post('/academic-years/{academicYear}/activate', [AcademicYearController::class, 'activate'])
                ->name('tenant.academic-years.activate');
            Route::delete('/academic-years/{academicYear}', [AcademicYearController::class, 'destroy'])
                ->name('tenant.academic-years.destroy');

            // Classes
            Route::get('/classes', [ClassController::class, 'index'])
                ->name('tenant.classes.index');
            Route::post('/classes', [ClassController::class, 'store'])
                ->name('tenant.classes.store');
            Route::put('/classes/{class}', [ClassController::class, 'update'])
                ->name('tenant.classes.update');
            Route::delete('/classes/{class}', [ClassController::class, 'destroy'])
                ->name('tenant.classes.destroy');
            Route::post('/classes/{class}/sections', [ClassController::class, 'storeSection'])
                ->name('tenant.classes.sections.store');
            Route::delete('/classes/{class}/sections/{section}', [ClassController::class, 'destroySection'])
                ->name('tenant.classes.sections.destroy');
            Route::post('/classes/reorder', [ClassController::class, 'reorder'])
                ->name('tenant.classes.reorder');

        });
    });

});