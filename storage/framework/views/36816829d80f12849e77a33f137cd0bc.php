<?php $__env->startSection('title', 'Manage Food Listings'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex">
    <?php echo $__env->make('business.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Manage Food Listings</h4>
            <a href="<?php echo e(route('business.food.create')); ?>" class="btn btn-success btn-sm px-3">
                <i class="fa fa-plus me-1"></i> Add Food Listing
            </a>
        </div>

        <div class="content-area">
            
            <div class="row g-3 mb-4 text-start">
                <div class="col-md-2.4 col-sm-6 flex-grow-1">
                    <div class="kpi-card p-3 bg-white">
                        <h4 class="fw-bold mb-0 text-dark"><?php echo e($stats['total']); ?></h4>
                        <div class="small text-muted">Total Uploaded</div>
                    </div>
                </div>
                <div class="col-md-2.4 col-sm-6 flex-grow-1">
                    <div class="kpi-card p-3 bg-white">
                        <h4 class="fw-bold mb-0 text-success"><?php echo e($stats['active']); ?></h4>
                        <div class="small text-muted">Active Deals</div>
                    </div>
                </div>
                <div class="col-md-2.4 col-sm-6 flex-grow-1">
                    <div class="kpi-card p-3 bg-white">
                        <h4 class="fw-bold mb-0 text-primary"><?php echo e($stats['sold_out']); ?></h4>
                        <div class="small text-muted">Sold Out</div>
                    </div>
                </div>
                <div class="col-md-2.4 col-sm-6 flex-grow-1">
                    <div class="kpi-card p-3 bg-white">
                        <h4 class="fw-bold mb-0 text-danger"><?php echo e($stats['expired']); ?></h4>
                        <div class="small text-muted">Expired Listings</div>
                    </div>
                </div>
                <div class="col-md-2.4 col-sm-6 flex-grow-1">
                    <div class="kpi-card p-3 bg-white border-danger">
                        <h4 class="fw-bold mb-0 text-danger"><?php echo e($stats['high_risk']); ?></h4>
                        <div class="small text-muted">High Expiry Risk</div>
                    </div>
                </div>
            </div>

            
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4 p-3 text-start">
                <form action="<?php echo e(route('business.food.index')); ?>" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Filter Category</label>
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Listing Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Active</option>
                            <option value="sold_out" <?php echo e(request('status') === 'sold_out' ? 'selected' : ''); ?>>Sold Out</option>
                            <option value="expired" <?php echo e(request('status') === 'expired' ? 'selected' : ''); ?>>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">AI Expiry Risk</label>
                        <select name="risk" class="form-select">
                            <option value="">All Risks</option>
                            <option value="low" <?php echo e(request('risk') === 'low' ? 'selected' : ''); ?>>Low Risk</option>
                            <option value="medium" <?php echo e(request('risk') === 'medium' ? 'selected' : ''); ?>>Medium Risk</option>
                            <option value="high" <?php echo e(request('risk') === 'high' ? 'selected' : ''); ?>>High Risk</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-success w-100">Apply Filters</button>
                        <a href="<?php echo e(route('business.food.index')); ?>" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>

            
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table table-responsive-stack mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Food Item</th>
                                    <th>Category</th>
                                    <th>Price (Orig / Sale)</th>
                                    <th>Available Qty</th>
                                    <th>Expiry Time</th>
                                    <th>AI Expiry Risk</th>
                                    <th>Featured</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $foods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $food): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3 text-start">
                                            <?php if($food->image): ?>
                                                <img src="<?php echo e(str_starts_with($food->image, 'assets/') ? asset($food->image) : asset('storage/' . $food->image)); ?>" alt="" class="rounded-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.5rem;">🍲</div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-bold text-dark"><?php echo e($food->name); ?></div>
                                                <div class="small text-muted"><?php echo e(Str::limit($food->description, 40)); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Category"><?php echo e($food->category->name); ?></td>
                                    <td data-label="Price">
                                        <span class="text-decoration-line-through text-muted small">Rs. <?php echo e(number_format($food->original_price)); ?></span>
                                        <div class="fw-bold text-success">Rs. <?php echo e(number_format($food->discount_price)); ?></div>
                                    </td>
                                    <td class="fw-bold text-dark" data-label="Available Qty"><?php echo e($food->available_quantity); ?> / <?php echo e($food->quantity); ?></td>
                                    <td data-label="Expiry Time">
                                        <div class="small fw-semibold text-dark"><?php echo e($food->expiry_datetime->format('M d, H:i')); ?></div>
                                        <div class="small <?php echo e($food->expiry_datetime->isPast() ? 'text-danger' : 'text-muted'); ?>"><?php echo e($food->expiry_datetime->diffForHumans()); ?></div>
                                    </td>
                                    <td data-label="AI Expiry Risk">
                                        <span class="ai-risk-inline <?php echo e($food->ai_risk_level ?: 'low'); ?>">
                                            AI: <?php echo e(ucfirst($food->ai_risk_level ?: 'low')); ?> Risk
                                        </span>
                                    </td>
                                    <td data-label="Featured">
                                        <form action="<?php echo e(route('business.food.featured', $food->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm <?php echo e($food->is_featured ? 'btn-warning text-dark' : 'btn-outline-secondary'); ?>">
                                                <?php echo e($food->is_featured ? '★ Featured' : '☆ Promote'); ?>

                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="<?php echo e(route('business.food.edit', $food->id)); ?>" class="btn btn-sm btn-outline-success">Edit</a>
                                            <form action="<?php echo e(route('business.food.delete', $food->id)); ?>" method="POST" onsubmit="return confirm('Delete this food listing?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fa fa-utensils fa-2x mb-3 text-muted"></i>
                                        <p class="mb-0">No food listings uploaded yet. List your surplus food now!</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="mt-4">
                <?php echo e($foods->appends(request()->input())->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/business/food/index.blade.php ENDPATH**/ ?>