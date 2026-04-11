@extends('layouts.tenant')

@section('title', 'Monthly Attendance Report')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Monthly Attendance Report / मासिक उपस्थिति रिपोर्ट</h4>
            <p class="text-muted mb-0 small">Student-wise attendance percentage for a month.</p>
        </div>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="icon-base ti tabler-printer me-1"></i> Print
        </button>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('tenant.attendance.reports.students.monthly') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2">
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
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">Class</label>
                        <select name="class_id" class="form-select">
                            <option value="">Select</option>
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
                            <select name="section_id" class="form-select">
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
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">
                            Defaulter % (Below)
                        </label>
                        <div class="input-group">
                            <input type="number" name="threshold" class="form-control"
                                   value="{{ $threshold }}" min="1" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="icon-base ti tabler-filter me-1"></i> Generate
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($classId && $reportData->count() > 0)

        {{-- Working Days Info --}}
        <div class="alert alert-info mb-4 small">
            <i class="icon-base ti tabler-calendar me-1"></i>
            Working days in {{ $months[$month] }} {{ $year }}: <strong>{{ $workingDays }}</strong>
            &nbsp;·&nbsp; Defaulter threshold: <strong>below {{ $threshold }}%</strong>
            &nbsp;·&nbsp; Defaulters: <strong class="text-danger">{{ $defaulters->count() }}</strong>
        </div>

        {{-- Defaulters Alert --}}
        @if($defaulters->count() > 0)
            <div class="card border-danger mb-4">
                <div class="card-header bg-label-danger">
                    <h6 class="mb-0 text-danger">
                        <i class="icon-base ti tabler-alert-triangle me-1"></i>
                        Defaulters — Below {{ $threshold }}% Attendance
                        ({{ $defaulters->count() }} students)
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th class="text-center">Present</th>
                                <th class="text-center">Absent</th>
                                <th class="text-center">Working Days</th>
                                <th class="text-center">Attendance %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($defaulters->sortBy('percentage') as $data)
                                <tr>
                                    <td>
                                        <p class="fw-semibold mb-0 small">
                                            {{ $data['student']->full_name }}
                                        </p>
                                        <span class="text-muted" style="font-size:10px">
                                            {{ $data['student']->admission_number }}
                                        </span>
                                    </td>
                                    <td class="text-center text-success fw-bold">{{ $data['present'] }}</td>
                                    <td class="text-center text-danger fw-bold">{{ $data['absent'] }}</td>
                                    <td class="text-center">{{ $workingDays }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">{{ $data['percentage'] }}%</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Full Report --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    Complete Attendance Report — {{ $months[$month] }} {{ $year }}
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th class="text-center text-success">Present</th>
                            <th class="text-center text-danger">Absent</th>
                            <th class="text-center text-warning">Late</th>
                            <th class="text-center text-info">Half Day</th>
                            <th class="text-center">Leave</th>
                            <th class="text-center">Working Days</th>
                            <th class="text-center">Attendance %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $i => $data)
                            @php
                                $pct = $data['percentage'];
                                $pctColor = $pct >= 75 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
                            @endphp
                            <tr class="{{ $pct < $threshold && $workingDays > 0 ? 'table-danger' : '' }}">
                                <td class="text-muted small">{{ $i + 1 }}</td>
                                <td>
                                    <p class="fw-semibold mb-0 small">
                                        {{ $data['student']->full_name }}
                                    </p>
                                    @if($data['student']->first_name_hi)
                                        <p class="text-muted mb-0" style="font-size:11px">
                                            {{ $data['student']->full_name_hi }}
                                        </p>
                                    @endif
                                    <span class="text-muted" style="font-size:10px">
                                        {{ $data['student']->admission_number }}
                                    </span>
                                </td>
                                <td class="text-center fw-bold text-success">{{ $data['present'] }}</td>
                                <td class="text-center fw-bold text-danger">{{ $data['absent'] }}</td>
                                <td class="text-center fw-bold text-warning">{{ $data['late'] }}</td>
                                <td class="text-center fw-bold text-info">{{ $data['halfDay'] }}</td>
                                <td class="text-center">{{ $data['leave'] }}</td>
                                <td class="text-center">{{ $workingDays }}</td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height:6px;">
                                            <div class="progress-bar bg-{{ $pctColor }}"
                                                 style="width:{{ $pct }}%"></div>
                                        </div>
                                        <span class="badge bg-label-{{ $pctColor }}">
                                            {{ $pct }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($classId)
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                No attendance records found for selected filters.
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="icon-base ti tabler-chart-bar"
                   style="font-size:3rem; color:#ccc;"></i>
                <p class="text-muted mt-2 mb-0">
                    Select month, year and class to generate report.
                </p>
            </div>
        </div>
    @endif

</div>
@endsection