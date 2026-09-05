@extends('layouts.superadmin.superadmin')

@section('title', 'Global Settings')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- ── Hero Banner ──────────────────────────────────────────────── --}}
  <div class="card mb-5" style="background: linear-gradient(135deg, #0f1f3d 0%, #1a2e4a 50%, #162040 100%); border: none; border-radius: 1rem; overflow: hidden; position: relative;">
    <span style="position:absolute;width:260px;height:260px;border-radius:50%;background:rgba(3,195,236,.07);top:-70px;right:-50px;pointer-events:none;"></span>
    <span style="position:absolute;width:160px;height:160px;border-radius:50%;background:rgba(105,108,255,.06);bottom:-40px;left:60px;pointer-events:none;"></span>
    <div class="card-body py-4 px-4" style="position:relative;z-index:1;">
      <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
        <div>
          <span style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(3,195,236,.2);border:1px solid rgba(3,195,236,.35);color:#5ee7f8;font-size:.72rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase;padding:.25rem .85rem;border-radius:50rem;margin-bottom:.85rem;">
            <i class="ti tabler-settings" style="font-size:.85rem;"></i>
            Platform Configuration
          </span>
          <h4 class="mb-1" style="color:#fff;font-weight:700;font-size:1.45rem;">Global Settings</h4>
          <p class="mb-0" style="color:rgba(255,255,255,.6);">Configure platform-wide behaviour, billing thresholds, and identity for all schools.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
          <div style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.13);border-radius:.75rem;padding:.75rem 1.25rem;text-align:center;">
            <div style="color:rgba(255,255,255,.5);font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem;">Platform</div>
            <div style="color:#5ee7f8;font-weight:700;font-size:.95rem;">{{ $settings->get('saas_name')?->value ?? 'School SaaS' }}</div>
          </div>
          <div style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.13);border-radius:.75rem;padding:.75rem 1.25rem;text-align:center;">
            <div style="color:rgba(255,255,255,.5);font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem;">Default Cycle</div>
            <div style="color:#a5b4fc;font-weight:700;font-size:.95rem;">{{ ucfirst($settings->get('default_billing_cycle')?->value ?? 'Monthly') }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Alerts ──────────────────────────────────────────────────── --}}
  @if(session('success'))
    <div class="alert alert-dismissible mb-4" role="alert" style="border-radius:.85rem;border:none;border-left:4px solid #28c76f;background:rgba(40,199,111,.1);">
      <div class="d-flex align-items-center gap-2">
        <i class="ti tabler-circle-check text-success fs-5"></i>
        <span class="fw-semibold">{{ session('success') }}</span>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-dismissible mb-4" role="alert" style="border-radius:.85rem;border:none;border-left:4px solid #ff4b4b;background:rgba(255,75,75,.08);">
      <div class="d-flex align-items-center gap-2 mb-1">
        <i class="ti tabler-alert-triangle text-danger fs-5"></i>
        <span class="fw-semibold text-danger">Please fix the following errors:</span>
      </div>
      <ul class="mb-0 ps-4 small mt-1">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <form action="{{ route('superadmin.settings.update') }}" method="POST" id="settingsForm">
    @csrf
    @method('PUT')

    <div class="row g-4">

      {{-- ── LEFT / MAIN COLUMN ─────────────────────────────────── --}}
      <div class="col-lg-8 d-flex flex-column gap-4">

        {{-- ── Grace Period Thresholds ──────────────────────── --}}
        <div class="card" style="border-radius:1rem;border:none;">
          <div class="card-header d-flex align-items-center gap-3 py-3 border-bottom" style="background:transparent;">
            <span class="avatar-initial rounded bg-label-warning" style="width:42px;height:42px;display:flex;align-items:center;justify-content:center;border-radius:.7rem!important;font-size:1.2rem;flex-shrink:0;">
              <i class="ti tabler-clock-exclamation text-warning"></i>
            </span>
            <div>
              <h5 class="mb-0 fw-bold">Grace Period Thresholds</h5>
              <p class="text-muted small mb-0">Controls escalation timing after a missed payment.</p>
            </div>
          </div>
          <div class="card-body">

            {{-- Visual Timeline --}}
            <div class="mb-4 p-3 rounded-3" style="background:var(--bs-body-bg);border:1px solid var(--bs-border-color);">
              <p class="text-muted small fw-semibold mb-3 text-uppercase" style="font-size:.68rem;letter-spacing:.07em;">Escalation Timeline</p>
              <div class="d-flex align-items-stretch gap-0 position-relative" style="min-height:64px;">

                {{-- Phase: Active --}}
                <div class="flex-fill d-flex flex-column align-items-center justify-content-between" style="min-width:0;">
                  <div class="badge bg-success mb-2 px-2" style="font-size:.68rem;border-radius:.4rem;">Day 0</div>
                  <div class="small text-muted text-center" style="font-size:.72rem;line-height:1.3;">Payment<br>Due</div>
                </div>

                {{-- Arrow + Label --}}
                <div class="d-flex flex-column align-items-center justify-content-center flex-fill" style="min-width:60px;">
                  <div class="badge bg-warning text-dark mb-2 px-2" style="font-size:.65rem;border-radius:.4rem;">Warning</div>
                  <div style="width:100%;height:3px;background:linear-gradient(90deg,#28c76f,#ffab00);border-radius:50rem;"></div>
                </div>

                {{-- Phase: Warning --}}
                <div class="flex-fill d-flex flex-column align-items-center justify-content-between" style="min-width:0;">
                  <div class="badge bg-warning text-dark mb-2 px-2" style="font-size:.68rem;border-radius:.4rem;">Day {{ $settings->get('grace_warning_days')?->value ?? 7 }}</div>
                  <div class="small text-muted text-center" style="font-size:.72rem;line-height:1.3;">Read-<br>Only</div>
                </div>

                {{-- Arrow + Label --}}
                <div class="d-flex flex-column align-items-center justify-content-center flex-fill" style="min-width:60px;">
                  <div class="badge bg-danger mb-2 px-2" style="font-size:.65rem;border-radius:.4rem;">Read-Only</div>
                  <div style="width:100%;height:3px;background:linear-gradient(90deg,#ffab00,#ff4b4b);border-radius:50rem;"></div>
                </div>

                {{-- Phase: Read-Only --}}
                <div class="flex-fill d-flex flex-column align-items-center justify-content-between" style="min-width:0;">
                  <div class="badge bg-danger mb-2 px-2" style="font-size:.68rem;border-radius:.4rem;">Day {{ $settings->get('grace_readonly_days')?->value ?? 30 }}</div>
                  <div class="small text-muted text-center" style="font-size:.72rem;line-height:1.3;">Sus-<br>pended</div>
                </div>

                {{-- Arrow + Label --}}
                <div class="d-flex flex-column align-items-center justify-content-center flex-fill" style="min-width:60px;">
                  <div class="badge bg-dark mb-2 px-2" style="font-size:.65rem;border-radius:.4rem;">Suspend</div>
                  <div style="width:100%;height:3px;background:linear-gradient(90deg,#ff4b4b,#343a40);border-radius:50rem;"></div>
                </div>

                {{-- Phase: Suspended --}}
                <div class="flex-fill d-flex flex-column align-items-center justify-content-between" style="min-width:0;">
                  <div class="badge bg-dark mb-2 px-2" style="font-size:.68rem;border-radius:.4rem;">Day {{ $settings->get('suspension_days')?->value ?? 31 }}+</div>
                  <div class="small text-muted text-center" style="font-size:.72rem;line-height:1.3;">Locked<br>Out</div>
                </div>

              </div>
            </div>

            {{-- Input Fields --}}
            <div class="row g-3">

              {{-- Phase 1 --}}
              <div class="col-md-4">
                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                  <span class="badge bg-warning text-dark rounded-pill" style="font-size:.65rem;">Phase 1</span>
                  Warning Period
                  <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text" style="background:var(--bs-warning-bg-subtle);border-color:var(--bs-warning-border-subtle);">
                    <i class="ti tabler-clock text-warning" style="font-size:.9rem;"></i>
                  </span>
                  <input type="number"
                         name="grace_warning_days"
                         id="graceWarningDays"
                         class="form-control @error('grace_warning_days') is-invalid @enderror"
                         value="{{ old('grace_warning_days', $settings->get('grace_warning_days')?->value ?? 7) }}"
                         min="1" max="30"
                         oninput="updateTimeline()">
                  <span class="input-group-text">days</span>
                  @error('grace_warning_days')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="form-text d-flex align-items-center gap-1 mt-1">
                  <i class="ti tabler-info-circle text-warning" style="font-size:.8rem;"></i>
                  Red banner shown. Full access retained.
                </div>
              </div>

              {{-- Phase 2 --}}
              <div class="col-md-4">
                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                  <span class="badge bg-danger rounded-pill" style="font-size:.65rem;">Phase 2</span>
                  Read-Only Period
                  <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text" style="background:var(--bs-danger-bg-subtle);border-color:var(--bs-danger-border-subtle);">
                    <i class="ti tabler-eye text-danger" style="font-size:.9rem;"></i>
                  </span>
                  <input type="number"
                         name="grace_readonly_days"
                         id="graceReadonlyDays"
                         class="form-control @error('grace_readonly_days') is-invalid @enderror"
                         value="{{ old('grace_readonly_days', $settings->get('grace_readonly_days')?->value ?? 30) }}"
                         min="1" max="60"
                         oninput="updateTimeline()">
                  <span class="input-group-text">days</span>
                  @error('grace_readonly_days')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="form-text d-flex align-items-center gap-1 mt-1">
                  <i class="ti tabler-info-circle text-danger" style="font-size:.8rem;"></i>
                  POST/PUT/DELETE blocked. View only.
                </div>
              </div>

              {{-- Phase 3 --}}
              <div class="col-md-4">
                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                  <span class="badge bg-dark rounded-pill" style="font-size:.65rem;">Phase 3</span>
                  Suspension Threshold
                  <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text" style="background:var(--bs-secondary-bg);border-color:var(--bs-border-color);">
                    <i class="ti tabler-lock text-secondary" style="font-size:.9rem;"></i>
                  </span>
                  <input type="number"
                         name="suspension_days"
                         id="suspensionDays"
                         class="form-control @error('suspension_days') is-invalid @enderror"
                         value="{{ old('suspension_days', $settings->get('suspension_days')?->value ?? 31) }}"
                         min="1" max="90"
                         oninput="updateTimeline()">
                  <span class="input-group-text">days</span>
                  @error('suspension_days')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="form-text d-flex align-items-center gap-1 mt-1">
                  <i class="ti tabler-info-circle text-secondary" style="font-size:.8rem;"></i>
                  Full lockout. Login blocked for all users.
                </div>
              </div>

            </div>
          </div>
        </div>

        {{-- ── Billing Settings ─────────────────────────────── --}}
        <div class="card" style="border-radius:1rem;border:none;">
          <div class="card-header d-flex align-items-center gap-3 py-3 border-bottom" style="background:transparent;">
            <span class="avatar-initial rounded bg-label-success" style="width:42px;height:42px;display:flex;align-items:center;justify-content:center;border-radius:.7rem!important;font-size:1.2rem;flex-shrink:0;">
              <i class="ti tabler-currency-rupee text-success"></i>
            </span>
            <div>
              <h5 class="mb-0 fw-bold">Billing Settings</h5>
              <p class="text-muted small mb-0">Default billing configuration for new schools.</p>
            </div>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  Default Billing Cycle <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text" style="background:var(--bs-success-bg-subtle);border-color:var(--bs-success-border-subtle);">
                    <i class="ti tabler-calendar-repeat text-success" style="font-size:.9rem;"></i>
                  </span>
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
                <div class="form-text">Applied to new schools if not overridden at registration.</div>
              </div>
            </div>
          </div>
        </div>

        {{-- ── Platform Identity ────────────────────────────── --}}
        <div class="card" style="border-radius:1rem;border:none;">
          <div class="card-header d-flex align-items-center gap-3 py-3 border-bottom" style="background:transparent;">
            <span class="avatar-initial rounded bg-label-primary" style="width:42px;height:42px;display:flex;align-items:center;justify-content:center;border-radius:.7rem!important;font-size:1.2rem;flex-shrink:0;">
              <i class="ti tabler-building-skyscraper text-primary"></i>
            </span>
            <div>
              <h5 class="mb-0 fw-bold">Platform Identity</h5>
              <p class="text-muted small mb-0">General platform name and support contact info.</p>
            </div>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  Platform Name <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text" style="background:var(--bs-primary-bg-subtle);border-color:var(--bs-primary-border-subtle);">
                    <i class="ti tabler-brand-appgallery text-primary" style="font-size:.9rem;"></i>
                  </span>
                  <input type="text"
                         name="saas_name"
                         class="form-control @error('saas_name') is-invalid @enderror"
                         value="{{ old('saas_name', $settings->get('saas_name')?->value) }}"
                         placeholder="School SaaS">
                  @error('saas_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="form-text">Displayed in emails and the login page.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  Support Email <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text" style="background:var(--bs-primary-bg-subtle);border-color:var(--bs-primary-border-subtle);">
                    <i class="ti tabler-mail text-primary" style="font-size:.9rem;"></i>
                  </span>
                  <input type="email"
                         name="support_email"
                         class="form-control @error('support_email') is-invalid @enderror"
                         value="{{ old('support_email', $settings->get('support_email')?->value) }}"
                         placeholder="support@saas.com">
                  @error('support_email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="form-text">Shown to suspended schools on the access-denied page.</div>
              </div>
            </div>
          </div>
        </div>

        {{-- ── Save Bar ─────────────────────────────────────── --}}
        <div class="card" style="border-radius:1rem;border:none;background:linear-gradient(135deg,var(--bs-primary-bg-subtle),var(--bs-card-bg));">
          <div class="card-body d-flex align-items-center justify-content-between gap-3 py-3">
            <div class="d-flex align-items-center gap-2">
              <i class="ti tabler-device-floppy text-primary fs-5"></i>
              <span class="fw-semibold text-body">Ready to save?</span>
              <span class="text-muted small">All changes apply globally to all schools from the next daily check.</span>
            </div>
            <div class="d-flex gap-2 flex-shrink-0">
              <a href="{{ route('superadmin.dashboard') }}" class="btn btn-outline-secondary fw-semibold" style="border-radius:.7rem;">
                Cancel
              </a>
              <button type="submit" class="btn btn-primary fw-semibold px-4" style="border-radius:.7rem;box-shadow:0 4px 15px rgba(105,108,255,.35);">
                <i class="ti tabler-device-floppy me-1"></i>
                Save All Settings
              </button>
            </div>
          </div>
        </div>

      </div>

      {{-- ── RIGHT SIDEBAR COLUMN ────────────────────────────────── --}}
      <div class="col-lg-4 d-flex flex-column gap-4">

        {{-- Current Active Values --}}
        <div class="card" style="border-radius:1rem;border:none;">
          <div class="card-header d-flex align-items-center gap-2 py-3 border-bottom" style="background:transparent;">
            <span class="avatar-initial rounded bg-label-info" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
              <i class="ti tabler-database text-info" style="font-size:1rem;"></i>
            </span>
            <div>
              <h6 class="mb-0 fw-bold">Active Configuration</h6>
              <p class="text-muted small mb-0">Current live values</p>
            </div>
          </div>
          <div class="card-body p-0">
            @php
              $currentValues = [
                ['icon' => 'tabler-clock text-warning',  'label' => 'Warning after',    'value' => ($settings->get('grace_warning_days')?->value ?? 7).' days',      'color' => 'warning'],
                ['icon' => 'tabler-eye text-danger',     'label' => 'Read-Only after',   'value' => ($settings->get('grace_readonly_days')?->value ?? 30).' days',    'color' => 'danger'],
                ['icon' => 'tabler-lock text-secondary', 'label' => 'Suspend after',     'value' => ($settings->get('suspension_days')?->value ?? 31).' days',        'color' => 'secondary'],
                ['icon' => 'tabler-calendar text-success','label' => 'Default cycle',    'value' => ucfirst($settings->get('default_billing_cycle')?->value ?? 'monthly'), 'color' => 'success'],
                ['icon' => 'tabler-building text-primary','label' => 'Platform name',    'value' => $settings->get('saas_name')?->value ?? '—',                      'color' => 'primary'],
                ['icon' => 'tabler-mail text-primary',   'label' => 'Support email',     'value' => $settings->get('support_email')?->value ?? '—',                  'color' => 'primary'],
              ];
            @endphp
            @foreach($currentValues as $i => $row)
              <div class="d-flex align-items-center justify-content-between px-3 py-2 {{ $i > 0 ? 'border-top' : '' }}" style="{{ $i % 2 === 0 ? '' : 'background:var(--bs-body-bg);' }}">
                <div class="d-flex align-items-center gap-2">
                  <i class="ti {{ $row['icon'] }}" style="font-size:.9rem;width:16px;"></i>
                  <span class="small text-muted">{{ $row['label'] }}</span>
                </div>
                <span class="badge bg-label-{{ $row['color'] }} fw-semibold" style="font-size:.72rem;max-width:120px;text-align:right;white-space:normal;word-break:break-all;">
                  {{ $row['value'] }}
                </span>
              </div>
            @endforeach
          </div>
        </div>

        {{-- Rule Validation Guide --}}
        <div class="card" style="border-radius:1rem;border:none;">
          <div class="card-header d-flex align-items-center gap-2 py-3 border-bottom" style="background:transparent;">
            <span class="avatar-initial rounded bg-label-warning" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
              <i class="ti tabler-alert-triangle text-warning" style="font-size:1rem;"></i>
            </span>
            <div>
              <h6 class="mb-0 fw-bold">Threshold Rules</h6>
              <p class="text-muted small mb-0">Must be satisfied to save</p>
            </div>
          </div>
          <div class="card-body d-flex flex-column gap-2">

            <div class="d-flex align-items-start gap-2 p-2 rounded-3" style="background:var(--bs-warning-bg-subtle);border:1px solid var(--bs-warning-border-subtle);">
              <i class="ti tabler-arrow-narrow-right text-warning mt-1" style="font-size:1rem;flex-shrink:0;"></i>
              <p class="small mb-0"><strong>Warning days</strong> must be <em>less than</em> Read-Only days</p>
            </div>
            <div class="d-flex align-items-start gap-2 p-2 rounded-3" style="background:var(--bs-danger-bg-subtle);border:1px solid var(--bs-danger-border-subtle);">
              <i class="ti tabler-arrow-narrow-right text-danger mt-1" style="font-size:1rem;flex-shrink:0;"></i>
              <p class="small mb-0"><strong>Read-Only days</strong> must be <em>less than</em> Suspension days</p>
            </div>
            <div class="d-flex align-items-start gap-2 p-2 rounded-3" style="background:var(--bs-info-bg-subtle);border:1px solid var(--bs-info-border-subtle);">
              <i class="ti tabler-info-circle text-info mt-1" style="font-size:1rem;flex-shrink:0;"></i>
              <p class="small mb-0">Changes apply from the <strong>next daily check</strong> — currently active subscriptions are not retroactively affected.</p>
            </div>

          </div>
        </div>

        {{-- Quick Navigation --}}
        <div class="card" style="border-radius:1rem;border:none;">
          <div class="card-header d-flex align-items-center gap-2 py-3 border-bottom" style="background:transparent;">
            <span class="avatar-initial rounded bg-label-secondary" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
              <i class="ti tabler-layout-grid text-secondary" style="font-size:1rem;"></i>
            </span>
            <h6 class="mb-0 fw-bold">Quick Navigation</h6>
          </div>
          <div class="card-body d-flex flex-column gap-2">

            <a href="{{ route('superadmin.dashboard') }}" class="d-flex align-items-center gap-3 text-decoration-none p-2 rounded-3" style="border:1px solid var(--bs-border-color);transition:all .2s ease;" onmouseenter="this.style.borderColor='#696cff';this.style.background='var(--bs-primary-bg-subtle)'" onmouseleave="this.style.borderColor='var(--bs-border-color)';this.style.background=''">
              <span class="avatar-initial rounded bg-label-primary" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:.5rem!important;font-size:.9rem;flex-shrink:0;">
                <i class="ti tabler-smart-home text-primary" style="font-size:.9rem;"></i>
              </span>
              <div class="flex-grow-1">
                <div class="fw-semibold text-body" style="font-size:.8rem;">Dashboard</div>
                <div class="text-muted" style="font-size:.7rem;">Platform overview</div>
              </div>
              <i class="ti tabler-chevron-right text-muted small"></i>
            </a>

            <a href="{{ route('superadmin.subscriptions.index') }}" class="d-flex align-items-center gap-3 text-decoration-none p-2 rounded-3" style="border:1px solid var(--bs-border-color);transition:all .2s ease;" onmouseenter="this.style.borderColor='#ffab00';this.style.background='var(--bs-warning-bg-subtle)'" onmouseleave="this.style.borderColor='var(--bs-border-color)';this.style.background=''">
              <span class="avatar-initial rounded bg-label-warning" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:.5rem!important;font-size:.9rem;flex-shrink:0;">
                <i class="ti tabler-receipt text-warning" style="font-size:.9rem;"></i>
              </span>
              <div class="flex-grow-1">
                <div class="fw-semibold text-body" style="font-size:.8rem;">Subscriptions</div>
                <div class="text-muted" style="font-size:.7rem;">Billing &amp; status</div>
              </div>
              <i class="ti tabler-chevron-right text-muted small"></i>
            </a>

            <a href="{{ route('superadmin.schools.index') }}" class="d-flex align-items-center gap-3 text-decoration-none p-2 rounded-3" style="border:1px solid var(--bs-border-color);transition:all .2s ease;" onmouseenter="this.style.borderColor='#28c76f';this.style.background='var(--bs-success-bg-subtle)'" onmouseleave="this.style.borderColor='var(--bs-border-color)';this.style.background=''">
              <span class="avatar-initial rounded bg-label-success" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:.5rem!important;font-size:.9rem;flex-shrink:0;">
                <i class="ti tabler-building text-success" style="font-size:.9rem;"></i>
              </span>
              <div class="flex-grow-1">
                <div class="fw-semibold text-body" style="font-size:.8rem;">Schools</div>
                <div class="text-muted" style="font-size:.7rem;">Manage tenants</div>
              </div>
              <i class="ti tabler-chevron-right text-muted small"></i>
            </a>

          </div>
        </div>

      </div>

    </div>
  </form>

</div>

@push('scripts')
<script>
  // Dynamically update the timeline day badges as the user types
  function updateTimeline() {
    const w  = parseInt(document.getElementById('graceWarningDays').value)  || 0;
    const ro = parseInt(document.getElementById('graceReadonlyDays').value) || 0;
    const s  = parseInt(document.getElementById('suspensionDays').value)    || 0;

    const badges = document.querySelectorAll('[data-timeline-badge]');
    badges.forEach(b => {
      const type = b.dataset.timelineBadge;
      if (type === 'warning')  { b.textContent = 'Day ' + w; }
      if (type === 'readonly') { b.textContent = 'Day ' + ro; }
      if (type === 'suspend')  { b.textContent = 'Day ' + s + '+'; }
    });
  }

  // Client-side guard before form submit
  document.getElementById('settingsForm').addEventListener('submit', function (e) {
    const w  = parseInt(document.getElementById('graceWarningDays').value);
    const ro = parseInt(document.getElementById('graceReadonlyDays').value);
    const s  = parseInt(document.getElementById('suspensionDays').value);

    if (w >= ro) {
      e.preventDefault();
      document.getElementById('graceWarningDays').classList.add('is-invalid');
      document.getElementById('graceWarningDays').nextElementSibling.nextElementSibling.textContent = 'Warning days must be less than Read-Only days.';
      document.getElementById('graceWarningDays').scrollIntoView({ behavior: 'smooth', block: 'center' });
      return;
    }
    if (ro >= s) {
      e.preventDefault();
      document.getElementById('graceReadonlyDays').classList.add('is-invalid');
      document.getElementById('graceReadonlyDays').nextElementSibling.nextElementSibling.textContent = 'Read-Only days must be less than Suspension days.';
      document.getElementById('graceReadonlyDays').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });
</script>
@endpush
@endsection