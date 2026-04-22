@extends('layouts.superadmin.superadmin')

@section('title', 'Edit · ' . $school->school_name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Page Header --}}
    <div class="d-flex align-items-center mb-5">
        <a href="{{ route('superadmin.schools.show', $school) }}" class="btn btn-icon btn-outline-secondary me-3">
            <i class="icon-base ti tabler-arrow-left"></i>
        </a>
        <div class="d-flex align-items-center gap-3 flex-grow-1">
            {{-- Current Logo --}}
            <img src="{{ $school->logo_url }}"
                 alt="{{ $school->school_name }}"
                 class="rounded border"
                 style="height:52px;width:auto;max-width:120px;object-fit:contain;background:#f8f8f8;padding:4px;">
            <div>
                <h4 class="fw-bold mb-0">Edit School</h4>
                <p class="text-muted mb-0 small">
                    <i class="icon-base ti tabler-id me-1"></i>
                    <code>{{ $school->id }}</code>
                    &nbsp;·&nbsp;
                    <i class="icon-base ti tabler-database me-1"></i>
                    <code>school_{{ $school->id }}</code>
                </p>
            </div>
        </div>
        <span class="badge bg-label-{{ $school->is_active ? 'success' : 'danger' }} fs-6 px-3 py-2">
            <i class="ti tabler-{{ $school->is_active ? 'circle-check' : 'circle-x' }} me-1"></i>
            {{ $school->is_active ? 'Active' : 'Inactive' }}
        </span>
    </div>

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
            <strong>Please fix the following:</strong>
            <ul class="mb-0 mt-1 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('superadmin.schools.update', $school) }}"
          method="POST"
          enctype="multipart/form-data"
          id="editSchoolForm">
        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- ── LEFT COLUMN ─────────────────────────────────────── --}}
            <div class="col-lg-8">

                {{-- ① Identity --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="ti tabler-building fs-5 text-primary"></i>
                        <h5 class="mb-0">School Identity</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            {{-- Logo Management --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">School Logo</label>
                                <div class="d-flex align-items-start gap-4">

                                    {{-- Current logo / preview --}}
                                    <div class="border rounded-3 d-flex align-items-center justify-content-center position-relative"
                                         style="width:110px;height:110px;background:#f8f8f8;flex-shrink:0;overflow:hidden;">
                                        <img id="logoPreview"
                                             src="{{ $school->logo_url }}"
                                             alt="Logo"
                                             style="max-width:100px;max-height:100px;object-fit:contain;">
                                    </div>

                                    <div class="flex-grow-1">
                                        <input type="file"
                                               name="logo"
                                               id="logoInput"
                                               class="form-control @error('logo') is-invalid @enderror"
                                               accept="image/png,image/jpeg,image/svg+xml,image/webp"
                                               onchange="previewLogo(this)">
                                        <div class="form-text">PNG, JPG, SVG, WebP · Max 2 MB · Leave blank to keep current</div>
                                        @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror

                                        @if($school->logo)
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="removeLogo">
                                                <label class="form-check-label small text-danger" for="removeLogo">
                                                    <i class="ti tabler-trash me-1"></i>Remove current logo
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- School Name --}}
                            <div class="col-md-7">
                                <label class="form-label fw-semibold">
                                    School Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="school_name"
                                       class="form-control @error('school_name') is-invalid @enderror"
                                       value="{{ old('school_name', $school->school_name) }}"
                                       placeholder="Full school name">
                                @error('school_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-semibold">
                                    स्कूल का नाम <span class="badge bg-label-warning ms-1">हिं</span>
                                </label>
                                <input type="text"
                                       name="school_name_hi"
                                       class="form-control"
                                       value="{{ old('school_name_hi', $school->school_name_hi) }}"
                                       placeholder="हिंदी में नाम">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Tagline / Motto</label>
                                <input type="text"
                                       name="tagline"
                                       class="form-control"
                                       value="{{ old('tagline', $school->tagline) }}"
                                       placeholder="e.g. Knowledge is Power">
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ② Contact --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="ti tabler-phone fs-5 text-success"></i>
                        <h5 class="mb-0">Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Official Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti tabler-mail"></i></span>
                                    <input type="email"
                                           name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', $school->email) }}"
                                           placeholder="school@example.com">
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Website</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti tabler-world"></i></span>
                                    <input type="url"
                                           name="website"
                                           class="form-control"
                                           value="{{ old('website', $school->website) }}"
                                           placeholder="https://myschool.edu.in">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Primary Phone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti tabler-phone"></i></span>
                                    <input type="text"
                                           name="phone"
                                           class="form-control"
                                           value="{{ old('phone', $school->phone) }}"
                                           placeholder="+91 98765 43210">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Alternate Phone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti tabler-phone-plus"></i></span>
                                    <input type="text"
                                           name="phone_alt"
                                           class="form-control"
                                           value="{{ old('phone_alt', $school->phone_alt) }}"
                                           placeholder="Secondary number">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ③ Address --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="ti tabler-map-pin fs-5 text-info"></i>
                        <h5 class="mb-0">Address</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label fw-semibold">Address Line 1</label>
                                <input type="text"
                                       name="address_line1"
                                       class="form-control"
                                       value="{{ old('address_line1', $school->address_line1) }}"
                                       placeholder="Building / Street / Locality">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">City</label>
                                <input type="text"
                                       name="city"
                                       class="form-control"
                                       value="{{ old('city', $school->city) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">State</label>
                                <input type="text"
                                       name="state"
                                       class="form-control"
                                       value="{{ old('state', $school->state) }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-semibold">PIN Code</label>
                                <input type="text"
                                       name="pincode"
                                       class="form-control"
                                       value="{{ old('pincode', $school->pincode) }}"
                                       maxlength="6">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-semibold">Country</label>
                                <input type="text"
                                       name="country"
                                       class="form-control"
                                       value="{{ old('country', $school->country ?? 'India') }}">
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ④ Academic --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="ti tabler-certificate fs-5 text-warning"></i>
                        <h5 class="mb-0">Academic Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Board / Affiliation</label>
                                <input type="text"
                                       name="board_affiliation"
                                       class="form-control"
                                       value="{{ old('board_affiliation', $school->board_affiliation) }}"
                                       placeholder="CBSE, RBSE, ICSE…">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">School Code</label>
                                <input type="text"
                                       name="school_code"
                                       class="form-control"
                                       value="{{ old('school_code', $school->school_code) }}"
                                       placeholder="Board-assigned code">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">UDISE Code</label>
                                <input type="text"
                                       name="udise_code"
                                       class="form-control"
                                       value="{{ old('udise_code', $school->udise_code) }}"
                                       placeholder="11-digit">
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ⑤ Billing & Status (super admin only) --}}
                <div class="card mb-4 border-danger border-opacity-25">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="ti tabler-receipt-rupee fs-5 text-danger"></i>
                        <h5 class="mb-0">Billing &amp; Status</h5>
                        <span class="badge bg-label-danger ms-auto small">Super Admin Only</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Rate per Student <span class="text-danger">*</span></label>
                                <select name="per_student_rate"
                                        id="rateSelect"
                                        class="form-select @error('per_student_rate') is-invalid @enderror">
                                    @foreach([10, 20, 30, 40, 50] as $rate)
                                        <option value="{{ $rate }}"
                                            {{ old('per_student_rate', $school->per_student_rate) == $rate ? 'selected' : '' }}>
                                            ₹{{ $rate }} / student / month
                                        </option>
                                    @endforeach
                                </select>
                                @error('per_student_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Billing Cycle</label>
                                <select name="billing_cycle"
                                        class="form-select @error('billing_cycle') is-invalid @enderror">
                                    @foreach(['monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'half_yearly' => 'Half-Yearly', 'yearly' => 'Yearly'] as $v => $l)
                                        <option value="{{ $v }}"
                                            {{ old('billing_cycle', $school->billing_cycle) === $v ? 'selected' : '' }}>
                                            {{ $l }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('billing_cycle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="is_active"
                                        class="form-select @error('is_active') is-invalid @enderror">
                                    <option value="1" {{ old('is_active', $school->is_active) == 1 ? 'selected' : '' }}>
                                        ✅ Active
                                    </option>
                                    <option value="0" {{ old('is_active', $school->is_active) == 0 ? 'selected' : '' }}>
                                        ⛔ Inactive
                                    </option>
                                </select>
                                @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Inactive schools cannot log in.</div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="ti tabler-device-floppy me-1"></i> Save Changes
                    </button>
                    <a href="{{ route('superadmin.schools.show', $school) }}" class="btn btn-outline-secondary btn-lg">
                        Cancel
                    </a>
                </div>

            </div>

            {{-- ── RIGHT SIDEBAR ──────────────────────────────────────── --}}
            <div class="col-lg-4">

                {{-- Domain card (read-only) --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="ti tabler-world fs-5 text-info"></i>
                        <h6 class="mb-0 fw-bold">Domain</h6>
                        <span class="badge bg-label-secondary ms-auto small">Read Only</span>
                    </div>
                    <div class="card-body">
                        @forelse($school->domains as $domain)
                            <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded mb-2">
                                <div>
                                    <i class="ti tabler-link me-1 text-primary"></i>
                                    <span class="fw-semibold small">{{ $domain->domain }}</span>
                                </div>
                                <a href="http://{{ $domain->domain }}" target="_blank" class="btn btn-sm btn-outline-primary py-1">
                                    <i class="ti tabler-external-link"></i>
                                </a>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">No domains assigned.</p>
                        @endforelse
                        <p class="text-muted small mt-2 mb-0">
                            <i class="ti tabler-info-circle me-1"></i>
                            Domain changes require manual DB update.
                        </p>
                    </div>
                </div>

                {{-- Billing preview --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="ti tabler-calculator fs-5 text-warning"></i>
                        <h6 class="mb-0 fw-bold">Billing Preview</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-2">At current rate (₹{{ $school->per_student_rate }}/student):</p>
                        <table class="table table-sm table-borderless mb-0 small">
                            <tr>
                                <td class="text-muted ps-0">100 students</td>
                                <td class="text-end fw-semibold" id="bill100">₹{{ number_format(100 * $school->per_student_rate) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">300 students</td>
                                <td class="text-end fw-semibold" id="bill300">₹{{ number_format(300 * $school->per_student_rate) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">500 students</td>
                                <td class="text-end fw-semibold" id="bill500">₹{{ number_format(500 * $school->per_student_rate) }}</td>
                            </tr>
                            <tr class="border-top">
                                <td class="text-muted ps-0">1000 students</td>
                                <td class="text-end fw-bold text-primary" id="bill1000">₹{{ number_format(1000 * $school->per_student_rate) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Important notes --}}
                <div class="card border-warning">
                    <div class="card-header bg-label-warning d-flex align-items-center gap-2">
                        <i class="ti tabler-alert-triangle fs-5 text-warning"></i>
                        <h6 class="mb-0 fw-bold">Important Notes</h6>
                    </div>
                    <div class="card-body small text-muted">
                        <ul class="ps-3 mb-0">
                            <li class="mb-2">All changes reflect <strong>immediately</strong> in the school portal — single source of truth.</li>
                            <li class="mb-2"><strong>Rate &amp; Billing Cycle</strong> changes take effect from the <em>next</em> billing cycle.</li>
                            <li><strong>Inactive</strong> prevents all school users from logging in.</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { document.getElementById('logoPreview').src = e.target.result; };
        reader.readAsDataURL(input.files[0]);
    }
}

// Live billing preview on rate change
document.getElementById('rateSelect')?.addEventListener('change', function () {
    const rate = parseInt(this.value) || 0;
    const fmt  = n => '₹' + (n * rate).toLocaleString('en-IN');
    document.getElementById('bill100').textContent  = fmt(100);
    document.getElementById('bill300').textContent  = fmt(300);
    document.getElementById('bill500').textContent  = fmt(500);
    document.getElementById('bill1000').textContent = fmt(1000);
});
</script>
@endpush

@endsection