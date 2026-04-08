@php
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.tenant')

@section('title', 'Staff')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Staff / स्टाफ</h4>
            <p class="text-muted mb-0 small">
                Manage all school staff members.
            </p>
        </div>
        <a href="{{ route('tenant.staff.create') }}" class="btn btn-primary">
            <i class="icon-base ti tabler-plus me-1"></i>
            Add Staff / स्टाफ जोड़ें
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        @php
            $total          = $staff->total();
            $teaching       = \App\Models\StaffProfile::where('staff_type', 'teaching')->count();
            $nonTeaching    = \App\Models\StaffProfile::where('staff_type', 'non_teaching')->count();
            $administrative = \App\Models\StaffProfile::where('staff_type', 'administrative')->count();
        @endphp
        <div class="col-sm-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <h3 class="fw-bold text-primary mb-1">{{ $total }}</h3>
                    <p class="text-muted small mb-0">Total Staff</p>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <h3 class="fw-bold text-success mb-1">{{ $teaching }}</h3>
                    <p class="text-muted small mb-0">Teaching</p>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <h3 class="fw-bold text-info mb-1">{{ $nonTeaching }}</h3>
                    <p class="text-muted small mb-0">Non-Teaching</p>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <h3 class="fw-bold text-warning mb-1">{{ $administrative }}</h3>
                    <p class="text-muted small mb-0">Administrative</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('tenant.staff.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Search</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="icon-base ti tabler-search"></i>
                            </span>
                            <input type="text" name="search"
                                   class="form-control"
                                   placeholder="Name, Employee Code, Designation..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Staff Type</label>
                        <select name="staff_type" class="form-select">
                            <option value="">All Types</option>
                            @foreach(\App\Models\StaffProfile::typeLabels() as $val => $label)
                                <option value="{{ $val }}"
                                    {{ request('staff_type') == $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            @foreach(['active'=>'Active','inactive'=>'Inactive','on_leave'=>'On Leave','resigned'=>'Resigned'] as $val=>$lbl)
                                <option value="{{ $val }}"
                                    {{ request('status') == $val ? 'selected' : '' }}>
                                    {{ $lbl }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-base ti tabler-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('tenant.staff.index') }}"
                               class="btn btn-outline-secondary">
                                <i class="icon-base ti tabler-x"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Staff Table --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                All Staff
                <span class="badge bg-label-primary ms-1">{{ $staff->total() }}</span>
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Staff Member</th>
                        <th>Type / Designation</th>
                        <th>Department</th>
                        <th>Contact</th>
                        <th>Joining Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $member)
                        @php
                            $typeColors = [
                                'teaching'       => 'primary',
                                'non_teaching'   => 'info',
                                'administrative' => 'warning',
                            ];
                            $statusColors = [
                                'active'     => 'success',
                                'inactive'   => 'secondary',
                                'on_leave'   => 'warning',
                                'resigned'   => 'danger',
                                'terminated' => 'dark',
                            ];
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($member->photo)
                                        <img src="{{ Storage::url($member->photo) }}"
                                             class="rounded-circle"
                                             width="36" height="36"
                                             style="object-fit:cover;">
                                    @else
                                        <div class="avatar avatar-sm">
                                            <span class="avatar-initial rounded-circle bg-label-{{ $typeColors[$member->staff_type] ?? 'primary' }}">
                                                {{ strtoupper(substr($member->first_name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('tenant.staff.show', $member) }}"
                                           class="fw-semibold text-body d-block">
                                            {{ $member->full_name }}
                                        </a>
                                        @if($member->first_name_hi)
                                            <span class="text-muted small">
                                                {{ $member->full_name_hi }}
                                            </span>
                                        @endif
                                        <span class="text-muted small d-block">
                                            {{ $member->employee_code }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-{{ $typeColors[$member->staff_type] ?? 'secondary' }} d-block mb-1">
                                    {{ \App\Models\StaffProfile::typeLabels()[$member->staff_type] ?? '—' }}
                                </span>
                                <span class="text-muted small">
                                    {{ $member->designation ?? '—' }}
                                </span>
                            </td>
                            <td>
                                <span class="small">{{ $member->department ?? '—' }}</span>
                                @if($member->department_hi)
                                    <div class="text-muted small">{{ $member->department_hi }}</div>
                                @endif
                            </td>
                            <td class="small">
                                {{ $member->phone ?? '—' }}
                                @if($member->email)
                                    <div class="text-muted small">{{ $member->email }}</div>
                                @endif
                            </td>
                            <td class="small">
                                {{ $member->joining_date?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-label-{{ $statusColors[$member->status] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $member->status)) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('tenant.staff.show', $member) }}"
                                       class="btn btn-sm btn-icon btn-outline-primary"
                                       title="View">
                                        <i class="icon-base ti tabler-eye"></i>
                                    </a>
                                    <a href="{{ route('tenant.staff.edit', $member) }}"
                                       class="btn btn-sm btn-icon btn-outline-warning"
                                       title="Edit">
                                        <i class="icon-base ti tabler-edit"></i>
                                    </a>
                                    <form action="{{ route('tenant.staff.destroy', $member) }}"
                                          method="POST"
                                          onsubmit="return confirm('Deactivate {{ $member->full_name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-icon btn-outline-danger"
                                                title="Deactivate">
                                            <i class="icon-base ti tabler-user-off"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="icon-base ti tabler-user-off"
                                   style="font-size:2.5rem; color:#ccc;"></i>
                                <p class="text-muted mt-2 mb-3">No staff members found.</p>
                                <a href="{{ route('tenant.staff.create') }}"
                                   class="btn btn-primary btn-sm">
                                    <i class="icon-base ti tabler-plus me-1"></i>
                                    Add First Staff Member
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($staff->hasPages())
            <div class="card-footer">
                {{ $staff->links() }}
            </div>
        @endif
    </div>

</div>
@endsection