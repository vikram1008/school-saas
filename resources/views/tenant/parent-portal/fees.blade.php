@extends('layouts.tenant')

@section('title', 'Fees — ' . $student->full_name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('tenant.parent-portal.dashboard') }}"
           class="btn btn-icon btn-outline-secondary me-3">
            <i class="icon-base ti tabler-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">Fee Details / शुल्क विवरण</h4>
            <p class="text-muted small mb-0">
                {{ $student->full_name }} — {{ $student->class_section }}
                @if($activeYear) · {{ $activeYear->name }} @endif
            </p>
        </div>
    </div>

    {{-- Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card text-center">
                <div class="card-body py-3">
                    <h4 class="fw-bold text-primary mb-1">
                        ₹{{ number_format($summary['total_due']) }}
                    </h4>
                    <p class="text-muted small mb-0">Total Due</p>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card text-center">
                <div class="card-body py-3">
                    <h4 class="fw-bold text-success mb-1">
                        ₹{{ number_format($summary['total_paid']) }}
                    </h4>
                    <p class="text-muted small mb-0">Total Paid</p>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card text-center">
                <div class="card-body py-3">
                    <h4 class="fw-bold {{ $summary['total_balance'] > 0 ? 'text-danger' : 'text-success' }} mb-1">
                        ₹{{ number_format($summary['total_balance']) }}
                    </h4>
                    <p class="text-muted small mb-0">
                        {{ $summary['total_balance'] > 0 ? 'Balance Due' : 'All Paid ✓' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Demands --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="icon-base ti tabler-file-invoice me-2 text-warning"></i>
                        Fee Demands
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Fee Head</th>
                                <th>Period</th>
                                <th class="text-end">Due</th>
                                <th class="text-end">Paid</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($demands as $demand)
                                @php
                                    $statusColors = [
                                        'paid'=>'success','partial'=>'warning',
                                        'pending'=>'secondary','overdue'=>'danger','waived'=>'info'
                                    ];
                                @endphp
                                <tr>
                                    <td>
                                        <p class="fw-semibold mb-0 small">{{ $demand->feeHead->name }}</p>
                                        @if($demand->feeHead->name_hi)
                                            <p class="text-muted mb-0" style="font-size:10px">
                                                {{ $demand->feeHead->name_hi }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="small">{{ $demand->period_label }}</td>
                                    <td class="text-end small">₹{{ number_format($demand->amount_due) }}</td>
                                    <td class="text-end small text-success">₹{{ number_format($demand->amount_paid) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-label-{{ $statusColors[$demand->status] ?? 'secondary' }}"
                                              style="font-size:10px">
                                            {{ ucfirst($demand->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted small">
                                        No demands generated yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Receipts --}}
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="icon-base ti tabler-receipt me-2 text-success"></i>
                        Payment Receipts
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
                                        <i class="icon-base ti tabler-download"></i>
                                        Receipt
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small p-3">
                            No payments recorded yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection