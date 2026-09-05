{{-- Shared leave history table partial --}}
{{-- Variables: $leaves (Collection), $showApprover (bool), $showCancelBtn (bool) --}}
@php $showApprover = $showApprover ?? false; $showCancelBtn = $showCancelBtn ?? false; @endphp

<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Leave Type</th>
                <th>Duration</th>
                <th>Days</th>
                <th>Reason</th>
                <th>Applied On</th>
                @if($showApprover) <th>Reviewed By</th> @endif
                <th class="text-center">Status</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leaves as $leave)
                <tr>
                    <td>
                        <span class="badge bg-label-primary">{{ $leave->leaveType?->name }}</span>
                        @if(isset($leave->studentProfile))
                            <br><small class="text-muted">{{ $leave->studentProfile?->full_name }}</small>
                        @endif
                    </td>
                    <td class="small">
                        <strong>{{ $leave->from_date->format('d M Y') }}</strong><br>
                        <span class="text-muted">to {{ $leave->to_date->format('d M Y') }}</span>
                    </td>
                    <td>
                        <span class="fw-bold">{{ $leave->total_days }}</span>
                        <small class="text-muted">day{{ $leave->total_days > 1 ? 's' : '' }}</small>
                    </td>
                    <td class="small text-muted" style="max-width:180px">{{ Str::limit($leave->reason, 60) }}</td>
                    <td class="small text-muted">{{ $leave->created_at->format('d M Y') }}</td>
                    @if($showApprover)
                        <td class="small">
                            @if($leave->reviewer)
                                <span class="text-muted">{{ $leave->reviewer->name }}</span><br>
                                <span class="text-muted" style="font-size:11px">{{ $leave->reviewed_at?->format('d M Y') }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    @endif
                    <td class="text-center">
                        <span class="badge bg-label-{{ $leave->statusColor() }}">
                            <i class="icon-base ti
                                @if($leave->status === 'pending') tabler-clock
                                @elseif($leave->status === 'approved') tabler-check
                                @elseif($leave->status === 'rejected') tabler-x
                                @else tabler-ban
                                @endif
                                me-1" style="font-size:11px"></i>
                            {{ $leave->statusLabel() }}
                        </span>
                        @if($leave->status === 'rejected' && $leave->rejection_reason)
                            <br><small class="text-danger" style="font-size:10px" title="{{ $leave->rejection_reason }}">
                                <i class="icon-base ti tabler-info-circle me-1"></i>{{ Str::limit($leave->rejection_reason, 30) }}
                            </small>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('tenant.leave.show', $leave) }}" class="btn btn-sm btn-icon btn-outline-primary" title="View Details">
                                <i class="icon-base ti tabler-eye"></i>
                            </a>
                            @if($showCancelBtn && $leave->isPending())
                                <form method="POST" action="{{ route('tenant.leave.cancel', $leave) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-icon btn-outline-secondary" title="Cancel Application"
                                        data-swal-confirm data-message="Cancel this leave application?">
                                        <i class="icon-base ti tabler-ban"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $showApprover ? 8 : 7 }}" class="text-center py-5">
                        <div class="py-3">
                            <i class="icon-base ti tabler-calendar-off text-muted mb-3" style="font-size:3rem; display:block"></i>
                            <p class="text-muted fw-semibold mb-1">No leave applications yet</p>
                            <p class="text-muted small mb-3">When you apply for leave, it will appear here.</p>
                            <a href="{{ route('tenant.leave.create') }}" class="btn btn-sm btn-primary">
                                <i class="icon-base ti tabler-plus me-1"></i>Apply for Leave
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
