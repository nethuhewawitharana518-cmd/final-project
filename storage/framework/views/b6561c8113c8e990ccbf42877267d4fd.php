<?php $__env->startSection('title', 'About Us'); ?>
<?php $__env->startSection('meta_description', 'Learn about FoodRescue — an AI-powered marketplace in Trincomalee dedicated to reducing food waste and supporting the local community.'); ?>

<?php $__env->startSection('content'); ?>
<section class="py-5 bg-light-gradient">
    <div class="container py-5 text-center">
        <h1 class="display-4 fw-bold text-dark mb-3">Our Mission to <span class="text-success text-gradient">Rescue Food</span></h1>
        <p class="lead text-muted max-width-600 mx-auto">
            We are fighting food waste in Trincomalee District by connecting local hotels, bakeries, cafes, and supermarkets with community members.
        </p>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="pe-lg-5">
                    <span class="badge bg-success-subtle text-success text-uppercase fw-bold px-3 py-2 mb-3">Why it matters</span>
                    <h2 class="fw-bold mb-4">The Challenge of Food Waste</h2>
                    <p class="text-muted mb-3">
                        Over one-third of all food produced globally goes to waste, contributing to huge economic loss and carbon emissions. Meanwhile, local families face rising food costs.
                    </p>
                    <p class="text-muted mb-4">
                        In the Trincomalee District, excess foods from high-quality hospitality operations and retailers are often thrown out at the end of the day. <strong>FoodRescue</strong> was built to solve this challenge.
                    </p>
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success text-white p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fa fa-globe fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Supporting UN SDG 12.3</h6>
                            <p class="text-muted small mb-0">Halving per capita global food waste by 2030.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg p-5 bg-success text-white rounded-3 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 opacity-10" style="font-size: 15rem; transform: translate(30%, -30%);">🌱</div>
                    <h3 class="fw-bold mb-4">How Tech Drives Our Impact</h3>
                    <ul class="list-unstyled d-flex flex-column gap-4">
                        <li class="d-flex gap-3">
                            <i class="fa fa-microchip fa-xl mt-1"></i>
                            <div>
                                <h5 class="fw-semibold">AI Expiry Risk Prediction</h5>
                                <p class="opacity-75 small">Our machine learning model analyzes item shelf life to calculate live risk levels, flagging high-risk foods before they go bad.</p>
                            </div>
                        </li>
                        <li class="d-flex gap-3">
                            <i class="fa fa-calculator fa-xl mt-1"></i>
                            <div>
                                <h5 class="fw-semibold">AI Dynamic Pricing</h5>
                                <p class="opacity-75 small">An automated recommendation system suggests optimal discounts to businesses based on remaining hours and category parameters, boosting sales.</p>
                            </div>
                        </li>
                        <li class="d-flex gap-3">
                            <i class="fa fa-qrcode fa-xl mt-1"></i>
                            <div>
                                <h5 class="fw-semibold">Secure QR Verification</h5>
                                <p class="opacity-75 small">All customer reservations generate secure, vector-based SVG QR codes for simple, contact-free pickup verification.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="section-header text-center mb-5">
            <span class="section-badge">How to Join</span>
            <h2 class="section-title">Be a Part of the Solution</h2>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-md-5">
                <div class="card h-100 border-0 shadow-sm p-4 text-center">
                    <div class="fs-1 mb-3">😋</div>
                    <h4 class="fw-bold mb-3">For Savvy Shoppers</h4>
                    <p class="text-muted small mb-4">
                        Buy fresh, high-quality meals, breads, desserts, and groceries at up to 80% off, earn loyalty reward points, and directly reduce landfill food waste.
                    </p>
                    <a href="<?php echo e(route('food.browse')); ?>" class="btn btn-success rounded-pill px-5 mt-auto">Browse Deals</a>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card h-100 border-0 shadow-sm p-4 text-center">
                    <div class="fs-1 mb-3">💼</div>
                    <h4 class="fw-bold mb-3">For Food Businesses</h4>
                    <p class="text-muted small mb-4">
                        List surplus inventory, recover raw ingredient costs, boost public visibility as a green business, and get insights via our AI analytics portal.
                    </p>
                    <a href="<?php echo e(route('register.business')); ?>" class="btn btn-outline-success rounded-pill px-5 mt-auto">Register Business</a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/public/about.blade.php ENDPATH**/ ?>