@extends('layouts.tenant')

@section('title', 'Students')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Students / छात्र</h4>
            <p class="text-muted mb-0 small">
                @if($activeYear)
                    Active Year: <strong>{{ $activeYear->name }}</strong>
                @else
                    <span class="text-warning">No active academic year</span>
                @endif
            </p>
        </div>
        <a href="{{ route('tenant.students.create') }}" class="btn btn-primary">
            <i class="icon-base ti tabler-plus me-1"></i>
            Add Student / छात्र जोड़ें
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('tenant.students.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Search / खोजें</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="icon-base ti tabler-search"></i>
                            </span>
                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="Name, Admission No, SR No..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Class / कक्षा</label>
                        <select name="class_id" class="form-select" id="classFilter"
                                onchange="loadSectionsFilter(this.value)">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}"
                                    {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Section / अनुभाग</label>
                        <select name="section_id" class="form-select" id="sectionFilter">
                            <option value="">All Sections</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Status / स्थिति</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                Active / सक्रिय
                            </option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                Inactive / निष्क्रिय
                            </option>
                            <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>
                                Graduated / उत्तीर्ण
                            </option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-2">
                                <i class="icon-base ti tabler-filter"></i>
                            </button>
                            <a href="{{ route('tenant.students.index') }}"
                               class="btn btn-outline-secondary px-2">
                                <i class="icon-base ti tabler-x"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Students Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                All Students
                <span class="badge bg-label-primary ms-1">
                    {{ $students->total() }}
                </span>
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>SR No.</th>
                        <th>Student / छात्र</th>
                        <th>Class / कक्षा</th>
                        <th>Father / पिता</th>
                        <th>Mobile</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>
                                <span class="text-muted small">
                                    {{ $student->sr_number ?? $student->admission_number }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($student->photo)
                                        <img src="{{ Storage::url($student->photo) }}"
                                             class="rounded-circle"
                                             width="36" height="36"
                                             style="object-fit:cover;">
                                    @else
                                        <div class="avatar avatar-sm">
                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('tenant.students.show', $student) }}"
                                           class="fw-semibold text-body d-block">
                                            {{ $student->full_name }}
                                        </a>
                                        @if($student->first_name_hi)
                                            <span class="text-muted small">
                                                {{ $student->first_name_hi }} {{ $student->last_name_hi }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-info">
                                    {{ $student->class_section }}
                                </span>
                            </td>
                            <td>
                                <span class="small">
                                    {{ $student->familyDetail?->father_name ?? '—' }}
                                </span>
                                @if($student->familyDetail?->father_name_hi)
                                    <div class="text-muted small">
                                        {{ $student->familyDetail->father_name_hi }}
                                    </div>
                                @endif
                            </td>
                            <td class="small">
                                {{ $student->phone ?? $student->familyDetail?->father_mobile ?? '—' }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-label-{{ $student->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('tenant.students.show', $student) }}"
                                       class="btn btn-sm btn-icon btn-outline-primary"
                                       title="View">
                                        <i class="icon-base ti tabler-eye"></i>
                                    </a>
                                    <a href="{{ route('tenant.students.edit', $student) }}"
                                       class="btn btn-sm btn-icon btn-outline-warning"
                                       title="Edit">
                                        <i class="icon-base ti tabler-edit"></i>
                                    </a>
                                    <form action="{{ route('tenant.students.destroy', $student) }}"
                                          method="POST"
                                          onsubmit="return confirm('Deactivate {{ $student->full_name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-icon btn-outline-danger"
                                                title="Deactivate">
                                            <i class="icon-base ti tabler-user-off"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="icon-base ti tabler-user-off"
                                   style="font-size:2.5rem; color:#ccc;"></i>
                                <p class="text-muted mt-2 mb-3">
                                    No students found. / कोई छात्र नहीं मिला।
                                </p>
                                <a href="{{ route('tenant.students.create') }}"
                                   class="btn btn-primary btn-sm">
                                    <i class="icon-base ti tabler-plus me-1"></i>
                                    Add First Student
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
            <div class="card-footer">
                {{ $students->links() }}
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
    async function loadSectionsFilter(classId) {
        const select = document.getElementById('sectionFilter');
        select.innerHTML = '<option value="">All Sections</option>';
        if (!classId) return;
        const res = await fetch(`/classes/${classId}/sections`);
        const sections = await res.json();
        sections.forEach(s => {
            select.innerHTML += `<option value="${s.id}">${s.name}</option>`;
        });
    }

    // Load sections if class pre-selected
    const classFilter = document.getElementById('classFilter');
    if (classFilter.value) loadSectionsFilter(classFilter.value);
</script>
@endpush

@endsection