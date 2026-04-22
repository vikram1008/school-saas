@extends('layouts.superadmin.superadmin')

@section('title', 'Add New School')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Page Header --}}
    <div class="d-flex align-items-center mb-5">
        <a href="{{ route('superadmin.schools.index') }}" class="btn btn-icon btn-outline-secondary me-3">
            <i class="icon-base ti tabler-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-1">Provision New School</h4>
            <p class="text-muted mb-0 small">
                <i class="icon-base ti tabler-info-circle me-1"></i>
                Each school gets an isolated database, subdomain, and admin account.
            </p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-4">
            <i class="icon-base ti tabler-alert-circle me-1"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('superadmin.schools.store') }}" method="POST" enctype="multipart/form-data" id="provisionForm">
        @csrf

        <div class="row g-4">

            {{-- ── LEFT COLUMN ────────────────────────────────────────── --}}
            <div class="col-lg-8">

                {{-- ① Identity Card --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <span class="badge bg-primary rounded-pill">1</span>
                        <h5 class="mb-0">School Identity</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            {{-- Logo Upload --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">School Logo</label>
                                <div class="d-flex align-items-center gap-4">
                                    <div class="border rounded-3 d-flex align-items-center justify-content-center"
                                         style="width:100px;height:100px;background:#f8f8f8;flex-shrink:0;overflow:hidden;">
                                        <img id="logoPreview"
                                             src="{{ asset('assets/img/school-logo-placeholder.png') }}"
                                             style="max-width:90px;max-height:90px;object-fit:contain;display:none;"
                                             alt="Logo preview">
                                        <i id="logoIcon" class="ti tabler-building-bank fs-1 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file"
                                               name="logo"
                                               id="logoInput"
                                               class="form-control @error('logo') is-invalid @enderror"
                                               accept="image/png,image/jpeg,image/svg+xml,image/webp"
                                               onchange="previewLogo(this)">
                                        <div class="form-text">PNG, JPG, SVG, WebP · Max 2 MB · Transparent PNG recommended</div>
                                        @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                       id="schoolNameInput"
                                       class="form-control @error('school_name') is-invalid @enderror"
                                       value="{{ old('school_name') }}"
                                       placeholder="e.g. Springfield High School">
                                @error('school_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Hindi Name --}}
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">
                                    स्कूल का नाम
                                    <span class="badge bg-label-warning ms-1">हिं</span>
                                </label>
                                <input type="text"
                                       name="school_name_hi"
                                       class="form-control @error('school_name_hi') is-invalid @enderror"
                                       value="{{ old('school_name_hi') }}"
                                       placeholder="हिंदी में नाम">
                                @error('school_name_hi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Tagline --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Tagline / Motto</label>
                                <input type="text"
                                       name="tagline"
                                       class="form-control"
                                       value="{{ old('tagline') }}"
                                       placeholder="e.g. Knowledge is Power · ज्ञान ही शक्ति है">
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ② Contact Card --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <span class="badge bg-success rounded-pill">2</span>
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
                                           value="{{ old('email') }}"
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
                                           value="{{ old('website') }}"
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
                                           value="{{ old('phone') }}"
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
                                           value="{{ old('phone_alt') }}"
                                           placeholder="Secondary number">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ③ Address Card --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <span class="badge bg-info rounded-pill">3</span>
                        <h5 class="mb-0">Address</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label fw-semibold">Address Line 1</label>
                                <input type="text"
                                       name="address_line1"
                                       class="form-control"
                                       value="{{ old('address_line1') }}"
                                       placeholder="Building / Street / Locality">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">City</label>
                                <input type="text"
                                       name="city"
                                       class="form-control"
                                       value="{{ old('city') }}"
                                       placeholder="City">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">State</label>
                                <input type="text"
                                       name="state"
                                       class="form-control"
                                       value="{{ old('state') }}"
                                       placeholder="State">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-semibold">PIN Code</label>
                                <input type="text"
                                       name="pincode"
                                       class="form-control"
                                       value="{{ old('pincode') }}"
                                       placeholder="6-digit"
                                       maxlength="6">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-semibold">Country</label>
                                <input type="text"
                                       name="country"
                                       class="form-control"
                                       value="{{ old('country', 'India') }}">
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ④ Academic Card --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <span class="badge bg-warning rounded-pill">4</span>
                        <h5 class="mb-0">Academic Details <span class="text-muted small fw-normal">(optional)</span></h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Board / Affiliation</label>
                                <input type="text"
                                       name="board_affiliation"
                                       class="form-control"
                                       value="{{ old('board_affiliation') }}"
                                       placeholder="CBSE, RBSE, ICSE…">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">School Code</label>
                                <input type="text"
                                       name="school_code"
                                       class="form-control"
                                       value="{{ old('school_code') }}"
                                       placeholder="Board-assigned code">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">UDISE Code</label>
                                <input type="text"
                                       name="udise_code"
                                       class="form-control"
                                       value="{{ old('udise_code') }}"
                                       placeholder="11-digit UDISE+">
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ⑤ Access & Billing Card --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <span class="badge bg-danger rounded-pill">5</span>
                        <h5 class="mb-0">Access &amp; Billing</h5>
                        <span class="badge bg-label-danger ms-auto small">Super Admin Only</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-7">
                                <label class="form-label fw-semibold">
                                    Subdomain <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text"
                                           name="subdomain"
                                           id="subdomainInput"
                                           class="form-control @error('subdomain') is-invalid @enderror"
                                           value="{{ old('subdomain') }}"
                                           placeholder="springfield"
                                           oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9\-]/g, '')">
                                    <span class="input-group-text">.{{ config('tenancy.central_domains')[0] }}</span>
                                    @error('subdomain')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-text">Lowercase, numbers, hyphens only · min 3 chars</div>
                                <div id="subdomainPreview" class="mt-1 text-primary small fw-semibold"></div>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-semibold">
                                    Rate per Student <span class="text-danger">*</span>
                                </label>
                                <select name="per_student_rate"
                                        id="rateSelect"
                                        class="form-select @error('per_student_rate') is-invalid @enderror">
                                    <option value="">Select Rate</option>
                                    @foreach([10, 20, 30, 40, 50] as $rate)
                                        <option value="{{ $rate }}" {{ old('per_student_rate') == $rate ? 'selected' : '' }}>
                                            ₹{{ $rate }} / student / month
                                        </option>
                                    @endforeach
                                </select>
                                @error('per_student_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Monthly bill = active students × rate</div>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-semibold">
                                    Billing Cycle <span class="text-danger">*</span>
                                </label>
                                <select name="billing_cycle"
                                        class="form-select @error('billing_cycle') is-invalid @enderror">
                                    <option value="">Select Cycle</option>
                                    @foreach(['monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'half_yearly' => 'Half-Yearly', 'yearly' => 'Yearly'] as $v => $l)
                                        <option value="{{ $v }}" {{ old('billing_cycle') === $v ? 'selected' : '' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                                @error('billing_cycle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ⑥ Admin Credentials Card --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <span class="badge bg-secondary rounded-pill">6</span>
                        <h5 class="mb-0">Default School Admin</h5>
                    </div>
                    <div class="card-header border-top-0 pt-0 pb-2">
                        <p class="text-muted small mb-0">
                            <i class="ti tabler-info-circle me-1"></i>
                            This account is auto-created inside the school's isolated database.
                        </p>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Admin Full Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="admin_name"
                                       class="form-control @error('admin_name') is-invalid @enderror"
                                       value="{{ old('admin_name') }}"
                                       placeholder="e.g. Rajesh Kumar">
                                @error('admin_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Admin Email <span class="text-danger">*</span></label>
                                <input type="email"
                                       name="admin_email"
                                       class="form-control @error('admin_email') is-invalid @enderror"
                                       value="{{ old('admin_email') }}"
                                       placeholder="admin@school.com">
                                @error('admin_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Admin Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password"
                                           name="admin_password"
                                           id="adminPassword"
                                           class="form-control @error('admin_password') is-invalid @enderror"
                                           placeholder="Min 8 characters">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                        <i class="ti tabler-eye" id="passwordEyeIcon"></i>
                                    </button>
                                    @error('admin_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-text text-warning">
                                    <i class="ti tabler-lock me-1"></i>
                                    Share these credentials with the school admin securely.
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                        <i class="ti tabler-rocket me-1"></i> Provision School
                    </button>
                    <a href="{{ route('superadmin.schools.index') }}" class="btn btn-outline-secondary btn-lg">
                        Cancel
                    </a>
                </div>

            </div>

            {{-- ── RIGHT SIDEBAR ───────────────────────────────────────── --}}
            <div class="col-lg-4">

                {{-- What happens card --}}
                <div class="card mb-4 border-primary">
                    <div class="card-header bg-label-primary d-flex align-items-center gap-2">
                        <i class="ti tabler-rocket fs-5 text-primary"></i>
                        <h6 class="mb-0 fw-bold">What "Provision" does</h6>
                    </div>
                    <div class="card-body">
                        <ol class="ps-3 mb-0 small text-muted">
                            <li class="mb-2">Creates a <strong>Tenant</strong> record in the central database with all the info you enter.</li>
                            <li class="mb-2">Spins up an <strong>isolated MySQL database</strong> (e.g. <code>school_springfield</code>).</li>
                            <li class="mb-2">Runs all <strong>tenant migrations</strong> inside that database.</li>
                            <li class="mb-2">Registers the <strong>subdomain</strong> and points it to this school.</li>
                            <li class="mb-2">Creates the <strong>admin account</strong> and staff profile.</li>
                            <li>Generates the <strong>first billing cycle</strong>.</li>
                        </ol>
                    </div>
                </div>

                {{-- Live Billing Preview --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="ti tabler-calculator fs-5 text-warning"></i>
                        <h6 class="mb-0 fw-bold">Billing Preview</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Estimated monthly bills at selected rate:</p>
                        <table class="table table-sm table-borderless mb-0 small">
                            <tr>
                                <td class="text-muted ps-0">100 students</td>
                                <td class="text-end fw-semibold"><strong id="bill100">₹—</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">300 students</td>
                                <td class="text-end fw-semibold"><strong id="bill300">₹—</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">500 students</td>
                                <td class="text-end fw-semibold"><strong id="bill500">₹—</strong></td>
                            </tr>
                            <tr class="border-top">
                                <td class="text-muted ps-0">1000 students</td>
                                <td class="text-end fw-bold text-primary"><strong id="bill1000">₹—</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Tips --}}
                <div class="card border-warning">
                    <div class="card-header bg-label-warning d-flex align-items-center gap-2">
                        <i class="ti tabler-bulb fs-5 text-warning"></i>
                        <h6 class="mb-0 fw-bold">Tips</h6>
                    </div>
                    <div class="card-body small text-muted">
                        <ul class="ps-3 mb-0">
                            <li class="mb-2">Logo &amp; extra details can also be set later from the school's own settings page.</li>
                            <li class="mb-2">The subdomain cannot be changed after provisioning.</li>
                            <li>Academic codes (UDISE, Board) are optional but useful for official documents.</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
// Logo preview
function previewLogo(input) {
    const preview = document.getElementById('logoPreview');
    const icon    = document.getElementById('logoIcon');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src     = e.target.result;
            preview.style.display = '';
            icon.style.display    = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Auto-generate subdomain from school name
const nameInput      = document.getElementById('schoolNameInput');
const subdomainInput = document.getElementById('subdomainInput');
const preview        = document.getElementById('subdomainPreview');
const centralDomain  = '{{ config("tenancy.central_domains")[0] }}';

function slugify(str) {
    return str.toLowerCase()
              .replace(/[^a-z0-9\s\-]/g, '')
              .replace(/\s+/g, '-')
              .replace(/-+/g, '-')
              .replace(/^-|-$/g, '');
}

nameInput.addEventListener('input', function () {
    const slug = slugify(this.value);
    if (!subdomainInput.dataset.manuallyEdited) {
        subdomainInput.value = slug;
        updatePreview();
    }
});

subdomainInput.addEventListener('input', function () {
    this.dataset.manuallyEdited = 'true';
    updatePreview();
});

function updatePreview() {
    const sub = subdomainInput.value;
    preview.textContent = sub ? '→ https://' + sub + '.' + centralDomain : '';
}

// Live billing preview
const rateSelect = document.getElementById('rateSelect');
rateSelect.addEventListener('change', function () {
    const rate = parseInt(this.value) || 0;
    const fmt  = n => rate ? '₹' + (n * rate).toLocaleString('en-IN') : '₹—';
    document.getElementById('bill100').textContent  = fmt(100);
    document.getElementById('bill300').textContent  = fmt(300);
    document.getElementById('bill500').textContent  = fmt(500);
    document.getElementById('bill1000').textContent = fmt(1000);
});

// Password toggle
function togglePassword() {
    const input = document.getElementById('adminPassword');
    const icon  = document.getElementById('passwordEyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('tabler-eye', 'tabler-eye-off');
    } else {
        input.type = 'password';
        icon.classList.replace('tabler-eye-off', 'tabler-eye');
    }
}

// Prevent double-submit
document.getElementById('provisionForm').addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Provisioning…';
});
</script>
@endpush

@endsection