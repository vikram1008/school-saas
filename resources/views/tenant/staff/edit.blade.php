@php
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.tenant')

@section('title', 'Edit ' . $staff->full_name)

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

    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('tenant.staff.show', $staff) }}"
           class="btn btn-icon btn-outline-secondary me-3">
            <i class="icon-base ti tabler-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">Edit Staff / स्टाफ संपादित करें</h4>
            <p class="text-muted small mb-0">
                {{ $staff->full_name }} · <strong>{{ $staff->employee_code }}</strong>
            </p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-4">
            <i class="icon-base ti tabler-alert-circle me-1"></i>
            <strong>Please fix errors:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('tenant.staff.update', $staff) }}"
          method="POST"
          enctype="multipart/form-data"
          id="staffEditForm"
          novalidate>
        @csrf @method('PUT')

        <div class="bs-stepper vertical wizard-modern wizard-modern-vertical">
            <div class="bs-stepper-header">
                @foreach([
                    ['step-personal',     'tabler-user',       'Personal',    'Name & Identity'],
                    ['step-professional', 'tabler-briefcase',  'Professional','Role & Salary'],
                    ['step-address',      'tabler-map-pin',    'Address',     'Permanent & Current'],
                    ['step-subjects',     'tabler-book',       'Subjects',    'Teaching assignments'],
                    ['step-documents',    'tabler-file',       'Documents',   'Upload certificates'],
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

                {{-- STEP 1: Personal --}}
                <div id="step-personal" class="content dstepper-block active show">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Personal Information / व्यक्तिगत जानकारी</h6>
                        <small class="text-muted">Fields marked <span class="text-danger">*</span> are required.</small>
                    </div>
                    <div class="row g-4">
                        {{-- Photo --}}
                        <div class="col-12 text-center">
                            @if($staff->photo)
                                <img id="photoPreview"
                                     src="{{ Storage::url($staff->photo) }}"
                                     class="rounded-circle mb-2" width="90" height="90"
                                     style="object-fit:cover; border:3px solid #eee;">
                            @else
                                <img id="photoPreview"
                                     src="{{ asset('assets/img/avatars/1.png') }}"
                                     class="rounded-circle mb-2" width="90" height="90"
                                     style="object-fit:cover; border:3px solid #eee;">
                            @endif
                            <div>
                                <input type="file" name="photo"
                                       class="form-control form-control-sm d-inline-block"
                                       style="width:auto" accept="image/*"
                                       onchange="previewPhoto(this)">
                                <div class="form-text">Leave empty to keep current photo.</div>
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
                                   value="{{ old('first_name', $staff->first_name) }}"
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
                            <input type="text" name="first_name_hi" class="form-control"
                                   value="{{ old('first_name_hi', $staff->first_name_hi) }}"
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
                                   value="{{ old('last_name', $staff->last_name) }}"
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
                            <input type="text" name="last_name_hi" class="form-control"
                                   value="{{ old('last_name_hi', $staff->last_name_hi) }}"
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
                                @foreach(['male'=>'Male / पुरुष','female'=>'Female / महिला','other'=>'Other / अन्य'] as $val=>$lbl)
                                    <option value="{{ $val }}" {{ old('gender',$staff->gender)==$val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                @error('gender'){{ $message }}@else Please select gender.@enderror
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Date of Birth</label>
                            <input type="text"
                                   name="date_of_birth"
                                   id="staffEditDob"
                                   class="form-control"
                                   placeholder="Select date"
                                   value="{{ old('date_of_birth', $staff->date_of_birth?->format('Y-m-d')) }}"
                                   autocomplete="off" readonly>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Blood Group</label>
                            <select name="blood_group" class="form-select">
                                <option value="">— Select —</option>
                                @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group',$staff->blood_group)==$bg ? 'selected' : '' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Aadhaar Number</label>
                            <input type="text" name="aadhaar_number" class="form-control"
                                   value="{{ old('aadhaar_number', $staff->aadhaar_number) }}"
                                   placeholder="12-digit" maxlength="12" inputmode="numeric">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">PAN Number</label>
                            <input type="text" name="pan_number" id="panNumber" class="form-control"
                                   value="{{ old('pan_number', $staff->pan_number) }}"
                                   placeholder="e.g. ABCDE1234F"
                                   style="text-transform:uppercase" maxlength="10">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">ID Proof Type</label>
                            <select name="id_proof_type" class="form-select">
                                <option value="">— Select —</option>
                                @foreach(['aadhaar'=>'Aadhaar','pan'=>'PAN Card','voter_id'=>'Voter ID','passport'=>'Passport','driving_license'=>'Driving License'] as $val=>$lbl)
                                    <option value="{{ $val }}" {{ old('id_proof_type',$staff->id_proof_type)==$val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">ID Proof Number</label>
                            <input type="text" name="id_proof_number" class="form-control"
                                   value="{{ old('id_proof_number', $staff->id_proof_number) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Mobile</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone', $staff->phone) }}"
                                   placeholder="10-digit mobile" maxlength="15">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">WhatsApp</label>
                            <input type="text" name="whatsapp" class="form-control"
                                   value="{{ old('whatsapp', $staff->whatsapp) }}"
                                   maxlength="15">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Emergency Contact Name</label>
                            <input type="text" name="emergency_contact_name" class="form-control"
                                   value="{{ old('emergency_contact_name', $staff->emergency_contact_name) }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Emergency Contact Phone</label>
                            <input type="text" name="emergency_contact_phone" class="form-control"
                                   value="{{ old('emergency_contact_phone', $staff->emergency_contact_phone) }}">
                        </div>

                        <div class="col-12 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary btn-next"
                                    data-step="step-personal">
                                Next <i class="icon-base ti tabler-arrow-right icon-xs ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 2: Professional --}}
                <div id="step-professional" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Professional Information / व्यावसायिक जानकारी</h6>
                    </div>
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Designation / पद</label>
                            <div class="row g-1">
                                <div class="col-7">
                                    <input type="text" name="designation" class="form-control"
                                           value="{{ old('designation', $staff->designation) }}"
                                           data-hindi-target="[name='designation_hi']">
                                </div>
                                <div class="col-5">
                                    <input type="text" name="designation_hi" class="form-control"
                                           value="{{ old('designation_hi', $staff->designation_hi) }}"
                                           placeholder="हिंदी में">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Department / विभाग</label>
                            <div class="row g-1">
                                <div class="col-7">
                                    <input type="text" name="department" class="form-control"
                                           value="{{ old('department', $staff->department) }}"
                                           data-hindi-target="[name='department_hi']">
                                </div>
                                <div class="col-5">
                                    <input type="text" name="department_hi" class="form-control"
                                           value="{{ old('department_hi', $staff->department_hi) }}"
                                           placeholder="हिंदी में">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Qualification / योग्यता</label>
                            <div class="row g-1">
                                <div class="col-7">
                                    <input type="text" name="qualification" class="form-control"
                                           value="{{ old('qualification', $staff->qualification) }}"
                                           data-hindi-target="[name='qualification_hi']">
                                </div>
                                <div class="col-5">
                                    <input type="text" name="qualification_hi" class="form-control"
                                           value="{{ old('qualification_hi', $staff->qualification_hi) }}"
                                           placeholder="हिंदी में">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label fw-semibold">Experience (Years)</label>
                            <input type="number" name="experience_years" class="form-control"
                                   value="{{ old('experience_years', $staff->experience_years) }}"
                                   min="0" max="50">
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label fw-semibold">Employment Type</label>
                            <select name="employment_type" class="form-select">
                                @foreach(['full_time'=>'Full Time','part_time'=>'Part Time','contract'=>'Contract','substitute'=>'Substitute'] as $val=>$lbl)
                                    <option value="{{ $val }}"
                                        {{ old('employment_type',$staff->employment_type)==$val ? 'selected' : '' }}>
                                        {{ $lbl }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Joining Date</label>
                            <input type="text"
                                   name="joining_date"
                                   id="staffEditJoining"
                                   class="form-control"
                                   placeholder="Select joining date"
                                   value="{{ old('joining_date', $staff->joining_date?->format('Y-m-d')) }}"
                                   autocomplete="off" readonly>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Monthly Salary (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="salary" class="form-control"
                                       value="{{ old('salary', $staff->salary) }}"
                                       step="0.01" min="0">
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

                {{-- STEP 3: Address --}}
                <div id="step-address" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Address / पता</h6>
                    </div>
                    @php $addr = $staff->address; @endphp
                    <div class="row g-4">
                        <div class="col-12">
                            <p class="fw-semibold text-primary mb-2 border-bottom pb-1">Permanent Address / स्थायी पता</p>
                        </div>
                        @foreach([
                            ['perm_house_no','perm_house_no_hi','House No.','मकान नं.'],
                            ['perm_street','perm_street_hi','Street','गली'],
                            ['perm_village_city','perm_village_city_hi','Village/City','ग्राम/शहर'],
                            ['perm_tehsil','perm_tehsil_hi','Tehsil','तहसील'],
                            ['perm_district','perm_district_hi','District','जिला'],
                            ['perm_state','perm_state_hi','State','राज्य'],
                        ] as [$f,$fhi,$ph,$phhi])
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">{{ $ph }}</label>
                                <div class="row g-1">
                                    <div class="col-6">
                                        <input type="text" name="{{ $f }}"
                                               class="form-control form-control-sm"
                                               value="{{ old($f, $addr?->{$f}) }}"
                                               placeholder="{{ $ph }}"
                                               data-hindi-target="[name='{{ $fhi }}']">
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
                                   placeholder="6-digit PIN" maxlength="6" inputmode="numeric">
                        </div>

                        <div class="col-12 mt-2">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-1">
                                <p class="fw-semibold text-primary mb-0">Current Address</p>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox"
                                           name="same_as_permanent" id="sameAsPermanent" value="1"
                                           {{ old('same_as_permanent', $addr?->same_as_permanent ?? true) ? 'checked' : '' }}
                                           onchange="toggleCurrAddress(this)">
                                    <label class="form-check-label small" for="sameAsPermanent">
                                        Same as Permanent
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div id="currAddressFields" class="col-12"
                             style="{{ old('same_as_permanent', $addr?->same_as_permanent ?? true) ? 'display:none' : '' }}">
                            <div class="row g-3">
                                @foreach([
                                    ['curr_house_no','House No.'],['curr_street','Street'],
                                    ['curr_village_city','Village/City'],['curr_tehsil','Tehsil'],
                                    ['curr_district','District'],['curr_state','State'],['curr_pincode','PIN'],
                                ] as [$field,$label])
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
                                <i class="icon-base ti tabler-arrow-left icon-xs me-1"></i> Previous
                            </button>
                            <button type="button" class="btn btn-primary btn-next"
                                    data-step="step-address">
                                Next <i class="icon-base ti tabler-arrow-right icon-xs ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 4: Subjects --}}
                <div id="step-subjects" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Subject Assignments / विषय असाइनमेंट</h6>
                        <small class="text-muted">Only for teaching staff. Existing assignments will be replaced.</small>
                    </div>
                    @if($staff->staff_type !== 'teaching')
                        <div class="alert alert-info small">
                            <i class="icon-base ti tabler-info-circle me-1"></i>
                            Subject assignments are only for teaching staff.
                        </div>
                    @else
                        <div id="assignmentsContainer">
                            @forelse($staff->subjectAssignments as $i => $assignment)
                                <div class="assignment-row border rounded p-3 mb-3">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-sm-4">
                                            <label class="form-label fw-semibold small">Class</label>
                                            <select name="assignments[{{ $i }}][class_id]"
                                                    class="form-select form-select-sm"
                                                    onchange="loadSectionsForAssignment(this, {{ $i }})">
                                                <option value="">— Select Class —</option>
                                                @foreach($classes as $class)
                                                    <option value="{{ $class->id }}"
                                                        {{ $assignment->class_id == $class->id ? 'selected' : '' }}>
                                                        {{ $class->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-3">
                                            <label class="form-label fw-semibold small">Section</label>
                                            <select name="assignments[{{ $i }}][section_id]"
                                                    class="form-select form-select-sm"
                                                    id="section-select-{{ $i }}">
                                                <option value="">All</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="form-label fw-semibold small">Subject</label>
                                            <select name="assignments[{{ $i }}][subject_name]"
                                                    id="subject-select-{{ $i }}"
                                                    class="form-select form-select-sm"
                                                    data-selected="{{ $assignment->subject_name }}"
                                                    onchange="syncSubjectHi(this, {{ $i }})">
                                                <option value="">— Select Subject —</option>
                                            </select>
                                            <input type="hidden"
                                                   name="assignments[{{ $i }}][subject_name_hi]"
                                                   id="subject-hi-{{ $i }}"
                                                   value="{{ $assignment->subject_name_hi }}">
                                        </div>
                                        <div class="col-sm-1">
                                            <button type="button"
                                                    class="btn btn-sm btn-icon btn-outline-danger mt-4"
                                                    onclick="this.closest('.assignment-row').remove()">
                                                <i class="icon-base ti tabler-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
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
                                                    data-selected=""
                                                    onchange="syncSubjectHi(this, 0)">
                                                <option value="">— Select Subject —</option>
                                            </select>
                                            <input type="hidden"
                                                   name="assignments[0][subject_name_hi]"
                                                   id="subject-hi-0">
                                        </div>
                                        <div class="col-sm-1"></div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2"
                                onclick="addAssignmentRow()">
                            <i class="icon-base ti tabler-plus me-1"></i> Add Another Subject
                        </button>
                    @endif

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

                {{-- STEP 5: Documents --}}
                <div id="step-documents" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Documents / दस्तावेज़</h6>
                        <small class="text-muted">Upload new file to replace existing. All optional.</small>
                    </div>
                    <div class="row g-3">
                        @foreach(\App\Models\StaffDocument::typeLabels() as $type => $label)
                            @php $existing = $staff->documents->firstWhere('document_type', $type); @endphp
                            <div class="col-md-6">
                                <div class="border rounded p-3 {{ $existing ? 'border-success' : '' }}">
                                    <label class="form-label fw-semibold small mb-1 d-block">
                                        {{ $label }}
                                        @if($existing)
                                            <span class="badge bg-label-success ms-1">
                                                <i class="icon-base ti tabler-check"></i> Uploaded
                                            </span>
                                        @endif
                                    </label>
                                    @if($existing)
                                        <div class="mb-2 d-flex align-items-center gap-2">
                                            <span class="text-muted small">{{ $existing->original_name }}</span>
                                            <a href="{{ Storage::url($existing->file_path) }}"
                                               target="_blank" class="btn btn-xs btn-outline-primary">
                                                <i class="icon-base ti tabler-eye"></i>
                                            </a>
                                        </div>
                                    @endif
                                    <input type="file" name="documents[{{ $type }}]"
                                           class="form-control form-control-sm"
                                           accept=".pdf,.jpg,.jpeg,.png">
                                    @if($existing)
                                        <div class="form-text">Upload new file to replace</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-label-secondary btn-prev">
                            <i class="icon-base ti tabler-arrow-left icon-xs me-1"></i> Previous
                        </button>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="icon-base ti tabler-device-floppy me-2"></i>
                            Save Changes
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

    // ── Stepper init ──────────────────────────────────────────────────
    const stepper = new Stepper(document.querySelector('.bs-stepper'), {
        linear: false,
        animation: false
    });
    stepper.to(1);

    // ── Validation rules per step ─────────────────────────────────────
    const stepValidations = {
        'step-personal': [
            { id: 'first_name', label: 'First Name', type: 'input',  msg: 'First name is required.' },
            { id: 'last_name',  label: 'Last Name',  type: 'input',  msg: 'Last name is required.' },
            { id: 'gender',     label: 'Gender',     type: 'select', msg: 'Please select gender.' },
        ],
    };

    // ── Core validation function ──────────────────────────────────────
    function validateStep(stepId) {
        const rules  = stepValidations[stepId] || [];
        const errors = [];
        let firstInvalid = null;

        rules.forEach(rule => {
            const field = document.getElementById(rule.id);
            if (!field) return;

            const isEmpty = field.tagName === 'SELECT'
                ? field.value === ''
                : field.value.trim() === '';

            if (isEmpty) {
                field.classList.add('is-invalid');
                field.classList.remove('is-valid');
                let fb = field.parentElement.querySelector('.invalid-feedback');
                if (fb) fb.textContent = rule.msg;
                errors.push(rule.msg);
                if (!firstInvalid) firstInvalid = field;
            } else {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
            }
        });

        if (errors.length > 0) {
            if (firstInvalid) firstInvalid.focus();
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

    // ── Live validation on field change ───────────────────────────────
    Object.entries(stepValidations).forEach(([stepId, rules]) => {
        rules.forEach(rule => {
            const field = document.getElementById(rule.id);
            if (!field) return;
            const evt = field.tagName === 'SELECT' ? 'change' : 'input';
            field.addEventListener(evt, function () {
                const isEmpty = field.tagName === 'SELECT'
                    ? this.value === '' : this.value.trim() === '';
                if (!isEmpty) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                }
            });
        });
    });

    // ── Next button ───────────────────────────────────────────────────
    document.querySelectorAll('.btn-next').forEach(btn => {
        btn.addEventListener('click', function () {
            if (validateStep(this.dataset.step)) stepper.next();
        });
    });

    // ── Prev button ───────────────────────────────────────────────────
    document.querySelectorAll('.btn-prev').forEach(btn => {
        btn.addEventListener('click', () => stepper.previous());
    });

    // ── Submit → validate all before sending ──────────────────────────
    document.getElementById('staffEditForm').addEventListener('submit', function (e) {
        const stepOrder = ['step-personal','step-professional','step-address','step-subjects','step-documents'];
        const allErrors = [];
        let firstFailedStep = null;

        Object.entries(stepValidations).forEach(([stepId, rules]) => {
            rules.forEach(rule => {
                const field = document.getElementById(rule.id);
                if (!field) return;
                const isEmpty = field.tagName === 'SELECT'
                    ? field.value === '' : field.value.trim() === '';
                if (isEmpty) {
                    field.classList.add('is-invalid');
                    allErrors.push({ stepId, msg: rule.msg });
                    if (!firstFailedStep) firstFailedStep = stepId;
                }
            });
        });

        if (allErrors.length > 0) {
            e.preventDefault();
            if (firstFailedStep) {
                stepper.to(stepOrder.indexOf(firstFailedStep) + 1);
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

    // ── Utilities ─────────────────────────────────────────────────────
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

    document.getElementById('panNumber')?.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // ── Flatpickr ─────────────────────────────────────────────────────
    flatpickr('#staffEditDob', {
        dateFormat:  'Y-m-d',
        altInput:    true,
        altFormat:   'd M Y',
        maxDate:     'today',
        allowInput:  false,
        defaultDate: '{{ old('date_of_birth', $staff->date_of_birth?->format('Y-m-d')) }}' || null,
    });

    flatpickr('#staffEditJoining', {
        dateFormat:  'Y-m-d',
        altInput:    true,
        altFormat:   'd M Y',
        allowInput:  false,
        defaultDate: '{{ old('joining_date', $staff->joining_date?->format('Y-m-d')) }}' || null,
    });

    // ── Subject helpers ───────────────────────────────────────────────
    window.syncSubjectHi = function(select, idx) {
        const hi = select.options[select.selectedIndex]?.dataset.hi || '';
        const h  = document.getElementById(`subject-hi-${idx}`);
        if (h) h.value = hi;
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
                        const h = document.getElementById(`subject-hi-${idx}`);
                        if (h) h.value = s.subject_name_hi || '';
                    }
                    subjectSelect.add(opt);
                });
            } catch(e) { console.warn('Subjects load failed', e); }
        }
    };

    // Restore existing assignments on load
    @foreach($staff->subjectAssignments as $i => $assignment)
        @if($assignment->class_id)
        (async () => {
            const sel = document.querySelector(`[name="assignments[{{ $i }}][class_id]"]`);
            if (sel) {
                await loadSectionsForAssignment(
                    sel, {{ $i }},
                    '{{ $assignment->section_id ?? '' }}',
                    '{{ addslashes($assignment->subject_name) }}'
                );
            }
        })();
        @endif
    @endforeach

    let assignmentCount = {{ max($staff->subjectAssignments->count(), 1) }};
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

    // ── Jump to error step on server validation fail ──────────────────
    @if($errors->any())
        const stepOrder    = ['step-personal','step-professional','step-address','step-subjects','step-documents'];
        const serverErrMap = {
            first_name: 'step-personal',
            last_name:  'step-personal',
            gender:     'step-personal',
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