<?php $__env->startSection('title', 'Reports Overview'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex">
    <?php echo $__env->make('admin.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Sales & Activity Reports</h4>
            <span class="badge bg-danger status-badge active">Analytics Console</span>
        </div>

        <div class="content-area">
            
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-success text-white p-4 rounded-3 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="text-uppercase fw-semibold mb-0">Monthly Income</h6>
                            <i class="fa fa-money-bill-wave fs-4"></i>
                        </div>
                        <h2 class="fw-bold mb-1">Rs. <?php echo e(number_format($monthlyIncome)); ?></h2>
                        <span class="small opacity-75">Accumulated sales in <?php echo e(date('F Y')); ?></span>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-primary text-white p-4 rounded-3 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="text-uppercase fw-semibold mb-0">Food Uploads</h6>
                            <i class="fa fa-utensils fs-4"></i>
                        </div>
                        <h2 class="fw-bold mb-1"><?php echo e(number_format($totalFoodUploads)); ?></h2>
                        <span class="small opacity-75">Surplus food items uploaded</span>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-warning text-white p-4 rounded-3 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="text-uppercase fw-semibold mb-0">Completed Orders</h6>
                            <i class="fa fa-box-open fs-4"></i>
                        </div>
                        <h2 class="fw-bold mb-1"><?php echo e(number_format($completedOrdersCount)); ?></h2>
                        <span class="small opacity-75">Rescued foods collected by customers</span>
                    </div>
                </div>
            </div>

            
            <div class="row g-4 mb-5">
                
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm bg-white p-4 h-100 rounded-3">
                        <h5 class="fw-bold mb-4 text-dark">Data Exports (CSV)</h5>
                        <hr>
                        <div class="d-grid gap-3">
                            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between bg-light">
                                <div>
                                    <h6 class="fw-semibold text-dark mb-1">Food Waste Analytics</h6>
                                    <small class="text-muted">Total food saved and remaining surplus</small>
                                </div>
                                <a href="<?php echo e(route('admin.reports.export', 'food_waste')); ?>" class="btn btn-sm btn-success">
                                    <i class="fa fa-download me-1"></i> Export
                                </a>
                            </div>

                            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between bg-light">
                                <div>
                                    <h6 class="fw-semibold text-dark mb-1">Financial Ledger</h6>
                                    <small class="text-muted">Breakdown of system commissions & fees</small>
                                </div>
                                <a href="<?php echo e(route('admin.reports.export', 'revenue')); ?>" class="btn btn-sm btn-success">
                                    <i class="fa fa-download me-1"></i> Export
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm bg-white p-4 h-100 rounded-3">
                        <h5 class="fw-bold mb-4 text-dark">Top-Performing Hotels & Restaurants</h5>
                        <hr>
                        <div class="table-responsive">
                            <table class="table fr-table mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>Business Name</th>
                                        <th>Type</th>
                                        <th>Contact Email</th>
                                        <th class="text-end">Completed Rescues</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $topBusinesses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $biz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="fw-semibold text-dark"><?php echo e($biz->business_name); ?></td>
                                        <td><span class="badge bg-secondary-subtle text-secondary"><?php echo e(ucfirst($biz->business_type)); ?></span></td>
                                        <td><?php echo e($biz->email); ?></td>
                                        <td class="text-end fw-bold text-success"><?php echo e($biz->reservations_count); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No top-performing partners logged yet.</td>
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
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/admin/reports.blade.php ENDPATH**/ ?>