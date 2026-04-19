@extends('layouts.tenant')

@section('title', 'Parent Portal')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Welcome --}}
    <div class="card bg-primary text-white mb-4">
        <div class="card-body d-flex justify-content-between align-items-center py-4">
            <div>
                <h5 class="text-white fw-bold mb-1">
                    Welcome, {{ $parent->full_name }}! 🙏
                </h5>
                @if($parent->first_name_hi)
                    <p class="mb-1 opacity-75 small">{{ $parent->full_name_hi }}</p>
                @endif
                <p class="mb-0 opacity-75 small">
                    {{ tenant('school_name') }} — Parent Portal
                </p>
            </div>
            <div class="d-none d-sm-block">
                <i class="icon-base ti tabler-users-group"
                   style="font-size:3rem; opacity:0.6;"></i>
            </div>
        </div>
    </div>

    {{-- Children Cards --}}
    @forelse($studentData as $data)
        @php $student = $data['student']; @endphp
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold">{{ $student->full_name }}</h5>
                    @if($student->first_name_hi)
                        <p class="text-muted small mb-0">{{ $student->full_name_hi }}</p>
                    @endif
                </div>
                <span class="badge bg-label-info fs-6">{{ $student->class_section }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- Attendance --}}
                    <div class="col-sm-4">
                        <div class="border rounded p-3 text-center h-100">
                            <i class="icon-base ti tabler-user-check mb-2"
                               style="font-size:2rem; color:{{ $data['attendance_pct'] >= 75 ? '#28c76f' : '#ff4961' }}"></i>
                            <h4 class="fw-bold mb-0 {{ $data['attendance_pct'] >= 75 ? 'text-success' : 'text-danger' }}">
                                {{ $data['attendance_pct'] }}%
                            </h4>
                            <p class="text-muted small mb-2">Attendance This Month</p>
                            <p class="text-muted small mb-2">
                                {{ $data['attended'] }} / {{ $data['total_days'] }} days
                            </p>
                            <a href="{{ route('tenant.parent-portal.attendance', $student->id) }}"
                               class="btn btn-sm btn-outline-primary w-100">
                                View Details
                            </a>
                        </div>
                    </div>

                    {{-- Fee --}}
                    <div class="col-sm-4">
                        <div class="border rounded p-3 text-center h-100">
                            <i class="icon-base ti tabler-currency-rupee mb-2"
                               style="font-size:2rem; color:{{ $data['fee_balance'] > 0 ? '#ff4961' : '#28c76f' }}"></i>
                            <h4 class="fw-bold mb-0 {{ $data['fee_balance'] > 0 ? 'text-danger' : 'text-success' }}">
                                ₹{{ number_format($data['fee_balance']) }}
                            </h4>
                            <p class="text-muted small mb-2">
                                {{ $data['fee_balance'] > 0 ? 'Fee Balance Due' : 'All Fees Paid ✓' }}
                            </p>
                            <a href="{{ route('tenant.parent-portal.fees', $student->id) }}"
                               class="btn btn-sm btn-outline-{{ $data['fee_balance'] > 0 ? 'danger' : 'success' }} w-100">
                                View Fees
                            </a>
                        </div>
                    </div>

                    {{-- Quick Info --}}
                    <div class="col-sm-4">
                        <div class="border rounded p-3 h-100">
                            <h6 class="fw-bold mb-3">Student Info</h6>
                            <p class="text-muted small mb-1">Admission No.</p>
                            <p class="fw-semibold mb-2">{{ $student->admission_number }}</p>
                            @if($student->sr_number)
                                <p class="text-muted small mb-1">SR Number</p>
                                <p class="fw-semibold mb-2">{{ $student->sr_number }}</p>
                            @endif
                            <p class="text-muted small mb-1">Academic Year</p>
                            <p class="fw-semibold mb-0">{{ $activeYear?->name ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="icon-base ti tabler-users-off"
                   style="font-size:3rem; color:#ccc;"></i>
                <p class="text-muted mt-2 mb-0">
                    No children linked to your account. Please contact the school.
                </p>
            </div>
        </div>
    @endforelse

    {{-- Recent Notices --}}
    @if($notices->count() > 0)
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="icon-base ti tabler-speakerphone me-2 text-warning"></i>
                    Recent Notices / सूचनाएं
                </h5>
                <a href="{{ route('tenant.parent-portal.notices') }}"
                   class="btn btn-sm btn-outline-warning">
                    View All
                </a>
            </div>
            <div class="card-body">
                @foreach($notices as $notice)
                    <div class="d-flex gap-3 mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                        <div class="avatar avatar-sm flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-bell"></i>
                            </span>
                        </div>
                        <div>
                            <p class="fw-semibold mb-0">{{ $notice->title }}</p>
                            @if($notice->title_hi)
                                <p class="text-muted small mb-0">{{ $notice->title_hi }}</p>
                            @endif
                            <p class="text-muted small mb-0">
                                {{ $notice->published_at?->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection