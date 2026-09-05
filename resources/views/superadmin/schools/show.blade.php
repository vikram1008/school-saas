@extends('layouts.superadmin.superadmin')

@section('title', $school->school_name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- ── Hero Header ──────────────────────────────────────────────── --}}
  <div class="card mb-5" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); border: none; border-radius: 1rem; overflow: hidden; position: relative;">
    <span style="position:absolute;width:320px;height:320px;border-radius:50%;background:rgba(105,108,255,.08);top:-90px;right:-70px;pointer-events:none;"></span>
    <span style="position:absolute;width:200px;height:200px;border-radius:50%;background:rgba(40,199,111,.06);bottom:-60px;left:80px;pointer-events:none;"></span>

    <div class="card-body py-4 px-4" style="position:relative;z-index:1;">
      <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-4">

        {{-- Back + Logo --}}
        <div class="d-flex align-items-center gap-3">
          <a href="{{ route('superadmin.schools.index') }}" class="btn btn-icon" style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:#fff;border-radius:.6rem;flex-shrink:0;">
            <i class="ti tabler-arrow-left"></i>
          </a>
          <div style="width:72px;height:72px;border-radius:.85rem;background:rgba(255,255,255,.1);border:2px solid rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;padding:4px;">
            <img src="{{ $school->logo_url }}" alt="{{ $school->school_name }}"
                 style="max-width:64px;max-height:64px;object-fit:contain;">
          </div>
        </div>

        {{-- Identity --}}
        <div class="flex-grow-1">
          <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
            <h4 class="fw-bold mb-0" style="color:#fff;">{{ $school->school_name }}</h4>
            @if($school->school_name_hi)
              <span style="color:rgba(255,255,255,.5); font-size:.875rem;">({{ $school->school_name_hi }})</span>
            @endif
          </div>
          <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
            <span class="badge rounded-pill" style="background:rgba({{ $school->is_active ? '40,199,111' : '255,75,75' }},.2);color:{{ $school->is_active ? '#28c76f' : '#ff4b4b' }};border:1px solid rgba({{ $school->is_active ? '40,199,111' : '255,75,75' }},.4);font-size:.75rem;padding:.35em .85em;">
              <i class="ti tabler-circle-filled me-1" style="font-size:7px;"></i>
              {{ $school->is_active ? 'Active' : 'Inactive' }}
            </span>
            @php
              $subStatusColors = ['active' => '105,108,255', 'grace_warning' => '255,171,0', 'grace_readonly' => '255,100,0', 'suspended' => '255,75,75'];
              $subColor = $subStatusColors[$school->subscription_status] ?? '140,152,164';
            @endphp
            <span class="badge rounded-pill" style="background:rgba({{ $subColor }},.2);color:rgb({{ $subColor }});border:1px solid rgba({{ $subColor }},.4);font-size:.75rem;padding:.35em .85em;">
              {{ ucfirst(str_replace('_', ' ', $school->subscription_status)) }}
            </span>
          </div>
          <p class="mb-0" style="color:rgba(255,255,255,.55); font-size:.8rem;">
            <i class="ti tabler-id me-1"></i><code style="color:rgba(255,255,255,.7);">{{ $school->id }}</code>
            &nbsp;·&nbsp;
            <i class="ti tabler-database me-1"></i><code style="color:rgba(255,255,255,.7);">school_{{ $school->id }}</code>
            @if($school->primary_domain)
              &nbsp;·&nbsp;
              <i class="ti tabler-world me-1"></i>
              <a href="http://{{ $school->primary_domain }}" target="_blank" style="color:#818cf8;">{{ $school->primary_domain }}</a>
            @endif
            @if($school->tagline)
              <br><i class="ti tabler-quote me-1 mt-1"></i><em style="color:rgba(255,255,255,.5);">{{ $school->tagline }}</em>
            @endif
          </p>
        </div>

        {{-- Actions --}}
        <div class="d-flex gap-2 flex-shrink-0 flex-wrap">
          <a href="{{ route('superadmin.schools.edit', $school) }}" class="btn btn-primary fw-semibold" style="border-radius:.7rem;">
            <i class="ti tabler-edit me-1"></i> Edit
          </a>
          <a href="{{ route('superadmin.subscriptions.show', $school) }}" class="btn btn-outline-warning fw-semibold" style="border-radius:.7rem;">
            <i class="ti tabler-receipt-rupee me-1"></i> Billing
          </a>
          @if($school->primary_domain)
            <a href="http://{{ $school->primary_domain }}" target="_blank" class="btn btn-outline-info fw-semibold" style="border-radius:.7rem;">
              <i class="ti tabler-external-link me-1"></i> Visit
            </a>
          @endif
          <form action="{{ route('superadmin.schools.destroy', $school) }}" method="POST"
                onsubmit="return confirm('Permanently delete {{ addslashes($school->school_name) }} and ALL its data?\n\nThis cannot be undone.')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger fw-semibold" style="border-radius:.7rem;">
              <i class="ti tabler-trash me-1"></i> Delete
            </button>
          </form>
        </div>

      </div>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible mb-4" style="border-radius:.85rem; border:none; border-left: 4px solid #28c76f; background:rgba(40,199,111,.1);">
      <i class="ti tabler-circle-check me-2 text-success"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- ── Stats Row ──────────────────────────────────────────────── --}}
  <div class="row g-4 mb-5">
    @php
      $statCards = [
        ['label' => 'Students',  'value' => $stats['students'], 'icon' => 'tabler-users',       'color' => 'primary', 'shadow' => 'rgba(105,108,255,.18)'],
        ['label' => 'Staff',     'value' => $stats['staff'],    'icon' => 'tabler-chalkboard',   'color' => 'success', 'shadow' => 'rgba(40,199,111,.18)'],
        ['label' => 'Parents',   'value' => $stats['parents'],  'icon' => 'tabler-users-group',  'color' => 'info',    'shadow' => 'rgba(3,195,236,.18)'],
        ['label' => 'Classes',   'value' => $stats['classes'],  'icon' => 'tabler-school',       'color' => 'warning', 'shadow' => 'rgba(255,171,0,.18)'],
      ];
    @endphp
    @foreach($statCards as $s)
      <div class="col-6 col-md-3">
        <div class="card h-100 text-center" style="border-radius:1rem; border:none; transition:transform .2s ease, box-shadow .2s ease;"
             onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px {{ $s['shadow'] }}'"
             onmouseleave="this.style.transform='';this.style.boxShadow=''">
          <div class="card-body py-4">
            <span class="avatar-initial rounded mb-3 bg-label-{{ $s['color'] }}" style="width:52px;height:52px;display:inline-flex;align-items:center;justify-content:center;border-radius:.85rem!important;font-size:1.4rem;margin:0 auto;">
              <i class="ti {{ $s['icon'] }} text-{{ $s['color'] }}"></i>
            </span>
            <h2 class="fw-bolder mb-0">{{ number_format($s['value']) }}</h2>
            <p class="text-muted small mb-0 mt-1 fw-medium">{{ $s['label'] }}</p>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- ── Main Content ─────────────────────────────────────────── --}}
  <div class="row g-4">

    {{-- ── LEFT COLUMN ──────────────────────────────────────────── --}}
    <div class="col-lg-8">

      {{-- School Details --}}
      <div class="card mb-4" style="border-radius:1rem; border:none;">
        <div class="card-header d-flex align-items-center gap-2 py-3 border-bottom" style="background:transparent;">
          <span class="avatar-initial rounded bg-label-primary" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
            <i class="ti tabler-building text-primary" style="font-size:1rem;"></i>
          </span>
          <div class="flex-grow-1">
            <h5 class="mb-0 fw-bold">School Details</h5>
            <p class="text-muted small mb-0">Identity &amp; contact information</p>
          </div>
          <a href="{{ route('superadmin.schools.edit', $school) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
            <i class="ti tabler-edit me-1"></i>Edit
          </a>
        </div>
        <div class="card-body">
          <div class="row g-4">

            <div class="col-md-6">
              <p class="text-muted small mb-1 text-uppercase fw-semibold" style="font-size:.7rem; letter-spacing:.06em;">School Name</p>
              <p class="fw-semibold mb-0">{{ $school->school_name }}</p>
              @if($school->school_name_hi)
                <p class="text-muted small mb-0">{{ $school->school_name_hi }}</p>
              @endif
            </div>

            <div class="col-md-6">
              <p class="text-muted small mb-1 text-uppercase fw-semibold" style="font-size:.7rem; letter-spacing:.06em;">Tagline</p>
              <p class="fw-semibold mb-0">{{ $school->tagline ?: '—' }}</p>
            </div>

            <div class="col-md-6">
              <p class="text-muted small mb-1 text-uppercase fw-semibold" style="font-size:.7rem; letter-spacing:.06em;">Official Email</p>
              <p class="fw-semibold mb-0">
                <a href="mailto:{{ $school->email }}" class="text-primary">{{ $school->email }}</a>
              </p>
            </div>

            <div class="col-md-6">
              <p class="text-muted small mb-1 text-uppercase fw-semibold" style="font-size:.7rem; letter-spacing:.06em;">Website</p>
              <p class="fw-semibold mb-0">
                @if($school->website)
                  <a href="{{ $school->website }}" target="_blank" class="text-primary">{{ $school->website }}</a>
                @else
                  —
                @endif
              </p>
            </div>

            <div class="col-md-6">
              <p class="text-muted small mb-1 text-uppercase fw-semibold" style="font-size:.7rem; letter-spacing:.06em;">Phone</p>
              <p class="fw-semibold mb-0">
                {{ $school->phone ?: '—' }}
                @if($school->phone_alt)
                  <span class="text-muted small"> · {{ $school->phone_alt }}</span>
                @endif
              </p>
            </div>

            <div class="col-md-6">
              <p class="text-muted small mb-1 text-uppercase fw-semibold" style="font-size:.7rem; letter-spacing:.06em;">Registered On</p>
              <p class="fw-semibold mb-0">{{ $school->created_at->format('d M Y') }}</p>
            </div>

            @if($school->full_address)
              <div class="col-12">
                <div class="p-3 rounded-3" style="background:var(--bs-body-bg); border:1px solid var(--bs-border-color);">
                  <p class="text-muted small mb-1 text-uppercase fw-semibold" style="font-size:.7rem; letter-spacing:.06em;">Address</p>
                  <p class="fw-semibold mb-0">
                    <i class="ti tabler-map-pin me-1 text-muted"></i>{{ $school->full_address }}
                  </p>
                </div>
              </div>
            @endif

          </div>
        </div>
      </div>

      {{-- Academic Details --}}
      @if($school->board_affiliation || $school->school_code || $school->udise_code)
        <div class="card mb-4" style="border-radius:1rem; border:none;">
          <div class="card-header d-flex align-items-center gap-2 py-3 border-bottom" style="background:transparent;">
            <span class="avatar-initial rounded bg-label-warning" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
              <i class="ti tabler-certificate text-warning" style="font-size:1rem;"></i>
            </span>
            <div>
              <h5 class="mb-0 fw-bold">Academic Details</h5>
              <p class="text-muted small mb-0">Affiliation &amp; official codes</p>
            </div>
          </div>
          <div class="card-body">
            <div class="row g-3">
              @if($school->board_affiliation)
                <div class="col-md-4">
                  <p class="text-muted small mb-1 text-uppercase fw-semibold" style="font-size:.7rem; letter-spacing:.06em;">Board / Affiliation</p>
                  <span class="badge bg-label-primary fs-6">{{ $school->board_affiliation }}</span>
                </div>
              @endif
              @if($school->school_code)
                <div class="col-md-4">
                  <p class="text-muted small mb-1 text-uppercase fw-semibold" style="font-size:.7rem; letter-spacing:.06em;">School Code</p>
                  <code class="fs-6">{{ $school->school_code }}</code>
                </div>
              @endif
              @if($school->udise_code)
                <div class="col-md-4">
                  <p class="text-muted small mb-1 text-uppercase fw-semibold" style="font-size:.7rem; letter-spacing:.06em;">UDISE Code</p>
                  <code class="fs-6">{{ $school->udise_code }}</code>
                </div>
              @endif
            </div>
          </div>
        </div>
      @endif

      {{-- Domain & Database --}}
      <div class="card" style="border-radius:1rem; border:none;">
        <div class="card-header d-flex align-items-center gap-2 py-3 border-bottom" style="background:transparent;">
          <span class="avatar-initial rounded bg-label-info" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
            <i class="ti tabler-world text-info" style="font-size:1rem;"></i>
          </span>
          <div>
            <h5 class="mb-0 fw-bold">Domain &amp; Database</h5>
            <p class="text-muted small mb-0">Tenant infrastructure details</p>
          </div>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <p class="text-muted small mb-2 text-uppercase fw-semibold" style="font-size:.7rem; letter-spacing:.06em;">Domains</p>
              @forelse($school->domains as $domain)
                <div class="d-flex align-items-center justify-content-between p-2 rounded-3 mb-2" style="background:var(--bs-primary-bg-subtle); border:1px solid var(--bs-primary-border-subtle);">
                  <div class="d-flex align-items-center gap-2">
                    <i class="ti tabler-link text-primary small"></i>
                    <span class="fw-semibold small">{{ $domain->domain }}</span>
                  </div>
                  <a href="http://{{ $domain->domain }}" target="_blank" class="btn btn-sm btn-icon btn-primary py-1" style="border-radius:.5rem;">
                    <i class="ti tabler-external-link" style="font-size:.8rem;"></i>
                  </a>
                </div>
              @empty
                <p class="text-muted small mb-0">No domains assigned.</p>
              @endforelse
            </div>
            <div class="col-md-6">
              <p class="text-muted small mb-2 text-uppercase fw-semibold" style="font-size:.7rem; letter-spacing:.06em;">Database</p>
              <div class="p-3 rounded-3" style="background:var(--bs-dark-bg-subtle); border:1px solid var(--bs-border-color);">
                <div class="d-flex align-items-center gap-2 mb-1">
                  <i class="ti tabler-database text-muted small"></i>
                  <code class="fw-semibold">school_{{ $school->id }}</code>
                </div>
                <p class="text-muted small mb-0">MySQL · Isolated · Dedicated</p>
              </div>
              <p class="text-muted small mt-2 mb-0">
                <i class="ti tabler-calendar me-1"></i>
                Provisioned: <strong>{{ $school->provisioned_at?->format('d M Y') ?? '—' }}</strong>
              </p>
            </div>
          </div>
        </div>
      </div>

    </div>

    {{-- ── RIGHT COLUMN ─────────────────────────────────────────── --}}
    <div class="col-lg-4 d-flex flex-column gap-4">

      {{-- Billing Card --}}
      <div class="card" style="border-radius:1rem; border:none; background: linear-gradient(145deg, var(--bs-card-bg) 0%, var(--bs-primary-bg-subtle) 100%);">
        <div class="card-header d-flex align-items-center gap-2 py-3 border-bottom" style="background:transparent;">
          <span class="avatar-initial rounded bg-label-warning" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
            <i class="ti tabler-receipt-rupee text-warning" style="font-size:1rem;"></i>
          </span>
          <div class="flex-grow-1">
            <h6 class="mb-0 fw-bold">Billing</h6>
          </div>
          <a href="{{ route('superadmin.subscriptions.show', $school) }}" class="btn btn-sm btn-warning fw-semibold px-3 rounded-pill">
            History
          </a>
        </div>
        <div class="card-body">
          <div class="text-center py-3 mb-3" style="background:var(--bs-body-bg); border-radius:.75rem;">
            <p class="text-muted small mb-1 text-uppercase fw-semibold" style="font-size:.68rem; letter-spacing:.06em;">Rate per Student</p>
            <h2 class="fw-bolder text-primary mb-0">
              ₹{{ $school->per_student_rate }}
              <small class="fs-6 text-muted fw-normal">/student/mo</small>
            </h2>
            <span class="badge bg-label-secondary mt-1">{{ ucfirst(str_replace('_', ' ', $school->billing_cycle)) }}</span>
          </div>

          <p class="text-muted small fw-semibold mb-2 text-uppercase" style="font-size:.68rem; letter-spacing:.06em;">Estimated Monthly Bills</p>
          <table class="table table-sm table-borderless mb-0 small">
            @foreach([100, 300, 500, 1000] as $n)
              <tr class="{{ $n === 1000 ? 'border-top' : '' }}">
                <td class="text-muted ps-0">{{ number_format($n) }} students</td>
                <td class="fw-semibold text-end {{ $n === 1000 ? 'text-primary fw-bolder' : '' }}">
                  ₹{{ number_format($n * $school->per_student_rate) }}
                </td>
              </tr>
            @endforeach
          </table>

          @if($school->latestSubscription)
            <div class="p-2 rounded-3 mt-3" style="background:var(--bs-success-bg-subtle); border:1px solid var(--bs-success-border-subtle);">
              <p class="text-muted small mb-1 fw-semibold">Current Cycle</p>
              <p class="small mb-1 fw-semibold">
                {{ $school->latestSubscription->period_start->format('d M Y') }}
                → {{ $school->latestSubscription->period_end->format('d M Y') }}
              </p>
              <span class="badge bg-label-{{ match($school->latestSubscription->status) {
                'active' => 'success', 'paid' => 'primary',
                'grace_warning', 'grace_readonly' => 'warning',
                default => 'danger' } }}">
                {{ ucfirst(str_replace('_', ' ', $school->latestSubscription->status)) }}
              </span>
            </div>
          @endif
        </div>
      </div>

      {{-- Quick Actions --}}
      <div class="card" style="border-radius:1rem; border:none;">
        <div class="card-header d-flex align-items-center gap-2 py-3 border-bottom" style="background:transparent;">
          <span class="avatar-initial rounded bg-label-info" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
            <i class="ti tabler-bolt text-info" style="font-size:1rem;"></i>
          </span>
          <h6 class="mb-0 fw-bold">Quick Actions</h6>
        </div>
        <div class="card-body d-flex flex-column gap-2">

          <a href="{{ route('superadmin.schools.edit', $school) }}" class="d-flex align-items-center gap-3 text-decoration-none p-2 rounded-3" style="border:1px solid var(--bs-border-color); transition:all .2s ease;" onmouseenter="this.style.borderColor='#696cff';this.style.background='var(--bs-primary-bg-subtle)'" onmouseleave="this.style.borderColor='var(--bs-border-color)';this.style.background=''">
            <span class="avatar-initial rounded bg-label-primary" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:.55rem!important;font-size:1rem;flex-shrink:0;">
              <i class="ti tabler-edit text-primary"></i>
            </span>
            <div class="flex-grow-1">
              <div class="fw-semibold text-body" style="font-size:.875rem;">Edit School</div>
              <div class="text-muted" style="font-size:.75rem;">Update details &amp; settings</div>
            </div>
            <i class="ti tabler-chevron-right text-muted"></i>
          </a>

          <a href="{{ route('superadmin.subscriptions.show', $school) }}" class="d-flex align-items-center gap-3 text-decoration-none p-2 rounded-3" style="border:1px solid var(--bs-border-color); transition:all .2s ease;" onmouseenter="this.style.borderColor='#ffab00';this.style.background='var(--bs-warning-bg-subtle)'" onmouseleave="this.style.borderColor='var(--bs-border-color)';this.style.background=''">
            <span class="avatar-initial rounded bg-label-warning" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:.55rem!important;font-size:1rem;flex-shrink:0;">
              <i class="ti tabler-receipt-rupee text-warning"></i>
            </span>
            <div class="flex-grow-1">
              <div class="fw-semibold text-body" style="font-size:.875rem;">Manage Billing</div>
              <div class="text-muted" style="font-size:.75rem;">View &amp; manage subscriptions</div>
            </div>
            <i class="ti tabler-chevron-right text-muted"></i>
          </a>

          @if($school->primary_domain)
            <a href="http://{{ $school->primary_domain }}" target="_blank" class="d-flex align-items-center gap-3 text-decoration-none p-2 rounded-3" style="border:1px solid var(--bs-border-color); transition:all .2s ease;" onmouseenter="this.style.borderColor='#03c9ec';this.style.background='var(--bs-info-bg-subtle)'" onmouseleave="this.style.borderColor='var(--bs-border-color)';this.style.background=''">
              <span class="avatar-initial rounded bg-label-info" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:.55rem!important;font-size:1rem;flex-shrink:0;">
                <i class="ti tabler-external-link text-info"></i>
              </span>
              <div class="flex-grow-1">
                <div class="fw-semibold text-body" style="font-size:.875rem;">Visit School Portal</div>
                <div class="text-muted" style="font-size:.75rem;">{{ $school->primary_domain }}</div>
              </div>
              <i class="ti tabler-arrow-up-right text-muted"></i>
            </a>
          @endif

          <form action="{{ route('superadmin.schools.destroy', $school) }}" method="POST"
                onsubmit="return confirm('Permanently delete {{ addslashes($school->school_name) }}?\n\nThis cannot be undone.')">
            @csrf @method('DELETE')
            <button class="d-flex align-items-center gap-3 text-decoration-none p-2 rounded-3 w-100 text-start" style="border:1px solid var(--bs-border-color); background:transparent; transition:all .2s ease; cursor:pointer;" onmouseenter="this.style.borderColor='#ff4b4b';this.style.background='var(--bs-danger-bg-subtle)'" onmouseleave="this.style.borderColor='var(--bs-border-color)';this.style.background=''">
              <span class="avatar-initial rounded bg-label-danger" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:.55rem!important;font-size:1rem;flex-shrink:0;">
                <i class="ti tabler-trash text-danger"></i>
              </span>
              <div class="flex-grow-1">
                <div class="fw-semibold text-danger" style="font-size:.875rem;">Delete School</div>
                <div class="text-muted" style="font-size:.75rem;">Permanently remove tenant</div>
              </div>
              <i class="ti tabler-chevron-right text-muted"></i>
            </button>
          </form>

        </div>
      </div>

    </div>
  </div>

</div>
@endsection