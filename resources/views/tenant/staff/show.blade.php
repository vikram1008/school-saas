@php
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.tenant')

@section('title', $staff->full_name)

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-user-view.scss'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tenant.staff.index') }}">Staff</a></li>
            <li class="breadcrumb-item active">{{ $staff->full_name }}</li>
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- LEFT SIDEBAR --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card mb-4">
                <div class="card-body pt-4">

                    {{-- Photo --}}
                    <div class="text-center mb-3">
                        @if($staff->photo)
                            <img src="{{ Storage::url($staff->photo) }}"
                                 class="img-fluid rounded-circle mb-3"
                                 width="110" height="110"
                                 style="object-fit:cover; border:4px solid var(--bs-primary-bg-subtle)">
                        @else
                            <div class="avatar avatar-xl mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-label-primary"
                                      style="font-size:2rem;">
                                    {{ strtoupper(substr($staff->first_name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <h5 class="fw-bold mb-0">{{ $staff->full_name }}</h5>
                        @if($staff->first_name_hi)
                            <p class="text-muted small mb-1">{{ $staff->full_name_hi }}</p>
                        @endif
                        @php
                            $typeColors = ['teaching'=>'primary','non_teaching'=>'info','administrative'=>'warning'];
                        @endphp
                        <span class="badge bg-label-{{ $typeColors[$staff->staff_type] ?? 'secondary' }} mt-1">
                            {{ \App\Models\StaffProfile::typeLabels()[$staff->staff_type] ?? '—' }}
                        </span>
                    </div>

                    {{-- Status --}}
                    <div class="text-center mb-3">
                        @php
                            $statusColors = ['active'=>'success','inactive'=>'secondary','on_leave'=>'warning','resigned'=>'danger','terminated'=>'dark'];
                        @endphp
                        <span class="badge bg-{{ $statusColors[$staff->status] ?? 'secondary' }} fs-6 px-3 py-2">
                            {{ ucfirst(str_replace('_', ' ', $staff->status)) }}
                        </span>
                    </div>

                    <hr>

                    <ul class="list-unstyled mb-0">
                        <li class="mb-3 d-flex">
                            <span class="fw-semibold me-2 text-nowrap" style="min-width:130px">Employee Code:</span>
                            <span class="text-muted">{{ $staff->employee_code }}</span>
                        </li>
                        <li class="mb-3 d-flex">
                            <span class="fw-semibold me-2 text-nowrap" style="min-width:130px">Designation:</span>
                            <span class="text-muted">{{ $staff->designation ?? '—' }}</span>
                        </li>
                        <li class="mb-3 d-flex">
                            <span class="fw-semibold me-2 text-nowrap" style="min-width:130px">Department:</span>
                            <span class="text-muted">{{ $staff->department ?? '—' }}</span>
                        </li>
                        <li class="mb-3 d-flex">
                            <span class="fw-semibold me-2 text-nowrap" style="min-width:130px">Gender:</span>
                            <span class="text-muted">{{ ucfirst($staff->gender ?? '—') }}</span>
                        </li>
                        <li class="mb-3 d-flex">
                            <span class="fw-semibold me-2 text-nowrap" style="min-width:130px">DOB:</span>
                            <span class="text-muted">{{ $staff->date_of_birth?->format('d M Y') ?? '—' }}</span>
                        </li>
                        <li class="mb-3 d-flex">
                            <span class="fw-semibold me-2 text-nowrap" style="min-width:130px">Joining Date:</span>
                            <span class="text-muted">{{ $staff->joining_date?->format('d M Y') ?? '—' }}</span>
                        </li>
                        <li class="mb-3 d-flex">
                            <span class="fw-semibold me-2 text-nowrap" style="min-width:130px">Mobile:</span>
                            <span class="text-muted">{{ $staff->phone ?? '—' }}</span>
                        </li>
                        <li class="mb-3 d-flex">
                            <span class="fw-semibold me-2 text-nowrap" style="min-width:130px">Email:</span>
                            <span class="text-muted small">{{ $staff->user?->email ?? '—' }}</span>
                        </li>
                        @if($staff->salary)
                        <li class="mb-3 d-flex">
                            <span class="fw-semibold me-2 text-nowrap" style="min-width:130px">Salary:</span>
                            <span class="text-success fw-bold">₹{{ number_format($staff->salary) }}/mo</span>
                        </li>
                        @endif
                        @if($staff->experience_years)
                        <li class="d-flex">
                            <span class="fw-semibold me-2 text-nowrap" style="min-width:130px">Experience:</span>
                            <span class="text-muted">{{ $staff->experience_years }} years</span>
                        </li>
                        @endif
                    </ul>

                    <hr>

                    {{-- Actions --}}
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('tenant.staff.edit', $staff) }}" class="btn btn-primary">
                            <i class="icon-base ti tabler-edit me-1"></i> Edit
                        </a>
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="window.print()">
                            <i class="icon-base ti tabler-printer me-1"></i> Print
                        </button>
                        <button type="button" class="btn btn-outline-warning"
                                data-bs-toggle="modal" data-bs-target="#changeStatusModal">
                            <i class="icon-base ti tabler-refresh me-1"></i> Change Status
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT CONTENT --}}
        <div class="col-xl-8 col-lg-7">
            <ul class="nav nav-pills flex-column flex-md-row mb-4 gap-2 gap-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="#professional" data-bs-toggle="tab">
                        <i class="icon-base ti tabler-briefcase me-1"></i>Professional
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#identity" data-bs-toggle="tab">
                        <i class="icon-base ti tabler-id me-1"></i>Identity
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#address" data-bs-toggle="tab">
                        <i class="icon-base ti tabler-map-pin me-1"></i>Address
                    </a>
                </li>
                @if($staff->staff_type === 'teaching')
                <li class="nav-item">
                    <a class="nav-link" href="#subjects" data-bs-toggle="tab">
                        <i class="icon-base ti tabler-book me-1"></i>Subjects
                        @if($staff->subjectAssignments->count())
                            <span class="badge bg-primary rounded-pill ms-1">{{ $staff->subjectAssignments->count() }}</span>
                        @endif
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link" href="#documents" data-bs-toggle="tab">
                        <i class="icon-base ti tabler-file me-1"></i>Documents
                        @if($staff->documents->count())
                            <span class="badge bg-primary rounded-pill ms-1">{{ $staff->documents->count() }}</span>
                        @endif
                    </a>
                </li>
            </ul>

            <div class="tab-content">

                {{-- Professional Tab --}}
                <div class="tab-pane fade show active" id="professional">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="icon-base ti tabler-briefcase me-2 text-primary"></i>
                                Professional Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-sm-6">
                                    <p class="text-muted small mb-1">Designation</p>
                                    <p class="fw-semibold mb-0">{{ $staff->designation ?? '—' }}</p>
                                    @if($staff->designation_hi)
                                        <p class="text-muted small mb-0">
                                            <span class="badge bg-label-warning me-1">हिं</span>
                                            {{ $staff->designation_hi }}
                                        </p>
                                    @endif
                                </div>
                                <div class="col-sm-6">
                                    <p class="text-muted small mb-1">Department</p>
                                    <p class="fw-semibold mb-0">{{ $staff->department ?? '—' }}</p>
                                    @if($staff->department_hi)
                                        <p class="text-muted small mb-0">
                                            <span class="badge bg-label-warning me-1">हिं</span>
                                            {{ $staff->department_hi }}
                                        </p>
                                    @endif
                                </div>
                                <div class="col-sm-6">
                                    <p class="text-muted small mb-1">Qualification</p>
                                    <p class="fw-semibold mb-0">{{ $staff->qualification ?? '—' }}</p>
                                    @if($staff->qualification_hi)
                                        <p class="text-muted small mb-0">
                                            <span class="badge bg-label-warning me-1">हिं</span>
                                            {{ $staff->qualification_hi }}
                                        </p>
                                    @endif
                                </div>
                                <div class="col-sm-3">
                                    <p class="text-muted small mb-1">Experience</p>
                                    <p class="fw-semibold mb-0">{{ $staff->experience_years ?? 0 }} years</p>
                                </div>
                                <div class="col-sm-3">
                                    <p class="text-muted small mb-1">Employment Type</p>
                                    <span class="badge bg-label-secondary">
                                        {{ ucfirst(str_replace('_', ' ', $staff->employment_type ?? '—')) }}
                                    </span>
                                </div>
                                <div class="col-sm-4">
                                    <p class="text-muted small mb-1">Joining Date</p>
                                    <p class="fw-semibold mb-0">{{ $staff->joining_date?->format('d M Y') ?? '—' }}</p>
                                </div>
                                <div class="col-sm-4">
                                    <p class="text-muted small mb-1">Monthly Salary</p>
                                    <p class="fw-bold text-success mb-0">
                                        {{ $staff->salary ? '₹'.number_format($staff->salary) : '—' }}
                                    </p>
                                </div>
                                <div class="col-sm-4">
                                    <p class="text-muted small mb-1">Blood Group</p>
                                    <span class="badge bg-label-danger">{{ $staff->blood_group ?? '—' }}</span>
                                </div>
                                @if($staff->emergency_contact_name)
                                <div class="col-12">
                                    <div class="border rounded p-3 bg-light">
                                        <p class="fw-semibold small mb-1">
                                            <i class="icon-base ti tabler-urgent me-1 text-danger"></i>
                                            Emergency Contact
                                        </p>
                                        <p class="mb-0">
                                            {{ $staff->emergency_contact_name }}
                                            &nbsp;·&nbsp;
                                            {{ $staff->emergency_contact_phone }}
                                        </p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Identity Tab --}}
                <div class="tab-pane fade" id="identity">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="icon-base ti tabler-id me-2 text-warning"></i>
                                Identity / पहचान
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-sm-4">
                                    <p class="text-muted small mb-1">Aadhaar Number</p>
                                    <p class="fw-semibold mb-0">
                                        {{ $staff->aadhaar_number
                                            ? 'XXXX XXXX '.substr($staff->aadhaar_number,-4)
                                            : '—' }}
                                    </p>
                                </div>
                                <div class="col-sm-4">
                                    <p class="text-muted small mb-1">PAN Number</p>
                                    <p class="fw-semibold font-monospace mb-0">
                                        {{ $staff->pan_number ?? '—' }}
                                    </p>
                                </div>
                                <div class="col-sm-4">
                                    <p class="text-muted small mb-1">ID Proof</p>
                                    <p class="fw-semibold mb-0">
                                        {{ ucfirst(str_replace('_',' ',$staff->id_proof_type ?? '')) }}
                                        @if($staff->id_proof_number)
                                            <br><span class="text-muted font-monospace small">{{ $staff->id_proof_number }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="text-muted small mb-1">WhatsApp</p>
                                    <p class="fw-semibold mb-0">{{ $staff->whatsapp ?? '—' }}</p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="text-muted small mb-1">Login Email</p>
                                    <p class="fw-semibold mb-0">{{ $staff->user?->email ?? '—' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Address Tab --}}
                <div class="tab-pane fade" id="address">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="icon-base ti tabler-map-pin me-2 text-info"></i>
                                Address / पता
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($staff->address)
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="border rounded p-3 h-100">
                                            <h6 class="fw-bold text-primary mb-2">Permanent / स्थायी</h6>
                                            @php
                                                $permAddr = collect([
                                                    $staff->address->perm_house_no,
                                                    $staff->address->perm_street,
                                                    $staff->address->perm_village_city,
                                                    $staff->address->perm_tehsil,
                                                    $staff->address->perm_district,
                                                    $staff->address->perm_state,
                                                    $staff->address->perm_pincode,
                                                ])->filter()->implode(', ');
                                                $permAddrHi = collect([
                                                    $staff->address->perm_house_no_hi,
                                                    $staff->address->perm_street_hi,
                                                    $staff->address->perm_village_city_hi,
                                                    $staff->address->perm_tehsil_hi,
                                                    $staff->address->perm_district_hi,
                                                    $staff->address->perm_state_hi,
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
                                            <h6 class="fw-bold text-success mb-2">Current Address</h6>
                                            @if($staff->address->same_as_permanent)
                                                <p class="text-muted mb-0">Same as permanent address</p>
                                            @else
                                                @php
                                                    $currAddr = collect([
                                                        $staff->address->curr_house_no,
                                                        $staff->address->curr_street,
                                                        $staff->address->curr_village_city,
                                                        $staff->address->curr_tehsil,
                                                        $staff->address->curr_district,
                                                        $staff->address->curr_state,
                                                        $staff->address->curr_pincode,
                                                    ])->filter()->implode(', ');
                                                @endphp
                                                <p class="mb-0">{{ $currAddr ?: '—' }}</p>
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

                {{-- Subjects Tab (teaching only) --}}
                @if($staff->staff_type === 'teaching')
                <div class="tab-pane fade" id="subjects">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="icon-base ti tabler-book me-2 text-info"></i>
                                Subject Assignments
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($staff->subjectAssignments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Class</th>
                                                <th>Section</th>
                                                <th>Subject</th>
                                                <th>विषय (हिं)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($staff->subjectAssignments as $assignment)
                                                <tr>
                                                    <td>{{ $assignment->class?->name ?? '—' }}</td>
                                                    <td>{{ $assignment->section?->name ?? 'All' }}</td>
                                                    <td class="fw-semibold">{{ $assignment->subject_name }}</td>
                                                    <td class="text-muted">{{ $assignment->subject_name_hi ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center py-4 mb-0">
                                    No subject assignments yet.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- Documents Tab --}}
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
                                $allTypes = \App\Models\StaffDocument::typeLabels();
                                $uploaded = $staff->documents->keyBy('document_type');
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
                                                    <p class="fw-semibold small mb-0">{{ $label }}</p>
                                                    @if($doc)
                                                        <p class="text-muted small mb-0">{{ $doc->original_name }}</p>
                                                    @else
                                                        <p class="text-muted small mb-0">Not uploaded</p>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($doc)
                                                <div class="d-flex gap-1">
                                                    @if($doc->is_verified)
                                                        <span class="badge bg-success" title="Verified">
                                                            <i class="icon-base ti tabler-shield-check"></i>
                                                        </span>
                                                    @endif
                                                    <a href="{{ Storage::url($doc->file_path) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-icon btn-outline-primary">
                                                        <i class="icon-base ti tabler-eye"></i>
                                                    </a>
                                                    <form action="{{ route('tenant.staff.documents.verify', $doc) }}"
                                                          method="POST">
                                                        @csrf @method('PATCH')
                                                        <button type="submit"
                                                                class="btn btn-sm btn-icon btn-outline-{{ $doc->is_verified ? 'warning' : 'success' }}"
                                                                title="{{ $doc->is_verified ? 'Unverify' : 'Verify' }}">
                                                            <i class="icon-base ti tabler-{{ $doc->is_verified ? 'shield-x' : 'shield-check' }}"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
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

{{-- Change Status Modal --}}
<div class="modal fade" id="changeStatusModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form action="{{ route('tenant.staff.status', $staff) }}" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Change Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <select name="status" class="form-select">
                        @foreach(['active'=>'Active','inactive'=>'Inactive','on_leave'=>'On Leave','resigned'=>'Resigned','terminated'=>'Terminated'] as $val=>$lbl)
                            <option value="{{ $val }}"
                                {{ $staff->status === $val ? 'selected' : '' }}>
                                {{ $lbl }}
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