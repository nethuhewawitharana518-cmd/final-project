<?php $__env->startSection('title', 'Browse Food Deals'); ?>
<?php $__env->startSection('meta_description', 'Browse and search surplus food deals from local restaurants, cafes, hotels and bakeries in Trincomalee. Save up to 80%.'); ?>

<?php $__env->startSection('content'); ?>
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>" class="text-white-50 text-decoration-none hover-primary">Home</a></li>
                <li class="breadcrumb-item text-primary fw-semibold" aria-current="page">Browse Deals</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4">
            
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm p-4 bg-light rounded-3 sticky-top" style="top: 90px; z-index: 10;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-dark mb-0"><i class="fa fa-filter me-2 text-primary"></i>Filters</h5>
                        <a href="<?php echo e(route('food.browse')); ?>" class="text-primary text-decoration-none small fw-semibold">Clear All</a>
                    </div>

                    <form action="<?php echo e(route('food.browse')); ?>" method="GET">
                        
                        <div class="mb-4">
                            <label for="search" class="form-label text-dark small fw-semibold">Search Deals</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-search"></i></span>
                                <input type="text" name="q" id="search" class="form-control border-start-0 ps-0" placeholder="Biryani, cake..." value="<?php echo e(request('q')); ?>">
                            </div>
                        </div>

                        
                        <div class="mb-4">
                            <label class="form-label text-dark small fw-semibold d-block">Categories</label>
                            <div class="d-flex flex-column gap-2 overflow-auto" style="max-height: 200px;">
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="category[]" value="<?php echo e($cat->id); ?>" id="cat_<?php echo e($cat->id); ?>" 
                                            <?php if(is_array(request('category')) && in_array($cat->id, request('category'))): ?> checked <?php elseif(request('category') == $cat->slug): ?> checked <?php endif; ?>>
                                        <label class="form-check-label text-muted small" for="cat_<?php echo e($cat->id); ?>">
                                            <?php echo e($cat->name); ?>

                                        </label>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        
                        <div class="mb-4">
                            <label for="sort" class="form-label text-dark small fw-semibold">Sort By</label>
                            <select name="sort" id="sort" class="form-select shadow-sm" onchange="this.form.submit()">
                                <option value="newest" <?php echo e(request('sort') == 'newest' ? 'selected' : ''); ?>>Newest Listed</option>
                                <option value="price_low" <?php echo e(request('sort') == 'price_low' ? 'selected' : ''); ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo e(request('sort') == 'price_high' ? 'selected' : ''); ?>>Price: High to Low</option>
                                <option value="expiring_soon" <?php echo e(request('sort') == 'expiring_soon' ? 'selected' : ''); ?>>Expiring Soonest</option>
                                <option value="discount_high" <?php echo e(request('sort') == 'discount_high' ? 'selected' : ''); ?>>Highest Discount</option>
                            </select>
                        </div>

                        
                        <div class="mb-4">
                            <label for="expiry_hours" class="form-label text-dark small fw-semibold">Time Remaining</label>
                            <select name="expiry_hours" id="expiry_hours" class="form-select shadow-sm">
                                <option value="">Any Time</option>
                                <option value="2" <?php echo e(request('expiry_hours') == '2' ? 'selected' : ''); ?>>Expiring in 2 hours</option>
                                <option value="6" <?php echo e(request('expiry_hours') == '6' ? 'selected' : ''); ?>>Expiring in 6 hours</option>
                                <option value="12" <?php echo e(request('expiry_hours') == '12' ? 'selected' : ''); ?>>Expiring in 12 hours</option>
                                <option value="24" <?php echo e(request('expiry_hours') == '24' ? 'selected' : ''); ?>>Expiring in 24 hours</option>
                            </select>
                        </div>

                        
                        <div class="mb-4">
                            <label for="ai_risk" class="form-label text-dark small fw-semibold">AI Expiry Risk</label>
                            <select name="ai_risk" id="ai_risk" class="form-select shadow-sm">
                                <option value="">All Risks</option>
                                <option value="high" <?php echo e(request('ai_risk') == 'high' ? 'selected' : ''); ?>>🚨 High Risk</option>
                                <option value="medium" <?php echo e(request('ai_risk') == 'medium' ? 'selected' : ''); ?>>⚠️ Medium Risk</option>
                                <option value="low" <?php echo e(request('ai_risk') == 'low' ? 'selected' : ''); ?>>✅ Low Risk</option>
                            </select>
                        </div>

                        
                        <div class="mb-4">
                            <label for="max_price" class="form-label text-dark small fw-semibold">Max Price (Rs.)</label>
                            <input type="number" name="max_price" id="max_price" class="form-control shadow-sm" placeholder="e.g. 500" value="<?php echo e(request('max_price')); ?>">
                        </div>

                        <button type="submit" class="btn btn-success w-100 rounded-pill fw-semibold shadow-sm mt-2">
                            Apply Filters
                        </button>
                    </form>
                </div>
            </div>

            
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <h4 class="fw-bold text-dark mb-0">Active Deals <span class="badge bg-primary rounded-pill fs-6 fw-normal ms-2"><?php echo e($foods->total()); ?> available</span></h4>
                    <span class="text-muted small">Showing <?php echo e($foods->firstItem() ?? 0); ?>–<?php echo e($foods->lastItem() ?? 0); ?> of <?php echo e($foods->total()); ?> results</span>
                </div>

                <div class="row g-4">
                    <?php $__currentLoopData = $foods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $food): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6 col-xl-4">
                            <?php echo $__env->make('partials.food-card', ['food' => $food], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php if($foods->isEmpty()): ?>
                        <div class="col-12 text-center py-5 bg-light rounded-3 shadow-sm border border-dashed">
                            <i class="fa fa-utensils fa-3x text-primary mb-3 opacity-50"></i>
                            <h5 class="fw-bold text-dark">No Deals Found</h5>
                            <p class="text-muted small mb-0">Try clearing filters or search terms to see available offers.</p>
                        </div>
                    <?php endif; ?>
                </div>

                
                <div class="d-flex justify-content-center mt-5">
                    <?php echo e($foods->links('pagination::bootstrap-5')); ?>

                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/public/browse.blade.php ENDPATH**/ ?>