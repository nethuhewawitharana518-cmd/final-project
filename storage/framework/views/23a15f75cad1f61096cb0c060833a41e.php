<?php
    $currentRoute = Route::currentRouteName();
?>
<div class="sidebar">
    <div class="sidebar-section">Business Panel</div>
    <a href="<?php echo e(route('business.dashboard')); ?>" class="sidebar-link <?php echo e($currentRoute == 'business.dashboard' ? 'active' : ''); ?>">
        <i class="fa fa-store me-2"></i> Dashboard
    </a>
    <a href="<?php echo e(route('business.food.index')); ?>" class="sidebar-link <?php echo e(str_contains($currentRoute, 'food') && $currentRoute != 'business.food.create' ? 'active' : ''); ?>">
        <i class="fa fa-utensils me-2"></i> Manage Food
    </a>
    <a href="<?php echo e(route('business.food.create')); ?>" class="sidebar-link <?php echo e($currentRoute == 'business.food.create' || $currentRoute == 'business.food.add' ? 'active' : ''); ?>">
        <i class="fa fa-plus-circle me-2"></i> Add Food
    </a>
    <a href="<?php echo e(route('business.reservations')); ?>" class="sidebar-link <?php echo e(str_contains($currentRoute, 'reservations') ? 'active' : ''); ?>">
        <i class="fa fa-receipt me-2"></i> Reservations
    </a>
    <a href="<?php echo e(route('business.scanner')); ?>" class="sidebar-link <?php echo e(str_contains($currentRoute, 'scanner') ? 'active' : ''); ?>">
        <i class="fa fa-qrcode me-2"></i> QR Scanner
    </a>
    <a href="<?php echo e(route('business.earnings')); ?>" class="sidebar-link <?php echo e(str_contains($currentRoute, 'earnings') ? 'active' : ''); ?>">
        <i class="fa fa-chart-line me-2"></i> Earnings
    </a>
    <a href="<?php echo e(route('business.analytics')); ?>" class="sidebar-link <?php echo e(str_contains($currentRoute, 'analytics') ? 'active' : ''); ?>">
        <i class="fa fa-brain me-2"></i> AI Analytics
    </a>
    <a href="<?php echo e(route('business.subscription')); ?>" class="sidebar-link <?php echo e(str_contains($currentRoute, 'subscription') ? 'active' : ''); ?>">
        <i class="fa fa-credit-card me-2"></i> Subscription
    </a>
    <a href="<?php echo e(route('business.profile')); ?>" class="sidebar-link <?php echo e(str_contains($currentRoute, 'profile') ? 'active' : ''); ?>">
        <i class="fa fa-gears me-2"></i> Business Profile
    </a>
    <a href="<?php echo e(route('home')); ?>" class="sidebar-link">
        <i class="fa fa-home me-2"></i> Back to Home
    </a>
</div>
<?php /**PATH D:\projectn_dark\resources\views/business/sidebar.blade.php ENDPATH**/ ?>