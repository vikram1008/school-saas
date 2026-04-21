<?php

use App\Http\Controllers\Tenant\AcademicYearController;
use App\Http\Controllers\Tenant\AuthController;
use App\Http\Controllers\Tenant\ClassController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\FeeHeadController;
use App\Http\Controllers\Tenant\FeeStructureController;
use App\Http\Controllers\Tenant\FeeCollectionController;
use App\Http\Controllers\Tenant\NoticeController;
use App\Http\Controllers\Tenant\ParentController;
use App\Http\Controllers\Tenant\ParentPortalController;
use App\Http\Controllers\Tenant\SchoolHomeController;
use App\Http\Controllers\Tenant\SchoolSettingsController;
use App\Http\Controllers\Tenant\StaffController;
use App\Http\Controllers\Tenant\StudentController;
use App\Http\Controllers\Tenant\SubjectController;
use App\Http\Controllers\Tenant\TimetableController;
use App\Http\Middleware\CheckSubscriptionStatus;
use App\Http\Middleware\TenantAdminOnly;
use App\Http\Middleware\TenantAssetUrl;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers\Tenant\StudentAttendanceController;
use App\Http\Controllers\Tenant\StaffAttendanceController;
use App\Http\Controllers\Tenant\AttendanceReportController;
use App\Http\Controllers\Tenant\ExamController;
use App\Http\Controllers\Tenant\MarksController;
use App\Http\Controllers\Tenant\ReportCardController;
use App\Http\Controllers\Tenant\GradeScaleController;

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

        // Notices (admin)
        Route::get('/notices', [NoticeController::class, 'index'])
            ->name('tenant.notices.index');
        Route::post('/notices', [NoticeController::class, 'store'])
            ->name('tenant.notices.store');
        Route::put('/notices/{notice}', [NoticeController::class, 'update'])
            ->name('tenant.notices.update');
        Route::delete('/notices/{notice}', [NoticeController::class, 'destroy'])
            ->name('tenant.notices.destroy');

        // Admin only
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
            // Class Subjects
            Route::post('/classes/{schoolClass}/subjects', [ClassController::class, 'storeSubject'])
                ->name('tenant.classes.subjects.store');
            Route::patch('/classes/subjects/{subject}/teacher', [ClassController::class, 'updateSubjectTeacher'])
                ->name('tenant.classes.subjects.update-teacher');
            Route::delete('/classes/subjects/{subject}', [ClassController::class, 'destroySubject'])
                ->name('tenant.classes.subjects.destroy');

            // Ajax: get subjects for a class (used by exam, timetable, marks)
            Route::get('/classes/{classId}/subjects', function ($classId) {
                return response()->json(
                    \App\Models\ClassSubject::where('class_id', $classId)
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->get(['id', 'subject_name', 'subject_name_hi', 'teacher_id'])
                );
            })->name('tenant.classes.subjects.list');

            Route::prefix('settings')->name('tenant.settings.')->group(function () {
                Route::get('/school', [SchoolSettingsController::class, 'edit'])
                    ->name('school.edit');
                Route::put('/school', [SchoolSettingsController::class, 'update'])
                    ->name('school.update');
            });

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

            Route::prefix('subjects')->name('tenant.subjects.')->middleware(['auth:tenant'])->group(function () {
                Route::get('/', [SubjectController::class, 'index'])->name('index');
                Route::post('/', [SubjectController::class, 'store'])->name('store');
                Route::put('/{subject}', [SubjectController::class, 'update'])->name('update');
                Route::delete('/{subject}', [SubjectController::class, 'destroy'])->name('destroy');

                Route::get('/assign', [SubjectController::class, 'assign'])->name('assign');
                Route::post('/assign', [SubjectController::class, 'saveAssign'])->name('assign.save');
                Route::delete('/assign/{subject}', [SubjectController::class, 'removeAssign'])->name('assign.remove');
            });

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


            // Attendance
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

            // Timetable
            Route::prefix('timetable')->name('tenant.timetable.')->middleware(['auth:tenant'])->group(function () {

                // Class timetable grid
                Route::get('/', [TimetableController::class, 'index'])
                    ->name('index');
                Route::post('/entries', [TimetableController::class, 'saveEntry'])
                    ->name('entries.save');
                Route::delete('/entries/{entry}', [TimetableController::class, 'deleteEntry'])
                    ->name('entries.delete');

                // Slot management
                Route::get('/slots', [TimetableController::class, 'slots'])
                    ->name('slots');
                Route::post('/slots', [TimetableController::class, 'storeSlot'])
                    ->name('slots.store');
                Route::delete('/slots/{slot}', [TimetableController::class, 'destroySlot'])
                    ->name('slots.destroy');

                // Teacher view
                Route::get('/teacher', [TimetableController::class, 'teacherView'])
                    ->name('teacher');

                // Print
                Route::get('/print', [TimetableController::class, 'print'])
                    ->name('print');

                // Ajax
                Route::get('/teacher-free-slots', [TimetableController::class, 'teacherFreeSlots'])
                    ->name('teacher-free-slots');
            });

            // Results and Exams
            Route::prefix('results')->name('tenant.results.')->middleware(['auth:tenant'])->group(function () {

                // Exams
                Route::get('/exams', [ExamController::class, 'index'])
                    ->name('exams.index');
                Route::post('/exams', [ExamController::class, 'store'])
                    ->name('exams.store');
                Route::put('/exams/{exam}', [ExamController::class, 'update'])
                    ->name('exams.update');
                Route::delete('/exams/{exam}', [ExamController::class, 'destroy'])
                    ->name('exams.destroy');

                // Exam Subjects
                Route::get('/exams/{exam}/subjects', [ExamController::class, 'subjects'])
                    ->name('exams.subjects');
                Route::post('/exams/{exam}/subjects', [ExamController::class, 'storeSubject'])
                    ->name('exams.subjects.store');
                Route::delete('/exams/{exam}/subjects/{subject}', [ExamController::class, 'destroySubject'])
                    ->name('exams.subjects.destroy');

                // Marks Entry
                Route::get('/marks', [MarksController::class, 'index'])
                    ->name('marks.index');
                Route::post('/marks', [MarksController::class, 'store'])
                    ->name('marks.store');

                // Report Cards
                Route::get('/report-cards', [ReportCardController::class, 'classResults'])
                    ->name('report-cards.index');
                Route::get('/report-cards/{student}/print', [ReportCardController::class, 'print'])
                    ->name('report-cards.print');

                // Grade Scales
                Route::get('/grade-scales', [GradeScaleController::class, 'index'])
                    ->name('grade-scales.index');
                Route::post('/grade-scales', [GradeScaleController::class, 'store'])
                    ->name('grade-scales.store');
                Route::delete('/grade-scales/{gradeScale}', [GradeScaleController::class, 'destroy'])
                    ->name('grade-scales.destroy');
                Route::post('/grade-scales/apply-default', [GradeScaleController::class, 'applyDefault'])
                    ->name('grade-scales.apply-default');
            });

            // Parent management (admin only)
            Route::get('/parents', [ParentController::class, 'index'])
                ->name('tenant.parents.index');
            Route::get('/parents/{parent}', [ParentController::class, 'show'])
                ->name('tenant.parents.show');
            Route::get('/parents/{parent}/edit', [ParentController::class, 'edit'])
                ->name('tenant.parents.edit');
            Route::put('/parents/{parent}', [ParentController::class, 'update'])
                ->name('tenant.parents.update');
            Route::post('/parents/{parent}/reset-password', [ParentController::class, 'resetPassword'])
                ->name('tenant.parents.reset-password');
            Route::post('/parents/{parent}/link-student', [ParentController::class, 'linkStudent'])
                ->name('tenant.parents.link-student');
            Route::delete('/parents/{parent}/unlink/{student}', [ParentController::class, 'unlinkStudent'])
                ->name('tenant.parents.unlink-student');

        });

        // Parent Portal (parents only)
        Route::prefix('parent-portal')->name('tenant.parent-portal.')->middleware('parent.only')->group(function () {
            Route::get('/dashboard', [ParentPortalController::class, 'dashboard'])
                ->name('dashboard');
            Route::get('/attendance/{studentId}', [ParentPortalController::class, 'childAttendance'])
                ->name('attendance');
            Route::get('/fees/{studentId}', [ParentPortalController::class, 'childFees'])
                ->name('fees');
            Route::get('/notices', [ParentPortalController::class, 'notices'])
                ->name('notices');
        });

    });

});


Route::get('/translate', function (\Illuminate\Http\Request $request) {
    $text = $request->q;
    $to   = $request->to ?? 'hi';

    if (!$text) return response()->json(['text' => '']);

    try {
        $response = \Illuminate\Support\Facades\Http::get(
            'https://translate.googleapis.com/translate_a/single', [
                'client' => 'gtx',
                'sl'     => 'en',
                'tl'     => $to,
                'dt'     => 't',
                'q'      => $text,
            ]
        );

        $translated = $response->json()[0][0][0] ?? '';
        return response()->json(['text' => $translated]);

    } catch (\Exception $e) {
        return response()->json(['text' => '']);
    }
})->name('tenant.translate')->middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
]);