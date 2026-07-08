<?php $__env->startSection('title', 'Commissions Ledger'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex">
    <?php echo $__env->make('admin.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Commissions Ledger</h4>
            <span class="badge bg-danger status-badge active">System Audit</span>
        </div>

        <div class="content-area">
            
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="kpi-card bg-white p-4 rounded-3 shadow-sm h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold">Total Pending Payout</span>
                                <h2 class="fw-extrabold text-warning mb-0 mt-1">Rs. <?php echo e(number_format($pendingTotal)); ?></h2>
                            </div>
                            <div class="fs-2">⏳</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="kpi-card bg-white p-4 rounded-3 shadow-sm h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold">Total Settled Payout</span>
                                <h2 class="fw-extrabold text-success mb-0 mt-1">Rs. <?php echo e(number_format($settledTotal)); ?></h2>
                            </div>
                            <div class="fs-2">✅</div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="<?php echo e(route('admin.commissions', ['status' => 'all'])); ?>" class="btn btn-sm <?php echo e($status == 'all' ? 'btn-danger' : 'btn-outline-secondary'); ?>">All Logs</a>
                <a href="<?php echo e(route('admin.commissions', ['status' => 'pending'])); ?>" class="btn btn-sm <?php echo e($status == 'pending' ? 'btn-danger' : 'btn-outline-secondary'); ?>">Pending</a>
                <a href="<?php echo e(route('admin.commissions', ['status' => 'settled'])); ?>" class="btn btn-sm <?php echo e($status == 'settled' ? 'btn-danger' : 'btn-outline-secondary'); ?>">Settled</a>
            </div>

            
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Business Partner</th>
                                    <th>Reservation Code</th>
                                    <th>Order Total</th>
                                    <th>Platform Fee</th>
                                    <th>Partner Share</th>
                                    <th>Status</th>
                                    <th>Settle Payout</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $commissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?php echo e($comm->business->business_name ?? 'N/A'); ?></td>
                                    <td><code><?php echo e($comm->reservation->reservation_code ?? 'N/A'); ?></code></td>
                                    <td>Rs. <?php echo e(number_format($comm->sale_amount)); ?></td>
                                    <td class="text-danger fw-semibold">Rs. <?php echo e(number_format($comm->commission_amount)); ?></td>
                                    <td class="text-success fw-bold">Rs. <?php echo e(number_format($comm->business_earnings)); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo e($comm->status); ?>">
                                            <?php echo e(ucfirst($comm->status)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <?php if($comm->status === 'pending'): ?>
                                            <form action="<?php echo e(route('admin.commissions.settle')); ?>" method="POST" onsubmit="return confirm('Settle all pending commissions for this business?')">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="business_id" value="<?php echo e($comm->business_id); ?>">
                                                <button type="submit" class="btn btn-sm btn-success px-3">Settle</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted small">Settled at <?php echo e($comm->settled_at ? $comm->settled_at->format('M d, H:i') : 'N/A'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fa fa-percentage fa-2x mb-3 text-muted"></i>
                                        <p class="mb-0">No commissions logs found matching this status.</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="mt-4">
                <?php echo e($commissions->appends(request()->input())->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/admin/commissions.blade.php ENDPATH**/ ?>