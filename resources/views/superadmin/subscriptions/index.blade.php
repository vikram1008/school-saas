@extends('layouts.superadmin.superadmin')

@section('title', 'Subscription Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- ── Hero Banner ──────────────────────────────────────────────── --}}
  <div class="card mb-5" style="background: linear-gradient(135deg, #1b1033 0%, #2d1b4e 50%, #1b2d4e 100%); border: none; border-radius: 1rem; overflow: hidden; position: relative;">
    <span style="position:absolute;width:280px;height:280px;border-radius:50%;background:rgba(255,171,0,.08);top:-80px;right:-60px;pointer-events:none;"></span>
    <span style="position:absolute;width:180px;height:180px;border-radius:50%;background:rgba(105,108,255,.06);bottom:-50px;left:60px;pointer-events:none;"></span>
    <div class="card-body py-4 px-4" style="position:relative;z-index:1;">
      <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
        <div>
          <span style="display:inline-flex; align-items:center; gap:.4rem; background:rgba(255,171,0,.2); border:1px solid rgba(255,171,0,.35); color:#ffd060; font-size:.72rem; font-weight:600; letter-spacing:.05em; text-transform:uppercase; padding:.25rem .85rem; border-radius:50rem; margin-bottom:.85rem;">
            <i class="ti tabler-receipt" style="font-size:.85rem;"></i>
            Billing &amp; Subscriptions
          </span>
          <h4 class="mb-1" style="color:#fff; font-weight:700; font-size:1.45rem;">Subscription Management</h4>
          <p class="mb-0" style="color:rgba(255,255,255,.6);">Monitor and manage all school subscriptions and billing cycles.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
          @if($overdueRevenue > 0)
            <div style="background:rgba(255,75,75,.15); border:1px solid rgba(255,75,75,.3); border-radius:.75rem; padding:.6rem 1.2rem; text-align:center;">
              <div style="color:rgba(255,255,255,.7); font-size:.72rem; margin-bottom:.15rem;">Revenue at Risk</div>
              <div style="color:#ff6b6b; font-weight:800; font-size:1.2rem;">₹{{ number_format($overdueRevenue) }}</div>
            </div>
          @endif
          <form action="{{ route('superadmin.subscriptions.run-check') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-warning fw-semibold px-4" style="border-radius:.75rem; box-shadow: 0 4px 15px rgba(255,171,0,.3);"
                    onclick="return confirm('Run subscription check now?')">
              <i class="ti tabler-refresh me-1"></i> Run Check
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Alerts ──────────────────────────────────────────────────── --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible mb-4" role="alert" style="border-radius:.85rem; border:none; border-left:4px solid #28c76f; background:rgba(40,199,111,.1);">
      <i class="icon-base ti tabler-circle-check me-2 text-success"></i>
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if(session('check_output'))
    <div class="alert alert-info alert-dismissible mb-4" style="border-radius:.85rem; border:none; border-left:4px solid #03c9ec; background:rgba(3,195,236,.1);">
      <strong>Check Output:</strong>
      <pre class="mb-0 mt-2 small">{{ session('check_output') }}</pre>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- ── Stat Cards ───────────────────────────────────────────────── --}}
  <div class="row g-4 mb-5">

    <div class="col-sm-6 col-lg-3">
      <div class="card h-100" style="border-radius:1rem; border:none; transition:transform .2s ease, box-shadow .2s ease;"
           onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(105,108,255,.18)'"
           onmouseleave="this.style.transform='';this.style.boxShadow=''">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-4">
            <span class="avatar-initial rounded bg-label-primary" style="width:52px;height:52px;display:flex;align-items:center;justify-content:center;border-radius:.85rem!important;font-size:1.45rem;">
              <i class="ti tabler-school text-primary"></i>
            </span>
            <span class="badge bg-label-primary rounded-pill">Total</span>
          </div>
          <h2 class="mb-1 fw-bolder">{{ $stats['total'] }}</h2>
          <p class="mb-0 text-muted fw-medium">Total Schools</p>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card h-100" style="border-radius:1rem; border:none; transition:transform .2s ease, box-shadow .2s ease;"
           onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(40,199,111,.18)'"
           onmouseleave="this.style.transform='';this.style.boxShadow=''">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-4">
            <span class="avatar-initial rounded bg-label-success" style="width:52px;height:52px;display:flex;align-items:center;justify-content:center;border-radius:.85rem!important;font-size:1.45rem;">
              <i class="ti tabler-circle-check text-success"></i>
            </span>
            <span class="badge bg-label-success rounded-pill">Healthy</span>
          </div>
          <h2 class="mb-1 fw-bolder text-success">{{ $stats['active'] }}</h2>
          <p class="mb-0 text-muted fw-medium">Active</p>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card h-100" style="border-radius:1rem; border:none; transition:transform .2s ease, box-shadow .2s ease;"
           onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(255,171,0,.18)'"
           onmouseleave="this.style.transform='';this.style.boxShadow=''">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-4">
            <span class="avatar-initial rounded bg-label-warning" style="width:52px;height:52px;display:flex;align-items:center;justify-content:center;border-radius:.85rem!important;font-size:1.45rem;">
              <i class="ti tabler-alert-triangle text-warning"></i>
            </span>
            <span class="badge bg-label-warning rounded-pill">At Risk</span>
          </div>
          <h2 class="mb-1 fw-bolder text-warning">{{ $stats['grace_warning'] + $stats['grace_readonly'] }}</h2>
          <p class="mb-0 text-muted fw-medium">In Grace Period</p>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card h-100" style="border-radius:1rem; border:none; transition:transform .2s ease, box-shadow .2s ease;"
           onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(255,75,75,.18)'"
           onmouseleave="this.style.transform='';this.style.boxShadow=''">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-4">
            <span class="avatar-initial rounded bg-label-danger" style="width:52px;height:52px;display:flex;align-items:center;justify-content:center;border-radius:.85rem!important;font-size:1.45rem;">
              <i class="ti tabler-lock text-danger"></i>
            </span>
            <span class="badge bg-label-danger rounded-pill">Locked</span>
          </div>
          <h2 class="mb-1 fw-bolder text-danger">{{ $stats['suspended'] }}</h2>
          <p class="mb-0 text-muted fw-medium">Suspended</p>
        </div>
      </div>
    </div>

  </div>

  {{-- ── Subscriptions Table ──────────────────────────────────────── --}}
  <div class="card" style="border-radius:1rem; border:none;">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 py-3 border-bottom" style="background:transparent;">
      <div class="d-flex align-items-center gap-2">
        <span class="avatar-initial rounded bg-label-warning" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
          <i class="ti tabler-receipt text-warning" style="font-size:1rem;"></i>
        </span>
        <div>
          <h5 class="mb-0 fw-bold">All School Subscriptions</h5>
          <p class="text-muted small mb-0">Current billing cycle per school</p>
        </div>
      </div>
      <div class="d-flex gap-2">
        <select class="form-select" id="statusFilter" style="width:auto;" onchange="filterTable()">
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
          <tr style="font-size:.72rem; letter-spacing:.06em; text-transform:uppercase;">
            <th style="min-width:180px; white-space:nowrap; padding-left:1.5rem;">School</th>
            <th style="white-space:nowrap;">Cycle</th>
            <th style="min-width:180px; white-space:nowrap;">Period</th>
            <th class="text-center" style="white-space:nowrap;">Students</th>
            <th class="text-end" style="white-space:nowrap;">Amount Due</th>
            <th class="text-center" style="white-space:nowrap;">Overdue</th>
            <th class="text-center" style="white-space:nowrap;">Status</th>
            <th class="text-center" style="white-space:nowrap;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($subscriptions as $sub)
            @php
              $rowBg = match($sub->status) {
                'grace_warning'  => 'rgba(255,171,0,.04)',
                'grace_readonly' => 'rgba(255,100,0,.05)',
                'suspended'      => 'rgba(255,75,75,.05)',
                default          => '',
              };
              $statusColors = [
                'active'         => 'success',
                'grace_warning'  => 'warning',
                'grace_readonly' => 'danger',
                'suspended'      => 'dark',
              ];
            @endphp
            <tr data-status="{{ $sub->status }}" style="vertical-align:middle; {{ $rowBg ? 'background:'.$rowBg.';' : '' }}">

              {{-- School --}}
              <td style="padding-left:1.5rem;">
                <div class="d-flex align-items-center gap-2">
                  <span class="avatar-initial rounded fw-bold" style="width:36px;height:36px;flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;border-radius:.55rem!important;font-size:.78rem;background:var(--bs-primary-bg-subtle);color:var(--bs-primary);">
                    {{ mb_strtoupper(mb_substr($sub->tenant->school_name, 0, 2)) }}
                  </span>
                  <div>
                    <a href="{{ route('superadmin.subscriptions.show', $sub->tenant) }}" class="fw-semibold text-body d-block" style="font-size:.875rem;">
                      {{ $sub->tenant->school_name }}
                    </a>
                    <span class="text-muted small">{{ $sub->tenant->domains->first()?->domain ?? '—' }}</span>
                  </div>
                </div>
              </td>

              {{-- Cycle --}}
              <td>
                <span class="badge bg-label-secondary">{{ ucfirst(str_replace('_', ' ', $sub->billing_cycle)) }}</span>
              </td>

              {{-- Period --}}
              <td>
                <span class="small fw-semibold">{{ $sub->period_start->format('d M Y') }}</span>
                <span class="text-muted small mx-1">→</span>
                <span class="small fw-semibold">{{ $sub->period_end->format('d M Y') }}</span>
              </td>

              {{-- Students --}}
              <td class="text-center fw-semibold">{{ number_format($sub->student_count_snapshot) }}</td>

              {{-- Amount --}}
              <td class="text-end fw-bold">₹{{ number_format($sub->amount_due) }}</td>

              {{-- Overdue --}}
              <td class="text-center">
                @if($sub->days_overdue > 0)
                  <span class="badge bg-danger fw-semibold" style="min-width:60px;">{{ $sub->days_overdue }}d</span>
                @else
                  <span class="text-muted small">—</span>
                @endif
              </td>

              {{-- Status --}}
              <td class="text-center">
                <span class="badge rounded-pill bg-label-{{ $statusColors[$sub->status] ?? 'secondary' }}" style="font-size:.75rem; padding:.35em .9em;">
                  {{ ucfirst(str_replace('_', ' ', $sub->status)) }}
                </span>
              </td>

              {{-- Actions --}}
              <td class="text-center">
                <div class="d-flex justify-content-center gap-1">
                  <a href="{{ route('superadmin.subscriptions.show', $sub->tenant) }}"
                     class="btn btn-sm btn-icon btn-outline-primary" title="View History" style="border-radius:.55rem;">
                    <i class="icon-base ti tabler-eye"></i>
                  </a>
                  @if(in_array($sub->status, ['grace_warning', 'grace_readonly', 'suspended']))
                    <button type="button" class="btn btn-sm btn-icon btn-outline-success" title="Mark Paid" style="border-radius:.55rem;"
                            data-bs-toggle="modal" data-bs-target="#markPaidModal{{ $sub->id }}">
                      <i class="icon-base ti tabler-check"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-icon btn-outline-warning" title="Waive" style="border-radius:.55rem;"
                            data-bs-toggle="modal" data-bs-target="#waiveModal{{ $sub->id }}">
                      <i class="icon-base ti tabler-gift"></i>
                    </button>
                  @endif
                  @if($sub->status === 'suspended')
                    <form action="{{ route('superadmin.subscriptions.reactivate', $sub->tenant) }}" method="POST"
                          onsubmit="return confirm('Reactivate {{ $sub->tenant->school_name }}?')">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-icon btn-outline-info" title="Reactivate" style="border-radius:.55rem;">
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
                <div class="modal-content" style="border-radius:1rem; border:none;">
                  <form action="{{ route('superadmin.subscriptions.mark-paid', $sub) }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom" style="background:var(--bs-success-bg-subtle);">
                      <h5 class="modal-title d-flex align-items-center gap-2">
                        <span class="avatar-initial rounded bg-label-success" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;">
                          <i class="ti tabler-check text-success" style="font-size:1rem;"></i>
                        </span>
                        Mark as Paid
                      </h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <div class="p-3 rounded-3 mb-3" style="background:var(--bs-info-bg-subtle); border:1px solid var(--bs-info-border-subtle);">
                        <p class="small mb-1"><strong>{{ $sub->tenant->school_name }}</strong></p>
                        <p class="small mb-0">Amount due: <strong class="text-success">₹{{ number_format($sub->amount_due) }}</strong>
                          · {{ $sub->period_start->format('d M Y') }} → {{ $sub->period_end->format('d M Y') }}</p>
                      </div>
                      <div class="mb-3">
                        <label class="form-label fw-semibold">Amount Received <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-text fw-bold">₹</span>
                          <input type="number" name="amount_paid" class="form-control" value="{{ $sub->amount_due }}" step="0.01" min="0" required>
                        </div>
                      </div>
                      <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Reference</label>
                        <input type="text" name="payment_reference" class="form-control" placeholder="UPI ref, cheque no, transaction ID...">
                      </div>
                      <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-success fw-semibold">
                        <i class="icon-base ti tabler-check me-1"></i> Confirm Payment
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            {{-- Waive Modal --}}
            <div class="modal fade" id="waiveModal{{ $sub->id }}" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content" style="border-radius:1rem; border:none;">
                  <form action="{{ route('superadmin.subscriptions.waive', $sub) }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom" style="background:var(--bs-warning-bg-subtle);">
                      <h5 class="modal-title d-flex align-items-center gap-2">
                        <span class="avatar-initial rounded bg-label-warning" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;">
                          <i class="ti tabler-gift text-warning" style="font-size:1rem;"></i>
                        </span>
                        Waive Subscription
                      </h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <div class="p-3 rounded-3 mb-3" style="background:var(--bs-warning-bg-subtle); border:1px solid var(--bs-warning-border-subtle);">
                        <p class="small mb-0">Waiving <strong class="text-warning">₹{{ number_format($sub->amount_due) }}</strong> for <strong>{{ $sub->tenant->school_name }}</strong>. A new cycle will be created automatically.</p>
                      </div>
                      <div class="mb-3">
                        <label class="form-label fw-semibold">Reason <span class="text-danger">*</span></label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Reason for waiving this subscription..." required></textarea>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-warning fw-semibold">
                        <i class="icon-base ti tabler-gift me-1"></i> Confirm Waive
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

          @empty
            <tr>
              <td colspan="8">
                <div class="text-center py-6">
                  <span class="avatar-initial rounded-circle mx-auto mb-3" style="width:72px;height:72px;display:flex;align-items:center;justify-content:center;background:var(--bs-secondary-bg);font-size:2rem;">
                    <i class="ti tabler-receipt-off text-muted"></i>
                  </span>
                  <p class="text-muted mb-0">No subscriptions found.</p>
                </div>
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