@extends('layouts.tenant')

@section('title', 'Subjects')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Subjects / विषय</h4>
            <p class="text-muted mb-0 small">
                All subjects defined across classes for
                <strong>{{ $activeYear?->name ?? 'active year' }}</strong>.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tenant.subjects.assign') }}"
               class="btn btn-outline-primary">
                <i class="icon-base ti tabler-book-upload me-1"></i>
                Assign to Class
            </a>
            <button type="button" class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#addSubjectModal">
                <i class="icon-base ti tabler-plus me-1"></i>
                Add Subject
            </button>
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

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card text-center">
                <div class="card-body py-3">
                    <h3 class="fw-bold text-primary mb-1">{{ $uniqueSubjects->count() }}</h3>
                    <p class="text-muted small mb-0">Unique Subjects</p>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card text-center">
                <div class="card-body py-3">
                    <h3 class="fw-bold text-success mb-1">{{ $classes->count() }}</h3>
                    <p class="text-muted small mb-0">Classes</p>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card text-center">
                <div class="card-body py-3">
                    <h3 class="fw-bold text-info mb-1">
                        {{ $subjects->sum(fn($s) => $s->count()) }}
                    </h3>
                    <p class="text-muted small mb-0">Total Assignments</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Subjects by Class --}}
    @forelse($classes as $class)
        @if($class->subjects->count() > 0)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded bg-label-primary">
                                {{ $class->order }}
                            </span>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">{{ $class->name }}</h5>
                            <span class="text-muted small">
                                {{ $class->subjects->count() }} subjects
                                · {{ $class->subjects->sum('periods_per_week') }} periods/week
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('tenant.subjects.assign', ['class_id' => $class->id]) }}"
                       class="btn btn-sm btn-outline-primary">
                        <i class="icon-base ti tabler-plus me-1"></i>
                        Add Subject
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Subject / विषय</th>
                                <th>Subject Teacher</th>
                                <th class="text-center">Periods/Week</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($class->subjects->sortBy('sort_order') as $i => $subject)
                                <tr>
                                    <td class="text-muted small">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar avatar-sm">
                                                <span class="avatar-initial rounded bg-label-info"
                                                      style="font-size:11px">
                                                    {{ strtoupper(substr($subject->subject_name, 0, 2)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="fw-semibold mb-0">{{ $subject->subject_name }}</p>
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
                                        @if($subject->teacher)
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar avatar-xs">
                                                    <span class="avatar-initial rounded-circle bg-label-success"
                                                          style="font-size:9px">
                                                        {{ strtoupper(substr($subject->teacher->first_name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="fw-semibold mb-0 small">
                                                        {{ $subject->teacher->full_name }}
                                                    </p>
                                                    <p class="text-muted mb-0"
                                                       style="font-size:10px">
                                                        {{ $subject->teacher->designation ?? '' }}
                                                    </p>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted small">
                                                <i class="icon-base ti tabler-user-off me-1"></i>
                                                Not assigned
                                            </span>
                                        @endif
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
                                        <div class="d-flex justify-content-center gap-1">
                                            <button type="button"
                                                    class="btn btn-sm btn-icon btn-outline-warning"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editSubjectModal{{ $subject->id }}"
                                                    title="Edit">
                                                <i class="icon-base ti tabler-edit"></i>
                                            </button>
                                            <form action="{{ route('tenant.subjects.destroy', $subject) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Remove {{ $subject->subject_name }} from {{ $class->name }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-icon btn-outline-danger"
                                                        title="Remove">
                                                    <i class="icon-base ti tabler-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Edit Modal --}}
                                <div class="modal fade"
                                     id="editSubjectModal{{ $subject->id }}"
                                     tabindex="-1">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <form action="{{ route('tenant.subjects.update', $subject) }}"
                                                  method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold">
                                                        Edit Subject
                                                    </h6>
                                                    <button type="button"
                                                            class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-2">
                                                        <label class="form-label fw-semibold small">
                                                            Subject Name
                                                        </label>
                                                        <input type="text"
                                                               name="subject_name"
                                                               class="form-control form-control-sm"
                                                               value="{{ $subject->subject_name }}"
                                                               required>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label fw-semibold small">
                                                            विषय नाम
                                                            <span class="badge bg-label-warning"
                                                                  style="font-size:9px">हिं</span>
                                                        </label>
                                                        <input type="text"
                                                               name="subject_name_hi"
                                                               class="form-control form-control-sm"
                                                               value="{{ $subject->subject_name_hi }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label fw-semibold small">
                                                            Periods/Week
                                                        </label>
                                                        <input type="number"
                                                               name="periods_per_week"
                                                               class="form-control form-control-sm"
                                                               value="{{ $subject->periods_per_week }}"
                                                               min="0" max="30">
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input"
                                                               type="checkbox"
                                                               name="is_active"
                                                               value="1"
                                                               {{ $subject->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label small">
                                                            Active
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer py-2">
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit"
                                                            class="btn btn-sm btn-primary">
                                                        Save
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="icon-base ti tabler-book-off"
                   style="font-size:3rem; color:#ccc;"></i>
                <p class="text-muted mt-2 mb-3">No subjects defined yet.</p>
                <button type="button" class="btn btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#addSubjectModal">
                    <i class="icon-base ti tabler-plus me-1"></i>
                    Add First Subject
                </button>
            </div>
        </div>
    @endforelse

</div>

{{-- Add Subject Modal --}}
<div class="modal fade" id="addSubjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tenant.subjects.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="icon-base ti tabler-book me-2"></i>
                        Add Subject / विषय जोड़ें
                    </h5>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-7">
                            <label class="form-label fw-semibold">
                                Subject Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="subject_name"
                                   class="form-control"
                                   placeholder="e.g. Mathematics"
                                   data-hindi-target="[name='subject_name_hi']"
                                   required>
                        </div>
                        <div class="col-sm-5">
                            <label class="form-label fw-semibold">
                                विषय नाम
                                <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="subject_name_hi"
                                   class="form-control"
                                   placeholder="जैसे: गणित">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Periods/Week</label>
                            <input type="number" name="periods_per_week"
                                   class="form-control" value="5" min="0" max="30">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Assign to Classes <span class="text-danger">*</span>
                            </label>
                            <div class="border rounded p-3" style="max-height:200px; overflow-y:auto;">
                                <div class="mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               id="selectAllClasses"
                                               onchange="toggleAllClasses(this)">
                                        <label class="form-check-label fw-semibold"
                                               for="selectAllClasses">
                                            Select All Classes
                                        </label>
                                    </div>
                                </div>
                                <hr class="my-1">
                                @foreach($classes as $class)
                                    <div class="form-check">
                                        <input class="form-check-input class-checkbox"
                                               type="checkbox"
                                               name="class_ids[]"
                                               value="{{ $class->id }}"
                                               id="class_{{ $class->id }}">
                                        <label class="form-check-label"
                                               for="class_{{ $class->id }}">
                                            {{ $class->name }}
                                            <span class="text-muted small">
                                                ({{ $class->subjects->count() }} subjects)
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-text">
                                Subject will be assigned to all selected classes.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base ti tabler-plus me-1"></i>
                        Add Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleAllClasses(master) {
    document.querySelectorAll('.class-checkbox').forEach(cb => {
        cb.checked = master.checked;
    });
}

// Re-init hindi autofill when modal opens
document.getElementById('addSubjectModal')
    ?.addEventListener('shown.bs.modal', function () {
        if (window.initHindiAutofill) window.initHindiAutofill();
    });
</script>
@endpush

@endsection