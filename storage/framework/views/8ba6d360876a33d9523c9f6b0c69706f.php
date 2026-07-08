<?php $__env->startSection('title', 'Subscription Payment'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex">
    <?php echo $__env->make('business.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Secure Payment Checkout</h4>
            <a href="<?php echo e(route('business.subscription')); ?>" class="btn btn-outline-danger btn-sm px-3">Cancel</a>
        </div>

        <div class="content-area text-start" style="max-width: 600px;">
            <div class="card border-0 shadow-sm bg-white p-4 rounded-3">
                <h5 class="fw-bold mb-3 text-dark">Checkout Details</h5>
                <hr>

                <div class="p-3 border rounded-3 bg-light mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subscription Plan:</span>
                        <span class="fw-bold text-dark"><?php echo e($planDetails['name'] ?? ucfirst($plan)); ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Renewal Interval:</span>
                        <span class="text-dark"><?php echo e($planDetails['duration'] ?? 30); ?> Days</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fs-5 fw-bold">
                        <span class="text-dark">Amount Due:</span>
                        <span class="text-success">Rs. <?php echo e(number_format($planDetails['price'] ?? 0)); ?></span>
                    </div>
                </div>

                <form action="<?php echo e(route('business.subscription.post')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="plan_type" value="<?php echo e($plan); ?>">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Choose Payment Method</label>
                        <div class="form-check p-3 border rounded-3 mb-3 bg-white d-flex align-items-center">
                            <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="payhere" value="payhere" checked>
                            <label class="form-check-label text-dark fw-bold" for="payhere">
                                💳 Credit / Debit Card (Visa, Mastercard, PayHere)
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="card_name" class="form-label fw-semibold">Cardholder Name</label>
                        <input type="text" id="card_name" class="form-control" placeholder="John Doe" pattern="[a-zA-Z\s]+" title="Please enter only letters for the cardholder name" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="card_number" class="form-label fw-semibold">Card Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-credit-card"></i></span>
                            <input type="text" id="card_number" class="form-control border-start-0 ps-0" placeholder="4111 1111 1111 1111" maxlength="19" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-4 text-start">
                            <label for="card_expiry_month" class="form-label fw-semibold">Expiry Month</label>
                            <select id="card_expiry_month" class="form-select" required>
                                <option value="">MM</option>
                                <?php for($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?php echo e(sprintf('%02d', $m)); ?>"><?php echo e(sprintf('%02d', $m)); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-4 text-start">
                            <label for="card_expiry_year" class="form-label fw-semibold">Expiry Year</label>
                            <select id="card_expiry_year" class="form-select" required>
                                <option value="">YYYY</option>
                                <?php for($y = 2026; $y <= 2035; $y++): ?>
                                    <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-4 text-start">
                            <label for="card_cvv" class="form-label fw-semibold">CVV</label>
                            <input type="password" id="card_cvv" class="form-control" placeholder="***" maxlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-3 fw-bold rounded-pill">
                        Complete Secure Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/business/payment.blade.php ENDPATH**/ ?>