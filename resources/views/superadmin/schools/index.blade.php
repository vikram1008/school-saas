@extends('layouts.superadmin.superadmin')

@section('title', 'Schools')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Schools</h4>
            <p class="text-muted mb-0">Manage all registered schools on the platform.</p>
        </div>
        <a href="{{ route('superadmin.schools.create') }}" class="btn btn-primary">
            <i class="icon-base ti tabler-plus me-1"></i> Add New School
        </a>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Schools Table --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Schools ({{ $schools->total() }})</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>School Name</th>
                        <th>Subdomain</th>
                        <th>Email</th>
                        <th>Rate</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schools as $school)
                        <tr>
                            <td>
                                <strong>{{ $school->school_name }}</strong>
                                <div class="text-muted small">ID: {{ $school->id }}</div>
                            </td>
                            <td>
                                @if($school->domains->first())
                                    <a href="http://{{ $school->domains->first()->domain }}"
                                       target="_blank"
                                       class="text-primary">
                                        {{ $school->domains->first()->domain }}
                                        <i class="icon-base ti tabler-external-link ms-1" style="font-size:11px"></i>
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $school->email }}</td>
                            <td>
                                <span class="badge bg-label-{{ $school->per_student_rate == 20 ? 'primary' : 'secondary' }}">
                                    ₹{{ $school->per_student_rate }}/student
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-{{ $school->is_active ? 'success' : 'danger' }}">
                                    {{ $school->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $school->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('superadmin.schools.show', $school) }}"
                                    class="btn btn-sm btn-icon btn-outline-primary"
                                    title="View">
                                        <i class="icon-base ti tabler-eye"></i>
                                    </a>
                                    <a href="{{ route('superadmin.schools.edit', $school) }}"
                                    class="btn btn-sm btn-icon btn-outline-warning"
                                    title="Edit">
                                        <i class="icon-base ti tabler-edit"></i>
                                    </a>
                                    <form action="{{ route('superadmin.schools.destroy', $school) }}"
                                        method="POST"
                                        onsubmit="return confirm('Delete this school and ALL its data? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-icon btn-outline-danger" title="Delete">
                                            <i class="icon-base ti tabler-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="icon-base ti tabler-building-off" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="text-muted mt-2 mb-3">No schools added yet.</p>
                                <a href="{{ route('superadmin.schools.create') }}" class="btn btn-primary btn-sm">
                                    <i class="icon-base ti tabler-plus me-1"></i> Add First School
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($schools->hasPages())
            <div class="card-footer">
                {{ $schools->links() }}
            </div>
        @endif
    </div>

</div>
@endsection