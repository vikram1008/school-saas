@extends('layouts.tenant')

@section('title', 'Staff Monthly Attendance')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Staff Monthly Report / स्टाफ मासिक रिपोर्ट</h4>
            <p class="text-muted mb-0 small">Monthly attendance summary for all staff.</p>
        </div>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="icon-base ti tabler-printer me-1"></i> Print
        </button>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('tenant.attendance.reports.staff.monthly') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Month</label>
                        <select name="month" class="form-select">
                            @foreach($months as $m => $name)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">Year</label>
                        <select name="year" class="form-select">
                            @foreach(range(now()->year, now()->year - 2) as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-base ti tabler-filter me-1"></i> Generate
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="alert alert-info small mb-4">
        <i class="icon-base ti tabler-calendar me-1"></i>
        Working days in {{ $months[$month] }} {{ $year }}: <strong>{{ $workingDays }}</strong>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                Staff Attendance — {{ $months[$month] }} {{ $year }}
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Staff Member</th>
                        <th>Type</th>
                        <th class="text-center text-success">Present</th>
                        <th class="text-center text-danger">Absent</th>
                        <th class="text-center text-warning">Late</th>
                        <th class="text-center">Leave</th>
                        <th class="text-center">Working Days</th>
                        <th class="text-center">Attendance %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $i => $data)
                        @php
                            $pct = $data['percentage'];
                            $pctColor = $pct >= 90 ? 'success' : ($pct >= 75 ? 'warning' : 'danger');
                        @endphp
                        <tr>
                            <td class="text-muted small">{{ $i + 1 }}</td>
                            <td>
                                <p class="fw-semibold mb-0 small">{{ $data['staff']->full_name }}</p>
                                @if($data['staff']->first_name_hi)
                                    <p class="text-muted mb-0" style="font-size:11px">
                                        {{ $data['staff']->full_name_hi }}
                                    </p>
                                @endif
                                <span class="text-muted" style="font-size:10px">
                                    {{ $data['staff']->employee_code }}
                                </span>
                            </td>
                            <td>
                                @php $typeColors = ['teaching'=>'primary','non_teaching'=>'info','administrative'=>'warning']; @endphp
                                <span class="badge bg-label-{{ $typeColors[$data['staff']->staff_type] ?? 'secondary' }}"
                                      style="font-size:10px">
                                    {{ ucfirst(str_replace('_',' ',$data['staff']->staff_type)) }}
                                </span>
                            </td>
                            <td class="text-center fw-bold text-success">{{ $data['present'] }}</td>
                            <td class="text-center fw-bold text-danger">{{ $data['absent'] }}</td>
                            <td class="text-center fw-bold text-warning">{{ $data['late'] }}</td>
                            <td class="text-center">{{ $data['leave'] }}</td>
                            <td class="text-center">{{ $workingDays }}</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:6px;">
                                        <div class="progress-bar bg-{{ $pctColor }}"
                                             style="width:{{ $pct }}%"></div>
                                    </div>
                                    <span class="badge bg-label-{{ $pctColor }}">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                No attendance records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection