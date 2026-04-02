@extends('layouts.tenant')

@section('title', 'Classes & Sections')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Classes & Sections</h4>
            <p class="text-muted mb-0 small">
                @if($activeYear)
                    Active Year:
                    <strong>{{ $activeYear->name }}</strong>
                @else
                    <span class="text-warning">No active academic year</span>
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tenant.academic-years.index') }}"
               class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-calendar me-1"></i>
                Academic Years
            </a>
            @if($activeYear)
                <button type="button" class="btn btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#addClassModal">
                    <i class="icon-base ti tabler-plus me-1"></i>
                    Add Class
                </button>
            @endif
        </div>
    </div>

    {{-- Alerts --}}
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

    @if(!$activeYear)
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="icon-base ti tabler-calendar-off"
                   style="font-size:3rem; color:#ccc;"></i>
                <p class="text-muted mt-2 mb-3">
                    Please set an active academic year first.
                </p>
                <a href="{{ route('tenant.academic-years.index') }}"
                   class="btn btn-primary">
                    <i class="icon-base ti tabler-calendar me-1"></i>
                    Manage Academic Years
                </a>
            </div>
        </div>
    @else

        {{-- Summary Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-4">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <h3 class="fw-bold text-primary mb-1">{{ $classes->count() }}</h3>
                        <p class="text-muted small mb-0">Total Classes</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <h3 class="fw-bold text-success mb-1">
                            {{ $classes->sum(fn($c) => $c->sections->count()) }}
                        </h3>
                        <p class="text-muted small mb-0">Total Sections</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <h3 class="fw-bold text-info mb-1">
                            {{ $classes->sum(fn($c) => $c->student_count) }}
                        </h3>
                        <p class="text-muted small mb-0">Total Students</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Classes List --}}
        @forelse($classes as $class)
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                {{ $class->order }}
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $class->name }}</h5>
                            <div class="d-flex gap-2 mt-1">
                                <span class="badge bg-label-{{ $class->has_sections ? 'info' : 'secondary' }}">
                                    {{ $class->has_sections ? 'With Sections' : 'Single Tier' }}
                                </span>
                                <span class="badge bg-label-primary">
                                    {{ $class->student_count }} students
                                </span>
                                @if($class->capacity)
                                    <span class="badge bg-label-secondary">
                                        Capacity: {{ $class->capacity }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        @if($class->has_sections)
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#addSectionModal{{ $class->id }}">
                                <i class="icon-base ti tabler-plus me-1"></i>
                                Add Section
                            </button>
                        @endif
                        <button type="button"
                                class="btn btn-sm btn-icon btn-outline-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#editClassModal{{ $class->id }}"
                                title="Edit">
                            <i class="icon-base ti tabler-edit"></i>
                        </button>
                        <form action="{{ route('tenant.classes.destroy', $class) }}"
                              method="POST"
                              onsubmit="return confirm('Delete {{ $class->name }}? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm btn-icon btn-outline-danger"
                                    title="Delete">
                                <i class="icon-base ti tabler-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Class Teacher (single-tier) --}}
                @if(!$class->has_sections)
                    <div class="card-body py-2 border-bottom">
                        <span class="text-muted small">
                            <i class="icon-base ti tabler-user me-1"></i>
                            Class Teacher:
                            <strong>
                                {{ $class->classTeacher?->name ?? 'Not assigned' }}
                            </strong>
                        </span>
                    </div>
                @endif

                {{-- Sections (two-tier) --}}
                @if($class->has_sections)
                    <div class="card-body">
                        @if($class->sections->count() > 0)
                            <div class="row g-3">
                                @foreach($class->sections as $section)
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="border rounded p-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="fw-bold mb-1">
                                                        {{ $class->name }} - {{ $section->name }}
                                                    </h6>
                                                    <p class="text-muted small mb-1">
                                                        <i class="icon-base ti tabler-user me-1"></i>
                                                        {{ $section->classTeacher?->name ?? 'No class teacher' }}
                                                    </p>
                                                    <div class="d-flex gap-2">
                                                        <span class="badge bg-label-primary small">
                                                            {{ $section->student_count }} students
                                                        </span>
                                                        @if($section->capacity)
                                                            <span class="badge bg-label-secondary small">
                                                                Cap: {{ $section->capacity }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <form action="{{ route('tenant.classes.sections.destroy', [$class, $section]) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Delete Section {{ $section->name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-xs btn-icon btn-outline-danger">
                                                        <i class="icon-base ti tabler-x"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted small mb-0 text-center py-2">
                                No sections yet.
                                <a href="#" data-bs-toggle="modal"
                                   data-bs-target="#addSectionModal{{ $class->id }}">
                                   Add first section
                                </a>
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Add Section Modal --}}
            @if($class->has_sections)
                <div class="modal fade" id="addSectionModal{{ $class->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('tenant.classes.sections.store', $class) }}"
                                  method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Add Section to {{ $class->name }}
                                    </h5>
                                    <button type="button" class="btn-close"
                                            data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            Section Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               name="name"
                                               class="form-control"
                                               placeholder="e.g. A, B, C or Rose, Lily"
                                               maxlength="10">
                                        <div class="form-text">
                                            Common: A, B, C or custom names
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            Class Teacher
                                        </label>
                                        <select name="class_teacher_id" class="form-select">
                                            <option value="">Not assigned</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}">
                                                    {{ $teacher->name }}
                                                    ({{ ucfirst($teacher->role) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            Capacity
                                        </label>
                                        <input type="number"
                                               name="capacity"
                                               class="form-control"
                                               placeholder="Max students (optional)"
                                               min="1">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button"
                                            class="btn btn-outline-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="icon-base ti tabler-plus me-1"></i>
                                        Add Section
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Edit Class Modal --}}
            <div class="modal fade" id="editClassModal{{ $class->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('tenant.classes.update', $class) }}"
                              method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">Edit {{ $class->name }}</h5>
                                <button type="button" class="btn-close"
                                        data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Class Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="name"
                                           class="form-control"
                                           value="{{ $class->name }}"
                                           required>
                                </div>
                                @if(!$class->has_sections)
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            Class Teacher
                                        </label>
                                        <select name="class_teacher_id" class="form-select">
                                            <option value="">Not assigned</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}"
                                                    {{ $class->class_teacher_id == $teacher->id ? 'selected' : '' }}>
                                                    {{ $teacher->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Capacity</label>
                                    <input type="number"
                                           name="capacity"
                                           class="form-control"
                                           value="{{ $class->capacity }}"
                                           min="1">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Description</label>
                                    <input type="text"
                                           name="description"
                                           class="form-control"
                                           value="{{ $class->description }}">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-base ti tabler-device-floppy me-1"></i>
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        @empty
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="icon-base ti tabler-layout-grid-remove"
                       style="font-size:3rem; color:#ccc;"></i>
                    <p class="text-muted mt-2 mb-3">
                        No classes yet for {{ $activeYear->name }}.
                    </p>
                    <button type="button" class="btn btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#addClassModal">
                        <i class="icon-base ti tabler-plus me-1"></i>
                        Add First Class
                    </button>
                </div>
            </div>
        @endforelse

    @endif

</div>

{{-- Add Class Modal --}}
@if($activeYear)
<div class="modal fade" id="addClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tenant.classes.store') }}" method="POST">
                @csrf
                <input type="hidden" name="academic_year_id" value="{{ $activeYear->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="icon-base ti tabler-plus me-2"></i>
                        Add Class — {{ $activeYear->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Class Name <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               placeholder="e.g. Nursery, KG, Class 1, Grade 6"
                               required>
                        <div class="form-text">
                            Use any naming convention your school follows.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Structure <span class="text-danger">*</span>
                        </label>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="form-check card border p-3 mb-0 cursor-pointer"
                                     onclick="selectStructure(true)">
                                    <input class="form-check-input" type="radio"
                                           name="has_sections" value="1"
                                           id="hasSectionsYes" checked>
                                    <label class="form-check-label w-100" for="hasSectionsYes">
                                        <i class="icon-base ti tabler-layout-columns text-primary d-block mb-1"></i>
                                        <strong class="small">With Sections</strong>
                                        <div class="text-muted" style="font-size:11px">
                                            Class 6-A, 6-B, 6-C
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check card border p-3 mb-0 cursor-pointer"
                                     onclick="selectStructure(false)">
                                    <input class="form-check-input" type="radio"
                                           name="has_sections" value="0"
                                           id="hasSectionsNo">
                                    <label class="form-check-label w-100" for="hasSectionsNo">
                                        <i class="icon-base ti tabler-layout-list text-success d-block mb-1"></i>
                                        <strong class="small">Single Tier</strong>
                                        <div class="text-muted" style="font-size:11px">
                                            Class 5 (no sections)
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Shown only for single-tier --}}
                    <div id="singleTierFields" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Class Teacher</label>
                            <select name="class_teacher_id" class="form-select">
                                <option value="">Not assigned</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">
                                        {{ $teacher->name }}
                                        ({{ ucfirst($teacher->role) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Capacity</label>
                            <input type="number"
                                   name="capacity"
                                   class="form-control"
                                   placeholder="Max students (optional)"
                                   min="1">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <input type="text"
                               name="description"
                               class="form-control"
                               placeholder="Optional note...">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base ti tabler-plus me-1"></i>
                        Create Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    function selectStructure(hasSections) {
        document.getElementById('hasSectionsYes').checked = hasSections;
        document.getElementById('hasSectionsNo').checked  = !hasSections;
        document.getElementById('singleTierFields').style.display = hasSections ? 'none' : 'block';
    }
</script>
@endpush

@endsection