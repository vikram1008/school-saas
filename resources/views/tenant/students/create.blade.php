@extends('layouts.tenant')

@section('title', 'Add Student')

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
        <a href="{{ route('tenant.students.index') }}"
           class="btn btn-icon btn-outline-secondary me-3">
            <i class="icon-base ti tabler-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">Add New Student / नया छात्र जोड़ें</h4>
            <p class="text-muted small mb-0">
                <span class="text-danger">*</span> Required fields &nbsp;|&nbsp;
                <span class="badge bg-label-warning">हिं</span> = Hindi field
            </p>
        </div>
    </div>

    {{-- Server-side errors (only visible after failed submission) --}}
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

    <form action="{{ route('tenant.students.store') }}"
          method="POST"
          enctype="multipart/form-data"
          id="studentCreateForm"
          novalidate>
        @csrf

        <div class="bs-stepper vertical wizard-modern wizard-modern-vertical">

            {{-- Stepper Header --}}
            <div class="bs-stepper-header">
                @foreach([
                    ['step-office',   'tabler-clipboard-list', 'Office Use / कार्यालय',  'Admission & Class Details'],
                    ['step-personal', 'tabler-user',           'Personal / व्यक्तिगत',   'Name, DOB, Identity'],
                    ['step-family',   'tabler-users',          'Family / परिवार',         'Father, Mother, Guardian'],
                    ['step-address',  'tabler-map-pin',        'Address / पता',           'Permanent & Correspondence'],
                    ['step-academic', 'tabler-school',         'Previous School',         'History & TC Details'],
                    ['step-bank',     'tabler-building-bank',  'Bank / बैंक',             'DBT & Scholarship'],
                    ['step-documents','tabler-file',           'Documents / दस्तावेज़',   'Upload Certificates'],
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

                {{-- STEP 1: Office Use --}}
                <div id="step-office" class="content dstepper-block active show">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Office Use Only / कार्यालय उपयोग</h6>
                        <small class="text-muted">Fields marked <span class="text-danger">*</span> are required.</small>
                    </div>
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                Admission Number / प्रवेश क्रमांक <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="admission_number"
                                   id="admission_number"
                                   class="form-control @error('admission_number') is-invalid @enderror"
                                   value="{{ old('admission_number') }}"
                                   placeholder="e.g. 2025-001"
                                   required>
                            <div class="invalid-feedback">
                                @error('admission_number'){{ $message }}@else Admission number is required.@enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">SR Number / पंजिका क्रमांक</label>
                            <input type="text" name="sr_number" class="form-control"
                                   value="{{ old('sr_number') }}" placeholder="Scholar Register No.">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Admission Date / प्रवेश दिनांक</label>
                            <input type="text"
                                   name="admission_date"
                                   id="admissionDate"
                                   class="form-control"
                                   placeholder="Select date"
                                   value="{{ old('admission_date') }}"
                                   autocomplete="off" readonly>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Academic Year</label>
                            <input type="text" class="form-control bg-light"
                                   value="{{ $activeYear?->name ?? 'No active year set' }}" readonly>
                            <input type="hidden" name="academic_year_id" value="{{ $activeYear?->id }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Class / कक्षा</label>
                            <select name="class_id" class="form-select" id="classSelect"
                                    onchange="loadSections(this.value)">
                                <option value="">— Select Class —</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}"
                                        {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Section / अनुभाग</label>
                            <select name="section_id" class="form-select" id="sectionSelect">
                                <option value="">— Select Section —</option>
                            </select>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary btn-next" data-step="step-office">
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
                                 class="rounded-circle mb-2" width="90" height="90"
                                 style="object-fit:cover; border:3px solid #eee;">
                            <div>
                                <input type="file" name="photo"
                                       class="form-control form-control-sm d-inline-block"
                                       style="width:auto" accept="image/*"
                                       onchange="previewPhoto(this)">
                                <div class="form-text">Photo / फोटो — Max 2MB (optional)</div>
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
                                   placeholder="First Name (English)"
                                   data-hindi-target="[name='first_name_hi']"
                                   required maxlength="100">
                            <div class="invalid-feedback">
                                @error('first_name'){{ $message }}@else First name is required.@enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                प्रथम नाम <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="first_name_hi" class="form-control"
                                   value="{{ old('first_name_hi') }}" placeholder="प्रथम नाम (हिंदी में)">
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
                                   placeholder="Last Name (English)"
                                   data-hindi-target="[name='last_name_hi']"
                                   required maxlength="100">
                            <div class="invalid-feedback">
                                @error('last_name'){{ $message }}@else Last name is required.@enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                उपनाम <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="last_name_hi" class="form-control"
                                   value="{{ old('last_name_hi') }}" placeholder="उपनाम (हिंदी में)">
                        </div>

                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">
                                Gender / लिंग <span class="text-danger">*</span>
                            </label>
                            <select name="gender" id="gender"
                                    class="form-select @error('gender') is-invalid @enderror"
                                    required>
                                <option value="">— Select / चुनें —</option>
                                <option value="male"   {{ old('gender') == 'male'   ? 'selected' : '' }}>Male / पुरुष</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female / महिला</option>
                                <option value="other"  {{ old('gender') == 'other'  ? 'selected' : '' }}>Other / अन्य</option>
                            </select>
                            <div class="invalid-feedback">
                                @error('gender'){{ $message }}@else Please select gender.@enderror
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Date of Birth / जन्म तिथि</label>
                            <input type="text" name="date_of_birth" id="studentDob"
                                   class="form-control" placeholder="Select date"
                                   value="{{ old('date_of_birth') }}" autocomplete="off" readonly>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Blood Group / रक्त समूह</label>
                            <select name="blood_group" class="form-select">
                                <option value="">— Select —</option>
                                @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">DOB in Words (English)</label>
                            <input type="text" name="dob_in_words" class="form-control"
                                   value="{{ old('dob_in_words') }}"
                                   placeholder="e.g. Fifteen March Two Thousand Ten"
                                   data-hindi-target="[name='dob_in_words_hi']">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                जन्म तिथि शब्दों में <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="dob_in_words_hi" class="form-control"
                                   value="{{ old('dob_in_words_hi') }}"
                                   placeholder="जैसे: पन्द्रह मार्च दो हजार दस">
                        </div>

                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Aadhaar / आधार नंबर</label>
                            <input type="text" name="aadhaar_number" class="form-control"
                                   value="{{ old('aadhaar_number') }}"
                                   placeholder="12-digit Aadhaar" maxlength="12" inputmode="numeric">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Jan Aadhaar / जन आधार</label>
                            <input type="text" name="jan_aadhaar_number" class="form-control"
                                   value="{{ old('jan_aadhaar_number') }}"
                                   placeholder="Jan Aadhaar / Enrollment ID">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Category / वर्ग</label>
                            <select name="category" class="form-select">
                                <option value="">— Select —</option>
                                @foreach(['general'=>'General / सामान्य','sc'=>'SC','st'=>'ST','obc'=>'OBC','mbc'=>'MBC','ews'=>'EWS'] as $val=>$lbl)
                                    <option value="{{ $val }}" {{ old('category') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Identification Mark / पहचान चिह्न</label>
                            <div class="row g-1">
                                <div class="col-6">
                                    <input type="text" name="identification_mark" class="form-control"
                                           value="{{ old('identification_mark') }}" placeholder="English"
                                           data-hindi-target="[name='identification_mark_hi']">
                                </div>
                                <div class="col-6">
                                    <input type="text" name="identification_mark_hi" class="form-control"
                                           value="{{ old('identification_mark_hi') }}" placeholder="हिंदी में">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">CWSN / दिव्यांगता</label>
                            <input type="text" name="cwsn_type" class="form-control"
                                   value="{{ old('cwsn_type') }}"
                                   placeholder="Type of disability, if any / यदि कोई हो">
                        </div>

                        <div class="col-sm-3">
                            <label class="form-label fw-semibold d-block">Minority / अल्पसंख्यक</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox"
                                       name="minority_status" value="1"
                                       {{ old('minority_status') ? 'checked' : '' }}>
                                <label class="form-check-label">Yes / हाँ</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label fw-semibold d-block">BPL Status</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox"
                                       name="bpl_status" value="1"
                                       {{ old('bpl_status') ? 'checked' : '' }}>
                                <label class="form-check-label">Yes / हाँ</label>
                            </div>
                        </div>

                        <div class="col-12"><hr class="my-1"><small class="fw-semibold text-muted">Contact / संपर्क</small></div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Mobile / मोबाइल</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone') }}" placeholder="Primary mobile">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">WhatsApp</label>
                            <input type="text" name="whatsapp" class="form-control"
                                   value="{{ old('whatsapp') }}" placeholder="WhatsApp number">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Email <span class="text-muted small">(Optional)</span></label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email') }}" placeholder="student@email.com">
                        </div>

                        <div class="col-12 d-flex justify-content-between">
                            <button type="button" class="btn btn-label-secondary btn-prev">
                                <i class="icon-base ti tabler-arrow-left icon-xs me-1"></i> Previous
                            </button>
                            <button type="button" class="btn btn-primary btn-next" data-step="step-personal">
                                Next <i class="icon-base ti tabler-arrow-right icon-xs ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 3: Family --}}
                <div id="step-family" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Family Details / परिवार विवरण</h6>
                        <small class="text-muted">Father, Mother and Guardian information. All optional.</small>
                    </div>
                    <div class="row g-4">
                        <div class="col-12">
                            <p class="fw-semibold text-primary mb-2 border-bottom pb-1">
                                <i class="icon-base ti tabler-man me-1"></i> Father / पिता
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Father's Name</label>
                            <input type="text" name="father_name" class="form-control"
                                   value="{{ old('father_name') }}" placeholder="Full name"
                                   data-hindi-target="[name='father_name_hi']">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                पिता का नाम <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="father_name_hi" class="form-control"
                                   value="{{ old('father_name_hi') }}" placeholder="पूरा नाम">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Occupation</label>
                            <input type="text" name="father_occupation" class="form-control"
                                   value="{{ old('father_occupation') }}" placeholder="e.g. Farmer"
                                   data-hindi-target="[name='father_occupation_hi']">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">
                                व्यवसाय <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="father_occupation_hi" class="form-control"
                                   value="{{ old('father_occupation_hi') }}" placeholder="जैसे: कृषक">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Annual Income / वार्षिक आय</label>
                            <input type="text" name="father_annual_income" class="form-control"
                                   value="{{ old('father_annual_income') }}" placeholder="₹ per year">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Father's Mobile</label>
                            <input type="text" name="father_mobile" class="form-control"
                                   value="{{ old('father_mobile') }}" placeholder="10-digit">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Father's Aadhaar</label>
                            <input type="text" name="father_aadhaar" class="form-control"
                                   value="{{ old('father_aadhaar') }}" placeholder="12-digit" maxlength="12">
                        </div>

                        <div class="col-12">
                            <p class="fw-semibold text-danger mb-2 border-bottom pb-1 mt-2">
                                <i class="icon-base ti tabler-woman me-1"></i> Mother / माता
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Mother's Name</label>
                            <input type="text" name="mother_name" class="form-control"
                                   value="{{ old('mother_name') }}" placeholder="Full name"
                                   data-hindi-target="[name='mother_name_hi']">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                माता का नाम <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="mother_name_hi" class="form-control"
                                   value="{{ old('mother_name_hi') }}" placeholder="पूरा नाम">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Occupation</label>
                            <input type="text" name="mother_occupation" class="form-control"
                                   value="{{ old('mother_occupation') }}" placeholder="e.g. Homemaker"
                                   data-hindi-target="[name='mother_occupation_hi']">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">
                                व्यवसाय <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="mother_occupation_hi" class="form-control"
                                   value="{{ old('mother_occupation_hi') }}" placeholder="जैसे: गृहिणी">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Annual Income / वार्षिक आय</label>
                            <input type="text" name="mother_annual_income" class="form-control"
                                   value="{{ old('mother_annual_income') }}" placeholder="₹ per year">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Mother's Mobile</label>
                            <input type="text" name="mother_mobile" class="form-control"
                                   value="{{ old('mother_mobile') }}" placeholder="10-digit">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Mother's Aadhaar</label>
                            <input type="text" name="mother_aadhaar" class="form-control"
                                   value="{{ old('mother_aadhaar') }}" placeholder="12-digit" maxlength="12">
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
                                   value="{{ old('guardian_name') }}" placeholder="Full name"
                                   data-hindi-target="[name='guardian_name_hi']">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                अभिभावक का नाम <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="guardian_name_hi" class="form-control"
                                   value="{{ old('guardian_name_hi') }}" placeholder="पूरा नाम">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Relationship</label>
                            <input type="text" name="guardian_relationship" class="form-control"
                                   value="{{ old('guardian_relationship') }}" placeholder="e.g. Uncle"
                                   data-hindi-target="[name='guardian_relationship_hi']">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">
                                संबंध <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="guardian_relationship_hi" class="form-control"
                                   value="{{ old('guardian_relationship_hi') }}" placeholder="जैसे: चाचा">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Guardian Mobile</label>
                            <input type="text" name="guardian_mobile" class="form-control"
                                   value="{{ old('guardian_mobile') }}" placeholder="10-digit">
                        </div>

                        <div class="col-12 d-flex justify-content-between">
                            <button type="button" class="btn btn-label-secondary btn-prev">
                                <i class="icon-base ti tabler-arrow-left icon-xs me-1"></i> Previous
                            </button>
                            <button type="button" class="btn btn-primary btn-next" data-step="step-family">
                                Next <i class="icon-base ti tabler-arrow-right icon-xs ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 4: Address --}}
                <div id="step-address" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Address / पता</h6>
                        <small class="text-muted">Permanent and correspondence address.</small>
                    </div>
                    <div class="row g-4">
                        <div class="col-12">
                            <p class="fw-semibold text-primary mb-2 border-bottom pb-1">
                                Permanent Address / स्थायी पता
                            </p>
                        </div>
                        @foreach([
                            ['perm_house_no','perm_house_no_hi','House No. / मकान नं.','House No.','मकान नं.'],
                            ['perm_street','perm_street_hi','Street / गली','Street','गली'],
                            ['perm_village_city','perm_village_city_hi','Village/City / ग्राम','Village/City','ग्राम/शहर'],
                            ['perm_tehsil','perm_tehsil_hi','Tehsil / तहसील','Tehsil','तहसील'],
                            ['perm_district','perm_district_hi','District / जिला','District','जिला'],
                            ['perm_state','perm_state_hi','State / राज्य','State','राज्य'],
                        ] as [$f,$fhi,$label,$ph,$phhi])
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">{{ $label }}</label>
                                <div class="row g-1">
                                    <div class="col-6">
                                        <input type="text" name="{{ $f }}"
                                               class="form-control form-control-sm"
                                               value="{{ old($f) }}" placeholder="{{ $ph }}"
                                               data-hindi-target="[name='{{ $fhi }}']">
                                    </div>
                                    <div class="col-6">
                                        <input type="text" name="{{ $fhi }}"
                                               class="form-control form-control-sm"
                                               value="{{ old($fhi) }}" placeholder="{{ $phhi }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="col-sm-3">
                            <label class="form-label fw-semibold">PIN Code</label>
                            <input type="text" name="perm_pincode" class="form-control"
                                   value="{{ old('perm_pincode') }}"
                                   placeholder="6-digit PIN" maxlength="6" inputmode="numeric">
                        </div>

                        <div class="col-12 mt-2">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-1">
                                <p class="fw-semibold text-primary mb-0">Correspondence Address / पत्राचार पता</p>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox"
                                           name="same_as_permanent" id="sameAsPermanent" value="1" checked
                                           onchange="toggleCorrAddress(this)">
                                    <label class="form-check-label small" for="sameAsPermanent">
                                        Same as permanent / स्थायी पते जैसा
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div id="corrAddressFields" class="col-12" style="display:none;">
                            <div class="row g-3">
                                @foreach([
                                    ['corr_house_no','House No.'],['corr_street','Street / गली'],
                                    ['corr_village_city','Village/City'],['corr_tehsil','Tehsil'],
                                    ['corr_district','District'],['corr_state','State'],['corr_pincode','PIN Code'],
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
                            <button type="button" class="btn btn-primary btn-next" data-step="step-address">
                                Next <i class="icon-base ti tabler-arrow-right icon-xs ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 5: Previous School --}}
                <div id="step-academic" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Previous School History / पूर्व विद्यालय</h6>
                        <small class="text-muted">Details of the school previously attended. All optional.</small>
                    </div>
                    <div class="row g-4">
                        <div class="col-sm-8">
                            <label class="form-label fw-semibold">Previous School Name</label>
                            <input type="text" name="previous_school_name" class="form-control"
                                   value="{{ old('previous_school_name') }}"
                                   placeholder="Name of previous school">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">School Type</label>
                            <select name="previous_school_type" class="form-select">
                                <option value="">— Select —</option>
                                <option value="government">Government / सरकारी</option>
                                <option value="private">Private / निजी</option>
                                <option value="aided">Aided / अनुदानित</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Last Class Attended</label>
                            <input type="text" name="last_class_attended" class="form-control"
                                   value="{{ old('last_class_attended') }}" placeholder="e.g. Class 5">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Result / परिणाम</label>
                            <select name="last_result" class="form-select">
                                <option value="">— Select —</option>
                                <option value="pass">Pass / उत्तीर्ण</option>
                                <option value="fail">Fail / अनुत्तीर्ण</option>
                                <option value="promoted">Promoted / पदोन्नत</option>
                                <option value="na">N/A</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Percentage / Grade</label>
                            <input type="text" name="percentage_grade" class="form-control"
                                   value="{{ old('percentage_grade') }}" placeholder="e.g. 85% or A+">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Medium of Instruction</label>
                            <select name="medium_of_instruction" class="form-select">
                                <option value="">— Select —</option>
                                <option value="hindi">Hindi / हिंदी</option>
                                <option value="english">English / अंग्रेजी</option>
                                <option value="other">Other / अन्य</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">TC Number</label>
                            <input type="text" name="tc_number" class="form-control"
                                   value="{{ old('tc_number') }}" placeholder="Transfer Certificate No.">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">TC Issue Date</label>
                            <input type="text" name="tc_issue_date" id="tcIssueDate"
                                   class="form-control" placeholder="Select date"
                                   value="{{ old('tc_issue_date') }}" autocomplete="off" readonly>
                        </div>

                        <div class="col-12 d-flex justify-content-between">
                            <button type="button" class="btn btn-label-secondary btn-prev">
                                <i class="icon-base ti tabler-arrow-left icon-xs me-1"></i> Previous
                            </button>
                            <button type="button" class="btn btn-primary btn-next" data-step="step-academic">
                                Next <i class="icon-base ti tabler-arrow-right icon-xs ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 6: Bank --}}
                <div id="step-bank" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Bank Details / बैंक विवरण</h6>
                        <small class="text-muted">Used for DBT and scholarship payments. All optional.</small>
                    </div>
                    <div class="alert alert-info small mb-3">
                        <i class="icon-base ti tabler-info-circle me-1"></i>
                        This information is used for Direct Benefit Transfer (DBT) and scholarship payments.
                    </div>
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Bank Name / बैंक का नाम</label>
                            <input type="text" name="bank_name" class="form-control"
                                   value="{{ old('bank_name') }}" placeholder="e.g. State Bank of India">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Branch / शाखा</label>
                            <input type="text" name="bank_branch" class="form-control"
                                   value="{{ old('bank_branch') }}" placeholder="Branch name">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Account Number / खाता नंबर</label>
                            <input type="text" name="account_number" class="form-control"
                                   value="{{ old('account_number') }}" placeholder="Account number">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">IFSC Code</label>
                            <input type="text" name="ifsc_code" id="ifscCode" class="form-control"
                                   value="{{ old('ifsc_code') }}"
                                   placeholder="e.g. SBIN0001234"
                                   style="text-transform:uppercase">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Account Holder</label>
                            <select name="account_holder" class="form-select">
                                <option value="parent">Parent / माता-पिता</option>
                                <option value="student">Student / छात्र</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Account Holder Name</label>
                            <input type="text" name="account_holder_name" class="form-control"
                                   value="{{ old('account_holder_name') }}"
                                   placeholder="Name as per bank records">
                        </div>

                        <div class="col-12 d-flex justify-content-between">
                            <button type="button" class="btn btn-label-secondary btn-prev">
                                <i class="icon-base ti tabler-arrow-left icon-xs me-1"></i> Previous
                            </button>
                            <button type="button" class="btn btn-primary btn-next" data-step="step-bank">
                                Next <i class="icon-base ti tabler-arrow-right icon-xs ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 7: Documents --}}
                <div id="step-documents" class="content dstepper-block">
                    <div class="content-header mb-4">
                        <h6 class="mb-0">Documents / दस्तावेज़ सूची</h6>
                        <small class="text-muted">Upload PDF, JPG or PNG. Max 2MB each. All optional.</small>
                    </div>
                    <div class="alert alert-warning small mb-3">
                        <i class="icon-base ti tabler-alert-triangle me-1"></i>
                        All documents are optional at admission time and can be uploaded later.
                    </div>
                    <div class="row g-3">
                        @foreach(\App\Models\StudentDocument::typeLabels() as $type => $label)
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <label class="form-label fw-semibold small mb-2 d-block">{{ $label }}</label>
                                    <input type="file" name="documents[{{ $type }}]"
                                           class="form-control form-control-sm"
                                           accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="col-12 d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-label-secondary btn-prev">
                            <i class="icon-base ti tabler-arrow-left icon-xs me-1"></i> Previous
                        </button>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="icon-base ti tabler-user-plus me-2"></i>
                            Admit Student / छात्र प्रवेश दें
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

    // ── Stepper ────────────────────────────────────────────────────────
    const stepper = new Stepper(document.querySelector('.bs-stepper'), {
        linear: false,
        animation: false
    });
    stepper.to(1);

    // ── Validation rules ───────────────────────────────────────────────
    // Only fields required by server-side validation.
    // Validation is inline — no blocking SweetAlert modals.
    // On submit failure: toast notification + stepper jumps to error step.
    const stepValidations = {
        'step-office': [
            { id: 'admission_number', msg: 'Admission number is required.' },
        ],
        'step-personal': [
            { id: 'first_name', msg: 'First name is required.' },
            { id: 'last_name',  msg: 'Last name is required.' },
            { id: 'gender',     msg: 'Please select gender.', isSelect: true },
        ],
    };

    // ── Validate a single step ─────────────────────────────────────────
    function validateStep(stepId) {
        const rules  = stepValidations[stepId] || [];
        let valid    = true;
        let firstBad = null;

        rules.forEach(rule => {
            const field = document.getElementById(rule.id);
            if (!field) return;

            const empty = field.tagName === 'SELECT'
                ? field.value === ''
                : field.value.trim() === '';

            if (empty) {
                field.classList.add('is-invalid');
                field.classList.remove('is-valid');
                const fb = field.parentElement.querySelector('.invalid-feedback');
                if (fb) fb.textContent = rule.msg;
                if (!firstBad) firstBad = field;
                valid = false;
            } else {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
            }
        });

        // Scroll to first invalid field — inline error is already visible
        if (firstBad) {
            firstBad.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstBad.focus();
        }
        return valid;
    }

    // ── Live validation: remove error as user types ────────────────────
    Object.values(stepValidations).flat().forEach(rule => {
        const field = document.getElementById(rule.id);
        if (!field) return;
        const evt = field.tagName === 'SELECT' ? 'change' : 'input';
        field.addEventListener(evt, function () {
            const empty = field.tagName === 'SELECT'
                ? this.value === '' : this.value.trim() === '';
            if (!empty) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
            }
        });
    });

    // ── Next buttons ───────────────────────────────────────────────────
    document.querySelectorAll('.btn-next').forEach(btn => {
        btn.addEventListener('click', function () {
            if (validateStep(this.dataset.step)) stepper.next();
        });
    });

    // ── Prev buttons ───────────────────────────────────────────────────
    document.querySelectorAll('.btn-prev').forEach(btn => {
        btn.addEventListener('click', () => stepper.previous());
    });

    // ── Submit: validate all required steps ────────────────────────────
    document.getElementById('studentCreateForm').addEventListener('submit', function (e) {
        const stepOrder = ['step-office','step-personal','step-family','step-address','step-academic','step-bank','step-documents'];
        let firstFailedStep = null;
        let hasError = false;

        Object.entries(stepValidations).forEach(([stepId, rules]) => {
            rules.forEach(rule => {
                const field = document.getElementById(rule.id);
                if (!field) return;
                const empty = field.tagName === 'SELECT'
                    ? field.value === '' : field.value.trim() === '';
                if (empty) {
                    field.classList.add('is-invalid');
                    const fb = field.parentElement.querySelector('.invalid-feedback');
                    if (fb) fb.textContent = rule.msg;
                    hasError = true;
                    if (!firstFailedStep) firstFailedStep = stepId;
                }
            });
        });

        if (hasError) {
            e.preventDefault();
            // Silently jump to first failing step
            if (firstFailedStep) stepper.to(stepOrder.indexOf(firstFailedStep) + 1);
            // Minimal toast — inline errors explain the details
            Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
            }).fire({
                icon: 'warning',
                title: 'Please fill in all required fields.',
            });
        }
    });

    // ── Utilities ──────────────────────────────────────────────────────

    window.previewPhoto = function(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => document.getElementById('photoPreview').src = e.target.result;
            reader.readAsDataURL(input.files[0]);
        }
    };

    window.loadSections = async function(classId) {
        const select = document.getElementById('sectionSelect');
        select.innerHTML = '<option value="">— Select Section —</option>';
        if (!classId) return;
        try {
            const sections = await (await fetch(`/classes/${classId}/sections`)).json();
            const oldSection = '{{ old('section_id') }}';
            sections.forEach(s => {
                const opt = new Option(s.name, s.id);
                if (String(s.id) === oldSection) opt.selected = true;
                select.add(opt);
            });
        } catch(e) { console.warn('Sections load failed', e); }
    };

    window.toggleCorrAddress = function(cb) {
        document.getElementById('corrAddressFields').style.display = cb.checked ? 'none' : 'block';
    };

    document.getElementById('ifscCode')?.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // ── Flatpickr ──────────────────────────────────────────────────────
    flatpickr('#admissionDate', {
        dateFormat:  'Y-m-d',
        altInput:    true,
        altFormat:   'd M Y',
        maxDate:     'today',
        allowInput:  false,
        defaultDate: '{{ old('admission_date') }}' || null,
    });

    flatpickr('#studentDob', {
        dateFormat:  'Y-m-d',
        altInput:    true,
        altFormat:   'd M Y',
        maxDate:     'today',
        allowInput:  false,
        defaultDate: '{{ old('date_of_birth') }}' || null,
    });

    flatpickr('#tcIssueDate', {
        dateFormat:  'Y-m-d',
        altInput:    true,
        altFormat:   'd M Y',
        allowInput:  false,
        defaultDate: '{{ old('tc_issue_date') }}' || null,
    });

    // ── On server error: restore class → section, jump to failing step ─
    @if($errors->any())
        (function() {
            const stepOrder = ['step-office','step-personal','step-family','step-address','step-academic','step-bank','step-documents'];
            const errMap    = {
                admission_number: 'step-office',
                first_name: 'step-personal',
                last_name:  'step-personal',
                gender:     'step-personal',
            };
            const errorKeys = @json(array_keys($errors->toArray()));
            for (const key of errorKeys) {
                if (errMap[key]) {
                    stepper.to(stepOrder.indexOf(errMap[key]) + 1);
                    break;
                }
            }
            // Restore section dropdown if class was selected
            const classId = document.getElementById('classSelect')?.value;
            if (classId) window.loadSections(classId);
        })();
    @endif

});
</script>
@endpush

@endsection
