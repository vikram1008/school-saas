@extends('layouts.tenant')

@section('title', 'Notices')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('tenant.parent-portal.dashboard') }}"
           class="btn btn-icon btn-outline-secondary me-3">
            <i class="icon-base ti tabler-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">Notices / सूचनाएं</h4>
            <p class="text-muted small mb-0">{{ tenant('school_name') }}</p>
        </div>
    </div>

    @forelse($notices as $notice)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h5 class="fw-bold mb-0">{{ $notice->title }}</h5>
                        @if($notice->title_hi)
                            <p class="text-muted small mb-0">
                                <span class="badge bg-label-warning me-1">हिं</span>
                                {{ $notice->title_hi }}
                            </p>
                        @endif
                    </div>
                    <span class="text-muted small text-nowrap ms-3">
                        {{ $notice->published_at?->format('d M Y') }}
                    </span>
                </div>
                <p class="mb-0">{{ $notice->content }}</p>
                @if($notice->content_hi)
                    <p class="text-muted small mt-2 mb-0">
                        <span class="badge bg-label-warning me-1">हिं</span>
                        {{ $notice->content_hi }}
                    </p>
                @endif
                @if($notice->expires_at)
                    <p class="text-muted small mt-2 mb-0">
                        <i class="icon-base ti tabler-clock me-1"></i>
                        Valid until: {{ $notice->expires_at->format('d M Y') }}
                    </p>
                @endif
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="icon-base ti tabler-speakerphone"
                   style="font-size:3rem; color:#ccc;"></i>
                <p class="text-muted mt-2 mb-0">No notices at this time.</p>
            </div>
        </div>
    @endforelse

    @if($notices->hasPages())
        <div class="mt-3">{{ $notices->links() }}</div>
    @endif

</div>
@endsection