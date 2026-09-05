<?php

use App\Http\Controllers\Tenant\AcademicYearController;
use App\Http\Controllers\Tenant\AttendanceReportController;
use App\Http\Controllers\Tenant\AuthController;
use App\Http\Controllers\Tenant\ClassController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\ExamController;
use App\Http\Controllers\Tenant\FeeCollectionController;
use App\Http\Controllers\Tenant\FeeHeadController;
use App\Http\Controllers\Tenant\FeeStructureController;
use App\Http\Controllers\Tenant\GradeScaleController;
use App\Http\Controllers\Tenant\LeaveController;
use App\Http\Controllers\Tenant\LibraryController;
use App\Http\Controllers\Tenant\MarksController;
use App\Http\Controllers\Tenant\NoticeController;
use App\Http\Controllers\Tenant\NotificationController;
use App\Http\Controllers\Tenant\ParentController;
use App\Http\Controllers\Tenant\ParentPortalController;
use App\Http\Controllers\Tenant\ReportCardController;
use App\Http\Controllers\Tenant\SchoolHomeController;
use App\Http\Controllers\Tenant\SchoolSettingsController;
use App\Http\Controllers\Tenant\StaffAttendanceController;
use App\Http\Controllers\Tenant\StaffController;
use App\Http\Controllers\Tenant\StaffDashboardController;
use App\Http\Controllers\Tenant\StaffPermissionsController;
use App\Http\Controllers\Tenant\StudentAttendanceController;
use App\Http\Controllers\Tenant\StudentController;
use App\Http\Controllers\Tenant\SubjectController;
use App\Http\Controllers\Tenant\TimetableController;
use App\Http\Middleware\CheckSubscriptionStatus;
use App\Http\Middleware\TenantAdminOnly;
use App\Http\Middleware\TenantAssetUrl;
use App\Models\ClassSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

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

        // Dashboard — role-based routing
        Route::get('/dashboard', function () {
            $user = Auth::guard('tenant')->user();
            if ($user && $user->isParent()) {
                return redirect()->route('tenant.parent-portal.dashboard');
            }
            if ($user && $user->isStaff()) {
                return redirect()->route('tenant.staff.dashboard');
            }

            return app(DashboardController::class)->index();
        })->name('tenant.dashboard');

        // Staff Dashboard
        Route::get('/staff-dashboard', [StaffDashboardController::class, 'index'])
            ->name('tenant.staff.dashboard');

        /*
        |-----------------------------------------------------------
        | SHARED — Admin + Staff (not parents)
        | Each controller method enforces its own fine-grained check
        |-----------------------------------------------------------
        */
        Route::middleware('not.parent')->group(function () {

            // ── Notices ─────────────────────────────────────────────
            Route::get('/notices', [NoticeController::class, 'index'])
                ->name('tenant.notices.index');
            Route::post('/notices', [NoticeController::class, 'store'])
                ->name('tenant.notices.store');
            Route::put('/notices/{notice}', [NoticeController::class, 'update'])
                ->name('tenant.notices.update');
            Route::delete('/notices/{notice}', [NoticeController::class, 'destroy'])
                ->name('tenant.notices.destroy');

            // ── Student Attendance ───────────────────────────────────
            Route::get('/attendance/students', [StudentAttendanceController::class, 'index'])
                ->name('tenant.attendance.students.index');
            Route::post('/attendance/students', [StudentAttendanceController::class, 'store'])
                ->name('tenant.attendance.students.store');

            // ── Staff Attendance ─────────────────────────────────────
            Route::get('/attendance/staff', [StaffAttendanceController::class, 'index'])
                ->name('tenant.attendance.staff.index');
            Route::post('/attendance/staff', [StaffAttendanceController::class, 'store'])
                ->name('tenant.attendance.staff.store');

            // ── Attendance Reports ───────────────────────────────────
            Route::get('/attendance/reports/daily', [AttendanceReportController::class, 'dailySummary'])
                ->name('tenant.attendance.reports.daily');
            Route::get('/attendance/reports/students/monthly', [AttendanceReportController::class, 'studentMonthly'])
                ->name('tenant.attendance.reports.students.monthly');
            Route::get('/attendance/reports/staff/monthly', [AttendanceReportController::class, 'staffMonthly'])
                ->name('tenant.attendance.reports.staff.monthly');

            // ── Students (view = staff, create/edit/delete = admin only, enforced in controller) ──
            Route::get('/students', [StudentController::class, 'index'])
                ->name('tenant.students.index');
            Route::get('/students/create', [StudentController::class, 'create'])
                ->name('tenant.students.create');
            Route::post('/students', [StudentController::class, 'store'])
                ->name('tenant.students.store');

            // ── Student Import / Export (must be before {student} wildcard) ──
            Route::get('/students/export', [StudentController::class, 'export'])
                ->name('tenant.students.export');
            Route::post('/students/import', [StudentController::class, 'import'])
                ->name('tenant.students.import');
            Route::get('/students/import/template', [StudentController::class, 'importTemplate'])
                ->name('tenant.students.import.template');
            Route::patch('/students/documents/{document}/verify', [StudentController::class, 'verifyDocument'])
                ->name('tenant.students.documents.verify');

            // ── Student wildcard routes ──────────────────────────────────────
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
            Route::post('/students/{student}/reset-password', [StudentController::class, 'resetPassword'])
                ->name('tenant.students.reset-password');

            // Ajax: sections for a class
            Route::get('/classes/{class}/sections', [StudentController::class, 'getSections'])
                ->name('tenant.classes.get-sections');

            // ── Parents (view = staff, edit/write = admin only, enforced in controller) ──
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

            // ── Fee Collections (view-reports = staff with can_view_fee_reports, collect = can_collect_fees) ──
            Route::prefix('fees')->name('tenant.fees.')->group(function () {
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

            // ── Marks Entry ──────────────────────────────────────────
            Route::get('/results/marks', [MarksController::class, 'index'])
                ->name('tenant.results.marks.index');
            Route::post('/results/marks', [MarksController::class, 'store'])
                ->name('tenant.results.marks.store');

            // ── Exams (view = staff, create/edit/delete = admin only) ──
            Route::get('/results/exams', [ExamController::class, 'index'])
                ->name('tenant.results.exams.index');
            Route::post('/results/exams', [ExamController::class, 'store'])
                ->name('tenant.results.exams.store');
            Route::put('/results/exams/{exam}', [ExamController::class, 'update'])
                ->name('tenant.results.exams.update');
            Route::delete('/results/exams/{exam}', [ExamController::class, 'destroy'])
                ->name('tenant.results.exams.destroy');
            Route::get('/results/exams/{exam}/subjects', [ExamController::class, 'subjects'])
                ->name('tenant.results.exams.subjects');
            Route::post('/results/exams/{exam}/subjects', [ExamController::class, 'storeSubject'])
                ->name('tenant.results.exams.subjects.store');
            Route::delete('/results/exams/{exam}/subjects/{subject}', [ExamController::class, 'destroySubject'])
                ->name('tenant.results.exams.subjects.destroy');

            // ── Report Cards ─────────────────────────────────────────
            Route::get('/results/report-cards', [ReportCardController::class, 'classResults'])
                ->name('tenant.results.report-cards.index');
            Route::get('/results/report-cards/{student}/print', [ReportCardController::class, 'print'])
                ->name('tenant.results.report-cards.print');

            // ── Timetable ────────────────────────────────────────────
            Route::prefix('timetable')->name('tenant.timetable.')->group(function () {
                Route::get('/', [TimetableController::class, 'index'])
                    ->name('index');
                Route::post('/entries', [TimetableController::class, 'saveEntry'])
                    ->name('entries.save');
                Route::delete('/entries/{entry}', [TimetableController::class, 'deleteEntry'])
                    ->name('entries.delete');
                Route::get('/slots', [TimetableController::class, 'slots'])
                    ->name('slots');
                Route::post('/slots', [TimetableController::class, 'storeSlot'])
                    ->name('slots.store');
                Route::delete('/slots/{slot}', [TimetableController::class, 'destroySlot'])
                    ->name('slots.destroy');
                Route::get('/teacher', [TimetableController::class, 'teacherView'])
                    ->name('teacher');
                Route::get('/print', [TimetableController::class, 'print'])
                    ->name('print');
                Route::get('/teacher-free-slots', [TimetableController::class, 'teacherFreeSlots'])
                    ->name('teacher-free-slots');
            });
        });

        /*
        |-----------------------------------------------------------
        | LIBRARY — Admin + Staff with can_manage_library permission
        | Each controller method enforces its own permission check.
        |-----------------------------------------------------------
        */
        Route::prefix('library')->name('tenant.library.')->group(function () {
            Route::get('/', [LibraryController::class, 'dashboard'])
                ->name('dashboard');

            // Books
            Route::get('/books', [LibraryController::class, 'books'])
                ->name('books');
            Route::post('/books', [LibraryController::class, 'storeBook'])
                ->name('books.store');
            Route::put('/books/{book}', [LibraryController::class, 'updateBook'])
                ->name('books.update');
            Route::delete('/books/{book}', [LibraryController::class, 'destroyBook'])
                ->name('books.destroy');

            // Members
            Route::get('/members', [LibraryController::class, 'members'])
                ->name('members');
            Route::post('/members', [LibraryController::class, 'storeMember'])
                ->name('members.store');
            Route::put('/members/{member}', [LibraryController::class, 'updateMember'])
                ->name('members.update');

            // Issues / Returns
            Route::get('/issues', [LibraryController::class, 'issues'])
                ->name('issues');
            Route::post('/issues', [LibraryController::class, 'storeIssue'])
                ->name('issues.store');
            Route::patch('/issues/{issue}/return', [LibraryController::class, 'returnBook'])
                ->name('issues.return');
            Route::patch('/issues/{issue}/lost', [LibraryController::class, 'markLost'])
                ->name('issues.lost');

            // Ajax search endpoints
            Route::get('/search/books', [LibraryController::class, 'searchBooks'])
                ->name('search.books');
            Route::get('/search/members', [LibraryController::class, 'searchMembers'])
                ->name('search.members');
        });

        /*
        |-----------------------------------------------------------
        | ADMIN ONLY
        |-----------------------------------------------------------
        */
        Route::middleware(TenantAdminOnly::class)->group(function () {

            // Staff Permissions Management
            Route::get('/staff-permissions', [StaffPermissionsController::class, 'index'])
                ->name('tenant.staff.permissions.index');
            Route::get('/staff-permissions/{userId}/edit', [StaffPermissionsController::class, 'edit'])
                ->name('tenant.staff.permissions.edit');
            Route::put('/staff-permissions/{userId}', [StaffPermissionsController::class, 'update'])
                ->name('tenant.staff.permissions.update');
            Route::put('/staff-permissions/{userId}/defaults', [StaffPermissionsController::class, 'applyDefaults'])
                ->name('tenant.staff.permissions.defaults');

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
                    ClassSubject::where('class_id', $classId)
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

            // Staff CRUD (admin only)
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
            Route::post('/staff/{staff}/reset-password', [StaffController::class, 'resetPassword'])
                ->name('tenant.staff.reset-password');

            Route::prefix('subjects')->name('tenant.subjects.')->group(function () {
                Route::get('/', [SubjectController::class, 'index'])->name('index');
                Route::post('/', [SubjectController::class, 'store'])->name('store');
                Route::put('/{subject}', [SubjectController::class, 'update'])->name('update');
                Route::delete('/{subject}', [SubjectController::class, 'destroy'])->name('destroy');
                Route::get('/assign', [SubjectController::class, 'assign'])->name('assign');
                Route::post('/assign', [SubjectController::class, 'saveAssign'])->name('assign.save');
                Route::delete('/assign/{subject}', [SubjectController::class, 'removeAssign'])->name('assign.remove');
            });

            // Fee Heads & Structures (admin only — setup)
            Route::prefix('fees')->name('tenant.fees.')->group(function () {
                Route::get('/heads', [FeeHeadController::class, 'index'])->name('heads.index');
                Route::post('/heads', [FeeHeadController::class, 'store'])->name('heads.store');
                Route::put('/heads/{feeHead}', [FeeHeadController::class, 'update'])->name('heads.update');
                Route::delete('/heads/{feeHead}', [FeeHeadController::class, 'destroy'])->name('heads.destroy');
                Route::get('/structures', [FeeStructureController::class, 'index'])->name('structures.index');
                Route::post('/structures', [FeeStructureController::class, 'store'])->name('structures.store');
                Route::delete('/structures/{feeStructure}', [FeeStructureController::class, 'destroy'])->name('structures.destroy');
            });

            // Grade Scales (admin only)
            Route::get('/results/grade-scales', [GradeScaleController::class, 'index'])
                ->name('tenant.results.grade-scales.index');
            Route::post('/results/grade-scales', [GradeScaleController::class, 'store'])
                ->name('tenant.results.grade-scales.store');
            Route::delete('/results/grade-scales/{gradeScale}', [GradeScaleController::class, 'destroy'])
                ->name('tenant.results.grade-scales.destroy');
            Route::post('/results/grade-scales/apply-default', [GradeScaleController::class, 'applyDefault'])
                ->name('tenant.results.grade-scales.apply-default');
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

        // ── Leave Management (all authenticated users) ────────────
        Route::prefix('leave')->name('tenant.leave.')->group(function () {
            Route::get('/', [LeaveController::class, 'index'])->name('index');
            Route::get('/apply', [LeaveController::class, 'create'])->name('create');
            Route::post('/', [LeaveController::class, 'store'])->name('store');
            Route::get('/{leave}', [LeaveController::class, 'show'])->name('show');
            Route::post('/{leave}/approve', [LeaveController::class, 'approve'])->name('approve');
            Route::post('/{leave}/reject', [LeaveController::class, 'reject'])->name('reject');
            Route::post('/{leave}/cancel', [LeaveController::class, 'cancel'])->name('cancel');
        });

        // ── Notifications ─────────────────────────────────────────
        Route::prefix('notifications')->name('tenant.notifications.')->group(function () {
            Route::get('/{id}/read', [NotificationController::class, 'markRead'])->name('read');
            Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
        });

    });

});

Route::get('/translate', function (Request $request) {
    $text = $request->q;
    $to = $request->to ?? 'hi';

    if (! $text) {
        return response()->json(['text' => '']);
    }

    try {
        $response = Http::get(
            'https://translate.googleapis.com/translate_a/single', [
                'client' => 'gtx',
                'sl' => 'en',
                'tl' => $to,
                'dt' => 't',
                'q' => $text,
            ]
        );

        $translated = $response->json()[0][0][0] ?? '';

        return response()->json(['text' => $translated]);

    } catch (Exception $e) {
        return response()->json(['text' => '']);
    }
})->name('tenant.translate')->middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
]);
