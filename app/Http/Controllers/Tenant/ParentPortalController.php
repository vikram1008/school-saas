<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeDemand;
use App\Models\FeeCollection;
use App\Models\Notice;
use App\Models\ParentProfile;
use App\Models\StudentAttendance;
use Illuminate\Support\Facades\Auth;

class ParentPortalController extends Controller
{
    private function getParentProfile(): ?ParentProfile
    {
        return ParentProfile::where('user_id', Auth::guard('tenant')->id())
            ->with(['students.class', 'students.section'])
            ->first();
    }

    public function dashboard()
    {
        $parent   = $this->getParentProfile();
        if (!$parent) {
            return view('tenant.parent-portal.no-profile');
        }

        $students  = $parent->students;
        $activeYear = AcademicYear::active();

        // Per-student summary
        $studentData = $students->map(function ($student) use ($activeYear) {
            // Attendance this month
            $attended = StudentAttendance::where('student_profile_id', $student->id)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->where('attendance_type', 'class_wise')
                ->whereIn('status', ['present', 'late', 'half_day'])
                ->count();

            $totalDays = StudentAttendance::where('student_profile_id', $student->id)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->where('attendance_type', 'class_wise')
                ->count();

            // Fee balance
            $feeBalance = FeeDemand::where('student_profile_id', $student->id)
                ->whereIn('status', ['pending', 'partial', 'overdue'])
                ->sum('balance');

            return [
                'student'       => $student,
                'attended'      => $attended,
                'total_days'    => $totalDays,
                'attendance_pct'=> $totalDays > 0 ? round(($attended / $totalDays) * 100) : 0,
                'fee_balance'   => $feeBalance,
            ];
        });

        // Active notices
        $notices = Notice::where('is_published', true)
            ->whereIn('visible_to', ['all', 'parents'])
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->latest('published_at')
            ->take(5)
            ->get();

        return view('tenant.parent-portal.dashboard', compact(
            'parent', 'studentData', 'notices', 'activeYear'
        ));
    }

    public function childAttendance(int $studentId)
    {
        $parent  = $this->getParentProfile();
        $student = $parent?->students->firstWhere('id', $studentId);

        if (!$student) abort(403, 'Access denied.');

        $month  = request('month', now()->month);
        $year   = request('year', now()->year);

        $records = StudentAttendance::where('student_profile_id', $student->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('attendance_type', 'class_wise')
            ->orderBy('date')
            ->get()
            ->keyBy(fn($r) => $r->date->format('Y-m-d'));

        $workingDays = $records->count();
        $present     = $records->whereIn('status', ['present', 'late', 'half_day'])->count();
        $absent      = $records->where('status', 'absent')->count();
        $percentage  = $workingDays > 0 ? round(($present / $workingDays) * 100) : 0;

        // Calendar days
        $daysInMonth = \Carbon\Carbon::create($year, $month)->daysInMonth;

        $months = collect(range(1, 12))->mapWithKeys(fn($m) => [
            $m => \Carbon\Carbon::create()->month($m)->format('F')
        ]);

        return view('tenant.parent-portal.attendance', compact(
            'parent', 'student', 'records', 'workingDays',
            'present', 'absent', 'percentage',
            'month', 'year', 'daysInMonth', 'months'
        ));
    }

    public function childFees(int $studentId)
    {
        $parent  = $this->getParentProfile();
        $student = $parent?->students->firstWhere('id', $studentId);

        if (!$student) abort(403, 'Access denied.');

        $activeYear = AcademicYear::active();

        $demands = FeeDemand::with('feeHead')
            ->where('student_profile_id', $student->id)
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->orderBy('due_date')
            ->get();

        $collections = FeeCollection::with('items.demand.feeHead')
            ->where('student_profile_id', $student->id)
            ->latest()
            ->get();

        $summary = [
            'total_due'     => $demands->sum('amount_due'),
            'total_paid'    => $demands->sum('amount_paid'),
            'total_balance' => $demands->sum('balance'),
        ];

        return view('tenant.parent-portal.fees', compact(
            'parent', 'student', 'demands', 'collections', 'summary', 'activeYear'
        ));
    }

    public function notices()
    {
        $parent  = $this->getParentProfile();

        $notices = Notice::where('is_published', true)
            ->whereIn('visible_to', ['all', 'parents'])
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->latest('published_at')
            ->paginate(10);

        return view('tenant.parent-portal.notices', compact('parent', 'notices'));
    }
}