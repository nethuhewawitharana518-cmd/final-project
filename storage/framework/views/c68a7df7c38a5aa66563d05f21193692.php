<?php $__env->startSection('title', 'Subscriptions Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex">
    <?php echo $__env->make('admin.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Manage Subscriptions</h4>
            <span class="badge bg-danger status-badge active">Platform Access</span>
        </div>

        <div class="content-area">
            
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="<?php echo e(route('admin.subscriptions', ['status' => 'all'])); ?>" class="btn btn-sm <?php echo e($status == 'all' ? 'btn-danger' : 'btn-outline-secondary'); ?>">All Subscriptions</a>
                <a href="<?php echo e(route('admin.subscriptions', ['status' => 'active'])); ?>" class="btn btn-sm <?php echo e($status == 'active' ? 'btn-danger' : 'btn-outline-secondary'); ?>">Active</a>
                <a href="<?php echo e(route('admin.subscriptions', ['status' => 'expired'])); ?>" class="btn btn-sm <?php echo e($status == 'expired' ? 'btn-danger' : 'btn-outline-secondary'); ?>">Expired</a>
                <a href="<?php echo e(route('admin.subscriptions', ['status' => 'cancelled'])); ?>" class="btn btn-sm <?php echo e($status == 'cancelled' ? 'btn-danger' : 'btn-outline-secondary'); ?>">Cancelled</a>
            </div>

            
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Business Partner</th>
                                    <th>Tier Plan</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Upload Limit</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?php echo e($sub->business->business_name ?? 'N/A'); ?></td>
                                    <td><span class="badge bg-primary"><?php echo e(ucfirst($sub->plan_type)); ?></span></td>
                                    <td><?php echo e($sub->start_date->format('M d, Y')); ?></td>
                                    <td><?php echo e($sub->end_date->format('M d, Y')); ?></td>
                                    <td><?php echo e($sub->upload_limit === -1 ? 'Unlimited' : $sub->upload_limit); ?> uploads</td>
                                    <td>
                                        <span class="status-badge <?php echo e($sub->status); ?>">
                                            <?php echo e(ucfirst($sub->status)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <?php if($sub->status === 'active'): ?>
                                                <form action="<?php echo e(route('admin.subscriptions.extend', $sub->id)); ?>" method="POST" class="d-flex gap-2 align-items-center">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="number" name="days" value="30" min="1" class="form-control form-control-sm" style="width: 70px;" required>
                                                    <button type="submit" class="btn btn-sm btn-outline-success">Extend</button>
                                                </form>
                                                <form action="<?php echo e(route('admin.subscriptions.cancel', $sub->id)); ?>" method="POST" onsubmit="return confirm('Cancel this subscription plan?')">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-muted small">No actions</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fa fa-credit-card fa-2x mb-3 text-muted"></i>
                                        <p class="mb-0">No subscriptions found matching this status.</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="mt-4">
                <?php echo e($subscriptions->appends(request()->input())->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/admin/subscriptions.blade.php ENDPATH**/ ?>