@php
    $currentRoute = Route::currentRouteName();
@endphp
<div class="sidebar">
    <div class="sidebar-section">Menu</div>
    <a href="{{ route('dashboard') }}" class="sidebar-link {{ $currentRoute == 'dashboard' || $currentRoute == 'customer.dashboard' ? 'active' : '' }}">
        <i class="fa fa-dashboard me-2 text-success"></i> Dashboard
    </a>
    <a href="{{ route('orders.index') }}" class="sidebar-link {{ $currentRoute == 'orders.index' || $currentRoute == 'customer.orders' ? 'active' : '' }}">
        <i class="fa fa-box me-2 text-primary"></i> My Orders
    </a>
    <a href="{{ route('customer.loyalty') }}" class="sidebar-link {{ $currentRoute == 'customer.loyalty' || $currentRoute == 'loyalty.index' ? 'active' : '' }}">
        <i class="fa fa-star me-2 text-warning"></i> Loyalty Points
    </a>
    <a href="{{ route('customer.profile') }}" class="sidebar-link {{ $currentRoute == 'customer.profile' ? 'active' : '' }}">
        <i class="fa fa-user me-2"></i> Profile
    </a>
    <a href="{{ route('home') }}" class="sidebar-link">
        <i class="fa fa-home me-2"></i> Back to Home
    </a>
</div>
