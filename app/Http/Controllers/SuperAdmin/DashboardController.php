<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Central DB stats
        $totalSchools    = Tenant::count();
        $activeSchools   = Tenant::where('is_active', true)->count();
        $inactiveSchools = Tenant::where('is_active', false)->count();

        // Recent schools
        $recentSchools = Tenant::with('domains')
            ->latest()
            ->take(5)
            ->get();

        // Per-school student counts + revenue calculation
        $schools = Tenant::with('domains')->get();

        $totalStudents      = 0;
        $currentMonthRev    = 0;
        $schoolStats        = [];

        foreach ($schools as $school) {
            try {
                tenancy()->initialize($school);
                $studentCount = DB::connection('tenant')
                    ->table('users')
                    ->where('role', 'student')
                    ->where('is_active', true)
                    ->count();
                tenancy()->end();

                $totalStudents   += $studentCount;
                $monthlyBill      = $studentCount * $school->per_student_rate;
                $currentMonthRev += $monthlyBill;

                $schoolStats[] = [
                    'name'          => $school->school_name,
                    'students'      => $studentCount,
                    'monthly_bill'  => $monthlyBill,
                    'rate'          => $school->per_student_rate,
                    'is_active'     => $school->is_active,
                    'domain'        => $school->domains->first()?->domain,
                    'id'            => $school->id,
                ];
            } catch (\Exception $e) {
                tenancy()->end();
                $schoolStats[] = [
                    'name'         => $school->school_name,
                    'students'     => 0,
                    'monthly_bill' => 0,
                    'rate'         => $school->per_student_rate,
                    'is_active'    => $school->is_active,
                    'domain'       => $school->domains->first()?->domain,
                    'id'           => $school->id,
                ];
            }
        }

        // Monthly revenue for last 6 months (from tenants created_at + student data)
        // For now using current revenue as baseline — will expand with subscriptions table later
        $monthlyRevenue = $currentMonthRev;

        return view('superadmin.dashboard', compact(
            'totalSchools',
            'activeSchools',
            'inactiveSchools',
            'totalStudents',
            'currentMonthRev',
            'recentSchools',
            'schoolStats'
        ));
    }
}