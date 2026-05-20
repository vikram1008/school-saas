@extends('layouts.tenant')

@section('title', 'Book Catalogue')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Book Catalogue / पुस्तक सूची</h4>
            <p class="text-muted mb-0 small">Add and manage books in the library.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tenant.library.dashboard') }}" class="btn btn-outline-secondary">
                <i class="icon-base ti tabler-arrow-left me-1"></i> Dashboard
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookModal">
                <i class="icon-base ti tabler-plus me-1"></i> Add Book
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
            <form method="GET" action="{{ route('tenant.library.books') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search title, author, accession no, ISBN..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="availability" class="form-select">
                        <option value="">All Availability</option>
                        <option value="available" {{ request('availability') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="unavailable" {{ request('availability') === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                        <option value="reference" {{ request('availability') === 'reference' ? 'selected' : '' }}>Reference Only</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="icon-base ti tabler-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('tenant.library.books') }}" class="btn btn-outline-secondary">
                        <i class="icon-base ti tabler-x"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Books Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="icon-base ti tabler-books me-2 text-primary"></i>
                Books
            </h5>
            <span class="badge bg-label-primary">{{ $books->total() }} titles</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Accession No.</th>
                        <th>Title / Author</th>
                        <th>Category</th>
                        <th>Language</th>
                        <th class="text-center">Copies</th>
                        <th class="text-center">Available</th>
                        <th>Rack</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                        <tr>
                            <td>
                                <span class="fw-semibold font-monospace small">{{ $book->accession_number }}</span>
                                @if($book->isbn)
                                    <p class="text-muted small mb-0">ISBN: {{ $book->isbn }}</p>
                                @endif
                            </td>
                            <td>
                                <p class="fw-semibold mb-0">{{ $book->title }}</p>
                                @if($book->title_hi)
                                    <p class="text-muted small mb-0">
                                        <span class="badge bg-label-warning me-1">हिं</span>{{ $book->title_hi }}
                                    </p>
                                @endif
                                @if($book->author)
                                    <p class="text-muted small mb-0"><i class="icon-base ti tabler-user me-1"></i>{{ $book->author }}</p>
                                @endif
                                @if($book->publisher)
                                    <p class="text-muted small mb-0"><i class="icon-base ti tabler-building me-1"></i>{{ $book->publisher }}
                                        @if($book->publication_year) ({{ $book->publication_year }}) @endif
                                    </p>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-label-info">{{ $categories[$book->category] ?? ucfirst($book->category) }}</span>
                            </td>
                            <td class="small text-muted text-capitalize">{{ $book->language }}</td>
                            <td class="text-center fw-bold">{{ $book->total_copies }}</td>
                            <td class="text-center">
                                @if($book->is_reference_only)
                                    <span class="badge bg-label-warning">Ref Only</span>
                                @elseif($book->available_copies > 0)
                                    <span class="badge bg-label-success">{{ $book->available_copies }}</span>
                                @else
                                    <span class="badge bg-label-danger">0</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $book->rack_location ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge bg-label-{{ $book->is_active ? 'success' : 'secondary' }}">
                                    {{ $book->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-warning"
                                            data-bs-toggle="modal" data-bs-target="#editBookModal{{ $book->id }}"
                                            title="Edit">
                                        <i class="icon-base ti tabler-edit"></i>
                                    </button>
                                    @if(!$book->activeIssues()->exists())
                                    <form action="{{ route('tenant.library.books.destroy', $book) }}" method="POST"
                                          onsubmit="return confirm('Delete this book? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Delete">
                                            <i class="icon-base ti tabler-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                        <button class="btn btn-sm btn-icon btn-outline-secondary" disabled title="Book has active issues">
                                            <i class="icon-base ti tabler-lock"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Edit Modal --}}
                        <div class="modal fade" id="editBookModal{{ $book->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('tenant.library.books.update', $book) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Book</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            @include('tenant.library._book_form', ['book' => $book, 'categories' => $categories, 'languages' => $languages])
                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" name="is_active" id="is_active_{{ $book->id }}"
                                                       value="1" {{ $book->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active_{{ $book->id }}">Active in catalogue</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="icon-base ti tabler-books" style="font-size:2.5rem; color:#ccc;"></i>
                                <p class="text-muted mt-2 mb-0">No books found. Add a book to get started.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($books->hasPages())
            <div class="card-footer">{{ $books->links() }}</div>
        @endif
    </div>

</div>

{{-- Add Book Modal --}}
<div class="modal fade" id="addBookModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('tenant.library.books.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="icon-base ti tabler-plus me-2"></i>Add New Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('tenant.library._book_form', ['book' => null, 'categories' => $categories, 'languages' => $languages])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base ti tabler-plus me-1"></i> Add Book
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session()->has('success') && request()->routeIs('tenant.library.books'))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Reopen modal on validation error
    @if($errors->any())
        var modal = new bootstrap.Modal(document.getElementById('addBookModal'));
        modal.show();
    @endif
});
</script>
@endpush
@endif

@endsection
