@extends('layouts.tenant')

@section('title', 'Exam Subjects — ' . $exam->name)

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('tenant.results.exams.index') }}" class="btn btn-icon btn-outline-secondary me-3">
                <i class="icon-base ti tabler-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0">{{ $exam->name }} — Subjects</h4>
                <p class="text-muted small mb-0">
                    Define subjects and max marks per class for this exam.
                </p>
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

        {{-- Class Filter --}}
        <div class="card mb-4">
            <div class="card-body py-3">
                <form method="GET" id="subjectFilterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Class</label>
                            <select name="class_id" class="form-select"
                                onchange="document.getElementById('subjectFilterForm').submit()">
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if($sections->count() > 0)
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">Section</label>
                                <select name="section_id" class="form-select"
                                    onchange="document.getElementById('subjectFilterForm').submit()">
                                    <option value="">All</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}" {{ $sectionId == $section->id ? 'selected' : '' }}>
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

        @if($classId)
            <div class="row g-4">
                {{-- Subject List --}}
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                Subjects
                                <span class="badge bg-label-primary ms-1">{{ $subjects->count() }}</span>
                            </h5>
                        </div>
                        @if($subjects->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Subject / विषय</th>
                                            <th class="text-center">Max Marks</th>
                                            <th class="text-center">Pass Marks</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subjects as $i => $subject)
                                            <tr>
                                                <td class="text-muted small">{{ $i + 1 }}</td>
                                                <td>
                                                    <p class="fw-semibold mb-0">{{ $subject->subject_name }}</p>
                                                    @if($subject->subject_name_hi)
                                                        <p class="text-muted small mb-0">
                                                            <span class="badge bg-label-warning me-1">हिं</span>
                                                            {{ $subject->subject_name_hi }}
                                                        </p>
                                                    @endif
                                                </td>
                                                <td class="text-center fw-bold text-primary">
                                                    {{ $subject->max_marks }}
                                                </td>
                                                <td class="text-center text-warning">
                                                    {{ $subject->pass_marks }}
                                                </td>
                                                <td class="text-center">
                                                    <form
                                                        action="{{ route('tenant.results.exams.subjects.destroy', [$exam, $subject]) }}"
                                                        method="POST" onsubmit="return confirm('Remove {{ $subject->subject_name }}?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger">
                                                            <i class="icon-base ti tabler-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="2" class="text-end fw-bold">Total</td>
                                            <td class="text-center fw-bold text-primary">
                                                {{ $subjects->sum('max_marks') }}
                                            </td>
                                            <td class="text-center fw-bold text-warning">
                                                {{ $subjects->sum('pass_marks') }}
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="card-body text-center py-4">
                                <p class="text-muted mb-0">No subjects defined for this class yet.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Add Subject Form --}}
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Add Subjects</h5>
                        </div>
                        <div class="card-body">

                            {{-- Import All from Class --}}
                            @if($classSubjects->count() > 0)
                                <div class="alert alert-info small mb-3">
                                    <i class="icon-base ti tabler-info-circle me-1"></i>
                                    <strong>{{ $classSubjects->count() }} class subjects</strong>
                                    available to import.
                                </div>
                                <form action="{{ route('tenant.results.exams.subjects.store', $exam) }}"
                                    method="POST" class="mb-3" id="importAllForm">
                                    @csrf
                                    <input type="hidden" name="class_id" value="{{ $classId }}">
                                    <input type="hidden" name="section_id" value="{{ $sectionId }}">
                                    <input type="hidden" name="import_all" value="1">
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <label class="form-label fw-semibold small">Default Max Marks</label>
                                            <input type="number" name="default_max_marks"
                                                class="form-control form-control-sm"
                                                value="100" min="1">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label fw-semibold small">Default Pass Marks</label>
                                            <input type="number" name="default_pass_marks"
                                                class="form-control form-control-sm"
                                                value="33" min="1">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100 btn-sm">
                                        <i class="icon-base ti tabler-download me-1"></i>
                                        Import All ({{ $classSubjects->count() }}) Class Subjects
                                    </button>
                                </form>
                                <hr>
                                <p class="text-muted small fw-semibold mb-2">Or add individually:</p>
                            @elseif($classId && $subjects->count() > 0 && $classSubjects->count() === 0)
                                <div class="alert alert-success small mb-3">
                                    <i class="icon-base ti tabler-circle-check me-1"></i>
                                    All class subjects imported.
                                </div>
                            @endif

                            {{-- Single Subject Form --}}
                            <form action="{{ route('tenant.results.exams.subjects.store', $exam) }}"
                                method="POST">
                                @csrf
                                <input type="hidden" name="class_id" value="{{ $classId }}">
                                <input type="hidden" name="section_id" value="{{ $sectionId }}">
                                <div class="mb-2">
                                    <label class="form-label fw-semibold small">Subject</label>
                                    <select name="subject_name" class="form-select form-select-sm"
                                            onchange="fillSubjectHindi(this)" required>
                                        <option value="">— Select Subject —</option>
                                        @foreach(\App\Models\ClassSubject::where('class_id', $classId)->where('is_active', true)->orderBy('sort_order')->get() as $cs)
                                            @php
                                                $alreadyAdded = $subjects->contains('subject_name', $cs->subject_name);
                                            @endphp
                                            <option value="{{ $cs->subject_name }}"
                                                    data-hi="{{ $cs->subject_name_hi }}"
                                                    {{ $alreadyAdded ? 'disabled' : '' }}>
                                                {{ $cs->subject_name }}
                                                @if($cs->subject_name_hi) · {{ $cs->subject_name_hi }} @endif
                                                @if($alreadyAdded) ✓ (already added) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="subject_name_hi" id="examSubjectHiHidden">
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="form-label fw-semibold small">Max Marks</label>
                                        <input type="number" name="max_marks"
                                            class="form-control form-control-sm" value="100" min="1">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-semibold small">Pass Marks</label>
                                        <input type="number" name="pass_marks"
                                            class="form-control form-control-sm" value="33" min="1">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 btn-sm">
                                    <i class="icon-base ti tabler-plus me-1"></i> Add Subject
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="icon-base ti tabler-book" style="font-size:3rem; color:#ccc;"></i>
                    <p class="text-muted mt-2 mb-0">
                        Select a class to manage its exam subjects.
                    </p>
                </div>
            </div>
        @endif

    </div>
@endsection

@push('scripts')
<script>
function fillSubjectHindi(select) {
    const hi = select.options[select.selectedIndex]?.dataset.hi || '';
    document.getElementById('examSubjectHiHidden').value = hi;
}
</script>
@endpush