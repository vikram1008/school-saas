@extends('layouts.tenant')

@section('title', 'Assign Subjects')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Assign Subjects / विषय असाइन करें</h4>
            <p class="text-muted mb-0 small">
                Assign subjects and teachers to each class.
            </p>
        </div>
        <a href="{{ route('tenant.subjects.index') }}"
           class="btn btn-outline-secondary">
            <i class="icon-base ti tabler-list me-1"></i>
            View All Subjects
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- LEFT: Class Selector --}}
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">
                        <i class="icon-base ti tabler-door me-1 text-primary"></i>
                        Select Class
                    </h6>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($classes as $class)
                        @php
                            $subjectCount = \App\Models\ClassSubject::where('class_id', $class->id)->count();
                        @endphp
                        <a href="{{ route('tenant.subjects.assign', ['class_id' => $class->id]) }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
                                  {{ $classId == $class->id ? 'active' : '' }}">
                            <div>
                                <p class="fw-semibold mb-0 small">{{ $class->name }}</p>
                                @if($class->has_sections)
                                    <p class="mb-0 opacity-75" style="font-size:10px">
                                        {{ $class->sections->count() }} sections
                                    </p>
                                @endif
                            </div>
                            <span class="badge {{ $classId == $class->id ? 'bg-white text-primary' : 'bg-label-primary' }}">
                                {{ $subjectCount }}
                            </span>
                        </a>
                    @empty
                        <div class="list-group-item text-muted small text-center py-3">
                            No classes found.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT: Subject Assignment --}}
        <div class="col-lg-9">

            @if(!$classId)
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="icon-base ti tabler-arrow-left"
                           style="font-size:3rem; color:#ccc;"></i>
                        <p class="text-muted mt-2 mb-0">
                            Select a class from the left to manage its subjects.
                        </p>
                    </div>
                </div>
            @else
                @php
                    $selectedClass = $classes->firstWhere('id', $classId);
                @endphp

                {{-- Class Header --}}
                <div class="card mb-4">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold mb-1">{{ $selectedClass?->name }}</h5>
                                <p class="text-muted small mb-0">
                                    {{ $assignedSubjects->count() }} subjects assigned
                                    · {{ $assignedSubjects->sum('periods_per_week') }} periods/week total
                                </p>
                            </div>
                            {{-- Section filter if class has sections --}}
                            @if($selectedClass?->has_sections && $selectedClass->sections->count() > 0)
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">Section:</span>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('tenant.subjects.assign', ['class_id' => $classId]) }}"
                                           class="btn {{ !$sectionId ? 'btn-primary' : 'btn-outline-primary' }}">
                                            All
                                        </a>
                                        @foreach($selectedClass->sections as $section)
                                            <a href="{{ route('tenant.subjects.assign', ['class_id' => $classId, 'section_id' => $section->id]) }}"
                                               class="btn {{ $sectionId == $section->id ? 'btn-primary' : 'btn-outline-primary' }}">
                                                {{ $section->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Add Subject Card --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold">
                            <i class="icon-base ti tabler-plus me-1 text-success"></i>
                            Assign New Subject
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tenant.subjects.assign.save') }}" method="POST">
                            @csrf
                            <input type="hidden" name="class_id" value="{{ $classId }}">
                            <input type="hidden" name="section_id" value="{{ $sectionId }}">

                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        Subject <span class="text-danger">*</span>
                                    </label>
                                    <select name="subject_name"
                                            class="form-select"
                                            id="subjectSelect"
                                            onchange="fillSubjectHindi(this)"
                                            required>
                                        <option value="">— Select Subject —</option>
                                        @foreach($availableSubjects as $s)
                                            <option value="{{ $s->subject_name }}"
                                                    data-hi="{{ $s->subject_name_hi }}">
                                                {{ $s->subject_name }}
                                                @if($s->subject_name_hi)
                                                    · {{ $s->subject_name_hi }}
                                                @endif
                                            </option>
                                        @endforeach
                                        <option value="__new__">
                                            + Add New Subject...
                                        </option>
                                    </select>
                                </div>

                                {{-- New Subject Name (shown when "+ Add New" selected) --}}
                                <div class="col-md-3" id="newSubjectFields" style="display:none;">
                                    <label class="form-label fw-semibold">New Subject Name</label>
                                    <input type="text" id="newSubjectName"
                                           class="form-control"
                                           placeholder="e.g. Environmental Science"
                                           data-hindi-target="#newSubjectHi">
                                </div>
                                <div class="col-md-2" id="newSubjectHiField" style="display:none;">
                                    <label class="form-label fw-semibold">
                                        विषय नाम <span class="badge bg-label-warning">हिं</span>
                                    </label>
                                    <input type="text" id="newSubjectHi"
                                           class="form-control"
                                           placeholder="जैसे: पर्यावरण">
                                </div>
                                <input type="hidden" name="subject_name_hi" id="subjectNameHiHidden">

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Subject Teacher</label>
                                    <select name="teacher_id" class="form-select">
                                        <option value="">— No Teacher —</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}">
                                                {{ $teacher->full_name }}
                                                @if($teacher->designation)
                                                    ({{ $teacher->designation }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Periods/Week</label>
                                    <input type="number" name="periods_per_week"
                                           class="form-control" value="5" min="0" max="30">
                                </div>

                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="icon-base ti tabler-check me-1"></i>
                                        Assign
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Assigned Subjects --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="icon-base ti tabler-book me-1 text-info"></i>
                            Assigned Subjects
                            <span class="badge bg-label-primary ms-1">
                                {{ $assignedSubjects->count() }}
                            </span>
                        </h6>
                        @if($assignedSubjects->count() > 0)
                            <span class="text-muted small">
                                Total:
                                <strong>{{ $assignedSubjects->sum('periods_per_week') }}</strong>
                                periods/week
                            </span>
                        @endif
                    </div>

                    @if($assignedSubjects->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Subject / विषय</th>
                                        <th>Subject Teacher</th>
                                        <th class="text-center">Periods/Week</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignedSubjects as $i => $subject)
                                        <tr>
                                            <td class="text-muted small fw-semibold">
                                                {{ $i + 1 }}
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar avatar-sm">
                                                        <span class="avatar-initial rounded bg-label-primary"
                                                              style="font-size:11px">
                                                            {{ strtoupper(substr($subject->subject_name, 0, 2)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="fw-semibold mb-0">
                                                            {{ $subject->subject_name }}
                                                        </p>
                                                        @if($subject->subject_name_hi)
                                                            <p class="text-muted small mb-0">
                                                                <span class="badge bg-label-warning me-1"
                                                                      style="font-size:9px">हिं</span>
                                                                {{ $subject->subject_name_hi }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                {{-- Inline teacher change --}}
                                                <form action="{{ route('tenant.subjects.update', $subject) }}"
                                                      method="POST"
                                                      class="d-flex gap-1 align-items-center">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="subject_name"
                                                           value="{{ $subject->subject_name }}">
                                                    <input type="hidden" name="subject_name_hi"
                                                           value="{{ $subject->subject_name_hi }}">
                                                    <input type="hidden" name="periods_per_week"
                                                           value="{{ $subject->periods_per_week }}">
                                                    <input type="hidden" name="is_active"
                                                           value="{{ $subject->is_active ? '1' : '0' }}">
                                                    <select name="teacher_id"
                                                            class="form-select form-select-sm"
                                                            style="min-width:180px"
                                                            onchange="this.form.submit()">
                                                        <option value="">— No Teacher —</option>
                                                        @foreach($teachers as $teacher)
                                                            <option value="{{ $teacher->id }}"
                                                                {{ $subject->teacher_id == $teacher->id ? 'selected' : '' }}>
                                                                {{ $teacher->full_name }}
                                                                @if($teacher->designation)
                                                                    ({{ $teacher->designation }})
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-label-secondary">
                                                    {{ $subject->periods_per_week }}
                                                    <span class="opacity-75">/ wk</span>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-label-{{ $subject->is_active ? 'success' : 'secondary' }}">
                                                    {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <form action="{{ route('tenant.subjects.assign.remove', $subject) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Remove {{ $subject->subject_name }} from this class?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-icon btn-outline-danger"
                                                            title="Remove">
                                                        <i class="icon-base ti tabler-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold text-muted small">
                                            Total Periods / Week
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">
                                                {{ $assignedSubjects->sum('periods_per_week') }}
                                            </span>
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="card-body text-center py-5">
                            <i class="icon-base ti tabler-book-off"
                               style="font-size:3rem; color:#ccc;"></i>
                            <p class="text-muted mt-2 mb-3">
                                No subjects assigned to
                                <strong>{{ $selectedClass?->name }}</strong> yet.
                            </p>
                            <p class="text-muted small mb-0">
                                Use the form above to assign subjects.
                            </p>
                        </div>
                    @endif
                </div>

            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function fillSubjectHindi(select) {
    const val = select.value;
    const hi  = select.options[select.selectedIndex]?.dataset.hi || '';

    const newFields  = document.getElementById('newSubjectFields');
    const newHiField = document.getElementById('newSubjectHiField');
    const hiddenHi   = document.getElementById('subjectNameHiHidden');

    if (val === '__new__') {
        newFields.style.display  = 'block';
        newHiField.style.display = 'block';
        hiddenHi.value = '';
    } else {
        newFields.style.display  = 'none';
        newHiField.style.display = 'none';
        hiddenHi.value = hi;
    }
}

// Handle "Add New Subject" option
document.getElementById('subjectSelect')?.addEventListener('change', function() {
    if (this.value === '__new__') {
        // Replace select value with text input value on submit
        this.closest('form').addEventListener('submit', function(e) {
            const select   = document.getElementById('subjectSelect');
            const hiddenHi = document.getElementById('subjectNameHiHidden');

            if (select.value === '__new__') {
                const newName = document.getElementById('newSubjectName').value.trim();
                const newHi   = document.getElementById('newSubjectHi').value.trim();

                if (!newName) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Required',
                        text: 'Please enter the new subject name.',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary waves-effect waves-light'
                        },
                        buttonsStyling: false,
                    });
                    return;
                }
                // Swap select out, inject hidden input with real name
                select.name    = '';
                hiddenHi.value = newHi;

                const inp   = document.createElement('input');
                inp.type    = 'hidden';
                inp.name    = 'subject_name';
                inp.value   = newName;
                this.appendChild(inp);

                const inpHi  = document.createElement('input');
                inpHi.type   = 'hidden';
                inpHi.name   = 'subject_name_hi';
                inpHi.value  = newHi;
                this.appendChild(inpHi);
            }

        }, { once: true });
    }
});
</script>
@endpush

@endsection