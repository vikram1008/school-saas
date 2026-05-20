@extends('layouts.tenant')

@section('title', $parent->full_name)

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-user-view.scss'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tenant.parents.index') }}">Parents</a></li>
            <li class="breadcrumb-item active">{{ $parent->full_name }}</li>
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible mb-4">
            <i class="icon-base ti tabler-alert-circle me-1"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card mb-4">
                <div class="card-body pt-4">
                    <div class="text-center mb-3">
                        <div class="avatar avatar-xl mx-auto mb-3">
                            <span class="avatar-initial rounded-circle bg-label-info"
                                  style="font-size:2rem;">
                                {{ strtoupper(substr($parent->first_name, 0, 1)) }}
                            </span>
                        </div>
                        <h5 class="fw-bold mb-0">{{ $parent->full_name }}</h5>
                        @if($parent->first_name_hi)
                            <p class="text-muted small mb-1">{{ $parent->full_name_hi }}</p>
                        @endif
                        <span class="badge bg-label-{{ $parent->is_active ? 'success' : 'secondary' }} mt-1">
                            {{ $parent->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <hr>

                    <ul class="list-unstyled mb-0">
                        <li class="mb-3 d-flex">
                            <span class="fw-semibold me-2" style="min-width:110px">Mobile:</span>
                            <span class="text-muted">{{ $parent->mobile ?? $parent->phone ?? '—' }}</span>
                        </li>
                        <li class="mb-3 d-flex">
                            <span class="fw-semibold me-2" style="min-width:110px">Login Email:</span>
                            <span class="text-muted small">{{ $parent->user?->email ?? '—' }}</span>
                        </li>
                        <li class="mb-3 d-flex">
                            <span class="fw-semibold me-2" style="min-width:110px">Relation:</span>
                            <span class="text-muted">{{ ucfirst($parent->relation ?? '—') }}</span>
                        </li>
                        <li class="mb-3 d-flex">
                            <span class="fw-semibold me-2" style="min-width:110px">Occupation:</span>
                            <span class="text-muted">{{ $parent->occupation ?? '—' }}</span>
                        </li>
                    </ul>

                    <hr>

                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('tenant.parents.edit', $parent) }}"
                           class="btn btn-primary">
                            <i class="icon-base ti tabler-edit me-1"></i> Edit
                        </a>
                        <button type="button" class="btn btn-outline-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#resetPasswordModal">
                            <i class="icon-base ti tabler-key me-1"></i> Reset Password
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right --}}
        <div class="col-xl-8 col-lg-7">

            {{-- Children --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="icon-base ti tabler-users me-2 text-primary"></i>
                        Children / बच्चे
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#linkStudentModal">
                        <i class="icon-base ti tabler-link me-1"></i> Link Student
                    </button>
                </div>
                <div class="card-body">
                    @if($parent->students->count() > 0)
                        <div class="row g-3">
                            @foreach($parent->students as $student)
                                <div class="col-sm-6">
                                    <div class="border rounded p-3 d-flex justify-content-between align-items-start">
                                        <div>
                                            <p class="fw-semibold mb-0">{{ $student->full_name }}</p>
                                            @if($student->first_name_hi)
                                                <p class="text-muted small mb-0">{{ $student->full_name_hi }}</p>
                                            @endif
                                            <span class="badge bg-label-info small">
                                                {{ $student->class_section }}
                                            </span>
                                            <p class="text-muted small mb-0 mt-1">
                                                Adm: {{ $student->admission_number }}
                                            </p>
                                            <span class="badge bg-label-secondary small">
                                                {{ ucfirst($student->pivot->relationship ?? '') }}
                                            </span>
                                        </div>
                                        <form action="{{ route('tenant.parents.unlink-student', [$parent, $student]) }}"
                                              method="POST"
                                              onsubmit="return confirm('Unlink this student?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-xs btn-icon btn-outline-danger">
                                                <i class="icon-base ti tabler-unlink"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-3 mb-0">No children linked yet.</p>
                    @endif
                </div>
            </div>

            {{-- Login Info --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="icon-base ti tabler-lock me-2 text-warning"></i>
                        Portal Access
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <p class="text-muted small mb-1">Login URL</p>
                            <p class="fw-semibold mb-0 small">
                                http://{{ request()->getHost() }}/login
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted small mb-1">Username (Email)</p>
                            <p class="fw-semibold mb-0 font-monospace small">
                                {{ $parent->user?->email ?? '—' }}
                            </p>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info small mb-0">
                                <i class="icon-base ti tabler-info-circle me-1"></i>
                                Default password = student's admission number.
                                Use "Reset Password" to change it.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reset Password Modal --}}
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="parentResetPasswordLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form action="{{ route('tenant.parents.reset-password', $parent) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="parentResetPasswordLabel">
                        <i class="icon-base ti tabler-key me-1 text-info"></i>
                        Reset Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info small mb-3">
                        <i class="icon-base ti tabler-info-circle me-1"></i>
                        Leave blank to reset to the <strong>mobile number</strong>
                        <code class="ms-1">{{ $parent->mobile ?? $parent->phone ?? 'parent123' }}</code>.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="text" name="password" id="parentResetPassword"
                               class="form-control"
                               placeholder="Leave blank = mobile number"
                               minlength="6">
                        <div class="form-text">Min 6 characters if setting custom password.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        <i class="icon-base ti tabler-key me-1"></i>
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Link Student Modal --}}
<div class="modal fade" id="linkStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tenant.parents.link-student', $parent) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Link Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Student</label>
                        <select name="student_profile_id" class="form-select" required>
                            <option value="">Select Student</option>
                            @foreach(\App\Models\StudentProfile::where('status','active')->orderBy('first_name')->get() as $s)
                                <option value="{{ $s->id }}">
                                    {{ $s->full_name }} ({{ $s->admission_number }})
                                    — {{ $s->class_section }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Relationship</label>
                        <select name="relationship" class="form-select">
                            <option value="father">Father</option>
                            <option value="mother">Mother</option>
                            <option value="guardian">Guardian</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Link</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection