@extends('layouts.tenant')

@section('title', 'Fee Ledger — ' . $student->full_name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('tenant.fees.collections.index') }}"
           class="btn btn-icon btn-outline-secondary me-3">
            <i class="icon-base ti tabler-arrow-left"></i>
        </a>
        <div class="flex-grow-1">
            <h4 class="fw-bold mb-0">
                Fee Ledger — {{ $student->full_name }}
            </h4>
            <p class="text-muted small mb-0">
                {{ $student->class_section }} &nbsp;·&nbsp;
                Admission: {{ $student->admission_number }}
                @if($activeYear) &nbsp;·&nbsp; {{ $activeYear->name }} @endif
            </p>
        </div>
        <a href="{{ route('tenant.fees.collections.create', ['student_id' => $student->id]) }}"
           class="btn btn-primary">
            <i class="icon-base ti tabler-cash me-1"></i> Collect Fee
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <h4 class="fw-bold text-primary mb-1">
                        ₹{{ number_format($summary['total_due']) }}
                    </h4>
                    <p class="text-muted small mb-0">Total Due</p>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <h4 class="fw-bold text-success mb-1">
                        ₹{{ number_format($summary['total_paid']) }}
                    </h4>
                    <p class="text-muted small mb-0">Total Paid</p>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <h4 class="fw-bold text-danger mb-1">
                        ₹{{ number_format($summary['total_balance']) }}
                    </h4>
                    <p class="text-muted small mb-0">Balance Due</p>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card text-center">
                <div class="card-body py-3">
                    <h4 class="fw-bold text-warning mb-1">
                        {{ $summary['pending_count'] }}
                    </h4>
                    <p class="text-muted small mb-0">Pending Demands</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- Fee Demands --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="icon-base ti tabler-file-invoice me-2 text-warning"></i>
                        Fee Demands
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Fee Head</th>
                                <th>Period</th>
                                <th class="text-end">Due</th>
                                <th class="text-end">Paid</th>
                                <th class="text-end">Balance</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($summary['demands'] as $demand)
                                @php
                                    $statusColors = [
                                        'paid'    => 'success',
                                        'partial' => 'warning',
                                        'pending' => 'secondary',
                                        'overdue' => 'danger',
                                        'waived'  => 'info',
                                    ];
                                @endphp
                                <tr>
                                    <td>
                                        <p class="fw-semibold mb-0 small">{{ $demand->feeHead->name }}</p>
                                        @if($demand->feeHead->name_hi)
                                            <p class="text-muted mb-0" style="font-size:11px">
                                                {{ $demand->feeHead->name_hi }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="small">{{ $demand->period_label }}</td>
                                    <td class="text-end small">₹{{ number_format($demand->amount_due) }}</td>
                                    <td class="text-end small text-success">
                                        ₹{{ number_format($demand->amount_paid) }}
                                    </td>
                                    <td class="text-end fw-bold {{ $demand->balance > 0 ? 'text-danger' : 'text-success' }}">
                                        ₹{{ number_format($demand->balance) }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-{{ $statusColors[$demand->status] ?? 'secondary' }}">
                                            {{ ucfirst($demand->status) }}
                                        </span>
                                        @if($demand->waive_reason)
                                            <div class="text-muted small mt-1" title="{{ $demand->waive_reason }}">
                                                Waived
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(in_array($demand->status, ['pending','partial','overdue']))
                                            <button type="button"
                                                    class="btn btn-xs btn-outline-info"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#waiveModal{{ $demand->id }}">
                                                Waive
                                            </button>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Waive Modal --}}
                                @if(in_array($demand->status, ['pending','partial','overdue']))
                                    <div class="modal fade" id="waiveModal{{ $demand->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                                <form action="{{ route('tenant.fees.collections.waive-demand', $demand) }}"
                                                      method="POST">
                                                    @csrf @method('PATCH')
                                                    <div class="modal-header">
                                                        <h6 class="modal-title">Waive Demand</h6>
                                                        <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="text-muted small">
                                                            {{ $demand->feeHead->name }} — {{ $demand->period_label }}
                                                            <br>Balance: <strong>₹{{ number_format($demand->balance) }}</strong>
                                                        </p>
                                                        <label class="form-label fw-semibold">
                                                            Reason <span class="text-danger">*</span>
                                                        </label>
                                                        <textarea name="waive_reason"
                                                                  class="form-control" rows="2"
                                                                  required></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-sm btn-info">Waive</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        No demands generated yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Payment History --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="icon-base ti tabler-receipt me-2 text-success"></i>
                        Payment History
                    </h5>
                </div>
                <div class="card-body p-0">
                    @forelse($collections as $collection)
                        <div class="border-bottom p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="fw-semibold mb-0 small font-monospace">
                                        {{ $collection->receipt_number }}
                                    </p>
                                    <p class="text-muted small mb-0">
                                        {{ $collection->collection_date->format('d M Y') }}
                                    </p>
                                    <span class="badge bg-label-secondary"
                                          style="font-size:10px">
                                        {{ strtoupper($collection->payment_mode) }}
                                    </span>
                                </div>
                                <div class="text-end">
                                    <p class="fw-bold text-success mb-1">
                                        ₹{{ number_format($collection->total_amount) }}
                                    </p>
                                    <a href="{{ route('tenant.fees.receipt', $collection) }}"
                                       target="_blank"
                                       class="btn btn-xs btn-outline-primary">
                                        <i class="icon-base ti tabler-receipt"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            No payments yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection