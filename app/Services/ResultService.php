<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\GradeScale;
use App\Models\StudentMark;
use App\Models\StudentProfile;
use App\Models\StudentAttendance;

class ResultService
{
    /**
     * Get full result data for a student in an exam.
     */
    public function getStudentResult(
        StudentProfile $student,
        Exam $exam
    ): array {
        $subjects = ExamSubject::where('exam_id', $exam->id)
            ->where('class_id', $student->class_id)
            ->when($student->section_id, fn($q) => $q->where(function ($q) use ($student) {
                $q->where('section_id', $student->section_id)
                  ->orWhereNull('section_id');
            }))
            ->orderBy('sort_order')
            ->get();

        $marks = StudentMark::where('exam_id', $exam->id)
            ->where('student_profile_id', $student->id)
            ->get()
            ->keyBy('exam_subject_id');

        $subjectResults = [];
        $totalMarks     = 0;
        $totalMax       = 0;
        $totalPassed    = 0;
        $isAbsent       = false;

        foreach ($subjects as $subject) {
            $mark   = $marks->get($subject->id);
            $obtained = $mark?->is_absent ? 0 : ($mark?->marks_obtained ?? 0);
            $passed   = !$mark?->is_absent && $obtained >= $subject->pass_marks;

            $percentage = $subject->max_marks > 0
                ? round(($obtained / $subject->max_marks) * 100, 1)
                : 0;

            $grade = GradeScale::getGrade($percentage, $exam->academic_year_id);

            $subjectResults[] = [
                'subject'    => $subject,
                'mark'       => $mark,
                'obtained'   => $obtained,
                'max'        => $subject->max_marks,
                'pass_marks' => $subject->pass_marks,
                'percentage' => $percentage,
                'grade'      => $grade,
                'passed'     => $passed,
                'is_absent'  => $mark?->is_absent ?? false,
            ];

            if (!$mark?->is_absent) {
                $totalMarks += $obtained;
                $totalMax   += $subject->max_marks;
                if ($passed) $totalPassed++;
            }
        }

        $overallPct    = $totalMax > 0 ? round(($totalMarks / $totalMax) * 100, 2) : 0;
        $overallGrade  = GradeScale::getGrade($overallPct, $exam->academic_year_id);
        $overallPassed = $totalPassed === count($subjects) && $totalMax > 0;

        // Attendance for exam period
        $attendancePct = 0;
        if ($exam->start_date && $exam->end_date) {
            $attended = StudentAttendance::where('student_profile_id', $student->id)
                ->whereBetween('date', [$exam->start_date, $exam->end_date])
                ->whereIn('status', ['present', 'late', 'half_day'])
                ->count();
            $total = StudentAttendance::where('student_profile_id', $student->id)
                ->whereBetween('date', [$exam->start_date, $exam->end_date])
                ->count();
            $attendancePct = $total > 0 ? round(($attended / $total) * 100) : 0;
        }

        return [
            'student'         => $student,
            'subjects'        => $subjectResults,
            'total_marks'     => $totalMarks,
            'total_max'       => $totalMax,
            'overall_pct'     => $overallPct,
            'overall_grade'   => $overallGrade,
            'overall_passed'  => $overallPassed,
            'attendance_pct'  => $attendancePct,
        ];
    }

    /**
     * Get class results with ranks.
     */
    public function getClassResults(Exam $exam, int $classId, ?int $sectionId = null): array
    {
        $students = StudentProfile::where('class_id', $classId)
            ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        $results = [];
        foreach ($students as $student) {
            $result    = $this->getStudentResult($student, $exam);
            $results[] = $result;
        }

        // Sort by total marks descending for ranking
        usort($results, fn($a, $b) => $b['total_marks'] <=> $a['total_marks']);

        // Assign section rank
        foreach ($results as $i => &$result) {
            $result['section_rank'] = $i + 1;
        }

        // Class rank (across all sections)
        if ($sectionId) {
            $allStudents = StudentProfile::where('class_id', $classId)
                ->where('status', 'active')
                ->get();
            $allResults = [];
            foreach ($allStudents as $student) {
                $r = $this->getStudentResult($student, $exam);
                $r['student_id'] = $student->id;
                $allResults[] = $r;
            }
            usort($allResults, fn($a, $b) => $b['total_marks'] <=> $a['total_marks']);
            $classRanks = [];
            foreach ($allResults as $i => $r) {
                $classRanks[$r['student_id']] = $i + 1;
            }
            foreach ($results as &$result) {
                $result['class_rank'] = $classRanks[$result['student']['id']] ?? '—';
            }
        } else {
            foreach ($results as $i => &$result) {
                $result['class_rank'] = $i + 1;
            }
        }

        return $results;
    }
}