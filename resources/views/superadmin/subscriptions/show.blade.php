@extends('layouts.superadmin.superadmin')

@section('title', $school->school_name . ' — Subscription History')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('superadmin.subscriptions.index') }}"
           class="btn btn-icon btn-outline-secondary me-3">
            <i class="icon-base ti tabler-arrow-left"></i>
        </a>
        <div class="flex-grow-1">
            <h4 class="fw-bold mb-0">{{ $school->school_name }}</h4>
            <p class="text-muted mb-0 small">
                Subscription history & management
            </p>
        </div>
        @if($school->subscription_status === 'suspended')
            <form action="{{ route('superadmin.subscriptions.reactivate', $school) }}"
                  method="POST"
                  onsubmit="return confirm('Reactivate {{ $school->school_name }}?')">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="icon-base ti tabler-refresh me-1"></i>
                    Reactivate School
                </button>
            </form>
        @endif
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- Left — History --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="icon-base ti tabler-history me-2 text-primary"></i>
                        Billing History
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th class="text-center">Students</th>
                                <th class="text-end">Amount Due</th>
                                <th class="text-end">Amount Paid</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $sub)
                                @php
                                    $statusColors = [
                                        'active'         => 'success',
                                        'grace_warning'  => 'warning',
                                        'grace_readonly' => 'danger',
                                        'suspended'      => 'dark',
                                        'paid'           => 'primary',
                                        'waived'         => 'info',
                                    ];
                                @endphp
                                <tr>
                                    <td>
                                        <span class="fw-semibold small">
                                            {{ $sub->period_start->format('d M Y') }}
                                        </span>
                                        <br>
                                        <span class="text-muted small">
                                            → {{ $sub->period_end->format('d M Y') }}
                                        </span>
                                        <div>
                                            <span class="badge bg-label-secondary"
                                                  style="font-size:10px">
                                                {{ ucfirst(str_replace('_', ' ', $sub->billing_cycle)) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        {{ number_format($sub->student_count_snapshot) }}
                                        <div class="text-muted small">
                                            ×₹{{ $sub->per_student_rate }}
                                        </div>
                                    </td>
                                    <td class="text-end fw-semibold">
                                        ₹{{ number_format($sub->amount_due) }}
                                    </td>
                                    <td class="text-end">
                                        @if($sub->amount_paid)
                                            <span class="text-success fw-semibold">
                                                ₹{{ number_format($sub->amount_paid) }}
                                            </span>
                                            @if($sub->payment_reference)
                                                <div class="text-muted small">
                                                    Ref: {{ $sub->payment_reference }}
                                                </div>
                                            @endif
                                            @if($sub->paid_at)
                                                <div class="text-muted small">
                                                    {{ $sub->paid_at->format('d M Y') }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-{{ $statusColors[$sub->status] ?? 'secondary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $sub->status)) }}
                                        </span>
                                        @if($sub->days_overdue > 0)
                                            <div class="text-danger small mt-1">
                                                {{ $sub->days_overdue }}d overdue
                                            </div>
                                        @endif
                                        @if($sub->notes)
                                            <div class="text-muted small mt-1"
                                                 title="{{ $sub->notes }}">
                                                <i class="icon-base ti tabler-note me-1"></i>
                                                Note
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(in_array($sub->status, ['grace_warning', 'grace_readonly', 'suspended']))
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button"
                                                        class="btn btn-xs btn-outline-success"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#markPaidModal{{ $sub->id }}">
                                                    <i class="icon-base ti tabler-check me-1"></i>
                                                    Pay
                                                </button>
                                                <button type="button"
                                                        class="btn btn-xs btn-outline-warning"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#waiveModal{{ $sub->id }}">
                                                    Waive
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Mark Paid Modal --}}
                                @if(in_array($sub->status, ['grace_warning', 'grace_readonly', 'suspended']))
                                <div class="modal fade" id="markPaidModal{{ $sub->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('superadmin.subscriptions.mark-paid', $sub) }}"
                                                  method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Mark as Paid</h5>
                                                    <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">
                                                            Amount Received
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">₹</span>
                                                            <input type="number"
                                                                   name="amount_paid"
                                                                   class="form-control"
                                                                   value="{{ $sub->amount_due }}"
                                                                   step="0.01"
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
                                                               placeholder="UPI ref, cheque no...">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">
                                                            Notes
                                                        </label>
                                                        <textarea name="notes"
                                                                  class="form-control"
                                                                  rows="2"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button"
                                                            class="btn btn-outline-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success">
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
                                                    <h5 class="modal-title">Waive Subscription</h5>
                                                    <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">
                                                            Reason <span class="text-danger">*</span>
                                                        </label>
                                                        <textarea name="notes"
                                                                  class="form-control"
                                                                  rows="3"
                                                                  required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button"
                                                            class="btn btn-outline-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-warning">
                                                        Confirm Waive
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif

                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No billing history yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($history->hasPages())
                    <div class="card-footer">
                        {{ $history->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Right — School Info --}}
        <div class="col-lg-4">

            {{-- Current Status --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="icon-base ti tabler-info-circle me-2 text-primary"></i>
                        Current Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Subscription Status</p>
                        @php
                            $statusColors = [
                                'active'         => 'success',
                                'grace_warning'  => 'warning',
                                'grace_readonly' => 'danger',
                                'suspended'      => 'dark',
                            ];
                        @endphp
                        <span class="badge bg-label-{{ $statusColors[$school->subscription_status] ?? 'secondary' }} fs-6">
                            {{ ucfirst(str_replace('_', ' ', $school->subscription_status)) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Billing Cycle</p>
                        <p class="fw-semibold mb-0">
                            {{ ucfirst(str_replace('_', ' ', $school->billing_cycle)) }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Rate per Student</p>
                        <p class="fw-semibold mb-0">
                            ₹{{ $school->per_student_rate }}/student/month
                        </p>
                    </div>
                    @if($current)
                        <div class="mb-3">
                            <p class="text-muted small mb-1">Current Period</p>
                            <p class="fw-semibold mb-0 small">
                                {{ $current->period_start->format('d M Y') }}
                                → {{ $current->period_end->format('d M Y') }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted small mb-1">Amount Due</p>
                            <h5 class="fw-bold text-primary mb-0">
                                ₹{{ number_format($current->amount_due) }}
                            </h5>
                            <p class="text-muted small">
                                {{ number_format($current->student_count_snapshot) }}
                                students × ₹{{ $current->per_student_rate }}
                            </p>
                        </div>
                        @if($current->days_overdue > 0)
                            <div class="alert alert-danger py-2 small mb-0">
                                <i class="icon-base ti tabler-alert-circle me-1"></i>
                                <strong>{{ $current->days_overdue }} days overdue</strong>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- School Info --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="icon-base ti tabler-building-school me-2 text-info"></i>
                        School Info
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-1">Email</p>
                    <p class="fw-semibold mb-3">
                        <a href="mailto:{{ $school->email }}">{{ $school->email }}</a>
                    </p>
                    <p class="text-muted small mb-1">Domain</p>
                    <p class="fw-semibold mb-3">
                        {{ $school->domains->first()?->domain ?? '—' }}
                    </p>
                    <p class="text-muted small mb-1">Provisioned</p>
                    <p class="fw-semibold mb-3">
                        {{ $school->provisioned_at?->format('d M Y') ?? '—' }}
                    </p>
                    <a href="{{ route('superadmin.schools.show', $school) }}"
                       class="btn btn-outline-primary btn-sm w-100">
                        <i class="icon-base ti tabler-external-link me-1"></i>
                        View School Details
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection