@extends('layouts.tenant')

@section('title', 'Exams')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Exams / परीक्षाएं</h4>
            <p class="text-muted mb-0 small">
                Manage exams for {{ $activeYear?->name ?? 'current year' }}.
            </p>
        </div>
        <button type="button" class="btn btn-primary"
                data-bs-toggle="modal" data-bs-target="#addExamModal">
            <i class="icon-base ti tabler-plus me-1"></i> Add Exam
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Exam Cards --}}
    @if($exams->count() > 0)
        <div class="row g-4">
            @foreach($exams as $exam)
                @php
                    $typeColors = \App\Models\Exam::typeColors();
                    $color = $typeColors[$exam->exam_type] ?? 'secondary';
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge bg-label-{{ $color }} mb-1">
                                    {{ \App\Models\Exam::typeLabels()[$exam->exam_type] ?? '' }}
                                </span>
                                <h5 class="fw-bold mb-0">{{ $exam->name }}</h5>
                                @if($exam->name_hi)
                                    <p class="text-muted small mb-0">{{ $exam->name_hi }}</p>
                                @endif
                            </div>
                            <span class="badge bg-label-{{ $exam->is_published ? 'success' : 'secondary' }}">
                                {{ $exam->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row g-2 text-center mb-3">
                                <div class="col-6">
                                    <p class="text-muted small mb-1">Start Date</p>
                                    <p class="fw-semibold mb-0 small">
                                        {{ $exam->start_date?->format('d M Y') ?? '—' }}
                                    </p>
                                </div>
                                <div class="col-6">
                                    <p class="text-muted small mb-1">End Date</p>
                                    <p class="fw-semibold mb-0 small">
                                        {{ $exam->end_date?->format('d M Y') ?? '—' }}
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex flex-column gap-2">
                                <a href="{{ route('tenant.results.exams.subjects', $exam) }}"
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="icon-base ti tabler-book me-1"></i>
                                    Subjects ({{ $exam->subjects_count }})
                                </a>
                                <a href="{{ route('tenant.results.marks.index', ['exam_id' => $exam->id]) }}"
                                   class="btn btn-outline-success btn-sm">
                                    <i class="icon-base ti tabler-pencil me-1"></i>
                                    Enter Marks
                                </a>
                                <a href="{{ route('tenant.results.report-cards.index', ['exam_id' => $exam->id]) }}"
                                   class="btn btn-outline-info btn-sm">
                                    <i class="icon-base ti tabler-certificate me-1"></i>
                                    Report Cards
                                </a>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <button type="button"
                                    class="btn btn-sm btn-outline-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editExamModal{{ $exam->id }}">
                                <i class="icon-base ti tabler-edit me-1"></i> Edit
                            </button>
                            <form action="{{ route('tenant.results.exams.destroy', $exam) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete {{ $exam->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="icon-base ti tabler-trash me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Edit Modal --}}
                <div class="modal fade" id="editExamModal{{ $exam->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('tenant.results.exams.update', $exam) }}"
                                  method="POST">
                                @csrf @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Exam</h5>
                                    <button type="button" class="btn-close"
                                            data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    @include('tenant.results.exams._form', ['exam' => $exam])
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="icon-base ti tabler-clipboard-list"
                   style="font-size:3rem; color:#ccc;"></i>
                <p class="text-muted mt-2 mb-3">No exams created yet.</p>
                <button type="button" class="btn btn-primary"
                        data-bs-toggle="modal" data-bs-target="#addExamModal">
                    <i class="icon-base ti tabler-plus me-1"></i> Create First Exam
                </button>
            </div>
        </div>
    @endif
</div>

{{-- Add Exam Modal --}}
<div class="modal fade" id="addExamModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tenant.results.exams.store') }}" method="POST">
                @csrf
                <input type="hidden" name="academic_year_id" value="{{ $activeYear?->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">Add Exam / परीक्षा जोड़ें</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('tenant.results.exams._form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base ti tabler-plus me-1"></i> Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection


@push('scripts')
<script>
// Clear add modal on close + re-init hindi autofill on open
const addModal = document.getElementById('addExamModal');
addModal?.addEventListener('show.bs.modal', function() {
    this.querySelectorAll('input[type=text], input[type=date]').forEach(i => {
        if (!i.name.includes('_method')) i.value = '';
    });
    this.querySelector('[name=exam_type]').value = 'unit_test';
    this.querySelector('[name=is_published]').checked = false;
    if (window.initHindiAutofill) window.initHindiAutofill();
});

// Init exam date pickers inside modals
document.querySelectorAll('.modal').forEach(function(modal) {
    modal.addEventListener('shown.bs.modal', function() {
        var startInputs = this.querySelectorAll('.exam-start-date');
        var endInputs   = this.querySelectorAll('.exam-end-date');

        startInputs.forEach(function(el) {
            if (el._flatpickr) return;
            var endEl = el.closest('form').querySelector('.exam-end-date');
            var fp = flatpickr(el, {
                dateFormat:  'Y-m-d',
                altInput:    true,
                altFormat:   'd M Y',
                allowInput:  false,
                defaultDate: el.value || null,
                appendTo: modal, // Ensures calendar appears within modal
                onChange: function(selectedDates) {
                    if (endEl && endEl._flatpickr) {
                        endEl._flatpickr.set('minDate', selectedDates[0]);
                    }
                },
            });
        });

        endInputs.forEach(function(el) {
            if (el._flatpickr) return;
            flatpickr(el, {
                dateFormat:  'Y-m-d',
                altInput:    true,
                altFormat:   'd M Y',
                allowInput:  false,
                defaultDate: el.value || null,
                appendTo: modal, // Ensures calendar appears within modal
            });
        });
    });
});
</script>
@endpush