@extends('layouts.tenant')

@section('title', 'Period Slots')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Period Slots / कालांश</h4>
            <p class="text-muted mb-0 small">
                Define period timings for each class before building the timetable.
            </p>
        </div>
        <a href="{{ route('tenant.timetable.index') }}" class="btn btn-outline-primary">
            <i class="icon-base ti tabler-calendar-time me-1"></i> Build Timetable
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Class Filter --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" id="slotFilterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Class</label>
                        <select name="class_id" class="form-select"
                                onchange="document.getElementById('slotFilterForm').submit()">
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
                                    onchange="document.getElementById('slotFilterForm').submit()">
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
                </div>
            </form>
        </div>
    </div>

    @if($classId)
        <div class="row g-4">
            {{-- Existing Slots --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            Defined Periods
                            <span class="badge bg-label-primary ms-1">{{ $slots->count() }}</span>
                        </h5>
                    </div>
                    @if($slots->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Period Name</th>
                                        <th>Time</th>
                                        <th>Day</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($slots->sortBy(['period_number','day_of_week']) as $slot)
                                        <tr>
                                            <td class="fw-semibold">{{ $slot->period_number }}</td>
                                            <td>{{ $slot->period_name }}</td>
                                            <td class="small font-monospace">
                                                {{ $slot->start_time }} – {{ $slot->end_time }}
                                            </td>
                                            <td class="small">
                                                {{ $slot->day_of_week
                                                    ? \App\Models\TimetableSlot::dayLabels()[$slot->day_of_week]
                                                    : 'All Days' }}
                                            </td>
                                            <td class="text-center">
                                                @if($slot->is_break)
                                                    <span class="badge bg-label-warning">Break</span>
                                                @else
                                                    <span class="badge bg-label-primary">Period</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <form action="{{ route('tenant.timetable.slots.destroy', $slot) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Delete this slot?')">
                                                    @csrf @method('DELETE')
                                                    <input type="hidden" name="class_id" value="{{ $classId }}">
                                                    <input type="hidden" name="section_id" value="{{ $sectionId }}">
                                                    <button type="submit"
                                                            class="btn btn-sm btn-icon btn-outline-danger">
                                                        <i class="icon-base ti tabler-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="card-body text-center py-4">
                            <p class="text-muted mb-0">No slots defined yet. Add periods on the right.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Add Slot Form --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Add Period Slot</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tenant.timetable.slots.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="academic_year_id" value="{{ $activeYear?->id }}">
                            <input type="hidden" name="class_id" value="{{ $classId }}">
                            <input type="hidden" name="section_id" value="{{ $sectionId }}">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Period Number <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="period_number"
                                       class="form-control"
                                       placeholder="e.g. 1" min="1" max="15" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Period Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="period_name"
                                       class="form-control"
                                       placeholder="e.g. Period 1, Lunch Break"
                                       required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Start Time <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                        name="start_time"
                                        id="slotStartTime"
                                        class="form-control flatpickr-input"
                                        required
                                        placeholder="Start time e.g. 09:00"
                                        value="{{ old('start_time') }}"
                                        autocomplete="off" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    End Time <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                        name="end_time"
                                        id="slotEndTime"
                                        class="form-control flatpickr-input"
                                        required
                                        placeholder="End time e.g. 09:45"
                                        value="{{ old('end_time') }}"
                                        autocomplete="off" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Day (optional)</label>
                                <select name="day_of_week" class="form-select">
                                    <option value="">All Days (same time)</option>
                                    @foreach(\App\Models\TimetableSlot::dayLabels() as $num => $name)
                                        <option value="{{ $num }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">
                                    Leave blank if timing is same for all days.
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           name="is_break" value="1">
                                    <label class="form-check-label fw-semibold">
                                        This is a break (Lunch/Assembly)
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="icon-base ti tabler-plus me-1"></i> Add Slot
                            </button>
                        </form>

                        {{-- Quick Add Template --}}
                        <hr>
                        <p class="fw-semibold small mb-2">Quick Templates:</p>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mb-2"
                                onclick="applyTemplate('standard')">
                            <i class="icon-base ti tabler-template me-1"></i>
                            Standard 8-Period Day
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100"
                                onclick="applyTemplate('primary')">
                            <i class="icon-base ti tabler-template me-1"></i>
                            Primary 6-Period Day
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="icon-base ti tabler-clock"
                   style="font-size:3rem; color:#ccc;"></i>
                <p class="text-muted mt-2 mb-0">
                    Select a class above to manage its period slots.
                </p>
            </div>
        </div>
    @endif

</div>

@push('scripts')
<script>
const templates = {
    standard: [
        { number: 1, name: 'Period 1',    start: '09:00', end: '09:45', is_break: false },
        { number: 2, name: 'Period 2',    start: '09:45', end: '10:30', is_break: false },
        { number: 3, name: 'Period 3',    start: '10:30', end: '11:15', is_break: false },
        { number: 4, name: 'Break',       start: '11:15', end: '11:30', is_break: true  },
        { number: 5, name: 'Period 4',    start: '11:30', end: '12:15', is_break: false },
        { number: 6, name: 'Period 5',    start: '12:15', end: '13:00', is_break: false },
        { number: 7, name: 'Lunch Break', start: '13:00', end: '13:30', is_break: true  },
        { number: 8, name: 'Period 6',    start: '13:30', end: '14:15', is_break: false },
        { number: 9, name: 'Period 7',    start: '14:15', end: '15:00', is_break: false },
        { number:10, name: 'Period 8',    start: '15:00', end: '15:45', is_break: false },
    ],
    primary: [
        { number: 1, name: 'Period 1',    start: '09:00', end: '09:45', is_break: false },
        { number: 2, name: 'Period 2',    start: '09:45', end: '10:30', is_break: false },
        { number: 3, name: 'Period 3',    start: '10:30', end: '11:15', is_break: false },
        { number: 4, name: 'Break',       start: '11:15', end: '11:30', is_break: true  },
        { number: 5, name: 'Period 4',    start: '11:30', end: '12:15', is_break: false },
        { number: 6, name: 'Period 5',    start: '12:15', end: '13:00', is_break: false },
        { number: 7, name: 'Period 6',    start: '13:00', end: '13:45', is_break: false },
    ],
};

async function applyTemplate(type) {
    if (!confirm(`This will bulk-add ${templates[type].length} slots. Continue?`)) return;

    const form    = document.querySelector('form[action*="slots"]');
    const baseUrl = form.action;
    const token   = form.querySelector('[name=_token]').value;
    const classId = form.querySelector('[name=class_id]').value;
    const sectionId = form.querySelector('[name=section_id]').value;
    const yearId  = form.querySelector('[name=academic_year_id]').value;

    for (const slot of templates[type]) {
        await fetch(baseUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': token,
            },
            body: new URLSearchParams({
                _token:            token,
                academic_year_id:  yearId,
                class_id:          classId,
                section_id:        sectionId,
                period_number:     slot.number,
                period_name:       slot.name,
                start_time:        slot.start,
                end_time:          slot.end,
                is_break:          slot.is_break ? '1' : '0',
            }),
        });
    }

    window.location.reload();
}
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var startTimeFp = flatpickr('#slotStartTime', {
        enableTime:      true,
        noCalendar:      true,
        dateFormat:      'H:i',
        altInput:        true,
        altFormat:       'h:i K',
        allowInput:      false,
        time_24hr:       false,
        minuteIncrement: 5,
        defaultDate:     '{{ old('start_time') }}' || null,
        onChange: function(selectedDates) {
            // Set end time min to start time
            if (endTimeFp && selectedDates[0]) {
                endTimeFp.set('minTime', selectedDates[0]);
            }
        },
    });

    var endTimeFp = flatpickr('#slotEndTime', {
        enableTime:      true,
        noCalendar:      true,
        dateFormat:      'H:i',
        altInput:        true,
        altFormat:       'h:i K',
        allowInput:      false,
        time_24hr:       false,
        minuteIncrement: 5,
        defaultDate:     '{{ old('end_time') }}' || null,
    });
});
</script>
@endpush

@endsection