@extends('layouts.tenant')

@section('title', 'Library Members')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Library Members / सदस्य</h4>
            <p class="text-muted mb-0 small">Register students and staff as library members.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tenant.library.dashboard') }}" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-arrow-left me-1"></i> Dashboard
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                <i class="icon-base ti tabler-plus me-1"></i> Register Member
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-4">
            <i class="icon-base ti tabler-alert-circle me-1"></i> {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('tenant.library.members') }}" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search by member no., name, admission no..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="student" {{ request('type') === 'student' ? 'selected' : '' }}>Student</option>
                        <option value="staff"   {{ request('type') === 'staff'   ? 'selected' : '' }}>Staff</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="icon-base ti tabler-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('tenant.library.members') }}" class="btn btn-outline-secondary">
                        <i class="icon-base ti tabler-x"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Members Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="icon-base ti tabler-id-badge-2 me-2 text-info"></i>
                Registered Members
            </h5>
            <span class="badge bg-label-info">{{ $members->total() }} members</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Member No.</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Membership</th>
                        <th class="text-center">Books Allowed</th>
                        <th class="text-center">Currently Issued</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        @php
                            $isExpired = $member->membership_expiry && $member->membership_expiry->isPast();
                        @endphp
                        <tr>
                            <td>
                                <span class="fw-semibold font-monospace small">{{ $member->member_number }}</span>
                            </td>
                            <td>
                                <p class="fw-semibold mb-0">{{ $member->display_name }}</p>
                                @if($member->member_type === 'student' && $member->studentProfile)
                                    <p class="text-muted small mb-0">{{ $member->studentProfile->admission_number }}</p>
                                @elseif($member->member_type === 'staff' && $member->staffProfile)
                                    <p class="text-muted small mb-0">{{ $member->staffProfile->employee_code ?? '' }}</p>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-label-{{ $member->member_type === 'student' ? 'primary' : 'success' }}">
                                    {{ ucfirst($member->member_type) }}
                                </span>
                            </td>
                            <td class="small">
                                <p class="mb-0">
                                    From: {{ $member->membership_start?->format('d M Y') ?? '—' }}
                                </p>
                                <p class="mb-0 {{ $isExpired ? 'text-danger fw-bold' : 'text-muted' }}">
                                    Until: {{ $member->membership_expiry?->format('d M Y') ?? 'No expiry' }}
                                    @if($isExpired) <span class="badge bg-label-danger">Expired</span> @endif
                                </p>
                            </td>
                            <td class="text-center fw-bold">{{ $member->max_books_allowed }}</td>
                            <td class="text-center">
                                @php $issued = $member->active_issues_count; @endphp
                                <span class="badge bg-label-{{ $issued >= $member->max_books_allowed ? 'danger' : ($issued > 0 ? 'warning' : 'secondary') }}">
                                    {{ $issued }} / {{ $member->max_books_allowed }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-label-{{ $member->is_active && !$isExpired ? 'success' : 'secondary' }}">
                                    {{ $member->is_active && !$isExpired ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-icon btn-outline-warning"
                                        data-bs-toggle="modal" data-bs-target="#editMemberModal{{ $member->id }}"
                                        title="Edit">
                                    <i class="icon-base ti tabler-edit"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- Edit Member Modal --}}
                        <div class="modal fade" id="editMemberModal{{ $member->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('tenant.library.members.update', $member) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Member — {{ $member->display_name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Member Number</label>
                                                <input type="text" class="form-control" value="{{ $member->member_number }}" disabled>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Membership Expiry</label>
                                                <input type="date" name="membership_expiry" class="form-control"
                                                       value="{{ $member->membership_expiry?->format('Y-m-d') }}">
                                                <div class="form-text">Leave blank for no expiry.</div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Max Books Allowed <span class="text-danger">*</span></label>
                                                <input type="number" name="max_books_allowed" class="form-control"
                                                       value="{{ $member->max_books_allowed }}" min="1" max="20" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Notes</label>
                                                <textarea name="notes" class="form-control" rows="2">{{ $member->notes }}</textarea>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="is_active"
                                                       id="member_active_{{ $member->id }}" value="1"
                                                       {{ $member->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label" for="member_active_{{ $member->id }}">
                                                    Active Member
                                                </label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="icon-base ti tabler-id-badge-2" style="font-size:2.5rem; color:#ccc;"></i>
                                <p class="text-muted mt-2 mb-0">No members registered yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($members->hasPages())
            <div class="card-footer">{{ $members->links() }}</div>
        @endif
    </div>

</div>

{{-- Add Member Modal --}}
<div class="modal fade" id="addMemberModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('tenant.library.members.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="icon-base ti tabler-plus me-2"></i>Register Library Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Member Type <span class="text-danger">*</span></label>
                            <select name="member_type" id="memberTypeSelect" class="form-select" required>
                                <option value="">Select type...</option>
                                <option value="student" {{ old('member_type') === 'student' ? 'selected' : '' }}>Student</option>
                                <option value="staff"   {{ old('member_type') === 'staff'   ? 'selected' : '' }}>Staff</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Member Number <span class="text-danger">*</span></label>
                            <input type="text" name="member_number" class="form-control"
                                   value="{{ old('member_number', $nextMemberNumber) }}" required>
                        </div>

                        <div class="col-12" id="studentSelectDiv" style="display:none;">
                            <label class="form-label fw-semibold">Select Student <span class="text-danger">*</span></label>
                            <select name="profile_id" id="studentProfileSelect" class="form-select">
                                <option value="">Select student...</option>
                                @foreach($students as $s)
                                    <option value="{{ $s->id }}" {{ old('profile_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->first_name }} {{ $s->last_name }} ({{ $s->admission_number }})
                                    </option>
                                @endforeach
                            </select>
                            @if($students->isEmpty())
                                <div class="form-text text-warning">All active students are already registered as members.</div>
                            @endif
                        </div>

                        <div class="col-12" id="staffSelectDiv" style="display:none;">
                            <label class="form-label fw-semibold">Select Staff <span class="text-danger">*</span></label>
                            <select name="profile_id" id="staffProfileSelect" class="form-select">
                                <option value="">Select staff member...</option>
                                @foreach($staffList as $s)
                                    <option value="{{ $s->id }}" {{ old('profile_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->first_name }} {{ $s->last_name }}
                                        @if($s->employee_code) ({{ $s->employee_code }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @if($staffList->isEmpty())
                                <div class="form-text text-warning">All active staff are already registered as members.</div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Membership Start <span class="text-danger">*</span></label>
                            <input type="date" name="membership_start" class="form-control"
                                   value="{{ old('membership_start', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Membership Expiry</label>
                            <input type="date" name="membership_expiry" class="form-control"
                                   value="{{ old('membership_expiry') }}"
                                   placeholder="Leave blank for no expiry">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Max Books Allowed <span class="text-danger">*</span></label>
                            <input type="number" name="max_books_allowed" class="form-control"
                                   value="{{ old('max_books_allowed', 3) }}" min="1" max="20" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"
                                      placeholder="Any notes about this member...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base ti tabler-plus me-1"></i> Register Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect      = document.getElementById('memberTypeSelect');
    const studentDiv      = document.getElementById('studentSelectDiv');
    const staffDiv        = document.getElementById('staffSelectDiv');
    const studentSelect   = document.getElementById('studentProfileSelect');
    const staffSelect     = document.getElementById('staffProfileSelect');

    function toggleProfileSelect(type) {
        const isStudent = type === 'student';
        const isStaff   = type === 'staff';

        // Show/hide the wrapper divs
        studentDiv.style.display = isStudent ? 'block' : 'none';
        staffDiv.style.display   = isStaff   ? 'block' : 'none';

        // required + disabled controls both validation and form submission
        // A disabled field is NOT submitted with the form
        studentSelect.required = isStudent;
        staffSelect.required   = isStaff;
        studentSelect.disabled = !isStudent;
        staffSelect.disabled   = !isStaff;

        // Clear the inactive select value so it doesn't hold stale data
        if (!isStudent) { studentSelect.value = ''; }
        if (!isStaff)   { staffSelect.value   = ''; }
    }

    typeSelect.addEventListener('change', function () {
        toggleProfileSelect(this.value);
    });

    // Restore state on page reload after validation error
    @if(old('member_type'))
        toggleProfileSelect('{{ old("member_type") }}');
    @else
        // Default: disable both until user picks a type
        studentSelect.disabled = true;
        staffSelect.disabled   = true;
    @endif

    // Reopen modal on validation error
    @if($errors->any())
        var modal = new bootstrap.Modal(document.getElementById('addMemberModal'));
        modal.show();
    @endif
});
</script>
@endpush

@endsection
