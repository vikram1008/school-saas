@extends('layouts.superadmin.superadmin')

@section('title', 'Global Settings')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Global Settings</h4>
            <p class="text-muted mb-0">
                Configure platform-wide behaviour for all schools.
            </p>
        </div>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('superadmin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-8">

                {{-- Billing Thresholds --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="icon-base ti tabler-clock-exclamation me-2 text-warning fs-5"></i>
                        <div>
                            <h5 class="mb-0">Grace Period Thresholds</h5>
                            <p class="text-muted small mb-0">
                                Controls escalation timing after a missed payment.
                            </p>
                        </div>
                    </div>
                    <div class="card-body">

                        {{-- Visual Timeline --}}
                        <div class="d-flex align-items-center mb-4 p-3 bg-light rounded">
                            <div class="text-center flex-fill">
                                <div class="badge bg-success mb-1 px-3">Day 0</div>
                                <div class="small text-muted">Payment Due</div>
                            </div>
                            <div class="flex-fill border-top border-2 border-warning position-relative">
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <span class="badge bg-warning text-dark">Warning</span>
                                </div>
                            </div>
                            <div class="text-center flex-fill">
                                <div class="badge bg-warning text-dark mb-1 px-3">
                                    Day {{ $settings->get('grace_warning_days')?->value ?? 7 }}
                                </div>
                                <div class="small text-muted">Read-Only</div>
                            </div>
                            <div class="flex-fill border-top border-2 border-danger position-relative">
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <span class="badge bg-danger">Read-Only</span>
                                </div>
                            </div>
                            <div class="text-center flex-fill">
                                <div class="badge bg-danger mb-1 px-3">
                                    Day {{ $settings->get('grace_readonly_days')?->value ?? 30 }}
                                </div>
                                <div class="small text-muted">Suspended</div>
                            </div>
                            <div class="flex-fill border-top border-2 border-dark position-relative">
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <span class="badge bg-dark">Suspend</span>
                                </div>
                            </div>
                            <div class="text-center flex-fill">
                                <div class="badge bg-dark mb-1 px-3">
                                    Day {{ $settings->get('suspension_days')?->value ?? 31 }}+
                                </div>
                                <div class="small text-muted">Locked Out</div>
                            </div>
                        </div>

                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    <span class="badge bg-warning text-dark me-1">Phase 1</span>
                                    Warning Period
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number"
                                           name="grace_warning_days"
                                           class="form-control @error('grace_warning_days') is-invalid @enderror"
                                           value="{{ old('grace_warning_days', $settings->get('grace_warning_days')?->value ?? 7) }}"
                                           min="1" max="30">
                                    <span class="input-group-text">days</span>
                                    @error('grace_warning_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">
                                    Red banner shown. Full access retained.
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    <span class="badge bg-danger me-1">Phase 2</span>
                                    Read-Only Period
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number"
                                           name="grace_readonly_days"
                                           class="form-control @error('grace_readonly_days') is-invalid @enderror"
                                           value="{{ old('grace_readonly_days', $settings->get('grace_readonly_days')?->value ?? 30) }}"
                                           min="1" max="60">
                                    <span class="input-group-text">days</span>
                                    @error('grace_readonly_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">
                                    POST/PUT/DELETE blocked. View only.
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    <span class="badge bg-dark me-1">Phase 3</span>
                                    Suspension Threshold
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number"
                                           name="suspension_days"
                                           class="form-control @error('suspension_days') is-invalid @enderror"
                                           value="{{ old('suspension_days', $settings->get('suspension_days')?->value ?? 31) }}"
                                           min="1" max="90">
                                    <span class="input-group-text">days</span>
                                    @error('suspension_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">
                                    Full lockout. Login blocked for all users.
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Billing Settings --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="icon-base ti tabler-currency-rupee me-2 text-success fs-5"></i>
                        <div>
                            <h5 class="mb-0">Billing Settings</h5>
                            <p class="text-muted small mb-0">
                                Default billing configuration for new schools.
                            </p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Default Billing Cycle
                                <span class="text-danger">*</span>
                            </label>
                            <select name="default_billing_cycle"
                                    class="form-select @error('default_billing_cycle') is-invalid @enderror">
                                @foreach([
                                    'monthly'     => 'Monthly',
                                    'quarterly'   => 'Quarterly (Every 3 months)',
                                    'half_yearly' => 'Half-Yearly (Every 6 months)',
                                    'yearly'      => 'Yearly (Annual)',
                                ] as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ old('default_billing_cycle', $settings->get('default_billing_cycle')?->value) === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('default_billing_cycle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Platform Settings --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="icon-base ti tabler-settings me-2 text-primary fs-5"></i>
                        <div>
                            <h5 class="mb-0">Platform Settings</h5>
                            <p class="text-muted small mb-0">
                                General platform identity and contact info.
                            </p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Platform Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="saas_name"
                                       class="form-control @error('saas_name') is-invalid @enderror"
                                       value="{{ old('saas_name', $settings->get('saas_name')?->value) }}"
                                       placeholder="School SaaS">
                                @error('saas_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Support Email <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                       name="support_email"
                                       class="form-control @error('support_email') is-invalid @enderror"
                                       value="{{ old('support_email', $settings->get('support_email')?->value) }}"
                                       placeholder="support@saas.com">
                                @error('support_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Shown to suspended schools on the access denied page.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base ti tabler-device-floppy me-1"></i>
                        Save All Settings
                    </button>
                    <a href="{{ route('superadmin.dashboard') }}"
                       class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">

                {{-- Current Values --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="icon-base ti tabler-info-circle me-1 text-info"></i>
                            Current Active Values
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted small">Warning after</td>
                                <td class="fw-semibold text-end">
                                    {{ $settings->get('grace_warning_days')?->value ?? 7 }} days
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Read-Only after</td>
                                <td class="fw-semibold text-end">
                                    {{ $settings->get('grace_readonly_days')?->value ?? 30 }} days
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Suspend after</td>
                                <td class="fw-semibold text-end">
                                    {{ $settings->get('suspension_days')?->value ?? 31 }} days
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Default cycle</td>
                                <td class="fw-semibold text-end">
                                    {{ ucfirst($settings->get('default_billing_cycle')?->value ?? 'monthly') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Platform</td>
                                <td class="fw-semibold text-end">
                                    {{ $settings->get('saas_name')?->value ?? '—' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Support</td>
                                <td class="fw-semibold text-end small">
                                    {{ $settings->get('support_email')?->value ?? '—' }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Warning Card --}}
                <div class="card border-warning">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2">
                            <i class="icon-base ti tabler-alert-triangle me-1 text-warning"></i>
                            Important
                        </h6>
                        <ul class="ps-3 mb-0 text-muted small">
                            <li class="mb-2">
                                Changes apply to <strong>all schools</strong> from the next daily check.
                            </li>
                            <li class="mb-2">
                                Warning days must always be <strong>less than</strong> Read-Only days.
                            </li>
                            <li class="mb-2">
                                Read-Only days must always be <strong>less than</strong> Suspension days.
                            </li>
                            <li>
                                Currently active subscriptions are not retroactively affected.
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection