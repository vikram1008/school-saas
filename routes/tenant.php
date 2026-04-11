<?php

use App\Http\Controllers\Tenant\AcademicYearController;
use App\Http\Controllers\Tenant\AuthController;
use App\Http\Controllers\Tenant\ClassController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\FeeHeadController;
use App\Http\Controllers\Tenant\FeeStructureController;
use App\Http\Controllers\Tenant\FeeCollectionController;
use App\Http\Controllers\Tenant\SchoolHomeController;
use App\Http\Controllers\Tenant\StaffController;
use App\Http\Controllers\Tenant\StudentController;
use App\Http\Middleware\CheckSubscriptionStatus;
use App\Http\Middleware\TenantAdminOnly;
use App\Http\Middleware\TenantAssetUrl;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers\Tenant\StudentAttendanceController;
use App\Http\Controllers\Tenant\StaffAttendanceController;
use App\Http\Controllers\Tenant\AttendanceReportController;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    TenantAssetUrl::class,
    CheckSubscriptionStatus::class,
])->group(function () {
    
    /*
    |-----------------------------------------------------------
    | PUBLIC — School website (no auth required)
    |-----------------------------------------------------------
    */
    Route::get('/', [SchoolHomeController::class, 'index'])
        ->name('tenant.home');
 
    Route::post('/contact', [SchoolHomeController::class, 'contact'])
        ->name('tenant.home.contact');

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
        Route::middleware(TenantAdminOnly::class)->group(function () {

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

            // Students
            Route::get('/students', [StudentController::class, 'index'])
                ->name('tenant.students.index');
            Route::get('/students/create', [StudentController::class, 'create'])
                ->name('tenant.students.create');
            Route::post('/students', [StudentController::class, 'store'])
                ->name('tenant.students.store');
            Route::get('/students/{student}', [StudentController::class, 'show'])
                ->name('tenant.students.show');
            Route::get('/students/{student}/edit', [StudentController::class, 'edit'])
                ->name('tenant.students.edit');
            Route::put('/students/{student}', [StudentController::class, 'update'])
                ->name('tenant.students.update');
            Route::delete('/students/{student}', [StudentController::class, 'destroy'])
                ->name('tenant.students.destroy');
            Route::patch('/students/{student}/status', [StudentController::class, 'updateStatus'])
                ->name('tenant.students.status');
            Route::patch('/students/documents/{document}/verify', [StudentController::class, 'verifyDocument'])
                ->name('tenant.students.documents.verify');

            // Ajax
            Route::get('/classes/{class}/sections', [StudentController::class, 'getSections'])
                ->name('tenant.classes.get-sections');

            // Staff
            Route::get('/staff', [StaffController::class, 'index'])
                ->name('tenant.staff.index');
            Route::get('/staff/create', [StaffController::class, 'create'])
                ->name('tenant.staff.create');
            Route::post('/staff', [StaffController::class, 'store'])
                ->name('tenant.staff.store');
            Route::get('/staff/{staff}', [StaffController::class, 'show'])
                ->name('tenant.staff.show');
            Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])
                ->name('tenant.staff.edit');
            Route::put('/staff/{staff}', [StaffController::class, 'update'])
                ->name('tenant.staff.update');
            Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])
                ->name('tenant.staff.destroy');
            Route::patch('/staff/{staff}/status', [StaffController::class, 'updateStatus'])
                ->name('tenant.staff.status');
            Route::patch('/staff/documents/{document}/verify', [StaffController::class, 'verifyDocument'])
                ->name('tenant.staff.documents.verify');


            // Fee Heads
            Route::prefix('fees')->name('tenant.fees.')->group(function () {

                // Fee Heads
                Route::get('/heads', [FeeHeadController::class, 'index'])
                    ->name('heads.index');
                Route::post('/heads', [FeeHeadController::class, 'store'])
                    ->name('heads.store');
                Route::put('/heads/{feeHead}', [FeeHeadController::class, 'update'])
                    ->name('heads.update');
                Route::delete('/heads/{feeHead}', [FeeHeadController::class, 'destroy'])
                    ->name('heads.destroy');

                // Fee Structures
                Route::get('/structures', [FeeStructureController::class, 'index'])
                    ->name('structures.index');
                Route::post('/structures', [FeeStructureController::class, 'store'])
                    ->name('structures.store');
                Route::delete('/structures/{feeStructure}', [FeeStructureController::class, 'destroy'])
                    ->name('structures.destroy');

                // Collections
                Route::get('/collections', [FeeCollectionController::class, 'index'])
                    ->name('collections.index');
                Route::get('/collections/create', [FeeCollectionController::class, 'create'])
                    ->name('collections.create');
                Route::post('/collections', [FeeCollectionController::class, 'store'])
                    ->name('collections.store');
                Route::get('/collections/ledger', [FeeCollectionController::class, 'studentLedger'])
                    ->name('collections.ledger');
                Route::get('/collections/receipt/{feeCollection}', [FeeCollectionController::class, 'receipt'])
                    ->name('receipt');
                Route::post('/collections/generate-demands', [FeeCollectionController::class, 'generateDemands'])
                    ->name('collections.generate-demands');
                Route::patch('/collections/demands/{demand}/waive', [FeeCollectionController::class, 'waiveDemand'])
                    ->name('collections.waive-demand');

                // Ajax
                Route::get('/students/search', [FeeCollectionController::class, 'searchStudents'])
                    ->name('students.search');
            });


            Route::prefix('attendance')->name('tenant.attendance.')->group(function () {

                // Student Attendance
                Route::get('/students', [StudentAttendanceController::class, 'index'])
                    ->name('students.index');
                Route::post('/students', [StudentAttendanceController::class, 'store'])
                    ->name('students.store');

                // Staff Attendance
                Route::get('/staff', [StaffAttendanceController::class, 'index'])
                    ->name('staff.index');
                Route::post('/staff', [StaffAttendanceController::class, 'store'])
                    ->name('staff.store');

                // Reports
                Route::get('/reports/daily', [AttendanceReportController::class, 'dailySummary'])
                    ->name('reports.daily');
                Route::get('/reports/students/monthly', [AttendanceReportController::class, 'studentMonthly'])
                    ->name('reports.students.monthly');
                Route::get('/reports/staff/monthly', [AttendanceReportController::class, 'staffMonthly'])
                    ->name('reports.staff.monthly');
            });

        });
    });

});
