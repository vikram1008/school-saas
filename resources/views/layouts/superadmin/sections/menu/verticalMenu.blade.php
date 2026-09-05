@php
use Illuminate\Support\Facades\Route;
use App\Models\Tenant;
$configData = Helper::appClasses();

// Badge counts for sidebar
$atRiskCount = 0;
try {
    $atRiskCount = \App\Models\Subscription::whereIn('status', ['grace_warning', 'grace_readonly', 'suspended'])
        ->count();
} catch (\Exception) {}
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu" @foreach ($configData['menuAttributes'] as $attribute =>
  $value)
  {{ $attribute }}="{{ $value }}" @endforeach>

  <!-- ! Hide app brand if navbar-full -->
  @if (!isset($navbarFull))
  <div class="app-brand demo">
    <a href="{{ url('/') }}" class="app-brand-link">
      <span class="app-brand-logo demo">@include('_partials.macros')</span>
      <span class="app-brand-text demo menu-text fw-bold ms-3">{{ config('variables.templateName') }}</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
      <i class="icon-base ti tabler-x d-block d-xl-none"></i>
    </a>
  </div>
  @endif

  {{-- Super Admin Role Chip --}}
  <div class="px-3 pb-2 pt-1">
    <div style="display:inline-flex; align-items:center; gap:.4rem; background:linear-gradient(135deg,rgba(105,108,255,.15),rgba(145,85,253,.15)); border:1px solid rgba(105,108,255,.3); color:#696cff; font-size:.68rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase; padding:.3rem .9rem; border-radius:50rem; width:100%; justify-content:center;">
      <i class="ti tabler-shield-check" style="font-size:.8rem;"></i>
      Super Administrator
    </div>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">

    {{-- ── MAIN ──────────────────────────────────────────── --}}
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Main</span>
    </li>

    <li class="menu-item {{ request()->is('superadmin/dashboard*') ? 'active' : '' }}">
      <a href="{{ url('/superadmin/dashboard') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-smart-home"></i>
        <div data-i18n="Dashboard">Dashboard</div>
      </a>
    </li>

    {{-- ── TENANT MANAGEMENT ────────────────────────────── --}}
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Tenant Management</span>
    </li>

    <li class="menu-item {{ request()->is('superadmin/schools*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-school"></i>
        <div data-i18n="Schools">Schools (Tenants)</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('superadmin.schools.index') ? 'active' : '' }}">
          <a href="{{ route('superadmin.schools.index') }}" class="menu-link">
            <div data-i18n="All Schools">All Schools</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('superadmin.schools.create') ? 'active' : '' }}">
          <a href="{{ route('superadmin.schools.create') }}" class="menu-link">
            <div data-i18n="Register New">Register New</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('superadmin.schools.logos') ? 'active' : '' }}">
          <a href="{{ route('superadmin.schools.logos') }}" class="menu-link">
            <div data-i18n="School Logos">School Logos</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- ── BILLING & SUBSCRIPTIONS ──────────────────────── --}}
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Billing</span>
    </li>

    <li class="menu-item {{ request()->routeIs('superadmin.subscriptions.*') ? 'active' : '' }}">
      <a href="{{ route('superadmin.subscriptions.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-receipt"></i>
        <div data-i18n="Subscriptions">Subscriptions</div>
        @if($atRiskCount > 0)
          <span class="badge bg-danger rounded-pill ms-auto" style="font-size:.65rem; padding:.2em .5em;">{{ $atRiskCount }}</span>
        @endif
      </a>
    </li>

    {{-- ── CONFIGURATION ────────────────────────────────── --}}
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Configuration</span>
    </li>

    <li class="menu-item {{ request()->routeIs('superadmin.settings.*') ? 'active' : '' }}">
      <a href="{{ route('superadmin.settings.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-settings"></i>
        <div data-i18n="Settings">Global Settings</div>
      </a>
    </li>

  </ul>

</aside>