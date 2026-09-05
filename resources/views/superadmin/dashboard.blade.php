@extends('layouts.superadmin.superadmin')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- ── Hero Banner ─────────────────────────────────────── --}}
  <div class="card mb-5" style="background: linear-gradient(135deg, #696cff 0%, #9155fd 55%, #a855f7 100%); border: none; border-radius: 1rem; overflow: hidden; position: relative;">
    {{-- Decorative circles --}}
    <span style="position:absolute; width:260px; height:260px; border-radius:50%; background:rgba(255,255,255,.07); top:-60px; right:-60px; pointer-events:none;"></span>
    <span style="position:absolute; width:160px; height:160px; border-radius:50%; background:rgba(255,255,255,.05); bottom:-40px; left:40px; pointer-events:none;"></span>

    <div class="card-body py-4 px-4" style="position:relative; z-index:1;">
      <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
        <div>
          <span style="display:inline-flex; align-items:center; gap:.4rem; background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.3); color:#fff; font-size:.72rem; font-weight:600; letter-spacing:.05em; text-transform:uppercase; padding:.25rem .85rem; border-radius:50rem; margin-bottom:.85rem;">
            <i class="ti tabler-shield-check" style="font-size:.85rem;"></i>
            Super Admin
          </span>
          <h4 class="mb-1" style="color:#fff; font-weight:700; font-size:1.5rem;">
            Welcome back, {{ auth()->user()->name }}! 🚀
          </h4>
          <p class="mb-0" style="color:rgba(255,255,255,.8);">
            Here's a live overview of all your schools &amp; revenue today.
          </p>
        </div>
        <div class="d-flex align-items-center gap-3">
          <div style="background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25); border-radius:.75rem; padding:.6rem 1.1rem; text-align:center; min-width:90px;">
            <div style="color:rgba(255,255,255,.75); font-size:.75rem; margin-bottom:.2rem;">Today</div>
            <div style="color:#fff; font-weight:700; font-size:1.05rem;">{{ now()->format('d M') }}</div>
            <div style="color:rgba(255,255,255,.75); font-size:.75rem;">{{ now()->format('Y') }}</div>
          </div>
          <div style="background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25); border-radius:.75rem; padding:.6rem 1.1rem; text-align:center; min-width:90px; position:relative;">
            <div style="color:rgba(255,255,255,.75); font-size:.75rem; margin-bottom:.2rem;">Schools</div>
            <div style="color:#fff; font-weight:800; font-size:1.6rem; line-height:1.1;">{{ $totalSchools }}</div>
            <div style="color:rgba(255,255,255,.75); font-size:.75rem;">Total</div>
            @if($newSchoolsThisMonth > 0)
              <span style="position:absolute; top:-8px; right:-8px; background:#28c76f; color:#fff; font-size:.65rem; font-weight:700; padding:.2rem .5rem; border-radius:50rem; border:2px solid rgba(255,255,255,.3);">
                +{{ $newSchoolsThisMonth }} new
              </span>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Stat Cards ───────────────────────────────────────── --}}
  <div class="row g-4 mb-5">

    {{-- Total Schools --}}
    <div class="col-sm-6 col-xl-3">
      <div class="card h-100" style="border-radius:1rem; border:none; transition:transform .2s ease, box-shadow .2s ease;" onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(105,108,255,.18)'" onmouseleave="this.style.transform='';this.style.boxShadow=''">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-4">
            <span class="avatar-initial rounded bg-label-primary" style="width:52px;height:52px;display:flex;align-items:center;justify-content:center;border-radius:.85rem!important;font-size:1.45rem;">
              <i class="ti tabler-building text-primary"></i>
            </span>
            <span class="badge bg-label-primary rounded-pill">Tenants</span>
          </div>
          <h2 class="mb-1 fw-bolder">{{ $totalSchools }}</h2>
          <p class="mb-3 text-muted fw-medium">Total Schools</p>
          <div class="d-flex gap-3">
            <span class="small d-flex align-items-center gap-1 text-success fw-semibold">
              <i class="ti tabler-circle-filled" style="font-size:8px;"></i>
              {{ $activeSchools }} Active
            </span>
            <span class="small d-flex align-items-center gap-1 text-danger fw-semibold">
              <i class="ti tabler-circle-filled" style="font-size:8px;"></i>
              {{ $inactiveSchools }} Inactive
            </span>
          </div>
        </div>
      </div>
    </div>

    {{-- Active Students --}}
    <div class="col-sm-6 col-xl-3">
      <div class="card h-100" style="border-radius:1rem; border:none; transition:transform .2s ease, box-shadow .2s ease;" onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(40,199,111,.18)'" onmouseleave="this.style.transform='';this.style.boxShadow=''">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-4">
            <span class="avatar-initial rounded bg-label-success" style="width:52px;height:52px;display:flex;align-items:center;justify-content:center;border-radius:.85rem!important;font-size:1.45rem;">
              <i class="ti tabler-users text-success"></i>
            </span>
            <span class="badge bg-label-success rounded-pill">Live</span>
          </div>
          <h2 class="mb-1 fw-bolder">{{ number_format($totalStudents) }}</h2>
          <p class="mb-3 text-muted fw-medium">Active Students</p>
          <p class="small text-muted mb-0">Across {{ $activeSchools }} active {{ Str::plural('school', $activeSchools) }}</p>
        </div>
      </div>
    </div>

    {{-- Monthly Revenue --}}
    <div class="col-sm-6 col-xl-3">
      <div class="card h-100" style="border-radius:1rem; border:none; transition:transform .2s ease, box-shadow .2s ease;" onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(255,171,0,.18)'" onmouseleave="this.style.transform='';this.style.boxShadow=''">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-4">
            <span class="avatar-initial rounded bg-label-warning" style="width:52px;height:52px;display:flex;align-items:center;justify-content:center;border-radius:.85rem!important;font-size:1.45rem;">
              <i class="ti tabler-currency-rupee text-warning"></i>
            </span>
            <span class="badge bg-label-warning rounded-pill">{{ now()->format('M') }}</span>
          </div>
          <h2 class="mb-1 fw-bolder">₹{{ number_format($currentMonthRev) }}</h2>
          <p class="mb-3 text-muted fw-medium">Monthly Revenue</p>
          <p class="small text-muted mb-0">{{ number_format($totalStudents) }} students × avg rate</p>
        </div>
      </div>
    </div>

    {{-- Annual Projection --}}
    <div class="col-sm-6 col-xl-3">
      <div class="card h-100" style="border-radius:1rem; border:none; transition:transform .2s ease, box-shadow .2s ease;" onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(3,195,236,.18)'" onmouseleave="this.style.transform='';this.style.boxShadow=''">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-4">
            <span class="avatar-initial rounded bg-label-info" style="width:52px;height:52px;display:flex;align-items:center;justify-content:center;border-radius:.85rem!important;font-size:1.45rem;">
              <i class="ti tabler-chart-line text-info"></i>
            </span>
            <span class="badge bg-label-info rounded-pill">Projected</span>
          </div>
          <h2 class="mb-1 fw-bolder">₹{{ number_format($currentMonthRev * 12) }}</h2>
          <p class="mb-3 text-muted fw-medium">Annual Projection</p>
          <p class="small text-muted mb-0">At current student count</p>
        </div>
      </div>
    </div>

  </div>

  {{-- ── Main Content Row ─────────────────────────────────── --}}
  <div class="row g-4 mb-4">

    {{-- Schools Overview Table --}}
    <div class="col-lg-8">
      <div class="card h-100" style="border-radius:1rem; border:none;">
        <div class="card-header d-flex justify-content-between align-items-center py-3 border-bottom" style="background:transparent;">
          <div>
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
              <span class="avatar-initial rounded bg-label-primary" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
                <i class="ti tabler-building text-primary" style="font-size:1rem;"></i>
              </span>
              Schools Overview
            </h5>
            <p class="text-muted small mb-0 mt-1">Live student counts &amp; billing per school</p>
          </div>
          <a href="{{ route('superadmin.schools.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
            <i class="ti tabler-arrow-right me-1"></i> View All
          </a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr style="font-size:.72rem; letter-spacing:.06em; text-transform:uppercase;">
                <th style="min-width:180px; white-space:nowrap; padding-left:1.5rem;">School</th>
                <th style="min-width:130px; white-space:nowrap;">Domain</th>
                <th class="text-center" style="white-space:nowrap;">Students</th>
                <th class="text-center" style="white-space:nowrap;">Rate</th>
                <th class="text-end" style="white-space:nowrap;">Monthly Bill</th>
                <th class="text-center" style="white-space:nowrap;">Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($schoolStats as $stat)
                <tr style="vertical-align:middle;">
                  <td style="padding-left:1.5rem;">
                    <div class="d-flex align-items-center gap-2">
                      <span class="avatar-initial rounded" style="width:36px;height:36px;flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;border-radius:.55rem!important;font-size:.8rem;font-weight:700;background:var(--bs-primary-bg-subtle);color:var(--bs-primary);">
                        {{ mb_strtoupper(mb_substr($stat['name'], 0, 2)) }}
                      </span>
                      <a href="{{ route('superadmin.schools.show', $stat['id']) }}" class="fw-semibold text-body" style="font-size:.875rem;">
                        {{ $stat['name'] }}
                      </a>
                    </div>
                  </td>
                  <td>
                    @if($stat['domain'])
                      <span class="text-muted small">{{ $stat['domain'] }}</span>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                  <td class="text-center fw-semibold">{{ number_format($stat['students']) }}</td>
                  <td class="text-center">
                    <span class="badge bg-label-secondary">₹{{ $stat['rate'] }}/student</span>
                  </td>
                  <td class="text-end fw-bold text-success">₹{{ number_format($stat['monthly_bill']) }}</td>
                  <td class="text-center">
                    <span class="badge rounded-pill bg-label-{{ $stat['is_active'] ? 'success' : 'danger' }}">
                      {{ $stat['is_active'] ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6">
                    <div class="text-center py-5">
                      <i class="ti tabler-building-off d-block mb-3 text-muted" style="font-size:3rem; opacity:.3;"></i>
                      <p class="text-muted mb-3">No schools provisioned yet.</p>
                      <a href="{{ route('superadmin.schools.create') }}" class="btn btn-sm btn-primary rounded-pill">
                        <i class="ti tabler-plus me-1"></i> Add First School
                      </a>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
            @if(count($schoolStats) > 0)
            <tfoot>
              <tr class="table-light" style="font-size:.8rem; text-transform:uppercase; letter-spacing:.05em;">
                <td colspan="2" class="fw-bold text-muted" style="padding-left:1.5rem;">Totals</td>
                <td class="text-center fw-bold">{{ number_format($totalStudents) }}</td>
                <td></td>
                <td class="text-end fw-bold text-success">₹{{ number_format($currentMonthRev) }}</td>
                <td></td>
              </tr>
            </tfoot>
            @endif
          </table>
        </div>
      </div>
    </div>

    {{-- Right Column --}}
    <div class="col-lg-4 d-flex flex-column gap-4">

      {{-- Revenue Breakdown --}}
      <div class="card" style="border-radius:1rem; border:none;">
        <div class="card-header py-3 border-bottom" style="background:transparent;">
          <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
            <span class="avatar-initial rounded bg-label-warning" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
              <i class="ti tabler-chart-donut text-warning" style="font-size:1rem;"></i>
            </span>
            Revenue Breakdown
          </h5>
          <p class="text-muted small mb-0 mt-1">Per school — {{ now()->format('M Y') }}</p>
        </div>
        <div class="card-body">
          @php
            $revColors = ['primary','success','warning','info','danger','secondary'];
          @endphp
          @forelse($schoolStats as $stat)
            @if($stat['monthly_bill'] > 0)
              @php
                $pct   = $currentMonthRev > 0 ? round(($stat['monthly_bill'] / $currentMonthRev) * 100) : 0;
                $color = $revColors[$loop->index % count($revColors)];
              @endphp
              <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                  <span class="small fw-semibold text-truncate me-2" style="max-width:160px;">{{ $stat['name'] }}</span>
                  <span class="small fw-bold text-success">₹{{ number_format($stat['monthly_bill']) }}</span>
                </div>
                <div class="progress" style="height:7px; border-radius:50rem;">
                  <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width:{{ $pct }}%; border-radius:50rem;" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="text-end small text-muted mt-1">{{ $pct }}%</div>
              </div>
            @endif
          @empty
            <div class="text-center py-4">
              <i class="ti tabler-chart-bar-off d-block mb-2 text-muted" style="font-size:2rem; opacity:.35;"></i>
              <p class="text-muted small mb-0">No revenue data yet.</p>
            </div>
          @endforelse

          @if($currentMonthRev > 0)
            <div class="d-flex justify-content-between align-items-center pt-3 mt-1 border-top">
              <span class="fw-semibold text-muted" style="font-size:.75rem; text-transform:uppercase; letter-spacing:.05em;">Total This Month</span>
              <span class="fw-bold text-success fs-6">₹{{ number_format($currentMonthRev) }}</span>
            </div>
          @endif
        </div>
      </div>

      {{-- Quick Actions --}}
      <div class="card" style="border-radius:1rem; border:none;">
        <div class="card-header py-3 border-bottom" style="background:transparent;">
          <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
            <span class="avatar-initial rounded bg-label-info" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
              <i class="ti tabler-bolt text-info" style="font-size:1rem;"></i>
            </span>
            Quick Actions
          </h5>
        </div>
        <div class="card-body d-flex flex-column gap-2">

          <a href="{{ route('superadmin.schools.create') }}" class="d-flex align-items-center gap-3 text-decoration-none p-2 rounded-3" style="border:1px solid var(--bs-border-color); transition:all .2s ease;" onmouseenter="this.style.borderColor='#696cff';this.style.background='var(--bs-primary-bg-subtle)'" onmouseleave="this.style.borderColor='var(--bs-border-color)';this.style.background=''">
            <span class="avatar-initial rounded bg-label-primary" style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1.1rem;flex-shrink:0;">
              <i class="ti tabler-plus text-primary"></i>
            </span>
            <div class="flex-grow-1">
              <div class="fw-semibold text-body" style="font-size:.875rem;">Add New School</div>
              <div class="text-muted" style="font-size:.75rem;">Register a new tenant</div>
            </div>
            <i class="ti tabler-chevron-right text-muted"></i>
          </a>

          <a href="{{ route('superadmin.schools.index') }}" class="d-flex align-items-center gap-3 text-decoration-none p-2 rounded-3" style="border:1px solid var(--bs-border-color); transition:all .2s ease;" onmouseenter="this.style.borderColor='#28c76f';this.style.background='var(--bs-success-bg-subtle)'" onmouseleave="this.style.borderColor='var(--bs-border-color)';this.style.background=''">
            <span class="avatar-initial rounded bg-label-success" style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1.1rem;flex-shrink:0;">
              <i class="ti tabler-building text-success"></i>
            </span>
            <div class="flex-grow-1">
              <div class="fw-semibold text-body" style="font-size:.875rem;">Manage Schools</div>
              <div class="text-muted" style="font-size:.75rem;">View all tenants</div>
            </div>
            <i class="ti tabler-chevron-right text-muted"></i>
          </a>

          <a href="{{ route('superadmin.subscriptions.index') }}" class="d-flex align-items-center gap-3 text-decoration-none p-2 rounded-3" style="border:1px solid var(--bs-border-color); transition:all .2s ease;" onmouseenter="this.style.borderColor='#ffab00';this.style.background='var(--bs-warning-bg-subtle)'" onmouseleave="this.style.borderColor='var(--bs-border-color)';this.style.background=''">
            <span class="avatar-initial rounded bg-label-warning" style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1.1rem;flex-shrink:0;">
              <i class="ti tabler-receipt text-warning"></i>
            </span>
            <div class="flex-grow-1">
              <div class="fw-semibold text-body" style="font-size:.875rem;">Subscriptions</div>
              <div class="text-muted" style="font-size:.75rem;">Billing &amp; plans</div>
            </div>
            <i class="ti tabler-chevron-right text-muted"></i>
          </a>

          <a href="{{ route('superadmin.settings.index') }}" class="d-flex align-items-center gap-3 text-decoration-none p-2 rounded-3" style="border:1px solid var(--bs-border-color); transition:all .2s ease;" onmouseenter="this.style.borderColor='#8592a3';this.style.background='var(--bs-secondary-bg-subtle)'" onmouseleave="this.style.borderColor='var(--bs-border-color)';this.style.background=''">
            <span class="avatar-initial rounded bg-label-secondary" style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1.1rem;flex-shrink:0;">
              <i class="ti tabler-settings text-secondary"></i>
            </span>
            <div class="flex-grow-1">
              <div class="fw-semibold text-body" style="font-size:.875rem;">Global Settings</div>
              <div class="text-muted" style="font-size:.75rem;">Configure platform</div>
            </div>
            <i class="ti tabler-chevron-right text-muted"></i>
          </a>

        </div>
      </div>

    </div>
  </div>

  {{-- ── Revenue Trend Chart ───────────────────────────────── --}}
  @if($currentMonthRev > 0)
  <div class="row g-4">
    <div class="col-12">
      <div class="card" style="border-radius:1rem; border:none;">
        <div class="card-header d-flex justify-content-between align-items-center py-3 border-bottom" style="background:transparent;">
          <div>
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
              <span class="avatar-initial rounded bg-label-primary" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
                <i class="ti tabler-chart-bar text-primary" style="font-size:1rem;"></i>
              </span>
              Revenue Trend
            </h5>
            <p class="text-muted small mb-0 mt-1">Monthly revenue — last 6 months</p>
          </div>
          <span class="badge bg-label-primary rounded-pill px-3">₹{{ number_format($currentMonthRev) }} this month</span>
        </div>
        <div class="card-body" style="padding:1.5rem;">
          <canvas id="revenueChart" style="max-height:220px;"></canvas>
        </div>
      </div>
    </div>
  </div>
  @endif

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  (function () {
    const canvas = document.getElementById('revenueChart');
    if (!canvas) { return; }

    const labels  = @json(array_column($monthlyRevenueChart, 'month'));
    const data    = @json(array_column($monthlyRevenueChart, 'revenue'));

    const isDark  = document.documentElement.classList.contains('dark-layout');
    const gridCol = isDark ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.05)';
    const textCol = isDark ? 'rgba(255,255,255,.5)'  : 'rgba(100,116,139,.8)';

    new Chart(canvas, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Revenue (₹)',
          data: data,
          backgroundColor: data.map((_, i) =>
            i === data.length - 1
              ? 'rgba(105,108,255,.85)'
              : 'rgba(105,108,255,.25)'
          ),
          borderColor: data.map((_, i) =>
            i === data.length - 1
              ? 'rgba(105,108,255,1)'
              : 'rgba(105,108,255,.4)'
          ),
          borderWidth: 2,
          borderRadius: 8,
          borderSkipped: false,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: ctx => '₹' + ctx.parsed.y.toLocaleString('en-IN')
            }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { color: textCol, font: { size: 12, weight: '600' } }
          },
          y: {
            grid: { color: gridCol },
            ticks: {
              color: textCol,
              callback: val => '₹' + (val >= 1000 ? (val / 1000).toFixed(0) + 'k' : val)
            },
            beginAtZero: true
          }
        }
      }
    });
  })();
</script>
@endpush
@endsection