<div class="card h-100 food-card position-relative transition-all hover-translate-y">
    
    <span class="ai-risk-inline <?php echo e(strtolower($food->ai_risk_level)); ?> position-absolute top-0 start-0 m-3 z-3 shadow-sm">
        AI Risk: <?php echo e($food->ai_risk_level); ?>

    </span>

    <?php if($food->is_featured): ?>
        <span class="badge-discount-hero position-absolute top-0 end-0 m-3 z-3 shadow-sm bg-warning text-dark">
            ⭐ Featured
        </span>
    <?php endif; ?>

    
    <div class="position-relative food-card-img">
        <?php if($food->image): ?>
            <img src="<?php echo e(str_starts_with($food->image, 'assets/') ? asset($food->image) : asset('storage/' . $food->image)); ?>" alt="<?php echo e($food->name); ?>">
        <?php else: ?>
            <div class="d-flex align-items-center justify-content-center h-100 bg-light text-primary">
                <i class="fa <?php echo e($food->category->icon ?? 'fa-bowl-food'); ?> fa-4x opacity-50"></i>
            </div>
        <?php endif; ?>
        
        
        <div class="badge-discount-hero position-absolute bottom-0 end-0 m-3 shadow">
            <?php echo e($food->discount_percentage); ?>% OFF
        </div>
    </div>

    <div class="food-card-body">
        
        <div class="d-flex justify-content-between align-items-center mb-2 gap-2 w-100">
            <span class="badge bg-light text-secondary text-uppercase font-monospace small px-2 py-1 border flex-shrink-0">
                <?php echo e($food->category->name ?? 'Other'); ?>

            </span>
            <div class="d-flex align-items-center gap-1 text-end">
                <?php if($food->business && $food->business->badge): ?>
                    <span class="badge rounded-circle shadow-sm px-2
                        <?php echo e($food->business->badge === '1st' ? 'bg-warning text-dark' : ($food->business->badge === '2nd' ? 'bg-secondary text-white' : 'bg-danger text-white')); ?>" 
                        title="Top Rated: <?php echo e($food->business->badge); ?> Place Vendor!" style="font-size: 0.75rem;">
                        <?php if($food->business->badge === '1st'): ?> 🥇
                        <?php elseif($food->business->badge === '2nd'): ?> 🥈
                        <?php elseif($food->business->badge === '3rd'): ?> 🥉
                        <?php endif; ?>
                    </span>
                <?php endif; ?>
                <span class="food-biz-name m-0 text-truncate" style="max-width: 120px; display: inline-block;" title="<?php echo e($food->business->business_name ?? 'Partner'); ?>">
                    <i class="fa fa-store me-1 text-primary"></i><?php echo e($food->business->business_name ?? 'Partner'); ?>

                </span>
            </div>
        </div>

        <?php if($food->business && $food->business->review_count > 0): ?>
            <div class="small text-warning mb-2" style="font-size: 0.8rem;">
                ⭐ <span class="fw-bold"><?php echo e(number_format($food->business->average_rating, 1)); ?></span> 
                <span class="text-muted">(<?php echo e($food->business->review_count); ?> <?php echo e(Str::plural('review', $food->business->review_count)); ?>)</span>
            </div>
        <?php endif; ?>

        
        <h5 class="food-name limit-text-2">
            <?php echo e($food->name); ?>

        </h5>

        
        <p class="text-muted small mb-3 limit-text-3">
            <?php echo e($food->description ?: 'No description provided.'); ?>

        </p>

        
        <div class="d-flex align-items-center gap-2 mb-4 p-2 bg-light rounded text-dark font-monospace small mt-auto border">
            <i class="fa-regular fa-clock text-danger animate-pulse"></i>
            <span>
                <?php if($food->hours_remaining <= 1): ?>
                    <strong class="text-danger">⏱ Expiring soon (<?php echo e(round($food->hours_remaining * 60)); ?>m)</strong>
                <?php else: ?>
                    <span>⏱ <?php echo e(number_format($food->hours_remaining, 1)); ?> hours left</span>
                <?php endif; ?>
            </span>
        </div>

        
        <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top">
            <div>
                <span class="price-original d-block">Rs. <?php echo e(number_format($food->original_price, 2)); ?></span>
                <span class="price-discounted">Rs. <?php echo e(number_format($food->discount_price, 2)); ?></span>
            </div>
            <a href="<?php echo e(route('food.detail', $food->id)); ?>" class="btn btn-success px-4 py-2 shadow-sm">
                View Deal
            </a>
        </div>
    </div>
</div>
<?php /**PATH D:\projectn_dark\resources\views/partials/food-card.blade.php ENDPATH**/ ?>