@php
    use Illuminate\Support\Facades\Storage;
@endphp
@extends('layouts.tenant')

@section('title', $student->full_name)

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-user-view.scss'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app-user-view.js'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ route('tenant.dashboard') }}">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('tenant.students.index') }}">Students</a>
            </li>
            <li class="breadcrumb-item active">{{ $student->full_name }}</li>
        </ol>
    </nav>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-4">
            <i class="icon-base ti tabler-alert-circle me-1"></i>
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- LEFT SIDEBAR --}}
        <div class="col-xl-4 col-lg-5 col-md-5">

            {{-- Profile Card --}}
            <div class="card mb-4">
                <div class="card-body pt-4">
                    <div class="user-avatar-section text-center mb-3">
                        @if($student->photo)
                            <img src="{{ Storage::url($student->photo) }}"
                                 class="img-fluid rounded-circle mb-3"
                                 width="110" height="110"
                                 style="object-fit:cover; border: 4px solid var(--bs-primary-bg-subtle)">
                        @else
                            <div class="avatar avatar-xl mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-label-primary"
                                      style="font-size:2rem;">
                                    {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <div class="user-info">
                            <h5 class="mb-0 fw-bold">{{ $student->full_name }}</h5>
                            @if($student->first_name_hi)
                                <p class="text-muted mb-1 small">{{ $student->full_name_hi }}</p>
                            @endif
                            <span class="badge bg-label-info mt-1">
                                {{ $student->class_section }}
                            </span>
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <div class="text-center mb-3">
                        @php
                            $statusColor = match($student->status) {
                                'active'      => 'success',
                                'inactive'    => 'secondary',
                                'graduated'   => 'primary',
                                'transferred' => 'warning',
                                'dropped'     => 'danger',
                                default       => 'secondary',
                            };
                        @endphp
                        <span class="badge bg-{{ $statusColor }} fs-6 px-3 py-2">
                            <i class="icon-base ti tabler-circle me-1"
                               style="font-size:8px"></i>
                            {{ ucfirst($student->status) }}
                        </span>
                    </div>

                    <hr>

                    {{-- Key Info --}}
                    <div class="info-container">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3 d-flex align-items-center">
                                <span class="fw-semibold me-2 text-nowrap" style="min-width:130px">
                                    Admission No:
                                </span>
                                <span class="text-muted">{{ $student->admission_number }}</span>
                            </li>
                            @if($student->sr_number)
                            <li class="mb-3 d-flex align-items-center">
                                <span class="fw-semibold me-2 text-nowrap" style="min-width:130px">
                                    SR Number:
                                </span>
                                <span class="text-muted">{{ $student->sr_number }}</span>
                            </li>
                            @endif
                            <li class="mb-3 d-flex align-items-center">
                                <span class="fw-semibold me-2 text-nowrap" style="min-width:130px">
                                    Gender:
                                </span>
                                <span class="text-muted">{{ ucfirst($student->gender ?? '—') }}</span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <span class="fw-semibold me-2 text-nowrap" style="min-width:130px">
                                    Date of Birth:
                                </span>
                                <span class="text-muted">
                                    {{ $student->date_of_birth?->format('d M Y') ?? '—' }}
                                </span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <span class="fw-semibold me-2 text-nowrap" style="min-width:130px">
                                    Blood Group:
                                </span>
                                <span class="badge bg-label-danger">
                                    {{ $student->blood_group ?? '—' }}
                                </span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <span class="fw-semibold me-2 text-nowrap" style="min-width:130px">
                                    Category:
                                </span>
                                <span class="badge bg-label-secondary">
                                    {{ strtoupper($student->category ?? '—') }}
                                </span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <span class="fw-semibold me-2 text-nowrap" style="min-width:130px">
                                    Mobile:
                                </span>
                                <span class="text-muted">{{ $student->phone ?? '—' }}</span>
                            </li>
                            @if($student->whatsapp)
                            <li class="mb-3 d-flex align-items-center">
                                <span class="fw-semibold me-2 text-nowrap" style="min-width:130px">
                                    WhatsApp:
                                </span>
                                <span class="text-muted">{{ $student->whatsapp }}</span>
                            </li>
                            @endif
                            <li class="mb-3 d-flex align-items-center">
                                <span class="fw-semibold me-2 text-nowrap" style="min-width:130px">
                                    Admission Date:
                                </span>
                                <span class="text-muted">
                                    {{ $student->admission_date?->format('d M Y') ?? '—' }}
                                </span>
                            </li>
                            <li class="d-flex align-items-center flex-wrap gap-1">
                                @if($student->minority_status)
                                    <span class="badge bg-label-info">Minority</span>
                                @endif
                                @if($student->bpl_status)
                                    <span class="badge bg-label-warning">BPL</span>
                                @endif
                                @if($student->cwsn_type)
                                    <span class="badge bg-label-danger"
                                          title="{{ $student->cwsn_type }}">CWSN</span>
                                @endif
                            </li>
                        </ul>
                    </div>

                    <hr>

                    {{-- Action Buttons --}}
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('tenant.students.edit', $student) }}"
                           class="btn btn-primary">
                            <i class="icon-base ti tabler-edit me-1"></i>
                            Edit Student
                        </a>
                        <button type="button"
                                class="btn btn-outline-secondary"
                                onclick="window.print()">
                            <i class="icon-base ti tabler-printer me-1"></i>
                            Print Profile
                        </button>
                        <button type="button"
                                class="btn btn-outline-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#changeStatusModal">
                            <i class="icon-base ti tabler-refresh me-1"></i>
                            Change Status
                        </button>
                        <button type="button"
                                class="btn btn-outline-danger suspend-user">
                            <i class="icon-base ti tabler-user-off me-1"></i>
                            Deactivate Student
                        </button>
                    </div>
                </div>
            </div>

        </div>

        {{-- RIGHT CONTENT --}}
        <div class="col-xl-8 col-lg-7 col-md-7">

            {{-- Nav Tabs --}}
            <div class="nav-align-top">
                <ul class="nav nav-pills flex-column flex-md-row mb-4 gap-2 gap-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="#personal" data-bs-toggle="tab">
                            <i class="icon-base ti tabler-user-circle me-1"></i>
                            Personal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#family" data-bs-toggle="tab">
                            <i class="icon-base ti tabler-users me-1"></i>
                            Family
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#address" data-bs-toggle="tab">
                            <i class="icon-base ti tabler-map-pin me-1"></i>
                            Address
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#academic" data-bs-toggle="tab">
                            <i class="icon-base ti tabler-school me-1"></i>
                            Prev. School
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#bank" data-bs-toggle="tab">
                            <i class="icon-base ti tabler-building-bank me-1"></i>
                            Bank
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#subjects" data-bs-toggle="tab">
                            <i class="icon-base ti tabler-book me-1"></i>
                            Subjects
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#documents" data-bs-toggle="tab">
                            <i class="icon-base ti tabler-file me-1"></i>
                            Documents
                            @if($student->documents->count() > 0)
                                <span class="badge bg-primary rounded-pill ms-1">
                                    {{ $student->documents->count() }}
                                </span>
                            @endif
                        </a>
                    </li>
                </ul>

                <div class="tab-content">

                    {{-- TAB: Personal --}}
                    <div class="tab-pane fade show active" id="personal">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="icon-base ti tabler-user me-2 text-primary"></i>
                                    Personal Information / व्यक्तिगत जानकारी
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    @php
                                        $personalFields = [
                                            ['First Name', $student->first_name, 'First Name (हिं)', $student->first_name_hi],
                                            ['Last Name', $student->last_name, 'Last Name (हिं)', $student->last_name_hi],
                                            ['DOB in Words', $student->dob_in_words, 'जन्म तिथि (हिं)', $student->dob_in_words_hi],
                                            ['Aadhaar', $student->aadhaar_number ? 'XXXX XXXX '.substr($student->aadhaar_number,-4) : null, null, null],
                                            ['Jan Aadhaar', $student->jan_aadhaar_number, null, null],
                                            ['Identification Mark', $student->identification_mark, 'पहचान चिह्न', $student->identification_mark_hi],
                                            ['CWSN Type', $student->cwsn_type, null, null],
                                            ['Email', $student->email, null, null],
                                        ];
                                    @endphp

                                    @foreach($personalFields as [$label, $value, $labelHi, $valueHi])
                                        @if($value || $valueHi)
                                        <div class="col-sm-6">
                                            <p class="text-muted small mb-1">{{ $label }}</p>
                                            <p class="fw-semibold mb-0">{{ $value ?? '—' }}</p>
                                            @if($labelHi && $valueHi)
                                                <p class="text-muted small mb-0 mt-1">
                                                    <span class="badge bg-label-warning me-1">हिं</span>
                                                    {{ $valueHi }}
                                                </p>
                                            @endif
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB: Family --}}
                    <div class="tab-pane fade" id="family">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="icon-base ti tabler-users me-2 text-success"></i>
                                    Family Details / परिवार विवरण
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($student->familyDetail)
                                    {{-- Father --}}
                                    <div class="border rounded p-3 mb-3">
                                        <h6 class="fw-bold text-primary mb-3">
                                            <i class="icon-base ti tabler-man me-1"></i>
                                            Father / पिता
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <p class="text-muted small mb-1">Name</p>
                                                <p class="fw-semibold mb-0">
                                                    {{ $student->familyDetail->father_name ?? '—' }}
                                                </p>
                                                @if($student->familyDetail->father_name_hi)
                                                    <p class="text-muted small mb-0">
                                                        <span class="badge bg-label-warning me-1">हिं</span>
                                                        {{ $student->familyDetail->father_name_hi }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="col-sm-6">
                                                <p class="text-muted small mb-1">Occupation</p>
                                                <p class="fw-semibold mb-0">
                                                    {{ $student->familyDetail->father_occupation ?? '—' }}
                                                </p>
                                                @if($student->familyDetail->father_occupation_hi)
                                                    <p class="text-muted small mb-0">
                                                        <span class="badge bg-label-warning me-1">हिं</span>
                                                        {{ $student->familyDetail->father_occupation_hi }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="col-sm-4">
                                                <p class="text-muted small mb-1">Annual Income</p>
                                                <p class="fw-semibold mb-0">
                                                    {{ $student->familyDetail->father_annual_income
                                                        ? '₹'.number_format($student->familyDetail->father_annual_income)
                                                        : '—' }}
                                                </p>
                                            </div>
                                            <div class="col-sm-4">
                                                <p class="text-muted small mb-1">Mobile</p>
                                                <p class="fw-semibold mb-0">
                                                    {{ $student->familyDetail->father_mobile ?? '—' }}
                                                </p>
                                            </div>
                                            <div class="col-sm-4">
                                                <p class="text-muted small mb-1">Aadhaar</p>
                                                <p class="fw-semibold mb-0">
                                                    {{ $student->familyDetail->father_aadhaar
                                                        ? 'XXXX '.substr($student->familyDetail->father_aadhaar,-4)
                                                        : '—' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Mother --}}
                                    <div class="border rounded p-3 mb-3">
                                        <h6 class="fw-bold text-danger mb-3">
                                            <i class="icon-base ti tabler-woman me-1"></i>
                                            Mother / माता
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <p class="text-muted small mb-1">Name</p>
                                                <p class="fw-semibold mb-0">
                                                    {{ $student->familyDetail->mother_name ?? '—' }}
                                                </p>
                                                @if($student->familyDetail->mother_name_hi)
                                                    <p class="text-muted small mb-0">
                                                        <span class="badge bg-label-warning me-1">हिं</span>
                                                        {{ $student->familyDetail->mother_name_hi }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="col-sm-6">
                                                <p class="text-muted small mb-1">Occupation</p>
                                                <p class="fw-semibold mb-0">
                                                    {{ $student->familyDetail->mother_occupation ?? '—' }}
                                                </p>
                                                @if($student->familyDetail->mother_occupation_hi)
                                                    <p class="text-muted small mb-0">
                                                        <span class="badge bg-label-warning me-1">हिं</span>
                                                        {{ $student->familyDetail->mother_occupation_hi }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="col-sm-4">
                                                <p class="text-muted small mb-1">Annual Income</p>
                                                <p class="fw-semibold mb-0">
                                                    {{ $student->familyDetail->mother_annual_income
                                                        ? '₹'.number_format($student->familyDetail->mother_annual_income)
                                                        : '—' }}
                                                </p>
                                            </div>
                                            <div class="col-sm-4">
                                                <p class="text-muted small mb-1">Mobile</p>
                                                <p class="fw-semibold mb-0">
                                                    {{ $student->familyDetail->mother_mobile ?? '—' }}
                                                </p>
                                            </div>
                                            <div class="col-sm-4">
                                                <p class="text-muted small mb-1">Aadhaar</p>
                                                <p class="fw-semibold mb-0">
                                                    {{ $student->familyDetail->mother_aadhaar
                                                        ? 'XXXX '.substr($student->familyDetail->mother_aadhaar,-4)
                                                        : '—' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Guardian --}}
                                    @if($student->familyDetail->guardian_name)
                                    <div class="border rounded p-3">
                                        <h6 class="fw-bold text-warning mb-3">
                                            <i class="icon-base ti tabler-user-heart me-1"></i>
                                            Guardian / अभिभावक
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <p class="text-muted small mb-1">Name</p>
                                                <p class="fw-semibold mb-0">
                                                    {{ $student->familyDetail->guardian_name }}
                                                </p>
                                                @if($student->familyDetail->guardian_name_hi)
                                                    <p class="text-muted small mb-0">
                                                        <span class="badge bg-label-warning me-1">हिं</span>
                                                        {{ $student->familyDetail->guardian_name_hi }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="col-sm-6">
                                                <p class="text-muted small mb-1">Relationship</p>
                                                <p class="fw-semibold mb-0">
                                                    {{ $student->familyDetail->guardian_relationship ?? '—' }}
                                                    @if($student->familyDetail->guardian_relationship_hi)
                                                        / {{ $student->familyDetail->guardian_relationship_hi }}
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-sm-4">
                                                <p class="text-muted small mb-1">Mobile</p>
                                                <p class="fw-semibold mb-0">
                                                    {{ $student->familyDetail->guardian_mobile ?? '—' }}
                                                </p>
                                            </div>
                                            <div class="col-sm-4">
                                                <p class="text-muted small mb-1">Occupation</p>
                                                <p class="fw-semibold mb-0">
                                                    {{ $student->familyDetail->guardian_occupation ?? '—' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @else
                                    <p class="text-muted text-center py-4 mb-0">
                                        No family details recorded.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- TAB: Address --}}
                    <div class="tab-pane fade" id="address">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="icon-base ti tabler-map-pin me-2 text-info"></i>
                                    Address / पता
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($student->address)
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 h-100">
                                                <h6 class="fw-bold text-primary mb-3">
                                                    <i class="icon-base ti tabler-home me-1"></i>
                                                    Permanent / स्थायी पता
                                                </h6>
                                                @php
                                                    $permAddr = collect([
                                                        $student->address->perm_house_no,
                                                        $student->address->perm_street,
                                                        $student->address->perm_village_city,
                                                        $student->address->perm_tehsil,
                                                        $student->address->perm_district,
                                                        $student->address->perm_state,
                                                        $student->address->perm_pincode,
                                                    ])->filter()->implode(', ');

                                                    $permAddrHi = collect([
                                                        $student->address->perm_house_no_hi,
                                                        $student->address->perm_street_hi,
                                                        $student->address->perm_village_city_hi,
                                                        $student->address->perm_tehsil_hi,
                                                        $student->address->perm_district_hi,
                                                        $student->address->perm_state_hi,
                                                        $student->address->perm_pincode,
                                                    ])->filter()->implode(', ');
                                                @endphp
                                                <p class="mb-1">{{ $permAddr ?: '—' }}</p>
                                                @if($permAddrHi)
                                                    <p class="text-muted small mb-0">
                                                        <span class="badge bg-label-warning me-1">हिं</span>
                                                        {{ $permAddrHi }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 h-100">
                                                <h6 class="fw-bold text-success mb-3">
                                                    <i class="icon-base ti tabler-mail me-1"></i>
                                                    Correspondence / पत्राचार पता
                                                </h6>
                                                @if($student->address->same_as_permanent)
                                                    <p class="text-muted mb-0">
                                                        <i class="icon-base ti tabler-copy me-1"></i>
                                                        Same as permanent address
                                                    </p>
                                                @else
                                                    @php
                                                        $corrAddr = collect([
                                                            $student->address->corr_house_no,
                                                            $student->address->corr_street,
                                                            $student->address->corr_village_city,
                                                            $student->address->corr_tehsil,
                                                            $student->address->corr_district,
                                                            $student->address->corr_state,
                                                            $student->address->corr_pincode,
                                                        ])->filter()->implode(', ');
                                                    @endphp
                                                    <p class="mb-0">{{ $corrAddr ?: '—' }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-muted text-center py-4 mb-0">No address recorded.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- TAB: Academic History --}}
                    <div class="tab-pane fade" id="academic">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="icon-base ti tabler-school me-2 text-warning"></i>
                                    Previous School / पूर्व विद्यालय
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($student->academicHistory && $student->academicHistory->previous_school_name)
                                    <div class="row g-4">
                                        <div class="col-sm-8">
                                            <p class="text-muted small mb-1">School Name</p>
                                            <p class="fw-semibold mb-0">
                                                {{ $student->academicHistory->previous_school_name }}
                                            </p>
                                        </div>
                                        <div class="col-sm-4">
                                            <p class="text-muted small mb-1">Type</p>
                                            <span class="badge bg-label-secondary">
                                                {{ ucfirst($student->academicHistory->previous_school_type ?? '—') }}
                                            </span>
                                        </div>
                                        <div class="col-sm-3">
                                            <p class="text-muted small mb-1">Last Class</p>
                                            <p class="fw-semibold mb-0">
                                                {{ $student->academicHistory->last_class_attended ?? '—' }}
                                            </p>
                                        </div>
                                        <div class="col-sm-3">
                                            <p class="text-muted small mb-1">Result</p>
                                            @php
                                                $resultColor = match($student->academicHistory->last_result) {
                                                    'pass','promoted' => 'success',
                                                    'fail'            => 'danger',
                                                    default           => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge bg-label-{{ $resultColor }}">
                                                {{ ucfirst($student->academicHistory->last_result ?? '—') }}
                                            </span>
                                        </div>
                                        <div class="col-sm-3">
                                            <p class="text-muted small mb-1">Grade / %</p>
                                            <p class="fw-semibold mb-0">
                                                {{ $student->academicHistory->percentage_grade ?? '—' }}
                                            </p>
                                        </div>
                                        <div class="col-sm-3">
                                            <p class="text-muted small mb-1">Medium</p>
                                            <p class="fw-semibold mb-0">
                                                {{ ucfirst($student->academicHistory->medium_of_instruction ?? '—') }}
                                            </p>
                                        </div>
                                        <div class="col-sm-4">
                                            <p class="text-muted small mb-1">TC Number</p>
                                            <p class="fw-semibold mb-0">
                                                {{ $student->academicHistory->tc_number ?? '—' }}
                                            </p>
                                        </div>
                                        <div class="col-sm-4">
                                            <p class="text-muted small mb-1">TC Issue Date</p>
                                            <p class="fw-semibold mb-0">
                                                {{ $student->academicHistory->tc_issue_date?->format('d M Y') ?? '—' }}
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-muted text-center py-4 mb-0">
                                        No previous school history recorded.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- TAB: Bank --}}
                    <div class="tab-pane fade" id="bank">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="icon-base ti tabler-building-bank me-2 text-primary"></i>
                                    Bank Details / बैंक विवरण
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($student->bankDetail && $student->bankDetail->account_number)
                                    <div class="row g-4">
                                        <div class="col-sm-6">
                                            <p class="text-muted small mb-1">Bank Name</p>
                                            <p class="fw-semibold mb-0">
                                                {{ $student->bankDetail->bank_name ?? '—' }}
                                            </p>
                                        </div>
                                        <div class="col-sm-6">
                                            <p class="text-muted small mb-1">Branch</p>
                                            <p class="fw-semibold mb-0">
                                                {{ $student->bankDetail->bank_branch ?? '—' }}
                                            </p>
                                        </div>
                                        <div class="col-sm-6">
                                            <p class="text-muted small mb-1">Account Number</p>
                                            <p class="fw-semibold mb-0 font-monospace">
                                                {{ $student->bankDetail->account_number ?? '—' }}
                                            </p>
                                        </div>
                                        <div class="col-sm-6">
                                            <p class="text-muted small mb-1">IFSC Code</p>
                                            <p class="fw-semibold mb-0 font-monospace">
                                                {{ $student->bankDetail->ifsc_code ?? '—' }}
                                            </p>
                                        </div>
                                        <div class="col-sm-6">
                                            <p class="text-muted small mb-1">Account Holder</p>
                                            <p class="fw-semibold mb-0">
                                                {{ $student->bankDetail->account_holder_name ?? '—' }}
                                                <span class="badge bg-label-secondary ms-1">
                                                    {{ ucfirst($student->bankDetail->account_holder ?? '') }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-muted text-center py-4 mb-0">
                                        No bank details recorded.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- TAB: Subjects --}}
                    <div class="tab-pane fade" id="subjects">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="icon-base ti tabler-book me-2 text-info"></i>
                                    Subjects / विषय
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($student->subjects && $student->subjects->subject_1)
                                    @if($student->subjects->stream !== 'na')
                                        <div class="mb-3">
                                            <span class="badge bg-label-primary fs-6 px-3 py-2">
                                                <i class="icon-base ti tabler-books me-1"></i>
                                                Stream: {{ ucfirst($student->subjects->stream) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="row g-3">
                                        @foreach(range(1, 5) as $i)
                                            @if($student->subjects->{"subject_{$i}"})
                                                <div class="col-sm-4">
                                                    <div class="border rounded p-3 text-center">
                                                        <p class="text-muted small mb-1">Subject {{ $i }}</p>
                                                        <p class="fw-bold mb-0">
                                                            {{ $student->subjects->{"subject_{$i}"} }}
                                                        </p>
                                                        @if($student->subjects->{"subject_{$i}_hi"})
                                                            <p class="text-muted small mb-0">
                                                                {{ $student->subjects->{"subject_{$i}_hi"} }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                        @if($student->subjects->additional_subject)
                                            <div class="col-sm-4">
                                                <div class="border border-dashed rounded p-3 text-center">
                                                    <p class="text-muted small mb-1">Additional</p>
                                                    <p class="fw-bold mb-0">
                                                        {{ $student->subjects->additional_subject }}
                                                    </p>
                                                    @if($student->subjects->additional_subject_hi)
                                                        <p class="text-muted small mb-0">
                                                            {{ $student->subjects->additional_subject_hi }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-muted text-center py-4 mb-0">
                                        No subjects recorded.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- TAB: Documents --}}
                    <div class="tab-pane fade" id="documents">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="icon-base ti tabler-file me-2 text-danger"></i>
                                    Documents / दस्तावेज़
                                </h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $allTypes  = \App\Models\StudentDocument::typeLabels();
                                    $uploaded  = $student->documents->keyBy('document_type');
                                @endphp
                                <div class="row g-3">
                                    @foreach($allTypes as $type => $label)
                                        @php $doc = $uploaded->get($type); @endphp
                                        <div class="col-sm-6">
                                            <div class="d-flex align-items-center justify-content-between
                                                        border rounded p-3
                                                        {{ $doc ? 'border-success bg-light' : '' }}">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar avatar-sm">
                                                        <span class="avatar-initial rounded bg-label-{{ $doc ? 'success' : 'secondary' }}">
                                                            <i class="icon-base ti tabler-{{ $doc ? 'check' : 'file-off' }}"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="fw-semibold small mb-0">
                                                            {{ $label }}
                                                        </p>
                                                        @if($doc)
                                                            <p class="text-muted small mb-0">
                                                                {{ $doc->original_name }}
                                                            </p>
                                                        @else
                                                            <p class="text-muted small mb-0">Not uploaded</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-1">
                                                    @if($doc)
                                                        @if($doc->is_verified)
                                                            <span class="badge bg-success" title="Verified">
                                                                <i class="icon-base ti tabler-shield-check"></i>
                                                            </span>
                                                        @endif
                                                        <a href="{{ Storage::url($doc->file_path) }}"
                                                           target="_blank"
                                                           class="btn btn-sm btn-icon btn-outline-primary"
                                                           title="View">
                                                            <i class="icon-base ti tabler-eye"></i>
                                                        </a>
                                                        <form action="{{ route('tenant.students.documents.verify', $doc) }}"
                                                              method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit"
                                                                    class="btn btn-sm btn-icon btn-outline-{{ $doc->is_verified ? 'warning' : 'success' }}"
                                                                    title="{{ $doc->is_verified ? 'Unverify' : 'Verify' }}">
                                                                <i class="icon-base ti tabler-{{ $doc->is_verified ? 'shield-x' : 'shield-check' }}"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Change Status Modal --}}
<div class="modal fade" id="changeStatusModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form action="{{ route('tenant.students.status', $student) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Change Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-semibold">New Status</label>
                    <select name="status" class="form-select">
                        @foreach(['active','inactive','graduated','transferred','dropped'] as $s)
                            <option value="{{ $s }}"
                                {{ $student->status === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection