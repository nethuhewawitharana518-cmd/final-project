@php
    $currentRoute = Route::currentRouteName();
@endphp
<div class="sidebar d-none d-md-block">
    <div class="sidebar-section">Admin Portal</div>
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ $currentRoute == 'admin.dashboard' ? 'active' : '' }}">
        <i class="fa fa-shield-alt me-2 text-danger"></i> Dashboard
    </a>
    <a href="{{ route('admin.businesses') }}" class="sidebar-link {{ str_contains($currentRoute, 'businesses') ? 'active' : '' }}">
        <i class="fa fa-store me-2 text-success"></i> Businesses
    </a>
    <a href="{{ route('admin.subscriptions') }}" class="sidebar-link {{ str_contains($currentRoute, 'subscriptions') ? 'active' : '' }}">
        <i class="fa fa-credit-card me-2 text-primary"></i> Subscriptions
    </a>
    <a href="{{ route('admin.users') }}" class="sidebar-link {{ str_contains($currentRoute, 'users') ? 'active' : '' }}">
        <i class="fa fa-users me-2 text-info"></i> User Management
    </a>
    <a href="{{ route('admin.revenue') }}" class="sidebar-link {{ str_contains($currentRoute, 'revenue') ? 'active' : '' }}">
        <i class="fa fa-chart-line me-2 text-warning"></i> Revenue
    </a>
    <a href="{{ route('admin.commissions') }}" class="sidebar-link {{ str_contains($currentRoute, 'commissions') ? 'active' : '' }}">
        <i class="fa fa-percentage me-2 text-secondary"></i> Commissions
    </a>
    <a href="{{ route('admin.payments.index') }}" class="sidebar-link {{ str_contains($currentRoute, 'payments') ? 'active' : '' }}">
        <i class="fa fa-hand-holding-usd me-2 text-success"></i> Payment Ledger
    </a>
    <a href="{{ route('admin.reports') }}" class="sidebar-link {{ str_contains($currentRoute, 'reports') ? 'active' : '' }}">
        <i class="fa fa-file-invoice me-2"></i> Reports
    </a>
    <a href="{{ route('admin.settings') }}" class="sidebar-link {{ str_contains($currentRoute, 'settings') ? 'active' : '' }}">
        <i class="fa fa-cog me-2"></i> Settings
    </a>
    <a href="{{ route('home') }}" class="sidebar-link">
        <i class="fa fa-home me-2"></i> Back to Home
    </a>
</div>
