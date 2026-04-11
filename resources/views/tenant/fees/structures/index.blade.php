@extends('layouts.tenant')

@section('title', 'Fee Structure')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Fee Structure / शुल्क संरचना</h4>
            <p class="text-muted mb-0 small">
                @if($activeYear)
                    Active Year: <strong>{{ $activeYear->name }}</strong>
                @else
                    <span class="text-warning">No active academic year</span>
                @endif
            </p>
        </div>
        @if($activeYear)
            <button type="button" class="btn btn-primary"
                    data-bs-toggle="modal" data-bs-target="#addStructureModal">
                <i class="icon-base ti tabler-plus me-1"></i> Add Fee Structure
            </button>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(!$activeYear)
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="icon-base ti tabler-calendar-off"
                   style="font-size:3rem; color:#ccc;"></i>
                <p class="text-muted mt-2 mb-3">Please set an active academic year first.</p>
                <a href="{{ route('tenant.academic-years.index') }}" class="btn btn-primary">
                    Manage Academic Years
                </a>
            </div>
        </div>
    @else
        @forelse($classes as $class)
            @php $classStructures = $structures->get($class->id, collect()); @endphp
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $class->name }}</h5>
                        <small class="text-muted">
                            {{ $classStructures->count() }} fee heads configured
                        </small>
                    </div>
                    <div class="text-end">
                        <p class="text-muted small mb-0">Total Monthly</p>
                        <h6 class="fw-bold text-success mb-0">
                            ₹{{ number_format($classStructures->where('feeHead.frequency', 'monthly')->sum('amount')) }}
                        </h6>
                    </div>
                </div>
                @if($classStructures->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Fee Head</th>
                                    <th>Frequency</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-center">Due Day</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classStructures as $structure)
                                    <tr>
                                        <td>
                                            <p class="fw-semibold mb-0 small">
                                                {{ $structure->feeHead->name }}
                                            </p>
                                            @if($structure->feeHead->name_hi)
                                                <p class="text-muted mb-0" style="font-size:11px">
                                                    {{ $structure->feeHead->name_hi }}
                                                </p>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-label-secondary small">
                                                {{ \App\Models\FeeHead::frequencyLabels()[$structure->feeHead->frequency] ?? '' }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold text-primary">
                                            ₹{{ number_format($structure->amount) }}
                                        </td>
                                        <td class="text-center text-muted small">
                                            {{ $structure->due_day }}{{ in_array($structure->due_day, [1,21,31]) ? 'st' : (in_array($structure->due_day, [2,22]) ? 'nd' : (in_array($structure->due_day, [3,23]) ? 'rd' : 'th')) }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-{{ $structure->is_active ? 'success' : 'secondary' }}">
                                                {{ $structure->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('tenant.fees.structures.destroy', $structure) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Remove this fee structure?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-icon btn-outline-danger">
                                                    <i class="icon-base ti tabler-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="card-body text-center py-3">
                        <p class="text-muted small mb-0">
                            No fee structure defined for this class.
                        </p>
                    </div>
                @endif
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center py-5">
                    <p class="text-muted">No classes found for active academic year.</p>
                </div>
            </div>
        @endforelse
    @endif

</div>

{{-- Add Structure Modal --}}
@if($activeYear)
<div class="modal fade" id="addStructureModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tenant.fees.structures.store') }}" method="POST">
                @csrf
                <input type="hidden" name="academic_year_id" value="{{ $activeYear->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">Add Fee Structure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Class <span class="text-danger">*</span>
                            </label>
                            <select name="class_id" class="form-select" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Fee Head <span class="text-danger">*</span>
                            </label>
                            <select name="fee_head_id" class="form-select" required>
                                <option value="">Select Fee Head</option>
                                @foreach($feeHeads as $head)
                                    <option value="{{ $head->id }}">
                                        {{ $head->name }}
                                        ({{ \App\Models\FeeHead::frequencyLabels()[$head->frequency] ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                Amount (₹) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="amount" class="form-control"
                                       placeholder="0.00" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                Due Day <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="due_day" class="form-control"
                                   value="10" min="1" max="28"
                                   placeholder="Day of month (1-28)" required>
                            <div class="form-text">Day of month when fee is due.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <input type="text" name="notes" class="form-control"
                                   placeholder="Optional note...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base ti tabler-plus me-1"></i> Save Structure
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection