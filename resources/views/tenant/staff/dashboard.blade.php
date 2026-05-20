@extends('layouts.tenant')
@section('title', 'My Dashboard')
@section('content')

@php
  $roleLabel = match($user->role) {
    'teacher'    => 'Teacher',
    'accountant' => 'Accountant',
    'librarian'  => 'Librarian',
    default      => ucfirst($user->role),
  };
  $roleIcon = match($user->role) {
    'teacher'    => 'tabler-chalkboard',
    'accountant' => 'tabler-report-money',
    'librarian'  => 'tabler-books',
    default      => 'tabler-user',
  };
  $gradients = [
    'teacher'    => 'linear-gradient(135deg,#28c76f 0%,#48da89 55%,#a8e6c0 100%)',
    'accountant' => 'linear-gradient(135deg,#ff9f43 0%,#ffbe76 55%,#ffd89b 100%)',
    'librarian'  => 'linear-gradient(135deg,#03c9ec 0%,#72efdd 55%,#a8edea 100%)',
  ];
  $gradient = $gradients[$user->role] ?? 'linear-gradient(135deg,#696cff 0%,#9155fd 100%)';
@endphp

{{-- Hero --}}
<div class="card mb-4" style="background:{{ $gradient }};border:none;border-radius:1rem;overflow:hidden;position:relative;">
  <span style="position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,.08);top:-50px;right:-50px;pointer-events:none;"></span>
  <span style="position:absolute;width:130px;height:130px;border-radius:50%;background:rgba(255,255,255,.05);bottom:-30px;left:30px;pointer-events:none;"></span>
  <div class="card-body py-4 px-4" style="position:relative;z-index:1;">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
      <div>
        <span style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);color:#fff;font-size:.72rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase;padding:.25rem .85rem;border-radius:50rem;margin-bottom:.75rem;">
          <i class="ti {{ $roleIcon }}" style="font-size:.85rem;"></i> {{ $roleLabel }}
        </span>
        <h4 class="mb-1" style="color:#fff;font-weight:700;">Welcome, {{ $user->name }}! 👋</h4>
        <p class="mb-0" style="color:rgba(255,255,255,.85);">{{ $school->school_name }} &mdash; {{ now()->format('l, d F Y') }}</p>
      </div>
      <div class="d-flex gap-2">
        <div style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:.75rem;padding:.6rem 1rem;text-align:center;min-width:80px;">
          <div style="color:rgba(255,255,255,.75);font-size:.7rem;margin-bottom:.2rem;">Academic Year</div>
          <div style="color:#fff;font-weight:700;font-size:.9rem;">{{ $activeAcademicYear?->name ?? '—' }}</div>
        </div>
        <div style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:.75rem;padding:.6rem 1rem;text-align:center;min-width:80px;">
          <div style="color:rgba(255,255,255,.75);font-size:.7rem;margin-bottom:.2rem;">Students</div>
          <div style="color:#fff;font-weight:700;font-size:.9rem;">{{ number_format($totalStudents) }}</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- My Attendance This Month --}}
@php
  $mp = $myAttendanceThisMonth->present ?? 0;
  $ma = $myAttendanceThisMonth->absent ?? 0;
  $ml = $myAttendanceThisMonth->late ?? 0;
  $mt = $myAttendanceThisMonth->total ?? 0;
  $attendancePct = $mt > 0 ? round(($mp / $mt) * 100) : 0;
@endphp
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="card h-100" style="border-radius:1rem;border:none;">
      <div class="card-body d-flex align-items-center gap-3">
        <span class="avatar-initial rounded bg-label-success" style="width:52px;height:52px;display:flex;align-items:center;justify-content:center;border-radius:.85rem!important;font-size:1.4rem;flex-shrink:0;">
          <i class="ti tabler-id-badge text-success"></i>
        </span>
        <div>
          <h2 class="fw-bolder mb-0">{{ $attendancePct }}%</h2>
          <p class="text-muted mb-0 small">My Attendance ({{ now()->format('M Y') }})</p>
          <small class="text-muted">{{ $mp }}P / {{ $ma }}A / {{ $ml }}L of {{ $mt }} days</small>
        </div>
      </div>
    </div>
  </div>

  @if($user->isTeacher() && $teacherStats)
  <div class="col-sm-6 col-xl-3">
    <div class="card h-100" style="border-radius:1rem;border:none;">
      <div class="card-body d-flex align-items-center gap-3">
        <span class="avatar-initial rounded bg-label-primary" style="width:52px;height:52px;display:flex;align-items:center;justify-content:center;border-radius:.85rem!important;font-size:1.4rem;flex-shrink:0;">
          <i class="ti tabler-door text-primary"></i>
        </span>
        <div>
          <h2 class="fw-bolder mb-0">{{ $teacherStats['assignedClasses']->count() }}</h2>
          <p class="text-muted mb-0 small">My Classes</p>
          <small class="text-muted">{{ $teacherStats['assignedSubjects'] }} subject(s) assigned</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card h-100" style="border-radius:1rem;border:none;">
      <div class="card-body d-flex align-items-center gap-3">
        <span class="avatar-initial rounded bg-label-info" style="width:52px;height:52px;display:flex;align-items:center;justify-content:center;border-radius:.85rem!important;font-size:1.4rem;flex-shrink:0;">
          <i class="ti tabler-user-check text-info"></i>
        </span>
        <div>
          <h2 class="fw-bolder mb-0">{{ $teacherStats['todayAttendanceMarked'] }}</h2>
          <p class="text-muted mb-0 small">Attendance Marked Today</p>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card h-100" style="border-radius:1rem;border:none;">
      <div class="card-body d-flex align-items-center gap-3">
        <span class="avatar-initial rounded bg-label-warning" style="width:52px;height:52px;display:flex;align-items:center;justify-content:center;border-radius:.85rem!important;font-size:1.4rem;flex-shrink:0;">
          <i class="ti tabler-clipboard-list text-warning"></i>
        </span>
        <div>
          <h2 class="fw-bolder mb-0">{{ $teacherStats['upcomingExams']->count() }}</h2>
          <p class="text-muted mb-0 small">Exams in Next 7 Days</p>
        </div>
      </div>
    </div>
  </div>
  @endif

  @if($user->isAccountant() && $accountantStats)
  <div class="col-sm-6 col-xl-3">
    <div class="card h-100" style="border-radius:1rem;border:none;">
      <div class="card-body d-flex align-items-center gap-3">
        <span class="avatar-initial rounded bg-label-primary" style="width:52px;height:52px;display:flex;align-items:center;justify-content:center;border-radius:.85rem!important;font-size:1.4rem;flex-shrink:0;">
          <i class="ti tabler-cash text-primary"></i>
        </span>
        <div>
          <h2 class="fw-bolder mb-0">₹{{ number_format($accountantStats['todayFeeCollection']->total ?? 0) }}</h2>
          <p class="text-muted mb-0 small">Collected Today</p>
          <small class="text-muted">{{ $accountantStats['todayFeeCollection']->receipts ?? 0 }} receipts</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card h-100" style="border-radius:1rem;border:none;">
      <div class="card-body d-flex align-items-center gap-3">
        <span class="avatar-initial rounded bg-label-success" style="width:52px;height:52px;display:flex;align-items:center;justify-content:center;border-radius:.85rem!important;font-size:1.4rem;flex-shrink:0;">
          <i class="ti tabler-report-money text-success"></i>
        </span>
        <div>
          <h2 class="fw-bolder mb-0">₹{{ number_format($accountantStats['monthlyFeeCollection']->total ?? 0) }}</h2>
          <p class="text-muted mb-0 small">This Month's Collection</p>
          <small class="text-muted">{{ $accountantStats['monthlyFeeCollection']->receipts ?? 0 }} receipts</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card h-100" style="border-radius:1rem;border:none;">
      <div class="card-body d-flex align-items-center gap-3">
        <span class="avatar-initial rounded bg-label-danger" style="width:52px;height:52px;display:flex;align-items:center;justify-content:center;border-radius:.85rem!important;font-size:1.4rem;flex-shrink:0;">
          <i class="ti tabler-alert-circle text-danger"></i>
        </span>
        <div>
          <h2 class="fw-bolder mb-0">{{ $accountantStats['pendingDemands']->count ?? 0 }}</h2>
          <p class="text-muted mb-0 small">Pending Demands</p>
          <small class="text-muted">₹{{ number_format($accountantStats['pendingDemands']->total ?? 0) }} due</small>
        </div>
      </div>
    </div>
  </div>
  @endif
</div>

<div class="row g-4">

  {{-- Quick Actions --}}
  <div class="col-lg-4">
    <div class="card h-100" style="border-radius:1rem;border:none;">
      <div class="card-header py-3 d-flex align-items-center gap-2">
        <span class="avatar-initial rounded bg-label-warning" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
          <i class="ti tabler-bolt text-warning" style="font-size:1rem;"></i>
        </span>
        <h5 class="mb-0 fw-bold">Quick Actions</h5>
      </div>
      <div class="card-body d-flex flex-column gap-2">
        @php
          $actions = [];
          if($permissions->can_mark_student_attendance)
            $actions[] = ['route'=>'tenant.attendance.students.index','label'=>'Mark Attendance','sub'=>'Student attendance','icon'=>'tabler-user-check','color'=>'primary'];
          if($permissions->can_enter_marks)
            $actions[] = ['route'=>'tenant.results.marks.index','label'=>'Enter Marks','sub'=>'Record exam marks','icon'=>'tabler-pencil','color'=>'success'];
          if($permissions->can_collect_fees)
            $actions[] = ['route'=>'tenant.fees.collections.create','label'=>'Collect Fee','sub'=>'Record a fee payment','icon'=>'tabler-cash','color'=>'warning'];
          if($permissions->can_view_timetable)
            $actions[] = ['route'=>'tenant.timetable.teacher','label'=>'My Timetable','sub'=>'View today\'s schedule','icon'=>'tabler-calendar-time','color'=>'info'];
          if($permissions->can_post_notices || $permissions->can_view_notices)
            $actions[] = ['route'=>'tenant.notices.index','label'=>'Notices','sub'=>'View school notices','icon'=>'tabler-speakerphone','color'=>'secondary'];
          if($permissions->can_view_fee_reports)
            $actions[] = ['route'=>'tenant.fees.collections.index','label'=>'Fee Collections','sub'=>'View all collections','icon'=>'tabler-report-money','color'=>'info'];
        @endphp
        @forelse($actions as $a)
          <a href="{{ route($a['route']) }}" class="d-flex align-items-center gap-3 text-decoration-none p-2 rounded-3"
             style="border:1px solid var(--bs-border-color);transition:all .2s;"
             onmouseenter="this.style.borderColor='var(--bs-{{ $a['color'] }})';this.style.background='var(--bs-{{ $a['color'] }}-bg-subtle)'"
             onmouseleave="this.style.borderColor='var(--bs-border-color)';this.style.background=''">
            <span class="avatar-initial rounded bg-label-{{ $a['color'] }}" style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1.1rem;flex-shrink:0;">
              <i class="ti {{ $a['icon'] }} text-{{ $a['color'] }}"></i>
            </span>
            <div class="flex-grow-1">
              <div class="fw-semibold text-body" style="font-size:.875rem;">{{ $a['label'] }}</div>
              <div class="text-muted" style="font-size:.75rem;">{{ $a['sub'] }}</div>
            </div>
            <i class="ti tabler-chevron-right text-muted"></i>
          </a>
        @empty
          <p class="text-muted small text-center py-3">No quick actions available. Contact your school admin to grant access.</p>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Teacher: Today's Timetable --}}
  @if($user->isTeacher() && $teacherStats)
  <div class="col-lg-4">
    <div class="card h-100" style="border-radius:1rem;border:none;">
      <div class="card-header py-3 d-flex align-items-center gap-2">
        <span class="avatar-initial rounded bg-label-primary" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
          <i class="ti tabler-calendar-time text-primary" style="font-size:1rem;"></i>
        </span>
        <div>
          <h5 class="mb-0 fw-bold">Today's Schedule</h5>
          <p class="text-muted small mb-0">{{ now()->format('l') }}</p>
        </div>
      </div>
      <div class="card-body">
        @forelse($teacherStats['todayTimetable'] as $slot)
          <div class="d-flex align-items-center gap-3 {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
            <div style="min-width:72px;text-align:center;background:var(--bs-primary-bg-subtle);border-radius:.5rem;padding:.35rem .5rem;">
              <div class="fw-bold text-primary" style="font-size:.8rem;">{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i') }}</div>
              <div class="text-muted" style="font-size:.7rem;">{{ \Carbon\Carbon::parse($slot->start_time)->format('A') }}</div>
            </div>
            <div>
              <div class="fw-semibold" style="font-size:.875rem;">{{ $slot->subject_name }}</div>
              <div class="text-muted small">{{ $slot->class_name }}</div>
            </div>
          </div>
        @empty
          <div class="text-center py-4">
            <i class="ti tabler-calendar-off d-block mb-2 text-muted" style="font-size:2.5rem;opacity:.3;"></i>
            <p class="text-muted small mb-0">No classes scheduled today.</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Teacher: Upcoming Exams --}}
  <div class="col-lg-4">
    <div class="card h-100" style="border-radius:1rem;border:none;">
      <div class="card-header py-3 d-flex align-items-center gap-2">
        <span class="avatar-initial rounded bg-label-warning" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
          <i class="ti tabler-clipboard-list text-warning" style="font-size:1rem;"></i>
        </span>
        <div>
          <h5 class="mb-0 fw-bold">Upcoming Exams</h5>
          <p class="text-muted small mb-0">Next 7 days</p>
        </div>
      </div>
      <div class="card-body">
        @forelse($teacherStats['upcomingExams'] as $exam)
          <div class="d-flex align-items-start gap-3 {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
            <span class="avatar-initial rounded bg-label-warning" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:.9rem;flex-shrink:0;">
              <i class="ti tabler-pencil text-warning"></i>
            </span>
            <div>
              <div class="fw-semibold" style="font-size:.875rem;">{{ $exam->subject_name }}</div>
              <div class="text-muted small">{{ $exam->exam_name }}</div>
              <div class="badge bg-label-warning mt-1">{{ \Carbon\Carbon::parse($exam->start_date)->format('d M') }}</div>
            </div>
          </div>
        @empty
          <div class="text-center py-4">
            <i class="ti tabler-circle-check d-block mb-2 text-success" style="font-size:2.5rem;opacity:.5;"></i>
            <p class="text-muted small mb-0">No exams in the next 7 days.</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>
  @endif

  {{-- Accountant: Recent Collections --}}
  @if($user->isAccountant() && $accountantStats)
  <div class="col-lg-8">
    <div class="card h-100" style="border-radius:1rem;border:none;">
      <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
          <span class="avatar-initial rounded bg-label-success" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
            <i class="ti tabler-cash text-success" style="font-size:1rem;"></i>
          </span>
          <div>
            <h5 class="mb-0 fw-bold">Recent Collections</h5>
            <p class="text-muted small mb-0">Last 10 transactions</p>
          </div>
        </div>
        @if($permissions->can_collect_fees)
          <a href="{{ route('tenant.fees.collections.create') }}" class="btn btn-sm btn-primary rounded-pill">
            <i class="ti tabler-plus me-1"></i>Collect Fee
          </a>
        @endif
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th class="small fw-semibold text-muted ps-3">Student</th>
                <th class="small fw-semibold text-muted">Amount</th>
                <th class="small fw-semibold text-muted">Mode</th>
                <th class="small fw-semibold text-muted pe-3">Date</th>
              </tr>
            </thead>
            <tbody>
              @forelse($accountantStats['recentCollections'] as $col)
                <tr>
                  <td class="ps-3">
                    <span class="fw-semibold" style="font-size:.875rem;">{{ $col->student_name }}</span>
                  </td>
                  <td><span class="fw-bold text-success">₹{{ number_format($col->total_amount) }}</span></td>
                  <td><span class="badge bg-label-primary">{{ ucfirst($col->payment_mode ?? '—') }}</span></td>
                  <td class="pe-3 text-muted small">{{ \Carbon\Carbon::parse($col->collection_date)->format('d M Y') }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No collections yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  @endif

  {{-- Recent Notices --}}
  <div class="{{ ($user->isTeacher() || $user->isLibrarian()) ? 'col-lg-4' : ($user->isAccountant() ? 'col-lg-4' : 'col-lg-8') }}">
    <div class="card h-100" style="border-radius:1rem;border:none;">
      <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
          <span class="avatar-initial rounded bg-label-info" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
            <i class="ti tabler-speakerphone text-info" style="font-size:1rem;"></i>
          </span>
          <h5 class="mb-0 fw-bold">Notices</h5>
        </div>
        @if($permissions->can_view_notices)
          <a href="{{ route('tenant.notices.index') }}" class="btn btn-sm btn-outline-info rounded-pill">View All</a>
        @endif
      </div>
      <div class="card-body">
        @forelse($recentNotices as $notice)
          @php $ac = match($notice->visible_to) { 'all'=>'primary','staff'=>'success','students'=>'warning', default=>'secondary' }; @endphp
          <div class="d-flex align-items-start gap-3 {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
            <span class="avatar-initial rounded bg-label-{{ $ac }}" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:.9rem;flex-shrink:0;">
              <i class="ti tabler-bell text-{{ $ac }}"></i>
            </span>
            <div class="flex-grow-1">
              <div class="fw-semibold" style="font-size:.875rem;">{{ $notice->title }}</div>
              <div class="d-flex gap-2 mt-1">
                <span class="badge bg-label-{{ $ac }}">{{ ucfirst($notice->visible_to) }}</span>
                <span class="text-muted small">{{ $notice->published_at ? \Carbon\Carbon::parse($notice->published_at)->diffForHumans() : '—' }}</span>
              </div>
            </div>
          </div>
        @empty
          <div class="text-center py-4">
            <i class="ti tabler-bell-off d-block mb-2 text-muted" style="font-size:2.5rem;opacity:.3;"></i>
            <p class="text-muted small mb-0">No notices published yet.</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>

</div>
@endsection
