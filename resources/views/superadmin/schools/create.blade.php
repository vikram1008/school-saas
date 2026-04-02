@extends('layouts.superadmin.superadmin')

@section('title', 'Add New School')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('superadmin.schools.index') }}" class="btn btn-icon btn-outline-secondary me-3">
                <i class="icon-base ti tabler-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-1">Add New School</h4>
                <p class="text-muted mb-0">Provision a new school with an isolated database.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <form action="{{ route('superadmin.schools.store') }}" method="POST">
                    @csrf

                    {{-- Basic Information --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bx bx-buildings me-2"></i>School Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-12">
                                    <label class="form-label fw-semibold">School Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="school_name"
                                           class="form-control @error('school_name') is-invalid @enderror"
                                           value="{{ old('school_name') }}"
                                           placeholder="e.g. Springfield High School"
                                           id="schoolNameInput">
                                    @error('school_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Official Email <span class="text-danger">*</span></label>
                                    <input type="email"
                                           name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}"
                                           placeholder="admin@springfieldhigh.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phone</label>
                                    <input type="text"
                                           name="phone"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           value="{{ old('phone') }}"
                                           placeholder="+91 98765 43210">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Address</label>
                                    <textarea name="address"
                                              class="form-control @error('address') is-invalid @enderror"
                                              rows="2"
                                              placeholder="School address...">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Subdomain & Plan --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bx bx-globe me-2"></i>Access & Subscription</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Subdomain <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text"
                                               name="subdomain"
                                               id="subdomainInput"
                                               class="form-control @error('subdomain') is-invalid @enderror"
                                               value="{{ old('subdomain') }}"
                                               placeholder="springfield"
                                               oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9\-]/g, '')">
                                        <span class="input-group-text">.{{ config('tenancy.central_domains')[0] }}</span>
                                        @error('subdomain')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">
                                        Lowercase letters, numbers, and hyphens only. Min 3 characters.
                                    </div>
                                    <div id="subdomainPreview" class="mt-1 text-primary small fw-semibold"></div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        Rate per Student <span class="text-danger">*</span>
                                    </label>
                                    <select name="per_student_rate"
                                            class="form-select @error('per_student_rate') is-invalid @enderror">
                                        <option value="">Select Rate</option>
                                        <option value="10" {{ old('per_student_rate') == '10' ? 'selected' : '' }}>
                                            ₹10 / student / month
                                        </option>
                                        <option value="20" {{ old('per_student_rate') == '20' ? 'selected' : '' }}>
                                            ₹20 / student / month
                                        </option>
                                        <option value="30" {{ old('per_student_rate') == '30' ? 'selected' : '' }}>
                                            ₹30 / student / month
                                        </option>
                                        <option value="40" {{ old('per_student_rate') == '40' ? 'selected' : '' }}>
                                            ₹40 / student / month
                                        </option>
                                        <option value="50" {{ old('per_student_rate') == '50' ? 'selected' : '' }}>
                                            ₹50 / student / month
                                        </option>
                                    </select>
                                    @error('per_student_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Monthly bill = active students × selected rate</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        Billing Cycle <span class="text-danger">*</span>
                                    </label>
                                    <select name="billing_cycle"
                                            class="form-select @error('billing_cycle') is-invalid @enderror">
                                        <option value="">Select Cycle</option>
                                        @foreach([
                                            'monthly'     => 'Monthly',
                                            'quarterly'   => 'Quarterly',
                                            'half_yearly' => 'Half-Yearly',
                                            'yearly'      => 'Yearly',
                                        ] as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('billing_cycle') === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('billing_cycle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Default School Admin --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="icon-base ti tabler-user-shield me-2"></i>Default School Admin
                            </h5>
                            <p class="text-muted small mb-0 mt-1">
                                This account will be auto-created inside the school's database.
                            </p>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Admin Full Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        name="admin_name"
                                        class="form-control @error('admin_name') is-invalid @enderror"
                                        value="{{ old('admin_name') }}"
                                        placeholder="e.g. Rajesh Kumar">
                                    @error('admin_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Admin Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                        name="admin_email"
                                        class="form-control @error('admin_email') is-invalid @enderror"
                                        value="{{ old('admin_email') }}"
                                        placeholder="e.g. rajesh@springfield.com">
                                    @error('admin_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Admin Password <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="password"
                                            name="admin_password"
                                            id="adminPassword"
                                            class="form-control @error('admin_password') is-invalid @enderror"
                                            placeholder="Min 8 characters">
                                        <button class="btn btn-outline-secondary"
                                                type="button"
                                                onclick="togglePassword()">
                                            <i class="icon-base ti tabler-eye" id="passwordEyeIcon"></i>
                                        </button>
                                        @error('admin_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">
                                        Share these credentials with the school admin securely.
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-cloud-upload me-1"></i> Provision School
                        </button>
                        <a href="{{ route('superadmin.schools.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>

                </form>
            </div>

            {{-- Sidebar Info --}}
            <div class="col-lg-4">
                <div class="card border-primary">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="icon-base ti tabler-info-circle me-1 text-primary"></i>What happens when you click "Provision School"?</h6>
                        <ol class="ps-3 mb-0 text-muted small">
                            <li class="mb-2">A new <strong>Tenant</strong> record is created in the central database.</li>
                            <li class="mb-2">An isolated <strong>school database</strong> is automatically created (e.g. <code>school_springfield-high</code>).</li>
                            <li class="mb-2">All <strong>tenant migrations</strong> run inside the new database (users, profiles, etc.).</li>
                            <li class="mb-2">The <strong>subdomain</strong> is registered and pointed to this school.</li>
                            <li>The school can now be accessed at their unique URL.</li>
                        </ol>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2">
                            <i class="icon-base ti tabler-calculator me-1 text-warning"></i>Billing Preview
                        </h6>
                        <p class="text-muted small mb-2">Estimated monthly bills:</p>
                        <table class="table table-sm table-borderless mb-0 small">
                            <tr>
                                <td class="text-muted">100 students</td>
                                <td><strong id="bill100">₹—</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">300 students</td>
                                <td><strong id="bill300">₹—</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">500 students</td>
                                <td><strong id="bill500">₹—</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        // Auto-generate subdomain from school name
        const nameInput      = document.getElementById('schoolNameInput');
        const subdomainInput = document.getElementById('subdomainInput');
        const preview        = document.getElementById('subdomainPreview');
        const dbPreview      = document.getElementById('dbPreview');
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
            }
            dbPreview.textContent = slug ? 'school_' + slug : 'school_[slug-of-name]';
            updatePreview();
        });

        subdomainInput.addEventListener('input', function () {
            this.dataset.manuallyEdited = 'true';
            updatePreview();
        });

        function updatePreview() {
            const sub = subdomainInput.value;
            preview.textContent = sub
                ? '→ ' + sub + '.' + centralDomain
                : '';
        }
    </script>
    <script>
        // Live billing preview
        const rateSelect = document.querySelector('select[name="per_student_rate"]');
        rateSelect.addEventListener('change', function () {
            const rate = parseInt(this.value) || 0;
            document.getElementById('bill100').textContent = rate ? '₹' + (100 * rate).toLocaleString('en-IN') : '₹—';
            document.getElementById('bill300').textContent = rate ? '₹' + (300 * rate).toLocaleString('en-IN') : '₹—';
            document.getElementById('bill500').textContent = rate ? '₹' + (500 * rate).toLocaleString('en-IN') : '₹—';
        });

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
    </script>
    @endpush

@endsection