@extends('layouts.tenant')

@section('title', 'Teacher Timetable')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Teacher Timetable / शिक्षक समय-सारणी</h4>
            <p class="text-muted mb-0 small">View schedule and free slots for any teacher.</p>
        </div>
        <a href="{{ route('tenant.timetable.index') }}"
           class="btn btn-outline-primary">
            <i class="icon-base ti tabler-calendar-time me-1"></i> Class View
        </a>
    </div>

    {{-- Teacher Filter --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" id="teacherFilterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold small">Select Teacher</label>
                        <select name="teacher_id" class="form-select"
                                onchange="document.getElementById('teacherFilterForm').submit()">
                            <option value="">— Select Teacher —</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}"
                                    {{ $teacherId == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->full_name }}
                                    ({{ $teacher->designation ?? ucfirst($teacher->staff_type) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($teacherId && count($periods) > 0)

        @php
            $teacher = $teachers->firstWhere('id', $teacherId);
            $days    = \App\Models\TimetableSlot::dayLabels();
            $totalPeriods = count($allPeriods) * count($days);
            $busyCount    = count($busySlots);
            $freeCount    = $totalPeriods - $busyCount;
        @endphp

        {{-- Teacher Summary --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar">
                        <span class="avatar-initial rounded-circle bg-label-primary">
                            {{ strtoupper(substr($teacher?->first_name ?? 'T', 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-0">{{ $teacher?->full_name }}</h5>
                        <p class="text-muted small mb-0">
                            {{ $teacher?->designation ?? '' }}
                            {{ $teacher?->department ? '· '.$teacher->department : '' }}
                        </p>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="text-center">
                            <h4 class="fw-bold text-primary mb-0">{{ $busyCount }}</h4>
                            <p class="text-muted small mb-0">Periods Assigned</p>
                        </div>
                        <div class="text-center">
                            <h4 class="fw-bold text-success mb-0">{{ $freeCount }}</h4>
                            <p class="text-muted small mb-0">Free Periods</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Teacher Timetable Grid --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Weekly Schedule</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width:80px">Period</th>
                            @foreach($days as $dayNum => $dayName)
                                <th class="text-center">{{ $dayName }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allPeriods as $period)
                            <tr>
                                <td class="text-center align-middle fw-bold">
                                    P{{ $period }}
                                </td>
                                @foreach($days as $dayNum => $dayName)
                                    @php
                                        $entry   = $grid[$period][$dayNum] ?? null;
                                        $isBusy  = in_array("{$dayNum}-{$period}", $busySlots);
                                    @endphp
                                    <td class="text-center align-middle p-1"
                                        style="min-width:110px;">
                                        @if($entry)
                                            <div class="rounded p-2 bg-label-primary">
                                                <p class="fw-semibold mb-0 small text-primary">
                                                    {{ $entry->subject_name }}
                                                </p>
                                                <p class="text-muted mb-0" style="font-size:10px">
                                                    {{ $entry->class?->name }}
                                                    {{ $entry->section ? '('.$entry->section->name.')' : '' }}
                                                </p>
                                                @if($entry->room_number)
                                                    <p class="text-muted mb-0" style="font-size:10px">
                                                        Room: {{ $entry->room_number }}
                                                    </p>
                                                @endif
                                            </div>
                                        @else
                                            <div class="rounded p-2 bg-label-success" style="opacity:0.5">
                                                <p class="text-success mb-0" style="font-size:10px">
                                                    Free
                                                </p>
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Legend --}}
            <div class="card-footer d-flex gap-3">
                <div class="d-flex align-items-center gap-1">
                    <div class="rounded bg-label-primary" style="width:16px;height:16px;"></div>
                    <span class="small">Assigned</span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <div class="rounded bg-label-success" style="width:16px;height:16px; opacity:0.5;"></div>
                    <span class="small">Free</span>
                </div>
            </div>
        </div>

    @elseif($teacherId)
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="icon-base ti tabler-calendar-off"
                   style="font-size:3rem; color:#ccc;"></i>
                <p class="text-muted mt-2 mb-0">
                    No timetable entries found for this teacher.
                </p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="icon-base ti tabler-chalkboard"
                   style="font-size:3.5rem; color:#ccc;"></i>
                <p class="text-muted mt-3 mb-0">
                    Select a teacher above to view their timetable and free slots.
                </p>
            </div>
        </div>
    @endif

</div>
@endsection