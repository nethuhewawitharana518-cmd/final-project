<?php $__env->startSection('title', 'User Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex">
    <?php echo $__env->make('admin.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">User Management</h4>
            <span class="badge bg-danger status-badge active">System Administration</span>
        </div>

        <div class="content-area">
            
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="<?php echo e(route('admin.users', ['role' => 'all'])); ?>" class="btn btn-sm <?php echo e($role == 'all' ? 'btn-danger' : 'btn-outline-secondary'); ?>">All Users</a>
                <a href="<?php echo e(route('admin.users', ['role' => 'customer'])); ?>" class="btn btn-sm <?php echo e($role == 'customer' ? 'btn-danger' : 'btn-outline-secondary'); ?>">Customers</a>
                <a href="<?php echo e(route('admin.users', ['role' => 'business_owner'])); ?>" class="btn btn-sm <?php echo e($role == 'business_owner' ? 'btn-danger' : 'btn-outline-secondary'); ?>">Hotels & Restaurants</a>
                <a href="<?php echo e(route('admin.users', ['role' => 'admin'])); ?>" class="btn btn-sm <?php echo e($role == 'admin' ? 'btn-danger' : 'btn-outline-secondary'); ?>">Admins</a>
            </div>

            
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>User Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Registration Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?php echo e($user->name); ?></td>
                                    <td><?php echo e($user->email); ?></td>
                                    <td><?php echo e($user->phone ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if($user->isAdmin()): ?>
                                            <span class="badge bg-danger">Admin</span>
                                        <?php elseif($user->isBusinessOwner()): ?>
                                            <span class="badge bg-success">Business Owner</span>
                                        <?php else: ?>
                                            <span class="badge bg-info">Customer</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($user->created_at->format('M d, Y H:i')); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo e($user->status === 'active' ? 'active' : 'expired'); ?>">
                                            <?php echo e(ucfirst($user->status)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <?php if($user->id !== auth()->id()): ?>
                                                <?php if($user->status === 'active'): ?>
                                                    <form action="<?php echo e(route('admin.users.suspend', $user->id)); ?>" method="POST" onsubmit="return confirm('Suspend this user account?')">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-warning">Suspend</button>
                                                    </form>
                                                <?php else: ?>
                                                    <form action="<?php echo e(route('admin.users.activate', $user->id)); ?>" method="POST" onsubmit="return confirm('<?php echo e($user->status === 'pending' && $user->isBusinessOwner() ? 'Approve' : 'Activate'); ?> this user account?')">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <?php echo e($user->status === 'pending' && $user->isBusinessOwner() ? 'Approve' : 'Activate'); ?>

                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <form action="<?php echo e(route('admin.users.delete', $user->id)); ?>" method="POST" onsubmit="return confirm('Permanently delete this user?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-muted small">Current User</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fa fa-users fa-2x mb-3 text-muted"></i>
                                        <p class="mb-0">No users found.</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="mt-4">
                <?php echo e($users->appends(request()->input())->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/admin/users.blade.php ENDPATH**/ ?>