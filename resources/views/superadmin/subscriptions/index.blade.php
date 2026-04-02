@extends('layouts.superadmin.superadmin')

@section('title', 'Subscription Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Subscription Management</h4>
            <p class="text-muted mb-0">Monitor and manage all school subscriptions.</p>
        </div>
        <form action="{{ route('superadmin.subscriptions.run-check') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-primary"
                    onclick="return confirm('Run subscription check now?')">
                <i class="icon-base ti tabler-refresh me-1"></i>
                Run Check Now
            </button>
        </form>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('check_output'))
        <div class="alert alert-info alert-dismissible mb-4">
            <strong>Check Output:</strong>
            <pre class="mb-0 mt-2 small">{{ session('check_output') }}</pre>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Summary Stats --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <div class="avatar mx-auto mb-2">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="icon-base ti tabler-school"></i>
                        </span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $stats['total'] }}</h3>
                    <p class="text-muted small mb-0">Total Schools</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <div class="avatar mx-auto mb-2">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="icon-base ti tabler-circle-check"></i>
                        </span>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">{{ $stats['active'] }}</h3>
                    <p class="text-muted small mb-0">Active</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <div class="avatar mx-auto mb-2">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="icon-base ti tabler-alert-triangle"></i>
                        </span>
                    </div>
                    <h3 class="fw-bold mb-1 text-warning">
                        {{ $stats['grace_warning'] + $stats['grace_readonly'] }}
                    </h3>
                    <p class="text-muted small mb-0">In Grace Period</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <div class="avatar mx-auto mb-2">
                        <span class="avatar-initial rounded bg-label-danger">
                            <i class="icon-base ti tabler-lock"></i>
                        </span>
                    </div>
                    <h3 class="fw-bold mb-1 text-danger">{{ $stats['suspended'] }}</h3>
                    <p class="text-muted small mb-0">Suspended</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Overdue Revenue Alert --}}
    @if($overdueRevenue > 0)
        <div class="alert alert-warning mb-4">
            <i class="icon-base ti tabler-currency-rupee me-1"></i>
            <strong>₹{{ number_format($overdueRevenue) }}</strong>
            in revenue at risk from overdue subscriptions.
        </div>
    @endif

    {{-- Subscriptions Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All School Subscriptions</h5>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" id="statusFilter"
                        style="width:auto" onchange="filterTable()">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="grace_warning">Grace Warning</option>
                    <option value="grace_readonly">Read-Only</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="subscriptionsTable">
                <thead>
                    <tr>
                        <th>School</th>
                        <th>Billing Cycle</th>
                        <th>Period</th>
                        <th class="text-center">Students</th>
                        <th class="text-end">Amount Due</th>
                        <th class="text-center">Overdue</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $sub)
                        <tr data-status="{{ $sub->status }}">
                            <td>
                                <a href="{{ route('superadmin.subscriptions.show', $sub->tenant) }}"
                                   class="fw-semibold text-body">
                                    {{ $sub->tenant->school_name }}
                                </a>
                                <div class="text-muted small">
                                    {{ $sub->tenant->domains->first()?->domain ?? '—' }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary">
                                    {{ ucfirst(str_replace('_', ' ', $sub->billing_cycle)) }}
                                </span>
                            </td>
                            <td>
                                <span class="small">
                                    {{ $sub->period_start->format('d M Y') }}<br>
                                    → {{ $sub->period_end->format('d M Y') }}
                                </span>
                            </td>
                            <td class="text-center">
                                {{ number_format($sub->student_count_snapshot) }}
                            </td>
                            <td class="text-end fw-semibold">
                                ₹{{ number_format($sub->amount_due) }}
                            </td>
                            <td class="text-center">
                                @if($sub->days_overdue > 0)
                                    <span class="badge bg-danger">
                                        {{ $sub->days_overdue }} days
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $statusColors = [
                                        'active'         => 'success',
                                        'grace_warning'  => 'warning',
                                        'grace_readonly' => 'danger',
                                        'suspended'      => 'dark',
                                    ];
                                @endphp
                                <span class="badge bg-label-{{ $statusColors[$sub->status] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $sub->status)) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('superadmin.subscriptions.show', $sub->tenant) }}"
                                       class="btn btn-sm btn-icon btn-outline-primary"
                                       title="View History">
                                        <i class="icon-base ti tabler-eye"></i>
                                    </a>
                                    @if(in_array($sub->status, ['grace_warning', 'grace_readonly', 'suspended']))
                                        <button type="button"
                                                class="btn btn-sm btn-icon btn-outline-success"
                                                title="Mark Paid"
                                                data-bs-toggle="modal"
                                                data-bs-target="#markPaidModal{{ $sub->id }}">
                                            <i class="icon-base ti tabler-check"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-sm btn-icon btn-outline-warning"
                                                title="Waive"
                                                data-bs-toggle="modal"
                                                data-bs-target="#waiveModal{{ $sub->id }}">
                                            <i class="icon-base ti tabler-gift"></i>
                                        </button>
                                    @endif
                                    @if($sub->status === 'suspended')
                                        <form action="{{ route('superadmin.subscriptions.reactivate', $sub->tenant) }}"
                                              method="POST"
                                              onsubmit="return confirm('Reactivate {{ $sub->tenant->school_name }}?')">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-sm btn-icon btn-outline-info"
                                                    title="Reactivate">
                                                <i class="icon-base ti tabler-refresh"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Mark Paid Modal --}}
                        <div class="modal fade" id="markPaidModal{{ $sub->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('superadmin.subscriptions.mark-paid', $sub) }}"
                                          method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="icon-base ti tabler-check me-2 text-success"></i>
                                                Mark as Paid — {{ $sub->tenant->school_name }}
                                            </h5>
                                            <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-info small mb-3">
                                                Amount due:
                                                <strong>₹{{ number_format($sub->amount_due) }}</strong>
                                                for {{ $sub->billing_cycle }} cycle
                                                ({{ $sub->period_start->format('d M Y') }}
                                                → {{ $sub->period_end->format('d M Y') }})
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">
                                                    Amount Received <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text">₹</span>
                                                    <input type="number"
                                                           name="amount_paid"
                                                           class="form-control"
                                                           value="{{ $sub->amount_due }}"
                                                           step="0.01"
                                                           min="0"
                                                           required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">
                                                    Payment Reference
                                                </label>
                                                <input type="text"
                                                       name="payment_reference"
                                                       class="form-control"
                                                       placeholder="UPI ref, cheque no, transaction ID...">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Notes</label>
                                                <textarea name="notes"
                                                          class="form-control"
                                                          rows="2"
                                                          placeholder="Optional notes..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button"
                                                    class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="icon-base ti tabler-check me-1"></i>
                                                Confirm Payment
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Waive Modal --}}
                        <div class="modal fade" id="waiveModal{{ $sub->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('superadmin.subscriptions.waive', $sub) }}"
                                          method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="icon-base ti tabler-gift me-2 text-warning"></i>
                                                Waive Subscription — {{ $sub->tenant->school_name }}
                                            </h5>
                                            <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-warning small mb-3">
                                                You are waiving
                                                <strong>₹{{ number_format($sub->amount_due) }}</strong>
                                                for this billing cycle.
                                                A new cycle will be created automatically.
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">
                                                    Reason <span class="text-danger">*</span>
                                                </label>
                                                <textarea name="notes"
                                                          class="form-control"
                                                          rows="3"
                                                          placeholder="Reason for waiving this subscription..."
                                                          required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button"
                                                    class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-warning">
                                                <i class="icon-base ti tabler-gift me-1"></i>
                                                Confirm Waive
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="icon-base ti tabler-receipt-off"
                                   style="font-size:2.5rem; color:#ccc;"></i>
                                <p class="text-muted mt-2 mb-0">
                                    No subscriptions found.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function filterTable() {
        const filter = document.getElementById('statusFilter').value;
        const rows   = document.querySelectorAll('#subscriptionsTable tbody tr[data-status]');
        rows.forEach(row => {
            row.style.display = (!filter || row.dataset.status === filter) ? '' : 'none';
        });
    }
</script>
@endpush

@endsection