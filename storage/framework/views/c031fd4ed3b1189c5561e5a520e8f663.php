<?php $__env->startSection('title', 'Reservation Confirmed'); ?>

<?php $__env->startSection('content'); ?>
<div class="py-5" style="background: var(--bg-main);">
    <div class="container">
        <div class="receipt-card">
            
            <div class="receipt-header-banner text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-white text-success rounded-circle mb-3 shadow-sm" style="width: 70px; height: 70px; font-size: 2.2rem;">
                    <i class="fa-solid fa-circle-check animate-bounce"></i>
                </div>
                <h3 class="fw-bold mb-1 text-white">RESCUE TRANSACTION SECURED</h3>
                <p class="mb-0 text-white-50 small">Order Code: <span class="fw-mono text-white"><?php echo e($reservation->reservation_code); ?></span></p>
                
                
                <span class="badge bg-white text-success px-3 py-2 mt-3 rounded-pill fw-bold uppercase shadow-sm" style="letter-spacing: 1px;">
                    <i class="fa fa-shield-halved me-1"></i> PAID & CONFIRMED
                </span>
            </div>

            
            <?php
                $isDelivery = $reservation->delivery_method === 'delivery';
                $address = $isDelivery ? $reservation->delivery_address : '';
                $loyaltyRecord = \App\Models\LoyaltyPoint::where('reservation_id', $reservation->id)->first();
            ?>
            <div class="receipt-body text-start text-light">
                
                <div class="text-center mb-4">
                    <div class="qr-target-box shadow-sm mb-2">
                        <div class="qr-target-corners">
                            <?php if($reservation->qrCode && $reservation->qrCode->qr_image_path): ?>
                                <img src="<?php echo e(asset($reservation->qrCode->qr_image_path)); ?>" alt="Verification QR Code" class="img-fluid bg-white p-2 rounded" style="max-width: 180px;">
                            <?php else: ?>
                                <div class="bg-white p-4 border rounded d-flex align-items-center justify-content-center text-muted" style="width: 180px; height: 180px;">
                                    <i class="fa fa-qrcode fa-3x"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <p class="text-muted small mb-0 mt-1"><i class="fa-solid fa-qrcode text-success me-1"></i>Present this QR Code to the <?php echo e($isDelivery ? 'delivery rider' : 'store merchant'); ?> to verify order fulfillment.</p>
                </div>

                
                <div class="receipt-dashed-divider"></div>

                
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted text-uppercase small mb-3">Fulfillment Details</h6>
                        <ul class="list-unstyled mb-0 text-muted small">
                            <li class="mb-2">
                                <span class="text-muted">Method:</span>
                                <strong class="text-white float-end"><?php echo e($isDelivery ? 'Home Delivery' : 'Store Pickup'); ?></strong>
                            </li>
                            <li class="mb-2">
                                <span class="text-muted">Time / Schedule:</span>
                                <strong class="text-white float-end"><?php echo e($reservation->pickup_time->format('M d, Y H:i')); ?></strong>
                            </li>
                            <li class="mb-2">
                                <span class="text-muted"><?php echo e($isDelivery ? 'Delivery Address:' : 'Store Address:'); ?></span>
                                <strong class="text-white d-block mt-1"><?php echo e($isDelivery ? $address : $reservation->business->address); ?></strong>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted text-uppercase small mb-3">Merchant Info</h6>
                        <ul class="list-unstyled mb-0 text-muted small">
                            <li class="mb-2">
                                <span class="text-muted">Store Partner:</span>
                                <strong class="text-white float-end"><?php echo e($reservation->business->business_name); ?></strong>
                            </li>
                            <li class="mb-2">
                                <span class="text-muted">Store Type:</span>
                                <strong class="text-white float-end"><?php echo e(ucfirst($reservation->business->business_type)); ?></strong>
                            </li>
                            <li class="mb-2">
                                <span class="text-muted">Phone Support:</span>
                                <strong class="text-white float-end"><?php echo e($reservation->business->phone); ?></strong>
                            </li>
                        </ul>
                    </div>
                </div>

                
                <div class="receipt-dashed-divider"></div>

                
                <h6 class="fw-bold text-muted text-uppercase small mb-3">Items Summary</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-borderless table-sm mb-0 text-light small align-middle" style="background: transparent !important; --bs-table-bg: transparent; --bs-table-color: var(--text);">
                        <thead>
                            <tr class="border-bottom pb-2" style="border-color: var(--border) !important;">
                                <th class="text-white ps-0">Item</th>
                                <th class="text-center text-white" style="width: 80px;">Qty</th>
                                <th class="text-end text-white" style="width: 120px;">Unit Price</th>
                                <th class="text-end text-white pe-0" style="width: 120px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $reservation->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="border-bottom-dashed">
                                    <td class="text-white fw-semibold ps-0 py-2"><?php echo e($item->food_name); ?></td>
                                    <td class="text-center text-white py-2"><?php echo e($item->quantity); ?></td>
                                    <td class="text-end text-white py-2">Rs. <?php echo e(number_format($item->unit_price, 2)); ?></td>
                                    <td class="text-end text-white fw-bold pe-0 py-2">Rs. <?php echo e(number_format($item->total_price, 2)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                
                <div class="d-flex justify-content-end">
                    <div class="text-end text-muted small" style="min-width: 280px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Basket Subtotal</span>
                            <span class="text-white fw-semibold">Rs. <?php echo e(number_format($reservation->subtotal, 2)); ?></span>
                        </div>
                        
                        <?php if($reservation->loyalty_discount > 0): ?>
                            <div class="d-flex justify-content-between mb-2 text-danger">
                                <span>Loyalty Points Discount</span>
                                <span class="fw-semibold">- Rs. <?php echo e(number_format($reservation->loyalty_discount, 2)); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if($isDelivery): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Home Delivery Fee</span>
                                <span class="text-white fw-semibold">Rs. <?php echo e(number_format($reservation->delivery_fee, 2)); ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="border-top pt-2 mt-2 d-flex justify-content-between text-white fw-bold fs-5">
                            <span>Grand Total Paid</span>
                            <span class="text-success">Rs. <?php echo e(number_format($reservation->total_amount, 2)); ?></span>
                        </div>
                    </div>
                </div>

                
                <?php if($loyaltyRecord && $loyaltyRecord->points_earned > 0): ?>
                    <div class="alert alert-success border-0 rounded-3 p-3 mt-4 text-center shadow-sm">
                        <i class="fa fa-star text-warning me-2 fs-5 animate-pulse"></i>
                        <span class="fw-semibold">You earned <span class="text-success fw-bold"><?php echo e($loyaltyRecord->points_earned); ?> loyalty points</span> with this purchase!</span>
                        <div class="small text-muted mt-1">New Balance: <strong><?php echo e($loyaltyRecord->balance); ?> points</strong> (Tier: <span class="badge bg-success text-white"><?php echo e(ucfirst($loyaltyRecord->tier)); ?></span>)</div>
                    </div>
                <?php endif; ?>

                
                <div class="receipt-dashed-divider"></div>

                
                <?php if($reservation->payment): ?>
                <?php $payment = $reservation->payment; ?>
                <div class="rounded-3 p-3 mb-4" style="background: rgba(16, 185, 129, 0.1); border: 1.5px solid rgba(16, 185, 129, 0.3);">
                    <h6 class="fw-bold text-success small mb-3 text-uppercase">
                        <i class="fa fa-shield-halved me-1"></i>Payment Confirmed via Stripe
                    </h6>
                    <div class="row g-2 text-muted small">
                        <div class="col-6">
                            <span class="d-block text-muted">Payment Method</span>
                            <strong class="text-white d-flex align-items-center gap-1 mt-1">
                                <i class="<?php echo e($payment->card_brand_icon); ?> fs-5"></i>
                                <?php echo e($payment->card_display); ?>

                            </strong>
                        </div>
                        <div class="col-6">
                            <span class="d-block text-muted">Amount Charged</span>
                            <strong class="text-white">Rs. <?php echo e(number_format($payment->amount, 2)); ?></strong>
                        </div>
                        <div class="col-6">
                            <span class="d-block text-muted">Transaction ID</span>
                            <code class="small text-white" style="font-size:.7rem;word-break:break-all;">
                                <?php echo e($payment->transaction_id ?? 'N/A'); ?>

                            </code>
                        </div>
                        <div class="col-6">
                            <span class="d-block text-muted">Paid At</span>
                            <strong class="text-white"><?php echo e($payment->paid_at?->format('M d, Y H:i') ?? now()->format('M d, Y H:i')); ?></strong>
                        </div>
                        <?php if($payment->card_funding): ?>
                        <div class="col-6">
                            <span class="d-block text-muted">Card Type</span>
                            <strong class="text-white"><?php echo e(ucfirst($payment->card_funding)); ?></strong>
                        </div>
                        <?php endif; ?>
                        <?php if($payment->card_country): ?>
                        <div class="col-6">
                            <span class="d-block text-muted">Card Country</span>
                            <strong class="text-white"><?php echo e(in_array(strtoupper($payment->card_country), ['US', 'LK']) ? 'Sri Lanka' : $payment->card_country); ?></strong>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                
                <div class="text-center">
                    <div class="barcode-line"></div>
                    <span class="text-muted fw-mono small" style="font-size: 0.75rem;">RESERVATION TRANSACTION: <?php echo e($reservation->reservation_code); ?></span>
                </div>
            </div>

            
            <div class="p-4 text-center border-top d-flex flex-wrap gap-3 justify-content-center" style="background: var(--bg-surface-2); border-color: var(--border) !important;">
                <a href="<?php echo e(route('orders.index')); ?>" class="btn btn-success px-4 py-2 rounded-pill fw-semibold shadow-sm">
                    <i class="fa fa-box me-1"></i> View My Orders
                </a>
                <a href="<?php echo e(route('customer.orders.receipt', $reservation->id)); ?>" class="btn btn-outline-success px-4 py-2 rounded-pill fw-semibold">
                    <i class="fa fa-file-pdf me-1"></i> Download Invoice
                </a>
                <a href="<?php echo e(route('home')); ?>" class="btn btn-outline-light px-4 py-2 rounded-pill fw-semibold">
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .receipt-card {
        background: var(--bg-surface);
        border-radius: 20px;
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
        overflow: hidden;
        max-width: 650px;
        margin: 0 auto;
    }
    .receipt-header-banner {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        padding: 40px 30px;
        color: #ffffff;
    }
    .receipt-body {
        padding: 40px 35px;
    }
    .receipt-dashed-divider {
        border-top: 2px dashed var(--border);
        margin: 30px 0;
        position: relative;
    }
    .receipt-dashed-divider::before {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        background: var(--bg-main);
        border-radius: 50%;
        left: -46px;
        top: -10px;
    }
    .receipt-dashed-divider::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        background: var(--bg-main);
        border-radius: 50%;
        right: -46px;
        top: -10px;
    }
    .qr-target-box {
        border: 2px solid var(--border);
        border-radius: 12px;
        padding: 20px;
        background: var(--bg-surface-2);
        display: inline-block;
        position: relative;
    }
    .qr-target-box::before, .qr-target-box::after, .qr-target-corners::before, .qr-target-corners::after {
        content: '';
        position: absolute;
        width: 15px;
        height: 15px;
        border-color: #10B981;
        border-style: solid;
    }
    .qr-target-box::before {
        top: 10px;
        left: 10px;
        border-width: 3px 0 0 3px;
    }
    .qr-target-box::after {
        top: 10px;
        right: 10px;
        border-width: 3px 3px 0 0;
    }
    .qr-target-corners::before {
        bottom: 10px;
        left: 10px;
        border-width: 0 0 3px 3px;
    }
    .qr-target-corners::after {
        bottom: 10px;
        right: 10px;
        border-width: 0 3px 3px 0;
    }
    .barcode-line {
        height: 35px;
        background: repeating-linear-gradient(90deg, #F9FAFB, #F9FAFB 2px, transparent 2px, transparent 6px);
        width: 180px;
        margin: 10px auto;
        opacity: 0.7;
    }
    .border-bottom-dashed {
        border-bottom: 1px dashed var(--border);
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/customer/checkout-success.blade.php ENDPATH**/ ?>