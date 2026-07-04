<?php $__env->startSection('title', 'Business Subscription'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex">
    <?php echo $__env->make('business.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Manage Subscriptions</h4>
            <span class="badge bg-success status-badge active">Plan Settings</span>
        </div>

        <div class="content-area text-start">
            
            <div class="card border-0 shadow-sm bg-white p-4 rounded-3 mb-5">
                <h5 class="fw-bold mb-3 text-dark">Your Active Subscription</h5>
                <hr>
                <?php if($activeSubscription): ?>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold d-block">Tier Plan</label>
                            <span class="badge bg-success fs-6 mt-1"><?php echo e(ucfirst($activeSubscription->plan_type)); ?></span>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold d-block">Monthly Price</label>
                            <span class="text-dark fw-bold fs-6">Rs. <?php echo e(number_format($activeSubscription->price)); ?></span>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold d-block">Upload Limit</label>
                            <span class="text-dark fw-bold fs-6">
                                <?php echo e($activeSubscription->upload_limit === -1 ? 'Unlimited' : $activeSubscription->upload_limit); ?> uploads/mo
                            </span>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold d-block">Start Date</label>
                            <span class="text-dark"><?php echo e($activeSubscription->start_date->format('M d, Y')); ?></span>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold d-block">Renewal/End Date</label>
                            <span class="text-dark fw-semibold"><?php echo e($activeSubscription->end_date->format('M d, Y')); ?></span>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold d-block">Status</label>
                            <span class="status-badge active mt-1">Active & Valid</span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">
                        <i class="fa fa-triangle-exclamation me-2"></i>You do not have any active subscription plan. Please select a plan below to start listing food.
                    </div>
                <?php endif; ?>
            </div>

            
            <h5 class="fw-bold text-dark mb-4">Choose Your Subscription Plan</h5>
            <div class="row g-4 mb-5">
                <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4">
                    <div class="plan-card h-100 p-4 <?php echo e(($activeSubscription && $activeSubscription->plan_type === $type) ? 'popular' : ''); ?>">
                        <h4 class="fw-bold text-dark mb-1"><?php echo e($plan['name']); ?></h4>
                        <div class="plan-price-main mb-3">Rs. <?php echo e(number_format($plan['price'])); ?><span class="fs-6 text-muted">/mo</span></div>
                        <ul class="plan-feature-list text-start">
                            <?php if($type === 'starter'): ?>
                                <li>50 food uploads / month</li>
                                <li>AI expiry risk prediction indicators</li>
                                <li>Real-time dashboard analytics</li>
                                <li>QR verified collections system</li>
                            <?php elseif($type === 'professional'): ?>
                                <li>Max 250 food uploads / month</li>
                                <li>AI expiry risk prediction indicators</li>
                                <li>Real-time dashboard analytics</li>
                                <li>QR verified collections system</li>
                            <?php elseif($type === 'enterprise'): ?>
                                <li>Unlimited food uploads / month</li>
                                <li>Advanced AI Analytics & custom reports</li>
                                <li>24/7 Priority merchant support</li>
                                <li>Featured placement on the customer homepage banner</li>
                            <?php endif; ?>
                        </ul>
                        <a href="<?php echo e(route('business.payment', ['plan' => $type])); ?>" class="btn btn-success w-100 py-2 mt-4 rounded-pill">
                            <?php echo e(($activeSubscription && $activeSubscription->plan_type === $type) ? 'Renew Plan' : 'Select Plan'); ?>

                        </a>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0 text-dark">Billing History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Plan Name</th>
                                    <th>Price</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?php echo e(ucfirst($item->plan_type)); ?></td>
                                    <td>Rs. <?php echo e(number_format($item->price)); ?></td>
                                    <td><?php echo e($item->start_date->format('M d, Y')); ?></td>
                                    <td><?php echo e($item->end_date->format('M d, Y')); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo e($item->status); ?>">
                                            <?php echo e(ucfirst($item->status)); ?>

                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No billing history logs found.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/business/subscription.blade.php ENDPATH**/ ?>