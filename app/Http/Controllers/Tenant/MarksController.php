<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\StudentMark;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarksController extends Controller
{
    public function index(Request $request)
    {
        $activeYear = AcademicYear::active();
        $exams      = Exam::where('academic_year_id', $activeYear?->id)
            ->latest()->get();

        $classes = $activeYear
            ? SchoolClass::where('academic_year_id', $activeYear->id)
                ->orderBy('order')->get()
            : collect();

        $examId    = $request->exam_id;
        $classId   = $request->class_id;
        $sectionId = $request->section_id;
        $subjectId = $request->subject_id;

        $sections = $classId
            ? Section::where('class_id', $classId)->orderBy('order')->get()
            : collect();

       $subjects = ($examId && $classId)
            ? ExamSubject::where('exam_id', $examId)
                ->where('class_id', $classId)
                ->when($sectionId, fn($q) => $q->where(function ($q) use ($sectionId) {
                    $q->where('section_id', $sectionId)->orWhereNull('section_id');
                }))
                ->orderBy('sort_order')
                ->get()
            : collect();

        // If no exam subjects defined yet, suggest importing
        $classSubjectsAvailable = ($examId && $classId && $subjects->isEmpty())
            ? \App\Models\ClassSubject::where('class_id', $classId)
                ->where('is_active', true)->count()
            : 0;

        $students = collect();
        $existingMarks = collect();

        if ($examId && $classId && $subjectId) {
            $students = StudentProfile::where('class_id', $classId)
                ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
                ->where('status', 'active')
                ->orderBy('first_name')
                ->get();

            $existingMarks = StudentMark::where('exam_id', $examId)
                ->where('exam_subject_id', $subjectId)
                ->get()
                ->keyBy('student_profile_id');
        }

        $selectedSubject = $subjectId
            ? ExamSubject::find($subjectId)
            : null;

        return view('tenant.results.marks.index', compact(
            'exams', 'classes', 'sections', 'subjects',
            'students', 'existingMarks', 'selectedSubject',
            'examId', 'classId', 'sectionId', 'subjectId', 'classSubjectsAvailable'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'exam_id'         => ['required', 'exists:exams,id'],
            'exam_subject_id' => ['required', 'exists:exam_subjects,id'],
            'marks'           => ['required', 'array'],
        ]);

        $subject = ExamSubject::findOrFail($request->exam_subject_id);

        DB::beginTransaction();
        try {
            foreach ($request->marks as $studentId => $data) {
                $isAbsent = isset($data['is_absent']) && $data['is_absent'] == '1';
                $marks    = $isAbsent ? 0 : min(
                    floatval($data['marks_obtained'] ?? 0),
                    $subject->max_marks
                );

                StudentMark::updateOrCreate(
                    [
                        'exam_id'            => $request->exam_id,
                        'student_profile_id' => $studentId,
                        'exam_subject_id'    => $request->exam_subject_id,
                    ],
                    [
                        'marks_obtained' => $marks,
                        'is_absent'      => $isAbsent,
                        'remarks'        => $data['remarks'] ?? null,
                    ]
                );
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed: ' . $e->getMessage()]);
        }

        return redirect()->back()
            ->with('success', 'Marks saved for ' . count($request->marks) . ' students.');
    }
}