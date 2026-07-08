<?php $__env->startSection('title', 'Business Approvals'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex">
    <?php echo $__env->make('admin.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Business Approvals</h4>
            <span class="badge bg-danger status-badge active">Verification Hub</span>
        </div>

        <div class="content-area">
            
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="<?php echo e(route('admin.businesses', ['status' => 'pending'])); ?>" class="btn btn-sm <?php echo e($status == 'pending' ? 'btn-danger' : 'btn-outline-secondary'); ?>">Pending Review</a>
                <a href="<?php echo e(route('admin.businesses', ['status' => 'approved'])); ?>" class="btn btn-sm <?php echo e($status == 'approved' ? 'btn-success' : 'btn-outline-secondary'); ?>">Approved (Active)</a>
                <a href="<?php echo e(route('admin.businesses', ['status' => 'rejected'])); ?>" class="btn btn-sm <?php echo e($status == 'rejected' ? 'btn-warning' : 'btn-outline-secondary'); ?>">Rejected</a>
            </div>

            
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Business Name</th>
                                    <th>Type</th>
                                    <th>Owner</th>
                                    <th>Email/Phone</th>
                                    <th>Reg Number</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $businesses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $biz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?php echo e($biz->business_name); ?></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?php echo e(ucfirst($biz->business_type)); ?></span></td>
                                    <td><?php echo e($biz->user->name ?? 'N/A'); ?></td>
                                    <td>
                                        <div class="small text-muted"><?php echo e($biz->email); ?></div>
                                        <div class="small text-muted"><?php echo e($biz->phone); ?></div>
                                    </td>
                                    <td><code><?php echo e($biz->reg_number); ?></code></td>
                                    <td>
                                        <span class="status-badge <?php echo e($biz->status); ?>">
                                            <?php echo e(ucfirst($biz->status)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="<?php echo e(route('admin.businesses.show', $biz->id)); ?>" class="btn btn-sm btn-outline-success">Review Documents</a>
                                            <?php if($biz->status === 'pending'): ?>
                                                <form action="<?php echo e(route('admin.businesses.approve', $biz->id)); ?>" method="POST" onsubmit="return confirm('Approve this business?')">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo e($biz->id); ?>">
                                                    Reject
                                                </button>

                                                
                                                <div class="modal fade" id="rejectModal<?php echo e($biz->id); ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <form action="<?php echo e(route('admin.businesses.reject', $biz->id)); ?>" method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Reject Business: <?php echo e($biz->business_name); ?></h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body text-start">
                                                                    <div class="mb-3">
                                                                        <label for="reason" class="form-label">Reason for Rejection</label>
                                                                        <textarea name="reason" id="reason" rows="3" class="form-control" placeholder="Provide a reason (min 5 characters)" required></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fa fa-folder-open fa-2x mb-3 text-muted"></i>
                                        <p class="mb-0">No business applications found matching this status.</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="mt-4">
                <?php echo e($businesses->appends(request()->input())->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/admin/businesses.blade.php ENDPATH**/ ?>