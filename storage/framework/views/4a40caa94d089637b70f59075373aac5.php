<?php $__env->startSection('title', 'Loyalty Points'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex">
    <?php echo $__env->make('customer.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Loyalty Points Program</h4>
            <span class="badge bg-success status-badge active">Rewards & Benefits</span>
        </div>

        <div class="content-area">
            <div class="row g-4 mb-5">
                
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 p-4 h-100 bg-white">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <h6 class="text-muted text-uppercase fw-semibold mb-1">Your Points Balance</h6>
                                <h1 class="fw-extrabold text-success mb-0" style="font-size: 3.5rem;"><?php echo e($balance); ?></h1>
                                <p class="text-muted small mt-2">Earn points on every order you collect.</p>
                            </div>
                            <div class="fs-1">🏆</div>
                        </div>
                        <div class="pt-3 border-top">
                            <span class="text-muted small">Current Status Tier:</span>
                            <span class="tier-badge <?php echo e(strtolower($tier)); ?> ms-2"><?php echo e($tier); ?></span>
                        </div>
                    </div>
                </div>

                
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 p-4 h-100 bg-white">
                        <h5 class="fw-bold text-dark mb-3">Redeem Rewards</h5>
                        <p class="text-muted small mb-4">Redeem your loyalty points for discount vouchers to use on subsequent surplus food purchases.</p>

                        <div class="d-flex flex-column gap-3">
                            <?php $__currentLoopData = $redemptionOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-light">
                                <div>
                                    <div class="fw-bold"><?php echo e($option['points']); ?> Points</div>
                                    <div class="text-muted small">Rs. <?php echo e(number_format((float) $option['value'])); ?> Voucher</div>
                                </div>
                                <form action="<?php echo e(route('customer.loyalty.redeem')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="points" value="<?php echo e($option['points']); ?>">
                                    <button type="submit" class="btn btn-success btn-sm px-4 rounded-pill" <?php echo e($balance < $option['points'] ? 'disabled' : ''); ?>>
                                        Redeem
                                    </button>
                                </form>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0 text-dark">Points History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $netPoints = $item->transaction_type === 'earn'
                                        ? $item->points_earned
                                        : -$item->points_redeemed;
                                ?>
                                <tr>
                                    <td><?php echo e($item->description); ?></td>
                                    <td class="fw-bold <?php echo e($netPoints >= 0 ? 'text-success' : 'text-danger'); ?>">
                                        <?php echo e($netPoints >= 0 ? '+' : ''); ?><?php echo e($netPoints); ?>

                                    </td>
                                    <td>
                                        <span class="badge <?php echo e($netPoints >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'); ?> rounded-pill px-3 py-1">
                                            <?php echo e($netPoints >= 0 ? 'Earned' : 'Redeemed'); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($item->created_at->format('M d, Y H:i')); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fa fa-history fa-2x mb-3 text-muted"></i>
                                        <p class="mb-0">No points transaction history found.</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="mt-4">
                <?php echo e($history->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/customer/loyalty.blade.php ENDPATH**/ ?>