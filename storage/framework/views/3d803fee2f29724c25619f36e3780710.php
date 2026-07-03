<?php $__env->startSection('title', 'Business Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex">
    <?php echo $__env->make('business.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Merchant Panel: <?php echo e($business->business_name); ?></h4>
            <span class="status-badge active">Approved Business</span>
        </div>

        <div class="content-area">
            
            <div class="row g-4 mb-5">
                <div class="col-md-3 col-sm-6">
                    <div class="kpi-card bg-white h-100">
                        <div class="kpi-icon green">
                            <i class="fa fa-hand-holding-usd"></i>
                        </div>
                        <div>
                            <div class="kpi-value">Rs. <?php echo e(number_format($todayEarnings)); ?></div>
                            <div class="kpi-label">Today's Earnings</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="kpi-card bg-white h-100">
                        <div class="kpi-icon blue">
                            <i class="fa fa-utensils"></i>
                        </div>
                        <div>
                            <div class="kpi-value"><?php echo e($activeListingsCount); ?></div>
                            <div class="kpi-label">Active Listings</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="kpi-card bg-white h-100">
                        <div class="kpi-icon amber">
                            <i class="fa fa-spinner"></i>
                        </div>
                        <div>
                            <div class="kpi-value"><?php echo e($pendingReservationsCount); ?></div>
                            <div class="kpi-label">Pending Bookings</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="kpi-card bg-white h-100">
                        <div class="kpi-icon red">
                            <i class="fa fa-percentage"></i>
                        </div>
                        <div>
                            <div class="kpi-value">Rs. <?php echo e(number_format($totalCommissionPaid)); ?></div>
                            <div class="kpi-label">Commission Paid</div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="kpi-card bg-white h-100">
                        <div class="kpi-icon green">
                            <i class="fa fa-chart-line"></i>
                        </div>
                        <div>
                            <div class="kpi-value">Rs. <?php echo e(number_format($totalRevenueThisMonth, 2)); ?></div>
                            <div class="kpi-label">Total Revenue (This Month)</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kpi-card bg-white h-100">
                        <div class="kpi-icon blue">
                            <i class="fa fa-calculator"></i>
                        </div>
                        <div>
                            <div class="kpi-value">Rs. <?php echo e(number_format($averageOrderValue, 2)); ?></div>
                            <div class="kpi-label">Average Order Value</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kpi-card bg-white h-100">
                        <div class="kpi-icon amber">
                            <i class="fa fa-clock-rotate-left"></i>
                        </div>
                        <div>
                            <div class="kpi-value">Rs. <?php echo e(number_format($pendingPayouts, 2)); ?></div>
                            <div class="kpi-label">Pending Payouts</div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="row g-4 mb-5">
                
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                        <h5 class="fw-bold text-white mb-4"><i class="fa fa-chart-bar text-success me-2"></i>Monthly Income Trends (<?php echo e(now()->year); ?>)</h5>
                        <div id="monthlyIncomeChart" style="min-height: 350px;"></div>
                    </div>
                </div>

                
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                        <h5 class="fw-bold text-white mb-4"><i class="fa fa-chart-area text-success me-2"></i>Daily Earnings (Last 15 Days)</h5>
                        <div id="dailyEarningsChart" style="min-height: 350px;"></div>
                    </div>
                </div>
            </div>

            
            <?php if($aiInsights->isNotEmpty()): ?>
            <div class="alert alert-warning border-0 shadow-sm rounded-3 p-4 mb-5 text-start">
                <h5 class="fw-bold mb-3"><i class="fa fa-brain me-2 text-danger"></i>AI Food Expiry Risk Alerts</h5>
                <p class="small text-muted mb-4">The following food listings are at high/medium risk of expiring unsold. Click 'Optimize' to modify pricing or details based on AI recommendations.</p>
                <div class="row g-3">
                    <?php $__currentLoopData = $aiInsights; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-12 p-3 bg-white rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="fw-bold text-dark"><?php echo e($item->name); ?></span>
                            <span class="ai-risk-inline <?php echo e(strtolower($item->ai_risk_level)); ?> ms-2">
                                <?php echo e(ucfirst($item->ai_risk_level)); ?> Expiry Risk
                            </span>
                            <div class="small text-muted mt-1">Expires: <?php echo e($item->expiry_datetime->format('M d, H:i')); ?> (<?php echo e($item->expiry_datetime->diffForHumans()); ?>)</div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="text-end small">
                                <div class="text-muted">AI Rec Discount:</div>
                                <span class="fw-bold" style="color: var(--primary);"><?php echo e($item->ai_recommended_discount); ?>% OFF</span>
                            </div>
                            <a href="<?php echo e(route('business.food.edit', $item->id)); ?>" class="btn btn-sm btn-outline-success">Optimize Pricing</a>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>

            
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">Recent Customer Bookings</h5>
                    <a href="<?php echo e(route('business.reservations')); ?>" class="btn btn-outline-success btn-sm px-3">All Bookings</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Reservation Code</th>
                                    <th>Customer</th>
                                    <th>Reserved Items</th>
                                    <th>Total Price</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $recentReservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $res): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo e($res->reservation_code); ?></td>
                                    <td><?php echo e($res->customer->name ?? 'Guest User'); ?></td>
                                    <td>
                                        <?php $__currentLoopData = $res->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge bg-light text-dark"><?php echo e($item->food->name ?? 'Food'); ?> (x<?php echo e($item->quantity); ?>)</span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                    <td class="fw-bold" style="color: var(--primary);">Rs. <?php echo e(number_format($res->total_amount, 2)); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo e($res->status); ?>">
                                            <?php echo e(ucfirst($res->status)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('business.reservations.show', $res->id)); ?>" class="btn btn-sm btn-success px-3">View</a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fa fa-receipt fa-2x mb-3 text-muted"></i>
                                        <p class="mb-0">No customer bookings logged yet.</p>
                                    </td>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Premium Dark Mode color variables
    const primaryColor = '#059669'; // Success emerald
    const secondaryColor = '#3b82f6'; // Blue
    const textColor = '#9ca3af'; // Gray muted text
    const gridColor = '#374151'; // Dark border/grid color

    // Monthly Income Chart (Bar Chart)
    var monthlyOptions = {
        series: [{
            name: 'Earnings (Rs.)',
            data: <?php echo json_encode($monthlyEarnings, 15, 512) ?>
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: false },
            foreColor: textColor,
            background: 'transparent'
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: '50%',
                distributed: false,
            }
        },
        colors: [primaryColor],
        dataLabels: { enabled: false },
        stroke: { show: true, width: 2, colors: ['transparent'] },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return "Rs. " + val.toLocaleString();
                }
            }
        },
        grid: {
            borderColor: gridColor,
            strokeDashArray: 4
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: "vertical",
                shadeIntensity: 0.5,
                gradientToColors: ['#34d399'], // lighter emerald
                inverseColors: true,
                opacityFrom: 0.85,
                opacityTo: 0.95,
                stops: [0, 100]
            }
        },
        tooltip: {
            theme: 'dark',
            y: {
                formatter: function (val) {
                    return "Rs. " + val.toLocaleString();
                }
            }
        }
    };

    var monthlyChart = new ApexCharts(document.querySelector("#monthlyIncomeChart"), monthlyOptions);
    monthlyChart.render();

    // Daily Earnings Overview Chart (Area/Line Chart)
    var dailyOptions = {
        series: [{
            name: 'Earnings (Rs.)',
            data: <?php echo json_encode($dailyEarnings, 15, 512) ?>
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: { show: false },
            foreColor: textColor,
            background: 'transparent'
        },
        colors: [secondaryColor],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        xaxis: {
            categories: <?php echo json_encode($dailyLabels, 15, 512) ?>,
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return "Rs. " + val.toLocaleString();
                }
            }
        },
        grid: {
            borderColor: gridColor,
            strokeDashArray: 4
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [0, 100]
            }
        },
        tooltip: {
            theme: 'dark',
            y: {
                formatter: function (val) {
                    return "Rs. " + val.toLocaleString();
                }
            }
        }
    };

    var dailyChart = new ApexCharts(document.querySelector("#dailyEarningsChart"), dailyOptions);
    dailyChart.render();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/business/dashboard.blade.php ENDPATH**/ ?>