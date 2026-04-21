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

      {{-- Sidebar --}}
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

        {{-- Subscription Banner (Sidebar) --}}
        @if(isset($subscriptionBanner))
          @php $banner = $subscriptionBanner; @endphp
          <div class="mx-3 mt-3">
            <div class="alert {{ $banner['status'] === 'grace_readonly' ? 'alert-danger' : 'alert-warning' }} p-2 small mb-0">
              <i class="icon-base ti {{ $banner['status'] === 'grace_readonly' ? 'tabler-lock' : 'tabler-alert-triangle' }} me-1"></i>
              @if($banner['status'] === 'grace_readonly')
                <strong>Read-Only Mode</strong><br>
                {{ $banner['days_overdue'] }} days overdue.
              @else
                <strong>Payment Overdue</strong><br>
                {{ $banner['days_overdue'] }} days overdue.
              @endif
            </div>
          </div>
        @endif

        <ul class="menu-inner py-1">

          {{-- Dashboard --}}
          <li class="menu-item {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}">
            <a href="{{ route('tenant.dashboard') }}" class="menu-link">
              <i class="menu-icon icon-base ti tabler-layout-dashboard"></i>
              <div>Dashboard</div>
            </a>
          </li>
          @if(Auth::guard('tenant')->user()?->isSchoolAdmin())
          <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Academic</span>
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
          @endif

          @if(Auth::guard('tenant')->user()?->isSchoolAdmin())
            {{-- School Management --}}
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">School Management</span>
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
            <li class="menu-item">
              <a href="#" class="menu-link">
                <i class="menu-icon icon-base ti tabler-users-group"></i>
                <div>Parents</div>
              </a>
            </li>
          @endif

          {{-- Parents & Notices (admin) --}}
          @if(Auth::guard('tenant')->user()?->isSchoolAdmin())
            <li class="menu-item {{ request()->routeIs('tenant.parents.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.parents.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-users-group"></i>
                <div>Parents / अभिभावक</div>
              </a>
            </li>
            <li class="menu-item {{ request()->routeIs('tenant.notices.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.notices.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-speakerphone"></i>
                <div>Notices / सूचनाएं</div>
              </a>
            </li>
          @endif

          {{-- Parent Portal --}}
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

          @if(Auth::guard('tenant')->user()?->isSchoolAdmin())
          {{-- Fee Management --}}
          <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Finance / वित्त</span>
          </li>
          <li class="menu-item {{ request()->routeIs('tenant.fees.collections.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.fees.collections.index') }}" class="menu-link">
                  <i class="menu-icon icon-base ti tabler-cash"></i>
                  <div>Fee Collection</div>
              </a>
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
          @endif

          <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Attendance / उपस्थिति</span>
          </li>
          <li class="menu-item {{ request()->routeIs('tenant.attendance.students.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.attendance.students.index') }}" class="menu-link">
                  <i class="menu-icon icon-base ti tabler-user-check"></i>
                  <div>Student Attendance</div>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('tenant.attendance.staff.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.attendance.staff.index') }}" class="menu-link">
                  <i class="menu-icon icon-base ti tabler-id-badge"></i>
                  <div>Staff Attendance</div>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('tenant.attendance.reports.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.attendance.reports.daily') }}" class="menu-link">
                  <i class="menu-icon icon-base ti tabler-chart-bar"></i>
                  <div>Attendance Reports</div>
              </a>
          </li>

          <li class="menu-item {{ request()->routeIs('tenant.timetable.*') ? 'active' : '' }}">
            <a href="{{ route('tenant.timetable.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-calendar-time"></i>
                <div>Timetable / समय-सारणी</div>
            </a>
          </li>
          <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Results / परीक्षा</span>
          </li>
          <li class="menu-item {{ request()->routeIs('tenant.results.exams.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.results.exams.index') }}" class="menu-link">
                  <i class="menu-icon icon-base ti tabler-clipboard-list"></i>
                  <div>Exams / परीक्षाएं</div>
              </a>
          </li>
          <li class="menu-item {{ request()->routeIs('tenant.results.marks.*') ? 'active' : '' }}">
              <a href="{{ route('tenant.results.marks.index') }}" class="menu-link">
                  <i class="menu-icon icon-base ti tabler-pencil"></i>
                  <div>Enter Marks / अंक दर्ज</div>
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

          <li class="menu-item {{ request()->routeIs('tenant.settings.*') ? 'active' : '' }}">
            <a href="{{ route('tenant.settings.school.edit') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-settings"></i>
                <div>School Settings</div>
            </a>
          </li>

        </ul>
      </aside>
      {{-- / Sidebar --}}

      <div class="layout-page">

        {{-- Navbar --}}
        <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme">
          <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
              <i class="icon-base ti tabler-menu-2 icon-lg"></i>
            </a>
          </div>

          {{-- Top Banner for larger screens --}}
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
        {{-- / Navbar --}}

        {{-- Content Wrapper --}}
        <div class="content-wrapper">
          <div class="{{ $container }} flex-grow-1 container-p-y">
            @yield('content')
          </div>
          <div class="content-backdrop fade"></div>
        </div>
        {{-- / Content Wrapper --}}

      </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
  </div>
@endsection

@push('scripts')
  <!-- Include Scripts -->
  @vite(['resources/assets/js/hindi-autofill.js'])
  @vite(['resources/assets/js/swal-confirms.js'])
@endpush