@extends('layouts.tenant')

@section('title', 'Leave Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="icon-base ti tabler-calendar-off me-2 text-primary"></i>
                @if($role === 'admin') Leave Management
                @elseif($role === 'staff') My Leave
                @elseif($role === 'parent') Child Leave Applications
                @else My Leave Applications
                @endif
            </h4>
            <p class="text-muted mb-0 small">
                @if($role === 'admin') Manage and approve all leave applications across the school.
                @elseif($role === 'staff') Apply for leave and view your leave history.
                @elseif($role === 'parent') Apply and track leave for your children.
                @else Apply for leave and track your application status.
                @endif
            </p>
        </div>
        <a href="{{ route('tenant.leave.create') }}" class="btn btn-primary">
            <i class="icon-base ti tabler-plus me-1"></i> Apply for Leave
        </a>
    </div>

    {{-- Alerts --}}
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

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ti tabler-clipboard-list"></i>
                            </span>
                        </div>
                        <span class="badge bg-label-primary">Total</span>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $stats['total'] }}</h3>
                    <p class="text-muted small mb-0">Total Applications</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-clock-hour-4"></i>
                            </span>
                        </div>
                        <span class="badge bg-label-warning">Pending</span>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $stats['pending'] }}</h3>
                    <p class="text-muted small mb-0">Awaiting Approval</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-calendar-check"></i>
                            </span>
                        </div>
                        <span class="badge bg-label-success">Approved</span>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $stats['approved'] }}</h3>
                    <p class="text-muted small mb-0">Approved</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="icon-base ti tabler-calendar-x"></i>
                            </span>
                        </div>
                        <span class="badge bg-label-danger">Rejected</span>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $stats['rejected'] }}</h3>
                    <p class="text-muted small mb-0">Rejected</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- ADMIN VIEW --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @if($role === 'admin')
        {{-- Filters --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('tenant.leave.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            <option value="pending"   @selected(request('status') === 'pending')>Pending</option>
                            <option value="approved"  @selected(request('status') === 'approved')>Approved</option>
                            <option value="rejected"  @selected(request('status') === 'rejected')>Rejected</option>
                            <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Applicant Type</label>
                        <select name="applicant_type" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="student" @selected(request('applicant_type') === 'student')>Students</option>
                            <option value="staff"   @selected(request('applicant_type') === 'staff')>Staff</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Leave Type</label>
                        <select name="type" class="form-select form-select-sm">
                            <option value="">All Types</option>
                            @foreach($leaveTypes as $lt)
                                <option value="{{ $lt->id }}" @selected(request('type') == $lt->id)>{{ $lt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                <i class="icon-base ti tabler-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('tenant.leave.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="icon-base ti tabler-x"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- All Leaves Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center border-0 bg-transparent">
                <h5 class="mb-0"><i class="icon-base ti tabler-list me-2 text-primary"></i>All Leave Applications</h5>
                <span class="badge bg-label-primary">{{ $leaves->total() }} total</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Applicant</th>
                            <th>Leave Type</th>
                            <th>Duration</th>
                            <th>Days</th>
                            <th>Applied On</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaves as $leave)
                            <tr>
                                <td><small class="text-muted">#{{ $leave->id }}</small></td>
                                <td>
                                    @if($leave->applicant_type === 'student')
                                        @php $p = $leave->studentProfile; @endphp
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar avatar-sm">
                                                <span class="avatar-initial rounded-circle bg-label-info">
                                                    {{ strtoupper(substr($p?->first_name ?? 'S', 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="fw-semibold mb-0 small">{{ $p?->full_name ?? '—' }}</p>
                                                <p class="text-muted mb-0" style="font-size:11px">
                                                    Student · {{ $p?->class?->name }} {{ $p?->section?->name }}
                                                    @if($leave->applied_by_parent)
                                                        <span class="badge bg-label-secondary ms-1" style="font-size:10px">By Parent</span>
                                                    @else
                                                        <span class="badge bg-label-info ms-1" style="font-size:10px">By Student</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        @php $p = $leave->staffProfile; @endphp
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar avatar-sm">
                                                <span class="avatar-initial rounded-circle bg-label-warning">
                                                    {{ strtoupper(substr($p?->first_name ?? 'S', 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="fw-semibold mb-0 small">{{ $p?->full_name ?? '—' }}</p>
                                                <p class="text-muted mb-0" style="font-size:11px">Staff · {{ $p?->designation ?? '—' }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td><span class="badge bg-label-primary">{{ $leave->leaveType?->name }}</span></td>
                                <td class="small">
                                    {{ $leave->from_date->format('d M Y') }}<br>
                                    <span class="text-muted">to {{ $leave->to_date->format('d M Y') }}</span>
                                </td>
                                <td><span class="fw-bold">{{ $leave->total_days }}</span><small class="text-muted"> days</small></td>
                                <td class="small text-muted">{{ $leave->created_at->format('d M Y') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-label-{{ $leave->statusColor() }}">{{ $leave->statusLabel() }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('tenant.leave.show', $leave) }}" class="btn btn-sm btn-icon btn-outline-primary" title="View">
                                            <i class="icon-base ti tabler-eye"></i>
                                        </a>
                                        @if($leave->isPending())
                                            <form method="POST" action="{{ route('tenant.leave.approve', $leave) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-icon btn-outline-success" title="Approve"
                                                    data-swal-confirm data-message="Approve this leave application?">
                                                    <i class="icon-base ti tabler-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-icon btn-outline-danger" title="Reject"
                                                data-bs-toggle="modal" data-bs-target="#rejectModal{{ $leave->id }}">
                                                <i class="icon-base ti tabler-x"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            {{-- Reject Modal --}}
                            @if($leave->isPending())
                            <div class="modal fade" id="rejectModal{{ $leave->id }}" tabindex="-1">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Reject Leave</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="{{ route('tenant.leave.reject', $leave) }}">
                                            @csrf
                                            <div class="modal-body">
                                                <label class="form-label">Reason for rejection <span class="text-danger">*</span></label>
                                                <textarea name="rejection_reason" class="form-control" rows="3" required
                                                    placeholder="Please provide a reason..."></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="icon-base ti tabler-calendar-off text-muted mb-2" style="font-size:2rem"></i>
                                    <p class="text-muted mb-0">No leave applications found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($leaves->hasPages())
                <div class="card-footer border-0 bg-transparent">
                    {{ $leaves->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- STAFF VIEW --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @if($role === 'staff')
        @if($pendingStudentLeaves->isNotEmpty())
        {{-- Pending approvals section --}}
        <div class="card mb-4 border-0 shadow-sm border-start border-warning" style="border-left: 4px solid #ff9f43 !important;">
            <div class="card-header d-flex justify-content-between align-items-center border-0 bg-transparent">
                <h5 class="mb-0 text-warning">
                    <i class="icon-base ti tabler-clock-exclamation me-2"></i>
                    Pending Approvals ({{ $pendingStudentLeaves->count() }})
                </h5>
                <span class="badge bg-warning text-dark">Requires Action</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Leave Type</th>
                            <th>Duration</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingStudentLeaves as $leave)
                            <tr>
                                <td>
                                    <p class="fw-semibold mb-0 small">{{ $leave->studentProfile?->full_name }}</p>
                                    <p class="text-muted mb-0" style="font-size:11px">
                                        {{ $leave->studentProfile?->class?->name }} {{ $leave->studentProfile?->section?->name }}
                                        @if($leave->applied_by_parent)
                                            · <span class="text-muted">By Parent</span>
                                        @else
                                            · <span class="text-info">By Student</span>
                                        @endif
                                    </p>
                                </td>
                                <td><span class="badge bg-label-primary">{{ $leave->leaveType?->name }}</span></td>
                                <td class="small">
                                    {{ $leave->from_date->format('d M') }} – {{ $leave->to_date->format('d M Y') }}
                                </td>
                                <td><strong>{{ $leave->total_days }}</strong> <small class="text-muted">days</small></td>
                                <td class="small text-muted">{{ Str::limit($leave->reason, 50) }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('tenant.leave.show', $leave) }}" class="btn btn-sm btn-icon btn-outline-primary" title="View">
                                            <i class="icon-base ti tabler-eye"></i>
                                        </a>
                                        <form method="POST" action="{{ route('tenant.leave.approve', $leave) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-icon btn-outline-success" title="Approve"
                                                data-swal-confirm data-message="Approve this leave for {{ $leave->studentProfile?->first_name }}?">
                                                <i class="icon-base ti tabler-check"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-danger" title="Reject"
                                            data-bs-toggle="modal" data-bs-target="#staffRejectModal{{ $leave->id }}">
                                            <i class="icon-base ti tabler-x"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            {{-- Reject Modal --}}
                            <div class="modal fade" id="staffRejectModal{{ $leave->id }}" tabindex="-1">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Reject Leave</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="{{ route('tenant.leave.reject', $leave) }}">
                                            @csrf
                                            <div class="modal-body">
                                                <p class="small text-muted mb-2">Rejecting leave for <strong>{{ $leave->studentProfile?->first_name }}</strong></p>
                                                <label class="form-label small">Reason <span class="text-danger">*</span></label>
                                                <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- My Leave History --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 bg-transparent">
                <h5 class="mb-0"><i class="icon-base ti tabler-history me-2 text-primary"></i>My Leave History</h5>
            </div>
            @include('tenant.leave._leave_table', ['leaves' => $myLeaves, 'showApprover' => true])
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- PARENT / STUDENT VIEW --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @if($role === 'parent' || $role === 'student')
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 bg-transparent">
                <h5 class="mb-0">
                    <i class="icon-base ti tabler-history me-2 text-primary"></i>
                    {{ $role === 'parent' ? 'Leave Applications' : 'My Leave History' }}
                </h5>
            </div>
            @include('tenant.leave._leave_table', ['leaves' => $leaves, 'showApprover' => true, 'showCancelBtn' => true])
        </div>
    @endif

</div>
@endsection
