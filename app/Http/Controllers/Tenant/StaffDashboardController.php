<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('tenant')->user();
        $school = tenant();

        // Ensure permissions exist (auto-seed with role defaults)
        $permissions = $user->resolvedPermissions();

        // Active academic year
        $activeAcademicYear = DB::connection('tenant')
            ->table('academic_years')
            ->where('is_active', true)
            ->first();

        // ── Teacher-specific stats ──────────────────────────────────
        $teacherStats = null;
        if ($user->isTeacher()) {
            // Resolve staff_profile — all timetable/subject FKs use staff_profiles.id
            $staffProfile = DB::connection('tenant')
                ->table('staff_profiles')
                ->where('user_id', $user->id)
                ->first();

            $assignedClasses = collect();
            $assignedSubjects = 0;
            $todayAttendanceMarked = 0;
            $upcomingExams = collect();
            $todayTimetable = collect();

            if ($staffProfile) {
                // Classes this teacher is assigned to
                $assignedClasses = DB::connection('tenant')
                    ->table('class_subjects')
                    ->where('teacher_id', $staffProfile->id)
                    ->where('is_active', true)
                    ->join('classes', 'class_subjects.class_id', '=', 'classes.id')
                    ->select('classes.id', 'classes.name')
                    ->distinct()
                    ->get();

                $assignedSubjects = DB::connection('tenant')
                    ->table('class_subjects')
                    ->where('teacher_id', $staffProfile->id)
                    ->where('is_active', true)
                    ->count();

                // Today's attendance marked for teacher's classes
                $todayAttendanceMarked = DB::connection('tenant')
                    ->table('student_attendance')
                    ->where('date', now()->toDateString())
                    ->whereIn('class_id', $assignedClasses->pluck('id'))
                    ->count();

                // Upcoming exams in the next 7 days (using exams.start_date)
                $upcomingExams = DB::connection('tenant')
                    ->table('exam_subjects')
                    ->join('exams', 'exam_subjects.exam_id', '=', 'exams.id')
                    ->whereIn('exam_subjects.class_id', $assignedClasses->pluck('id'))
                    ->where('exams.start_date', '>=', now()->toDateString())
                    ->where('exams.start_date', '<=', now()->addDays(7)->toDateString())
                    ->whereNull('exams.deleted_at')
                    ->orderBy('exams.start_date')
                    ->limit(5)
                    ->select('exams.name as exam_name', 'exams.start_date', 'exam_subjects.subject_name')
                    ->get();

                // My timetable for today (day_of_week: 1=Mon…6=Sat)
                $todayDayOfWeek = now()->dayOfWeekIso;
                $todayTimetable = DB::connection('tenant')
                    ->table('timetable_entries')
                    ->join(
                        'timetable_slots',
                        fn ($j) => $j
                            ->on('timetable_slots.class_id', '=', 'timetable_entries.class_id')
                            ->on('timetable_slots.period_number', '=', 'timetable_entries.period_number')
                    )
                    ->join('classes', 'timetable_entries.class_id', '=', 'classes.id')
                    ->where('timetable_entries.teacher_id', $staffProfile->id)
                    ->where('timetable_entries.day_of_week', $todayDayOfWeek)
                    ->where('timetable_slots.is_active', true)
                    ->orderBy('timetable_slots.period_number')
                    ->select(
                        'timetable_slots.start_time',
                        'timetable_slots.end_time',
                        'timetable_entries.subject_name',
                        'classes.name as class_name',
                    )
                    ->get();
            }

            $teacherStats = compact(
                'assignedClasses',
                'assignedSubjects',
                'todayAttendanceMarked',
                'upcomingExams',
                'todayTimetable',
            );
        }

        // ── Accountant-specific stats ───────────────────────────────
        $accountantStats = null;
        if ($user->isAccountant()) {
            $todayFeeCollection = DB::connection('tenant')
                ->table('fee_collections')
                ->where('collection_date', now()->toDateString())
                ->selectRaw('coalesce(sum(total_amount), 0) as total, count(*) as receipts')
                ->first();

            $monthlyFeeCollection = DB::connection('tenant')
                ->table('fee_collections')
                ->whereMonth('collection_date', now()->month)
                ->whereYear('collection_date', now()->year)
                ->selectRaw('coalesce(sum(total_amount), 0) as total, count(*) as receipts')
                ->first();

            $pendingDemands = DB::connection('tenant')
                ->table('fee_demands')
                ->where('status', 'pending')
                ->selectRaw('count(*) as count, coalesce(sum(amount), 0) as total')
                ->first();

            $recentCollections = DB::connection('tenant')
                ->table('fee_collections')
                ->join('users', 'fee_collections.student_id', '=', 'users.id')
                ->orderByDesc('fee_collections.collection_date')
                ->limit(10)
                ->select(
                    'fee_collections.id',
                    'users.name as student_name',
                    'fee_collections.total_amount',
                    'fee_collections.collection_date',
                    'fee_collections.payment_mode',
                )
                ->get();

            $accountantStats = compact(
                'todayFeeCollection',
                'monthlyFeeCollection',
                'pendingDemands',
                'recentCollections',
            );
        }

        // ── Common stats ────────────────────────────────────────────
        $recentNotices = DB::connection('tenant')
            ->table('notices')
            ->where('is_published', true)
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->where('visible_to', 'all')
                    ->orWhere('visible_to', 'staff');
            })
            ->orderByDesc('published_at')
            ->limit(5)
            ->get(['id', 'title', 'visible_to', 'published_at']);

        $totalStudents = DB::connection('tenant')
            ->table('users')
            ->where('role', 'student')
            ->where('is_active', true)
            ->count();

        // Staff's own attendance this month — staff_attendance uses staff_profile_id, not user_id
        $staffProfileForAttendance = DB::connection('tenant')
            ->table('staff_profiles')
            ->where('user_id', $user->id)
            ->value('id');

        $myAttendanceThisMonth = DB::connection('tenant')
            ->table('staff_attendance')
            ->when($staffProfileForAttendance, fn ($q) => $q->where('staff_profile_id', $staffProfileForAttendance))
            ->when(! $staffProfileForAttendance, fn ($q) => $q->whereRaw('0 = 1'))
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->selectRaw("
                count(*) as total,
                sum(case when status = 'present' then 1 else 0 end) as present,
                sum(case when status = 'absent' then 1 else 0 end) as absent,
                sum(case when status = 'late' then 1 else 0 end) as late
            ")
            ->first();

        return view('tenant.staff.dashboard', compact(
            'user',
            'school',
            'permissions',
            'activeAcademicYear',
            'teacherStats',
            'accountantStats',
            'recentNotices',
            'totalStudents',
            'myAttendanceThisMonth',
        ));
    }
}
