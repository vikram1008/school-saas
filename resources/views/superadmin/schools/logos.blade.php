@php
use Illuminate\Support\Facades\Storage;
@endphp
@extends('layouts.superadmin.superadmin')

@section('title', 'School Logos')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">School Logos</h4>
            <p class="text-muted small mb-0">Upload or manage logos for all tenant schools.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($tenants as $tenant)
            @php
                // Get settings from tenant DB safely
                try {
                    tenancy()->initialize($tenant);
                    $sch = \App\Models\SchoolSettings::current();
                    tenancy()->end();
                } catch (\Exception $e) {
                    $sch = null;
                    tenancy()->end();
                }
                $domain = $tenant->domains->first()?->domain ?? $tenant->id;
            @endphp
            <div class="col-md-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            {{-- Current logo --}}
                            <div class="border rounded p-2 bg-light d-flex align-items-center justify-content-center"
                                 style="width:80px; height:80px; flex-shrink:0;">
                                @if($sch?->logo)
                                    <img src="{{ Storage::url($sch->logo) }}"
                                         alt="Logo" style="max-width:72px; max-height:72px; object-fit:contain;"
                                         onerror="this.src='{{ asset('assets/img/school-logo-placeholder.png') }}'">
                                @else
                                    <i class="icon-base ti tabler-building-school"
                                       style="font-size:2rem; color:#ccc;"></i>
                                @endif
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">
                                    {{ $sch?->school_name ?? $tenant->id }}
                                </h6>
                                @if($sch?->school_name_hi)
                                    <p class="text-muted small mb-1">{{ $sch->school_name_hi }}</p>
                                @endif
                                <a href="http://{{ $domain }}/dashboard"
                                   target="_blank"
                                   class="badge bg-label-primary text-decoration-none">
                                    <i class="icon-base ti tabler-external-link me-1"></i>
                                    {{ $domain }}
                                </a>
                            </div>
                        </div>

                        {{-- Upload form --}}
                        <form action="{{ route('superadmin.schools.logo.update', $tenant) }}"
                              method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-2">
                                <input type="file" name="logo"
                                       class="form-control form-control-sm"
                                       accept="image/png,image/jpeg,image/svg+xml,image/webp"
                                       required>
                                <div class="form-text">PNG, JPG, SVG. Max 2MB.</div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="icon-base ti tabler-upload me-1"></i>
                                    {{ $sch?->logo ? 'Replace Logo' : 'Upload Logo' }}
                                </button>
                                @if($sch?->logo)
                                    <button type="button"
                                            class="btn btn-outline-danger btn-sm"
                                            onclick="removeLogo('{{ $tenant->id }}')"
                                            title="Remove logo">
                                        <i class="icon-base ti tabler-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </form>

                        {{-- Remove form (hidden, triggered by JS) --}}
                        @if($sch?->logo)
                            <form id="removeLogoForm-{{ $tenant->id }}"
                                  action="{{ route('superadmin.schools.logo.remove', $tenant) }}"
                                  method="POST" style="display:none;">
                                @csrf @method('DELETE')
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="icon-base ti tabler-building-school"
                           style="font-size:3rem; color:#ccc;"></i>
                        <p class="text-muted mt-2 mb-0">No schools found.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function removeLogo(tenantId) {
    Swal.fire({
        icon: 'warning',
        title: 'Remove Logo?',
        text: 'The school logo will be deleted.',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'btn btn-danger me-2 waves-effect waves-light',
            cancelButton:  'btn btn-outline-secondary waves-effect',
        },
        buttonsStyling: false,
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('removeLogoForm-' + tenantId)?.submit();
        }
    });
}
</script>
@endpush

@endsection