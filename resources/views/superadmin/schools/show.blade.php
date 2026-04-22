@extends('layouts.superadmin.superadmin')

@section('title', $school->school_name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Page Header --}}
    <div class="d-flex align-items-start gap-3 mb-5">
        <a href="{{ route('superadmin.schools.index') }}" class="btn btn-icon btn-outline-secondary mt-1">
            <i class="icon-base ti tabler-arrow-left"></i>
        </a>

        {{-- Logo --}}
        <div class="border rounded-3 d-flex align-items-center justify-content-center"
             style="width:80px;height:80px;background:#f8f8f8;flex-shrink:0;overflow:hidden;padding:4px;">
            <img src="{{ $school->logo_url }}"
                 alt="{{ $school->school_name }}"
                 style="max-width:72px;max-height:72px;object-fit:contain;">
        </div>

        <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <h4 class="fw-bold mb-0">{{ $school->school_name }}</h4>
                @if($school->school_name_hi)
                    <span class="text-muted small">({{ $school->school_name_hi }})</span>
                @endif
                <span class="badge bg-label-{{ $school->is_active ? 'success' : 'danger' }}">
                    {{ $school->is_active ? 'Active' : 'Inactive' }}
                </span>
                <span class="badge bg-label-{{ match($school->subscription_status) { 'active' => 'primary', 'grace_warning' => 'warning', 'grace_readonly' => 'orange', default => 'danger' } }}">
                    {{ ucfirst(str_replace('_', ' ', $school->subscription_status)) }}
                </span>
            </div>
            @if($school->tagline)
                <p class="text-muted fst-italic small mb-1">{{ $school->tagline }}</p>
            @endif
            <p class="text-muted mb-0 small">
                <i class="ti tabler-id me-1"></i><code>{{ $school->id }}</code>
                &nbsp;·&nbsp;
                <i class="ti tabler-database me-1"></i><code>school_{{ $school->id }}</code>
                @if($school->primary_domain)
                    &nbsp;·&nbsp;
                    <i class="ti tabler-world me-1"></i>
                    <a href="http://{{ $school->primary_domain }}" target="_blank" class="text-primary">
                        {{ $school->primary_domain }}
                    </a>
                @endif
            </p>
        </div>

        <div class="d-flex gap-2 flex-shrink-0">
            <a href="{{ route('superadmin.schools.edit', $school) }}" class="btn btn-primary">
                <i class="ti tabler-edit me-1"></i> Edit School
            </a>
            <a href="{{ route('superadmin.subscriptions.show', $school) }}" class="btn btn-outline-warning">
                <i class="ti tabler-receipt-rupee me-1"></i> Billing
            </a>
            <form action="{{ route('superadmin.schools.destroy', $school) }}"
                  method="POST"
                  onsubmit="return confirm('Permanently delete {{ addslashes($school->school_name) }} and ALL its data?\n\nThis cannot be undone.')">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger">
                    <i class="ti tabler-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="ti tabler-circle-check me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── STATS ROW ───────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        @php
            $statCards = [
                ['label' => 'Students', 'value' => $stats['students'], 'icon' => 'tabler-users', 'color' => 'primary'],
                ['label' => 'Staff',    'value' => $stats['staff'],    'icon' => 'tabler-chalkboard', 'color' => 'success'],
                ['label' => 'Parents',  'value' => $stats['parents'],  'icon' => 'tabler-users-group', 'color' => 'info'],
                ['label' => 'Classes',  'value' => $stats['classes'],  'icon' => 'tabler-school', 'color' => 'warning'],
            ];
        @endphp
        @foreach($statCards as $s)
            <div class="col-6 col-md-3">
                <div class="card text-center h-100">
                    <div class="card-body py-3">
                        <div class="badge bg-label-{{ $s['color'] }} rounded-circle mb-2" style="width:42px;height:42px;display:inline-flex;align-items:center;justify-content:center;">
                            <i class="ti {{ $s['icon'] }} fs-5"></i>
                        </div>
                        <h3 class="fw-bold mb-0">{{ number_format($s['value']) }}</h3>
                        <p class="text-muted small mb-0">{{ $s['label'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">

        {{-- ── LEFT COLUMN ──────────────────────────────────────────── --}}
        <div class="col-lg-8">

            {{-- Identity & Contact --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="ti tabler-building fs-5 text-primary"></i>
                    <h5 class="mb-0">School Details</h5>
                    <a href="{{ route('superadmin.schools.edit', $school) }}" class="btn btn-sm btn-outline-primary ms-auto">
                        <i class="ti tabler-edit me-1"></i>Edit
                    </a>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        {{-- Identity row --}}
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">School Name</p>
                            <p class="fw-semibold mb-0">{{ $school->school_name }}</p>
                            @if($school->school_name_hi)
                                <p class="text-muted small mb-0">{{ $school->school_name_hi }}</p>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Tagline</p>
                            <p class="fw-semibold mb-0">{{ $school->tagline ?: '—' }}</p>
                        </div>

                        {{-- Contact row --}}
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Official Email</p>
                            <p class="fw-semibold mb-0">
                                <a href="mailto:{{ $school->email }}" class="text-primary">{{ $school->email }}</a>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Website</p>
                            <p class="fw-semibold mb-0">
                                @if($school->website)
                                    <a href="{{ $school->website }}" target="_blank" class="text-primary">
                                        {{ $school->website }}
                                    </a>
                                @else —
                                @endif
                            </p>
                        </div>

                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Phone</p>
                            <p class="fw-semibold mb-0">
                                {{ $school->phone ?: '—' }}
                                @if($school->phone_alt)
                                    <span class="text-muted small"> · {{ $school->phone_alt }}</span>
                                @endif
                            </p>
                        </div>

                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Registered On</p>
                            <p class="fw-semibold mb-0">{{ $school->created_at->format('d M Y') }}</p>
                        </div>

                        {{-- Address --}}
                        @if($school->full_address)
                            <div class="col-12">
                                <hr class="my-1">
                                <p class="text-muted small mb-1 mt-2">Address</p>
                                <p class="fw-semibold mb-0">
                                    <i class="ti tabler-map-pin me-1 text-muted"></i>
                                    {{ $school->full_address }}
                                </p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

            {{-- Academic Details --}}
            @if($school->board_affiliation || $school->school_code || $school->udise_code)
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="ti tabler-certificate fs-5 text-warning"></i>
                        <h5 class="mb-0">Academic Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @if($school->board_affiliation)
                                <div class="col-md-4">
                                    <p class="text-muted small mb-1">Board / Affiliation</p>
                                    <span class="badge bg-label-primary fs-6">{{ $school->board_affiliation }}</span>
                                </div>
                            @endif
                            @if($school->school_code)
                                <div class="col-md-4">
                                    <p class="text-muted small mb-1">School Code</p>
                                    <code class="fs-6">{{ $school->school_code }}</code>
                                </div>
                            @endif
                            @if($school->udise_code)
                                <div class="col-md-4">
                                    <p class="text-muted small mb-1">UDISE Code</p>
                                    <code class="fs-6">{{ $school->udise_code }}</code>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Domain & Database --}}
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="ti tabler-world fs-5 text-info"></i>
                    <h5 class="mb-0">Domain &amp; Database</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Domains</p>
                            @forelse($school->domains as $domain)
                                <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded mb-2">
                                    <div>
                                        <i class="ti tabler-link me-1 text-primary"></i>
                                        <span class="fw-semibold small">{{ $domain->domain }}</span>
                                    </div>
                                    <a href="http://{{ $domain->domain }}" target="_blank" class="btn btn-sm btn-outline-primary py-1">
                                        <i class="ti tabler-external-link"></i>
                                    </a>
                                </div>
                            @empty
                                <p class="text-muted small mb-0">No domains assigned.</p>
                            @endforelse
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Database</p>
                            <div class="p-2 bg-light rounded">
                                <code>school_{{ $school->id }}</code>
                                <p class="text-muted small mb-0 mt-1">MySQL · Isolated · Dedicated</p>
                            </div>
                            <p class="text-muted small mt-2 mb-0">
                                <i class="ti tabler-calendar me-1"></i>
                                Provisioned: {{ $school->provisioned_at?->format('d M Y') ?? '—' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── RIGHT COLUMN ──────────────────────────────────────────── --}}
        <div class="col-lg-4">

            {{-- Billing Card --}}
            <div class="card mb-4 border-primary border-opacity-25">
                <div class="card-header bg-label-primary d-flex align-items-center gap-2">
                    <i class="ti tabler-receipt-rupee fs-5 text-primary"></i>
                    <h6 class="mb-0 fw-bold text-primary">Billing</h6>
                    <a href="{{ route('superadmin.subscriptions.show', $school) }}"
                       class="btn btn-sm btn-primary ms-auto">
                        View History
                    </a>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <p class="text-muted small mb-1">Rate per Student</p>
                        <h2 class="fw-bold text-primary mb-0">
                            ₹{{ $school->per_student_rate }}
                            <small class="fs-6 text-muted fw-normal">/student/month</small>
                        </h2>
                        <p class="text-muted small mb-0 mt-1">
                            Cycle: {{ ucfirst(str_replace('_', ' ', $school->billing_cycle)) }}
                        </p>
                    </div>
                    <hr>
                    <p class="text-muted small fw-semibold mb-2">Estimated Monthly Bills</p>
                    <table class="table table-sm table-borderless mb-0 small">
                        @foreach([100, 300, 500, 1000] as $n)
                            <tr @if($n === 1000) class="border-top" @endif>
                                <td class="text-muted ps-0">{{ number_format($n) }} students</td>
                                <td class="fw-semibold text-end {{ $n === 1000 ? 'text-primary fw-bold' : '' }}">
                                    ₹{{ number_format($n * $school->per_student_rate) }}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    @if($school->latestSubscription)
                        <hr>
                        <p class="text-muted small mb-1 fw-semibold">Current Cycle</p>
                        <p class="small mb-1">
                            {{ $school->latestSubscription->period_start->format('d M Y') }}
                            → {{ $school->latestSubscription->period_end->format('d M Y') }}
                        </p>
                        <span class="badge bg-label-{{ match($school->latestSubscription->status) {
                            'active' => 'success', 'paid' => 'primary',
                            'grace_warning', 'grace_readonly' => 'warning',
                            default => 'danger' } }}">
                            {{ ucfirst(str_replace('_', ' ', $school->latestSubscription->status)) }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="ti tabler-bolt fs-5 text-warning"></i>
                    <h6 class="mb-0 fw-bold">Quick Actions</h6>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('superadmin.schools.edit', $school) }}" class="btn btn-outline-primary">
                        <i class="ti tabler-edit me-1"></i> Edit School
                    </a>
                    <a href="{{ route('superadmin.subscriptions.show', $school) }}" class="btn btn-outline-warning">
                        <i class="ti tabler-receipt-rupee me-1"></i> Manage Billing
                    </a>
                    @if($school->primary_domain)
                        <a href="http://{{ $school->primary_domain }}" target="_blank" class="btn btn-outline-info">
                            <i class="ti tabler-external-link me-1"></i> Visit School Portal
                        </a>
                    @endif
                    <form action="{{ route('superadmin.schools.destroy', $school) }}"
                          method="POST"
                          onsubmit="return confirm('Permanently delete {{ addslashes($school->school_name) }}?\n\nThis cannot be undone.')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger w-100">
                            <i class="ti tabler-trash me-1"></i> Delete School
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection