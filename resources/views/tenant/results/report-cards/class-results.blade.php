@extends('layouts.tenant')

@section('title', 'Class Results')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Report Cards / रिपोर्ट कार्ड</h4>
            <p class="text-muted mb-0 small">Class-wise results and individual report cards.</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Exam</label>
                        <select name="exam_id" class="form-select"
                                onchange="this.form.submit()">
                            <option value="">Select Exam</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}"
                                    {{ $examId == $exam->id ? 'selected' : '' }}>
                                    {{ $exam->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Class</label>
                        <select name="class_id" class="form-select"
                                onchange="this.form.submit()">
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}"
                                    {{ $classId == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if($sections->count() > 0)
                        <div class="col-md-2">
                            <label class="form-label fw-semibold small">Section</label>
                            <select name="section_id" class="form-select"
                                    onchange="this.form.submit()">
                                <option value="">All</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}"
                                        {{ $sectionId == $section->id ? 'selected' : '' }}>
                                        {{ $section->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($examId && $classId && count($results) > 0)

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    {{ $exam?->name }} — Results
                    <span class="badge bg-label-primary ms-1">{{ count($results) }} students</span>
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Rank</th>
                            <th>Student</th>
                            @foreach($subjects as $subject)
                                <th class="text-center small">
                                    {{ $subject->subject_name }}<br>
                                    <span class="text-muted" style="font-size:10px">
                                        /{{ $subject->max_marks }}
                                    </span>
                                </th>
                            @endforeach
                            <th class="text-center">Total</th>
                            <th class="text-center">%</th>
                            <th class="text-center">Grade</th>
                            <th class="text-center">Result</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                            @php
                                $student = $result['student'];
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        @if(($result['class_rank'] ?? 0) <= 3)
                                            @php
                                                $medals = [1=>'🥇',2=>'🥈',3=>'🥉'];
                                            @endphp
                                            <span>{{ $medals[$result['class_rank']] ?? '' }}</span>
                                        @endif
                                        <span class="badge bg-label-primary small">
                                            C: {{ $result['class_rank'] ?? '—' }}
                                        </span>
                                        @if(isset($result['section_rank']) && $sectionId)
                                            <span class="badge bg-label-info small mt-1">
                                                S: {{ $result['section_rank'] }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <p class="fw-semibold mb-0 small">{{ $student->full_name }}</p>
                                    @if($student->first_name_hi)
                                        <p class="text-muted mb-0" style="font-size:11px">
                                            {{ $student->full_name_hi }}
                                        </p>
                                    @endif
                                    <span class="text-muted" style="font-size:10px">
                                        {{ $student->admission_number }}
                                    </span>
                                </td>
                                @foreach($result['subjects'] as $sr)
                                    <td class="text-center small {{ $sr['is_absent'] ? 'bg-light' : (!$sr['passed'] ? 'table-danger' : '') }}">
                                        @if($sr['is_absent'])
                                            <span class="badge bg-label-secondary">AB</span>
                                        @else
                                            <span class="{{ !$sr['passed'] ? 'text-danger fw-bold' : '' }}">
                                                {{ $sr['obtained'] }}
                                            </span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-center fw-bold text-primary">
                                    {{ $result['total_marks'] }}/{{ $result['total_max'] }}
                                </td>
                                <td class="text-center">
                                    @php
                                        $pct = $result['overall_pct'];
                                        $pctColor = $pct >= 75 ? 'success' : ($pct >= 33 ? 'warning' : 'danger');
                                    @endphp
                                    <span class="badge bg-label-{{ $pctColor }}">
                                        {{ $pct }}%
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($result['overall_grade'])
                                        <span class="badge bg-label-{{ $result['overall_grade']->color ?? 'secondary' }}">
                                            {{ $result['overall_grade']->grade }}
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-label-{{ $result['overall_passed'] ? 'success' : 'danger' }}">
                                        {{ $result['overall_passed'] ? 'Pass' : 'Fail' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('tenant.results.report-cards.print', $student) }}?exam_id={{ $examId }}"
                                       target="_blank"
                                       class="btn btn-sm btn-icon btn-outline-primary"
                                       title="Print Report Card">
                                        <i class="icon-base ti tabler-printer"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($examId && $classId)
        <div class="card">
            <div class="card-body text-center py-5">
                <p class="text-muted mb-0">
                    No results found. Make sure marks are entered for this class.
                </p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="icon-base ti tabler-certificate"
                   style="font-size:3rem; color:#ccc;"></i>
                <p class="text-muted mt-2 mb-0">
                    Select exam and class to view results.
                </p>
            </div>
        </div>
    @endif

</div>
@endsection