@extends('layouts.tenant')

@section('title', 'Enter Marks')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Enter Marks / अंक दर्ज करें</h4>
            <p class="text-muted mb-0 small">Enter student marks subject-wise.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-4">
            <i class="icon-base ti tabler-alert-circle me-1"></i>
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($examId && $classId && $classSubjectsAvailable > 0 && $subjects->isEmpty())
        <div class="alert alert-warning mb-4">
            <i class="icon-base ti tabler-alert-triangle me-1"></i>
            No subjects defined for this exam + class yet.
            <a href="{{ route('tenant.results.exams.subjects', ['exam' => $examId, 'class_id' => $classId]) }}"
            class="alert-link">
                Import class subjects into this exam →
            </a>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" id="marksFilterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Exam</label>
                        <select name="exam_id" class="form-select"
                                onchange="document.getElementById('marksFilterForm').submit()">
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
                                onchange="document.getElementById('marksFilterForm').submit()">
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
                                    onchange="document.getElementById('marksFilterForm').submit()">
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
                    @if($subjects->count() > 0)
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Subject</label>
                            <select name="subject_id" class="form-select"
                                    onchange="document.getElementById('marksFilterForm').submit()">
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}"
                                        {{ $subjectId == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->subject_name }}
                                        (Max: {{ $subject->max_marks }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($examId && $classId && $subjectId && $students->count() > 0)
        {{-- Subject Info --}}
        <div class="alert alert-info mb-4 small">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="icon-base ti tabler-book me-1"></i>
                    <strong>{{ $selectedSubject?->subject_name }}</strong>
                    @if($selectedSubject?->subject_name_hi)
                        · {{ $selectedSubject->subject_name_hi }}
                    @endif
                </div>
                <div>
                    Max Marks: <strong>{{ $selectedSubject?->max_marks }}</strong>
                    &nbsp;·&nbsp;
                    Pass Marks: <strong>{{ $selectedSubject?->pass_marks }}</strong>
                </div>
            </div>
        </div>

        {{-- Marks Form --}}
        <form action="{{ route('tenant.results.marks.store') }}" method="POST">
            @csrf
            <input type="hidden" name="exam_id" value="{{ $examId }}">
            <input type="hidden" name="exam_subject_id" value="{{ $subjectId }}">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        Enter Marks — {{ $students->count() }} Students
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-warning"
                            onclick="markAllAbsent()">
                        <i class="icon-base ti tabler-user-off me-1"></i> Mark All Absent
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th style="width:150px">
                                    Marks / {{ $selectedSubject?->max_marks }}
                                </th>
                                <th class="text-center">Absent</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $i => $student)
                                @php $existing = $existingMarks->get($student->id); @endphp
                                <tr id="row-{{ $student->id }}"
                                    class="{{ $existing?->is_absent ? 'table-secondary' : '' }}">
                                    <td class="text-muted small">{{ $i + 1 }}</td>
                                    <td>
                                        <p class="fw-semibold mb-0 small">
                                            {{ $student->full_name }}
                                        </p>
                                        @if($student->first_name_hi)
                                            <p class="text-muted mb-0" style="font-size:11px">
                                                {{ $student->full_name_hi }}
                                            </p>
                                        @endif
                                        <span class="text-muted" style="font-size:10px">
                                            {{ $student->admission_number }}
                                        </span>
                                    </td>
                                    <td>
                                        <input type="number"
                                               name="marks[{{ $student->id }}][marks_obtained]"
                                               id="marks_{{ $student->id }}"
                                               class="form-control form-control-sm marks-input"
                                               value="{{ $existing?->is_absent ? '' : $existing?->marks_obtained }}"
                                               min="0"
                                               max="{{ $selectedSubject?->max_marks }}"
                                               step="0.5"
                                               {{ $existing?->is_absent ? 'disabled' : '' }}
                                               onchange="highlightRow({{ $student->id }}, this.value)">
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox"
                                               name="marks[{{ $student->id }}][is_absent]"
                                               id="absent_{{ $student->id }}"
                                               class="form-check-input absent-check"
                                               value="1"
                                               {{ $existing?->is_absent ? 'checked' : '' }}
                                               data-student="{{ $student->id }}"
                                               onchange="toggleAbsent(this)">
                                    </td>
                                    <td>
                                        <input type="text"
                                               name="marks[{{ $student->id }}][remarks]"
                                               class="form-control form-control-sm"
                                               value="{{ $existing?->remarks }}"
                                               placeholder="Optional...">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <span class="text-muted small">
                        {{ $students->count() }} students
                    </span>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base ti tabler-device-floppy me-1"></i>
                        Save All Marks
                    </button>
                </div>
            </div>
        </form>

    @elseif($examId && $classId && $subjectId && $students->count() === 0)
        <div class="card">
            <div class="card-body text-center py-4">
                <p class="text-muted mb-0">No active students found for this class.</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="icon-base ti tabler-pencil"
                   style="font-size:3rem; color:#ccc;"></i>
                <p class="text-muted mt-2 mb-0">
                    Select exam, class and subject above to enter marks.
                </p>
            </div>
        </div>
    @endif

</div>

@push('scripts')
<script>
const maxMarks = {{ $selectedSubject?->max_marks ?? 100 }};
const passMarks = {{ $selectedSubject?->pass_marks ?? 33 }};

function toggleAbsent(checkbox) {
    const studentId = checkbox.dataset.student;
    const marksInput = document.getElementById(`marks_${studentId}`);
    const row = document.getElementById(`row-${studentId}`);

    if (checkbox.checked) {
        marksInput.disabled = true;
        marksInput.value = '';
        row.classList.add('table-secondary');
        row.classList.remove('table-danger', 'table-success');
    } else {
        marksInput.disabled = false;
        row.classList.remove('table-secondary');
    }
}

function highlightRow(studentId, value) {
    const row = document.getElementById(`row-${studentId}`);
    row.classList.remove('table-danger', 'table-success', 'table-warning');
    if (value === '' || value === null) return;
    const marks = parseFloat(value);
    if (marks < passMarks) {
        row.classList.add('table-danger');
    } else if (marks >= maxMarks * 0.75) {
        row.classList.add('table-success');
    } else {
        row.classList.add('table-warning');
    }
}

function markAllAbsent() {
    if (!confirm('Mark all students as absent?')) return;
    document.querySelectorAll('.absent-check').forEach(cb => {
        cb.checked = true;
        toggleAbsent(cb);
    });
}

// Highlight existing marks on load
document.querySelectorAll('.marks-input').forEach(input => {
    if (input.value) {
        const studentId = input.id.replace('marks_', '');
        highlightRow(studentId, input.value);
    }
});
</script>
@endpush

@endsection