@extends('layouts.tenant')

@section('title', 'Add Staff')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss',
           'resources/assets/vendor/libs/flatpickr/flatpickr.scss'])
@endsection

@section('page-style')
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js',
           'resources/assets/vendor/libs/flatpickr/flatpickr.js'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('tenant.staff.index') }}"
           class="btn btn-icon btn-outline-secondary me-3">
            <i class="icon-base ti tabler-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">Add Staff / नया स्टाफ जोड़ें</h4>
            <p class="text-muted small mb-0">
                <span class="text-danger">*</span> Required fields must be filled before proceeding.
            </p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-4">
            <i class="icon-base ti tabler-alert-circle me-1"></i>
            <strong>Please fix errors:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('tenant.staff.store') }}"
          method="POST"
          enctype="multipart/form-data"
          id="staffCreateForm"
          novalidate>
        @csrf

        <div class="bs-stepper vertical wizard-modern wizard-modern-vertical">

            {{-- Stepper Header --}}
            <div class="bs-stepper-header">
                @foreach([
                    ['step-basic',        'tabler-clipboard-list', 'Basic Info',    'Employee code & type'],
                    ['step-personal',     'tabler-user',           'Personal',      'Name, DOB, Identity'],
                    ['step-professional', 'tabler-briefcase',      'Professional',  'Designation & Salary'],
                    ['step-address',      'tabler-map-pin',        'Address / पता', 'Permanent & Current'],
                    ['step-subjects',     'tabler-book',           'Subjects',      'Teaching assignments'],
                    ['step-documents',    'tabler-file',           'Documents',     'Upload certificates'],
                ] as $i => [$id, $icon, $title, $subtitle])
                    @if($i > 0) <div class="line"></div> @endif
                    <div class="step {{ $i === 0 ? 'active' : '' }}" data-target="#{{ $id }}">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle">
                                <i class="icon-base ti {{ $icon }}"></i>
                            </span>
                            <span class="bs-stepper-label">
                                <span class="bs-stepper-title">{{ $title }}</span>
                                <span class="bs-stepper-subtitle">{{ $subtitle }}</span>
                            </span>
                        </button>
                    </div>
                @endforeach
            </div>

            <div class="bs-stepper-content">

                {{-- STEP 1: Basic Info --}}
                <div id="step-basic" class="content dstepper-block active show">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Basic Information / मूल जानकारी</h6>
                        <small class="text-muted">Fields marked <span class="text-danger">*</span> are required.</small>
                    </div>
                    <div class="row g-4">
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">
                                Employee Code <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="employee_code"
                                   id="employee_code"
                                   class="form-control @error('employee_code') is-invalid @enderror"
                                   value="{{ old('employee_code') }}"
                                   placeholder="e.g. EMP-001"
                                   required>
                            <div class="invalid-feedback">
                                @error('employee_code'){{ $message }}@else Employee code is required.@enderror
                            </div>
                            <div class="form-text">Default login password = employee code</div>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">
                                Staff Type <span class="text-danger">*</span>
                            </label>
                            <select name="staff_type"
                                    id="staffTypeSelect"
                                    class="form-select @error('staff_type') is-invalid @enderror"
                                    required
                                    onchange="handleStaffType(this.value)">
                                <option value="">— Select Type —</option>
                                @foreach(\App\Models\StaffProfile::typeLabels() as $val => $label)
                                    <option value="{{ $val }}"
                                        {{ old('staff_type') == $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                @error('staff_type'){{ $message }}@else Please select staff type.@enderror
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Login Email <span class="text-muted small">(Optional)</span></label>
                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   value="{{ old('email') }}"
                                   placeholder="Auto-generated if empty">
                            <div class="form-text">Leave blank to auto-generate</div>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="button"
                                    class="btn btn-primary btn-next"
                                    data-step="step-basic">
                                Next <i class="icon-base ti tabler-arrow-right icon-xs ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 2: Personal --}}
                <div id="step-personal" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Personal Information / व्यक्तिगत जानकारी</h6>
                        <small class="text-muted">Fields marked <span class="text-danger">*</span> are required.</small>
                    </div>
                    <div class="row g-4">

                        {{-- Photo --}}
                        <div class="col-12 text-center">
                            <img id="photoPreview"
                                 src="{{ asset('assets/img/avatars/1.png') }}"
                                 class="rounded-circle mb-2"
                                 width="90" height="90"
                                 style="object-fit:cover; border:3px solid #eee;">
                            <div>
                                <input type="file"
                                       name="photo"
                                       class="form-control form-control-sm d-inline-block"
                                       style="width:auto"
                                       accept="image/*"
                                       onchange="previewPhoto(this)">
                                <div class="form-text">Photo — Max 2MB (optional)</div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                First Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="first_name"
                                   id="first_name"
                                   class="form-control @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name') }}"
                                   data-hindi-target="[name='first_name_hi']"
                                   required
                                   maxlength="100">
                            <div class="invalid-feedback">
                                @error('first_name'){{ $message }}@else First name is required.@enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                प्रथम नाम <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text"
                                   name="first_name_hi"
                                   class="form-control"
                                   value="{{ old('first_name_hi') }}"
                                   placeholder="हिंदी में">
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                Last Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="last_name"
                                   id="last_name"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name') }}"
                                   data-hindi-target="[name='last_name_hi']"
                                   required
                                   maxlength="100">
                            <div class="invalid-feedback">
                                @error('last_name'){{ $message }}@else Last name is required.@enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                उपनाम <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text"
                                   name="last_name_hi"
                                   class="form-control"
                                   value="{{ old('last_name_hi') }}"
                                   placeholder="हिंदी में">
                        </div>

                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">
                                Gender <span class="text-danger">*</span>
                            </label>
                            <select name="gender"
                                    id="gender"
                                    class="form-select @error('gender') is-invalid @enderror"
                                    required>
                                <option value="">— Select Gender —</option>
                                <option value="male"   {{ old('gender') == 'male'   ? 'selected' : '' }}>Male / पुरुष</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female / महिला</option>
                                <option value="other"  {{ old('gender') == 'other'  ? 'selected' : '' }}>Other / अन्य</option>
                            </select>
                            <div class="invalid-feedback">
                                @error('gender'){{ $message }}@else Please select gender.@enderror
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Date of Birth</label>
                            <input type="text"
                                   name="date_of_birth"
                                   id="staffDob"
                                   class="form-control"
                                   placeholder="Select date"
                                   value="{{ old('date_of_birth') }}"
                                   autocomplete="off" readonly>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Blood Group</label>
                            <select name="blood_group" class="form-select">
                                <option value="">— Select —</option>
                                @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Aadhaar Number</label>
                            <input type="text"
                                   name="aadhaar_number"
                                   class="form-control"
                                   value="{{ old('aadhaar_number') }}"
                                   placeholder="12-digit"
                                   maxlength="12"
                                   pattern="\d{12}"
                                   inputmode="numeric">
                            <div class="invalid-feedback">Must be exactly 12 digits.</div>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">PAN Number</label>
                            <input type="text"
                                   name="pan_number"
                                   id="panNumber"
                                   class="form-control"
                                   value="{{ old('pan_number') }}"
                                   placeholder="e.g. ABCDE1234F"
                                   maxlength="10"
                                   pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}"
                                   style="text-transform:uppercase">
                            <div class="invalid-feedback">Invalid PAN format (e.g. ABCDE1234F).</div>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">ID Proof Type</label>
                            <select name="id_proof_type" class="form-select">
                                <option value="">— Select —</option>
                                @foreach(['aadhaar'=>'Aadhaar','pan'=>'PAN Card','voter_id'=>'Voter ID','passport'=>'Passport','driving_license'=>'Driving License'] as $val=>$lbl)
                                    <option value="{{ $val }}" {{ old('id_proof_type') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">ID Proof Number</label>
                            <input type="text" name="id_proof_number"
                                   class="form-control"
                                   value="{{ old('id_proof_number') }}">
                        </div>

                        <div class="col-12"><hr class="my-1"><p class="fw-semibold small text-muted mb-0">Contact / संपर्क</p></div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Mobile</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone') }}"
                                   placeholder="10-digit mobile"
                                   maxlength="15">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">WhatsApp</label>
                            <input type="text" name="whatsapp" class="form-control"
                                   value="{{ old('whatsapp') }}"
                                   placeholder="WhatsApp number"
                                   maxlength="15">
                        </div>

                        <div class="col-12"><hr class="my-1"><p class="fw-semibold small text-muted mb-0">Emergency Contact / आपातकालीन संपर्क</p></div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Emergency Contact Name</label>
                            <input type="text" name="emergency_contact_name"
                                   class="form-control"
                                   value="{{ old('emergency_contact_name') }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Emergency Contact Phone</label>
                            <input type="text" name="emergency_contact_phone"
                                   class="form-control"
                                   value="{{ old('emergency_contact_phone') }}">
                        </div>

                        <div class="col-12 d-flex justify-content-between">
                            <button type="button" class="btn btn-label-secondary btn-prev">
                                <i class="icon-base ti tabler-arrow-left icon-xs me-1"></i> Previous
                            </button>
                            <button type="button" class="btn btn-primary btn-next"
                                    data-step="step-personal">
                                Next <i class="icon-base ti tabler-arrow-right icon-xs ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 3: Professional --}}
                <div id="step-professional" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Professional Information / व्यावसायिक जानकारी</h6>
                    </div>
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Designation / पद</label>
                            <div class="row g-1">
                                <div class="col-7">
                                    <input type="text" name="designation"
                                           class="form-control"
                                           value="{{ old('designation') }}"
                                           placeholder="e.g. Principal, TGT, PRT"
                                           data-hindi-target="[name='designation_hi']">
                                </div>
                                <div class="col-5">
                                    <input type="text" name="designation_hi"
                                           class="form-control"
                                           value="{{ old('designation_hi') }}"
                                           placeholder="हिंदी में">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Department / विभाग</label>
                            <div class="row g-1">
                                <div class="col-7">
                                    <input type="text" name="department"
                                           class="form-control"
                                           value="{{ old('department') }}"
                                           placeholder="e.g. Science, Administration"
                                           data-hindi-target="[name='department_hi']">
                                </div>
                                <div class="col-5">
                                    <input type="text" name="department_hi"
                                           class="form-control"
                                           value="{{ old('department_hi') }}"
                                           placeholder="हिंदी में">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Qualification / योग्यता</label>
                            <div class="row g-1">
                                <div class="col-7">
                                    <input type="text" name="qualification"
                                           class="form-control"
                                           value="{{ old('qualification') }}"
                                           placeholder="e.g. B.Ed, M.Sc, MBA"
                                           data-hindi-target="[name='qualification_hi']">
                                </div>
                                <div class="col-5">
                                    <input type="text" name="qualification_hi"
                                           class="form-control"
                                           value="{{ old('qualification_hi') }}"
                                           placeholder="हिंदी में">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label fw-semibold">Experience (Years)</label>
                            <input type="number" name="experience_years"
                                   class="form-control"
                                   value="{{ old('experience_years', 0) }}"
                                   min="0" max="50">
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label fw-semibold">Employment Type</label>
                            <select name="employment_type" class="form-select">
                                @foreach(['full_time'=>'Full Time','part_time'=>'Part Time','contract'=>'Contract','substitute'=>'Substitute'] as $val=>$lbl)
                                    <option value="{{ $val }}" {{ old('employment_type','full_time') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Joining Date</label>
                            <input type="text"
                                   name="joining_date"
                                   id="staffJoiningDate"
                                   class="form-control"
                                   placeholder="Select joining date"
                                   value="{{ old('joining_date') }}"
                                   autocomplete="off" readonly>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Monthly Salary (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="salary"
                                       class="form-control"
                                       value="{{ old('salary') }}"
                                       placeholder="0.00" step="0.01" min="0">
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-between">
                            <button type="button" class="btn btn-label-secondary btn-prev">
                                <i class="icon-base ti tabler-arrow-left icon-xs me-1"></i> Previous
                            </button>
                            <button type="button" class="btn btn-primary btn-next"
                                    data-step="step-professional">
                                Next <i class="icon-base ti tabler-arrow-right icon-xs ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 4: Address --}}
                <div id="step-address" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Address / पता</h6>
                    </div>
                    <div class="row g-4">
                        <div class="col-12">
                            <p class="fw-semibold text-primary mb-2 border-bottom pb-1">
                                Permanent Address / स्थायी पता
                            </p>
                        </div>
                        @foreach([
                            ['perm_house_no','perm_house_no_hi','House No.','मकान नं.'],
                            ['perm_street','perm_street_hi','Street / गली','गली'],
                            ['perm_village_city','perm_village_city_hi','Village/City','ग्राम/शहर'],
                            ['perm_tehsil','perm_tehsil_hi','Tehsil','तहसील'],
                            ['perm_district','perm_district_hi','District','जिला'],
                            ['perm_state','perm_state_hi','State','राज्य'],
                        ] as [$f,$fhi,$ph,$phhi])
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">{{ $ph }} / {{ $phhi }}</label>
                                <div class="row g-1">
                                    <div class="col-6">
                                        <input type="text" name="{{ $f }}"
                                               class="form-control form-control-sm"
                                               value="{{ old($f) }}"
                                               placeholder="{{ $ph }}"
                                               data-hindi-target="[name='{{ $fhi }}']">
                                    </div>
                                    <div class="col-6">
                                        <input type="text" name="{{ $fhi }}"
                                               class="form-control form-control-sm"
                                               value="{{ old($fhi) }}"
                                               placeholder="{{ $phhi }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="col-sm-3">
                            <label class="form-label fw-semibold">PIN Code</label>
                            <input type="text" name="perm_pincode"
                                   class="form-control"
                                   value="{{ old('perm_pincode') }}"
                                   placeholder="6-digit PIN"
                                   maxlength="6"
                                   pattern="\d{6}"
                                   inputmode="numeric">
                            <div class="invalid-feedback">PIN code must be 6 digits.</div>
                        </div>

                        <div class="col-12 mt-2">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-1">
                                <p class="fw-semibold text-primary mb-0">Current Address</p>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox"
                                           name="same_as_permanent"
                                           id="sameAsPermanent" value="1" checked
                                           onchange="toggleCurrAddress(this)">
                                    <label class="form-check-label small" for="sameAsPermanent">
                                        Same as Permanent
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div id="currAddressFields" class="col-12" style="display:none;">
                            <div class="row g-3">
                                @foreach([
                                    ['curr_house_no','House No.'],
                                    ['curr_street','Street'],
                                    ['curr_village_city','Village/City'],
                                    ['curr_tehsil','Tehsil'],
                                    ['curr_district','District'],
                                    ['curr_state','State'],
                                    ['curr_pincode','PIN Code'],
                                ] as [$field,$label])
                                    <div class="col-sm-4">
                                        <label class="form-label fw-semibold small">{{ $label }}</label>
                                        <input type="text" name="{{ $field }}"
                                               class="form-control form-control-sm"
                                               value="{{ old($field) }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-between">
                            <button type="button" class="btn btn-label-secondary btn-prev">
                                <i class="icon-base ti tabler-arrow-left icon-xs me-1"></i> Previous
                            </button>
                            <button type="button" class="btn btn-primary btn-next"
                                    data-step="step-address">
                                Next <i class="icon-base ti tabler-arrow-right icon-xs ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 5: Subject Assignments --}}
                <div id="step-subjects" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Subject Assignments / विषय असाइनमेंट</h6>
                        <small class="text-muted">Only applicable for teaching staff.</small>
                    </div>
                    <div id="subjectAssignmentSection">
                        <div class="alert alert-info small mb-3" id="nonTeachingNotice" style="display:none;">
                            <i class="icon-base ti tabler-info-circle me-1"></i>
                            Subject assignments are only for teaching staff.
                        </div>
                        <div id="assignmentsContainer">
                            <div class="assignment-row border rounded p-3 mb-3">
                                <div class="row g-3 align-items-end">
                                    <div class="col-sm-4">
                                        <label class="form-label fw-semibold small">Class</label>
                                        <select name="assignments[0][class_id]"
                                                class="form-select form-select-sm"
                                                onchange="loadSectionsForAssignment(this, 0)">
                                            <option value="">— Select Class —</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label fw-semibold small">Section</label>
                                        <select name="assignments[0][section_id]"
                                                class="form-select form-select-sm"
                                                id="section-select-0">
                                            <option value="">All</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label fw-semibold small">Subject</label>
                                        <select name="assignments[0][subject_name]"
                                                id="subject-select-0"
                                                class="form-select form-select-sm"
                                                onchange="syncSubjectHi(this, 0)">
                                            <option value="">— Select Subject —</option>
                                        </select>
                                        <input type="hidden" name="assignments[0][subject_name_hi]" id="subject-hi-0">
                                    </div>
                                    <div class="col-sm-1">
                                        <button type="button"
                                                class="btn btn-sm btn-icon btn-outline-danger mt-4"
                                                style="display:none;"
                                                onclick="this.closest('.assignment-row').remove()">
                                            <i class="icon-base ti tabler-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button"
                                class="btn btn-outline-primary btn-sm mt-2"
                                id="addAssignmentBtn"
                                onclick="addAssignmentRow()">
                            <i class="icon-base ti tabler-plus me-1"></i> Add Another Subject
                        </button>
                    </div>

                    <div class="col-12 d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-label-secondary btn-prev">
                            <i class="icon-base ti tabler-arrow-left icon-xs me-1"></i> Previous
                        </button>
                        <button type="button" class="btn btn-primary btn-next"
                                data-step="step-subjects">
                            Next <i class="icon-base ti tabler-arrow-right icon-xs ms-1"></i>
                        </button>
                    </div>
                </div>

                {{-- STEP 6: Documents --}}
                <div id="step-documents" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Documents / दस्तावेज़</h6>
                        <small class="text-muted">Upload PDF, JPG or PNG. Max 2MB each. All optional.</small>
                    </div>
                    <div class="row g-3">
                        @foreach(\App\Models\StaffDocument::typeLabels() as $type => $label)
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <label class="form-label fw-semibold small mb-2 d-block">{{ $label }}</label>
                                    <input type="file"
                                           name="documents[{{ $type }}]"
                                           class="form-control form-control-sm"
                                           accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-label-secondary btn-prev">
                            <i class="icon-base ti tabler-arrow-left icon-xs me-1"></i> Previous
                        </button>
                        <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                            <i class="icon-base ti tabler-user-plus me-2"></i>
                            Add Staff Member
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

    // ── Stepper init ─────────────────────────────────────────────────
    const stepper = new Stepper(document.querySelector('.bs-stepper'), {
        linear: false,
        animation: false
    });
    stepper.to(1);

    // ── Validation rules per step ────────────────────────────────────
    // Each entry: { fieldId, label, type: 'input'|'select', message }
    const stepValidations = {
        'step-basic': [
            { id: 'employee_code', label: 'Employee Code',  type: 'input',  msg: 'Employee code is required.' },
            { id: 'staffTypeSelect', label: 'Staff Type',   type: 'select', msg: 'Please select a staff type.' },
        ],
        'step-personal': [
            { id: 'first_name', label: 'First Name', type: 'input',  msg: 'First name is required.' },
            { id: 'last_name',  label: 'Last Name',  type: 'input',  msg: 'Last name is required.' },
            { id: 'gender',     label: 'Gender',     type: 'select', msg: 'Please select gender.' },
        ],
    };

    // ── Core validation function ─────────────────────────────────────
    function validateStep(stepId) {
        const rules   = stepValidations[stepId] || [];
        const errors  = [];
        let firstInvalidField = null;

        rules.forEach(rule => {
            const field = document.getElementById(rule.id);
            if (!field) return;

            const isEmpty = field.tagName === 'SELECT'
                ? field.value === '' || field.value === null
                : field.value.trim() === '';

            if (isEmpty) {
                // Show inline error
                field.classList.add('is-invalid');
                field.classList.remove('is-valid');

                // Ensure invalid-feedback message is set
                let fb = field.parentElement.querySelector('.invalid-feedback');
                if (fb) fb.textContent = rule.msg;

                errors.push(rule.msg);
                if (!firstInvalidField) firstInvalidField = field;
            } else {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
            }
        });

        if (errors.length > 0) {
            // Focus first invalid field
            if (firstInvalidField) firstInvalidField.focus();

            // SweetAlert error
            Swal.fire({
                icon: 'warning',
                title: 'Required Fields Missing',
                html: '<ul class="text-start mb-0 ps-3">'
                    + errors.map(e => `<li>${e}</li>`).join('')
                    + '</ul>',
                confirmButtonText: 'OK, let me fix it',
                customClass: {
                    confirmButton: 'btn btn-primary waves-effect waves-light',
                },
                buttonsStyling: false,
            });
            return false;
        }

        return true;
    }

    // ── Live validation on field change ──────────────────────────────
    function attachLiveValidation(stepId) {
        const rules = stepValidations[stepId] || [];
        rules.forEach(rule => {
            const field = document.getElementById(rule.id);
            if (!field) return;

            const eventType = field.tagName === 'SELECT' ? 'change' : 'input';
            field.addEventListener(eventType, function () {
                const isEmpty = field.tagName === 'SELECT'
                    ? this.value === ''
                    : this.value.trim() === '';

                if (!isEmpty) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                }
            });
        });
    }

    // Attach live validation for all steps
    Object.keys(stepValidations).forEach(attachLiveValidation);

    // ── Next button → validate current step ──────────────────────────
    document.querySelectorAll('.btn-next').forEach(btn => {
        btn.addEventListener('click', function () {
            const stepId = this.dataset.step;
            if (validateStep(stepId)) {
                stepper.next();
            }
        });
    });

    // ── Prev button ───────────────────────────────────────────────────
    document.querySelectorAll('.btn-prev').forEach(btn => {
        btn.addEventListener('click', () => stepper.previous());
    });

    // ── Submit → validate all required steps before sending ──────────
    document.getElementById('staffCreateForm').addEventListener('submit', function (e) {
        // Validate all steps that have rules
        const allStepIds = Object.keys(stepValidations);
        let firstFailedStep = null;

        const allErrors = [];

        allStepIds.forEach(stepId => {
            const rules = stepValidations[stepId] || [];
            rules.forEach(rule => {
                const field = document.getElementById(rule.id);
                if (!field) return;

                const isEmpty = field.tagName === 'SELECT'
                    ? field.value === ''
                    : field.value.trim() === '';

                if (isEmpty) {
                    field.classList.add('is-invalid');
                    allErrors.push({ stepId, msg: rule.msg, field });
                    if (!firstFailedStep) firstFailedStep = stepId;
                }
            });
        });

        if (allErrors.length > 0) {
            e.preventDefault();

            // Jump to first step with error
            if (firstFailedStep) {
                const stepIndex = Object.keys(stepValidations).indexOf(firstFailedStep);
                // Map stepId to stepper index (step-basic=0, step-personal=1...)
                const stepOrder = ['step-basic','step-personal','step-professional','step-address','step-subjects','step-documents'];
                const idx = stepOrder.indexOf(firstFailedStep);
                if (idx >= 0) stepper.to(idx + 1);
            }

            Swal.fire({
                icon: 'error',
                title: 'Incomplete Form',
                html: '<p class="mb-2">Please complete all required fields:</p>'
                    + '<ul class="text-start mb-0 ps-3">'
                    + allErrors.map(e => `<li>${e.msg}</li>`).join('')
                    + '</ul>',
                confirmButtonText: 'Fix Now',
                customClass: {
                    confirmButton: 'btn btn-danger waves-effect waves-light',
                },
                buttonsStyling: false,
            });
        }
    });

    // ── Utility functions ─────────────────────────────────────────────

    window.previewPhoto = function(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => document.getElementById('photoPreview').src = e.target.result;
            reader.readAsDataURL(input.files[0]);
        }
    };

    window.toggleCurrAddress = function(cb) {
        document.getElementById('currAddressFields').style.display = cb.checked ? 'none' : 'block';
    };

    window.handleStaffType = function(type) {
        const notice    = document.getElementById('nonTeachingNotice');
        const container = document.getElementById('assignmentsContainer');
        const addBtn    = document.getElementById('addAssignmentBtn');
        if (type === 'teaching') {
            notice.style.display    = 'none';
            container.style.display = 'block';
            addBtn.style.display    = 'inline-block';
        } else {
            notice.style.display    = 'block';
            container.style.display = 'none';
            addBtn.style.display    = 'none';
        }
    };

    // Init staff type state from old() value
    const currentStaffType = document.getElementById('staffTypeSelect').value;
    if (currentStaffType) handleStaffType(currentStaffType);

    // ── Subject helpers ───────────────────────────────────────────────

    window.syncSubjectHi = function(select, idx) {
        const hi = select.options[select.selectedIndex]?.dataset.hi || '';
        const hiddenHi = document.getElementById(`subject-hi-${idx}`);
        if (hiddenHi) hiddenHi.value = hi;
    };

    window.loadSectionsForAssignment = async function(select, idx, restoreSection = null, restoreSubject = null) {
        const classId       = select.value;
        const sectionSelect = document.getElementById(`section-select-${idx}`);
        const subjectSelect = document.getElementById(`subject-select-${idx}`);

        sectionSelect.innerHTML = '<option value="">All</option>';
        if (subjectSelect) subjectSelect.innerHTML = '<option value="">— Select Subject —</option>';
        if (!classId) return;

        try {
            const sections = await (await fetch(`/classes/${classId}/sections`)).json();
            sections.forEach(s => {
                const opt = new Option(s.name, s.id);
                if (restoreSection && s.id == restoreSection) opt.selected = true;
                sectionSelect.add(opt);
            });
        } catch(e) { console.warn('Sections load failed', e); }

        if (subjectSelect) {
            try {
                const subjects = await (await fetch(`/classes/${classId}/subjects`)).json();
                subjects.forEach(s => {
                    const opt      = new Option(s.subject_name + (s.subject_name_hi ? ' · ' + s.subject_name_hi : ''), s.subject_name);
                    opt.dataset.hi = s.subject_name_hi || '';
                    if (restoreSubject && s.subject_name === restoreSubject) {
                        opt.selected = true;
                        const hi = document.getElementById(`subject-hi-${idx}`);
                        if (hi) hi.value = s.subject_name_hi || '';
                    }
                    subjectSelect.add(opt);
                });
            } catch(e) { console.warn('Subjects load failed', e); }
        }
    };

    let assignmentCount = 1;
    const classOptions  = [
        '<option value="">— Select Class —</option>',
        @foreach($classes as $class)
            `<option value="{{ $class->id }}">{{ $class->name }}</option>`,
        @endforeach
    ].join('');

    window.addAssignmentRow = function() {
        const idx = assignmentCount++;
        const row = document.createElement('div');
        row.className = 'assignment-row border rounded p-3 mb-3';
        row.innerHTML = `
            <div class="row g-3 align-items-end">
                <div class="col-sm-4">
                    <label class="form-label fw-semibold small">Class</label>
                    <select name="assignments[${idx}][class_id]" class="form-select form-select-sm"
                            onchange="loadSectionsForAssignment(this, ${idx})">
                        ${classOptions}
                    </select>
                </div>
                <div class="col-sm-3">
                    <label class="form-label fw-semibold small">Section</label>
                    <select name="assignments[${idx}][section_id]" class="form-select form-select-sm" id="section-select-${idx}">
                        <option value="">All</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <label class="form-label fw-semibold small">Subject</label>
                    <select name="assignments[${idx}][subject_name]" id="subject-select-${idx}"
                            class="form-select form-select-sm" onchange="syncSubjectHi(this, ${idx})">
                        <option value="">— Select Subject —</option>
                    </select>
                    <input type="hidden" name="assignments[${idx}][subject_name_hi]" id="subject-hi-${idx}">
                </div>
                <div class="col-sm-1">
                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger mt-4"
                            onclick="this.closest('.assignment-row').remove()">
                        <i class="icon-base ti tabler-trash"></i>
                    </button>
                </div>
            </div>`;
        document.getElementById('assignmentsContainer').appendChild(row);
    };

    // ── PAN uppercase ─────────────────────────────────────────────────
    document.getElementById('panNumber')?.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // ── Flatpickr ─────────────────────────────────────────────────────
    flatpickr('#staffDob', {
        dateFormat:  'Y-m-d',
        altInput:    true,
        altFormat:   'd M Y',
        maxDate:     'today',
        allowInput:  false,
        defaultDate: '{{ old('date_of_birth') }}' || null,
    });

    flatpickr('#staffJoiningDate', {
        dateFormat:  'Y-m-d',
        altInput:    true,
        altFormat:   'd M Y',
        allowInput:  false,
        defaultDate: '{{ old('joining_date') }}' || null,
    });

    // ── Jump to error step on server validation fail ──────────────────
    @if($errors->any())
        const stepOrder    = ['step-basic','step-personal','step-professional','step-address','step-subjects','step-documents'];
        const serverErrMap = {
            employee_code: 'step-basic', staff_type: 'step-basic',
            first_name: 'step-personal', last_name: 'step-personal', gender: 'step-personal',
        };
        const errorKeys = @json(array_keys($errors->toArray()));
        for (const key of errorKeys) {
            if (serverErrMap[key]) {
                stepper.to(stepOrder.indexOf(serverErrMap[key]) + 1);
                break;
            }
        }
    @endif

});
</script>
@endpush

@endsection