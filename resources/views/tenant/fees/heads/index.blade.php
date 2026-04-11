@extends('layouts.tenant')

@section('title', 'Fee Heads')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Fee Heads / शुल्क मद</h4>
            <p class="text-muted mb-0 small">Manage fee categories for your school.</p>
        </div>
        <button type="button" class="btn btn-primary"
                data-bs-toggle="modal" data-bs-target="#addFeeHeadModal">
            <i class="icon-base ti tabler-plus me-1"></i> Add Fee Head
        </button>
    </div>

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

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Fee Heads ({{ $feeHeads->count() }})</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name / नाम</th>
                        <th>Type</th>
                        <th>Frequency</th>
                        <th class="text-center">Optional</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feeHeads as $head)
                        <tr>
                            <td class="text-muted small">{{ $head->sort_order }}</td>
                            <td>
                                <p class="fw-semibold mb-0">{{ $head->name }}</p>
                                @if($head->name_hi)
                                    <p class="text-muted small mb-0">
                                        <span class="badge bg-label-warning me-1">हिं</span>
                                        {{ $head->name_hi }}
                                    </p>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-label-{{ $head->type === 'preset' ? 'primary' : 'info' }}">
                                    {{ ucfirst($head->type) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary">
                                    {{ \App\Models\FeeHead::frequencyLabels()[$head->frequency] ?? $head->frequency }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($head->is_optional)
                                    <span class="badge bg-label-warning">Optional</span>
                                @else
                                    <span class="badge bg-label-success">Mandatory</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-label-{{ $head->is_active ? 'success' : 'secondary' }}">
                                    {{ $head->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button"
                                            class="btn btn-sm btn-icon btn-outline-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editFeeHeadModal{{ $head->id }}"
                                            title="Edit">
                                        <i class="icon-base ti tabler-edit"></i>
                                    </button>
                                    @if($head->type === 'custom')
                                        <form action="{{ route('tenant.fees.heads.destroy', $head) }}"
                                              method="POST"
                                              onsubmit="return confirm('Delete {{ $head->name }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-icon btn-outline-danger">
                                                <i class="icon-base ti tabler-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Edit Modal --}}
                        <div class="modal fade" id="editFeeHeadModal{{ $head->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('tenant.fees.heads.update', $head) }}"
                                          method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit — {{ $head->name }}</h5>
                                            <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-sm-6">
                                                    <label class="form-label fw-semibold">Name</label>
                                                    <input type="text" name="name"
                                                           class="form-control"
                                                           value="{{ $head->name }}" required>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label fw-semibold">
                                                        नाम <span class="badge bg-label-warning">हिं</span>
                                                    </label>
                                                    <input type="text" name="name_hi"
                                                           class="form-control"
                                                           value="{{ $head->name_hi }}"
                                                           placeholder="हिंदी में">
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label fw-semibold">Frequency</label>
                                                    <select name="frequency" class="form-select">
                                                        @foreach(\App\Models\FeeHead::frequencyLabels() as $val => $lbl)
                                                            <option value="{{ $val }}"
                                                                {{ $head->frequency === $val ? 'selected' : '' }}>
                                                                {{ $lbl }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label fw-semibold">Status</label>
                                                    <select name="is_active" class="form-select">
                                                        <option value="1" {{ $head->is_active ? 'selected' : '' }}>Active</option>
                                                        <option value="0" {{ !$head->is_active ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                               name="is_optional" value="1"
                                                               {{ $head->is_optional ? 'checked' : '' }}>
                                                        <label class="form-check-label">Optional Fee</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                No fee heads found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Fee Head Modal --}}
<div class="modal fade" id="addFeeHeadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tenant.fees.heads.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="icon-base ti tabler-plus me-2"></i>Add Fee Head
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control"
                                   placeholder="e.g. Activity Fee" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                नाम <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="name_hi" class="form-control"
                                   placeholder="जैसे: गतिविधि शुल्क">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                Frequency <span class="text-danger">*</span>
                            </label>
                            <select name="frequency" class="form-select" required>
                                <option value="">Select</option>
                                @foreach(\App\Models\FeeHead::frequencyLabels() as $val => $lbl)
                                    <option value="{{ $val }}">{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       name="is_optional" value="1">
                                <label class="form-check-label">
                                    Optional fee (student may not be charged)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base ti tabler-plus me-1"></i> Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->any())
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Modal(document.getElementById('addFeeHeadModal')).show();
    });
</script>
@endpush
@endif

@endsection