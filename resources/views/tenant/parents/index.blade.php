@extends('layouts.tenant')

@section('title', 'Parents')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Parents / अभिभावक</h4>
            <p class="text-muted mb-0 small">
                Parent accounts are auto-created when students are admitted.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('tenant.parents.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold small">Search</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="icon-base ti tabler-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Name or Mobile..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-base ti tabler-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('tenant.parents.index') }}"
                               class="btn btn-outline-secondary">
                                <i class="icon-base ti tabler-x"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                All Parents
                <span class="badge bg-label-primary ms-1">{{ $parents->total() }}</span>
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Parent / अभिभावक</th>
                        <th>Mobile / Login</th>
                        <th>Children</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parents as $parent)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm">
                                        <span class="avatar-initial rounded-circle bg-label-info">
                                            {{ strtoupper(substr($parent->first_name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <a href="{{ route('tenant.parents.show', $parent) }}"
                                           class="fw-semibold text-body d-block">
                                            {{ $parent->full_name }}
                                        </a>
                                        @if($parent->first_name_hi)
                                            <span class="text-muted small">
                                                {{ $parent->full_name_hi }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="fw-semibold mb-0 small">{{ $parent->mobile ?? $parent->phone ?? '—' }}</p>
                                <p class="text-muted small mb-0">
                                    Login: {{ $parent->user?->email ?? '—' }}
                                </p>
                            </td>
                            <td>
                                @forelse($parent->students as $student)
                                    <span class="badge bg-label-primary mb-1">
                                        {{ $student->full_name }}
                                        ({{ $student->class_section }})
                                    </span><br>
                                @empty
                                    <span class="text-muted small">No children linked</span>
                                @endforelse
                            </td>
                            <td class="text-center">
                                <span class="badge bg-label-{{ $parent->is_active ? 'success' : 'secondary' }}">
                                    {{ $parent->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('tenant.parents.show', $parent) }}"
                                       class="btn btn-sm btn-icon btn-outline-primary"
                                       title="View">
                                        <i class="icon-base ti tabler-eye"></i>
                                    </a>
                                    <a href="{{ route('tenant.parents.edit', $parent) }}"
                                       class="btn btn-sm btn-icon btn-outline-warning"
                                       title="Edit">
                                        <i class="icon-base ti tabler-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="icon-base ti tabler-users-off"
                                   style="font-size:2.5rem; color:#ccc;"></i>
                                <p class="text-muted mt-2 mb-0">
                                    No parents found. They are auto-created when students are admitted.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($parents->hasPages())
            <div class="card-footer">{{ $parents->links() }}</div>
        @endif
    </div>

</div>
@endsection