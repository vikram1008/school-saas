@php
use Illuminate\Support\Facades\Route;
$configData = Helper::appClasses();
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

  <div class="menu-inner-shadow"></div>
<ul class="menu-inner py-1">
  <li class="menu-item {{ request()->is('superadmin/dashboard*') ? 'active' : '' }}">
    <a href="{{ url('/superadmin/dashboard') }}" class="menu-link">
      <i class="menu-icon icon-base ti tabler-smart-home"></i>
      <div data-i18n="Dashboard">Dashboard</div>
    </a>
  </li>

  <li class="menu-header small text-uppercase">
    <span class="menu-header-text">Tenant Management</span>
  </li>

  <li class="menu-item {{ request()->is('superadmin/schools*') ? 'active open' : '' }}">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
      <i class="menu-icon icon-base ti tabler-school"></i>
      <div data-i18n="Schools">Schools (Tenants)</div>
    </a>
    <ul class="menu-sub">
      <li class="menu-item {{ request()->is('superadmin/schools') ? 'active' : '' }}">
        <a href="{{ url('/superadmin/schools') }}" class="menu-link">
          <div data-i18n="All Schools">All Schools</div>
        </a>
      </li>
      <li class="menu-item {{ request()->is('superadmin/schools/create') ? 'active' : '' }}">
        <a href="{{ url('/superadmin/schools/create') }}" class="menu-link">
          <div data-i18n="Add New">Register New</div>
        </a>
      </li>
    </ul>
  </li>

  <li class="menu-item {{ request()->routeIs('superadmin.subscriptions.*') ? 'active' : '' }}">
    <a href="{{ route('superadmin.subscriptions.index') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-receipt"></i>
        <div>Subscriptions</div>
    </a>
  </li>

  <li class="menu-item">
    <a href="#" class="menu-link">
      <i class="menu-icon icon-base ti tabler-settings"></i>
      <div data-i18n="Settings">Global Settings</div>
    </a>
  </li>

  <li class="menu-item {{ request()->routeIs('superadmin.schools.logos') ? 'active' : '' }}">
    <a href="{{ route('superadmin.schools.logos') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-photo-up"></i>
        <div>School Logos</div>
    </a>
  </li>
</ul>

</aside>