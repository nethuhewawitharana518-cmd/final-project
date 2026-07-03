<?php $__env->startSection('title', 'Manage Reservations'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex">
    <?php echo $__env->make('business.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Customer Bookings</h4>
            <span class="badge bg-success status-badge active">Reservations Ledger</span>
        </div>

        <div class="content-area text-start">
            
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="<?php echo e(route('business.reservations', ['status' => 'all'])); ?>" class="btn btn-sm <?php echo e($status == 'all' ? 'btn-success' : 'btn-outline-secondary'); ?>">All</a>
                <a href="<?php echo e(route('business.reservations', ['status' => 'pending'])); ?>" class="btn btn-sm <?php echo e($status == 'pending' ? 'btn-success' : 'btn-outline-secondary'); ?>">Pending Scan</a>
                <a href="<?php echo e(route('business.reservations', ['status' => 'confirmed'])); ?>" class="btn btn-sm <?php echo e($status == 'confirmed' ? 'btn-success' : 'btn-outline-secondary'); ?>">Confirmed</a>
                <a href="<?php echo e(route('business.reservations', ['status' => 'paid'])); ?>" class="btn btn-sm <?php echo e($status == 'paid' ? 'btn-success' : 'btn-outline-secondary'); ?>">Paid</a>
                <a href="<?php echo e(route('business.reservations', ['status' => 'collected'])); ?>" class="btn btn-sm <?php echo e($status == 'collected' ? 'btn-success' : 'btn-outline-secondary'); ?>">Collected</a>
                <a href="<?php echo e(route('business.reservations', ['status' => 'cancelled'])); ?>" class="btn btn-sm <?php echo e($status == 'cancelled' ? 'btn-success' : 'btn-outline-secondary'); ?>">Cancelled</a>
            </div>

            
            <div class="card border-0 shadow-sm bg-white rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Order Code</th>
                                    <th>Customer</th>
                                    <th>Quantity</th>
                                    <th>Price Total</th>
                                    <th>Reservation Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $reservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $res): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo e($res->reservation_code); ?></td>
                                    <td><?php echo e($res->customer->name ?? 'Guest User'); ?></td>
                                    <td><?php echo e($res->items->sum('quantity')); ?> items</td>
                                    <td class="fw-bold text-success">Rs. <?php echo e(number_format($res->total_amount, 2)); ?></td>
                                    <td><?php echo e($res->created_at->format('M d, Y H:i')); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo e($res->status); ?>">
                                            <?php echo e(ucfirst($res->status)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('business.reservations.show', $res->id)); ?>" class="btn btn-sm btn-success px-3">View Detail</a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">No reservations log matched.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <?php echo e($reservations->appends(request()->input())->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/business/reservations.blade.php ENDPATH**/ ?>