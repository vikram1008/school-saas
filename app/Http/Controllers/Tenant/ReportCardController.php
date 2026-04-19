<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\StudentProfile;
use App\Services\ResultService;
use Illuminate\Http\Request;

class ReportCardController extends Controller
{
    public function __construct(protected ResultService $resultService) {}

    // Class result list
    public function classResults(Request $request)
    {
        $activeYear = AcademicYear::active();
        $exams      = Exam::where('academic_year_id', $activeYear?->id)->latest()->get();
        $classes    = $activeYear
            ? SchoolClass::where('academic_year_id', $activeYear->id)
                ->orderBy('order')->get()
            : collect();

        $examId    = $request->exam_id;
        $classId   = $request->class_id;
        $sectionId = $request->section_id;

        $sections = $classId
            ? Section::where('class_id', $classId)->orderBy('order')->get()
            : collect();

        $results = [];
        $exam    = null;
        $subjects = collect();

        if ($examId && $classId) {
            $exam    = Exam::findOrFail($examId);
            $results = $this->resultService->getClassResults($exam, $classId, $sectionId ?: null);

            $subjects = \App\Models\ExamSubject::where('exam_id', $examId)
                ->where('class_id', $classId)
                ->orderBy('sort_order')
                ->get();
        }

        return view('tenant.results.report-cards.class-results', compact(
            'exams', 'classes', 'sections',
            'results', 'exam', 'subjects',
            'examId', 'classId', 'sectionId'
        ));
    }

    // Individual printable report card
    public function print(Request $request, StudentProfile $student)
    {
        $exam    = Exam::findOrFail($request->exam_id);
        $result  = $this->resultService->getStudentResult($student, $exam);
        $student->load(['class', 'section', 'familyDetail']);

        return view('tenant.results.report-cards.print', compact(
            'student', 'exam', 'result'
        ));
    }
}