<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Timetable — {{ $class?->name }}</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body { background: #fff; font-size: 12px; }
        .school-header { background: linear-gradient(135deg,#696cff,#9155fd); }
        table { border-collapse: collapse; }
        td, th { border: 1px solid #dee2e6 !important; }
        .period-cell { min-width: 100px; height: 60px; }
        @media print {
            .no-print { display:none !important; }
        }
    </style>
</head>
<body class="p-4">

<div class="no-print mb-3">
    <button onclick="window.print()" class="btn btn-primary btn-sm">
        🖨️ Print
    </button>
    <button onclick="window.close()" class="btn btn-outline-secondary btn-sm ms-2">
        Close
    </button>
</div>

{{-- Header --}}
<div class="school-header text-white p-3 rounded mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h5 class="text-white fw-bold mb-1">{{ tenant('school_name') }}</h5>
            @if(tenant('school_name_hi'))
                <p class="mb-0 opacity-75 small">{{ tenant('school_name_hi') }}</p>
            @endif
        </div>
        <div class="col-auto text-end">
            <h6 class="text-white fw-bold mb-1">TIME TABLE / समय-सारणी</h6>
            <p class="mb-0 opacity-75 small">
                {{ $class?->name }}
                @if($section) — Section {{ $section->name }} @endif
                | {{ $activeYear?->name }}
            </p>
        </div>
    </div>
</div>

{{-- Timetable --}}
<div class="table-responsive">
    <table class="table table-bordered text-center w-100">
        <thead class="table-light">
            <tr>
                <th style="width:100px">Period</th>
                <th style="width:90px">Time</th>
                @foreach($days as $dayName)
                    <th>{{ $dayName }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($slots->unique('period_number')->sortBy('period_number') as $slot)
                <tr>
                    <td class="fw-bold align-middle">
                        {{ $slot->period_name }}
                    </td>
                    <td class="align-middle text-muted" style="font-size:10px">
                        {{ $slot->start_time }}<br>{{ $slot->end_time }}
                    </td>
                    @if($slot->is_break)
                        <td colspan="{{ count($days) }}"
                            class="align-middle bg-light text-muted fw-semibold">
                            ——— {{ $slot->period_name }} ———
                        </td>
                    @else
                        @foreach($days as $dayNum => $dayName)
                            @php $entry = $grid[$slot->period_number][$dayNum] ?? null; @endphp
                            <td class="period-cell align-middle">
                                @if($entry)
                                    <p class="fw-bold mb-0">{{ $entry->subject_name }}</p>
                                    @if($entry->subject_name_hi)
                                        <p class="text-muted mb-0" style="font-size:10px">
                                            {{ $entry->subject_name_hi }}
                                        </p>
                                    @endif
                                    @if($entry->teacher)
                                        <p class="text-success mb-0" style="font-size:10px">
                                            {{ $entry->teacher->full_name }}
                                        </p>
                                    @endif
                                    @if($entry->room_number)
                                        <p class="text-muted mb-0" style="font-size:10px">
                                            Room: {{ $entry->room_number }}
                                        </p>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        @endforeach
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-between mt-3">
    <p class="text-muted small mb-0">Generated: {{ now()->format('d M Y') }}</p>
    <p class="text-muted small mb-0">{{ tenant('school_name') }}</p>
</div>

</body>
</html>