<?php $__env->startPush('styles'); ?>
<style>
    /* Flying Food Animations */
    .flying-food {
        position: absolute;
        font-size: 3rem;
        opacity: 0.15;
        animation: floatFood 15s infinite ease-in-out;
        z-index: 0;
        pointer-events: none;
        user-select: none;
    }
    .flying-food.slow {
        animation-duration: 20s;
    }
    .flying-food.fast {
        animation-duration: 12s;
    }
    @keyframes floatFood {
        0% { transform: translateY(0) rotate(0deg); }
        33% { transform: translateY(-30px) rotate(15deg); }
        66% { transform: translateY(15px) rotate(-10deg); }
        100% { transform: translateY(0) rotate(0deg); }
    }
    .flying-food img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        border: 2px solid rgba(255, 107, 0, 0.2);
    }
    .deal-preview-card {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        background: #1E1E1E;
        padding: 1.25rem;
        border-radius: 16px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.4);
        border: 1px solid rgba(255,255,255,0.05);
        margin-bottom: 1.25rem;
        transition: transform 0.3s ease;
    }
    .deal-preview-card:hover {
        transform: translateX(-10px);
        border-color: rgba(255, 107, 0, 0.3);
    }
    .deal-img-photo {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.5);
        border: 1px solid rgba(255,255,255,0.1);
        flex-shrink: 0;
    }
    
    /* Impact Section Parallax */
    .impact-section {
        position: relative;
        background: url('<?php echo e(asset("assets/images/impact_bg.png")); ?>') center center fixed;
        background-size: cover;
        overflow: hidden;
    }
    .impact-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(18, 18, 18, 0.80);
        backdrop-filter: blur(6px);
        z-index: 0;
    }
    .impact-section .container {
        position: relative;
        z-index: 1;
    }
    .impact-num {
        font-size: 3.5rem;
        font-weight: 800;
        color: var(--primary);
        text-shadow: 0 0 25px rgba(255, 107, 0, 0.5);
        margin-bottom: 0.5rem;
    }
</style>
<?php $__env->stopPush(); ?>


<?php $__env->startSection('title', 'Home'); ?>
<?php $__env->startSection('meta_description', 'Discover discounted surplus food from hotels, restaurants, bakeries & cafes in Trincomalee. Save money, reduce food waste.'); ?>

<?php $__env->startSection('content'); ?>


<section class="hero-section">
    <div class="hero-bg-shapes"></div>
    <div class="container py-5">
        <div class="row align-items-center min-vh-80 py-5">
            <div class="col-lg-6 text-white" data-aos="fade-right">
                <div class="badge-pill mb-3">
                    <span class="dot-pulse"></span>
                    <span>Live Deals Available Now in Trincomalee</span>
                </div>
                <h1 class="hero-title mb-4">
                    Save Food.<br>Save Money.<br>
                    <span class="text-gradient">Save Trincomalee.</span>
                </h1>
                <p class="hero-sub mb-4">
                    Discover deeply discounted surplus food from top hotels, restaurants, bakeries and supermarkets in Trincomalee — AI-powered, QR-verified, community-driven.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?php echo e(route('food.browse')); ?>" class="btn btn-success btn-hero px-4">
                        <i class="fa fa-search me-2"></i>Browse Deals
                    </a>
                    <a href="<?php echo e(route('map')); ?>" class="btn btn-outline-light btn-hero px-4">
                        <i class="fa fa-map-location-dot me-2"></i>Live Map
                    </a>
                    <a href="<?php echo e(route('register')); ?>" class="btn btn-link btn-hero px-4 text-white text-decoration-none">
                        <i class="fa fa-user-plus me-2"></i>Get Started
                    </a>
                </div>

                
                <div class="d-flex flex-wrap gap-4 mt-5">
                    <div class="stat-badge">
                        <div class="stat-number counter" data-target="1240">0</div>
                        <div class="stat-label">kg Food Saved</div>
                    </div>
                    <div class="stat-badge">
                        <div class="stat-number counter" data-target="340">0</div>
                        <div class="stat-label">Happy Customers</div>
                    </div>
                    <div class="stat-badge">
                        <div class="stat-number counter" data-target="48">0</div>
                        <div class="stat-label">Partner Businesses</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mt-5 mt-lg-0" data-aos="fade-left">
                <div class="hero-card-stack">
                    
                    <div class="deal-preview-card card-1">
                        <img src="<?php echo e(asset('assets/images/biryani.png')); ?>" alt="Biryani" class="deal-img-photo">
                        <div>
                            <div class="fw-semibold">Fish Biryani</div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted text-decoration-line-through small">Rs.450</span>
                                <span class="text-success fw-bold">Rs.270</span>
                                <span class="badge-discount-hero">40% OFF</span>
                            </div>
                            <div class="countdown-mini" data-expiry="4">⏱ 4 hrs left</div>
                        </div>
                    </div>
                    <div class="deal-preview-card card-2">
                        <img src="<?php echo e(asset('assets/images/pastries.png')); ?>" alt="Pastries" class="deal-img-photo">
                        <div>
                            <div class="fw-semibold">Assorted Pastries</div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted text-decoration-line-through small">Rs.350</span>
                                <span class="text-success fw-bold">Rs.140</span>
                                <span class="badge-discount-hero">60% OFF</span>
                            </div>
                            <div class="countdown-mini">⏱ 2 hrs left</div>
                        </div>
                    </div>
                    <div class="deal-preview-card card-3">
                        <img src="<?php echo e(asset('assets/images/cake.png')); ?>" alt="Cake" class="deal-img-photo">
                        <div>
                            <div class="fw-semibold">Chocolate Cake Slice</div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted text-decoration-line-through small">Rs.280</span>
                                <span class="text-success fw-bold">Rs.196</span>
                                <span class="badge-discount-hero" style="background: var(--amber);">30% OFF</span>
                            </div>
                            <div class="countdown-mini">⏱ 6 hrs left</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-5 bg-white position-relative overflow-hidden">
    <!-- Flying Food Elements -->
    <div class="flying-food slow" style="top: 15%; left: 5%; animation-delay: 0s; opacity: 0.6;"><img src="<?php echo e(asset('assets/images/burger.png')); ?>" alt="Burger"></div>
    <div class="flying-food fast" style="top: 65%; left: 15%; animation-delay: -3s; opacity: 0.6;"><img src="<?php echo e(asset('assets/images/cake.png')); ?>" alt="Cake"></div>
    <div class="flying-food" style="top: 25%; right: 10%; animation-delay: -5s; opacity: 0.5;"><img src="<?php echo e(asset('assets/images/biryani.png')); ?>" alt="Biryani"></div>
    <div class="flying-food slow" style="top: 75%; right: 20%; animation-delay: -2s; opacity: 0.7;"><img src="<?php echo e(asset('assets/images/pastries.png')); ?>" alt="Pastries"></div>
    
    <div class="container position-relative" style="z-index: 1;">
        <div class="section-header text-center mb-5">
            <span class="section-badge">Explore</span>
            <h2 class="section-title">Browse by Category</h2>
        </div>
        <div class="row g-3 justify-content-center">
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-6 col-sm-4 col-md-3 col-lg-auto">
                <a href="<?php echo e(route('food.browse', ['category' => $cat->slug])); ?>" class="category-card">
                    <div class="category-icon"><i class="fa <?php echo e($cat->icon); ?>"></i></div>
                    <div class="category-name"><?php echo e($cat->name); ?></div>
                    <div class="category-count text-muted small"><?php echo e($cat->foods()->active()->count()); ?> deals</div>
                </a>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="py-5 section-bg-alt">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="section-badge">🔥 Hot Deals</span>
                <h2 class="section-title mt-1">Expiring Soon — Act Fast!</h2>
            </div>
            <a href="<?php echo e(route('food.browse')); ?>" class="btn btn-outline-success">View All</a>
        </div>

        <div class="row g-4">
            <?php $__currentLoopData = $expiringSoonFoods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $food): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-sm-6 col-lg-3">
                <?php echo $__env->make('partials.food-card', ['food' => $food], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if($expiringSoonFoods->isEmpty()): ?>
            <div class="col-12 text-center py-5">
                <div class="text-muted">
                    <i class="fa fa-leaf fa-3x mb-3 text-primary"></i>
                    <p>No deals expiring soon. Check back later!</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>


<section class="py-5 bg-white position-relative overflow-hidden">
    <!-- Flying Food Elements -->
    <div class="flying-food fast" style="top: 20%; left: 8%; animation-delay: -1s; opacity: 0.6;"><img src="<?php echo e(asset('assets/images/pastries.png')); ?>" alt="Pastries"></div>
    <div class="flying-food slow" style="top: 70%; left: 20%; animation-delay: -4s; opacity: 0.5;"><img src="<?php echo e(asset('assets/images/biryani.png')); ?>" alt="Biryani"></div>
    <div class="flying-food" style="top: 15%; right: 15%; animation-delay: -2s; opacity: 0.7;"><img src="<?php echo e(asset('assets/images/burger.png')); ?>" alt="Burger"></div>
    <div class="flying-food fast" style="top: 75%; right: 5%; animation-delay: -6s; opacity: 0.6;"><img src="<?php echo e(asset('assets/images/cake.png')); ?>" alt="Cake"></div>

    <div class="container position-relative" style="z-index: 1;">
        <div class="section-header text-center mb-5">
            <span class="section-badge">Simple & Easy</span>
            <h2 class="section-title">How FoodRescue Works</h2>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="how-card">
                    <div class="how-step">1</div>
                    <div class="how-icon">🔍</div>
                    <h5 class="fw-semibold mt-3">Discover Deals</h5>
                    <p class="text-muted small">Browse discounted surplus food from local restaurants, bakeries, hotels, and supermarkets near you.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="how-card">
                    <div class="how-step">2</div>
                    <div class="how-icon">💳</div>
                    <h5 class="fw-semibold mt-3">Reserve & Pay</h5>
                    <p class="text-muted small">Add to cart, choose your pickup time, and pay securely online. Earn loyalty points on every order.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="how-card">
                    <div class="how-step">3</div>
                    <div class="how-icon">📱</div>
                    <h5 class="fw-semibold mt-3">Collect with QR</h5>
                    <p class="text-muted small">Receive your unique QR code. Visit the business, show your QR, and collect your food. That's it!</p>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-5 section-bg-alt">
    <div class="container">
        <div class="section-header text-center mb-5">
            <span class="section-badge">⭐ Premium Partners</span>
            <h2 class="section-title">Featured Businesses</h2>
        </div>
        <div class="row g-4">
            <?php $__currentLoopData = $featuredBusinesses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $biz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-sm-6 col-lg-4">
                <div class="business-card">
                    <div class="biz-banner" style="background: linear-gradient(135deg, var(--primary), var(--primary-dark))">
                        <div class="biz-type-badge"><?php echo e(ucfirst($biz->business_type)); ?></div>
                        <div class="featured-badge-star">⭐ Featured</div>
                    </div>
                    <div class="biz-body">
                        <h6 class="fw-bold"><?php echo e($biz->business_name); ?></h6>
                        <p class="text-muted small mb-2"><i class="fa fa-map-pin me-1 text-primary"></i><?php echo e(Str::limit($biz->address, 45)); ?></p>
                        <p class="text-muted small mb-3"><?php echo e($biz->foods()->active()->count()); ?> active deals</p>
                        <a href="<?php echo e(route('food.browse', ['business' => $biz->id])); ?>" class="btn btn-success btn-sm w-100">View Deals</a>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="impact-section py-5">
    <div class="container text-center text-white py-3">
        <h2 class="fw-bold mb-2 text-white">Our Impact in Trincomalee</h2>
        <p class="opacity-75 mb-5 text-light">Together we're fighting food waste, one deal at a time.</p>
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="impact-num counter" data-target="1240">0</div>
                <div class="impact-label text-light">kg Food Rescued</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="impact-num counter" data-target="2480">0</div>
                <div class="impact-label text-light">kg CO₂ Saved</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="impact-num counter" data-target="8600">0</div>
                <div class="impact-label text-light">Rs. Customer Savings</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="impact-num counter" data-target="340">0</div>
                <div class="impact-label text-light">Orders Completed</div>
            </div>
        </div>
    </div>
</section>


<section class="py-5 bg-white">
    <div class="container">
        <div class="cta-card text-center p-5">
            <h2 class="fw-bold mb-2">Own a Restaurant, Bakery, or Hotel?</h2>
            <p class="text-muted mb-4">Turn unsold food into revenue. Join 48+ businesses already saving with FoodRescue.</p>
            <div class="row g-3 justify-content-center mb-4">
                <div class="col-auto">
                    <div class="plan-pill">Starter — Rs.2,000/mo</div>
                </div>
                <div class="col-auto">
                    <div class="plan-pill popular">Professional — Rs.5,000/mo ⭐</div>
                </div>
                <div class="col-auto">
                    <div class="plan-pill">Enterprise — Rs.10,000/mo</div>
                </div>
            </div>
            <?php if(auth()->check() && auth()->user()->isBusinessOwner()): ?>
                <a href="<?php echo e(route('business.subscription')); ?>" class="btn btn-success btn-lg px-5">
                    <i class="fa fa-credit-card me-2"></i>View Subscription Plans
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('register.business')); ?>" class="btn btn-success btn-lg px-5">
                    <i class="fa fa-rocket me-2"></i>Start Free Registration
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Counter animation
document.querySelectorAll('.counter').forEach(el => {
    const target = +el.dataset.target;
    const duration = 2000;
    const step = target / (duration / 16);
    let count = 0;
    const update = () => {
        count = Math.min(count + step, target);
        el.textContent = Math.floor(count).toLocaleString();
        if (count < target) requestAnimationFrame(update);
    };
    const observer = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting) { update(); observer.disconnect(); }
    });
    observer.observe(el);
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/public/home.blade.php ENDPATH**/ ?>