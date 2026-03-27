<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bold ms-2">SaaS Master</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item {{ request()->is('superadmin/dashboard*') ? 'active' : '' }}">
            <a href="{{ url('/superadmin/dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Tenant Management</span>
        </li>

        <li class="menu-item {{ request()->is('superadmin/schools*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-buildings"></i>
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

        <li class="menu-item">
            <a href="#" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div data-i18n="Settings">Global Settings</div>
            </a>
        </li>
    </ul>
</aside>