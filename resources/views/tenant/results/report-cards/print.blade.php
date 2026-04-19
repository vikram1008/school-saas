<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Card — {{ $student->full_name }}</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body { background:#fff; font-size:13px; }
        .school-header { background: linear-gradient(135deg,#696cff,#9155fd); }
        .grade-badge { width:50px; height:50px; border-radius:50%; display:flex;
                       align-items:center; justify-content:center; font-weight:bold;
                       font-size:1.1rem; }
        @media print {
            .no-print { display:none !important; }
            body { font-size:11px; }
        }
    </style>
</head>
<body class="p-3">

<div class="no-print mb-3 d-flex gap-2">
    <button onclick="window.print()" class="btn btn-primary btn-sm">
        🖨️ Print Report Card
    </button>
    <button onclick="window.close()" class="btn btn-outline-secondary btn-sm">
        Close
    </button>
</div>

<div style="max-width:750px; margin:auto;">

    {{-- School Header --}}
    <div class="school-header text-white p-3 rounded-top">
        <div class="row align-items-center">
            <div class="col-9">
                <h4 class="text-white fw-bold mb-1">{{ tenant('school_name') }}</h4>
                @if(tenant('school_name_hi'))
                    <p class="mb-0 opacity-75">{{ tenant('school_name_hi') }}</p>
                @endif
            </div>
            <div class="col-3 text-end">
                <p class="text-white fw-bold mb-0">REPORT CARD</p>
                <p class="text-white fw-bold mb-0">प्रगति पत्र</p>
            </div>
        </div>
    </div>

    <div class="border border-top-0 p-3">

        {{-- Student Info --}}
        <div class="row g-3 mb-3">
            <div class="col-sm-9">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="fw-semibold text-muted" style="width:130px">Student Name:</td>
                        <td class="fw-bold">{{ $student->full_name }}</td>
                    </tr>
                    @if($student->first_name_hi)
                        <tr>
                            <td class="fw-semibold text-muted">छात्र का नाम:</td>
                            <td>{{ $student->full_name_hi }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="fw-semibold text-muted">Admission No.:</td>
                        <td>{{ $student->admission_number }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold text-muted">Class / Section:</td>
                        <td>{{ $student->class_section }}</td>
                    </tr>
                    @if($student->familyDetail?->father_name)
                        <tr>
                            <td class="fw-semibold text-muted">Father's Name:</td>
                            <td>{{ $student->familyDetail->father_name }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="fw-semibold text-muted">Exam:</td>
                        <td class="fw-bold">{{ $exam->name }}</td>
                    </tr>
                    @if($exam->start_date)
                        <tr>
                            <td class="fw-semibold text-muted">Exam Period:</td>
                            <td>
                                {{ $exam->start_date?->format('d M Y') }}
                                @if($exam->end_date)
                                    — {{ $exam->end_date->format('d M Y') }}
                                @endif
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
            <div class="col-sm-3 text-center">
                @if($student->photo)
                    <img src="{{ Storage::url($student->photo) }}"
                         class="rounded border" width="80" height="90"
                         style="object-fit:cover;">
                @else
                    <div class="border rounded d-flex align-items-center justify-content-center"
                         style="width:80px; height:90px; background:#f5f5f5; margin:auto;">
                        <span class="text-muted small">Photo</span>
                    </div>
                @endif
            </div>
        </div>

        <hr class="my-2">

        {{-- Marks Table --}}
        <table class="table table-bordered text-center mb-3">
            <thead class="table-light">
                <tr>
                    <th class="text-start">#</th>
                    <th class="text-start">Subject / विषय</th>
                    <th>Max Marks</th>
                    <th>Pass Marks</th>
                    <th>Marks Obtained</th>
                    <th>%</th>
                    <th>Grade</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                @foreach($result['subjects'] as $i => $sr)
                    <tr class="{{ !$sr['passed'] && !$sr['is_absent'] ? 'table-danger' : '' }}">
                        <td class="text-start">{{ $i + 1 }}</td>
                        <td class="text-start">
                            <strong>{{ $sr['subject']->subject_name }}</strong>
                            @if($sr['subject']->subject_name_hi)
                                <br><small class="text-muted">{{ $sr['subject']->subject_name_hi }}</small>
                            @endif
                        </td>
                        <td>{{ $sr['max'] }}</td>
                        <td>{{ $sr['pass_marks'] }}</td>
                        <td>
                            @if($sr['is_absent'])
                                <span class="badge bg-secondary">Absent</span>
                            @else
                                <strong>{{ $sr['obtained'] }}</strong>
                            @endif
                        </td>
                        <td>{{ $sr['is_absent'] ? '—' : $sr['percentage'].'%' }}</td>
                        <td>
                            @if($sr['grade'] && !$sr['is_absent'])
                                <span class="badge bg-{{ $sr['grade']->color ?? 'secondary' }}">
                                    {{ $sr['grade']->grade }}
                                </span>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($sr['is_absent'])
                                <span class="badge bg-secondary">AB</span>
                            @elseif($sr['passed'])
                                <span class="badge bg-success">Pass</span>
                            @else
                                <span class="badge bg-danger">Fail</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="4" class="text-end fw-bold">Total / कुल</td>
                    <td class="fw-bold text-primary">
                        {{ $result['total_marks'] }} / {{ $result['total_max'] }}
                    </td>
                    <td class="fw-bold">{{ $result['overall_pct'] }}%</td>
                    <td>
                        @if($result['overall_grade'])
                            <span class="badge bg-{{ $result['overall_grade']->color ?? 'secondary' }}">
                                {{ $result['overall_grade']->grade }}
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $result['overall_passed'] ? 'success' : 'danger' }}">
                            {{ $result['overall_passed'] ? 'PASS' : 'FAIL' }}
                        </span>
                    </td>
                </tr>
            </tfoot>
        </table>

        {{-- Summary Row --}}
        <div class="row g-3 mb-3">
            <div class="col-sm-3 text-center">
                <div class="border rounded p-2">
                    <p class="text-muted small mb-1">Total Marks</p>
                    <h5 class="fw-bold text-primary mb-0">
                        {{ $result['total_marks'] }}/{{ $result['total_max'] }}
                    </h5>
                </div>
            </div>
            <div class="col-sm-3 text-center">
                <div class="border rounded p-2">
                    <p class="text-muted small mb-1">Percentage</p>
                    <h5 class="fw-bold mb-0
                        {{ $result['overall_pct'] >= 75 ? 'text-success' : ($result['overall_pct'] >= 33 ? 'text-warning' : 'text-danger') }}">
                        {{ $result['overall_pct'] }}%
                    </h5>
                </div>
            </div>
            <div class="col-sm-3 text-center">
                <div class="border rounded p-2">
                    <p class="text-muted small mb-1">Grade</p>
                    <h5 class="fw-bold mb-0">
                        {{ $result['overall_grade']?->grade ?? '—' }}
                        @if($result['overall_grade']?->description)
                            <br><small class="text-muted" style="font-size:11px">
                                {{ $result['overall_grade']->description }}
                            </small>
                        @endif
                    </h5>
                </div>
            </div>
            <div class="col-sm-3 text-center">
                <div class="border rounded p-2">
                    <p class="text-muted small mb-1">Attendance</p>
                    <h5 class="fw-bold mb-0 {{ $result['attendance_pct'] >= 75 ? 'text-success' : 'text-danger' }}">
                        {{ $result['attendance_pct'] }}%
                    </h5>
                </div>
            </div>
        </div>

        {{-- Teacher/Principal Signature --}}
        <div class="row mt-4">
            <div class="col-4 text-center">
                <div style="border-top:1px solid #ccc; padding-top:5px;">
                    <p class="text-muted small mb-0">Class Teacher</p>
                </div>
            </div>
            <div class="col-4 text-center">
                <div style="border-top:1px solid #ccc; padding-top:5px;">
                    <p class="text-muted small mb-0">Parent Signature</p>
                </div>
            </div>
            <div class="col-4 text-center">
                <div style="border-top:1px solid #ccc; padding-top:5px;">
                    <p class="text-muted small mb-0">Principal</p>
                </div>
            </div>
        </div>

        <p class="text-muted text-center mt-3 mb-0" style="font-size:10px">
            Generated: {{ now()->format('d M Y') }} · {{ tenant('school_name') }}
        </p>

    </div>
</div>

</body>
</html>