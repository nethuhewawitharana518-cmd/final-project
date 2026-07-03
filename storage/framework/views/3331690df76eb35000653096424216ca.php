<?php
    $currentRoute = Route::currentRouteName();
?>
<div class="sidebar d-none d-md-block">
    <div class="sidebar-section">Menu</div>
    <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-link <?php echo e($currentRoute == 'dashboard' || $currentRoute == 'customer.dashboard' ? 'active' : ''); ?>">
        <i class="fa fa-dashboard me-2"></i> Dashboard
    </a>
    <a href="<?php echo e(route('orders.index')); ?>" class="sidebar-link <?php echo e($currentRoute == 'orders.index' || $currentRoute == 'customer.orders' ? 'active' : ''); ?>">
        <i class="fa fa-box me-2"></i> My Orders
    </a>
    <a href="<?php echo e(route('customer.loyalty')); ?>" class="sidebar-link <?php echo e($currentRoute == 'customer.loyalty' || $currentRoute == 'loyalty.index' ? 'active' : ''); ?>">
        <i class="fa fa-star me-2"></i> Loyalty Points
    </a>
    <a href="<?php echo e(route('customer.profile')); ?>" class="sidebar-link <?php echo e($currentRoute == 'customer.profile' ? 'active' : ''); ?>">
        <i class="fa fa-user me-2"></i> Profile
    </a>
    <a href="<?php echo e(route('home')); ?>" class="sidebar-link">
        <i class="fa fa-home me-2"></i> Back to Home
    </a>
</div>
<?php /**PATH D:\projectn_dark\resources\views/customer/sidebar.blade.php ENDPATH**/ ?>