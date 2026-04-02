@extends('layouts.superadmin.superadmin')

@section('title', $school->school_name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('superadmin.schools.index') }}" class="btn btn-icon btn-outline-secondary me-3">
            <i class="icon-base ti tabler-arrow-left"></i>
        </a>
        <div class="flex-grow-1">
            <h4 class="fw-bold mb-0">{{ $school->school_name }}</h4>
            <p class="text-muted mb-0 small">
                <i class="icon-base ti tabler-id me-1"></i>Tenant ID: <code>{{ $school->id }}</code>
                &nbsp;·&nbsp;
                <i class="icon-base ti tabler-database me-1"></i>Database: <code>school_{{ $school->id }}</code>
            </p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-label-{{ $school->is_active ? 'success' : 'danger' }} fs-6 px-3 py-2">
                <i class="icon-base ti tabler-{{ $school->is_active ? 'circle-check' : 'circle-x' }} me-1"></i>
                {{ $school->is_active ? 'Active' : 'Inactive' }}
            </span>
            <a href="{{ route('superadmin.schools.edit', $school) }}" class="btn btn-outline-primary">
                <i class="icon-base ti tabler-edit me-1"></i> Edit
            </a>
            <form action="{{ route('superadmin.schools.destroy', $school) }}"
                method="POST"
                onsubmit="return confirm('Permanently delete {{ $school->school_name }} and ALL its data? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger">
                    <i class="icon-base ti tabler-trash me-1"></i> Delete School
                </button>
            </form>
        </div>
    </div>

    <div class="row g-4">

        {{-- Left Column --}}
        <div class="col-lg-8">

            {{-- School Info Card --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="icon-base ti tabler-building me-2 text-primary fs-5"></i>
                    <h5 class="mb-0">School Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">School Name</p>
                            <p class="fw-semibold mb-0">{{ $school->school_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Official Email</p>
                            <p class="fw-semibold mb-0">
                                <a href="mailto:{{ $school->email }}">{{ $school->email }}</a>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Phone</p>
                            <p class="fw-semibold mb-0">{{ $school->phone ?? '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Registered On</p>
                            <p class="fw-semibold mb-0">{{ $school->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="col-12">
                            <p class="text-muted small mb-1">Address</p>
                            <p class="fw-semibold mb-0">{{ $school->address ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Domain Card --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="icon-base ti tabler-world me-2 text-info fs-5"></i>
                    <h5 class="mb-0">Domain & Access</h5>
                </div>
                <div class="card-body">
                    @forelse($school->domains as $domain)
                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-2">
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
                </div>
            </div>

            {{-- Database Card --}}
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="icon-base ti tabler-database me-2 text-warning fs-5"></i>
                    <h5 class="mb-0">Database Info</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Database Name</p>
                            <code class="fs-6">school_{{ $school->id }}</code>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Connection</p>
                            <code class="fs-6">MySQL (Isolated)</code>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Storage</p>
                            <span class="fw-semibold">Dedicated DB</span>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Tenant ID</p>
                            <code class="fs-6">{{ $school->id }}</code>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">

            {{-- Billing Card --}}
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="icon-base ti tabler-receipt-rupee me-2 fs-5"></i>
                    <h5 class="mb-0 text-white">Billing</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <p class="text-muted small mb-1">Rate per Student</p>
                        <h2 class="fw-bold text-primary mb-0">
                            ₹{{ $school->per_student_rate }}
                            <small class="fs-6 text-muted fw-normal">/student/month</small>
                        </h2>
                    </div>
                    <hr>
                    <p class="text-muted small fw-semibold mb-2">Estimated Monthly Bills</p>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">100 students</td>
                            <td class="fw-semibold text-end">
                                ₹{{ number_format(100 * $school->per_student_rate) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">300 students</td>
                            <td class="fw-semibold text-end">
                                ₹{{ number_format(300 * $school->per_student_rate) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">500 students</td>
                            <td class="fw-semibold text-end">
                                ₹{{ number_format(500 * $school->per_student_rate) }}
                            </td>
                        </tr>
                        <tr class="border-top">
                            <td class="text-muted">1000 students</td>
                            <td class="fw-bold text-end text-primary">
                                ₹{{ number_format(1000 * $school->per_student_rate) }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Quick Stats Card (placeholder for future live data) --}}
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="icon-base ti tabler-chart-bar me-2 text-success fs-5"></i>
                    <h5 class="mb-0">Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="badge bg-label-primary rounded p-2">
                                <i class="icon-base ti tabler-users"></i>
                            </div>
                            <span class="text-muted">Students</span>
                        </div>
                        <span class="fw-bold">—</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="badge bg-label-success rounded p-2">
                                <i class="icon-base ti tabler-chalkboard"></i>
                            </div>
                            <span class="text-muted">Staff</span>
                        </div>
                        <span class="fw-bold">—</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="badge bg-label-warning rounded p-2">
                                <i class="icon-base ti tabler-users-group"></i>
                            </div>
                            <span class="text-muted">Parents</span>
                        </div>
                        <span class="fw-bold">—</span>
                    </div>
                    <p class="text-muted small text-center mt-3 mb-0">
                        Live counts available after School Admin seeds data.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection