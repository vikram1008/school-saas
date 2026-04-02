@extends('layouts.superadmin.superadmin')

@section('title', 'Edit ' . $school->school_name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('superadmin.schools.show', $school) }}" class="btn btn-icon btn-outline-secondary me-3">
            <i class="icon-base ti tabler-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">Edit School</h4>
            <p class="text-muted mb-0 small">
                <i class="icon-base ti tabler-id me-1"></i>
                Tenant ID: <code>{{ $school->id }}</code>
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('superadmin.schools.update', $school) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Success Alert --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible mb-4" role="alert">
                        <i class="icon-base ti tabler-circle-check me-1"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- School Information --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="icon-base ti tabler-building me-2 text-primary fs-5"></i>
                        <h5 class="mb-0">School Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    School Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="school_name"
                                       class="form-control @error('school_name') is-invalid @enderror"
                                       value="{{ old('school_name', $school->school_name) }}"
                                       placeholder="e.g. Springfield High School">
                                @error('school_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Official Email <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                       name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $school->email) }}"
                                       placeholder="admin@school.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text"
                                       name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $school->phone) }}"
                                       placeholder="+91 98765 43210">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Address</label>
                                <textarea name="address"
                                          rows="2"
                                          class="form-control @error('address') is-invalid @enderror"
                                          placeholder="School address...">{{ old('address', $school->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Billing & Status --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="icon-base ti tabler-settings me-2 text-warning fs-5"></i>
                        <h5 class="mb-0">Billing & Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Rate per Student <span class="text-danger">*</span>
                                </label>
                                <select name="per_student_rate"
                                        class="form-select @error('per_student_rate') is-invalid @enderror">
                                    @foreach([10, 20, 30, 40, 50] as $rate)
                                        <option value="{{ $rate }}"
                                            {{ old('per_student_rate', $school->per_student_rate) == $rate ? 'selected' : '' }}>
                                            ₹{{ $rate }} / student / month
                                        </option>
                                    @endforeach
                                </select>
                                @error('per_student_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="is_active"
                                        class="form-select @error('is_active') is-invalid @enderror">
                                    <option value="1" {{ old('is_active', $school->is_active) == 1 ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="0" {{ old('is_active', $school->is_active) == 0 ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Inactive schools cannot log in.
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Domain Info (read-only) --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="icon-base ti tabler-world me-2 text-info fs-5"></i>
                        <h5 class="mb-0">Domain</h5>
                        <span class="badge bg-label-secondary ms-2 small">Read Only</span>
                    </div>
                    <div class="card-body">
                        @forelse($school->domains as $domain)
                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                <div>
                                    <i class="icon-base ti tabler-link me-2 text-primary"></i>
                                    <span class="fw-semibold">{{ $domain->domain }}</span>
                                </div>
                                <a href="http://{{ $domain->domain }}"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="icon-base ti tabler-external-link me-1"></i> Visit
                                </a>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No domains assigned.</p>
                        @endforelse
                        <p class="text-muted small mt-2 mb-0">
                            <i class="icon-base ti tabler-info-circle me-1"></i>
                            Domain changes require manual DB update to avoid breaking active sessions.
                        </p>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base ti tabler-device-floppy me-1"></i> Save Changes
                    </button>
                    <a href="{{ route('superadmin.schools.show', $school) }}"
                       class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>

            </form>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card border-warning">
                <div class="card-header d-flex align-items-center">
                    <i class="icon-base ti tabler-alert-triangle me-2 text-warning fs-5"></i>
                    <h5 class="mb-0">Important Notes</h5>
                </div>
                <div class="card-body">
                    <ul class="ps-3 mb-0 text-muted small">
                        <li class="mb-2">
                            <strong>School Name</strong> change only updates the central record — the database name stays as <code>school_{{ $school->id }}</code>.
                        </li>
                        <li class="mb-2">
                            <strong>Email</strong> here is the school's contact email, not the admin login email.
                        </li>
                        <li class="mb-2">
                            <strong>Rate change</strong> takes effect from the next billing cycle.
                        </li>
                        <li>
                            Setting status to <strong>Inactive</strong> will prevent school users from logging in.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection