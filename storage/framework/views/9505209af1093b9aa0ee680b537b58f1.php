<?php $__env->startSection('title', 'My Orders'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex">
    <?php echo $__env->make('customer.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">My Orders</h4>
            <span class="badge bg-success status-badge active">Order History</span>
        </div>

        <div class="content-area">
            
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="<?php echo e(route('orders.index', ['status' => 'all'])); ?>" class="btn btn-sm <?php echo e($status == 'all' ? 'btn-success' : 'btn-outline-secondary'); ?>">All</a>
                <a href="<?php echo e(route('orders.index', ['status' => 'pending'])); ?>" class="btn btn-sm <?php echo e($status == 'pending' ? 'btn-success' : 'btn-outline-secondary'); ?>">Pending</a>
                <a href="<?php echo e(route('orders.index', ['status' => 'confirmed'])); ?>" class="btn btn-sm <?php echo e($status == 'confirmed' ? 'btn-success' : 'btn-outline-secondary'); ?>">Confirmed</a>
                <a href="<?php echo e(route('orders.index', ['status' => 'paid'])); ?>" class="btn btn-sm <?php echo e($status == 'paid' ? 'btn-success' : 'btn-outline-secondary'); ?>">Paid</a>
                <a href="<?php echo e(route('orders.index', ['status' => 'collected'])); ?>" class="btn btn-sm <?php echo e($status == 'collected' ? 'btn-success' : 'btn-outline-secondary'); ?>">Collected</a>
                <a href="<?php echo e(route('orders.index', ['status' => 'cancelled'])); ?>" class="btn btn-sm <?php echo e($status == 'cancelled' ? 'btn-success' : 'btn-outline-secondary'); ?>">Cancelled</a>
            </div>

            
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Order Code</th>
                                    <th>Business</th>
                                    <th>Items</th>
                                    <th>Total Price</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo e($order->reservation_code); ?></td>
                                    <td><?php echo e($order->business->business_name ?? 'N/A'); ?></td>
                                    <td>
                                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge bg-light text-dark"><?php echo e($item->food->name ?? 'Item'); ?> (x<?php echo e($item->quantity); ?>)</span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                    <td class="fw-bold">Rs. <?php echo e(number_format($order->total_amount, 2)); ?></td>
                                    <td><?php echo e($order->created_at->format('M d, Y H:i')); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo e($order->status); ?>">
                                            <?php echo e(ucfirst($order->status)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="<?php echo e(route('customer.orders.show', $order->id)); ?>" class="btn btn-sm btn-success px-3">View</a>
                                            <?php if($order->status === 'pending'): ?>
                                                <form action="<?php echo e(route('customer.orders.cancel', $order->id)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fa fa-receipt fa-2x mb-3 text-muted"></i>
                                        <p class="mb-0">No orders found matching the filter.</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="mt-4">
                <?php echo e($orders->appends(request()->input())->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/customer/orders.blade.php ENDPATH**/ ?>