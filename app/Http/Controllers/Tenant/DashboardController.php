<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('tenant')->user();
        $school = tenant();

        // Live stats from tenant DB
        $totalStudents = DB::connection('tenant')
            ->table('users')
            ->where('role', 'student')
            ->count();

        $activeStudents = DB::connection('tenant')
            ->table('users')
            ->where('role', 'student')
            ->where('is_active', true)
            ->count();

        $totalStaff = DB::connection('tenant')
            ->table('users')
            ->whereIn('role', ['teacher', 'accountant', 'librarian', 'school_admin'])
            ->count();

        $activeStaff = DB::connection('tenant')
            ->table('users')
            ->whereIn('role', ['teacher', 'accountant', 'librarian', 'school_admin'])
            ->where('is_active', true)
            ->count();

        $totalParents = DB::connection('tenant')
            ->table('users')
            ->where('role', 'parent')
            ->count();

        $activeParents = DB::connection('tenant')
            ->table('users')
            ->where('role', 'parent')
            ->where('is_active', true)
            ->count();

        // Monthly billing estimate
        $monthlyBill = $activeStudents * $school->per_student_rate;

        // Recent users added (last 30 days)
        $recentStudents = DB::connection('tenant')
            ->table('users')
            ->where('role', 'student')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $recentStaff = DB::connection('tenant')
            ->table('users')
            ->whereIn('role', ['teacher', 'accountant', 'librarian'])
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        // Gender breakdown from student_profiles
        $genderStats = DB::connection('tenant')
            ->table('student_profiles')
            ->selectRaw('gender, count(*) as total')
            ->whereNotNull('gender')
            ->groupBy('gender')
            ->pluck('total', 'gender')
            ->toArray();

        // Subscription info from central DB — use latestSubscription() so paid schools
        // still see their current billing cycle (activeSubscription filters out 'paid').
        $subscription = $school->latestSubscription()->first();

        // ── Additional dashboard data ──────────────────────────────

        // Total classes
        $totalClasses = DB::connection('tenant')
            ->table('classes')
            ->count();

        // Total subjects
        $totalSubjects = DB::connection('tenant')
            ->table('class_subjects')
            ->count();

        // Active academic year
        $activeAcademicYear = DB::connection('tenant')
            ->table('academic_years')
            ->where('is_active', true)
            ->first();

        // Today's student attendance summary
        $todayStudentAttendance = DB::connection('tenant')
            ->table('student_attendance')
            ->where('date', now()->toDateString())
            ->selectRaw("
                count(*) as total_marked,
                sum(case when status = 'present' then 1 else 0 end) as present,
                sum(case when status = 'absent' then 1 else 0 end) as absent,
                sum(case when status = 'late' then 1 else 0 end) as late
            ")
            ->first();

        // Today's staff attendance summary
        $todayStaffAttendance = DB::connection('tenant')
            ->table('staff_attendance')
            ->where('date', now()->toDateString())
            ->selectRaw("
                count(*) as total_marked,
                sum(case when status = 'present' then 1 else 0 end) as present,
                sum(case when status = 'absent' then 1 else 0 end) as absent,
                sum(case when status = 'late' then 1 else 0 end) as late
            ")
            ->first();

        // Fee collection — this month
        $monthlyFeeCollection = DB::connection('tenant')
            ->table('fee_collections')
            ->whereMonth('collection_date', now()->month)
            ->whereYear('collection_date', now()->year)
            ->selectRaw('coalesce(sum(total_amount), 0) as total, count(*) as receipts')
            ->first();

        // Recent notices (latest 5)
        $recentNotices = DB::connection('tenant')
            ->table('notices')
            ->where('is_published', true)
            ->whereNull('deleted_at')
            ->orderByDesc('published_at')
            ->limit(5)
            ->get(['id', 'title', 'visible_to', 'published_at']);

        return view('tenant.dashboard', compact(
            'user',
            'school',
            'totalStudents',
            'activeStudents',
            'totalStaff',
            'activeStaff',
            'totalParents',
            'activeParents',
            'monthlyBill',
            'recentStudents',
            'recentStaff',
            'genderStats',
            'subscription',
            'totalClasses',
            'totalSubjects',
            'activeAcademicYear',
            'todayStudentAttendance',
            'todayStaffAttendance',
            'monthlyFeeCollection',
            'recentNotices',
        ));
    }
}
