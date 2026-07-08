<?php $__env->startSection('title', 'AI Demand Analytics'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex">
    <?php echo $__env->make('business.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">AI Analytics & Forecasting</h4>
            <span class="badge bg-success status-badge active">AI Engine v1.0</span>
        </div>

        <div class="content-area text-start">
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold text-dark mb-0">Predicted Peak Demand Hours</h5>
                            <i class="fa fa-clock text-primary fs-4"></i>
                        </div>
                        <p class="text-muted small">Based on historical customer reservation check-ins, the AI predicts maximum customer pickup traffic during the following hours:</p>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php $__currentLoopData = $forecast['peak_hours'] ?? [12, 18]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="bg-light border text-dark px-3 py-2 rounded-3 text-center" style="min-width: 90px;">
                                    <div class="fw-bold fs-5"><?php echo e(sprintf("%02d:00", $hour)); ?></div>
                                    <div class="small text-muted"><?php echo e($hour < 12 ? 'AM Peak' : ($hour < 17 ? 'Lunch Peak' : 'Dinner Peak')); ?></div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="alert alert-info mt-3 mb-0 py-2 small">
                            <i class="fa fa-info-circle me-1"></i> <strong>Pro-Tip:</strong> Schedule your surplus listings 2-3 hours before these peak traffic windows to maximize sale likelihood.
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold text-dark mb-0">Demand Forecast Trend</h5>
                            <span class="status-badge <?php echo e(($forecast['trend'] ?? 'rising') === 'rising' ? 'active' : 'warning'); ?>">
                                <?php echo e(ucfirst($forecast['trend'] ?? 'rising')); ?> Trend
                            </span>
                        </div>
                        <p class="text-muted small">Platform forecast model detects a <strong><?php echo e($forecast['trend'] ?? 'rising'); ?></strong> demand pattern for your business area.</p>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="border rounded p-3 bg-light">
                                    <div class="small text-muted">Forecast Model</div>
                                    <div class="fw-bold text-dark"><?php echo e($forecast['model'] ?? 'LinearRegression'); ?></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-3 bg-light">
                                    <div class="small text-muted">Average Confidence</div>
                                    <div class="fw-bold text-dark">78.0%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="row g-4 mb-4">
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm rounded-3 bg-white">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="fw-bold mb-0 text-dark">7-Day Demand Forecast (Predicted Orders)</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table fr-table mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Day</th>
                                            <th>Predicted Orders</th>
                                            <th>Confidence Level</th>
                                            <th>Status Recommendation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $forecast['forecast'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayForecast): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td class="fw-bold text-dark"><?php echo e($dayForecast['day']); ?></td>
                                            <td class="fw-bold text-success"><?php echo e($dayForecast['predicted_orders']); ?> orders</td>
                                            <td>
                                                <div class="progress" style="height: 6px; max-width: 120px;">
                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo e($dayForecast['confidence'] * 100); ?>%"></div>
                                                </div>
                                                <span class="small text-muted"><?php echo e($dayForecast['confidence'] * 100); ?>%</span>
                                            </td>
                                            <td>
                                                <?php if($dayForecast['predicted_orders'] > 40): ?>
                                                    <span class="badge bg-success">High Demand Expected</span>
                                                <?php elseif($dayForecast['predicted_orders'] > 25): ?>
                                                    <span class="badge bg-info text-dark">Steady Traffic</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">Increase Promotions</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                No forecasting data available. Keep selling to train the AI!
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                        <h5 class="fw-bold text-dark mb-3">AI Category Demand Shares</h5>
                        <p class="text-muted small">Demand interest breakdown across different food categories for your business location:</p>
                        
                        <div class="d-flex flex-column gap-3">
                            <?php $__empty_1 = true; $__currentLoopData = $forecast['category_demand'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $share): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div>
                                    <div class="d-flex justify-content-between mb-1 small fw-bold text-dark">
                                        <span><?php echo e($share['category']); ?></span>
                                        <span><?php echo e(number_format($share['share'] * 100, 1)); ?>%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo e($share['share'] * 100); ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="text-center py-4 text-muted">No category distribution data found.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                <h5 class="fw-bold text-dark mb-3">Active Inventory Analysis</h5>
                <div class="row g-3 text-center">
                    <?php $__empty_1 = true; $__currentLoopData = $foodStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="col-md-3">
                            <div class="border rounded p-3 bg-light">
                                <div class="small text-muted mb-1"><?php echo e($stat->name); ?></div>
                                <div class="fw-bold text-success fs-4"><?php echo e($stat->total_qty); ?> Qty</div>
                                <div class="small text-muted"><?php echo e($stat->count); ?> Active listings</div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-12 py-3 text-muted text-center">No active inventory stats available.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/business/analytics.blade.php ENDPATH**/ ?>