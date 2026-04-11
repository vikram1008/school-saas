@extends('layouts.tenant')

@section('title', 'Daily Attendance Summary')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Daily Summary / दैनिक सारांश</h4>
            <p class="text-muted mb-0 small">School-wide attendance for a given date.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tenant.attendance.reports.students.monthly') }}"
               class="btn btn-outline-primary">
                <i class="icon-base ti tabler-calendar-stats me-1"></i> Monthly Report
            </a>
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-printer me-1"></i> Print
            </button>
        </div>
    </div>

    {{-- Date Filter --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('tenant.attendance.reports.daily') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Date</label>
                        <input type="date" name="date" class="form-control"
                               value="{{ $date }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-base ti tabler-filter me-1"></i> View
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- School Totals --}}
    <div class="row g-3 mb-4">
        @php
            $totalColors = ['total'=>'primary','present'=>'success','absent'=>'danger','late'=>'warning','marked'=>'info'];
        @endphp
        @foreach($totalColors as $key => $color)
            <div class="col">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <h3 class="fw-bold text-{{ $color }} mb-1">
                            {{ $schoolTotals[$key] }}
                        </h3>
                        <p class="text-muted small mb-0">{{ ucfirst($key) }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Per Class Summary --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="icon-base ti tabler-layout-grid me-2 text-primary"></i>
                Class-wise Summary — {{ \Carbon\Carbon::parse($date)->format('d M Y, l') }}
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Class</th>
                        <th class="text-center">Total Students</th>
                        <th class="text-center">Marked</th>
                        <th class="text-center text-success">Present</th>
                        <th class="text-center text-danger">Absent</th>
                        <th class="text-center text-warning">Late</th>
                        <th class="text-center">Attendance %</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classSummaries as $summary)
                        @php
                            $pct = $summary['total'] > 0
                                ? round(($summary['present'] / $summary['total']) * 100)
                                : 0;
                            $pctColor = $pct >= 75 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $summary['class']->name }}</td>
                            <td class="text-center">{{ $summary['total'] }}</td>
                            <td class="text-center">
                                @if($summary['marked'] < $summary['total'])
                                    <span class="badge bg-label-warning">{{ $summary['marked'] }}</span>
                                @else
                                    <span class="badge bg-label-success">{{ $summary['marked'] }}</span>
                                @endif
                            </td>
                            <td class="text-center fw-bold text-success">{{ $summary['present'] }}</td>
                            <td class="text-center fw-bold text-danger">{{ $summary['absent'] }}</td>
                            <td class="text-center fw-bold text-warning">{{ $summary['late'] }}</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:6px;">
                                        <div class="progress-bar bg-{{ $pctColor }}"
                                             style="width:{{ $pct }}%"></div>
                                    </div>
                                    <span class="small fw-semibold text-{{ $pctColor }}">
                                        {{ $pct }}%
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('tenant.attendance.students.index', ['class_id' => $summary['class']->id, 'date' => $date]) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="icon-base ti tabler-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                No classes found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection