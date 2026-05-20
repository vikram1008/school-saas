<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\TenantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    private function tenantUser(): TenantUser
    {
        return Auth::guard('tenant')->user();
    }

    private function requireAdmin(): void
    {
        if (! $this->tenantUser()->isSchoolAdmin()) {
            abort(403, 'Only school admins can perform this action.');
        }
    }

    public function index()
    {
        $this->tenantUser()->authorizePermission('can_view_exams');

        $activeYear = AcademicYear::active();
        $exams = Exam::where('academic_year_id', $activeYear?->id)
            ->withCount('subjects')
            ->latest()
            ->get();

        return view('tenant.results.exams.index', compact('exams', 'activeYear'));
    }

    public function store(Request $request)
    {
        $this->requireAdmin();

        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'exam_type' => ['required', 'in:unit_test,half_yearly,annual,quarterly,pre_board,other'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ]);

        Exam::create($request->only([
            'academic_year_id', 'name', 'name_hi',
            'exam_type', 'start_date', 'end_date', 'description',
        ]) + ['is_published' => false]);

        return redirect()
            ->route('tenant.results.exams.index')
            ->with('success', "Exam \"{$request->name}\" created.");
    }

    public function update(Request $request, Exam $exam)
    {
        $this->requireAdmin();

        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'exam_type' => ['required'],
        ]);

        $exam->update($request->only([
            'name', 'name_hi', 'exam_type',
            'start_date', 'end_date',
            'description', 'is_published',
        ]));

        return redirect()
            ->route('tenant.results.exams.index')
            ->with('success', 'Exam updated.');
    }

    public function destroy(Exam $exam)
    {
        $this->requireAdmin();

        $exam->delete();

        return redirect()
            ->route('tenant.results.exams.index')
            ->with('success', 'Exam deleted.');
    }

    // Manage subjects for an exam
    public function subjects(Request $request, Exam $exam)
    {
        $this->tenantUser()->authorizePermission('can_view_exams');

        $activeYear = AcademicYear::active();
        $classes = SchoolClass::where('academic_year_id', $activeYear?->id)
            ->orderBy('order')->get();

        $classId = $request->class_id;
        $sectionId = $request->section_id;

        $sections = $classId
            ? Section::where('class_id', $classId)->orderBy('order')->get()
            : collect();

        // Existing exam subjects
        $subjects = ($classId)
            ? ExamSubject::where('exam_id', $exam->id)
                ->where('class_id', $classId)
                ->orderBy('sort_order')
                ->get()
            : collect();

        // Class subjects not yet added to this exam
        $classSubjects = $classId
            ? ClassSubject::where('class_id', $classId)
                ->where('is_active', true)
                ->whereNotIn('subject_name', $subjects->pluck('subject_name'))
                ->orderBy('sort_order')
                ->get()
            : collect();

        return view('tenant.results.exams.subjects', compact(
            'exam',
            'classes',
            'sections',
            'subjects',
            'classSubjects',
            'classId',
            'sectionId'
        ));
    }

    public function storeSubject(Request $request, Exam $exam)
    {
        $this->requireAdmin();

        // Bulk import from class subjects
        if ($request->boolean('import_all') && $request->class_id) {

            $classSubjects = ClassSubject::where('class_id', $request->class_id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            if ($classSubjects->isEmpty()) {
                return redirect()
                    ->route('tenant.results.exams.subjects', [
                        'exam' => $exam,
                        'class_id' => $request->class_id,
                    ])
                    ->withErrors(['error' => 'No subjects found for this class. Add subjects to the class first.']);
            }

            $order = ExamSubject::where('exam_id', $exam->id)
                ->where('class_id', $request->class_id)
                ->max('sort_order') ?? 0;

            $imported = 0;
            foreach ($classSubjects as $cs) {
                $exists = ExamSubject::where('exam_id', $exam->id)
                    ->where('class_id', $request->class_id)
                    ->where('subject_name', $cs->subject_name)
                    ->exists();

                if (! $exists) {
                    ExamSubject::create([
                        'exam_id' => $exam->id,
                        'class_id' => $request->class_id,
                        'section_id' => $request->section_id ?: null,
                        'subject_name' => $cs->subject_name,
                        'subject_name_hi' => $cs->subject_name_hi,
                        'max_marks' => $request->default_max_marks ?? 100,
                        'pass_marks' => $request->default_pass_marks ?? 33,
                        'sort_order' => ++$order,
                    ]);
                    $imported++;
                }
            }

            return redirect()
                ->route('tenant.results.exams.subjects', [
                    'exam' => $exam,
                    'class_id' => $request->class_id,
                    'section_id' => $request->section_id,
                ])
                ->with('success', "{$imported} subjects imported successfully.");
        }

        // Single subject — from dropdown
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'subject_name' => ['required', 'string', 'max:100'],
            'max_marks' => ['required', 'integer', 'min:1'],
            'pass_marks' => ['required', 'integer', 'min:1'],
        ]);

        // Duplicate check
        $exists = ExamSubject::where('exam_id', $exam->id)
            ->where('class_id', $request->class_id)
            ->where('subject_name', $request->subject_name)
            ->exists();

        if ($exists) {
            return redirect()
                ->route('tenant.results.exams.subjects', [
                    'exam' => $exam,
                    'class_id' => $request->class_id,
                    'section_id' => $request->section_id,
                ])
                ->withErrors(['error' => "\"{$request->subject_name}\" is already added to this exam."]);
        }

        ExamSubject::create([
            'exam_id' => $exam->id,
            'class_id' => $request->class_id,
            'section_id' => $request->section_id ?: null,
            'subject_name' => $request->subject_name,
            'subject_name_hi' => $request->subject_name_hi,
            'max_marks' => $request->max_marks,
            'pass_marks' => $request->pass_marks,
            'sort_order' => ExamSubject::where('exam_id', $exam->id)
                ->where('class_id', $request->class_id)
                ->max('sort_order') + 1,
        ]);

        return redirect()
            ->route('tenant.results.exams.subjects', [
                'exam' => $exam,
                'class_id' => $request->class_id,
                'section_id' => $request->section_id,
            ])
            ->with('success', 'Subject added.');
    }

    public function destroySubject(Exam $exam, ExamSubject $subject)
    {
        $this->requireAdmin();

        $classId = $subject->class_id;
        $sectionId = $subject->section_id;
        $subject->delete();

        return redirect()
            ->route('tenant.results.exams.subjects', [
                'exam' => $exam,
                'class_id' => $classId,
                'section_id' => $sectionId,
            ])
            ->with('success', 'Subject removed.');
    }
}
