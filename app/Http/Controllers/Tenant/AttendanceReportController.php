<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\StaffAttendance;
use App\Models\StaffProfile;
use App\Models\StudentAttendance;
use App\Models\StudentProfile;
use App\Models\TenantUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceReportController extends Controller
{
    private function tenantUser(): TenantUser
    {
        return Auth::guard('tenant')->user();
    }

    // Monthly student attendance report
    public function studentMonthly(Request $request)
    {
        $this->tenantUser()->authorizePermission('can_view_attendance_reports');

        $activeYear = AcademicYear::active();
        $classes = $activeYear
            ? SchoolClass::where('academic_year_id', $activeYear->id)
                ->orderBy('order')->get()
            : collect();

        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $classId = $request->class_id;
        $sectionId = $request->section_id;
        $threshold = $request->threshold ?? 75; // defaulter threshold %

        $sections = $classId
            ? Section::where('class_id', $classId)->get()
            : collect();

        $reportData = collect();
        $defaulters = collect();
        $workingDays = 0;

        if ($classId) {
            // Get all dates in month that have attendance records
            $attendanceQuery = StudentAttendance::where('class_id', $classId)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->where('attendance_type', 'class_wise')
                ->when($sectionId, fn ($q) => $q->where('section_id', $sectionId));

            $workingDays = $attendanceQuery->clone()
                ->distinct('date')
                ->count('date');

            $students = StudentProfile::where('class_id', $classId)
                ->when($sectionId, fn ($q) => $q->where('section_id', $sectionId))
                ->where('status', 'active')
                ->orderBy('first_name')
                ->get();

            foreach ($students as $student) {
                $records = StudentAttendance::where('student_profile_id', $student->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->where('attendance_type', 'class_wise')
                    ->get();

                $present = $records->whereIn('status', ['present', 'late', 'half_day'])->count();
                $absent = $records->where('status', 'absent')->count();
                $late = $records->where('status', 'late')->count();
                $halfDay = $records->where('status', 'half_day')->count();
                $leave = $records->where('status', 'leave')->count();
                $percentage = $workingDays > 0
                    ? round(($present / $workingDays) * 100, 1)
                    : 0;

                $data = compact(
                    'present', 'absent', 'late',
                    'halfDay', 'leave', 'percentage'
                );
                $data['student'] = $student;
                $reportData->push($data);

                if ($percentage < $threshold && $workingDays > 0) {
                    $defaulters->push($data);
                }
            }
        }

        $months = collect(range(1, 12))->mapWithKeys(fn ($m) => [
            $m => Carbon::create()->month($m)->format('F'),
        ]);

        return view('tenant.attendance.reports.student-monthly', compact(
            'classes', 'sections', 'reportData', 'defaulters',
            'month', 'year', 'classId', 'sectionId',
            'workingDays', 'threshold', 'months', 'activeYear'
        ));
    }

    // Daily summary report
    public function dailySummary(Request $request)
    {
        $this->tenantUser()->authorizePermission('can_view_attendance_reports');

        $activeYear = AcademicYear::active();
        $classes = $activeYear
            ? SchoolClass::where('academic_year_id', $activeYear->id)
                ->orderBy('order')->get()
            : collect();

        $date = $request->date ?? today()->toDateString();

        // Per-class summary
        $classSummaries = collect();
        foreach ($classes as $class) {
            $records = StudentAttendance::where('class_id', $class->id)
                ->whereDate('date', $date)
                ->where('attendance_type', 'class_wise')
                ->get();

            $total = StudentProfile::where('class_id', $class->id)
                ->where('status', 'active')->count();

            $classSummaries->push([
                'class' => $class,
                'total' => $total,
                'present' => $records->where('status', 'present')->count(),
                'absent' => $records->where('status', 'absent')->count(),
                'late' => $records->where('status', 'late')->count(),
                'marked' => $records->count(),
            ]);
        }

        // School-wide totals
        $schoolTotals = [
            'total' => $classSummaries->sum('total'),
            'present' => $classSummaries->sum('present'),
            'absent' => $classSummaries->sum('absent'),
            'late' => $classSummaries->sum('late'),
            'marked' => $classSummaries->sum('marked'),
        ];

        return view('tenant.attendance.reports.daily-summary', compact(
            'classSummaries', 'schoolTotals', 'date', 'activeYear'
        ));
    }

    // Staff monthly report
    public function staffMonthly(Request $request)
    {
        $this->tenantUser()->authorizePermission('can_view_attendance_reports');

        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $staff = StaffProfile::where('status', 'active')
            ->orderBy('first_name')
            ->get();

        $workingDays = StaffAttendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->distinct('date')
            ->count('date');

        $reportData = collect();
        foreach ($staff as $member) {
            $records = StaffAttendance::where('staff_profile_id', $member->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->get();

            $present = $records->whereIn('status', ['present', 'late', 'half_day'])->count();
            $absent = $records->where('status', 'absent')->count();
            $late = $records->where('status', 'late')->count();
            $leave = $records->where('status', 'leave')->count();
            $percentage = $workingDays > 0
                ? round(($present / $workingDays) * 100, 1)
                : 0;

            $reportData->push([
                'staff' => $member,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'leave' => $leave,
                'percentage' => $percentage,
            ]);
        }

        $months = collect(range(1, 12))->mapWithKeys(fn ($m) => [
            $m => Carbon::create()->month($m)->format('F'),
        ]);

        return view('tenant.attendance.reports.staff-monthly', compact(
            'reportData', 'month', 'year', 'workingDays', 'months'
        ));
    }
}
