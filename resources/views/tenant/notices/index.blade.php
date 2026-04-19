@extends('layouts.tenant')

@section('title', 'Notices')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Notices / सूचनाएं</h4>
            <p class="text-muted mb-0 small">Manage school announcements and notices.</p>
        </div>
        <button type="button" class="btn btn-primary"
                data-bs-toggle="modal" data-bs-target="#addNoticeModal">
            <i class="icon-base ti tabler-plus me-1"></i> Add Notice
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-4">
            <i class="icon-base ti tabler-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Title / शीर्षक</th>
                        <th>Visible To</th>
                        <th>Published</th>
                        <th>Expires</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notices as $notice)
                        <tr>
                            <td>
                                <p class="fw-semibold mb-0">{{ $notice->title }}</p>
                                @if($notice->title_hi)
                                    <p class="text-muted small mb-0">
                                        <span class="badge bg-label-warning me-1">हिं</span>
                                        {{ $notice->title_hi }}
                                    </p>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-label-info">
                                    {{ \App\Models\Notice::visibleToLabels()[$notice->visible_to] ?? $notice->visible_to }}
                                </span>
                            </td>
                            <td class="small text-muted">
                                {{ $notice->published_at?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="small text-muted">
                                {{ $notice->expires_at?->format('d M Y') ?? 'No expiry' }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-label-{{ $notice->is_published ? 'success' : 'secondary' }}">
                                    {{ $notice->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button"
                                            class="btn btn-sm btn-icon btn-outline-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editNoticeModal{{ $notice->id }}">
                                        <i class="icon-base ti tabler-edit"></i>
                                    </button>
                                    <form action="{{ route('tenant.notices.destroy', $notice) }}"
                                          method="POST"
                                          onsubmit="return confirm('Delete this notice?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-icon btn-outline-danger">
                                            <i class="icon-base ti tabler-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Edit Modal --}}
                        <div class="modal fade" id="editNoticeModal{{ $notice->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('tenant.notices.update', $notice) }}"
                                          method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Notice</h5>
                                            <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            @include('tenant.notices._form', ['notice' => $notice])
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="icon-base ti tabler-speakerphone"
                                   style="font-size:2.5rem; color:#ccc;"></i>
                                <p class="text-muted mt-2 mb-0">No notices yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($notices->hasPages())
            <div class="card-footer">{{ $notices->links() }}</div>
        @endif
    </div>
</div>

{{-- Add Notice Modal --}}
<div class="modal fade" id="addNoticeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('tenant.notices.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="icon-base ti tabler-speakerphone me-2"></i>Add Notice
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('tenant.notices._form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base ti tabler-plus me-1"></i> Create Notice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Add flatpickr + vendor assets if not already loaded
    document.querySelectorAll('.modal').forEach(function (modal) {
        modal.addEventListener('shown.bs.modal', function () {

            // ── Flatpickr for expiry date ────────────────────────────
            this.querySelectorAll('.notice-expiry').forEach(function (el) {
                // Destroy existing instance first — ensures edit modal
                // always shows the correct pre-filled date
                if (el._flatpickr) {
                    el._flatpickr.destroy();
                }

                var defaultVal = el.dataset.default || el.value || null;

                flatpickr(el, {
                    dateFormat:  'Y-m-d',
                    altInput:    true,
                    altFormat:   'd M Y',
                    minDate:     'today',
                    allowInput:  false,
                    defaultDate: defaultVal || null,
                    appendTo: modal, // Ensures calendar appears within modal
                });
            });

            // ── Hindi autofill re-init for modal fields ──────────────
            if (window.initHindiAutofill) {
                // Reset init flags so fields get re-bound
                this.querySelectorAll('[data-hindi-init]').forEach(function (el) {
                    delete el.dataset.hindiInit;
                });
                window.initHindiAutofill();
            }
        });
    });

    // ── Clear Add Notice modal on open ───────────────────────────────
    var addModal = document.getElementById('addNoticeModal');
    if (addModal) {
        addModal.addEventListener('show.bs.modal', function () {
            // Clear all text inputs and textareas
            this.querySelectorAll('input[type=text], textarea').forEach(function (el) {
                el.value = '';
            });
            // Reset select to default
            var visibleTo = this.querySelector('[name="visible_to"]');
            if (visibleTo) visibleTo.value = 'all';
            // Uncheck publish
            var isPublished = this.querySelector('[name="is_published"]');
            if (isPublished) isPublished.checked = false;
            // Clear flatpickr if already initialized
            var expiryInput = this.querySelector('.notice-expiry');
            if (expiryInput?._flatpickr) {
                expiryInput._flatpickr.clear();
            }
            // Clear data-default so flatpickr opens empty
            if (expiryInput) expiryInput.dataset.default = '';
        });
    }

});
</script>
@endpush

@endsection