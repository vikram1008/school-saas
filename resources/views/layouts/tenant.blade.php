@isset($pageConfigs)
  {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset
@php
use Illuminate\Support\Facades\Auth;
$configData = Helper::appClasses();
$customizerHidden = $customizerHidden ?? 'customizer-hide';
$navbarDetached = 'navbar-detached';
$menuFixed = isset($configData['menuFixed']) ? $configData['menuFixed'] : '';
$navbarType = isset($configData['navbarType']) ? $configData['navbarType'] : '';
$footerFixed = isset($configData['footerFixed']) ? $configData['footerFixed'] : '';
$menuCollapsed = isset($configData['menuCollapsed']) ? $configData['menuCollapsed'] : '';
$container = isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact'
  ? 'container-xxl'
  : 'container-fluid';
@endphp

@extends('layouts/commonMaster')

@push('head')
@php $__schoolSettings = \App\Models\SchoolSettings::current(); @endphp
<link rel="icon" type="image/x-icon" href="{{ asset($__schoolSettings->favicon_url) }}" />
@endpush

@section('layoutContent')
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

      {{-- ===================== SIDEBAR ===================== --}}
      <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <div class="app-brand demo">
          <a href="{{ route('tenant.dashboard') }}" class="app-brand-link">
            @include('tenant.partials.school-logo', ['size' => 'sm', 'showName' => true, 'darkMode' => false])
          </a>
          <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="icon-base ti tabler-x"></i>
          </a>
        </div>

        <div class="menu-inner-shadow"></div>

        {{-- Subscription Warning Banner --}}
        @if(isset($subscriptionBanner))
          @php $banner = $subscriptionBanner; @endphp
          <div class="mx-3 mt-3">
            <div class="alert {{ $banner['status'] === 'grace_readonly' ? 'alert-danger' : 'alert-warning' }} p-2 small mb-0">
              <i class="icon-base ti {{ $banner['status'] === 'grace_readonly' ? 'tabler-lock' : 'tabler-alert-triangle' }} me-1"></i>
              @if($banner['status'] === 'grace_readonly')
                <strong>Read-Only Mode</strong><br>{{ $banner['days_overdue'] }} days overdue.
              @else
                <strong>Payment Overdue</strong><br>{{ $banner['days_overdue'] }} days overdue.
              @endif
            </div>
          </div>
        @endif

        <ul class="menu-inner py-1">

          {{-- ── DASHBOARD (always visible) ── --}}
          <li class="menu-item {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}">
            <a href="{{ route('tenant.dashboard') }}" class="menu-link">
              <i class="menu-icon icon-base ti tabler-layout-dashboard"></i>
              <div>Dashboard</div>
            </a>
          </li>

          {{-- ════════════════════════════════ --}}
          {{--   SCHOOL ADMIN MENU             --}}
          {{-- ════════════════════════════════ --}}
          @if(Auth::guard('tenant')->user()?->isSchoolAdmin())

            {{-- ── 1. DAILY OPERATIONS (highest priority) ── --}}
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Daily Operations</span>
            </li>

            {{-- Fee Collection --}}
            <li class="menu-item {{ request()->routeIs('tenant.fees.collections.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.fees.collections.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-cash"></i>
                <div>Fee Collection / शुल्क संग्रह</div>
              </a>
            </li>

            {{-- Student Attendance --}}
            <li class="menu-item {{ request()->routeIs('tenant.attendance.students.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.attendance.students.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-user-check"></i>
                <div>Student Attendance / उपस्थिति</div>
              </a>
            </li>

            {{-- Staff Attendance --}}
            <li class="menu-item {{ request()->routeIs('tenant.attendance.staff.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.attendance.staff.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-id-badge"></i>
                <div>Staff Attendance</div>
              </a>
            </li>

            {{-- Enter Marks --}}
            <li class="menu-item {{ request()->routeIs('tenant.results.marks.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.results.marks.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-pencil"></i>
                <div>Enter Marks / अंक दर्ज</div>
              </a>
            </li>

            {{-- Notices --}}
            <li class="menu-item {{ request()->routeIs('tenant.notices.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.notices.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-speakerphone"></i>
                <div>Notices / सूचनाएं</div>
              </a>
            </li>

            {{-- ── 2. PEOPLE MANAGEMENT ── --}}
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">People / लोग</span>
            </li>

            <li class="menu-item {{ request()->routeIs('tenant.students.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.students.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-users"></i>
                <div>Students / छात्र</div>
              </a>
            </li>

            <li class="menu-item {{ request()->routeIs('tenant.staff.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.staff.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-chalkboard"></i>
                <div>Staff / स्टाफ</div>
              </a>
            </li>

            <li class="menu-item {{ request()->routeIs('tenant.parents.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.parents.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-users-group"></i>
                <div>Parents / अभिभावक</div>
              </a>
            </li>

            {{-- ── 3. TIMETABLE ── --}}
            <li class="menu-item {{ request()->routeIs('tenant.timetable.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.timetable.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-calendar-time"></i>
                <div>Timetable / समय-सारणी</div>
              </a>
            </li>

            {{-- ── LIBRARY ── --}}
            <li class="menu-item {{ request()->routeIs('tenant.library.*') ? 'active open' : '' }}">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-books"></i>
                <div>Library / पुस्तकालय</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('tenant.library.dashboard') ? 'active' : '' }}">
                  <a href="{{ route('tenant.library.dashboard') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-layout-dashboard"></i>
                    <div>Dashboard</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->routeIs('tenant.library.books') ? 'active' : '' }}">
                  <a href="{{ route('tenant.library.books') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-book-2"></i>
                    <div>Book Catalogue</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->routeIs('tenant.library.members') ? 'active' : '' }}">
                  <a href="{{ route('tenant.library.members') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-id-badge-2"></i>
                    <div>Members</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->routeIs('tenant.library.issues') ? 'active' : '' }}">
                  <a href="{{ route('tenant.library.issues') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-book-upload"></i>
                    <div>Issue & Return</div>
                  </a>
                </li>
              </ul>
            </li>

            {{-- ── 4. ATTENDANCE REPORTS ── --}}
            <li class="menu-item {{ request()->routeIs('tenant.attendance.reports.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.attendance.reports.daily') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-chart-bar"></i>
                <div>Attendance Reports</div>
              </a>
            </li>

            {{-- ── 5. RESULTS / EXAMS ── --}}
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Results / परीक्षा</span>
            </li>

            <li class="menu-item {{ request()->routeIs('tenant.results.exams.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.results.exams.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-clipboard-list"></i>
                <div>Exams / परीक्षाएं</div>
              </a>
            </li>

            <li class="menu-item {{ request()->routeIs('tenant.results.report-cards.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.results.report-cards.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-certificate"></i>
                <div>Report Cards</div>
              </a>
            </li>

            <li class="menu-item {{ request()->routeIs('tenant.results.grade-scales.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.results.grade-scales.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-award"></i>
                <div>Grade Scale</div>
              </a>
            </li>

            {{-- ── 6. FINANCE (setup / reports) ── --}}
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Finance / वित्त</span>
            </li>

            <li class="menu-item {{ request()->routeIs('tenant.fees.structures.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.fees.structures.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-file-invoice"></i>
                <div>Fee Structure</div>
              </a>
            </li>

            <li class="menu-item {{ request()->routeIs('tenant.fees.heads.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.fees.heads.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-tags"></i>
                <div>Fee Heads</div>
              </a>
            </li>

            {{-- ── 7. ACADEMIC SETUP ── --}}
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Academic Setup</span>
            </li>

            <li class="menu-item {{ request()->routeIs('tenant.academic-years.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.academic-years.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-calendar"></i>
                <div>Academic Years</div>
              </a>
            </li>

            <li class="menu-item {{ request()->routeIs('tenant.classes.*') || request()->routeIs('tenant.subjects.*') ? 'active open' : '' }}">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-layout-grid"></i>
                <div>Classes & Subjects</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('tenant.classes.index') ? 'active' : '' }}">
                  <a href="{{ route('tenant.classes.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-door"></i>
                    <div>Classes</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->routeIs('tenant.subjects.index') ? 'active' : '' }}">
                  <a href="{{ route('tenant.subjects.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-book"></i>
                    <div>Subjects</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->routeIs('tenant.subjects.assign') ? 'active' : '' }}">
                  <a href="{{ route('tenant.subjects.assign') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-book-upload"></i>
                    <div>Assign Subjects</div>
                  </a>
                </li>
              </ul>
            </li>

            {{-- ── 8. SETTINGS (rarely visited, always last) ── --}}
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Administration</span>
            </li>

            <li class="menu-item {{ request()->routeIs('tenant.staff.permissions.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.staff.permissions.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-shield-check"></i>
                <div>Staff Permissions</div>
              </a>
            </li>

            <li class="menu-item {{ request()->routeIs('tenant.settings.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.settings.school.edit') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-settings"></i>
                <div>School Settings</div>
              </a>
            </li>

          @endif
          {{-- /isSchoolAdmin --}}

          {{-- ════════════════════════════════ --}}
          {{--   STAFF MENU (teacher/accountant/librarian) --}}
          {{-- ════════════════════════════════ --}}
          @if(Auth::guard('tenant')->user()?->isStaff())
            @php $staffUser = Auth::guard('tenant')->user(); $sp = $staffUser->resolvedPermissions(); @endphp

            {{-- ── DAILY OPERATIONS ── --}}
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Daily Operations</span>
            </li>

            @if($sp->can_mark_student_attendance)
            <li class="menu-item {{ request()->routeIs('tenant.attendance.students.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.attendance.students.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-user-check"></i>
                <div>Student Attendance</div>
              </a>
            </li>
            @endif

            @if($sp->can_mark_staff_attendance)
            <li class="menu-item {{ request()->routeIs('tenant.attendance.staff.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.attendance.staff.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-id-badge"></i>
                <div>Staff Attendance</div>
              </a>
            </li>
            @endif

            @if($sp->can_enter_marks)
            <li class="menu-item {{ request()->routeIs('tenant.results.marks.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.results.marks.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-pencil"></i>
                <div>Enter Marks</div>
              </a>
            </li>
            @endif

            @if($sp->can_collect_fees)
            <li class="menu-item {{ request()->routeIs('tenant.fees.collections.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.fees.collections.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-cash"></i>
                <div>Fee Collection</div>
              </a>
            </li>
            @endif

            @if($sp->can_view_notices || $sp->can_post_notices)
            <li class="menu-item {{ request()->routeIs('tenant.notices.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.notices.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-speakerphone"></i>
                <div>Notices</div>
              </a>
            </li>
            @endif

            {{-- ── ACADEMICS ── --}}
            @if($sp->can_view_exams || $sp->can_view_report_cards || $sp->can_view_timetable || $sp->can_view_attendance_reports)
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Academics</span>
            </li>
            @endif

            @if($sp->can_view_timetable)
            <li class="menu-item {{ request()->routeIs('tenant.timetable.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.timetable.teacher') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-calendar-time"></i>
                <div>My Timetable</div>
              </a>
            </li>
            @endif

            @if($sp->can_view_exams)
            <li class="menu-item {{ request()->routeIs('tenant.results.exams.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.results.exams.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-clipboard-list"></i>
                <div>Exams</div>
              </a>
            </li>
            @endif

            @if($sp->can_view_report_cards)
            <li class="menu-item {{ request()->routeIs('tenant.results.report-cards.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.results.report-cards.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-certificate"></i>
                <div>Report Cards</div>
              </a>
            </li>
            @endif

            @if($sp->can_view_attendance_reports)
            <li class="menu-item {{ request()->routeIs('tenant.attendance.reports.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.attendance.reports.daily') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-chart-bar"></i>
                <div>Attendance Reports</div>
              </a>
            </li>
            @endif

            {{-- ── FINANCE ── --}}
            @if($sp->can_view_fee_reports)
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Finance</span>
            </li>
            <li class="menu-item {{ request()->routeIs('tenant.fees.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.fees.collections.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-report-money"></i>
                <div>Fee Reports</div>
              </a>
            </li>
            @endif

            {{-- ── PEOPLE ── --}}
            @if($sp->can_view_students || $sp->can_view_staff || $sp->can_view_parents)
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">People</span>
            </li>
            @if($sp->can_view_students)
            <li class="menu-item {{ request()->routeIs('tenant.students.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.students.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-users"></i>
                <div>Students</div>
              </a>
            </li>
            @endif
            @if($sp->can_view_staff)
            <li class="menu-item {{ request()->routeIs('tenant.staff.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.staff.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-chalkboard"></i>
                <div>Staff</div>
              </a>
            </li>
            @endif
            @if($sp->can_view_parents)
            <li class="menu-item {{ request()->routeIs('tenant.parents.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.parents.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-users-group"></i>
                <div>Parents</div>
              </a>
            </li>
            @endif
            @endif

            {{-- ── LIBRARY (librarian + anyone with permission) ── --}}
            @if($sp->can_manage_library)
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Library / पुस्तकालय</span>
            </li>
            <li class="menu-item {{ request()->routeIs('tenant.library.dashboard') ? 'active' : '' }}">
              <a href="{{ route('tenant.library.dashboard') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-layout-dashboard"></i>
                <div>Library Dashboard</div>
              </a>
            </li>
            <li class="menu-item {{ request()->routeIs('tenant.library.books') ? 'active' : '' }}">
              <a href="{{ route('tenant.library.books') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-books"></i>
                <div>Book Catalogue</div>
              </a>
            </li>
            <li class="menu-item {{ request()->routeIs('tenant.library.members') ? 'active' : '' }}">
              <a href="{{ route('tenant.library.members') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-id-badge-2"></i>
                <div>Members</div>
              </a>
            </li>
            <li class="menu-item {{ request()->routeIs('tenant.library.issues') ? 'active' : '' }}">
              <a href="{{ route('tenant.library.issues') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-book-upload"></i>
                <div>Issue & Return</div>
              </a>
            </li>
            @endif

          @endif
          {{-- /isStaff --}}

          {{-- ════════════════════════════════ --}}
          {{--   PARENT PORTAL MENU            --}}
          {{-- ════════════════════════════════ --}}
          @if(Auth::guard('tenant')->user()?->isParent())

            <li class="menu-item {{ request()->routeIs('tenant.parent-portal.dashboard') ? 'active' : '' }}">
              <a href="{{ route('tenant.parent-portal.dashboard') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-home"></i>
                <div>My Dashboard</div>
              </a>
            </li>

            <li class="menu-item {{ request()->routeIs('tenant.parent-portal.notices') ? 'active' : '' }}">
              <a href="{{ route('tenant.parent-portal.notices') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-speakerphone"></i>
                <div>Notices</div>
              </a>
            </li>

          @endif
          {{-- /isParent --}}

        </ul>
      </aside>
      {{-- /Sidebar --}}

      <div class="layout-page">

        {{-- ===================== NAVBAR ===================== --}}
        <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme">
          <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
              <i class="icon-base ti tabler-menu-2 icon-lg"></i>
            </a>
          </div>

          {{-- Subscription banner (large screens) --}}
          @if(isset($subscriptionBanner))
            @php $banner = $subscriptionBanner; @endphp
            <div class="d-none d-xl-flex align-items-center gap-2 flex-grow-1">
              <div class="alert {{ $banner['status'] === 'grace_readonly' ? 'alert-danger' : 'alert-warning' }} py-1 px-3 mb-0 small">
                <i class="icon-base ti {{ $banner['status'] === 'grace_readonly' ? 'tabler-lock-exclamation' : 'tabler-alert-triangle' }} me-1"></i>
                @if($banner['status'] === 'grace_readonly')
                  <strong>Read-Only Mode:</strong> {{ $banner['days_overdue'] }} days overdue. Amount due: <strong>₹{{ number_format($banner['amount_due']) }}</strong>.
                @else
                  <strong>Payment Overdue:</strong> {{ $banner['days_overdue'] }} days overdue. Amount due: <strong>₹{{ number_format($banner['amount_due']) }}</strong>.
                @endif
                Contact <a href="mailto:{{ $banner['support_email'] }}" class="alert-link">{{ $banner['support_email'] }}</a>
              </div>
            </div>
          @endif

          <div class="navbar-nav-right d-flex align-items-center ms-auto">
            <ul class="navbar-nav flex-row align-items-center ms-auto">

              {{-- User Dropdown --}}
              <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);"
                   data-bs-toggle="dropdown">
                  <div class="avatar avatar-online">
                    <span class="avatar-initial rounded-circle bg-label-primary">
                      {{ strtoupper(substr(Auth::guard('tenant')->user()?->name ?? 'U', 0, 1)) }}
                    </span>
                  </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item mt-0" href="#">
                      <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                          <div class="avatar">
                            <span class="avatar-initial rounded-circle bg-label-primary">
                              {{ strtoupper(substr(Auth::guard('tenant')->user()?->name ?? 'U', 0, 1)) }}
                            </span>
                          </div>
                        </div>
                        <div class="flex-grow-1">
                          <small class="fw-semibold d-block">
                            {{ Auth::guard('tenant')->user()?->name }}
                          </small>
                          <small class="text-muted">
                            {{ ucfirst(str_replace('_', ' ', Auth::guard('tenant')->user()?->role ?? '')) }}
                          </small>
                        </div>
                      </div>
                    </a>
                  </li>
                  <li><div class="dropdown-divider my-1"></div></li>
                  <li>
                    <form method="POST" action="{{ route('tenant.logout') }}">
                      @csrf
                      <button type="submit" class="dropdown-item text-danger">
                        <i class="icon-base ti tabler-logout me-1"></i> Logout
                      </button>
                    </form>
                  </li>
                </ul>
              </li>

            </ul>
          </div>
        </nav>
        {{-- /Navbar --}}

        {{-- Content Wrapper --}}
        <div class="content-wrapper">
          <div class="{{ $container }} flex-grow-1 container-p-y">
            @yield('content')
          </div>
          <div class="content-backdrop fade"></div>
        </div>
      </div>

    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
  </div>
@endsection

@push('scripts')
  @vite(['resources/assets/js/hindi-autofill.js'])
  @vite(['resources/assets/js/swal-confirms.js'])
@endpush