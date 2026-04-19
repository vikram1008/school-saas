@php
    use Illuminate\Support\Facades\Storage;
@endphp
@extends('layouts.tenant')

@section('title', 'Edit ' . $student->full_name)

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss'])
@endsection

@section('page-style')
    
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('tenant.students.show', $student) }}"
           class="btn btn-icon btn-outline-secondary me-3">
            <i class="icon-base ti tabler-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">
                Edit Student / छात्र संपादित करें
            </h4>
            <p class="text-muted small mb-0">
                {{ $student->full_name }}
                @if($student->first_name_hi)
                    · {{ $student->full_name_hi }}
                @endif
                · <strong>{{ $student->admission_number }}</strong>
            </p>
        </div>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-4">
            <i class="icon-base ti tabler-alert-circle me-1"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('tenant.students.update', $student) }}"
          method="POST"
          enctype="multipart/form-data"
          id="studentEditForm">
        @csrf
        @method('PUT')

        <div class="bs-stepper vertical wizard-modern wizard-modern-vertical">

            {{-- Stepper Header --}}
            <div class="bs-stepper-header">

                <div class="step active" data-target="#step-office">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle">
                            <i class="icon-base ti tabler-clipboard-list"></i>
                        </span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">Office Use / कार्यालय</span>
                            <span class="bs-stepper-subtitle">Admission & Class Details</span>
                        </span>
                    </button>
                </div>
                <div class="line"></div>

                <div class="step" data-target="#step-personal">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle">
                            <i class="icon-base ti tabler-user"></i>
                        </span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">Personal / व्यक्तिगत</span>
                            <span class="bs-stepper-subtitle">Name, DOB, Identity</span>
                        </span>
                    </button>
                </div>
                <div class="line"></div>

                <div class="step" data-target="#step-family">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle">
                            <i class="icon-base ti tabler-users"></i>
                        </span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">Family / परिवार</span>
                            <span class="bs-stepper-subtitle">Father, Mother, Guardian</span>
                        </span>
                    </button>
                </div>
                <div class="line"></div>

                <div class="step" data-target="#step-address">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle">
                            <i class="icon-base ti tabler-map-pin"></i>
                        </span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">Address / पता</span>
                            <span class="bs-stepper-subtitle">Permanent & Correspondence</span>
                        </span>
                    </button>
                </div>
                <div class="line"></div>

                <div class="step" data-target="#step-academic">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle">
                            <i class="icon-base ti tabler-school"></i>
                        </span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">Previous School</span>
                            <span class="bs-stepper-subtitle">History & TC Details</span>
                        </span>
                    </button>
                </div>
                <div class="line"></div>

                <div class="step" data-target="#step-bank">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle">
                            <i class="icon-base ti tabler-building-bank"></i>
                        </span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">Bank / बैंक</span>
                            <span class="bs-stepper-subtitle">DBT & Scholarship</span>
                        </span>
                    </button>
                </div>
                <div class="line"></div>

                <div class="step" data-target="#step-documents">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle">
                            <i class="icon-base ti tabler-file"></i>
                        </span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">Documents / दस्तावेज़</span>
                            <span class="bs-stepper-subtitle">Upload Certificates</span>
                        </span>
                    </button>
                </div>

            </div>

            {{-- Stepper Content --}}
            <div class="bs-stepper-content">

                {{-- STEP 1: Office Use --}}
                <div id="step-office" class="content dstepper-block active show">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Office Use Only / कार्यालय उपयोग</h6>
                        <small>Admission number, class and section assignment.</small>
                    </div>
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                Admission Number / प्रवेश क्रमांक
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="admission_number"
                                   class="form-control @error('admission_number') is-invalid @enderror"
                                   value="{{ old('admission_number', $student->admission_number) }}"
                                   data-required="true">
                            @error('admission_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">SR Number / पंजिका क्रमांक</label>
                            <input type="text" name="sr_number"
                                   class="form-control"
                                   value="{{ old('sr_number', $student->sr_number) }}"
                                   placeholder="Scholar Register No.">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Admission Date / प्रवेश दिनांक</label>
                            <input type="text"
                                    name="admission_date"
                                    id="editAdmissionDate"
                                    class="form-control flatpickr-input"
                                    placeholder="Admission Date"
                                    value="{{ old('admission_date', $student->admission_date?->format('Y-m-d')) }}"
                                    autocomplete="off" readonly>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Academic Year</label>
                            <input type="text" class="form-control bg-light"
                                   value="{{ $activeYear?->name ?? 'No active year' }}"
                                   readonly>
                            <input type="hidden" name="academic_year_id"
                                   value="{{ $activeYear?->id }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Class / कक्षा</label>
                            <select name="class_id" class="form-select"
                                    id="classSelect"
                                    onchange="loadSections(this.value)">
                                <option value="">— Select Class —</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}"
                                        {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Section / अनुभाग</label>
                            <select name="section_id" class="form-select" id="sectionSelect">
                                <option value="">— Select Section —</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}"
                                        {{ old('section_id', $student->section_id) == $section->id ? 'selected' : '' }}>
                                        {{ $section->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary btn-next"
                                    data-step="step-office">
                                <span class="me-2">Next</span>
                                <i class="icon-base ti tabler-arrow-right icon-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 2: Personal --}}
                <div id="step-personal" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Personal Information / व्यक्तिगत जानकारी</h6>
                        <small>Student's personal and identity details.</small>
                    </div>
                    <div class="row g-4">

                        {{-- Photo --}}
                        <div class="col-12 text-center">
                            @if($student->photo)
                                <img id="photoPreview"
                                     src="{{ Storage::url($student->photo) }}"
                                     class="rounded-circle mb-2"
                                     width="90" height="90"
                                     style="object-fit:cover; border:3px solid #eee;">
                            @else
                                <img id="photoPreview"
                                     src="{{ asset('assets/img/avatars/1.png') }}"
                                     class="rounded-circle mb-2"
                                     width="90" height="90"
                                     style="object-fit:cover; border:3px solid #eee;">
                            @endif
                            <div>
                                <input type="file" name="photo"
                                       class="form-control form-control-sm d-inline-block"
                                       style="width:auto"
                                       accept="image/*"
                                       onchange="previewPhoto(this)">
                                <div class="form-text">
                                    Leave empty to keep current photo. Max 2MB.
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                First Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="first_name"
                                   class="form-control @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name', $student->first_name) }}"
                                   data-required="true">
                            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                प्रथम नाम <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="first_name_hi"
                                   class="form-control"
                                   value="{{ old('first_name_hi', $student->first_name_hi) }}"
                                   placeholder="प्रथम नाम (हिंदी में)">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                Last Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="last_name"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name', $student->last_name) }}"
                                   data-required="true">
                            @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                उपनाम <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="last_name_hi"
                                   class="form-control"
                                   value="{{ old('last_name_hi', $student->last_name_hi) }}"
                                   placeholder="उपनाम (हिंदी में)">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">
                                Gender / लिंग <span class="text-danger">*</span>
                            </label>
                            <select name="gender" class="form-select" data-required="true">
                                <option value="">Select</option>
                                @foreach(['male' => 'Male / पुरुष', 'female' => 'Female / महिला', 'other' => 'Other / अन्य'] as $val => $label)
                                    <option value="{{ $val }}"
                                        {{ old('gender', $student->gender) == $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Date of Birth / जन्म तिथि</label>
                            <input type="text"
                                    name="date_of_birth"
                                    id="editStudentDob"
                                    class="form-control flatpickr-input"
                                    placeholder="Date of Birth"
                                    value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"
                                    autocomplete="off" readonly>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Blood Group</label>
                            <select name="blood_group" class="form-select">
                                <option value="">Select</option>
                                @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                    <option value="{{ $bg }}"
                                        {{ old('blood_group', $student->blood_group) == $bg ? 'selected' : '' }}>
                                        {{ $bg }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">DOB in Words (English)</label>
                            <input type="text" name="dob_in_words"
                                   class="form-control"
                                   value="{{ old('dob_in_words', $student->dob_in_words) }}"
                                   placeholder="e.g. Fifteen March Two Thousand Ten">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                जन्म तिथि शब्दों में <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="dob_in_words_hi"
                                   class="form-control"
                                   value="{{ old('dob_in_words_hi', $student->dob_in_words_hi) }}"
                                   placeholder="जैसे: पन्द्रह मार्च दो हजार दस">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Aadhaar / आधार</label>
                            <input type="text" name="aadhaar_number"
                                   class="form-control"
                                   value="{{ old('aadhaar_number', $student->aadhaar_number) }}"
                                   maxlength="12" placeholder="12-digit">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Jan Aadhaar</label>
                            <input type="text" name="jan_aadhaar_number"
                                   class="form-control"
                                   value="{{ old('jan_aadhaar_number', $student->jan_aadhaar_number) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Category / वर्ग</label>
                            <select name="category" class="form-select">
                                <option value="">Select</option>
                                @foreach(['general'=>'General / सामान्य','sc'=>'SC','st'=>'ST','obc'=>'OBC','mbc'=>'MBC','ews'=>'EWS'] as $val => $label)
                                    <option value="{{ $val }}"
                                        {{ old('category', $student->category) == $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Identification Mark</label>
                            <div class="row g-1">
                                <div class="col-6">
                                    <input type="text" name="identification_mark"
                                           class="form-control"
                                           value="{{ old('identification_mark', $student->identification_mark) }}"
                                           placeholder="English">
                                </div>
                                <div class="col-6">
                                    <input type="text" name="identification_mark_hi"
                                           class="form-control"
                                           value="{{ old('identification_mark_hi', $student->identification_mark_hi) }}"
                                           placeholder="हिंदी में">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">CWSN / दिव्यांगता</label>
                            <input type="text" name="cwsn_type"
                                   class="form-control"
                                   value="{{ old('cwsn_type', $student->cwsn_type) }}"
                                   placeholder="Type of disability, if any">
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label fw-semibold d-block">Minority</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox"
                                       name="minority_status" value="1"
                                       {{ old('minority_status', $student->minority_status) ? 'checked' : '' }}>
                                <label class="form-check-label">Yes / हाँ</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label fw-semibold d-block">BPL Status</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox"
                                       name="bpl_status" value="1"
                                       {{ old('bpl_status', $student->bpl_status) ? 'checked' : '' }}>
                                <label class="form-check-label">Yes / हाँ</label>
                            </div>
                        </div>

                        {{-- Contact --}}
                        <div class="col-12">
                            <hr class="my-1">
                            <small class="fw-semibold text-muted">Contact / संपर्क</small>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Mobile</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone', $student->phone) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">WhatsApp</label>
                            <input type="text" name="whatsapp" class="form-control"
                                   value="{{ old('whatsapp', $student->whatsapp) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email', $student->email) }}">
                        </div>

                        <div class="col-12 d-flex justify-content-between">
                            <button type="button" class="btn btn-label-secondary btn-prev">
                                <i class="icon-base ti tabler-arrow-left icon-xs me-2"></i>Previous
                            </button>
                            <button type="button" class="btn btn-primary btn-next"
                                    data-step="step-personal">
                                <span class="me-2">Next</span>
                                <i class="icon-base ti tabler-arrow-right icon-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 3: Family --}}
                <div id="step-family" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Family Details / परिवार विवरण</h6>
                        <small>Father, Mother and Guardian information.</small>
                    </div>
                    @php $family = $student->familyDetail; @endphp
                    <div class="row g-4">
                        <div class="col-12">
                            <p class="fw-semibold text-primary mb-2 border-bottom pb-1">
                                <i class="icon-base ti tabler-man me-1"></i> Father / पिता
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Father's Name</label>
                            <input type="text" name="father_name" class="form-control"
                                   value="{{ old('father_name', $family?->father_name) }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                पिता का नाम <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="father_name_hi" class="form-control"
                                   value="{{ old('father_name_hi', $family?->father_name_hi) }}"
                                   placeholder="पूरा नाम">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Occupation</label>
                            <input type="text" name="father_occupation" class="form-control"
                                   value="{{ old('father_occupation', $family?->father_occupation) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">
                                व्यवसाय <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="father_occupation_hi" class="form-control"
                                   value="{{ old('father_occupation_hi', $family?->father_occupation_hi) }}"
                                   placeholder="जैसे: कृषक">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Annual Income</label>
                            <input type="text" name="father_annual_income" class="form-control"
                                   value="{{ old('father_annual_income', $family?->father_annual_income) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Father's Mobile</label>
                            <input type="text" name="father_mobile" class="form-control"
                                   value="{{ old('father_mobile', $family?->father_mobile) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Father's Aadhaar</label>
                            <input type="text" name="father_aadhaar" class="form-control"
                                   value="{{ old('father_aadhaar', $family?->father_aadhaar) }}"
                                   maxlength="12">
                        </div>

                        <div class="col-12">
                            <p class="fw-semibold text-danger mb-2 border-bottom pb-1 mt-2">
                                <i class="icon-base ti tabler-woman me-1"></i> Mother / माता
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Mother's Name</label>
                            <input type="text" name="mother_name" class="form-control"
                                   value="{{ old('mother_name', $family?->mother_name) }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                माता का नाम <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="mother_name_hi" class="form-control"
                                   value="{{ old('mother_name_hi', $family?->mother_name_hi) }}"
                                   placeholder="पूरा नाम">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Occupation</label>
                            <input type="text" name="mother_occupation" class="form-control"
                                   value="{{ old('mother_occupation', $family?->mother_occupation) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">
                                व्यवसाय <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="mother_occupation_hi" class="form-control"
                                   value="{{ old('mother_occupation_hi', $family?->mother_occupation_hi) }}"
                                   placeholder="जैसे: गृहिणी">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Annual Income</label>
                            <input type="text" name="mother_annual_income" class="form-control"
                                   value="{{ old('mother_annual_income', $family?->mother_annual_income) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Mother's Mobile</label>
                            <input type="text" name="mother_mobile" class="form-control"
                                   value="{{ old('mother_mobile', $family?->mother_mobile) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Mother's Aadhaar</label>
                            <input type="text" name="mother_aadhaar" class="form-control"
                                   value="{{ old('mother_aadhaar', $family?->mother_aadhaar) }}"
                                   maxlength="12">
                        </div>

                        <div class="col-12">
                            <p class="fw-semibold text-warning mb-2 border-bottom pb-1 mt-2">
                                <i class="icon-base ti tabler-user-heart me-1"></i>
                                Guardian / अभिभावक
                                <small class="text-muted fw-normal">(if not living with parents)</small>
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Guardian's Name</label>
                            <input type="text" name="guardian_name" class="form-control"
                                   value="{{ old('guardian_name', $family?->guardian_name) }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                अभिभावक का नाम <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="guardian_name_hi" class="form-control"
                                   value="{{ old('guardian_name_hi', $family?->guardian_name_hi) }}"
                                   placeholder="पूरा नाम">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Relationship</label>
                            <input type="text" name="guardian_relationship" class="form-control"
                                   value="{{ old('guardian_relationship', $family?->guardian_relationship) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">
                                संबंध <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="guardian_relationship_hi" class="form-control"
                                   value="{{ old('guardian_relationship_hi', $family?->guardian_relationship_hi) }}"
                                   placeholder="जैसे: चाचा">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Guardian Mobile</label>
                            <input type="text" name="guardian_mobile" class="form-control"
                                   value="{{ old('guardian_mobile', $family?->guardian_mobile) }}">
                        </div>

                        <div class="col-12 d-flex justify-content-between">
                            <button type="button" class="btn btn-label-secondary btn-prev">
                                <i class="icon-base ti tabler-arrow-left icon-xs me-2"></i>Previous
                            </button>
                            <button type="button" class="btn btn-primary btn-next"
                                    data-step="step-family">
                                <span class="me-2">Next</span>
                                <i class="icon-base ti tabler-arrow-right icon-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 4: Address --}}
                <div id="step-address" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Address / पता</h6>
                        <small>Permanent and correspondence address.</small>
                    </div>
                    @php $addr = $student->address; @endphp
                    <div class="row g-4">
                        <div class="col-12">
                            <p class="fw-semibold text-primary mb-2 border-bottom pb-1">
                                Permanent Address / स्थायी पता
                            </p>
                        </div>

                        @foreach([
                            ['perm_house_no',     'perm_house_no_hi',     'House No. / मकान नं.',   'House No.',   'मकान नं.'],
                            ['perm_street',       'perm_street_hi',       'Street / गली',            'Street',      'गली'],
                            ['perm_village_city', 'perm_village_city_hi', 'Village/City / ग्राम',    'Village/City','ग्राम/शहर'],
                            ['perm_tehsil',       'perm_tehsil_hi',       'Tehsil / तहसील',          'Tehsil',      'तहसील'],
                            ['perm_district',     'perm_district_hi',     'District / जिला',         'District',    'जिला'],
                            ['perm_state',        'perm_state_hi',        'State / राज्य',           'State',       'राज्य'],
                        ] as [$f, $fhi, $label, $ph, $phhi])
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">{{ $label }}</label>
                                <div class="row g-1">
                                    <div class="col-6">
                                        <input type="text" name="{{ $f }}"
                                               class="form-control form-control-sm"
                                               value="{{ old($f, $addr?->{$f}) }}"
                                               placeholder="{{ $ph }}">
                                    </div>
                                    <div class="col-6">
                                        <input type="text" name="{{ $fhi }}"
                                               class="form-control form-control-sm"
                                               value="{{ old($fhi, $addr?->{$fhi}) }}"
                                               placeholder="{{ $phhi }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="col-sm-3">
                            <label class="form-label fw-semibold">PIN Code</label>
                            <input type="text" name="perm_pincode" class="form-control"
                                   value="{{ old('perm_pincode', $addr?->perm_pincode) }}"
                                   maxlength="6">
                        </div>

                        <div class="col-12 mt-2">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-1">
                                <p class="fw-semibold text-primary mb-0">
                                    Correspondence Address / पत्राचार पता
                                </p>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox"
                                           name="same_as_permanent"
                                           id="sameAsPermanent" value="1"
                                           {{ old('same_as_permanent', $addr?->same_as_permanent ?? true) ? 'checked' : '' }}
                                           onchange="toggleCorrAddress(this)">
                                    <label class="form-check-label small" for="sameAsPermanent">
                                        Same as permanent
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="corrAddressFields" class="col-12"
                             style="{{ old('same_as_permanent', $addr?->same_as_permanent ?? true) ? 'display:none' : '' }}">
                            <div class="row g-3">
                                @foreach([
                                    ['corr_house_no',     'House No.'],
                                    ['corr_street',       'Street / गली'],
                                    ['corr_village_city', 'Village/City'],
                                    ['corr_tehsil',       'Tehsil'],
                                    ['corr_district',     'District'],
                                    ['corr_state',        'State'],
                                    ['corr_pincode',      'PIN Code'],
                                ] as [$field, $label])
                                    <div class="col-sm-4">
                                        <label class="form-label fw-semibold small">{{ $label }}</label>
                                        <input type="text" name="{{ $field }}"
                                               class="form-control form-control-sm"
                                               value="{{ old($field, $addr?->{$field}) }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-between">
                            <button type="button" class="btn btn-label-secondary btn-prev">
                                <i class="icon-base ti tabler-arrow-left icon-xs me-2"></i>Previous
                            </button>
                            <button type="button" class="btn btn-primary btn-next"
                                    data-step="step-address">
                                <span class="me-2">Next</span>
                                <i class="icon-base ti tabler-arrow-right icon-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 5: Previous School --}}
                <div id="step-academic" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Previous School History / पूर्व विद्यालय</h6>
                    </div>
                    @php $hist = $student->academicHistory; @endphp
                    <div class="row g-4">
                        <div class="col-sm-8">
                            <label class="form-label fw-semibold">Previous School Name</label>
                            <input type="text" name="previous_school_name" class="form-control"
                                   value="{{ old('previous_school_name', $hist?->previous_school_name) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">School Type</label>
                            <select name="previous_school_type" class="form-select">
                                <option value="">Select</option>
                                @foreach(['government'=>'Government / सरकारी','private'=>'Private / निजी','aided'=>'Aided / अनुदानित'] as $val=>$lbl)
                                    <option value="{{ $val }}"
                                        {{ old('previous_school_type', $hist?->previous_school_type) == $val ? 'selected' : '' }}>
                                        {{ $lbl }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Last Class Attended</label>
                            <input type="text" name="last_class_attended" class="form-control"
                                   value="{{ old('last_class_attended', $hist?->last_class_attended) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Result</label>
                            <select name="last_result" class="form-select">
                                <option value="">Select</option>
                                @foreach(['pass'=>'Pass / उत्तीर्ण','fail'=>'Fail / अनुत्तीर्ण','promoted'=>'Promoted / पदोन्नत','na'=>'N/A'] as $val=>$lbl)
                                    <option value="{{ $val }}"
                                        {{ old('last_result', $hist?->last_result) == $val ? 'selected' : '' }}>
                                        {{ $lbl }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Percentage / Grade</label>
                            <input type="text" name="percentage_grade" class="form-control"
                                   value="{{ old('percentage_grade', $hist?->percentage_grade) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Medium of Instruction</label>
                            <select name="medium_of_instruction" class="form-select">
                                <option value="">Select</option>
                                @foreach(['hindi'=>'Hindi / हिंदी','english'=>'English','other'=>'Other'] as $val=>$lbl)
                                    <option value="{{ $val }}"
                                        {{ old('medium_of_instruction', $hist?->medium_of_instruction) == $val ? 'selected' : '' }}>
                                        {{ $lbl }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">TC Number</label>
                            <input type="text" name="tc_number" class="form-control"
                                   value="{{ old('tc_number', $hist?->tc_number) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">TC Issue Date</label>
                            <input type="text"
                                    name="tc_issue_date"
                                    id="editTcIssueDate"
                                    class="form-control flatpickr-input"
                                    placeholder="TC Issue Date"
                                    value="{{ old('tc_issue_date', $student->academicHistory?->tc_issue_date?->format('Y-m-d')) }}"
                                    autocomplete="off" readonly>
                        </div>

                        <div class="col-12 d-flex justify-content-between">
                            <button type="button" class="btn btn-label-secondary btn-prev">
                                <i class="icon-base ti tabler-arrow-left icon-xs me-2"></i>Previous
                            </button>
                            <button type="button" class="btn btn-primary btn-next"
                                    data-step="step-academic">
                                <span class="me-2">Next</span>
                                <i class="icon-base ti tabler-arrow-right icon-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 6: Bank --}}
                <div id="step-bank" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Bank Details / बैंक विवरण</h6>
                        <small>DBT and scholarship payment information.</small>
                    </div>
                    @php $bank = $student->bankDetail; @endphp
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control"
                                   value="{{ old('bank_name', $bank?->bank_name) }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Branch</label>
                            <input type="text" name="bank_branch" class="form-control"
                                   value="{{ old('bank_branch', $bank?->bank_branch) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Account Number</label>
                            <input type="text" name="account_number" class="form-control"
                                   value="{{ old('account_number', $bank?->account_number) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">IFSC Code</label>
                            <input type="text" name="ifsc_code" id="ifscCode"
                                   class="form-control"
                                   value="{{ old('ifsc_code', $bank?->ifsc_code) }}"
                                   style="text-transform:uppercase">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Account Holder</label>
                            <select name="account_holder" class="form-select">
                                <option value="parent" {{ old('account_holder', $bank?->account_holder) == 'parent' ? 'selected' : '' }}>Parent</option>
                                <option value="student" {{ old('account_holder', $bank?->account_holder) == 'student' ? 'selected' : '' }}>Student</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Account Holder Name</label>
                            <input type="text" name="account_holder_name" class="form-control"
                                   value="{{ old('account_holder_name', $bank?->account_holder_name) }}">
                        </div>

                        <div class="col-12 d-flex justify-content-between">
                            <button type="button" class="btn btn-label-secondary btn-prev">
                                <i class="icon-base ti tabler-arrow-left icon-xs me-2"></i>Previous
                            </button>
                            <button type="button" class="btn btn-primary btn-next"
                                    data-step="step-bank">
                                <span class="me-2">Next</span>
                                <i class="icon-base ti tabler-arrow-right icon-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 7: Documents --}}
                <div id="step-documents" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Documents / दस्तावेज़</h6>
                        <small>Upload new or replace existing documents.</small>
                    </div>
                    <div class="alert alert-info small mb-3">
                        <i class="icon-base ti tabler-info-circle me-1"></i>
                        Uploading a new file will replace the existing document for that type.
                    </div>
                    <div class="row g-3">
                        @foreach(\App\Models\StudentDocument::typeLabels() as $type => $label)
                            @php
                                $existing = $student->documents->firstWhere('document_type', $type);
                            @endphp
                            <div class="col-md-6">
                                <div class="border rounded p-3 {{ $existing ? 'border-success' : '' }}">
                                    <label class="form-label fw-semibold small mb-1 d-block">
                                        {{ $label }}
                                        @if($existing)
                                            <span class="badge bg-label-success ms-1">
                                                <i class="icon-base ti tabler-check"></i>
                                                Uploaded
                                            </span>
                                        @endif
                                    </label>
                                    @if($existing)
                                        <div class="mb-2 d-flex align-items-center gap-2">
                                            <span class="text-muted small">
                                                {{ $existing->original_name }}
                                            </span>
                                            <a href="{{ Storage::url($existing->file_path) }}"
                                               target="_blank"
                                               class="btn btn-xs btn-outline-primary">
                                                <i class="icon-base ti tabler-eye"></i>
                                            </a>
                                        </div>
                                    @endif
                                    <input type="file"
                                           name="documents[{{ $type }}]"
                                           class="form-control form-control-sm"
                                           accept=".pdf,.jpg,.jpeg,.png">
                                    @if($existing)
                                        <div class="form-text">Upload to replace existing file</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-label-secondary btn-prev">
                            <i class="icon-base ti tabler-arrow-left icon-xs me-2"></i>Previous
                        </button>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="icon-base ti tabler-device-floppy me-2"></i>
                            Save Changes / बदलाव सहेजें
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const stepper = new Stepper(document.querySelector('.bs-stepper'), {
        linear: false,
        animation: false
    });
    stepper.to(1);

    document.querySelectorAll('.bs-stepper').forEach(el => {
        el.addEventListener('show.bs-stepper', function(e) {
            setTimeout(() => initFlatpickrs(document), 150);
        });
    });

    // Required fields per step
    const stepRequired = {
        'step-office':   ['admission_number'],
        'step-personal': ['first_name', 'last_name', 'gender'],
    };

    function validateStep(stepId) {
        const required = stepRequired[stepId] || [];
        let valid = true;
        required.forEach(name => {
            const field = document.querySelector(`[name="${name}"]`);
            if (!field) return;
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                valid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        return valid;
    }

    function updateNextBtn(stepId) {
        const required = stepRequired[stepId] || [];
        const btn = document.querySelector(`#${stepId} .btn-next`);
        if (!btn) return;
        btn.disabled = !required.every(name => {
            const f = document.querySelector(`[name="${name}"]`);
            return f && f.value.trim() !== '';
        });
    }

    document.querySelectorAll('.btn-next').forEach(btn => {
        const stepId = btn.dataset.step;
        updateNextBtn(stepId);

        const required = stepRequired[stepId] || [];
        required.forEach(name => {
            const field = document.querySelector(`[name="${name}"]`);
            if (field) {
                field.addEventListener('input', () => updateNextBtn(stepId));
                field.addEventListener('change', () => updateNextBtn(stepId));
            }
        });

        btn.addEventListener('click', () => {
            if (validateStep(stepId)) stepper.next();
        });
    });

    document.querySelectorAll('.btn-prev').forEach(btn => {
        btn.addEventListener('click', () => stepper.previous());
    });

    // Photo preview
    window.previewPhoto = function(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('photoPreview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    // Load sections
    window.loadSections = async function(classId) {
        const select = document.getElementById('sectionSelect');
        select.innerHTML = '<option value="">— Select Section —</option>';
        if (!classId) return;
        const res      = await fetch(`/classes/${classId}/sections`);
        const sections = await res.json();
        sections.forEach(s => {
            const selected = {{ $student->section_id ?? 'null' }} === s.id ? 'selected' : '';
            select.innerHTML += `<option value="${s.id}" ${selected}>${s.name}</option>`;
        });
    };

    // Correspondence address toggle
    window.toggleCorrAddress = function(checkbox) {
        document.getElementById('corrAddressFields').style.display
            = checkbox.checked ? 'none' : 'block';
    };

    // IFSC uppercase
    document.getElementById('ifscCode')?.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Jump to error step on server validation failure
    @if($errors->any())
        const stepMap = {
            'admission_number': 0,
            'first_name': 1, 'last_name': 1, 'gender': 1,
        };
        const errorKeys = @json(array_keys($errors->toArray()));
        for (const key of errorKeys) {
            if (stepMap[key] !== undefined) {
                stepper.to(stepMap[key] + 1);
                break;
            }
        }
    @endif

    flatpickr('#editAdmissionDate', {
        dateFormat:  'Y-m-d',
        altInput:    true,
        altFormat:   'd M Y',
        maxDate:     'today',
        allowInput:  false,
        defaultDate: '{{ old('admission_date', $student->admission_date?->format('Y-m-d')) }}' || null,
    });

    flatpickr('#editStudentDob', {
        dateFormat:  'Y-m-d',
        altInput:    true,
        altFormat:   'd M Y',
        maxDate:     'today',
        allowInput:  false,
        defaultDate: '{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}' || null,
    });

    flatpickr('#editTcIssueDate', {
        dateFormat:  'Y-m-d',
        altInput:    true,
        altFormat:   'd M Y',
        allowInput:  false,
        defaultDate: '{{ old('tc_issue_date', $student->academicHistory?->tc_issue_date?->format('Y-m-d')) }}' || null,
    });

});
</script>
@endpush

@endsection