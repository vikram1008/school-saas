@extends('layouts.tenant')

@section('title', 'Apply for Leave')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/flatpickr/flatpickr.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/flatpickr/flatpickr.js'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('tenant.leave.index') }}" class="btn btn-icon btn-outline-secondary btn-sm">
            <i class="icon-base ti tabler-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0"><i class="icon-base ti tabler-calendar-plus me-2 text-primary"></i>Apply for Leave</h4>
            <p class="text-muted small mb-0">Submit a new leave application for approval</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-4">
            <i class="icon-base ti tabler-alert-circle me-2"></i>
            <strong>Please fix the following:</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('tenant.leave.store') }}" enctype="multipart/form-data" id="leaveForm">
        @csrf

        <div class="row g-4">

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- LEFT: Main Form --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div class="col-lg-8">

                {{-- Applicant Selection (Admin / Parent) --}}
                @php $authUser = auth('tenant')->user(); @endphp

                @if($authUser->isSchoolAdmin())
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-0 bg-transparent pb-0">
                        <h6 class="mb-0 fw-bold text-uppercase small text-muted ls-1">
                            <i class="icon-base ti tabler-users me-2 text-primary"></i>Apply On Behalf Of
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Applicant Type <span class="text-danger">*</span></label>
                                <select name="applicant_type" id="applicantType" class="form-select" required>
                                    <option value="">— Select Type —</option>
                                    <option value="student" @selected(old('applicant_type') === 'student')>Student</option>
                                    <option value="staff"   @selected(old('applicant_type') === 'staff')>Staff</option>
                                </select>
                            </div>
                            <div class="col-md-8" id="studentSelectWrap" style="display:none">
                                <label class="form-label fw-semibold">Student <span class="text-danger">*</span></label>
                                <select name="applicant_id" id="studentSelect" class="form-select">
                                    <option value="">— Select Student —</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" @selected(old('applicant_id') == $student->id)>
                                            {{ $student->first_name }} {{ $student->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8" id="staffSelectWrap" style="display:none">
                                <label class="form-label fw-semibold">Staff Member <span class="text-danger">*</span></label>
                                <select name="applicant_id" id="staffSelect" class="form-select">
                                    <option value="">— Select Staff —</option>
                                    @foreach($staffList as $staff)
                                        <option value="{{ $staff->id }}" @selected(old('applicant_id') == $staff->id)>
                                            {{ $staff->first_name }} {{ $staff->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($authUser->isParent() && $students->isNotEmpty())
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-0 bg-transparent pb-0">
                        <h6 class="mb-0 fw-bold text-uppercase small text-muted">
                            <i class="icon-base ti tabler-user-heart me-2 text-primary"></i>Apply for Child
                        </h6>
                    </div>
                    <div class="card-body">
                        <label class="form-label fw-semibold">Select Child <span class="text-danger">*</span></label>
                        <select name="applicant_id" class="form-select" required>
                            <option value="">— Select Child —</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" @selected(old('applicant_id') == $student->id)>
                                    {{ $student->first_name }} {{ $student->last_name }}
                                    @if($student->class) — {{ $student->class->name }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                {{-- Leave Type & Duration --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-0 bg-transparent pb-0">
                        <h6 class="mb-0 fw-bold text-uppercase small text-muted">
                            <i class="icon-base ti tabler-calendar me-2 text-primary"></i>Leave Details
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">

                            {{-- Leave Type --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Leave Type <span class="text-danger">*</span></label>
                                <div class="row g-2" id="leaveTypeCards">
                                    @foreach($leaveTypes as $lt)
                                        <div class="col-sm-6 col-lg-4">
                                            <input type="radio" class="btn-check" name="leave_type_id"
                                                id="lt_{{ $lt->id }}" value="{{ $lt->id }}"
                                                data-requires-doc="{{ $lt->requires_document ? '1' : '0' }}"
                                                data-max="{{ $lt->max_days_per_year }}"
                                                @checked(old('leave_type_id') == $lt->id)
                                                autocomplete="off" required>
                                            <label class="btn btn-outline-secondary w-100 text-start py-2 px-3" for="lt_{{ $lt->id }}" style="border-radius:10px">
                                                <span class="d-flex align-items-center gap-2">
                                                    <i class="icon-base ti tabler-tag text-primary"></i>
                                                    <span>
                                                        <span class="d-block fw-semibold" style="font-size:0.85rem">{{ $lt->name }}</span>
                                                        @if($lt->name_hi)<span class="d-block text-muted" style="font-size:0.75rem">{{ $lt->name_hi }}</span>@endif
                                                    </span>
                                                </span>
                                                <span class="d-block mt-1">
                                                    <span class="badge bg-label-primary" style="font-size:10px">Max {{ $lt->max_days_per_year }} days/yr</span>
                                                    @if($lt->requires_document)
                                                        <span class="badge bg-label-warning" style="font-size:10px">Doc required</span>
                                                    @endif
                                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div id="leaveTypeAlert" class="mt-2 d-none">
                                    <small class="text-muted" id="leaveTypeAlertText"></small>
                                </div>
                            </div>

                            {{-- Dates --}}
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">
                                    <i class="icon-base ti tabler-calendar-event me-1 text-primary"></i>From Date <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="icon-base ti tabler-calendar"></i></span>
                                    <input type="text" name="from_date" id="fromDate"
                                        class="form-control flatpickr-input @error('from_date') is-invalid @enderror"
                                        placeholder="Select start date"
                                        value="{{ old('from_date') }}"
                                        autocomplete="off" readonly required>
                                </div>
                                @error('from_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-semibold">
                                    <i class="icon-base ti tabler-calendar-event me-1 text-primary"></i>To Date <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="icon-base ti tabler-calendar"></i></span>
                                    <input type="text" name="to_date" id="toDate"
                                        class="form-control flatpickr-input @error('to_date') is-invalid @enderror"
                                        placeholder="Select end date"
                                        value="{{ old('to_date') }}"
                                        autocomplete="off" readonly required>
                                </div>
                                @error('to_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Days Preview --}}
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="text-center w-100 p-3 rounded-3" id="daysPreviewBox"
                                    style="background: rgba(105,108,255,0.08); border: 2px dashed rgba(105,108,255,0.3);">
                                    <p class="mb-0 fw-bold fs-4 text-primary" id="daysCount" style="line-height:1">—</p>
                                    <small class="text-muted d-block" style="font-size:11px">Days</small>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Reason --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-0 bg-transparent pb-0">
                        <h6 class="mb-0 fw-bold text-uppercase small text-muted">
                            <i class="icon-base ti tabler-message-2 me-2 text-primary"></i>Reason for Leave
                        </h6>
                    </div>
                    <div class="card-body">
                        <label class="form-label fw-semibold">
                            Detailed Reason <span class="text-danger">*</span>
                        </label>
                        <textarea name="reason" id="reasonField" class="form-control @error('reason') is-invalid @enderror"
                            rows="5" required maxlength="1000"
                            placeholder="Please describe the reason for your leave request in detail. For medical leave, mention the condition briefly.">{{ old('reason') }}</textarea>
                        <div class="d-flex justify-content-between mt-1">
                            <div>@error('reason')<span class="text-danger small">{{ $message }}</span>@enderror</div>
                            <small class="text-muted"><span id="charCount">0</span>/1000</small>
                        </div>
                    </div>
                </div>

                {{-- Document Upload --}}
                <div class="card border-0 shadow-sm mb-4" id="documentSection">
                    <div class="card-header border-0 bg-transparent pb-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-uppercase small text-muted">
                            <i class="icon-base ti tabler-paperclip me-2 text-primary"></i>Supporting Document
                        </h6>
                        <span id="docRequiredBadge" class="badge bg-label-secondary d-none">Required for this leave type</span>
                        <span id="docOptionalBadge" class="badge bg-label-secondary">Optional</span>
                    </div>
                    <div class="card-body">
                        <div class="document-dropzone border rounded-3 p-4 text-center position-relative"
                            id="dropZone"
                            style="border: 2px dashed #c9c9c9; cursor:pointer; transition: all 0.25s ease; background: transparent;">
                            <div id="dropZoneContent">
                                <i class="icon-base ti tabler-cloud-upload mb-2" style="font-size:2.5rem; color:#aaa; display:block"></i>
                                <p class="fw-semibold mb-1" style="color:#555">Drop file here or <span class="text-primary">browse</span></p>
                                <p class="text-muted small mb-0">Accepted: PDF, JPG, PNG — Max 2MB</p>
                                <p class="text-muted" style="font-size:11px">(e.g. medical certificate, doctor's note)</p>
                            </div>
                            <div id="filePreviewContent" class="d-none">
                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <div class="avatar">
                                        <span class="avatar-initial rounded bg-label-success">
                                            <i class="icon-base ti tabler-file-check"></i>
                                        </span>
                                    </div>
                                    <div class="text-start">
                                        <p class="fw-semibold mb-0" id="fileName"></p>
                                        <p class="text-muted small mb-0" id="fileSize"></p>
                                    </div>
                                    <button type="button" class="btn btn-icon btn-sm btn-outline-danger ms-2" id="removeFile">
                                        <i class="icon-base ti tabler-x"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="file" name="document" id="documentInput" class="d-none"
                                accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                        <i class="icon-base ti tabler-send me-2"></i>Submit Application
                    </button>
                    <a href="{{ route('tenant.leave.index') }}" class="btn btn-outline-secondary btn-lg px-4">Cancel</a>
                </div>

            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- RIGHT: Info Sidebar --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div class="col-lg-4">

                {{-- Duration Summary --}}
                <div class="card border-0 shadow-sm mb-4" id="summaryCard" style="display:none !important">
                    <div class="card-body text-center py-4">
                        <div class="avatar avatar-xl mb-3">
                            <span class="avatar-initial rounded-circle bg-label-primary">
                                <i class="icon-base ti tabler-calendar-stats" style="font-size:1.5rem"></i>
                            </span>
                        </div>
                        <h2 class="fw-bold text-primary mb-0" id="summaryDays">—</h2>
                        <p class="text-muted mb-1">Days of Leave</p>
                        <div id="summaryDates" class="small text-muted d-none">
                            <i class="icon-base ti tabler-calendar me-1"></i>
                            <span id="summaryFrom"></span> → <span id="summaryTo"></span>
                        </div>
                    </div>
                </div>

                {{-- Leave Policy Info --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-0 bg-transparent pb-0">
                        <h6 class="mb-0 fw-bold"><i class="icon-base ti tabler-info-circle me-2 text-primary"></i>Guidelines</h6>
                    </div>
                    <div class="card-body pt-2">
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex gap-2 mb-3">
                                <div class="avatar avatar-xs mt-1 flex-shrink-0">
                                    <span class="avatar-initial rounded-circle bg-label-success"><i class="icon-base ti tabler-check" style="font-size:10px"></i></span>
                                </div>
                                <small class="text-muted">Class teacher approves student leave by default. School admin can approve all leave.</small>
                            </li>
                            <li class="d-flex gap-2 mb-3">
                                <div class="avatar avatar-xs mt-1 flex-shrink-0">
                                    <span class="avatar-initial rounded-circle bg-label-warning"><i class="icon-base ti tabler-file" style="font-size:10px"></i></span>
                                </div>
                                <small class="text-muted">Medical leave requires a supporting document (prescription or medical certificate).</small>
                            </li>
                            <li class="d-flex gap-2 mb-3">
                                <div class="avatar avatar-xs mt-1 flex-shrink-0">
                                    <span class="avatar-initial rounded-circle bg-label-info"><i class="icon-base ti tabler-clock" style="font-size:10px"></i></span>
                                </div>
                                <small class="text-muted">You can cancel a pending application before it is reviewed.</small>
                            </li>
                            <li class="d-flex gap-2">
                                <div class="avatar avatar-xs mt-1 flex-shrink-0">
                                    <span class="avatar-initial rounded-circle bg-label-danger"><i class="icon-base ti tabler-alert-triangle" style="font-size:10px"></i></span>
                                </div>
                                <small class="text-muted">Retroactive or fraudulent leave applications may be rejected and flagged.</small>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Leave Types Quick Reference --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-0 bg-transparent pb-0">
                        <h6 class="mb-0 fw-bold"><i class="icon-base ti tabler-list me-2 text-primary"></i>Leave Types</h6>
                    </div>
                    <div class="card-body pt-2">
                        @foreach($leaveTypes as $lt)
                            <div class="d-flex justify-content-between align-items-start py-2 border-bottom">
                                <div>
                                    <p class="fw-semibold mb-0 small">{{ $lt->name }}</p>
                                    @if($lt->name_hi)<small class="text-muted">{{ $lt->name_hi }}</small>@endif
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-label-primary" style="font-size:10px">{{ $lt->max_days_per_year }} days/yr</span>
                                    @if($lt->requires_document)
                                        <br><span class="badge bg-label-warning mt-1" style="font-size:9px">Doc needed</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Admin: show/hide student or staff select ──────────────────────
    const applicantType = document.getElementById('applicantType');
    if (applicantType) {
        function toggleApplicantSelect() {
            const v = applicantType.value;
            document.getElementById('studentSelectWrap').style.display = v === 'student' ? '' : 'none';
            document.getElementById('staffSelectWrap').style.display   = v === 'staff'   ? '' : 'none';
            const ss = document.getElementById('studentSelect');
            const fs = document.getElementById('staffSelect');
            if (ss) ss.required = v === 'student';
            if (fs) fs.required = v === 'staff';
        }
        applicantType.addEventListener('change', toggleApplicantSelect);
        toggleApplicantSelect(); // run on load for old() values
    }

    // ── Flatpickr date pickers ────────────────────────────────────────
    const today = new Date().toISOString().split('T')[0];

    const fpFrom = flatpickr('#fromDate', {
        dateFormat:   'Y-m-d',
        altInput:     true,
        altFormat:    'd M Y',
        minDate:      'today',
        allowInput:   false,
        disableMobile: true,
        defaultDate:  '{{ old('from_date') }}' || null,
        onChange: function(selectedDates, dateStr) {
            if (selectedDates[0]) {
                fpTo.set('minDate', selectedDates[0]);
                // Auto-set to same day if to is before from
                if (fpTo.selectedDates[0] && fpTo.selectedDates[0] < selectedDates[0]) {
                    fpTo.setDate(selectedDates[0]);
                }
            }
            updateDays();
        },
    });

    const fpTo = flatpickr('#toDate', {
        dateFormat:   'Y-m-d',
        altInput:     true,
        altFormat:    'd M Y',
        minDate:      'today',
        allowInput:   false,
        disableMobile: true,
        defaultDate:  '{{ old('to_date') }}' || null,
        onChange: function() {
            updateDays();
        },
    });

    // ── Days calculator ───────────────────────────────────────────────
    const daysCount    = document.getElementById('daysCount');
    const summaryCard  = document.getElementById('summaryCard');
    const summaryDays  = document.getElementById('summaryDays');
    const summaryDates = document.getElementById('summaryDates');
    const summaryFrom  = document.getElementById('summaryFrom');
    const summaryTo    = document.getElementById('summaryTo');

    function updateDays() {
        const f = fpFrom.selectedDates[0];
        const t = fpTo.selectedDates[0];

        if (f && t && t >= f) {
            const diff = Math.round((t - f) / 86400000) + 1;

            daysCount.textContent = diff;
            daysCount.style.color = diff > 15 ? '#ea5455' : diff > 7 ? '#ff9f43' : '#696cff';

            summaryDays.textContent = diff + (diff === 1 ? ' Day' : ' Days');
            summaryFrom.textContent = f.toLocaleDateString('en-IN', {day:'numeric', month:'short'});
            summaryTo.textContent   = t.toLocaleDateString('en-IN', {day:'numeric', month:'short', year:'numeric'});
            summaryDates.classList.remove('d-none');
            summaryCard.style.removeProperty('display');
        } else {
            daysCount.textContent = '—';
            daysCount.style.color = '';
            summaryCard.style.display = 'none';
        }
    }

    updateDays(); // run once for old() values

    // ── Leave type radio card → show doc requirement ──────────────────
    const docSection      = document.getElementById('documentSection');
    const docRequiredBadge = document.getElementById('docRequiredBadge');
    const docOptionalBadge = document.getElementById('docOptionalBadge');
    const leaveTypeAlert  = document.getElementById('leaveTypeAlert');
    const leaveTypeAlertText = document.getElementById('leaveTypeAlertText');

    document.querySelectorAll('[name="leave_type_id"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const requiresDoc = this.dataset.requiresDoc === '1';
            const maxDays     = this.dataset.max;

            if (requiresDoc) {
                docRequiredBadge.classList.remove('d-none');
                docOptionalBadge.classList.add('d-none');
                docSection.style.borderLeft = '3px solid #ff9f43';
            } else {
                docRequiredBadge.classList.add('d-none');
                docOptionalBadge.classList.remove('d-none');
                docSection.style.borderLeft = '';
            }

            leaveTypeAlert.classList.remove('d-none');
            leaveTypeAlertText.textContent = `Maximum ${maxDays} days per year allowed for this leave type.`;
        });
    });

    // ── Character counter for reason ──────────────────────────────────
    const reasonField = document.getElementById('reasonField');
    const charCount   = document.getElementById('charCount');
    if (reasonField) {
        charCount.textContent = reasonField.value.length;
        reasonField.addEventListener('input', () => {
            const len = reasonField.value.length;
            charCount.textContent = len;
            charCount.style.color = len > 900 ? '#ea5455' : '';
        });
    }

    // ── File upload drag & drop ────────────────────────────────────────
    const dropZone          = document.getElementById('dropZone');
    const fileInput         = document.getElementById('documentInput');
    const dropZoneContent   = document.getElementById('dropZoneContent');
    const filePreviewContent = document.getElementById('filePreviewContent');
    const fileNameSpan      = document.getElementById('fileName');
    const fileSizeSpan      = document.getElementById('fileSize');
    const removeFile        = document.getElementById('removeFile');

    dropZone?.addEventListener('click', (e) => {
        if (!e.target.closest('#removeFile')) fileInput.click();
    });

    dropZone?.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '#696cff';
        dropZone.style.background  = 'rgba(105,108,255,0.06)';
    });

    dropZone?.addEventListener('dragleave', () => {
        dropZone.style.borderColor = '#c9c9c9';
        dropZone.style.background  = '';
    });

    dropZone?.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '#c9c9c9';
        dropZone.style.background  = '';
        const file = e.dataTransfer.files[0];
        if (file) showFile(file);
    });

    fileInput?.addEventListener('change', function () {
        if (this.files[0]) showFile(this.files[0]);
    });

    removeFile?.addEventListener('click', (e) => {
        e.stopPropagation();
        fileInput.value = '';
        dropZoneContent.classList.remove('d-none');
        filePreviewContent.classList.add('d-none');
        dropZone.style.borderColor = '#c9c9c9';
    });

    function showFile(file) {
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({ icon: 'error', title: 'File too large', text: 'Maximum file size is 2MB.', confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' }, buttonsStyling: false });
            return;
        }
        fileNameSpan.textContent = file.name;
        fileSizeSpan.textContent = (file.size / 1024).toFixed(0) + ' KB';
        dropZoneContent.classList.add('d-none');
        filePreviewContent.classList.remove('d-none');
        dropZone.style.borderColor = '#71dd37';
        dropZone.style.background  = 'rgba(113,221,55,0.04)';
        // Transfer file to the input
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
    }
});
</script>
@endpush
