@extends('layouts.superadmin.superadmin')

@section('title', 'Schools')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- ── Hero Banner ──────────────────────────────────────────────── --}}
  <div class="card mb-5" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); border: none; border-radius: 1rem; overflow: hidden; position: relative;">
    <span style="position:absolute; width:300px; height:300px; border-radius:50%; background:rgba(105,108,255,.1); top:-80px; right:-60px; pointer-events:none;"></span>
    <span style="position:absolute; width:180px; height:180px; border-radius:50%; background:rgba(40,199,111,.07); bottom:-50px; left:60px; pointer-events:none;"></span>
    <div class="card-body py-4 px-4" style="position:relative; z-index:1;">
      <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
        <div>
          <span style="display:inline-flex; align-items:center; gap:.4rem; background:rgba(105,108,255,.25); border:1px solid rgba(105,108,255,.4); color:#a5b4fc; font-size:.72rem; font-weight:600; letter-spacing:.05em; text-transform:uppercase; padding:.25rem .85rem; border-radius:50rem; margin-bottom:.85rem;">
            <i class="ti tabler-building" style="font-size:.85rem;"></i>
            Tenant Management
          </span>
          <h4 class="mb-1" style="color:#fff; font-weight:700; font-size:1.45rem;">
            Schools Overview
          </h4>
          <p class="mb-0" style="color:rgba(255,255,255,.6);">
            All registered tenants on the platform — manage, edit, and monitor.
          </p>
        </div>
        <div class="d-flex align-items-center gap-3">
          <div style="background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.15); border-radius:.75rem; padding:.6rem 1.2rem; text-align:center; min-width:80px;">
            <div style="color:rgba(255,255,255,.6); font-size:.72rem; margin-bottom:.2rem;">Total</div>
            <div style="color:#fff; font-weight:800; font-size:1.7rem; line-height:1.1;">{{ $schools->total() }}</div>
            <div style="color:rgba(255,255,255,.6); font-size:.72rem;">Schools</div>
          </div>
          <div style="background:rgba(40,199,111,.15); border:1px solid rgba(40,199,111,.3); border-radius:.75rem; padding:.6rem 1.2rem; text-align:center; min-width:80px;">
            <div style="color:rgba(255,255,255,.6); font-size:.72rem; margin-bottom:.2rem;">Active</div>
            <div style="color:#28c76f; font-weight:800; font-size:1.7rem; line-height:1.1;">{{ $activeCount }}</div>
            <div style="color:rgba(255,255,255,.6); font-size:.72rem;">Online</div>
          </div>
          <a href="{{ route('superadmin.schools.create') }}" class="btn btn-primary fw-semibold px-4" style="border-radius:.75rem; box-shadow: 0 4px 15px rgba(105,108,255,.4);">
            <i class="ti tabler-plus me-1"></i> Add School
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Alerts ──────────────────────────────────────────────────── --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible mb-4" role="alert" style="border-radius:.85rem; border:none; background: linear-gradient(135deg,rgba(40,199,111,.15),rgba(40,199,111,.05)); border-left: 4px solid #28c76f;">
      <i class="icon-base ti tabler-circle-check me-2 text-success"></i>
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- ── Schools Table Card ──────────────────────────────────────── --}}
  <div class="card" style="border-radius:1rem; border:none;">
    <div class="card-header py-3 border-bottom" style="background:transparent;">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
          <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
            <span class="avatar-initial rounded bg-label-primary" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
              <i class="ti tabler-building text-primary" style="font-size:1rem;"></i>
            </span>
            All Schools
          </h5>
          <p class="text-muted small mb-0 mt-1">Live student counts &amp; status per school</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <div class="input-group" style="width:240px;">
            <span class="input-group-text border-end-0 bg-transparent">
              <i class="ti tabler-search text-muted" style="font-size:.9rem;"></i>
            </span>
            <input type="text" id="schoolSearch" class="form-control border-start-0 ps-0"
                   placeholder="Search schools..." oninput="filterSchools()" autocomplete="off">
          </div>
          <select id="statusFilter" class="form-select" style="width:auto;" onchange="filterSchools()">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover mb-0" id="schoolsTable">
        <thead>
          <tr style="font-size:.72rem; letter-spacing:.06em; text-transform:uppercase; background: var(--bs-body-bg);">
            <th style="min-width:200px; white-space:nowrap; padding-left:1.5rem;">School</th>
            <th style="min-width:160px; white-space:nowrap;">Subdomain</th>
            <th style="min-width:180px; white-space:nowrap;">Email</th>
            <th class="text-center" style="white-space:nowrap;">Students</th>
            <th class="text-center" style="white-space:nowrap;">Rate</th>
            <th class="text-center" style="white-space:nowrap;">Status</th>
            <th style="white-space:nowrap;">Registered</th>
            <th class="text-center" style="white-space:nowrap;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($schools as $school)
            <tr style="vertical-align:middle;" data-name="{{ strtolower($school->school_name) }}" data-domain="{{ strtolower($school->domains->first()?->domain ?? '') }}" data-email="{{ strtolower($school->email) }}" data-status="{{ $school->is_active ? 'active' : 'inactive' }}">
              {{-- School Name --}}
              <td style="padding-left:1.5rem;">
                <div class="d-flex align-items-center gap-2">
                  <span class="avatar-initial rounded fw-bold" style="width:40px;height:40px;flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;border-radius:.7rem!important;font-size:.8rem;background:var(--bs-primary-bg-subtle);color:var(--bs-primary);">
                    {{ mb_strtoupper(mb_substr($school->school_name, 0, 2)) }}
                  </span>
                  <div>
                    <a href="{{ route('superadmin.schools.show', $school) }}" class="fw-semibold text-body d-block" style="font-size:.875rem;">
                      {{ $school->school_name }}
                    </a>
                    @if($school->school_name_hi)
                      <span class="text-muted small">{{ $school->school_name_hi }}</span>
                    @else
                      <span class="text-muted small" style="font-size:.7rem;">ID: {{ $school->id }}</span>
                    @endif
                  </div>
                </div>
              </td>

              {{-- Domain --}}
              <td>
                @if($school->domains->first())
                  <a href="http://{{ $school->domains->first()->domain }}" target="_blank" class="d-flex align-items-center gap-1 text-primary small fw-semibold text-decoration-none">
                    <i class="ti tabler-link" style="font-size:.85rem;"></i>
                    {{ $school->domains->first()->domain }}
                    <i class="ti tabler-external-link text-muted" style="font-size:.75rem;"></i>
                  </a>
                @else
                  <span class="text-muted small">—</span>
                @endif
              </td>

              {{-- Email --}}
              <td>
                <a href="mailto:{{ $school->email }}" class="text-body small">{{ $school->email }}</a>
              </td>

              {{-- Students --}}
              <td class="text-center">
                <span class="fw-bold" style="font-size:1rem;">{{ number_format($schoolStudents[$school->id] ?? 0) }}</span>
                <div class="text-muted" style="font-size:.7rem;">students</div>
              </td>

              {{-- Rate --}}
              <td class="text-center">
                <span class="badge bg-label-secondary fw-semibold">₹{{ $school->per_student_rate }}/student</span>
              </td>

              {{-- Status --}}
              <td class="text-center">
                <span class="badge rounded-pill bg-label-{{ $school->is_active ? 'success' : 'danger' }}" style="font-size:.75rem; padding:.35em .85em;">
                  <i class="ti tabler-circle-filled me-1" style="font-size:7px;"></i>
                  {{ $school->is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>

              {{-- Registered --}}
              <td>
                <span class="small text-muted">{{ $school->created_at->format('d M Y') }}</span>
              </td>

              {{-- Actions --}}
              <td class="text-center">
                <div class="d-flex justify-content-center gap-1">
                  <a href="{{ route('superadmin.schools.show', $school) }}"
                     class="btn btn-sm btn-icon btn-outline-primary"
                     title="View Details" style="border-radius:.6rem;">
                    <i class="icon-base ti tabler-eye"></i>
                  </a>
                  <a href="{{ route('superadmin.schools.edit', $school) }}"
                     class="btn btn-sm btn-icon btn-outline-warning"
                     title="Edit" style="border-radius:.6rem;">
                    <i class="icon-base ti tabler-edit"></i>
                  </a>
                  <a href="{{ route('superadmin.subscriptions.show', $school) }}"
                     class="btn btn-sm btn-icon btn-outline-info"
                     title="Billing" style="border-radius:.6rem;">
                    <i class="icon-base ti tabler-receipt"></i>
                  </a>
                  <form action="{{ route('superadmin.schools.destroy', $school) }}"
                        method="POST"
                        onsubmit="return confirm('Delete {{ addslashes($school->school_name) }} and ALL its data?\n\nThis cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-icon btn-outline-danger" title="Delete" style="border-radius:.6rem;">
                      <i class="icon-base ti tabler-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8">
                <div class="text-center py-6">
                  <div class="avatar avatar-lg mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-secondary" style="width:72px;height:72px;font-size:2rem;">
                      <i class="ti tabler-building-off text-muted"></i>
                    </span>
                  </div>
                  <p class="text-muted mb-3 fw-medium">No schools provisioned yet.</p>
                  <a href="{{ route('superadmin.schools.create') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="ti tabler-plus me-1"></i> Add First School
                  </a>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Empty search state (hidden by default) --}}
    <div id="emptySearch" class="text-center py-5 d-none">
      <i class="ti tabler-search-off d-block mb-3 text-muted" style="font-size:2.5rem; opacity:.35;"></i>
      <p class="text-muted mb-0">No schools match your search.</p>
    </div>

    {{-- Pagination --}}
    @if($schools->hasPages())
      <div class="card-footer border-top py-3">
        {{ $schools->links() }}
      </div>
    @endif
  </div>

</div>

@push('scripts')
<script>
  function filterSchools() {
    const query  = document.getElementById('schoolSearch').value.toLowerCase().trim();
    const status = document.getElementById('statusFilter').value;
    const rows   = document.querySelectorAll('#schoolsTable tbody tr[data-name]');
    let   visible = 0;

    rows.forEach(row => {
      const matchSearch = !query || row.dataset.name.includes(query) || row.dataset.domain.includes(query) || row.dataset.email.includes(query);
      const matchStatus = !status || row.dataset.status === status;
      const show = matchSearch && matchStatus;
      row.style.display = show ? '' : 'none';
      if (show) visible++;
    });

    const empty = document.getElementById('emptySearch');
    if (empty) {
      empty.classList.toggle('d-none', visible > 0 || rows.length === 0);
    }
  }
</script>
@endpush
@endsection