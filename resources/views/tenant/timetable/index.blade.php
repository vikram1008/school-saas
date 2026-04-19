@extends('layouts.tenant')

@section('title', 'Timetable')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Timetable / समय-सारणी</h4>
            <p class="text-muted mb-0 small">
                Build and manage class timetables.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tenant.timetable.teacher') }}"
               class="btn btn-outline-info">
                <i class="icon-base ti tabler-chalkboard me-1"></i> Teacher View
            </a>
            <a href="{{ route('tenant.timetable.slots') }}"
               class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-clock me-1"></i> Manage Slots
            </a>
            @if($classId)
                <a href="{{ route('tenant.timetable.print', ['class_id'=>$classId,'section_id'=>$sectionId]) }}"
                   target="_blank"
                   class="btn btn-outline-primary">
                    <i class="icon-base ti tabler-printer me-1"></i> Print
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" id="ttFilterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Class</label>
                        <select name="class_id" class="form-select"
                                onchange="document.getElementById('ttFilterForm').submit()">
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
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Section</label>
                            <select name="section_id" class="form-select"
                                    onchange="document.getElementById('ttFilterForm').submit()">
                                <option value="">All / No Section</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}"
                                        {{ $sectionId == $section->id ? 'selected' : '' }}>
                                        {{ $section->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($classId && $slots->count() === 0)
        <div class="alert alert-warning">
            <i class="icon-base ti tabler-alert-triangle me-1"></i>
            No period slots defined for this class yet.
            <a href="{{ route('tenant.timetable.slots', ['class_id'=>$classId]) }}"
               class="alert-link">Define slots first →</a>
        </div>
    @endif

    @if($classId && $slots->count() > 0)

        {{-- Timetable Grid --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="icon-base ti tabler-calendar-time me-2 text-primary"></i>
                    {{ collect($classes)->firstWhere('id', $classId)?->name }}
                    @if($sectionId)
                        — Section {{ collect($sections)->firstWhere('id', $sectionId)?->name }}
                    @endif
                </h5>
                <span class="text-muted small">Click any cell to edit</span>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered mb-0" id="timetableGrid">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width:130px">Period / Time</th>
                            @foreach($days as $dayNum => $dayName)
                                <th class="text-center">{{ $dayName }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($slots->unique('period_number')->sortBy('period_number') as $slot)
                            <tr class="{{ $slot->is_break ? 'table-secondary' : '' }}">
                                <td class="text-center align-middle">
                                    <p class="fw-bold mb-0 small">{{ $slot->period_name }}</p>
                                    <p class="text-muted mb-0"
                                       style="font-size:10px">
                                        {{ $slot->start_time }} – {{ $slot->end_time }}
                                    </p>
                                </td>
                                @foreach($days as $dayNum => $dayName)
                                    @if($slot->is_break)
                                        <td class="text-center align-middle bg-light text-muted small"
                                            colspan="1">
                                            {{ $slot->period_name }}
                                        </td>
                                    @else
                                        @php
                                            $entry = $grid[$slot->period_number][$dayNum] ?? null;
                                        @endphp
                                        <td class="timetable-cell p-1 align-middle"
                                            style="min-width:120px; cursor:pointer;"
                                            data-period="{{ $slot->period_number }}"
                                            data-day="{{ $dayNum }}"
                                            data-entry-id="{{ $entry?->id }}"
                                            data-subject="{{ $entry?->subject_name }}"
                                            data-subject-hi="{{ $entry?->subject_name_hi }}"
                                            data-teacher="{{ $entry?->teacher_id }}"
                                            data-room="{{ $entry?->room_number }}"
                                            onclick="openEntryModal(this)">
                                            @if($entry)
                                                <div class="rounded p-1 bg-label-primary h-100 text-center">
                                                    <p class="fw-semibold mb-0 small text-primary">
                                                        {{ $entry->subject_name }}
                                                    </p>
                                                    @if($entry->subject_name_hi)
                                                        <p class="text-muted mb-0"
                                                           style="font-size:10px">
                                                            {{ $entry->subject_name_hi }}
                                                        </p>
                                                    @endif
                                                    @if($entry->teacher)
                                                        <p class="text-success mb-0"
                                                           style="font-size:10px">
                                                            <i class="icon-base ti tabler-user"
                                                               style="font-size:9px"></i>
                                                            {{ $entry->teacher->full_name }}
                                                        </p>
                                                    @endif
                                                    @if($entry->room_number)
                                                        <p class="text-muted mb-0"
                                                           style="font-size:10px">
                                                            Room: {{ $entry->room_number }}
                                                        </p>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="text-center text-muted py-2"
                                                     style="font-size:11px;">
                                                    <i class="icon-base ti tabler-plus"
                                                       style="font-size:14px;"></i><br>
                                                    Add
                                                </div>
                                            @endif
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @elseif(!$classId)
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="icon-base ti tabler-calendar-time"
                   style="font-size:3.5rem; color:#ccc;"></i>
                <p class="text-muted mt-3 mb-0">
                    Select a class above to view or edit its timetable.
                </p>
            </div>
        </div>
    @endif

</div>

{{-- Entry Modal --}}
<div class="modal fade" id="entryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="entryModalTitle">Edit Period</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="conflictAlert" class="alert alert-danger d-none mb-3">
                    <i class="icon-base ti tabler-alert-triangle me-1"></i>
                    <span id="conflictMessage"></span>
                </div>
                <div class="row g-3">
                    <div class="col-sm-7">
                        <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                        <select id="modal_subject_select" class="form-select"
                                onchange="fillSubjectFromSelect(this)">
                            <option value="">— Select Subject —</option>
                            @if($classId)
                                @foreach(\App\Models\ClassSubject::where('class_id', $classId)->where('is_active', true)->orderBy('sort_order')->get() as $cs)
                                    <option value="{{ $cs->subject_name }}"
                                            data-hi="{{ $cs->subject_name_hi }}">
                                        {{ $cs->subject_name }}
                                        @if($cs->subject_name_hi) · {{ $cs->subject_name_hi }} @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <input type="hidden" id="modal_subject" value="">
                    </div>
                    <div class="col-sm-5">
                        <label class="form-label fw-semibold">विषय</label>
                        <input type="text" id="modal_subject_hi" class="form-control bg-light" readonly>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Teacher</label>
                        <select id="modal_teacher" class="form-select"
                                onchange="checkTeacherConflict(this.value)">
                            <option value="">— No Teacher —</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">
                                    {{ $teacher->full_name }}
                                    ({{ $teacher->designation ?? $teacher->staff_type }})
                                </option>
                            @endforeach
                        </select>
                        <div id="teacherFreeInfo" class="form-text"></div>
                    </div>
                    <div class="col-sm-5">
                        <label class="form-label fw-semibold">Room No.</label>
                        <input type="text" id="modal_room" class="form-control"
                               placeholder="e.g. 101">
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" id="deleteEntryBtn"
                        class="btn btn-outline-danger d-none"
                        onclick="deleteEntry()">
                    <i class="icon-base ti tabler-trash me-1"></i> Remove
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary"
                            onclick="saveEntry()">
                        <i class="icon-base ti tabler-device-floppy me-1"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentCell   = null;
let currentEntryId = null;
let currentPeriod = null;
let currentDay    = null;
let busySlots     = [];

const ACADEMIC_YEAR_ID = {{ $activeYear?->id ?? 'null' }};
const CLASS_ID         = {{ $classId ?? 'null' }};
const SECTION_ID       = {{ $sectionId ?? 'null' }};

function openEntryModal(cell) {
    currentCell    = cell;
    currentEntryId = cell.dataset.entryId || null;
    currentPeriod  = cell.dataset.period;
    currentDay     = cell.dataset.day;

    const dayName    = @json(\App\Models\TimetableSlot::dayLabels());
    document.getElementById('entryModalTitle').textContent =
        `${dayName[currentDay]} — Period ${currentPeriod}`;

    document.getElementById('modal_subject').value    = cell.dataset.subject || '';
    document.getElementById('modal_subject_hi').value = cell.dataset.subjectHi || '';
    document.getElementById('modal_teacher').value    = cell.dataset.teacher || '';
    document.getElementById('modal_room').value       = cell.dataset.room || '';

    // Show/hide delete button
    document.getElementById('deleteEntryBtn').classList.toggle('d-none', !currentEntryId);

    // Hide conflict alert
    document.getElementById('conflictAlert').classList.add('d-none');
    document.getElementById('teacherFreeInfo').textContent = '';

    const subjectSelect = document.getElementById('modal_subject_select');
    if (subjectSelect) {
        subjectSelect.value = cell.dataset.subject || '';
        document.getElementById('modal_subject').value    = cell.dataset.subject || '';
        document.getElementById('modal_subject_hi').value = cell.dataset.subjectHi || '';
    }

    new bootstrap.Modal(document.getElementById('entryModal')).show();
}

function fillSubjectFromSelect(select) {
    const hi = select.options[select.selectedIndex]?.dataset.hi || '';
    document.getElementById('modal_subject').value    = select.value;
    document.getElementById('modal_subject_hi').value = hi;
}


async function checkTeacherConflict(teacherId) {
    document.getElementById('teacherFreeInfo').textContent = '';
    if (!teacherId) return;

    const res  = await fetch(`/timetable/teacher-free-slots?teacher_id=${teacherId}`);
    const data = await res.json();
    busySlots  = data.busy || [];

    const key  = `${currentDay}-${currentPeriod}`;
    if (busySlots.includes(key)) {
        document.getElementById('teacherFreeInfo').innerHTML =
            '<span class="text-danger">⚠ This teacher is busy at this time slot.</span>';
    } else {
        document.getElementById('teacherFreeInfo').innerHTML =
            '<span class="text-success">✓ Teacher is free at this time.</span>';
    }
}

async function saveEntry() {
    const subject = document.getElementById('modal_subject').value.trim();
    if (!subject) {
        alert('Subject name is required.');
        return;
    }

    const payload = {
        _token:            document.querySelector('meta[name=csrf-token]')?.content
                           || '{{ csrf_token() }}',
        academic_year_id:  ACADEMIC_YEAR_ID,
        class_id:          CLASS_ID,
        section_id:        SECTION_ID,
        day_of_week:       currentDay,
        period_number:     currentPeriod,
        subject_name:      subject,
        subject_name_hi:   document.getElementById('modal_subject_hi').value,
        teacher_id:        document.getElementById('modal_teacher').value,
        room_number:       document.getElementById('modal_room').value,
    };

    const res  = await fetch('{{ route("tenant.timetable.entries.save") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': payload._token,
            'Accept': 'application/json',
        },
        body: JSON.stringify(payload),
    });

    const data = await res.json();

    if (data.conflict) {
        document.getElementById('conflictMessage').textContent = data.message;
        document.getElementById('conflictAlert').classList.remove('d-none');
        return;
    }

    if (data.success) {
        // Update cell UI
        const teacher = data.entry.teacher ? `<p class="text-success mb-0" style="font-size:10px"><i class="icon-base ti tabler-user" style="font-size:9px"></i> ${data.entry.teacher}</p>` : '';
        const room    = data.entry.room_number ? `<p class="text-muted mb-0" style="font-size:10px">Room: ${data.entry.room_number}</p>` : '';
        const hi      = data.entry.subject_name_hi ? `<p class="text-muted mb-0" style="font-size:10px">${data.entry.subject_name_hi}</p>` : '';

        currentCell.innerHTML = `
            <div class="rounded p-1 bg-label-primary h-100 text-center">
                <p class="fw-semibold mb-0 small text-primary">${data.entry.subject_name}</p>
                ${hi}${teacher}${room}
            </div>`;

        currentCell.dataset.entryId   = data.entry.id;
        currentCell.dataset.subject   = data.entry.subject_name;
        currentCell.dataset.subjectHi = data.entry.subject_name_hi || '';
        currentCell.dataset.teacher   = document.getElementById('modal_teacher').value;
        currentCell.dataset.room      = data.entry.room_number || '';

        bootstrap.Modal.getInstance(document.getElementById('entryModal')).hide();
    }
}

async function deleteEntry() {
    if (!currentEntryId || !confirm('Remove this entry?')) return;

    const res  = await fetch(`/timetable/entries/${currentEntryId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    });

    const data = await res.json();
    if (data.success) {
        currentCell.innerHTML = `
            <div class="text-center text-muted py-2" style="font-size:11px;">
                <i class="icon-base ti tabler-plus" style="font-size:14px;"></i><br>Add
            </div>`;
        currentCell.dataset.entryId   = '';
        currentCell.dataset.subject   = '';
        currentCell.dataset.subjectHi = '';
        currentCell.dataset.teacher   = '';
        currentCell.dataset.room      = '';

        bootstrap.Modal.getInstance(document.getElementById('entryModal')).hide();
    }
}
</script>
@endpush

@endsection