@php
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.tenant')

@section('title', 'Edit ' . $staff->full_name)

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
          enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="bs-stepper vertical wizard-modern wizard-modern-vertical">
            <div class="bs-stepper-header">
                @foreach([
                    ['step-personal',     'tabler-user',       'Personal',   'Name & Identity'],
                    ['step-professional', 'tabler-briefcase',  'Professional','Role & Salary'],
                    ['step-address',      'tabler-map-pin',    'Address',    'Permanent & Current'],
                    ['step-subjects',     'tabler-book',       'Subjects',   'Teaching assignments'],
                    ['step-documents',    'tabler-file',       'Documents',  'Upload certificates'],
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
                        <h6 class="mb-0">Personal Information</h6>
                    </div>
                    <div class="row g-4">
                        <div class="col-12 text-center">
                            @if($staff->photo)
                                <img id="photoPreview" src="{{ Storage::url($staff->photo) }}"
                                     class="rounded-circle mb-2" width="90" height="90"
                                     style="object-fit:cover; border:3px solid #eee;">
                            @else
                                <img id="photoPreview" src="{{ asset('assets/img/avatars/1.png') }}"
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
                            <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name"
                                   class="form-control @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name', $staff->first_name) }}"
                                   data-required="true">
                            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">प्रथम नाम <span class="badge bg-label-warning">हिं</span></label>
                            <input type="text" name="first_name_hi" class="form-control"
                                   value="{{ old('first_name_hi', $staff->first_name_hi) }}"
                                   placeholder="हिंदी में">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name', $staff->last_name) }}"
                                   data-required="true">
                            @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">उपनाम <span class="badge bg-label-warning">हिं</span></label>
                            <input type="text" name="last_name_hi" class="form-control"
                                   value="{{ old('last_name_hi', $staff->last_name_hi) }}"
                                   placeholder="हिंदी में">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" data-required="true">
                                <option value="">Select</option>
                                @foreach(['male'=>'Male / पुरुष','female'=>'Female / महिला','other'=>'Other'] as $val=>$lbl)
                                    <option value="{{ $val }}" {{ old('gender',$staff->gender)==$val?'selected':'' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Date of Birth</label>
                            <input type="text"
                                    name="date_of_birth"
                                    id="staffEditDob"
                                    class="form-control flatpickr-input"
                                    placeholder="Date of Birth"
                                    value="{{ old('date_of_birth', $staff->date_of_birth?->format('Y-m-d')) }}"
                                    autocomplete="off" readonly>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Blood Group</label>
                            <select name="blood_group" class="form-select">
                                <option value="">Select</option>
                                @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group',$staff->blood_group)==$bg?'selected':'' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Aadhaar</label>
                            <input type="text" name="aadhaar_number" class="form-control"
                                   value="{{ old('aadhaar_number', $staff->aadhaar_number) }}"
                                   maxlength="12">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">PAN</label>
                            <input type="text" name="pan_number" id="panNumber"
                                   class="form-control"
                                   value="{{ old('pan_number', $staff->pan_number) }}"
                                   style="text-transform:uppercase" maxlength="10">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">ID Proof Type</label>
                            <select name="id_proof_type" class="form-select">
                                <option value="">Select</option>
                                @foreach(['aadhaar'=>'Aadhaar','pan'=>'PAN Card','voter_id'=>'Voter ID','passport'=>'Passport','driving_license'=>'Driving License'] as $val=>$lbl)
                                    <option value="{{ $val }}" {{ old('id_proof_type',$staff->id_proof_type)==$val?'selected':'' }}>{{ $lbl }}</option>
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
                                   value="{{ old('phone', $staff->phone) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">WhatsApp</label>
                            <input type="text" name="whatsapp" class="form-control"
                                   value="{{ old('whatsapp', $staff->whatsapp) }}">
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
                                <span class="me-2">Next</span>
                                <i class="icon-base ti tabler-arrow-right icon-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 2: Professional --}}
                <div id="step-professional" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Professional Information</h6>
                    </div>
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Designation / पद</label>
                            <div class="row g-1">
                                <div class="col-7">
                                    <input type="text" name="designation" class="form-control"
                                           value="{{ old('designation', $staff->designation) }}">
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
                                           value="{{ old('department', $staff->department) }}">
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
                                           value="{{ old('qualification', $staff->qualification) }}">
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
                                   min="0">
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label fw-semibold">Employment Type</label>
                            <select name="employment_type" class="form-select">
                                @foreach(['full_time'=>'Full Time','part_time'=>'Part Time','contract'=>'Contract','substitute'=>'Substitute'] as $val=>$lbl)
                                    <option value="{{ $val }}"
                                        {{ old('employment_type',$staff->employment_type)==$val?'selected':'' }}>
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
                                    class="form-control flatpickr-input"
                                    placeholder="Joining Date"
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

                {{-- STEP 3: Address --}}
                <div id="step-address" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Address / पता</h6>
                    </div>
                    @php $addr = $staff->address; @endphp
                    <div class="row g-4">
                        <div class="col-12">
                            <p class="fw-semibold text-primary mb-2 border-bottom pb-1">Permanent Address</p>
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
                                   value="{{ old('perm_pincode', $addr?->perm_pincode) }}" maxlength="6">
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

                {{-- STEP 4: Subjects --}}
                <div id="step-subjects" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Subject Assignments</h6>
                        <small>Only for teaching staff. Existing assignments will be replaced.</small>
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
                                                <option value="">Select Class</option>
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
                                                <option value="">Select Subject</option>
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
                                                <option value="">Select Class</option>
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
                                                <option value="">Select Subject</option>
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
                            <i class="icon-base ti tabler-arrow-left icon-xs me-2"></i>Previous
                        </button>
                        <button type="button" class="btn btn-primary btn-next"
                                data-step="step-subjects">
                            <span class="me-2">Next</span>
                            <i class="icon-base ti tabler-arrow-right icon-xs"></i>
                        </button>
                    </div>
                </div>

                {{-- STEP 5: Documents --}}
                <div id="step-documents" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Documents / दस्तावेज़</h6>
                        <small>Upload new to replace existing documents.</small>
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
                                        <div class="form-text">Upload to replace</div>
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

    // ── Stepper ──────────────────────────────────────────────────────
    const stepper = new Stepper(document.querySelector('.bs-stepper'), {
        linear: false, animation: false
    });
    stepper.to(1);

    const stepRequired = {
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
        (stepRequired[stepId] || []).forEach(name => {
            const field = document.querySelector(`[name="${name}"]`);
            if (field) {
                field.addEventListener('input',  () => updateNextBtn(stepId));
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

    // ── Photo preview ────────────────────────────────────────────────
    window.previewPhoto = function(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => document.getElementById('photoPreview').src = e.target.result;
            reader.readAsDataURL(input.files[0]);
        }
    };

    // ── Address toggle ───────────────────────────────────────────────
    window.toggleCurrAddress = function(cb) {
        document.getElementById('currAddressFields').style.display =
            cb.checked ? 'none' : 'block';
    };

    // ── PAN uppercase ────────────────────────────────────────────────
    document.getElementById('panNumber')?.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // ── Flatpickr ────────────────────────────────────────────────────
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

    // ── Subject assignment helpers ────────────────────────────────────

    // Sync hidden hindi field when subject changes
    window.syncSubjectHi = function(select, idx) {
        const hi = select.options[select.selectedIndex]?.dataset.hi || '';
        const hiddenHi = document.getElementById(`subject-hi-${idx}`);
        if (hiddenHi) hiddenHi.value = hi;
    };

    // Load sections + subjects for a class, then restore selections
    window.loadSectionsForAssignment = async function(
        select, idx,
        restoreSection = null,
        restoreSubject = null
    ) {
        const classId       = select.value;
        const sectionSelect = document.getElementById(`section-select-${idx}`);
        const subjectSelect = document.getElementById(`subject-select-${idx}`);

        sectionSelect.innerHTML = '<option value="">All</option>';
        if (subjectSelect) {
            subjectSelect.innerHTML = '<option value="">Select Subject</option>';
        }
        if (!classId) return;

        // Load sections
        try {
            const secRes   = await fetch(`/classes/${classId}/sections`);
            const sections = await secRes.json();
            sections.forEach(s => {
                const opt      = document.createElement('option');
                opt.value      = s.id;
                opt.textContent = s.name;
                if (restoreSection && s.id == restoreSection) opt.selected = true;
                sectionSelect.appendChild(opt);
            });
        } catch(e) {
            console.warn('Section load failed', e);
        }

        // Load subjects from class_subjects
        if (subjectSelect) {
            try {
                const subRes  = await fetch(`/classes/${classId}/subjects`);
                const subjects = await subRes.json();
                subjects.forEach(s => {
                    const opt       = document.createElement('option');
                    opt.value       = s.subject_name;
                    opt.dataset.hi  = s.subject_name_hi || '';
                    opt.textContent = s.subject_name +
                        (s.subject_name_hi ? ' · ' + s.subject_name_hi : '');
                    if (restoreSubject && s.subject_name === restoreSubject) {
                        opt.selected = true;
                        // Also update hidden hi field
                        const hiddenHi = document.getElementById(`subject-hi-${idx}`);
                        if (hiddenHi) hiddenHi.value = s.subject_name_hi || '';
                    }
                    subjectSelect.appendChild(opt);
                });
            } catch(e) {
                console.warn('Subject load failed', e);
            }
        }
    };

    // ── Restore existing assignments on page load ─────────────────────
    @foreach($staff->subjectAssignments as $i => $assignment)
        @if($assignment->class_id)
        (async () => {
            const select = document.querySelector(
                `[name="assignments[{{ $i }}][class_id]"]`
            );
            if (select) {
                await loadSectionsForAssignment(
                    select,
                    {{ $i }},
                    '{{ $assignment->section_id ?? '' }}',
                    '{{ addslashes($assignment->subject_name) }}'
                );
            }
        })();
        @endif
    @endforeach

    // ── Dynamic add row ──────────────────────────────────────────────
    let assignmentCount = {{ max($staff->subjectAssignments->count(), 1) }};

    const classOptions = [
        '<option value="">Select Class</option>',
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
                    <select name="assignments[${idx}][class_id]"
                            class="form-select form-select-sm"
                            onchange="loadSectionsForAssignment(this, ${idx})">
                        ${classOptions}
                    </select>
                </div>
                <div class="col-sm-3">
                    <label class="form-label fw-semibold small">Section</label>
                    <select name="assignments[${idx}][section_id]"
                            class="form-select form-select-sm"
                            id="section-select-${idx}">
                        <option value="">All</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <label class="form-label fw-semibold small">Subject</label>
                    <select name="assignments[${idx}][subject_name]"
                            id="subject-select-${idx}"
                            class="form-select form-select-sm"
                            onchange="syncSubjectHi(this, ${idx})">
                        <option value="">Select Subject</option>
                    </select>
                    <input type="hidden"
                           name="assignments[${idx}][subject_name_hi]"
                           id="subject-hi-${idx}">
                </div>
                <div class="col-sm-1">
                    <button type="button"
                            class="btn btn-sm btn-icon btn-outline-danger mt-4"
                            onclick="this.closest('.assignment-row').remove()">
                        <i class="icon-base ti tabler-trash"></i>
                    </button>
                </div>
            </div>`;
        document.getElementById('assignmentsContainer').appendChild(row);
    };

});
</script>
@endpush

@endsection