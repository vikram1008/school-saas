<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\StaffProfile;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    // ── Master Subject List ──────────────────────────────────────────

    public function index()
    {
        // Group class_subjects by subject_name to get unique subjects
        $subjects = ClassSubject::with(['class', 'teacher'])
            ->orderBy('subject_name')
            ->get()
            ->groupBy('subject_name');

        // Unique subject names with their Hindi names
        $uniqueSubjects = ClassSubject::select('subject_name', 'subject_name_hi')
            ->groupBy('subject_name', 'subject_name_hi')
            ->orderBy('subject_name')
            ->get();

        $activeYear = AcademicYear::active();
        $classes    = $activeYear
            ? SchoolClass::with('subjects')
                ->where('academic_year_id', $activeYear->id)
                ->orderBy('order')
                ->get()
            : collect();

        return view('tenant.subjects.index', compact(
            'subjects', 'uniqueSubjects', 'classes', 'activeYear'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_name' => ['required', 'string', 'max:100'],
        ]);

        // Add this subject to selected classes
        $classIds = $request->class_ids ?? [];

        if (empty($classIds)) {
            return back()->withErrors(['class_ids' => 'Select at least one class.'])
                         ->withInput();
        }

        foreach ($classIds as $classId) {
            ClassSubject::firstOrCreate(
                [
                    'class_id'     => $classId,
                    'subject_name' => $request->subject_name,
                ],
                [
                    'subject_name_hi'  => $request->subject_name_hi,
                    'periods_per_week' => $request->periods_per_week ?? 5,
                    'sort_order'       => ClassSubject::where('class_id', $classId)
                                            ->max('sort_order') + 1,
                    'is_active'        => true,
                ]
            );
        }

        return redirect()
            ->route('tenant.subjects.index')
            ->with('success', "\"{$request->subject_name}\" added to " . count($classIds) . " class(es).");
    }

    public function update(Request $request, ClassSubject $subject)
    {
        $request->validate([
            'subject_name' => ['required', 'string', 'max:100'],
        ]);

        $subject->update([
            'subject_name'    => $request->subject_name,
            'subject_name_hi' => $request->subject_name_hi,
            'periods_per_week'=> $request->periods_per_week ?? $subject->periods_per_week,
            'is_active'       => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('tenant.subjects.index')
            ->with('success', 'Subject updated.');
    }

    public function destroy(ClassSubject $subject)
    {
        $subject->delete();
        return redirect()
            ->route('tenant.subjects.index')
            ->with('success', 'Subject removed from class.');
    }

    // ── Assign Subject to Class ──────────────────────────────────────

    public function assign(Request $request)
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

        // Already assigned subjects for this class
        $assignedSubjects = $classId
            ? ClassSubject::with('teacher')
                ->where('class_id', $classId)
                ->orderBy('sort_order')
                ->get()
            : collect();

        // Available subject names (from all existing subjects in the system)
        $availableSubjects = ClassSubject::select('subject_name', 'subject_name_hi')
            ->groupBy('subject_name', 'subject_name_hi')
            ->orderBy('subject_name')
            ->get();

        $teachers = StaffProfile::where('staff_type', 'teaching')
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('tenant.subjects.assign', compact(
            'classes', 'sections', 'classId', 'sectionId',
            'assignedSubjects', 'availableSubjects', 'teachers', 'activeYear'
        ));
    }

    public function saveAssign(Request $request)
    {
        $request->validate([
            'class_id'     => ['required', 'exists:classes,id'],
            'subject_name' => ['required', 'string', 'max:100'],
        ]);

        $subject = ClassSubject::updateOrCreate(
            [
                'class_id'     => $request->class_id,
                'subject_name' => $request->subject_name,
            ],
            [
                'subject_name_hi'  => $request->subject_name_hi,
                'teacher_id'       => $request->teacher_id ?: null,
                'periods_per_week' => $request->periods_per_week ?? 5,
                'sort_order'       => ClassSubject::where('class_id', $request->class_id)
                                        ->max('sort_order') + 1,
                'is_active'        => true,
            ]
        );

        return redirect()
            ->route('tenant.subjects.assign', [
                'class_id'   => $request->class_id,
                'section_id' => $request->section_id,
            ])
            ->with('success', "\"{$request->subject_name}\" assigned successfully.");
    }

    public function removeAssign(ClassSubject $subject)
    {
        $classId = $subject->class_id;
        $subject->delete();

        return redirect()
            ->route('tenant.subjects.assign', ['class_id' => $classId])
            ->with('success', 'Subject removed.');
    }
}