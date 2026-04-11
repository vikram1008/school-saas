<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AttendancePeriod;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\StudentAttendance;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $activeYear = AcademicYear::active();
        $classes    = $activeYear
            ? SchoolClass::where('academic_year_id', $activeYear->id)
                ->orderBy('order')->get()
            : collect();

        $date       = $request->date ?? today()->toDateString();
        $classId    = $request->class_id;
        $sectionId  = $request->section_id;
        $type       = $request->type ?? 'class_wise';

        $sections = $classId
            ? Section::where('class_id', $classId)->orderBy('order')->get()
            : collect();

        $periods = ($classId && $type === 'subject_wise')
            ? AttendancePeriod::where('class_id', $classId)
                ->when($sectionId, fn($q) => $q->where(function($q) use ($sectionId) {
                    $q->where('section_id', $sectionId)->orWhereNull('section_id');
                }))
                ->where('is_active', true)
                ->orderBy('period_number')
                ->get()
            : collect();

        // Load students if class selected
        $students = collect();
        $existingAttendance = collect();

        if ($classId) {
            $students = StudentProfile::where('class_id', $classId)
                ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
                ->where('status', 'active')
                ->orderBy('first_name')
                ->get();

            $periodId = $request->period_id;
            $existingAttendance = StudentAttendance::where('class_id', $classId)
                ->whereDate('date', $date)
                ->where('attendance_type', $type)
                ->when($periodId, fn($q) => $q->where('period_id', $periodId))
                ->when(!$periodId && $type === 'class_wise', fn($q) => $q->whereNull('period_id'))
                ->get()
                ->keyBy('student_profile_id');
        }

        // Daily summary
        $dailySummary = $classId ? [
            'present'  => $existingAttendance->where('status', 'present')->count(),
            'absent'   => $existingAttendance->where('status', 'absent')->count(),
            'late'     => $existingAttendance->where('status', 'late')->count(),
            'half_day' => $existingAttendance->where('status', 'half_day')->count(),
            'leave'    => $existingAttendance->where('status', 'leave')->count(),
            'total'    => $students->count(),
        ] : null;

        return view('tenant.attendance.students.index', compact(
            'classes', 'sections', 'periods', 'students',
            'existingAttendance', 'dailySummary',
            'activeYear', 'date', 'classId', 'sectionId', 'type'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'            => ['required', 'date'],
            'class_id'        => ['required', 'exists:classes,id'],
            'academic_year_id'=> ['required', 'exists:academic_years,id'],
            'attendance_type' => ['required', 'in:class_wise,subject_wise'],
            'attendance'      => ['required', 'array'],
        ]);

        $markedBy  = Auth::guard('tenant')->id();
        $date      = $request->date;
        $classId   = $request->class_id;
        $sectionId = $request->section_id;
        $type      = $request->attendance_type;
        $periodId  = $request->period_id ?? null;

        DB::beginTransaction();
        try {
            foreach ($request->attendance as $studentId => $data) {
                StudentAttendance::updateOrCreate(
                    [
                        'student_profile_id' => $studentId,
                        'date'               => $date,
                        'attendance_type'    => $type,
                        'period_id'          => $periodId,
                    ],
                    [
                        'class_id'        => $classId,
                        'section_id'      => $sectionId,
                        'academic_year_id'=> $request->academic_year_id,
                        'status'          => $data['status'] ?? 'present',
                        'subject_name'    => $data['subject_name'] ?? null,
                        'remarks'         => $data['remarks'] ?? null,
                        'marked_by'       => $markedBy,
                    ]
                );
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed: ' . $e->getMessage()]);
        }

        return redirect()
            ->back()
            ->with('success', 'Attendance saved for ' . count($request->attendance) . ' students.');
    }
}