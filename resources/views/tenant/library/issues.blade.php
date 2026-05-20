@extends('layouts.tenant')

@section('title', 'Book Issues & Returns')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Issue & Return / जारी करें और वापस करें</h4>
            <p class="text-muted mb-0 small">Manage book issue and return transactions.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tenant.library.dashboard') }}" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-arrow-left me-1"></i> Dashboard
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#issueBookModal">
                <i class="icon-base ti tabler-book-upload me-1"></i> Issue Book
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible mb-4">
            <i class="icon-base ti tabler-alert-circle me-1"></i> {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('tenant.library.issues') }}" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search issue no., book title, accession, member name..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Active Issues (issued + overdue)</option>
                        <option value="issued"   {{ request('status') === 'issued'   ? 'selected' : '' }}>Issued</option>
                        <option value="overdue"  {{ request('status') === 'overdue'  ? 'selected' : '' }}>Overdue</option>
                        <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned</option>
                        <option value="lost"     {{ request('status') === 'lost'     ? 'selected' : '' }}>Lost</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="icon-base ti tabler-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('tenant.library.issues') }}" class="btn btn-outline-secondary">
                        <i class="icon-base ti tabler-x"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Issues Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="icon-base ti tabler-book-upload me-2 text-primary"></i>
                Transactions
            </h5>
            <span class="badge bg-label-primary">{{ $issues->total() }} records</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Issue #</th>
                        <th>Book</th>
                        <th>Member</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Return Date</th>
                        <th class="text-end">Fine</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($issues as $issue)
                        @php
                            $statusColors = \App\Models\BookIssue::statusColors();
                            $isActive     = in_array($issue->status, ['issued', 'overdue']);
                        @endphp
                        <tr class="{{ $issue->status === 'overdue' ? 'table-danger bg-opacity-25' : '' }}">
                            <td>
                                <span class="fw-semibold font-monospace small">{{ $issue->issue_number }}</span>
                            </td>
                            <td>
                                <p class="fw-semibold mb-0 small">{{ Str::limit($issue->book->title, 28) }}</p>
                                <p class="text-muted small mb-0 font-monospace">{{ $issue->book->accession_number }}</p>
                            </td>
                            <td>
                                <p class="fw-semibold mb-0 small">{{ $issue->member->display_name }}</p>
                                <p class="text-muted small mb-0">
                                    <span class="badge bg-label-{{ $issue->member->member_type === 'student' ? 'primary' : 'success' }} me-1">
                                        {{ ucfirst($issue->member->member_type) }}
                                    </span>
                                    {{ $issue->member->member_number }}
                                </p>
                            </td>
                            <td class="small text-muted">{{ $issue->issue_date->format('d M Y') }}</td>
                            <td class="small {{ $issue->isOverdue() ? 'text-danger fw-bold' : 'text-muted' }}">
                                {{ $issue->due_date->format('d M Y') }}
                                @if($issue->isOverdue())
                                    <br><span class="badge bg-label-danger">{{ $issue->overdueDays() }}d late</span>
                                @endif
                            </td>
                            <td class="small text-muted">
                                {{ $issue->return_date?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="text-end small">
                                @if($issue->fine_amount > 0)
                                    <span class="text-danger fw-bold">₹{{ number_format($issue->fine_amount) }}</span>
                                    @if($issue->fine_paid > 0)
                                        <br><span class="text-success small">Paid: ₹{{ number_format($issue->fine_paid) }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-label-{{ $statusColors[$issue->status] ?? 'secondary' }}">
                                    {{ ucfirst($issue->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    @if($isActive)
                                        {{-- Return button --}}
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-success"
                                                data-bs-toggle="modal" data-bs-target="#returnModal{{ $issue->id }}"
                                                title="Return Book">
                                            <i class="icon-base ti tabler-book-download"></i>
                                        </button>
                                        {{-- Mark Lost --}}
                                        <form action="{{ route('tenant.library.issues.lost', $issue) }}" method="POST"
                                              onsubmit="return confirm('Mark this book as LOST? The inventory will be updated and the copy cannot be recovered.')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-icon btn-outline-dark" title="Mark as Lost">
                                                <i class="icon-base ti tabler-alert-octagon"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Return Modal --}}
                        @if($isActive)
                        <div class="modal fade" id="returnModal{{ $issue->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('tenant.library.issues.return', $issue) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="icon-base ti tabler-book-download me-2 text-success"></i>
                                                Return Book
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            {{-- Book & member summary --}}
                                            <div class="alert alert-light border mb-3 py-2 px-3">
                                                <p class="fw-bold mb-1">{{ $issue->book->title }}</p>
                                                <p class="text-muted small mb-1">
                                                    <span class="me-3">ACC: {{ $issue->book->accession_number }}</span>
                                                    <span>Member: {{ $issue->member->display_name }}</span>
                                                </p>
                                                <p class="text-muted small mb-0">
                                                    Issued: {{ $issue->issue_date->format('d M Y') }} &nbsp;|&nbsp;
                                                    Due: <span class="{{ $issue->isOverdue() ? 'text-danger fw-bold' : '' }}">
                                                        {{ $issue->due_date->format('d M Y') }}
                                                    </span>
                                                </p>
                                            </div>

                                            @if($issue->isOverdue())
                                                <div class="alert alert-warning py-2 mb-3">
                                                    <i class="icon-base ti tabler-alert-triangle me-1"></i>
                                                    This book is <strong>{{ $issue->overdueDays() }} days overdue</strong>.
                                                    Fine rate: ₹{{ $issue->fine_per_day }}/day.
                                                    Estimated fine: <strong>₹{{ number_format($issue->calculateFine()) }}</strong>
                                                </div>
                                            @endif

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Return Date <span class="text-danger">*</span></label>
                                                <input type="date" name="return_date" class="form-control return-date-input"
                                                       value="{{ date('Y-m-d') }}"
                                                       data-issue-date="{{ $issue->issue_date->format('Y-m-d') }}"
                                                       data-due-date="{{ $issue->due_date->format('Y-m-d') }}"
                                                       data-fine-rate="{{ $issue->fine_per_day }}"
                                                       min="{{ $issue->issue_date->format('Y-m-d') }}"
                                                       required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Book Condition on Return</label>
                                                <select name="condition_on_return" class="form-select">
                                                    <option value="">Select condition...</option>
                                                    <option value="good">Good — No damage</option>
                                                    <option value="fair">Fair — Minor wear</option>
                                                    <option value="poor">Poor — Significant damage</option>
                                                </select>
                                            </div>
                                            <div class="mb-3 fine-section" style="{{ $issue->isOverdue() ? '' : 'display:none;' }}">
                                                <label class="form-label fw-semibold">Fine Amount Due</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">₹</span>
                                                    <input type="number" class="form-control fine-display" readonly
                                                           value="{{ $issue->calculateFine() }}">
                                                </div>
                                            </div>
                                            <div class="mb-3 fine-paid-section" style="{{ $issue->isOverdue() ? '' : 'display:none;' }}">
                                                <label class="form-label fw-semibold">Fine Collected (₹)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">₹</span>
                                                    <input type="number" step="0.01" name="fine_paid" class="form-control fine-paid-input"
                                                           value="{{ $issue->calculateFine() }}" min="0">
                                                </div>
                                                <div class="form-text">Enter 0 if fine is waived.</div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Notes</label>
                                                <textarea name="notes" class="form-control" rows="2"
                                                          placeholder="Any notes about this return..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="icon-base ti tabler-book-download me-1"></i> Confirm Return
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="icon-base ti tabler-book-upload" style="font-size:2.5rem; color:#ccc;"></i>
                                <p class="text-muted mt-2 mb-0">No issue records found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($issues->hasPages())
            <div class="card-footer">{{ $issues->links() }}</div>
        @endif
    </div>

</div>

{{-- Issue Book Modal --}}
<div class="modal fade" id="issueBookModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('tenant.library.issues.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="icon-base ti tabler-book-upload me-2 text-primary"></i>
                        Issue Book
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Issue Number <span class="text-danger">*</span></label>
                            <input type="text" name="issue_number" class="form-control"
                                   value="{{ old('issue_number', $nextIssueNumber) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fine Rate (₹/day) <span class="text-danger">*</span></label>
                            <input type="number" name="fine_per_day" class="form-control"
                                   value="{{ old('fine_per_day', 1) }}" min="0" max="100" required>
                        </div>

                        {{-- Book search --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Book <span class="text-danger">*</span></label>
                            <input type="text" id="bookSearchInput" class="form-control mb-2"
                                   placeholder="Search by title, accession number, author, ISBN...">
                            <div id="bookSearchResults" class="list-group mb-2" style="display:none;"></div>
                            <input type="hidden" name="book_id" id="selectedBookId" value="{{ old('book_id') }}" required>
                            <div id="selectedBookDisplay" class="alert alert-light border py-2 px-3 small" style="display:none;"></div>
                        </div>

                        {{-- Member search --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Member <span class="text-danger">*</span></label>
                            <input type="text" id="memberSearchInput" class="form-control mb-2"
                                   placeholder="Search by member number, name, admission number...">
                            <div id="memberSearchResults" class="list-group mb-2" style="display:none;"></div>
                            <input type="hidden" name="member_id" id="selectedMemberId" value="{{ old('member_id') }}" required>
                            <div id="selectedMemberDisplay" class="alert alert-light border py-2 px-3 small" style="display:none;"></div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Issue Date <span class="text-danger">*</span></label>
                            <input type="date" name="issue_date" class="form-control"
                                   value="{{ old('issue_date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Due Date <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" class="form-control"
                                   value="{{ old('due_date', date('Y-m-d', strtotime('+14 days'))) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Condition of Book</label>
                            <select name="condition_on_issue" class="form-select">
                                <option value="">Select...</option>
                                <option value="good"  {{ old('condition_on_issue') === 'good'  ? 'selected' : '' }}>Good</option>
                                <option value="fair"  {{ old('condition_on_issue') === 'fair'  ? 'selected' : '' }}>Fair</option>
                                <option value="poor"  {{ old('condition_on_issue') === 'poor'  ? 'selected' : '' }}>Poor</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"
                                      placeholder="Any notes about this issue...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="issueSubmitBtn">
                        <i class="icon-base ti tabler-book-upload me-1"></i> Issue Book
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Dynamic fine calculator in return modals ──────────────────
    document.querySelectorAll('.return-date-input').forEach(function (input) {
        input.addEventListener('change', function () {
            const modal       = this.closest('.modal');
            const dueDate     = new Date(this.dataset.dueDate);
            const returnDate  = new Date(this.value);
            const fineRate    = parseInt(this.dataset.fineRate) || 1;
            const fineSection = modal.querySelector('.fine-section');
            const fineDisplay = modal.querySelector('.fine-display');
            const finePaid    = modal.querySelector('.fine-paid-input');
            const finePaidSec = modal.querySelector('.fine-paid-section');

            const diffMs   = returnDate - dueDate;
            const overdue  = diffMs > 0 ? Math.ceil(diffMs / 86400000) : 0;
            const fine     = overdue * fineRate;

            if (fine > 0) {
                fineDisplay.value   = fine;
                finePaid.value      = fine;
                fineSection.style.display  = '';
                finePaidSec.style.display  = '';
            } else {
                fineDisplay.value   = 0;
                finePaid.value      = 0;
                fineSection.style.display  = 'none';
                finePaidSec.style.display  = 'none';
            }
        });
    });

    // ── Book search (issue modal) ─────────────────────────────────
    let bookTimer;
    const bookInput     = document.getElementById('bookSearchInput');
    const bookResults   = document.getElementById('bookSearchResults');
    const selectedBookId      = document.getElementById('selectedBookId');
    const selectedBookDisplay = document.getElementById('selectedBookDisplay');

    bookInput.addEventListener('input', function () {
        clearTimeout(bookTimer);
        const q = this.value.trim();
        if (q.length < 2) { bookResults.style.display = 'none'; return; }

        bookTimer = setTimeout(async () => {
            const res  = await fetch(`/library/search/books?q=${encodeURIComponent(q)}`);
            const data = await res.json();
            bookResults.innerHTML = '';

            if (!data.results.length) {
                bookResults.innerHTML = '<div class="list-group-item text-muted small">No books found.</div>';
            } else {
                data.results.forEach(b => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'list-group-item list-group-item-action small' + (!b.can_issue ? ' disabled text-muted' : '');
                    btn.innerHTML = `<strong>${b.text}</strong> &nbsp;
                        <span class="badge bg-label-${b.can_issue ? 'success' : 'danger'} ms-1">
                            ${b.is_ref ? 'Reference Only' : `${b.available} available`}
                        </span>`;
                    if (b.can_issue) {
                        btn.addEventListener('click', () => {
                            selectedBookId.value = b.id;
                            selectedBookDisplay.innerHTML = `<strong>${b.text}</strong> — <span class="text-success">${b.available} copies available</span>`;
                            selectedBookDisplay.style.display = '';
                            bookInput.value = '';
                            bookResults.style.display = 'none';
                        });
                    }
                    bookResults.appendChild(btn);
                });
            }
            bookResults.style.display = 'block';
        }, 300);
    });

    // ── Member search (issue modal) ───────────────────────────────
    let memberTimer;
    const memberInput     = document.getElementById('memberSearchInput');
    const memberResults   = document.getElementById('memberSearchResults');
    const selectedMemberId      = document.getElementById('selectedMemberId');
    const selectedMemberDisplay = document.getElementById('selectedMemberDisplay');

    memberInput.addEventListener('input', function () {
        clearTimeout(memberTimer);
        const q = this.value.trim();
        if (q.length < 2) { memberResults.style.display = 'none'; return; }

        memberTimer = setTimeout(async () => {
            const res  = await fetch(`/library/search/members?q=${encodeURIComponent(q)}`);
            const data = await res.json();
            memberResults.innerHTML = '';

            if (!data.results.length) {
                memberResults.innerHTML = '<div class="list-group-item text-muted small">No members found.</div>';
            } else {
                data.results.forEach(m => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'list-group-item list-group-item-action small' + (!m.can_borrow ? ' disabled text-muted' : '');
                    btn.innerHTML = `<strong>${m.text}</strong> &nbsp;
                        <span class="badge bg-label-${m.can_borrow ? 'success' : 'danger'} ms-1">
                            ${m.issued}/${m.max} books
                        </span>`;
                    if (m.can_borrow) {
                        btn.addEventListener('click', () => {
                            selectedMemberId.value = m.id;
                            selectedMemberDisplay.innerHTML = `<strong>${m.text}</strong> — <span class="text-success">${m.issued} of ${m.max} slots used</span>`;
                            selectedMemberDisplay.style.display = '';
                            memberInput.value = '';
                            memberResults.style.display = 'none';
                        });
                    }
                    memberResults.appendChild(btn);
                });
            }
            memberResults.style.display = 'block';
        }, 300);
    });

    // Close dropdowns on outside click
    document.addEventListener('click', function (e) {
        if (!bookResults.contains(e.target) && e.target !== bookInput) {
            bookResults.style.display = 'none';
        }
        if (!memberResults.contains(e.target) && e.target !== memberInput) {
            memberResults.style.display = 'none';
        }
    });

    // Reopen modal on validation error
    @if($errors->any())
        var modal = new bootstrap.Modal(document.getElementById('issueBookModal'));
        modal.show();
    @endif
});
</script>
@endpush

@endsection
