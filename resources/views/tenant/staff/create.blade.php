@extends('layouts.tenant')

@section('title', 'Add Staff')

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
        <a href="{{ route('tenant.staff.index') }}"
           class="btn btn-icon btn-outline-secondary me-3">
            <i class="icon-base ti tabler-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">Add Staff / नया स्टाफ जोड़ें</h4>
            <p class="text-muted small mb-0">
                <span class="text-danger">*</span> Required &nbsp;|&nbsp;
                <span class="badge bg-label-warning">हिं</span> = Hindi field
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
          enctype="multipart/form-data">
        @csrf

        <div class="bs-stepper vertical wizard-modern wizard-modern-vertical">

            {{-- Stepper Header --}}
            <div class="bs-stepper-header">
                @foreach([
                    ['step-basic',    'tabler-clipboard-list', 'Basic Info',    'Employee code & type'],
                    ['step-personal', 'tabler-user',           'Personal',      'Name, DOB, Identity'],
                    ['step-professional', 'tabler-briefcase',  'Professional',  'Designation & Salary'],
                    ['step-address',  'tabler-map-pin',        'Address / पता', 'Permanent & Current'],
                    ['step-subjects', 'tabler-book',           'Subjects',      'Teaching assignments'],
                    ['step-documents','tabler-file',           'Documents',     'Upload certificates'],
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
                        <small>Employee code, staff type and login details.</small>
                    </div>
                    <div class="row g-4">
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">
                                Employee Code <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="employee_code"
                                   class="form-control @error('employee_code') is-invalid @enderror"
                                   value="{{ old('employee_code') }}"
                                   placeholder="e.g. EMP-001"
                                   data-required="true">
                            @error('employee_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Default login password = employee code
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">
                                Staff Type <span class="text-danger">*</span>
                            </label>
                            <select name="staff_type" class="form-select"
                                    id="staffTypeSelect"
                                    data-required="true"
                                    onchange="handleStaffType(this.value)">
                                <option value="">Select Type</option>
                                @foreach(\App\Models\StaffProfile::typeLabels() as $val => $label)
                                    <option value="{{ $val }}"
                                        {{ old('staff_type') == $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Login Email (Optional)</label>
                            <input type="email" name="email"
                                   class="form-control"
                                   value="{{ old('email') }}"
                                   placeholder="Auto-generated if empty">
                            <div class="form-text">Leave blank to auto-generate</div>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary btn-next"
                                    data-step="step-basic">
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
                                <input type="file" name="photo"
                                       class="form-control form-control-sm d-inline-block"
                                       style="width:auto" accept="image/*"
                                       onchange="previewPhoto(this)">
                                <div class="form-text">Photo — Max 2MB</div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                First Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="first_name"
                                   class="form-control @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name') }}"
                                   data-required="true">
                            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                प्रथम नाम <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="first_name_hi"
                                   class="form-control"
                                   value="{{ old('first_name_hi') }}"
                                   placeholder="हिंदी में">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                Last Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="last_name"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name') }}"
                                   data-required="true">
                            @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                उपनाम <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="last_name_hi"
                                   class="form-control"
                                   value="{{ old('last_name_hi') }}"
                                   placeholder="हिंदी में">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">
                                Gender <span class="text-danger">*</span>
                            </label>
                            <select name="gender" class="form-select" data-required="true">
                                <option value="">Select</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male / पुरुष</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female / महिला</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" name="date_of_birth"
                                   class="form-control"
                                   value="{{ old('date_of_birth') }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Blood Group</label>
                            <select name="blood_group" class="form-select">
                                <option value="">Select</option>
                                @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Aadhaar Number</label>
                            <input type="text" name="aadhaar_number"
                                   class="form-control"
                                   value="{{ old('aadhaar_number') }}"
                                   placeholder="12-digit" maxlength="12">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">PAN Number</label>
                            <input type="text" name="pan_number"
                                   id="panNumber"
                                   class="form-control"
                                   value="{{ old('pan_number') }}"
                                   placeholder="e.g. ABCDE1234F"
                                   maxlength="10"
                                   style="text-transform:uppercase">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">ID Proof Type</label>
                            <select name="id_proof_type" class="form-select">
                                <option value="">Select</option>
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

                        {{-- Contact --}}
                        <div class="col-12"><hr class="my-1"><small class="fw-semibold text-muted">Contact</small></div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Mobile</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone') }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">WhatsApp</label>
                            <input type="text" name="whatsapp" class="form-control"
                                   value="{{ old('whatsapp') }}">
                        </div>

                        {{-- Emergency --}}
                        <div class="col-12"><hr class="my-1"><small class="fw-semibold text-muted">Emergency Contact</small></div>
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
                                           id="designationInput">
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
                                           placeholder="e.g. Science, Administration">
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
                                           placeholder="e.g. B.Ed, M.Sc, MBA">
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
                            <input type="date" name="joining_date"
                                   class="form-control"
                                   value="{{ old('joining_date') }}">
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
                                <i class="icon-base ti tabler-arrow-left icon-xs me-2"></i>Previous
                            </button>
                            <button type="button" class="btn btn-primary btn-next"
                                    data-step="step-professional">
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
                                               placeholder="{{ $ph }}">
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
                                   maxlength="6">
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

                {{-- STEP 5: Subject Assignments --}}
                <div id="step-subjects" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Subject Assignments / विषय असाइनमेंट</h6>
                        <small>Only applicable for teaching staff.</small>
                    </div>
                    <div id="subjectAssignmentSection">
                        <div class="alert alert-info small mb-3" id="nonTeachingNotice"
                             style="display:none;">
                            <i class="icon-base ti tabler-info-circle me-1"></i>
                            Subject assignments are only for teaching staff.
                        </div>
                        <div id="assignmentsContainer">
                            <div class="assignment-row border rounded p-3 mb-3">
                                <div class="row g-3 align-items-end">
                                    <div class="col-sm-3">
                                        <label class="form-label fw-semibold small">Class</label>
                                        <select name="assignments[0][class_id]"
                                                class="form-select form-select-sm class-select"
                                                onchange="loadSectionsForAssignment(this, 0)">
                                            <option value="">Select Class</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-label fw-semibold small">Section</label>
                                        <select name="assignments[0][section_id]"
                                                class="form-select form-select-sm section-select"
                                                id="section-select-0">
                                            <option value="">All</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label fw-semibold small">Subject (English)</label>
                                        <input type="text"
                                               name="assignments[0][subject_name]"
                                               class="form-control form-control-sm"
                                               placeholder="e.g. Mathematics">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label fw-semibold small">
                                            विषय <span class="badge bg-label-warning" style="font-size:9px">हिं</span>
                                        </label>
                                        <input type="text"
                                               name="assignments[0][subject_name_hi]"
                                               class="form-control form-control-sm"
                                               placeholder="जैसे: गणित">
                                    </div>
                                    <div class="col-sm-1">
                                        <button type="button"
                                                class="btn btn-sm btn-icon btn-outline-danger remove-assignment"
                                                style="display:none;">
                                            <i class="icon-base ti tabler-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button"
                                class="btn btn-outline-primary btn-sm"
                                id="addAssignmentBtn"
                                onclick="addAssignmentRow()">
                            <i class="icon-base ti tabler-plus me-1"></i>
                            Add Another Subject
                        </button>
                    </div>

                    <div class="col-12 d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-label-secondary btn-prev">
                            <i class="icon-base ti tabler-arrow-left icon-xs me-2"></i>Previous
                        </button>
                        <button type="button" class="btn btn-primary btn-next"
                                data-step="step-subjects">
                            <span class="me-2">Next</span>
                            <i class="icon-base ti tabler-arrow-right icon-xs"></i>
                        </button>
                    </div>
                </div>

                {{-- STEP 6: Documents --}}
                <div id="step-documents" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Documents / दस्तावेज़</h6>
                        <small>Upload PDF, JPG or PNG. Max 2MB each.</small>
                    </div>
                    <div class="row g-3">
                        @foreach(\App\Models\StaffDocument::typeLabels() as $type => $label)
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <label class="form-label fw-semibold small mb-2 d-block">
                                        {{ $label }}
                                    </label>
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
                            <i class="icon-base ti tabler-arrow-left icon-xs me-2"></i>Previous
                        </button>
                        <button type="submit" class="btn btn-success btn-lg">
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
    const stepper = new Stepper(document.querySelector('.bs-stepper'), {
        linear: false, animation: false
    });
    stepper.to(1);

    const stepRequired = {
        'step-basic':    ['employee_code', 'staff_type'],
        'step-personal': ['first_name', 'last_name', 'gender'],
    };

    function validateStep(stepId) {
        const required = stepRequired[stepId] || [];
        let valid = true;
        required.forEach(name => {
            const field = document.querySelector(`[name="${name}"]`);
            if (!field || !field.value.trim()) {
                if (field) field.classList.add('is-invalid');
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
        const notice = document.getElementById('nonTeachingNotice');
        const container = document.getElementById('assignmentsContainer');
        const addBtn = document.getElementById('addAssignmentBtn');
        if (type === 'teaching') {
            notice.style.display = 'none';
            container.style.display = 'block';
            addBtn.style.display = 'inline-block';
        } else {
            notice.style.display = 'block';
            container.style.display = 'none';
            addBtn.style.display = 'none';
        }
    };

    // Initialize based on old value
    const staffType = document.getElementById('staffTypeSelect').value;
    if (staffType) handleStaffType(staffType);

    let assignmentCount = 1;
    const classOptions = `@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach`;

    window.addAssignmentRow = function() {
        const idx = assignmentCount++;
        const row = document.createElement('div');
        row.className = 'assignment-row border rounded p-3 mb-3';
        row.innerHTML = `
            <div class="row g-3 align-items-end">
                <div class="col-sm-3">
                    <label class="form-label fw-semibold small">Class</label>
                    <select name="assignments[${idx}][class_id]"
                            class="form-select form-select-sm class-select"
                            onchange="loadSectionsForAssignment(this, ${idx})">
                        <option value="">Select Class</option>
                        ${classOptions}
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label fw-semibold small">Section</label>
                    <select name="assignments[${idx}][section_id]"
                            class="form-select form-select-sm"
                            id="section-select-${idx}">
                        <option value="">All</option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <label class="form-label fw-semibold small">Subject (English)</label>
                    <input type="text" name="assignments[${idx}][subject_name]"
                           class="form-control form-control-sm" placeholder="e.g. Mathematics">
                </div>
                <div class="col-sm-3">
                    <label class="form-label fw-semibold small">विषय</label>
                    <input type="text" name="assignments[${idx}][subject_name_hi]"
                           class="form-control form-control-sm" placeholder="जैसे: गणित">
                </div>
                <div class="col-sm-1">
                    <button type="button"
                            class="btn btn-sm btn-icon btn-outline-danger remove-assignment"
                            onclick="this.closest('.assignment-row').remove()">
                        <i class="icon-base ti tabler-trash"></i>
                    </button>
                </div>
            </div>`;
        document.getElementById('assignmentsContainer').appendChild(row);
    };

    window.loadSectionsForAssignment = async function(select, idx) {
        const classId = select.value;
        const sectionSelect = document.getElementById(`section-select-${idx}`);
        sectionSelect.innerHTML = '<option value="">All</option>';
        if (!classId) return;
        const res = await fetch(`/classes/${classId}/sections`);
        const sections = await res.json();
        sections.forEach(s => {
            sectionSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`;
        });
    };

    document.getElementById('panNumber')?.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    @if($errors->any())
        const stepMap = { 'employee_code': 0, 'staff_type': 0, 'first_name': 1, 'last_name': 1, 'gender': 1 };
        const errorKeys = @json(array_keys($errors->toArray()));
        for (const key of errorKeys) {
            if (stepMap[key] !== undefined) { stepper.to(stepMap[key] + 1); break; }
        }
    @endif
});
</script>
@endpush

@endsection