@extends('layouts.tenant')

@section('title', 'Leave Application #' . $leave->id)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('tenant.leave.index') }}" class="btn btn-icon btn-outline-secondary btn-sm">
            <i class="icon-base ti tabler-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">Leave Application <span class="text-muted fw-normal">#{{ $leave->id }}</span></h4>
            <p class="text-muted small mb-0">Details and status of this leave request</p>
        </div>
        <div class="ms-auto">
            <span class="badge bg-{{ $leave->statusColor() }} fs-6 px-3 py-2">
                <i class="icon-base ti
                    @if($leave->status === 'pending') tabler-clock
                    @elseif($leave->status === 'approved') tabler-calendar-check
                    @elseif($leave->status === 'rejected') tabler-calendar-x
                    @else tabler-ban
                    @endif me-1"></i>
                {{ $leave->statusLabel() }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->has('error'))
        <div class="alert alert-danger alert-dismissible mb-4">
            <i class="icon-base ti tabler-alert-circle me-2"></i>{{ $errors->first('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- Left: Application Details --}}
        <div class="col-lg-8">

            {{-- Applicant Info --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header border-0 bg-transparent">
                    <h5 class="mb-0"><i class="icon-base ti tabler-user me-2 text-primary"></i>Applicant</h5>
                </div>
                <div class="card-body pt-0">
                    @if($leave->applicant_type === 'student')
                        @php $profile = $leave->studentProfile; @endphp
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-lg">
                                <span class="avatar-initial rounded-circle bg-label-info fs-4">
                                    {{ strtoupper(substr($profile?->first_name ?? 'S', 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">{{ $profile?->full_name ?? '—' }}</h5>
                                <p class="text-muted small mb-1">
                                    <i class="icon-base ti tabler-school me-1"></i>
                                    Student · {{ $profile?->class?->name }} {{ $profile?->section?->name }}
                                </p>
                                @if($leave->applied_by_parent)
                                    <span class="badge bg-label-secondary">
                                        <i class="icon-base ti tabler-users me-1"></i>Applied by Parent
                                    </span>
                                @else
                                    <span class="badge bg-label-info">
                                        <i class="icon-base ti tabler-user me-1"></i>Applied by Student
                                    </span>
                                @endif
                            </div>
                        </div>
                    @else
                        @php $profile = $leave->staffProfile; @endphp
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-lg">
                                <span class="avatar-initial rounded-circle bg-label-warning fs-4">
                                    {{ strtoupper(substr($profile?->first_name ?? 'S', 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">{{ $profile?->full_name ?? '—' }}</h5>
                                <p class="text-muted small mb-0">
                                    <i class="icon-base ti tabler-briefcase me-1"></i>
                                    Staff · {{ $profile?->designation ?? '—' }} · {{ $profile?->department ?? '—' }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Leave Details --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header border-0 bg-transparent">
                    <h5 class="mb-0"><i class="icon-base ti tabler-calendar me-2 text-primary"></i>Leave Details</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <p class="text-muted small mb-1">Leave Type</p>
                            <span class="badge bg-label-primary fs-6">{{ $leave->leaveType?->name }}</span>
                            @if($leave->leaveType?->name_hi)
                                <br><small class="text-muted">{{ $leave->leaveType->name_hi }}</small>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted small mb-1">Duration</p>
                            <p class="fw-semibold mb-0">{{ $leave->from_date->format('d M Y') }}</p>
                            <p class="text-muted small mb-0">to {{ $leave->to_date->format('d M Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted small mb-1">Total Days</p>
                            <h3 class="fw-bold mb-0 text-primary">{{ $leave->total_days }}</h3>
                            <small class="text-muted">day{{ $leave->total_days > 1 ? 's' : '' }}</small>
                        </div>
                        <div class="col-12">
                            <p class="text-muted small mb-1">Reason</p>
                            <div class="p-3 bg-light rounded-2">
                                <p class="mb-0">{{ $leave->reason }}</p>
                            </div>
                        </div>
                        @if($leave->document_path)
                            <div class="col-12">
                                <p class="text-muted small mb-1">Supporting Document</p>
                                <a href="{{ Storage::url($leave->document_path) }}" target="_blank"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="icon-base ti tabler-file-download me-1"></i>View Document
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Rejection Reason --}}
            @if($leave->status === 'rejected' && $leave->rejection_reason)
                <div class="card border-0 shadow-sm border-start border-danger mb-4" style="border-left: 4px solid #ea5455 !important;">
                    <div class="card-body">
                        <h6 class="fw-bold text-danger mb-2">
                            <i class="icon-base ti tabler-alert-circle me-2"></i>Rejection Reason
                        </h6>
                        <p class="mb-0 text-muted">{{ $leave->rejection_reason }}</p>
                    </div>
                </div>
            @endif

            {{-- Approve / Reject Actions (for admin and class teacher) --}}
            @auth('tenant')
                @php $authUser = auth('tenant')->user(); @endphp
                @if($leave->isPending() && ($authUser->isSchoolAdmin() || ($authUser->isStaff() && $authUser->hasPermission('can_approve_student_leave') && $leave->applicant_type === 'student')))
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-0 bg-transparent">
                            <h5 class="mb-0"><i class="icon-base ti tabler-checkup-list me-2 text-primary"></i>Review Application</h5>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <form method="POST" action="{{ route('tenant.leave.approve', $leave) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100"
                                            data-swal-confirm data-message="Approve this leave application?">
                                            <i class="icon-base ti tabler-calendar-check me-2"></i>Approve Leave
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-danger w-100"
                                        data-bs-toggle="collapse" data-bs-target="#rejectSection">
                                        <i class="icon-base ti tabler-calendar-x me-2"></i>Reject Leave
                                    </button>
                                </div>
                                <div class="col-12 collapse" id="rejectSection">
                                    <form method="POST" action="{{ route('tenant.leave.reject', $leave) }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                                            <textarea name="rejection_reason" class="form-control" rows="3" required
                                                placeholder="Please provide a reason for rejecting this leave..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger">
                                            <i class="icon-base ti tabler-x me-2"></i>Confirm Rejection
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Cancel by applicant --}}
                @if($leave->canBeCancelledBy($authUser))
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <form method="POST" action="{{ route('tenant.leave.cancel', $leave) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary"
                                    data-swal-confirm data-message="Cancel this leave application? This cannot be undone.">
                                    <i class="icon-base ti tabler-ban me-2"></i>Cancel Application
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endauth
        </div>

        {{-- Right: Timeline / Meta --}}
        <div class="col-lg-4">

            {{-- Status Timeline --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header border-0 bg-transparent">
                    <h5 class="mb-0"><i class="icon-base ti tabler-timeline me-2 text-primary"></i>Timeline</h5>
                </div>
                <div class="card-body pt-0">
                    <ul class="timeline-vertical">

                        {{-- Submitted --}}
                        <li class="timeline-item">
                            <span class="timeline-indicator timeline-indicator-success">
                                <i class="icon-base ti tabler-send"></i>
                            </span>
                            <div class="timeline-event">
                                <div class="timeline-header">
                                    <h6 class="mb-0">Submitted</h6>
                                    <small class="text-muted">{{ $leave->created_at->format('d M Y, h:i A') }}</small>
                                </div>
                                <p class="text-muted small mb-0">Application submitted by {{ $leave->user?->name }}.</p>
                            </div>
                        </li>

                        {{-- Under Review --}}
                        <li class="timeline-item @if($leave->status !== 'pending') timeline-item-past @endif">
                            <span class="timeline-indicator timeline-indicator-{{ $leave->isPending() ? 'warning' : ($leave->isApproved() ? 'success' : 'danger') }}">
                                <i class="icon-base ti tabler-eye"></i>
                            </span>
                            <div class="timeline-event">
                                <div class="timeline-header">
                                    <h6 class="mb-0">
                                        @if($leave->isPending()) Under Review
                                        @elseif($leave->isApproved()) Approved
                                        @elseif($leave->status === 'rejected') Rejected
                                        @else Cancelled
                                        @endif
                                    </h6>
                                    @if($leave->reviewed_at)
                                        <small class="text-muted">{{ $leave->reviewed_at->format('d M Y, h:i A') }}</small>
                                    @endif
                                </div>
                                @if($leave->isPending())
                                    <p class="text-muted small mb-0">Waiting for approval from class teacher or admin.</p>
                                @elseif($leave->reviewer)
                                    <p class="text-muted small mb-0">Reviewed by {{ $leave->reviewer->name }}.</p>
                                @endif
                            </div>
                        </li>

                        {{-- Completed (only if approved) --}}
                        @if($leave->isApproved())
                        <li class="timeline-item">
                            <span class="timeline-indicator timeline-indicator-success">
                                <i class="icon-base ti tabler-calendar-check"></i>
                            </span>
                            <div class="timeline-event">
                                <div class="timeline-header">
                                    <h6 class="mb-0">Leave Starts</h6>
                                    <small class="text-muted">{{ $leave->from_date->format('d M Y') }}</small>
                                </div>
                                <p class="text-muted small mb-0">{{ $leave->total_days }} day{{ $leave->total_days > 1 ? 's' : '' }} until {{ $leave->to_date->format('d M Y') }}.</p>
                            </div>
                        </li>
                        @endif

                    </ul>
                </div>
            </div>

            {{-- Meta info --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 bg-transparent">
                    <h5 class="mb-0"><i class="icon-base ti tabler-info-circle me-2 text-primary"></i>Details</h5>
                </div>
                <div class="card-body pt-0">
                    <dl class="row mb-0">
                        <dt class="col-6 small text-muted">Application ID</dt>
                        <dd class="col-6 small fw-semibold">#{{ $leave->id }}</dd>

                        <dt class="col-6 small text-muted">Applied On</dt>
                        <dd class="col-6 small">{{ $leave->created_at->format('d M Y') }}</dd>

                        <dt class="col-6 small text-muted">Applicant Type</dt>
                        <dd class="col-6 small text-capitalize">{{ $leave->applicant_type }}</dd>

                        <dt class="col-6 small text-muted">Leave Type</dt>
                        <dd class="col-6 small">{{ $leave->leaveType?->name }}</dd>

                        <dt class="col-6 small text-muted">Total Days</dt>
                        <dd class="col-6 small fw-semibold">{{ $leave->total_days }} day{{ $leave->total_days > 1 ? 's' : '' }}</dd>

                        @if($leave->reviewer)
                            <dt class="col-6 small text-muted">Reviewed By</dt>
                            <dd class="col-6 small">{{ $leave->reviewer->name }}</dd>
                        @endif

                        <dt class="col-6 small text-muted">Document</dt>
                        <dd class="col-6 small">
                            @if($leave->document_path)
                                <a href="{{ Storage::url($leave->document_path) }}" target="_blank" class="text-primary small">
                                    <i class="icon-base ti tabler-file me-1"></i>View
                                </a>
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
