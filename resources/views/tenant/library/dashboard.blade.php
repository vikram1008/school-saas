@extends('layouts.tenant')

@section('title', 'Library')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Library / पुस्तकालय</h4>
            <p class="text-muted mb-0 small">Manage books, members, and issue/return records.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tenant.library.books') }}" class="btn btn-outline-primary">
                <i class="icon-base ti tabler-books me-1"></i> Catalogue
            </a>
            <a href="{{ route('tenant.library.issues') }}" class="btn btn-primary">
                <i class="icon-base ti tabler-book-upload me-1"></i> Issue Book
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

    {{-- Stats Row --}}
    <div class="row g-4 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ti tabler-books"></i>
                            </span>
                        </div>
                        <span class="badge bg-label-primary">Catalogue</span>
                    </div>
                    <h3 class="fw-bold mb-0">{{ number_format($stats['total_books']) }}</h3>
                    <p class="text-muted small mb-1">Titles</p>
                    <p class="text-muted small mb-0">{{ number_format($stats['total_copies']) }} total copies</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-book-2"></i>
                            </span>
                        </div>
                        <span class="badge bg-label-success">Available</span>
                    </div>
                    <h3 class="fw-bold mb-0">{{ number_format($stats['available_copies']) }}</h3>
                    <p class="text-muted small mb-1">Copies Available</p>
                    <p class="text-muted small mb-0">{{ number_format($stats['active_issues']) }} currently issued</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-clock-exclamation"></i>
                            </span>
                        </div>
                        <span class="badge bg-label-warning">Overdue</span>
                    </div>
                    <h3 class="fw-bold mb-0">{{ number_format($stats['overdue_issues']) }}</h3>
                    <p class="text-muted small mb-1">Overdue Books</p>
                    <p class="text-muted small mb-0">
                        Fine due: ₹{{ number_format($stats['total_fine_due']) }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="icon-base ti tabler-users"></i>
                            </span>
                        </div>
                        <span class="badge bg-label-info">Members</span>
                    </div>
                    <h3 class="fw-bold mb-0">{{ number_format($stats['total_members']) }}</h3>
                    <p class="text-muted small mb-1">Active Members</p>
                    <p class="text-muted small mb-0">{{ number_format($stats['issued_today']) }} issued today</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Recent Active Issues --}}
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="icon-base ti tabler-book-upload me-2 text-primary"></i>
                        Active Issues
                    </h5>
                    <a href="{{ route('tenant.library.issues') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Book</th>
                                <th>Member</th>
                                <th>Due Date</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentIssues as $issue)
                                <tr>
                                    <td>
                                        <p class="fw-semibold mb-0 small">{{ Str::limit($issue->book->title, 30) }}</p>
                                        <p class="text-muted small mb-0">{{ $issue->book->accession_number }}</p>
                                    </td>
                                    <td>
                                        <p class="fw-semibold mb-0 small">
                                            {{ $issue->member->display_name }}
                                        </p>
                                        <p class="text-muted small mb-0 text-capitalize">{{ $issue->member->member_type }}</p>
                                    </td>
                                    <td class="small {{ $issue->isOverdue() ? 'text-danger fw-bold' : 'text-muted' }}">
                                        {{ $issue->due_date->format('d M Y') }}
                                        @if($issue->isOverdue())
                                            <br><span class="badge bg-label-danger">{{ $issue->overdueDays() }}d late</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-{{ \App\Models\BookIssue::statusColors()[$issue->status] ?? 'secondary' }}">
                                            {{ ucfirst($issue->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No active issues.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right column: Overdue alerts + Popular books --}}
        <div class="col-lg-5">

            {{-- Overdue Alert --}}
            @if($overdueIssues->isNotEmpty())
            <div class="card mb-4 border-danger" style="border-left: 4px solid #ea5455 !important;">
                <div class="card-header">
                    <h5 class="mb-0 text-danger">
                        <i class="icon-base ti tabler-alert-triangle me-2"></i>
                        Overdue Books ({{ $stats['overdue_issues'] }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($overdueIssues->take(5) as $issue)
                            <li class="list-group-item py-2 px-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="fw-semibold mb-0 small">{{ $issue->member->display_name }}</p>
                                        <p class="text-muted small mb-0">{{ Str::limit($issue->book->title, 25) }}</p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-danger">{{ $issue->overdueDays() }}d</span>
                                        <p class="text-danger small mb-0 fw-bold">₹{{ number_format($issue->calculateFine()) }}</p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    @if($stats['overdue_issues'] > 5)
                        <div class="card-footer text-center py-2">
                            <a href="{{ route('tenant.library.issues', ['status' => 'overdue']) }}" class="small text-danger">
                                View all {{ $stats['overdue_issues'] }} overdue books →
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Popular Books --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="icon-base ti tabler-trending-up me-2 text-success"></i>
                        Most Borrowed
                    </h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($popularBooks as $index => $book)
                            <li class="list-group-item py-2 px-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar avatar-sm">
                                        <span class="avatar-initial rounded bg-label-{{ ['primary','success','warning','info','danger'][$index % 5] }}">
                                            {{ $index + 1 }}
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="fw-semibold mb-0 small">{{ Str::limit($book->title, 30) }}</p>
                                        <p class="text-muted small mb-0">{{ $book->author ?? 'Unknown' }}</p>
                                    </div>
                                    <span class="badge bg-label-secondary">{{ $book->issues_count }} issues</span>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-3">No data yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>
    </div>

    {{-- Quick nav --}}
    <div class="row g-3 mt-2">
        <div class="col-md-4">
            <a href="{{ route('tenant.library.books') }}" class="card text-decoration-none h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="icon-base ti tabler-books"></i>
                        </span>
                    </div>
                    <div>
                        <p class="fw-bold mb-0">Book Catalogue</p>
                        <p class="text-muted small mb-0">Add, edit & search books</p>
                    </div>
                    <i class="icon-base ti tabler-chevron-right ms-auto text-muted"></i>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('tenant.library.members') }}" class="card text-decoration-none h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="icon-base ti tabler-id-badge-2"></i>
                        </span>
                    </div>
                    <div>
                        <p class="fw-bold mb-0">Library Members</p>
                        <p class="text-muted small mb-0">Register students & staff</p>
                    </div>
                    <i class="icon-base ti tabler-chevron-right ms-auto text-muted"></i>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('tenant.library.issues') }}" class="card text-decoration-none h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="icon-base ti tabler-book-upload"></i>
                        </span>
                    </div>
                    <div>
                        <p class="fw-bold mb-0">Issue / Return</p>
                        <p class="text-muted small mb-0">Track book issue transactions</p>
                    </div>
                    <i class="icon-base ti tabler-chevron-right ms-auto text-muted"></i>
                </div>
            </a>
        </div>
    </div>

</div>
@endsection
