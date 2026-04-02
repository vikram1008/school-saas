@extends('layouts.tenant')

@section('title', 'Academic Years')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Academic Years</h4>
            <p class="text-muted mb-0 small">
                Manage academic years for {{ tenant('school_name') }}
            </p>
        </div>
        <button type="button" class="btn btn-primary"
                data-bs-toggle="modal" data-bs-target="#addYearModal">
            <i class="icon-base ti tabler-plus me-1"></i> Add Academic Year
        </button>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-4">
            <i class="icon-base ti tabler-alert-circle me-1"></i>
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Active Year Banner --}}
    @if($activeYear)
        <div class="alert alert-primary mb-4">
            <i class="icon-base ti tabler-calendar-check me-1"></i>
            Currently active year:
            <strong>{{ $activeYear->name }}</strong>
            ({{ $activeYear->start_date->format('d M Y') }}
            → {{ $activeYear->end_date->format('d M Y') }})
        </div>
    @else
        <div class="alert alert-warning mb-4">
            <i class="icon-base ti tabler-alert-triangle me-1"></i>
            No active academic year set. Please activate one to start managing classes.
        </div>
    @endif

    {{-- Years Table --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Academic Years ({{ $years->count() }})</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Duration</th>
                        <th class="text-center">Classes</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($years as $year)
                        <tr>
                            <td>
                                <span class="fw-semibold">{{ $year->name }}</span>
                                @if($year->description)
                                    <div class="text-muted small">{{ $year->description }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="small">
                                    {{ $year->start_date->format('d M Y') }}
                                    →
                                    {{ $year->end_date->format('d M Y') }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-label-primary">
                                    {{ $year->classes_count }} classes
                                </span>
                            </td>
                            <td class="text-center">
                                @if($year->is_active)
                                    <span class="badge bg-success">
                                        <i class="icon-base ti tabler-circle-check me-1"></i>
                                        Active
                                    </span>
                                @else
                                    <span class="badge bg-label-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    @if(!$year->is_active)
                                        <form action="{{ route('tenant.academic-years.activate', $year) }}"
                                              method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-success"
                                                    onclick="return confirm('Set {{ $year->name }} as active year?')">
                                                <i class="icon-base ti tabler-check me-1"></i>
                                                Activate
                                            </button>
                                        </form>
                                        @if($year->classes_count == 0)
                                            <form action="{{ route('tenant.academic-years.destroy', $year) }}"
                                                  method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-icon btn-outline-danger"
                                                        onclick="return confirm('Delete {{ $year->name }}?')">
                                                    <i class="icon-base ti tabler-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <a href="{{ route('tenant.classes.index') }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="icon-base ti tabler-layout-grid me-1"></i>
                                            Manage Classes
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="icon-base ti tabler-calendar-off"
                                   style="font-size:2.5rem; color:#ccc;"></i>
                                <p class="text-muted mt-2 mb-3">No academic years yet.</p>
                                <button type="button" class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addYearModal">
                                    Add First Academic Year
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Add Academic Year Modal --}}
<div class="modal fade" id="addYearModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tenant.academic-years.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="icon-base ti tabler-calendar-plus me-2"></i>
                        Add Academic Year
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Year Name <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="e.g. 2025-26"
                               maxlength="20">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Format suggestion: 2025-26 or 2025-2026
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Start Date <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   name="start_date"
                                   class="form-control @error('start_date') is-invalid @enderror"
                                   value="{{ old('start_date') }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                End Date <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   name="end_date"
                                   class="form-control @error('end_date') is-invalid @enderror"
                                   value="{{ old('end_date') }}">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <input type="text"
                               name="description"
                               class="form-control"
                               value="{{ old('description') }}"
                               placeholder="Optional note...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base ti tabler-plus me-1"></i>
                        Create Year
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->any())
@push('scripts')
<script>
    // Reopen modal if validation failed
    document.addEventListener('DOMContentLoaded', function () {
        var modal = new bootstrap.Modal(document.getElementById('addYearModal'));
        modal.show();
    });
</script>
@endpush
@endif

@endsection