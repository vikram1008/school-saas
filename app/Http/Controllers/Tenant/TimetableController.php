<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\StaffProfile;
use App\Models\TimetableEntry;
use App\Models\TimetableSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{
    // ─── Slot Management ────────────────────────────────────────────

    public function slots(Request $request)
    {
        $activeYear = AcademicYear::active();
        $classes    = $activeYear
            ? SchoolClass::where('academic_year_id', $activeYear->id)
                ->orderBy('order')->get()
            : collect();

        $classId   = $request->class_id;
        $sectionId = $request->section_id;

        $sections = $classId
            ? Section::where('class_id', $classId)->orderBy('order')->get()
            : collect();

        $slots = ($classId && $activeYear)
            ? TimetableSlot::where('academic_year_id', $activeYear->id)
                ->where('class_id', $classId)
                ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
                ->orderBy('period_number')
                ->orderBy('day_of_week')
                ->get()
            : collect();

        return view('tenant.timetable.slots', compact(
            'activeYear', 'classes', 'sections',
            'slots', 'classId', 'sectionId'
        ));
    }

    public function storeSlot(Request $request)
    {
        $request->validate([
            'class_id'        => ['required', 'exists:classes,id'],
            'period_number'   => ['required', 'integer', 'min:1', 'max:15'],
            'period_name'     => ['required', 'string', 'max:50'],
            'start_time'      => ['required', 'string'],
            'end_time'        => ['required', 'string'],
            'academic_year_id'=> ['required', 'exists:academic_years,id'],
        ]);

        TimetableSlot::updateOrCreate(
            [
                'academic_year_id' => $request->academic_year_id,
                'class_id'         => $request->class_id,
                'section_id'       => $request->section_id ?: null,
                'period_number'    => $request->period_number,
                'day_of_week'      => $request->day_of_week ?: null,
            ],
            [
                'period_name' => $request->period_name,
                'start_time'  => $request->start_time,
                'end_time'    => $request->end_time,
                'is_break'    => $request->boolean('is_break'),
                'is_active'   => true,
            ]
        );

        return redirect()
            ->route('tenant.timetable.slots', [
                'class_id'   => $request->class_id,
                'section_id' => $request->section_id,
            ])
            ->with('success', 'Period slot saved.');
    }

    public function destroySlot(TimetableSlot $slot)
    {
        $classId   = $slot->class_id;
        $sectionId = $slot->section_id;
        $slot->delete();

        return redirect()
            ->route('tenant.timetable.slots', compact('classId', 'sectionId'))
            ->with('success', 'Slot deleted.');
    }

    // ─── Timetable Entry ────────────────────────────────────────────

    public function index(Request $request)
    {
        $activeYear = AcademicYear::active();
        $classes    = $activeYear
            ? SchoolClass::where('academic_year_id', $activeYear->id)
                ->orderBy('order')->get()
            : collect();

        $classId   = $request->class_id;
        $sectionId = $request->section_id;

        $sections = $classId
            ? Section::where('class_id', $classId)->orderBy('order')->get()
            : collect();

        $teachers = StaffProfile::where('staff_type', 'teaching')
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        // Load slots and entries
        $slots   = collect();
        $entries = collect();
        $grid    = [];

        if ($classId && $activeYear) {
            $slots = TimetableSlot::where('academic_year_id', $activeYear->id)
                ->where('class_id', $classId)
                ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
                ->where('is_active', true)
                ->orderBy('period_number')
                ->get();

            $entries = TimetableEntry::with('teacher')
                ->where('academic_year_id', $activeYear->id)
                ->where('class_id', $classId)
                ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
                ->get();

            // Build grid: grid[period][day] = entry
            foreach ($entries as $entry) {
                $grid[$entry->period_number][$entry->day_of_week] = $entry;
            }
        }

        $days = TimetableSlot::dayLabels();

        return view('tenant.timetable.index', compact(
            'activeYear', 'classes', 'sections', 'teachers',
            'slots', 'entries', 'grid', 'days',
            'classId', 'sectionId'
        ));
    }

    public function saveEntry(Request $request)
    {
        $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'class_id'         => ['required', 'exists:classes,id'],
            'day_of_week'      => ['required', 'integer', 'min:1', 'max:6'],
            'period_number'    => ['required', 'integer', 'min:1'],
            'subject_name'     => ['required', 'string', 'max:100'],
        ]);

        // Conflict check — same teacher at same time
        $conflict = null;
        if ($request->teacher_id) {
            $conflict = TimetableEntry::with(['class', 'section'])
                ->where('academic_year_id', $request->academic_year_id)
                ->where('teacher_id', $request->teacher_id)
                ->where('day_of_week', $request->day_of_week)
                ->where('period_number', $request->period_number)
                ->where(function ($q) use ($request) {
                    $q->where('class_id', '!=', $request->class_id)
                      ->orWhere('section_id', '!=', $request->section_id);
                })
                ->first();

            if ($conflict) {
                $className = $conflict->class?->name
                    . ($conflict->section ? ' ' . $conflict->section->name : '');
                return response()->json([
                    'conflict' => true,
                    'message'  => "Teacher already assigned to {$className} on "
                        . TimetableSlot::dayLabels()[$request->day_of_week]
                        . ", Period {$request->period_number}.",
                ], 422);
            }
        }

        $entry = TimetableEntry::updateOrCreate(
            [
                'academic_year_id' => $request->academic_year_id,
                'class_id'         => $request->class_id,
                'section_id'       => $request->section_id ?: null,
                'day_of_week'      => $request->day_of_week,
                'period_number'    => $request->period_number,
            ],
            [
                'subject_name'    => $request->subject_name,
                'subject_name_hi' => $request->subject_name_hi,
                'teacher_id'      => $request->teacher_id ?: null,
                'room_number'     => $request->room_number,
            ]
        );

        $entry->load('teacher');

        return response()->json([
            'success' => true,
            'entry'   => [
                'id'             => $entry->id,
                'subject_name'   => $entry->subject_name,
                'subject_name_hi'=> $entry->subject_name_hi,
                'teacher'        => $entry->teacher?->full_name,
                'room_number'    => $entry->room_number,
            ],
        ]);
    }

    public function deleteEntry(TimetableEntry $entry)
    {
        $entry->delete();
        return response()->json(['success' => true]);
    }

    // ─── Teacher Timetable View ──────────────────────────────────────

    public function teacherView(Request $request)
    {
        $activeYear = AcademicYear::active();
        $teachers   = StaffProfile::where('staff_type', 'teaching')
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        $teacherId = $request->teacher_id;
        $grid      = [];
        $periods   = [];
        $days      = TimetableSlot::dayLabels();

        if ($teacherId && $activeYear) {
            $entries = TimetableEntry::with(['class', 'section'])
                ->where('academic_year_id', $activeYear->id)
                ->where('teacher_id', $teacherId)
                ->get();

            foreach ($entries as $entry) {
                $grid[$entry->period_number][$entry->day_of_week] = $entry;
                $periods[$entry->period_number] = $entry->period_number;
            }
            ksort($periods);
            ksort($grid);
        }

        // Free slots — find which periods the teacher is NOT assigned
        $allPeriods = range(1, 8);
        $busySlots  = [];
        foreach ($grid as $period => $dayEntries) {
            foreach ($dayEntries as $day => $entry) {
                $busySlots[] = "{$day}-{$period}";
            }
        }

        return view('tenant.timetable.teacher', compact(
            'teachers', 'teacherId', 'grid',
            'periods', 'days', 'activeYear', 'busySlots', 'allPeriods'
        ));
    }

    // ─── Print View ─────────────────────────────────────────────────

    public function print(Request $request)
    {
        $activeYear = AcademicYear::active();
        $classId    = $request->class_id;
        $sectionId  = $request->section_id;

        $class   = SchoolClass::find($classId);
        $section = Section::find($sectionId);

        $slots = TimetableSlot::where('academic_year_id', $activeYear?->id)
            ->where('class_id', $classId)
            ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
            ->where('is_active', true)
            ->orderBy('period_number')
            ->get();

        $entries = TimetableEntry::with('teacher')
            ->where('academic_year_id', $activeYear?->id)
            ->where('class_id', $classId)
            ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
            ->get();

        $grid = [];
        foreach ($entries as $entry) {
            $grid[$entry->period_number][$entry->day_of_week] = $entry;
        }

        $days = TimetableSlot::dayLabels();

        return view('tenant.timetable.print', compact(
            'class', 'section', 'slots', 'grid', 'days', 'activeYear'
        ));
    }

    // ─── Ajax: Get teacher free slots ───────────────────────────────

    public function teacherFreeSlots(Request $request)
    {
        $activeYear = AcademicYear::active();
        if (!$activeYear || !$request->teacher_id) {
            return response()->json([]);
        }

        $busySlots = TimetableEntry::where('academic_year_id', $activeYear->id)
            ->where('teacher_id', $request->teacher_id)
            ->get(['day_of_week', 'period_number'])
            ->map(fn($e) => "{$e->day_of_week}-{$e->period_number}")
            ->toArray();

        return response()->json(['busy' => $busySlots]);
    }
}