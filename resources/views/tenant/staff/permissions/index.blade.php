@extends('layouts.tenant')
@section('title', 'Staff Permissions')
@section('content')

<div class="card mb-4" style="background:linear-gradient(135deg,#696cff 0%,#9155fd 100%);border:none;border-radius:1rem;">
  <div class="card-body py-4 px-4">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h4 class="mb-1" style="color:#fff;font-weight:700;"><i class="ti tabler-shield-check me-2"></i>Staff Permissions</h4>
        <p class="mb-0" style="color:rgba(255,255,255,.8);">Control which modules each staff member can access</p>
      </div>
    </div>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-4"><i class="ti tabler-circle-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card" style="border-radius:1rem;border:none;">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th class="ps-4 py-3 small fw-semibold text-muted">Staff Member</th>
            <th class="py-3 small fw-semibold text-muted">Role</th>
            <th class="py-3 small fw-semibold text-muted">Permissions</th>
            <th class="pe-4 py-3 small fw-semibold text-muted text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($staffMembers as $staff)
            @php $perms = $staff->staffPermission; $grantedCount = $perms ? collect($perms->toArray())->filter(fn($v, $k) => str_starts_with($k, 'can_') && $v)->count() : 0; $totalPerms = 15; @endphp
            <tr>
              <td class="ps-4 py-3">
                <div class="d-flex align-items-center gap-3">
                  <span class="avatar-initial rounded-circle bg-label-primary" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;flex-shrink:0;">
                    {{ strtoupper(substr($staff->name, 0, 1)) }}
                  </span>
                  <div>
                    <div class="fw-semibold">{{ $staff->name }}</div>
                    <div class="text-muted small">{{ $staff->email }}</div>
                  </div>
                </div>
              </td>
              <td class="py-3">
                <span class="badge bg-label-{{ match($staff->role) { 'teacher'=>'success', 'accountant'=>'warning', default=>'info' } }}">
                  {{ ucfirst($staff->role) }}
                </span>
              </td>
              <td class="py-3">
                @if($perms)
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height:6px;max-width:120px;border-radius:50rem;">
                      <div class="progress-bar bg-success" style="width:{{ round($grantedCount/$totalPerms*100) }}%;border-radius:50rem;"></div>
                    </div>
                    <span class="text-muted small">{{ $grantedCount }}/{{ $totalPerms }}</span>
                  </div>
                @else
                  <span class="badge bg-label-secondary">Default (not set)</span>
                @endif
              </td>
              <td class="pe-4 py-3 text-end">
                <a href="{{ route('tenant.staff.permissions.edit', $staff->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                  <i class="ti tabler-edit me-1"></i>Edit
                </a>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted py-5">No active staff members found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
