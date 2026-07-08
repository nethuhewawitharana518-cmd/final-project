<?php $__env->startSection('title', 'Revenue Ledger'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex">
    <?php echo $__env->make('admin.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Revenue Ledger</h4>
            <span class="badge bg-danger status-badge active">Financial Hub</span>
        </div>

        <div class="content-area">
            
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="kpi-card bg-white h-100">
                        <div class="kpi-icon green">
                            <i class="fa fa-percent text-success"></i>
                        </div>
                        <div>
                            <div class="kpi-value">Rs. <?php echo e(number_format($commissionsCollected)); ?></div>
                            <div class="kpi-label">Commissions Collected</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kpi-card bg-white h-100">
                        <div class="kpi-icon blue">
                            <i class="fa fa-credit-card text-primary"></i>
                        </div>
                        <div>
                            <div class="kpi-value">Rs. <?php echo e(number_format($subscriptionRevenue)); ?></div>
                            <div class="kpi-label">Subscription Revenue</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kpi-card bg-white h-100">
                        <div class="kpi-icon amber">
                            <i class="fa fa-store text-warning"></i>
                        </div>
                        <div>
                            <div class="kpi-value">Rs. <?php echo e(number_format($registrationRevenue)); ?></div>
                            <div class="kpi-label">Registration Fee Income</div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0 text-dark">Successful Payments</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Payment ID</th>
                                    <th>Transaction ID</th>
                                    <th>Amount</th>
                                    <th>Gateway Source</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="fw-semibold">#<?php echo e($payment->id); ?></td>
                                    <td><code><?php echo e($payment->transaction_id); ?></code></td>
                                    <td class="fw-bold text-success">Rs. <?php echo e(number_format($payment->amount)); ?></td>
                                    <td><span class="badge bg-secondary"><?php echo e(ucfirst($payment->gateway)); ?></span></td>
                                    <td><span class="badge bg-success">Success</span></td>
                                    <td><?php echo e($payment->created_at->format('M d, Y H:i')); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fa fa-history fa-2x mb-3 text-muted"></i>
                                        <p class="mb-0">No successful payments logged yet.</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="mt-4">
                <?php echo e($payments->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/admin/revenue.blade.php ENDPATH**/ ?>