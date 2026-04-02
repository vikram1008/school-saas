@extends('layouts.superadmin.superadmin')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Welcome Banner --}}
    <div class="card bg-primary text-white mb-4">
        <div class="card-body d-flex justify-content-between align-items-center py-4">
            <div>
                <h4 class="card-title text-white mb-1">
                    Welcome back, {{ auth()->user()->name }}! 🚀
                </h4>
                <p class="mb-0 opacity-75">
                    Here's what's happening across all your schools today.
                </p>
            </div>
            <div class="d-none d-sm-flex align-items-center gap-3">
                <div class="text-end">
                    <div class="text-white opacity-75 small">Today</div>
                    <div class="text-white fw-semibold">{{ now()->format('d M Y') }}</div>
                </div>
                <i class="icon-base ti tabler-building-community" style="font-size: 3.5rem; opacity: 0.6;"></i>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-4 mb-4">

        {{-- Total Schools --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ti tabler-building"></i>
                            </span>
                        </div>
                        <span class="badge bg-label-primary">Total</span>
                    </div>
                    <h3 class="mb-1 fw-bold">{{ $totalSchools }}</h3>
                    <p class="mb-2 text-muted">Total Schools</p>
                    <div class="d-flex gap-3 small">
                        <span class="text-success">
                            <i class="icon-base ti tabler-circle-filled me-1" style="font-size:8px"></i>
                            {{ $activeSchools }} Active
                        </span>
                        <span class="text-danger">
                            <i class="icon-base ti tabler-circle-filled me-1" style="font-size:8px"></i>
                            {{ $inactiveSchools }} Inactive
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Active Students --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-users"></i>
                            </span>
                        </div>
                        <span class="badge bg-label-success">Live</span>
                    </div>
                    <h3 class="mb-1 fw-bold">{{ number_format($totalStudents) }}</h3>
                    <p class="mb-2 text-muted">Active Students</p>
                    <p class="small text-muted mb-0">
                        Across {{ $activeSchools }} active schools
                    </p>
                </div>
            </div>
        </div>

        {{-- Monthly Revenue --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-currency-rupee"></i>
                            </span>
                        </div>
                        <span class="badge bg-label-warning">This Month</span>
                    </div>
                    <h3 class="mb-1 fw-bold">₹{{ number_format($currentMonthRev) }}</h3>
                    <p class="mb-2 text-muted">Monthly Revenue</p>
                    <p class="small text-muted mb-0">
                        Based on {{ number_format($totalStudents) }} active students
                    </p>
                </div>
            </div>
        </div>

        {{-- Annual Projection --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="icon-base ti tabler-chart-line"></i>
                            </span>
                        </div>
                        <span class="badge bg-label-info">Projected</span>
                    </div>
                    <h3 class="mb-1 fw-bold">₹{{ number_format($currentMonthRev * 12) }}</h3>
                    <p class="mb-2 text-muted">Annual Projection</p>
                    <p class="small text-muted mb-0">
                        At current student count
                    </p>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-4">

        {{-- Schools Breakdown Table --}}
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="icon-base ti tabler-building me-2 text-primary"></i>
                            Schools Overview
                        </h5>
                        <p class="text-muted small mb-0">Live student counts & billing per school</p>
                    </div>
                    <a href="{{ route('superadmin.schools.index') }}"
                       class="btn btn-sm btn-outline-primary">
                        <i class="icon-base ti tabler-arrow-right me-1"></i> View All
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>School</th>
                                <th>Domain</th>
                                <th class="text-center">Students</th>
                                <th class="text-center">Rate</th>
                                <th class="text-end">Monthly Bill</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schoolStats as $stat)
                                <tr>
                                    <td>
                                        <a href="{{ route('superadmin.schools.show', $stat['id']) }}"
                                           class="fw-semibold text-body">
                                            {{ $stat['name'] }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($stat['domain'])
                                            <span class="text-muted small">{{ $stat['domain'] }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-semibold">{{ number_format($stat['students']) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-secondary">
                                            ₹{{ $stat['rate'] }}/student
                                        </span>
                                    </td>
                                    <td class="text-end fw-semibold text-success">
                                        ₹{{ number_format($stat['monthly_bill']) }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-{{ $stat['is_active'] ? 'success' : 'danger' }}">
                                            {{ $stat['is_active'] ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="icon-base ti tabler-building-off mb-2 d-block"
                                           style="font-size:2rem"></i>
                                        No schools provisioned yet.
                                        <a href="{{ route('superadmin.schools.create') }}"
                                           class="d-block mt-1">Add first school</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($schoolStats) > 0)
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="fw-bold">Total</td>
                                <td class="text-center fw-bold">{{ number_format($totalStudents) }}</td>
                                <td></td>
                                <td class="text-end fw-bold text-success">
                                    ₹{{ number_format($currentMonthRev) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">

            {{-- Revenue Breakdown --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="icon-base ti tabler-chart-donut me-2 text-warning"></i>
                        Revenue Breakdown
                    </h5>
                    <p class="text-muted small mb-0">Per school this month</p>
                </div>
                <div class="card-body">
                    @forelse($schoolStats as $stat)
                        @if($stat['monthly_bill'] > 0)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small fw-semibold text-truncate me-2"
                                          style="max-width:150px">
                                        {{ $stat['name'] }}
                                    </span>
                                    <span class="small text-success fw-semibold">
                                        ₹{{ number_format($stat['monthly_bill']) }}
                                    </span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-primary"
                                         style="width: {{ $currentMonthRev > 0 ? round(($stat['monthly_bill'] / $currentMonthRev) * 100) : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <p class="text-muted small text-center mb-0">No revenue data yet.</p>
                    @endforelse

                    @if($currentMonthRev > 0)
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold">Total This Month</span>
                            <span class="fw-bold text-success fs-6">
                                ₹{{ number_format($currentMonthRev) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="icon-base ti tabler-bolt me-2 text-info"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('superadmin.schools.create') }}"
                       class="btn btn-primary">
                        <i class="icon-base ti tabler-plus me-1"></i>
                        Add New School
                    </a>
                    <a href="{{ route('superadmin.schools.index') }}"
                       class="btn btn-outline-secondary">
                        <i class="icon-base ti tabler-building me-1"></i>
                        Manage Schools
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection