@extends('layouts.tenant')

@section('title', 'Students')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
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
        <div class="d-flex gap-2 flex-wrap">

            {{-- Export Dropdown --}}
            <div class="dropdown">
                <button class="btn btn-outline-success dropdown-toggle" type="button"
                        id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="icon-base ti tabler-download me-1"></i>
                    Export / निर्यात
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="exportDropdown">
                    <li>
                        <h6 class="dropdown-header text-muted small">Export current filters</h6>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" onclick="exportStudents('xlsx'); return false;">
                            <i class="icon-base ti tabler-file-spreadsheet me-2 text-success"></i>
                            Excel (.xlsx)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" onclick="exportStudents('csv'); return false;">
                            <i class="icon-base ti tabler-file-text me-2 text-info"></i>
                            CSV (.csv)
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Import Button --}}
            <button type="button" class="btn btn-outline-primary"
                    data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="icon-base ti tabler-upload me-1"></i>
                Import / आयात
            </button>

            {{-- Add Student --}}
            <a href="{{ route('tenant.students.create') }}" class="btn btn-primary">
                <i class="icon-base ti tabler-plus me-1"></i>
                Add Student / छात्र जोड़ें
            </a>
        </div>
    </div>

    {{-- Import Result Panel --}}
    @if(session('import_imported') !== null || session('import_skipped') !== null || session('import_errors') !== null)
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-label-primary border-0 d-flex align-items-center gap-2">
                <i class="icon-base ti tabler-table-import"></i>
                <strong>Import Results / आयात परिणाम</strong>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-2 p-3 rounded bg-label-success">
                            <i class="icon-base ti tabler-circle-check icon-24px text-success"></i>
                            <div>
                                <div class="fw-bold text-success">{{ count(session('import_imported', [])) }}</div>
                                <div class="small text-muted">Imported successfully</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-2 p-3 rounded bg-label-warning">
                            <i class="icon-base ti tabler-alert-triangle icon-24px text-warning"></i>
                            <div>
                                <div class="fw-bold text-warning">{{ count(session('import_skipped', [])) }}</div>
                                <div class="small text-muted">Skipped (duplicates)</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-2 p-3 rounded bg-label-danger">
                            <i class="icon-base ti tabler-circle-x icon-24px text-danger"></i>
                            <div>
                                <div class="fw-bold text-danger">{{ count(session('import_errors', [])) }}</div>
                                <div class="small text-muted">Errors</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(session('import_skipped'))
                    <div class="alert alert-warning py-2 mb-2">
                        <strong>Skipped rows:</strong>
                        <ul class="mb-0 mt-1 small">
                            @foreach(session('import_skipped') as $msg)
                                <li>{{ $msg }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('import_errors'))
                    <div class="alert alert-danger py-2 mb-0">
                        <strong>Errors:</strong>
                        <ul class="mb-0 mt-1 small">
                            @foreach(session('import_errors') as $msg)
                                <li>{{ $msg }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    @endif

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
            <form method="GET" action="{{ route('tenant.students.index') }}" id="filtersForm">
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
            @if($students->total())
                <span class="text-muted small">
                    <i class="icon-base ti tabler-info-circle me-1"></i>
                    Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ $students->total() }}
                </span>
            @endif
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

{{-- ─── Import Modal ──────────────────────────────────────────────────── --}}
<div class="modal fade" id="importModal" tabindex="-1"
     aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header border-bottom">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="icon-base ti tabler-table-import me-2 text-primary"></i>
                    Import Students / छात्र आयात करें
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('tenant.students.import') }}"
                  enctype="multipart/form-data" id="importForm">
                @csrf

                <div class="modal-body">

                    {{-- Info Banner --}}
                    <div class="alert alert-info d-flex gap-2 mb-4">
                        <i class="icon-base ti tabler-info-circle icon-18px mt-1 flex-shrink-0"></i>
                        <div>
                            <strong>Before importing:</strong>
                            <ul class="mb-0 mt-1 small">
                                <li>Download the sample template to see the correct column format.</li>
                                <li>Supported formats: <strong>Excel (.xlsx, .xls)</strong> and <strong>CSV (.csv)</strong>.</li>
                                <li>Students with an existing <strong>Admission Number</strong> will be skipped.</li>
                                <li>Class and Section names must exactly match those in the system.</li>
                                <li>Date format: <code>DD/MM/YYYY</code> (e.g. 15/08/2010).</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Download Template --}}
                    <div class="mb-4">
                        <a href="{{ route('tenant.students.import.template') }}"
                           class="btn btn-outline-success w-100" target="_blank">
                            <i class="icon-base ti tabler-file-download me-2"></i>
                            Download Sample Template (Excel)
                        </a>
                    </div>

                    {{-- Drop Zone --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Upload File / फ़ाइल अपलोड करें</label>
                        <div id="dropZone"
                             class="border border-2 rounded p-5 text-center"
                             style="border-color: #7367f0 !important; border-style: dashed !important; cursor: pointer; transition: background .2s;"
                             onclick="document.getElementById('importFile').click()"
                             ondragover="event.preventDefault(); this.style.background='#f0eeff';"
                             ondragleave="this.style.background='';"
                             ondrop="handleDrop(event)">
                            <i class="icon-base ti tabler-cloud-upload"
                               style="font-size:2.5rem; color:#7367f0;"></i>
                            <p class="mt-2 mb-1 fw-semibold text-primary">
                                Drag &amp; drop file here, or click to browse
                            </p>
                            <p class="text-muted small mb-0">
                                Accepted: .xlsx, .xls, .csv &mdash; Max 5 MB
                            </p>
                        </div>
                        <input type="file" id="importFile" name="file"
                               accept=".xlsx,.xls,.csv"
                               class="d-none"
                               onchange="showFileName(this)">
                        @error('file')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Selected File Display --}}
                    <div id="filePreview" class="d-none mb-3">
                        <div class="d-flex align-items-center gap-2 p-3 rounded bg-label-success">
                            <i class="icon-base ti tabler-file-check icon-24px text-success"></i>
                            <div class="flex-grow-1">
                                <div class="fw-semibold" id="fileNameDisplay"></div>
                                <div class="text-muted small" id="fileSizeDisplay"></div>
                            </div>
                            <button type="button" class="btn btn-sm btn-icon btn-outline-danger"
                                    onclick="clearFile()">
                                <i class="icon-base ti tabler-x"></i>
                            </button>
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="importBtn" disabled>
                        <i class="icon-base ti tabler-upload me-1"></i>
                        Start Import
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@push('scripts')
<script>
    // ─── Section loader ────────────────────────────────────────────────────
    async function loadSectionsFilter(classId) {
        const select = document.getElementById('sectionFilter');
        const current = '{{ request('section_id') }}';
        select.innerHTML = '<option value="">All Sections</option>';
        if (!classId) return;
        const res = await fetch(`/classes/${classId}/sections`);
        const sections = await res.json();
        sections.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.name;
            if (String(s.id) === String(current)) opt.selected = true;
            select.appendChild(opt);
        });
    }

    const classFilter = document.getElementById('classFilter');
    if (classFilter && classFilter.value) loadSectionsFilter(classFilter.value);

    // ─── Export respecting current filters ────────────────────────────────
    function exportStudents(format) {
        const form = document.getElementById('filtersForm');
        const params = new URLSearchParams(new FormData(form));
        params.set('format', format);
        window.location.href = '{{ route('tenant.students.export') }}?' + params.toString();
    }

    // ─── Import drag-and-drop ──────────────────────────────────────────────
    function handleDrop(e) {
        e.preventDefault();
        document.getElementById('dropZone').style.background = '';
        const file = e.dataTransfer.files[0];
        if (file) {
            const input = document.getElementById('importFile');
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            showFileName(input);
        }
    }

    function showFileName(input) {
        const file = input.files[0];
        if (!file) return;
        const allowed = ['xlsx', 'xls', 'csv'];
        const ext = file.name.split('.').pop().toLowerCase();
        if (!allowed.includes(ext)) {
            alert('Invalid file type. Please upload .xlsx, .xls, or .csv');
            clearFile();
            return;
        }
        document.getElementById('fileNameDisplay').textContent = file.name;
        document.getElementById('fileSizeDisplay').textContent = (file.size / 1024).toFixed(1) + ' KB';
        document.getElementById('filePreview').classList.remove('d-none');
        document.getElementById('dropZone').classList.add('d-none');
        document.getElementById('importBtn').removeAttribute('disabled');
    }

    function clearFile() {
        document.getElementById('importFile').value = '';
        document.getElementById('filePreview').classList.add('d-none');
        document.getElementById('dropZone').classList.remove('d-none');
        document.getElementById('importBtn').setAttribute('disabled', '');
    }

    document.getElementById('importForm').addEventListener('submit', function () {
        const btn = document.getElementById('importBtn');
        btn.setAttribute('disabled', '');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Importing...';
    });
</script>
@endpush

@endsection