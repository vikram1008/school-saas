@extends('layouts.tenant')

@section('title', 'Attendance — ' . $student->full_name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('tenant.parent-portal.dashboard') }}"
           class="btn btn-icon btn-outline-secondary me-3">
            <i class="icon-base ti tabler-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">Attendance / उपस्थिति</h4>
            <p class="text-muted small mb-0">
                {{ $student->full_name }} — {{ $student->class_section }}
            </p>
        </div>
    </div>

    {{-- Month Filter --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Month</label>
                        <select name="month" class="form-select">
                            @foreach($months as $m => $name)
                                <option value="{{ $m }}"
                                    {{ $month == $m ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">Year</label>
                        <select name="year" class="form-select">
                            @foreach(range(now()->year, now()->year - 1) as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
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

    {{-- Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <h3 class="fw-bold text-primary mb-1">{{ $workingDays }}</h3>
                    <p class="text-muted small mb-0">Working Days</p>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <h3 class="fw-bold text-success mb-1">{{ $present }}</h3>
                    <p class="text-muted small mb-0">Present / उपस्थित</p>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <h3 class="fw-bold text-danger mb-1">{{ $absent }}</h3>
                    <p class="text-muted small mb-0">Absent / अनुपस्थित</p>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <h3 class="fw-bold {{ $percentage >= 75 ? 'text-success' : 'text-danger' }} mb-1">
                        {{ $percentage }}%
                    </h3>
                    <p class="text-muted small mb-0">Attendance %</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Calendar View --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                {{ $months[$month] }} {{ $year }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-2">
                @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
                    <div class="col text-center">
                        <span class="small fw-semibold text-muted">{{ $day }}</span>
                    </div>
                @endforeach

                @php
                    $firstDay = \Carbon\Carbon::create($year, $month, 1)->dayOfWeek;
                    $firstDay = $firstDay === 0 ? 7 : $firstDay; // Make Monday=1
                @endphp

                {{-- Empty cells before first day --}}
                @for($i = 1; $i < $firstDay; $i++)
                    <div class="col"></div>
                @endfor

                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
                        $record  = $records->get($dateStr);
                        $color   = $record
                            ? \App\Models\StudentAttendance::statusColors()[$record->status] ?? 'secondary'
                            : 'light';
                        $isToday = $dateStr === today()->toDateString();
                    @endphp
                    <div class="col text-center">
                        <div class="rounded p-1 bg-label-{{ $color }} {{ $isToday ? 'border border-primary' : '' }}"
                             style="min-height:40px; display:flex; align-items:center; justify-content:center; flex-direction:column;"
                             title="{{ $record ? ucfirst($record->status) . ($record->remarks ? ': '.$record->remarks : '') : 'No record' }}">
                            <span class="fw-semibold small">{{ $day }}</span>
                            @if($record)
                                <span style="font-size:9px; line-height:1;">
                                    {{ strtoupper(substr($record->status, 0, 1)) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    @php
                        $dayOfWeek = \Carbon\Carbon::create($year, $month, $day)->dayOfWeek;
                        if ($dayOfWeek === 0) echo '</div><div class="row g-2 mt-1">';
                    @endphp
                @endfor
            </div>

            {{-- Legend --}}
            <div class="d-flex gap-3 mt-4 flex-wrap">
                @foreach(\App\Models\StudentAttendance::statusLabels() as $val => $lbl)
                    <div class="d-flex align-items-center gap-1">
                        <div class="rounded bg-label-{{ \App\Models\StudentAttendance::statusColors()[$val] }}"
                             style="width:20px;height:20px;"></div>
                        <span class="small">{{ explode('/',$lbl)[0] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection