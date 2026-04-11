@extends('layouts.tenant')

@section('title', 'Fee Collection')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Fee Collection / शुल्क संग्रह</h4>
            <p class="text-muted mb-0 small">Collect fees and manage student payments.</p>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('tenant.fees.collections.generate-demands') }}"
                  method="POST"
                  onsubmit="return confirm('Generate fee demands for this month?')">
                @csrf
                <button type="submit" class="btn btn-outline-primary">
                    <i class="icon-base ti tabler-refresh me-1"></i>
                    Generate Demands
                </button>
            </form>
            <a href="{{ route('tenant.fees.collections.create') }}" class="btn btn-primary">
                <i class="icon-base ti tabler-cash me-1"></i>
                Collect Fee
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-4">
            <i class="icon-base ti tabler-alert-circle me-1"></i>
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-cash"></i>
                            </span>
                        </div>
                        <span class="badge bg-label-success">Today</span>
                    </div>
                    <h3 class="fw-bold mb-1">₹{{ number_format($stats['total_collected_today']) }}</h3>
                    <p class="text-muted small mb-0">Collected Today</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ti tabler-calendar-stats"></i>
                            </span>
                        </div>
                        <span class="badge bg-label-primary">This Month</span>
                    </div>
                    <h3 class="fw-bold mb-1">₹{{ number_format($stats['total_collected_month']) }}</h3>
                    <p class="text-muted small mb-0">Collected This Month</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-clock-exclamation"></i>
                            </span>
                        </div>
                        <span class="badge bg-label-warning">Pending</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ number_format($stats['pending_demands']) }}</h3>
                    <p class="text-muted small mb-0">Pending Demands</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="icon-base ti tabler-alert-triangle"></i>
                            </span>
                        </div>
                        <span class="badge bg-label-danger">Overdue</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ number_format($stats['overdue_demands']) }}</h3>
                    <p class="text-muted small mb-0">Overdue Demands</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Quick Search --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="icon-base ti tabler-search me-2 text-primary"></i>
                        Student Ledger
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Search for a student to view their complete fee ledger.
                    </p>
                    <form action="{{ route('tenant.fees.collections.ledger') }}" method="GET">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Search Student</label>
                            <input type="text" name="q" id="studentSearch"
                                   class="form-control"
                                   placeholder="Name or Admission No...">
                            <input type="hidden" name="student_id" id="selectedStudentId">
                        </div>
                        <div id="searchResults" class="mb-3" style="display:none;">
                            <div class="list-group" id="searchResultsList"></div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="icon-base ti tabler-eye me-1"></i>
                            View Ledger
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Recent Collections --}}
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="icon-base ti tabler-receipt me-2 text-success"></i>
                        Recent Collections
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Receipt</th>
                                <th>Student</th>
                                <th>Mode</th>
                                <th class="text-end">Amount</th>
                                <th>Date</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCollections as $collection)
                                @php
                                    $modeColors = [
                                        'cash'          => 'success',
                                        'upi'           => 'primary',
                                        'bank_transfer' => 'info',
                                        'cheque'        => 'warning',
                                        'dd'            => 'warning',
                                        'online'        => 'primary',
                                    ];
                                @endphp
                                <tr>
                                    <td>
                                        <span class="fw-semibold small font-monospace">
                                            {{ $collection->receipt_number }}
                                        </span>
                                    </td>
                                    <td>
                                        <p class="fw-semibold mb-0 small">
                                            {{ $collection->student?->full_name }}
                                        </p>
                                        <p class="text-muted small mb-0">
                                            {{ $collection->student?->class_section }}
                                        </p>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-{{ $modeColors[$collection->payment_mode] ?? 'secondary' }}">
                                            {{ strtoupper($collection->payment_mode) }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        ₹{{ number_format($collection->total_amount) }}
                                    </td>
                                    <td class="small text-muted">
                                        {{ $collection->collection_date->format('d M Y') }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('tenant.fees.receipt', $collection) }}"
                                           class="btn btn-sm btn-icon btn-outline-primary"
                                           title="View Receipt" target="_blank">
                                            <i class="icon-base ti tabler-receipt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No collections yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
let searchTimer;
const searchInput    = document.getElementById('studentSearch');
const searchResults  = document.getElementById('searchResults');
const resultsList    = document.getElementById('searchResultsList');
const selectedIdInput = document.getElementById('selectedStudentId');

searchInput.addEventListener('input', function() {
    clearTimeout(searchTimer);
    const q = this.value.trim();
    if (q.length < 2) { searchResults.style.display = 'none'; return; }

    searchTimer = setTimeout(async () => {
        const res  = await fetch(`/fees/students/search?q=${encodeURIComponent(q)}`);
        const data = await res.json();

        resultsList.innerHTML = '';
        if (data.results.length === 0) {
            resultsList.innerHTML = '<div class="list-group-item text-muted small">No students found.</div>';
        } else {
            data.results.forEach(s => {
                const item = document.createElement('button');
                item.type  = 'button';
                item.className = 'list-group-item list-group-item-action small';
                item.textContent = s.text;
                item.addEventListener('click', () => {
                    searchInput.value    = s.text;
                    selectedIdInput.value = s.id;
                    searchResults.style.display = 'none';
                });
                resultsList.appendChild(item);
            });
        }
        searchResults.style.display = 'block';
    }, 300);
});

document.addEventListener('click', function(e) {
    if (!searchResults.contains(e.target) && e.target !== searchInput) {
        searchResults.style.display = 'none';
    }
});
</script>
@endpush

@endsection