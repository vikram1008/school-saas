@extends('layouts.tenant')

@section('title', 'Grade Scale')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Grade Scale / ग्रेड स्केल</h4>
            <p class="text-muted mb-0 small">
                Configure grading for {{ $activeYear?->name ?? 'active year' }}.
            </p>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('tenant.results.grade-scales.apply-default') }}"
                  method="POST"
                  onsubmit="return confirm('Apply CBSE grade scale? This will replace existing grades.')">
                @csrf
                <button type="submit" class="btn btn-outline-primary">
                    <i class="icon-base ti tabler-template me-1"></i>
                    Apply CBSE Default
                </button>
            </form>
            <button type="button" class="btn btn-primary"
                    data-bs-toggle="modal" data-bs-target="#addGradeModal">
                <i class="icon-base ti tabler-plus me-1"></i> Add Grade
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Grade</th>
                        <th>Range (%)</th>
                        <th>Grade Point</th>
                        <th>Description</th>
                        <th class="text-center">Color</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($scales as $scale)
                        <tr>
                            <td>
                                <span class="badge bg-{{ $scale->color ?? 'secondary' }} fs-6 px-3">
                                    {{ $scale->grade }}
                                </span>
                            </td>
                            <td class="fw-semibold">
                                {{ $scale->min_percentage }}% — {{ $scale->max_percentage }}%
                            </td>
                            <td>{{ $scale->grade_point }}</td>
                            <td>
                                <p class="mb-0">{{ $scale->description ?? '—' }}</p>
                                @if($scale->description_hi)
                                    <p class="text-muted small mb-0">
                                        <span class="badge bg-label-warning me-1">हिं</span>
                                        {{ $scale->description_hi }}
                                    </p>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $scale->color ?? 'secondary' }}">
                                    {{ $scale->color ?? 'secondary' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('tenant.results.grade-scales.destroy', $scale) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete grade {{ $scale->grade }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-icon btn-outline-danger">
                                        <i class="icon-base ti tabler-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="icon-base ti tabler-award"
                                   style="font-size:2.5rem; color:#ccc;"></i>
                                <p class="text-muted mt-2 mb-2">No grade scale defined.</p>
                                <form action="{{ route('tenant.results.grade-scales.apply-default') }}"
                                      method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        Apply CBSE Default Scale
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Add Grade Modal --}}
<div class="modal fade" id="addGradeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tenant.results.grade-scales.store') }}" method="POST">
                @csrf
                <input type="hidden" name="academic_year_id" value="{{ $activeYear?->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">Add Grade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">
                                Grade <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="grade" class="form-control"
                                   placeholder="e.g. A1" required maxlength="5">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Min %</label>
                            <input type="number" name="min_percentage" class="form-control"
                                   placeholder="e.g. 91" min="0" max="100" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Max %</label>
                            <input type="number" name="max_percentage" class="form-control"
                                   placeholder="e.g. 100" min="0" max="100" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Grade Point</label>
                            <input type="number" name="grade_point" class="form-control"
                                   placeholder="e.g. 10.0" step="0.1" min="0">
                        </div>
                        <div class="col-sm-8">
                            <label class="form-label fw-semibold">Description</label>
                            <input type="text" name="description" class="form-control"
                                   placeholder="e.g. Outstanding"
                                   data-hindi-target="[name='description_hi']">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                विवरण <span class="badge bg-label-warning">हिं</span>
                            </label>
                            <input type="text" name="description_hi" class="form-control"
                                   placeholder="जैसे: उत्कृष्ट">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Color</label>
                            <select name="color" class="form-select">
                                @foreach(['success'=>'Green (success)','primary'=>'Blue (primary)','info'=>'Cyan (info)','warning'=>'Yellow (warning)','danger'=>'Red (danger)','secondary'=>'Grey (secondary)'] as $val=>$lbl)
                                    <option value="{{ $val }}">{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Grade</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection