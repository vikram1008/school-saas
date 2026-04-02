<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user   = Auth::guard('tenant')->user();
        $school = tenant();

        // Live stats from tenant DB
        $totalStudents  = DB::connection('tenant')
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

        // Subscription info from central DB
        $subscription = $school->activeSubscription()->first();

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
            'subscription'
        ));
    }
}