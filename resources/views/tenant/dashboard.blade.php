@extends('layouts.tenant')

@section('title', 'Dashboard')

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">
            <span class="text-muted fw-light">{{ $school->school_name }} /</span> Dashboard
        </h4>
        <p class="text-muted mb-0 small">
            {{ now()->format('l, d F Y') }}
        </p>
    </div>
    <div>
        <span class="badge bg-label-primary fs-6 px-3 py-2">
            <i class="icon-base ti tabler-user me-1"></i>
            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
        </span>
    </div>
</div>

{{-- Welcome Banner --}}
<div class="card bg-primary text-white mb-4">
    <div class="card-body d-flex justify-content-between align-items-center py-4">
        <div>
            <h5 class="text-white fw-bold mb-1">
                Welcome back, {{ $user->name }}! 👋
            </h5>
            <p class="mb-0 opacity-75 small">
                Here's what's happening at {{ $school->school_name }} today.
            </p>
        </div>
        <div class="d-none d-sm-block">
            <i class="icon-base ti tabler-school"
               style="font-size: 3.5rem; opacity: 0.6;"></i>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-4 mb-4">

    {{-- Students --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="icon-base ti tabler-users"></i>
                        </span>
                    </div>
                    <span class="badge bg-label-primary">Students</span>
                </div>
                <h3 class="fw-bold mb-1">{{ number_format($totalStudents) }}</h3>
                <p class="text-muted mb-2 small">Total Students</p>
                <div class="d-flex gap-3 small">
                    <span class="text-success">
                        <i class="icon-base ti tabler-circle-filled me-1"
                           style="font-size:8px"></i>
                        {{ number_format($activeStudents) }} Active
                    </span>
                    <span class="text-muted">
                        {{ number_format($totalStudents - $activeStudents) }} Inactive
                    </span>
                </div>
                @if($recentStudents > 0)
                    <div class="mt-2">
                        <span class="badge bg-label-success small">
                            +{{ $recentStudents }} this month
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Staff --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="icon-base ti tabler-chalkboard"></i>
                        </span>
                    </div>
                    <span class="badge bg-label-success">Staff</span>
                </div>
                <h3 class="fw-bold mb-1">{{ number_format($totalStaff) }}</h3>
                <p class="text-muted mb-2 small">Total Staff</p>
                <div class="d-flex gap-3 small">
                    <span class="text-success">
                        <i class="icon-base ti tabler-circle-filled me-1"
                           style="font-size:8px"></i>
                        {{ number_format($activeStaff) }} Active
                    </span>
                    <span class="text-muted">
                        {{ number_format($totalStaff - $activeStaff) }} Inactive
                    </span>
                </div>
                @if($recentStaff > 0)
                    <div class="mt-2">
                        <span class="badge bg-label-success small">
                            +{{ $recentStaff }} this month
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Parents --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="icon-base ti tabler-users-group"></i>
                        </span>
                    </div>
                    <span class="badge bg-label-info">Parents</span>
                </div>
                <h3 class="fw-bold mb-1">{{ number_format($totalParents) }}</h3>
                <p class="text-muted mb-2 small">Total Parents</p>
                <div class="d-flex gap-3 small">
                    <span class="text-success">
                        <i class="icon-base ti tabler-circle-filled me-1"
                           style="font-size:8px"></i>
                        {{ number_format($activeParents) }} Active
                    </span>
                    <span class="text-muted">
                        {{ number_format($totalParents - $activeParents) }} Inactive
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Monthly Bill --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="icon-base ti tabler-currency-rupee"></i>
                        </span>
                    </div>
                    <span class="badge bg-label-warning">Billing</span>
                </div>
                <h3 class="fw-bold mb-1">₹{{ number_format($monthlyBill) }}</h3>
                <p class="text-muted mb-2 small">Est. Monthly Bill</p>
                <p class="small text-muted mb-0">
                    {{ number_format($activeStudents) }} students ×
                    ₹{{ $school->per_student_rate }}
                </p>
                @if($subscription)
                    <div class="mt-2">
                        <span class="badge bg-label-{{ $subscription->status === 'active' ? 'success' : 'danger' }}">
                            {{ ucfirst(str_replace('_', ' ', $subscription->status)) }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

<div class="row g-4">

    {{-- Gender Breakdown --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="icon-base ti tabler-chart-donut me-2 text-primary"></i>
                    Student Gender
                </h5>
                <p class="text-muted small mb-0">Breakdown by gender</p>
            </div>
            <div class="card-body">
                @if(array_sum($genderStats) > 0)
                    @php
                        $total = array_sum($genderStats);
                        $genderColors = [
                            'male'   => 'primary',
                            'female' => 'danger',
                            'other'  => 'warning',
                        ];
                    @endphp
                    @foreach($genderStats as $gender => $count)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-semibold text-capitalize">
                                    {{ $gender }}
                                </span>
                                <span class="small text-muted">
                                    {{ $count }} ({{ round(($count / $total) * 100) }}%)
                                </span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ $genderColors[$gender] ?? 'secondary' }}"
                                     style="width: {{ round(($count / $total) * 100) }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Total with profile</span>
                        <span class="fw-semibold small">{{ $total }}</span>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="icon-base ti tabler-chart-donut"
                           style="font-size: 2.5rem; color: #ccc;"></i>
                        <p class="text-muted mt-2 mb-0 small">
                            No student profiles yet.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Subscription Info --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="icon-base ti tabler-receipt me-2 text-warning"></i>
                    Subscription
                </h5>
                <p class="text-muted small mb-0">Current billing cycle</p>
            </div>
            <div class="card-body">
                @if($subscription)
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Status</p>
                        <span class="badge bg-label-{{ $subscription->status === 'active' ? 'success' : ($subscription->status === 'grace_warning' ? 'warning' : 'danger') }} fs-6">
                            {{ ucfirst(str_replace('_', ' ', $subscription->status)) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Billing Cycle</p>
                        <p class="fw-semibold mb-0">
                            {{ ucfirst(str_replace('_', ' ', $subscription->billing_cycle)) }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Period</p>
                        <p class="fw-semibold mb-0">
                            {{ $subscription->period_start->format('d M Y') }}
                            →
                            {{ $subscription->period_end->format('d M Y') }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Amount Due</p>
                        <h5 class="fw-bold text-primary mb-0">
                            ₹{{ number_format($subscription->amount_due) }}
                        </h5>
                    </div>
                    @if($subscription->days_overdue > 0)
                        <div class="alert alert-danger py-2 mb-0 small">
                            <i class="icon-base ti tabler-alert-circle me-1"></i>
                            {{ $subscription->days_overdue }} days overdue.
                            Contact your administrator.
                        </div>
                    @else
                        <div class="alert alert-success py-2 mb-0 small">
                            <i class="icon-base ti tabler-circle-check me-1"></i>
                            Account is in good standing.
                        </div>
                    @endif
                @else
                    <p class="text-muted small text-center py-4 mb-0">
                        No active subscription found.
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="icon-base ti tabler-bolt me-2 text-info"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body d-grid gap-2">
                @if($user->isSchoolAdmin())
                    <a href="#" class="btn btn-primary">
                        <i class="icon-base ti tabler-user-plus me-1"></i>
                        Add Student
                    </a>
                    <a href="#" class="btn btn-outline-success">
                        <i class="icon-base ti tabler-user-check me-1"></i>
                        Add Staff
                    </a>
                    <a href="#" class="btn btn-outline-info">
                        <i class="icon-base ti tabler-users-group me-1"></i>
                        Add Parent
                    </a>
                @elseif($user->isTeacher())
                    <a href="#" class="btn btn-primary">
                        <i class="icon-base ti tabler-clipboard-list me-1"></i>
                        Mark Attendance
                    </a>
                    <a href="#" class="btn btn-outline-primary">
                        <i class="icon-base ti tabler-users me-1"></i>
                        My Students
                    </a>
                @else
                    <a href="#" class="btn btn-primary">
                        <i class="icon-base ti tabler-layout-dashboard me-1"></i>
                        Go to Dashboard
                    </a>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection