@extends('layouts.tenant')
@section('title', 'Edit Permissions — ' . $staffUser->name)
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-1"><i class="ti tabler-shield-check me-2 text-primary"></i>Edit Permissions</h4>
    <p class="text-muted mb-0">{{ $staffUser->name }} &mdash; <span class="badge bg-label-{{ match($staffUser->role) { 'teacher'=>'success', 'accountant'=>'warning', default=>'info' } }}">{{ ucfirst($staffUser->role) }}</span></p>
  </div>
  <div class="d-flex gap-2">
    <form method="POST" action="{{ route('tenant.staff.permissions.defaults', $staffUser->id) }}">
      @csrf @method('PUT')
      <button type="submit" class="btn btn-outline-secondary rounded-pill" onclick="return confirm('Reset to role defaults?')">
        <i class="ti tabler-refresh me-1"></i>Reset Defaults
      </button>
    </form>
    <a href="{{ route('tenant.staff.permissions.index') }}" class="btn btn-outline-primary rounded-pill">
      <i class="ti tabler-arrow-left me-1"></i>Back
    </a>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-4"><i class="ti tabler-circle-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<form method="POST" action="{{ route('tenant.staff.permissions.update', $staffUser->id) }}">
  @csrf @method('PUT')
  <div class="row g-4">
    @foreach($grouped as $groupName => $items)
      <div class="col-lg-6">
        <div class="card" style="border-radius:1rem;border:none;">
          <div class="card-header py-3">
            <h6 class="mb-0 fw-bold text-uppercase small text-muted">{{ $groupName }}</h6>
          </div>
          <div class="card-body d-flex flex-column gap-3">
            @foreach($items as $item)
              <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="border:1px solid var(--bs-border-color);">
                <div class="d-flex align-items-center gap-3">
                  <span class="avatar-initial rounded bg-label-primary" style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
                    <i class="ti {{ $item['icon'] }} text-primary"></i>
                  </span>
                  <div>
                    <div class="fw-semibold" style="font-size:.875rem;">{{ $item['label'] }}</div>
                    <div class="text-muted" style="font-size:.75rem;">{{ $item['description'] }}</div>
                  </div>
                </div>
                <div class="form-check form-switch ms-3 mb-0">
                  <input class="form-check-input" type="checkbox" role="switch"
                    name="{{ $item['key'] }}"
                    id="perm_{{ $item['key'] }}"
                    style="width:2.5rem;height:1.25rem;cursor:pointer;"
                    {{ $permissions->{$item['key']} ? 'checked' : '' }}>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('tenant.staff.permissions.index') }}" class="btn btn-outline-secondary rounded-pill">Cancel</a>
    <button type="submit" class="btn btn-primary rounded-pill px-4">
      <i class="ti tabler-device-floppy me-1"></i>Save Permissions
    </button>
  </div>
</form>
@endsection
