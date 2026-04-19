<div class="row g-3">
    <div class="col-sm-7">
        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
        <input type="text" name="name"
               class="form-control"
               value="{{ old('name', $exam->name ?? '') }}"
               id="{{ isset($exam) ? 'editExamName'.$exam->id : 'addExamName' }}"
               data-hindi-target="{{ isset($exam) ? '#editExamNameHi'.$exam->id : '#addExamNameHi' }}"
               placeholder="e.g. Unit Test 1" required>
    </div>
    <div class="col-sm-5">
        <label class="form-label fw-semibold">
            नाम <span class="badge bg-label-warning">हिं</span>
        </label>
        <input type="text" name="name_hi"
               class="form-control"
               id="{{ isset($exam) ? 'editExamNameHi'.$exam->id : 'addExamNameHi' }}"
               value="{{ old('name_hi', $exam->name_hi ?? '') }}"
               placeholder="जैसे: इकाई परीक्षा 1">
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">Exam Type <span class="text-danger">*</span></label>
        <select name="exam_type" class="form-select" required>
            @foreach(\App\Models\Exam::typeLabels() as $val => $lbl)
                <option value="{{ $val }}"
                    {{ old('exam_type', $exam->exam_type ?? 'unit_test') === $val ? 'selected' : '' }}>
                    {{ $lbl }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Start Date</label>
        <input type="text"
                name="start_date"
                class="form-control flatpickr-input exam-start-date"
                placeholder="Exam start date"
                value="{{ old('start_date', isset($exam) && $exam->start_date ? $exam->start_date->format('Y-m-d') : '') }}"
                autocomplete="off" readonly>
    </div>
    <div class="col-sm-6">
        <label class="form-label fw-semibold">End Date</label>
        <input type="text"
                name="end_date"
                class="form-control flatpickr-input exam-end-date"
                placeholder="Exam end date"
                value="{{ old('end_date', isset($exam) && $exam->end_date ? $exam->end_date->format('Y-m-d') : '') }}"
                autocomplete="off" readonly>
    </div>
    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox"
                   name="is_published" value="1"
                   {{ old('is_published', $exam->is_published ?? false) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold">Published (visible to parents)</label>
        </div>
    </div>
</div>