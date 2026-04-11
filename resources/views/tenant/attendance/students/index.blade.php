@extends('layouts.tenant')

@section('title', 'Student Attendance')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Student Attendance / छात्र उपस्थिति</h4>
            <p class="text-muted mb-0 small">Mark daily attendance for students.</p>
        </div>
        <a href="{{ route('tenant.attendance.reports.daily') }}"
           class="btn btn-outline-primary">
            <i class="icon-base ti tabler-chart-bar me-1"></i> Reports
        </a>
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

    {{-- Filter Form --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('tenant.attendance.students.index') }}"
                  id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">Date / दिनांक</label>
                        <input type="date" name="date" class="form-control"
                               value="{{ $date }}"
                               onchange="document.getElementById('filterForm').submit()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Class / कक्षा</label>
                        <select name="class_id" class="form-select"
                                onchange="document.getElementById('filterForm').submit()">
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
                                    onchange="document.getElementById('filterForm').submit()">
                                <option value="">All Sections</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}"
                                        {{ $sectionId == $section->id ? 'selected' : '' }}>
                                        {{ $section->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">Type</label>
                        <select name="type" class="form-select"
                                onchange="document.getElementById('filterForm').submit()">
                            <option value="class_wise" {{ $type === 'class_wise' ? 'selected' : '' }}>
                                Class-wise
                            </option>
                            <option value="subject_wise" {{ $type === 'subject_wise' ? 'selected' : '' }}>
                                Subject-wise
                            </option>
                        </select>
                    </div>
                    @if($type === 'subject_wise' && $periods->count() > 0)
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Period / Subject</label>
                            <select name="period_id" class="form-select"
                                    onchange="document.getElementById('filterForm').submit()">
                                <option value="">Select Period</option>
                                @foreach($periods as $period)
                                    <option value="{{ $period->id }}"
                                        {{ request('period_id') == $period->id ? 'selected' : '' }}>
                                        Period {{ $period->period_number }} — {{ $period->subject_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($classId && $students->count() > 0)

        {{-- Daily Summary --}}
        @if($dailySummary)
            <div class="row g-3 mb-4">
                @foreach(['present'=>'success','absent'=>'danger','late'=>'warning','half_day'=>'info','leave'=>'secondary'] as $status => $color)
                    <div class="col">
                        <div class="card text-center">
                            <div class="card-body py-2">
                                <h4 class="fw-bold text-{{ $color }} mb-0">
                                    {{ $dailySummary[$status] }}
                                </h4>
                                <p class="text-muted small mb-0">{{ ucfirst(str_replace('_',' ',$status)) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="col">
                    <div class="card text-center">
                        <div class="card-body py-2">
                            <h4 class="fw-bold mb-0">{{ $dailySummary['total'] }}</h4>
                            <p class="text-muted small mb-0">Total</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Attendance Form --}}
        <form action="{{ route('tenant.attendance.students.store') }}" method="POST">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <input type="hidden" name="class_id" value="{{ $classId }}">
            <input type="hidden" name="section_id" value="{{ $sectionId }}">
            <input type="hidden" name="academic_year_id" value="{{ $activeYear?->id }}">
            <input type="hidden" name="attendance_type" value="{{ $type }}">
            @if(request('period_id'))
                <input type="hidden" name="period_id" value="{{ request('period_id') }}">
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        Mark Attendance — {{ \Carbon\Carbon::parse($date)->format('d M Y, l') }}
                    </h5>
                    <div class="d-flex gap-2">
                        {{-- Quick Mark All --}}
                        @foreach(['present'=>'success','absent'=>'danger'] as $s => $c)
                            <button type="button"
                                    class="btn btn-sm btn-outline-{{ $c }}"
                                    onclick="markAll('{{ $s }}')">
                                All {{ ucfirst($s) }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Student / छात्र</th>
                                @foreach(\App\Models\StudentAttendance::statusLabels() as $val => $lbl)
                                    <th class="text-center">
                                        <span class="badge bg-label-{{ \App\Models\StudentAttendance::statusColors()[$val] }}">
                                            {{ ucfirst(str_replace('_',' ',$val)) }}
                                        </span>
                                    </th>
                                @endforeach
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $i => $student)
                                @php
                                    $existing = $existingAttendance->get($student->id);
                                    $currentStatus = $existing?->status ?? 'present';
                                @endphp
                                <tr class="attendance-row" data-student="{{ $student->id }}">
                                    <td class="text-muted small">{{ $i + 1 }}</td>
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
                                    @foreach(\App\Models\StudentAttendance::statusLabels() as $val => $lbl)
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input status-radio"
                                                       type="radio"
                                                       name="attendance[{{ $student->id }}][status]"
                                                       value="{{ $val }}"
                                                       data-student="{{ $student->id }}"
                                                       data-status="{{ $val }}"
                                                       {{ $currentStatus === $val ? 'checked' : '' }}
                                                       onchange="highlightRow({{ $student->id }}, '{{ $val }}')">
                                            </div>
                                        </td>
                                    @endforeach
                                    <td>
                                        <input type="text"
                                               name="attendance[{{ $student->id }}][remarks]"
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
                        Save Attendance
                    </button>
                </div>
            </div>
        </form>

    @elseif($classId && $students->count() === 0)
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="icon-base ti tabler-users-off"
                   style="font-size:3rem; color:#ccc;"></i>
                <p class="text-muted mt-2 mb-0">No active students in this class.</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="icon-base ti tabler-user-check"
                   style="font-size:3rem; color:#ccc;"></i>
                <p class="text-muted mt-3 mb-0">
                    Select a date and class above to mark attendance.
                </p>
            </div>
        </div>
    @endif

</div>

@push('scripts')
<script>
    const statusColors = {
        present:  'table-success',
        absent:   'table-danger',
        late:     'table-warning',
        half_day: 'table-info',
        leave:    'table-secondary',
    };

    function highlightRow(studentId, status) {
        const row = document.querySelector(`tr[data-student="${studentId}"]`);
        if (!row) return;
        Object.values(statusColors).forEach(c => row.classList.remove(c));
        if (statusColors[status]) row.classList.add(statusColors[status]);
    }

    function markAll(status) {
        document.querySelectorAll('.status-radio').forEach(radio => {
            if (radio.dataset.status === status) {
                radio.checked = true;
                highlightRow(parseInt(radio.dataset.student), status);
            }
        });
    }

    // Highlight existing attendance on load
    document.querySelectorAll('.status-radio:checked').forEach(radio => {
        highlightRow(parseInt(radio.dataset.student), radio.dataset.status);
    });
</script>
@endpush

@endsection