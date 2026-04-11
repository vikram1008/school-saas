@extends('layouts.tenant')

@section('title', 'Staff Attendance')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Staff Attendance / स्टाफ उपस्थिति</h4>
            <p class="text-muted mb-0 small">Mark daily attendance for staff members.</p>
        </div>
        <a href="{{ route('tenant.attendance.reports.staff.monthly') }}"
           class="btn btn-outline-primary">
            <i class="icon-base ti tabler-chart-bar me-1"></i> Monthly Report
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

    {{-- Filter --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('tenant.attendance.staff.index') }}"
                  id="staffFilterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Date</label>
                        <input type="date" name="date" class="form-control"
                               value="{{ $date }}"
                               onchange="document.getElementById('staffFilterForm').submit()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Staff Type</label>
                        <select name="staff_type" class="form-select"
                                onchange="document.getElementById('staffFilterForm').submit()">
                            <option value="">All Types</option>
                            @foreach(\App\Models\StaffProfile::typeLabels() as $val => $lbl)
                                <option value="{{ $val }}"
                                    {{ $type === $val ? 'selected' : '' }}>
                                    {{ $lbl }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary --}}
    <div class="row g-3 mb-4">
        @foreach(['present'=>'success','absent'=>'danger','late'=>'warning','half_day'=>'info','leave'=>'secondary'] as $s => $c)
            <div class="col">
                <div class="card text-center">
                    <div class="card-body py-2">
                        <h4 class="fw-bold text-{{ $c }} mb-0">{{ $summary[$s] }}</h4>
                        <p class="text-muted small mb-0">{{ ucfirst(str_replace('_',' ',$s)) }}</p>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="col">
            <div class="card text-center">
                <div class="card-body py-2">
                    <h4 class="fw-bold mb-0">{{ $summary['total'] }}</h4>
                    <p class="text-muted small mb-0">Total</p>
                </div>
            </div>
        </div>
    </div>

    @if($staff->count() > 0)
        <form action="{{ route('tenant.attendance.staff.store') }}" method="POST">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        Mark Attendance — {{ \Carbon\Carbon::parse($date)->format('d M Y, l') }}
                    </h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-success"
                                onclick="markAllStaff('present')">All Present</button>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                                onclick="markAllStaff('absent')">All Absent</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Staff Member</th>
                                <th>Designation</th>
                                @foreach(\App\Models\StaffAttendance::statusLabels() as $val => $lbl)
                                    <th class="text-center">
                                        <span class="badge bg-label-{{ \App\Models\StaffAttendance::statusColors()[$val] }}"
                                              style="font-size:10px">
                                            {{ ucfirst(str_replace('_',' ',$val)) }}
                                        </span>
                                    </th>
                                @endforeach
                                <th>In Time</th>
                                <th>Out Time</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staff as $i => $member)
                                @php
                                    $existing = $existingAttendance->get($member->id);
                                    $currentStatus = $existing?->status ?? 'present';
                                @endphp
                                <tr class="staff-row" data-staff="{{ $member->id }}">
                                    <td class="text-muted small">{{ $i + 1 }}</td>
                                    <td>
                                        <p class="fw-semibold mb-0 small">{{ $member->full_name }}</p>
                                        @if($member->first_name_hi)
                                            <p class="text-muted mb-0" style="font-size:11px">
                                                {{ $member->full_name_hi }}
                                            </p>
                                        @endif
                                        <span class="text-muted" style="font-size:10px">
                                            {{ $member->employee_code }}
                                        </span>
                                    </td>
                                    <td class="small text-muted">{{ $member->designation ?? '—' }}</td>
                                    @foreach(\App\Models\StaffAttendance::statusLabels() as $val => $lbl)
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input staff-radio"
                                                       type="radio"
                                                       name="attendance[{{ $member->id }}][status]"
                                                       value="{{ $val }}"
                                                       data-staff="{{ $member->id }}"
                                                       data-status="{{ $val }}"
                                                       {{ $currentStatus === $val ? 'checked' : '' }}
                                                       onchange="highlightStaffRow({{ $member->id }}, '{{ $val }}')">
                                            </div>
                                        </td>
                                    @endforeach
                                    <td>
                                        <input type="time"
                                               name="attendance[{{ $member->id }}][in_time]"
                                               class="form-control form-control-sm"
                                               value="{{ $existing?->in_time }}"
                                               style="width:100px">
                                    </td>
                                    <td>
                                        <input type="time"
                                               name="attendance[{{ $member->id }}][out_time]"
                                               class="form-control form-control-sm"
                                               value="{{ $existing?->out_time }}"
                                               style="width:100px">
                                    </td>
                                    <td>
                                        <input type="text"
                                               name="attendance[{{ $member->id }}][remarks]"
                                               class="form-control form-control-sm"
                                               value="{{ $existing?->remarks }}"
                                               placeholder="Optional..."
                                               style="width:120px">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <span class="text-muted small">{{ $staff->count() }} staff members</span>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base ti tabler-device-floppy me-1"></i>
                        Save Attendance
                    </button>
                </div>
            </div>
        </form>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="icon-base ti tabler-users-off"
                   style="font-size:3rem; color:#ccc;"></i>
                <p class="text-muted mt-2 mb-0">No active staff members found.</p>
            </div>
        </div>
    @endif

</div>

@push('scripts')
<script>
    const staffStatusColors = {
        present:  'table-success',
        absent:   'table-danger',
        late:     'table-warning',
        half_day: 'table-info',
        leave:    'table-secondary',
        holiday:  'table-primary',
    };

    function highlightStaffRow(staffId, status) {
        const row = document.querySelector(`tr[data-staff="${staffId}"]`);
        if (!row) return;
        Object.values(staffStatusColors).forEach(c => row.classList.remove(c));
        if (staffStatusColors[status]) row.classList.add(staffStatusColors[status]);
    }

    function markAllStaff(status) {
        document.querySelectorAll('.staff-radio').forEach(radio => {
            if (radio.dataset.status === status) {
                radio.checked = true;
                highlightStaffRow(parseInt(radio.dataset.staff), status);
            }
        });
    }

    // Highlight existing on load
    document.querySelectorAll('.staff-radio:checked').forEach(radio => {
        highlightStaffRow(parseInt(radio.dataset.staff), radio.dataset.status);
    });
</script>
@endpush

@endsection