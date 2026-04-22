@extends('layouts.tenant')
@section('title', 'Dashboard')
@section('content')

{{-- Hero Banner --}}
<div class="card mb-4" style="background:linear-gradient(135deg,#696cff 0%,#9155fd 55%,#a855f7 100%);border:none;border-radius:1rem;overflow:hidden;position:relative;">
  <span style="position:absolute;width:250px;height:250px;border-radius:50%;background:rgba(255,255,255,.07);top:-60px;right:-60px;pointer-events:none;"></span>
  <span style="position:absolute;width:150px;height:150px;border-radius:50%;background:rgba(255,255,255,.05);bottom:-40px;left:40px;pointer-events:none;"></span>
  <div class="card-body py-4 px-4" style="position:relative;z-index:1;">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
      <div>
        <span style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);color:#fff;font-size:.72rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase;padding:.25rem .85rem;border-radius:50rem;margin-bottom:.75rem;">
          <i class="ti tabler-school" style="font-size:.85rem;"></i>
          School Admin
        </span>
        <h4 class="mb-1" style="color:#fff;font-weight:700;">Welcome back, {{ $user->name }}! 👋</h4>
        <p class="mb-0" style="color:rgba(255,255,255,.8);">{{ $school->school_name }} &mdash; {{ now()->format('l, d F Y') }}</p>
      </div>
      <div class="d-flex gap-2">
        <div style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:.75rem;padding:.6rem 1rem;text-align:center;min-width:80px;">
          <div style="color:rgba(255,255,255,.75);font-size:.7rem;margin-bottom:.2rem;">Academic Year</div>
          <div style="color:#fff;font-weight:700;font-size:.9rem;">{{ $activeAcademicYear?->name ?? '—' }}</div>
        </div>
        <div style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:.75rem;padding:.6rem 1rem;text-align:center;min-width:80px;">
          <div style="color:rgba(255,255,255,.75);font-size:.7rem;margin-bottom:.2rem;">Role</div>
          <div style="color:#fff;font-weight:700;font-size:.9rem;">{{ ucfirst(str_replace('_',' ',$user->role)) }}</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Stat Cards Row 1 --}}
<div class="row g-3 mb-4">
  @php
  $cards = [
    ['label'=>'Total Students','value'=>number_format($totalStudents),'sub'=>number_format($activeStudents).' active','badge'=>$recentStudents>0?'+'.$recentStudents.' this month':null,'icon'=>'tabler-users','color'=>'primary','shadow'=>'rgba(105,108,255,.18)'],
    ['label'=>'Total Staff','value'=>number_format($totalStaff),'sub'=>number_format($activeStaff).' active','badge'=>$recentStaff>0?'+'.$recentStaff.' this month':null,'icon'=>'tabler-chalkboard','color'=>'success','shadow'=>'rgba(40,199,111,.18)'],
    ['label'=>'Total Parents','value'=>number_format($totalParents),'sub'=>number_format($activeParents).' active','badge'=>null,'icon'=>'tabler-users-group','color'=>'info','shadow'=>'rgba(3,195,236,.18)'],
    ['label'=>'Monthly Bill','value'=>'₹'.number_format($monthlyBill),'sub'=>number_format($activeStudents).' × ₹'.$school->per_student_rate,'badge'=>$subscription?ucfirst(str_replace('_',' ',$subscription->status)):null,'icon'=>'tabler-currency-rupee','color'=>'warning','shadow'=>'rgba(255,171,0,.18)'],
  ];
  @endphp
  @foreach($cards as $c)
  <div class="col-sm-6 col-xl-3">
    <div class="card h-100" style="border-radius:1rem;border:none;transition:transform .2s,box-shadow .2s;" onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 28px {{ $c['shadow'] }}'" onmouseleave="this.style.transform='';this.style.boxShadow=''">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <span class="avatar-initial rounded bg-label-{{ $c['color'] }}" style="width:52px;height:52px;display:flex;align-items:center;justify-content:center;border-radius:.85rem!important;font-size:1.4rem;">
            <i class="ti {{ $c['icon'] }} text-{{ $c['color'] }}"></i>
          </span>
          <span class="badge bg-label-{{ $c['color'] }} rounded-pill">{{ $c['label'] }}</span>
        </div>
        <h2 class="fw-bolder mb-1">{{ $c['value'] }}</h2>
        <p class="text-muted mb-2 small">{{ $c['sub'] }}</p>
        @if($c['badge'])
          <span class="badge bg-label-{{ $c['color'] }}">{{ $c['badge'] }}</span>
        @endif
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- Stat Cards Row 2: classes, subjects, fee collected, attendance --}}
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="card h-100" style="border-radius:1rem;border:none;">
      <div class="card-body d-flex align-items-center gap-3">
        <span class="avatar-initial rounded bg-label-primary" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;border-radius:.75rem!important;font-size:1.3rem;flex-shrink:0;">
          <i class="ti tabler-door text-primary"></i>
        </span>
        <div>
          <h4 class="fw-bolder mb-0">{{ number_format($totalClasses) }}</h4>
          <p class="text-muted mb-0 small">Total Classes</p>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card h-100" style="border-radius:1rem;border:none;">
      <div class="card-body d-flex align-items-center gap-3">
        <span class="avatar-initial rounded bg-label-success" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;border-radius:.75rem!important;font-size:1.3rem;flex-shrink:0;">
          <i class="ti tabler-book text-success"></i>
        </span>
        <div>
          <h4 class="fw-bolder mb-0">{{ number_format($totalSubjects) }}</h4>
          <p class="text-muted mb-0 small">Subjects Assigned</p>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card h-100" style="border-radius:1rem;border:none;">
      <div class="card-body d-flex align-items-center gap-3">
        <span class="avatar-initial rounded bg-label-warning" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;border-radius:.75rem!important;font-size:1.3rem;flex-shrink:0;">
          <i class="ti tabler-cash text-warning"></i>
        </span>
        <div>
          <h4 class="fw-bolder mb-0">₹{{ number_format($monthlyFeeCollection->total ?? 0) }}</h4>
          <p class="text-muted mb-0 small">Fees This Month <span class="badge bg-label-secondary">{{ $monthlyFeeCollection->receipts ?? 0 }} receipts</span></p>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card h-100" style="border-radius:1rem;border:none;">
      <div class="card-body d-flex align-items-center gap-3">
        <span class="avatar-initial rounded bg-label-info" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;border-radius:.75rem!important;font-size:1.3rem;flex-shrink:0;">
          <i class="ti tabler-user-check text-info"></i>
        </span>
        <div>
          <h4 class="fw-bolder mb-0">{{ $todayStudentAttendance->present ?? 0 }}</h4>
          <p class="text-muted mb-0 small">Students Present Today</p>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Main content --}}
<div class="row g-4">

  {{-- Today's Attendance --}}
  <div class="col-lg-4">
    <div class="card h-100" style="border-radius:1rem;border:none;">
      <div class="card-header py-3 d-flex align-items-center gap-2">
        <span class="avatar-initial rounded bg-label-primary" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
          <i class="ti tabler-user-check text-primary" style="font-size:1rem;"></i>
        </span>
        <div>
          <h5 class="mb-0 fw-bold">Today's Attendance</h5>
          <p class="text-muted small mb-0">{{ now()->format('d M Y') }}</p>
        </div>
      </div>
      <div class="card-body">
        <p class="text-muted small fw-semibold text-uppercase mb-2">Students</p>
        @php
          $sp = $todayStudentAttendance->present ?? 0;
          $sa = $todayStudentAttendance->absent ?? 0;
          $sl = $todayStudentAttendance->late ?? 0;
          $st = $sp + $sa + $sl;
        @endphp
        @if($st > 0)
          <div class="d-flex gap-3 mb-3">
            <div class="text-center"><div class="fw-bolder text-success fs-5">{{ $sp }}</div><div class="small text-muted">Present</div></div>
            <div class="text-center"><div class="fw-bolder text-danger fs-5">{{ $sa }}</div><div class="small text-muted">Absent</div></div>
            <div class="text-center"><div class="fw-bolder text-warning fs-5">{{ $sl }}</div><div class="small text-muted">Late</div></div>
          </div>
          <div class="progress mb-3" style="height:8px;border-radius:50rem;">
            <div class="progress-bar bg-success" style="width:{{ $st>0?round($sp/$st*100):0 }}%;border-radius:50rem;"></div>
            <div class="progress-bar bg-danger" style="width:{{ $st>0?round($sa/$st*100):0 }}%;"></div>
            <div class="progress-bar bg-warning" style="width:{{ $st>0?round($sl/$st*100):0 }}%;border-radius:0 50rem 50rem 0;"></div>
          </div>
        @else
          <p class="text-muted small mb-3">No attendance marked yet today.</p>
        @endif
        <p class="text-muted small fw-semibold text-uppercase mb-2">Staff</p>
        @php
          $sfp = $todayStaffAttendance->present ?? 0;
          $sfa = $todayStaffAttendance->absent ?? 0;
          $sft = $sfp + $sfa + ($todayStaffAttendance->late ?? 0);
        @endphp
        @if($sft > 0)
          <div class="d-flex gap-3">
            <div class="text-center"><div class="fw-bolder text-success fs-5">{{ $sfp }}</div><div class="small text-muted">Present</div></div>
            <div class="text-center"><div class="fw-bolder text-danger fs-5">{{ $sfa }}</div><div class="small text-muted">Absent</div></div>
          </div>
        @else
          <p class="text-muted small mb-0">No staff attendance marked yet.</p>
        @endif
        <div class="mt-3 pt-3 border-top">
          <a href="{{ route('tenant.attendance.students.index') }}" class="btn btn-sm btn-outline-primary w-100">
            <i class="ti tabler-clipboard-list me-1"></i> Mark Attendance
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- Gender Breakdown --}}
  <div class="col-lg-4">
    <div class="card h-100" style="border-radius:1rem;border:none;">
      <div class="card-header py-3 d-flex align-items-center gap-2">
        <span class="avatar-initial rounded bg-label-info" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
          <i class="ti tabler-chart-donut text-info" style="font-size:1rem;"></i>
        </span>
        <div>
          <h5 class="mb-0 fw-bold">Student Gender</h5>
          <p class="text-muted small mb-0">Breakdown by gender</p>
        </div>
      </div>
      <div class="card-body">
        @php $gTotal = array_sum($genderStats); $gColors = ['male'=>'primary','female'=>'danger','other'=>'warning']; @endphp
        @if($gTotal > 0)
          @foreach($genderStats as $gender => $count)
            @php $pct = round(($count/$gTotal)*100); @endphp
            <div class="mb-3">
              <div class="d-flex justify-content-between mb-1">
                <span class="small fw-semibold text-capitalize">{{ $gender }}</span>
                <span class="small text-muted">{{ $count }} ({{ $pct }}%)</span>
              </div>
              <div class="progress" style="height:8px;border-radius:50rem;">
                <div class="progress-bar bg-{{ $gColors[$gender] ?? 'secondary' }}" style="width:{{ $pct }}%;border-radius:50rem;"></div>
              </div>
            </div>
          @endforeach
          <div class="d-flex justify-content-between pt-2 border-top">
            <span class="text-muted small">Total with profile</span>
            <span class="fw-semibold small">{{ $gTotal }}</span>
          </div>
        @else
          <div class="text-center py-4">
            <i class="ti tabler-chart-donut d-block mb-2 text-muted" style="font-size:2.5rem;opacity:.3;"></i>
            <p class="text-muted small mb-0">No student profiles yet.</p>
          </div>
        @endif
      </div>
    </div>
  </div>

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
          if($user->isSchoolAdmin()) {
            $actions = [
              ['route'=>'tenant.students.create','label'=>'Add Student','sub'=>'Register new student','icon'=>'tabler-user-plus','color'=>'primary'],
              ['route'=>'tenant.staff.create','label'=>'Add Staff','sub'=>'Register new staff member','icon'=>'tabler-user-check','color'=>'success'],
              ['route'=>'tenant.fees.collections.create','label'=>'Collect Fee','sub'=>'Record a fee payment','icon'=>'tabler-cash','color'=>'warning'],
              ['route'=>'tenant.notices.index','label'=>'Post Notice','sub'=>'Publish a school notice','icon'=>'tabler-speakerphone','color'=>'info'],
            ];
          } elseif($user->isTeacher()) {
            $actions = [
              ['route'=>'tenant.attendance.students.index','label'=>'Mark Attendance','sub'=>'Student attendance','icon'=>'tabler-clipboard-list','color'=>'primary'],
              ['route'=>'tenant.results.marks.index','label'=>'Enter Marks','sub'=>'Record exam marks','icon'=>'tabler-pencil','color'=>'success'],
            ];
          }
        @endphp
        @foreach($actions as $a)
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
        @endforeach
      </div>
    </div>
  </div>

  {{-- Subscription --}}
  <div class="col-lg-4">
    <div class="card h-100" style="border-radius:1rem;border:none;">
      <div class="card-header py-3 d-flex align-items-center gap-2">
        <span class="avatar-initial rounded bg-label-warning" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
          <i class="ti tabler-receipt text-warning" style="font-size:1rem;"></i>
        </span>
        <div>
          <h5 class="mb-0 fw-bold">Subscription</h5>
          <p class="text-muted small mb-0">Current billing cycle</p>
        </div>
      </div>
      <div class="card-body">
        @if($subscription)
          <div class="mb-3">
            <p class="text-muted small mb-1">Status</p>
            <span class="badge bg-label-{{ $subscription->status === 'active' ? 'success' : ($subscription->status === 'grace_warning' ? 'warning' : 'danger') }} fs-6">
              {{ ucfirst(str_replace('_',' ',$subscription->status)) }}
            </span>
          </div>
          <div class="mb-3">
            <p class="text-muted small mb-1">Billing Cycle</p>
            <p class="fw-semibold mb-0">{{ ucfirst(str_replace('_',' ',$subscription->billing_cycle)) }}</p>
          </div>
          <div class="mb-3">
            <p class="text-muted small mb-1">Period</p>
            <p class="fw-semibold mb-0 small">{{ $subscription->period_start->format('d M Y') }} → {{ $subscription->period_end->format('d M Y') }}</p>
          </div>
          <div class="mb-3">
            <p class="text-muted small mb-1">Amount Due</p>
            <h5 class="fw-bold text-primary mb-0">₹{{ number_format($subscription->amount_due) }}</h5>
          </div>
          @if($subscription->days_overdue > 0)
            <div class="alert alert-danger py-2 mb-0 small"><i class="ti tabler-alert-circle me-1"></i>{{ $subscription->days_overdue }} days overdue. Contact admin.</div>
          @else
            <div class="alert alert-success py-2 mb-0 small"><i class="ti tabler-circle-check me-1"></i>Account is in good standing.</div>
          @endif
        @else
          <p class="text-muted small text-center py-4 mb-0">No active subscription found.</p>
        @endif
      </div>
    </div>
  </div>

  {{-- Recent Notices --}}
  <div class="col-lg-8">
    <div class="card h-100" style="border-radius:1rem;border:none;">
      <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
          <span class="avatar-initial rounded bg-label-info" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:1rem;flex-shrink:0;">
            <i class="ti tabler-speakerphone text-info" style="font-size:1rem;"></i>
          </span>
          <div>
            <h5 class="mb-0 fw-bold">Recent Notices</h5>
            <p class="text-muted small mb-0">Latest published announcements</p>
          </div>
        </div>
        <a href="{{ route('tenant.notices.index') }}" class="btn btn-sm btn-outline-info rounded-pill"><i class="ti tabler-arrow-right me-1"></i>View All</a>
      </div>
      <div class="card-body">
        @forelse($recentNotices as $notice)
          @php
            $audienceColor = match($notice->visible_to) {
              'all' => 'primary', 'parents' => 'info', 'staff' => 'success', 'students' => 'warning', default => 'secondary'
            };
          @endphp
          <div class="d-flex align-items-start gap-3 {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
            <span class="avatar-initial rounded bg-label-{{ $audienceColor }}" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:.6rem!important;font-size:.9rem;flex-shrink:0;">
              <i class="ti tabler-bell text-{{ $audienceColor }}"></i>
            </span>
            <div class="flex-grow-1">
              <div class="fw-semibold" style="font-size:.875rem;">{{ $notice->title }}</div>
              <div class="d-flex gap-2 mt-1">
                <span class="badge bg-label-{{ $audienceColor }}">{{ ucfirst($notice->visible_to) }}</span>
                <span class="text-muted small">{{ $notice->published_at ? \Carbon\Carbon::parse($notice->published_at)->diffForHumans() : '—' }}</span>
              </div>
            </div>
          </div>
        @empty
          <div class="text-center py-5">
            <i class="ti tabler-bell-off d-block mb-2 text-muted" style="font-size:2.5rem;opacity:.3;"></i>
            <p class="text-muted small mb-0">No notices published yet.</p>
            @if($user->isSchoolAdmin())
              <a href="{{ route('tenant.notices.index') }}" class="btn btn-sm btn-outline-primary mt-2">Post First Notice</a>
            @endif
          </div>
        @endforelse
      </div>
    </div>
  </div>

</div>

@endsection