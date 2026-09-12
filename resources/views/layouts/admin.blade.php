<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - MomasPay Plus')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('styles')
</head>
<body class="admin-body-bg @yield('body_class')">

    <div class="admin-layout">
        <div class="sidebar-backdrop"></div>

        <!-- Admin Sidebar Navigation -->
        <aside class="admin-sidebar">
            <div class="admin-logo-wrapper">
                <svg width="32" height="22" viewBox="0 0 100 65" fill="none">
                    <rect x="15" y="32" width="14" height="24" rx="7" transform="rotate(-35 15 32)" fill="#00A859"/>
                    <rect x="36" y="18" width="14" height="42" rx="7" transform="rotate(-35 36 18)" fill="#00A859"/>
                    <rect x="57" y="4" width="14" height="60" rx="7" transform="rotate(-35 57 4)" fill="#00A859"/>
                </svg>
                <span class="admin-logo-text" style="font-weight: 800; font-size: 1.1rem; color: #00A859; letter-spacing: -0.02em;">MomasPay</span>
            </div>

            <div class="admin-nav-list">
                <a href="{{ route('admin.dashboard') }}" class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><div class="admin-nav-item-left"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg><span class="admin-nav-text">Dashboard</span></div></a>
                <a href="{{ route('admin.estate') }}" class="admin-nav-item {{ request()->routeIs('admin.estate*') ? 'active' : '' }}"><div class="admin-nav-item-left"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11"/></svg><span class="admin-nav-text">Estate</span></div></a>
                <a href="{{ route('admin.transformer') }}" class="admin-nav-item {{ request()->routeIs('admin.transformer*') ? 'active' : '' }}"><div class="admin-nav-item-left"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/></svg><span class="admin-nav-text">Transformer</span></div></a>
                <a href="{{ route('admin.tariff') }}" class="admin-nav-item {{ request()->routeIs('admin.tariff*') ? 'active' : '' }}"><div class="admin-nav-item-left"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg><span class="admin-nav-text">Tariff</span></div></a>
                <a href="{{ route('admin.meter') }}" class="admin-nav-item {{ request()->routeIs('admin.meter*') ? 'active' : '' }}"><div class="admin-nav-item-left"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg><span class="admin-nav-text">Meter</span></div></a>
                <a href="{{ route('admin.customer') }}" class="admin-nav-item {{ request()->routeIs('admin.customer*') ? 'active' : '' }}"><div class="admin-nav-item-left"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg><span class="admin-nav-text">Customer</span></div></a>
                <a href="{{ route('admin.estate-service') }}" class="admin-nav-item {{ request()->routeIs('admin.estate-service*') ? 'active' : '' }}"><div class="admin-nav-item-left"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg><span class="admin-nav-text">Estate Service</span></div></a>
                <a href="{{ route('admin.meter-token') }}" class="admin-nav-item {{ request()->routeIs('admin.meter-token*') ? 'active' : '' }}"><div class="admin-nav-item-left"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 15h0M12 15h0M17 15h0"/></svg><span class="admin-nav-text">Meter Token</span></div></a>
                <a href="{{ route('admin.pos-merchant') }}" class="admin-nav-item {{ request()->routeIs('admin.pos-merchant*') ? 'active' : '' }}"><div class="admin-nav-item-left"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg><span class="admin-nav-text">POS Merchant</span></div></a>
                <a href="{{ route('admin.access-token') }}" class="admin-nav-item {{ request()->routeIs('admin.access-token*') ? 'active' : '' }}"><div class="admin-nav-item-left"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 2l-2 2m-2 2l-2 2m7-2l-2-2m-2 2l-2-2M3 13.64V21h7.36l10-10L13 3.64l-10 10z"/></svg><span class="admin-nav-text">Access Token</span></div></a>
                <a href="{{ route('admin.users') }}" class="admin-nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}"><div class="admin-nav-item-left"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg><span class="admin-nav-text">Users</span></div></a>
                <a href="{{ route('admin.logged-issues') }}" class="admin-nav-item {{ request()->routeIs('admin.logged-issues*') ? 'active' : '' }}"><div class="admin-nav-item-left"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg><span class="admin-nav-text">Logged Issues</span></div></a>
                <a href="{{ route('admin.report') }}" class="admin-nav-item {{ request()->routeIs('admin.report') ? 'active' : '' }}"><div class="admin-nav-item-left"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg><span class="admin-nav-text">Report</span></div></a>
                <a href="{{ route('admin.audits') }}" class="admin-nav-item {{ request()->routeIs('admin.audits') ? 'active' : '' }}"><div class="admin-nav-item-left"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg><span class="admin-nav-text">Audits</span></div></a>
                <a href="{{ route('admin.settings') }}" class="admin-nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}"><div class="admin-nav-item-left"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg><span class="admin-nav-text">Settings</span></div></a>
            </div>
        </aside>

        <!-- Admin Main Content Body -->
        <main class="admin-main">
            <header class="admin-top-bar">
                <div class="admin-greeting">
                    <button type="button" class="admin-sidebar-toggle" id="sidebar-toggle" title="Toggle Navigation Menu">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    </button>
                    <span>Good Morning, Admin</span>
                </div>
                <div class="admin-profile-pill">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80" alt="Admin" class="admin-avatar">
                    <span style="font-size: 0.8rem; font-weight: 600; color: #1E293B;">Admin</span>
                </div>
            </header>

            <div class="admin-body">
                @yield('content')

                <footer class="admin-footer">© 2026 - MOMASPAY</footer>
            </div>
        </main>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
