<?php $__env->startSection('title', 'My Cart'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row">
        <div class="col-12 mb-4">
            <h4 class="fw-bold text-dark"><i class="fa fa-shopping-cart text-success me-2"></i>My Shopping Cart</h4>
            <p class="text-muted small">Manage surplus deals before securing reservations.</p>
        </div>
    </div>

    <?php if(empty($cart)): ?>
        <div class="card border-0 shadow-sm p-5 text-center bg-white rounded-3">
            <div class="py-5">
                <i class="fa fa-shopping-basket fa-4x text-muted mb-3"></i>
                <h5 class="fw-bold text-dark">Your cart is currently empty</h5>
                <p class="text-muted small mb-4">Browse hot surplus food deals and rescue food items near you.</p>
                <a href="<?php echo e(route('food.browse')); ?>" class="btn btn-success px-4 py-2 rounded-pill fw-semibold shadow-sm">
                    Browse Deals
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm bg-white rounded-3 p-4">
                    <div class="d-flex justify-between align-items-center mb-3">
                        <span class="text-dark fw-bold">Items from: <span class="text-success"><?php echo e(reset($cart)['business_name']); ?></span></span>
                        <form action="<?php echo e(route('customer.cart.clear')); ?>" method="POST" onsubmit="return confirm('Are you sure you want to clear your cart?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-link text-danger text-decoration-none small p-0">
                                <i class="fa fa-trash-can me-1"></i>Clear Cart
                            </button>
                        </form>
                    </div>
                    <hr class="mt-0">

                    <div class="table-responsive">
                        <table class="table fr-table align-middle">
                            <thead>
                                <tr class="text-muted small uppercase">
                                    <th>Food Item</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center" style="width: 130px;">Quantity</th>
                                    <th class="text-end">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <?php if($item['image']): ?>
                                                    <img src="<?php echo e(str_starts_with($item['image'], 'assets/') ? asset($item['image']) : asset('storage/' . $item['image'])); ?>" alt="<?php echo e($item['name']); ?>" class="rounded-3" style="width: 60px; height: 60px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-success" style="width: 60px; height: 60px; font-size: 1.5rem;">🍲</div>
                                                <?php endif; ?>
                                                <div class="text-start">
                                                    <h6 class="fw-bold text-dark mb-0"><?php echo e($item['name']); ?></h6>
                                                    <small class="text-muted d-block"><?php echo e($item['business_name']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-muted small text-decoration-line-through d-block">Rs. <?php echo e(number_format($item['original_price'], 2)); ?></span>
                                            <span class="fw-bold text-success">Rs. <?php echo e(number_format($item['discount_price'], 2)); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <div class="input-group input-group-sm">
                                                <input type="number" class="form-control text-center quantity-input" 
                                                       data-id="<?php echo e($id); ?>" 
                                                       value="<?php echo e($item['quantity']); ?>" 
                                                       min="1">
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            Rs. <span class="item-total-val" id="total_<?php echo e($id); ?>"><?php echo e(number_format($item['discount_price'] * $item['quantity'], 2)); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <form action="<?php echo e(route('customer.cart.remove', $id)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-link text-danger p-0">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm bg-white rounded-3 p-4">
                    <h5 class="fw-bold text-dark mb-4">Summary</h5>
                    
                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>Items Subtotal</span>
                        <span class="fw-bold text-dark">Rs. <span id="cart-subtotal"><?php echo e(number_format(array_sum(array_map(fn($i) => $i['discount_price'] * $i['quantity'], $cart)), 2)); ?></span></span>
                    </div>
                    <div class="d-flex justify-content-between mb-4 text-muted">
                        <span>Rescue Discount</span>
                        <span class="fw-bold text-success">- Rs. <?php echo e(number_format(array_sum(array_map(fn($i) => ($i['original_price'] - $i['discount_price']) * $i['quantity'], $cart)), 2)); ?></span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-4 fs-5 text-dark fw-bold">
                        <span>Total Due</span>
                        <span>Rs. <span id="cart-total"><?php echo e(number_format(array_sum(array_map(fn($i) => $i['discount_price'] * $i['quantity'], $cart)), 2)); ?></span></span>
                    </div>

                    <a href="<?php echo e(route('customer.checkout')); ?>" class="btn btn-success w-100 py-3 rounded-pill fw-semibold shadow-sm mb-3">
                        Proceed to Checkout <i class="fa fa-arrow-right ms-1"></i>
                    </a>
                    
                    <a href="<?php echo e(route('food.browse')); ?>" class="btn btn-outline-secondary w-100 py-2 rounded-pill fw-semibold">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const qtyInputs = document.querySelectorAll('.quantity-input');
    
    qtyInputs.forEach(input => {
        input.addEventListener('change', function() {
            const itemId = this.dataset.id;
            const newQty = parseInt(this.value);
            
            if (newQty < 1 || isNaN(newQty)) {
                this.value = 1;
                return;
            }

            // Perform AJAX update to update session cart
            fetch(`/customer/cart/${itemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ quantity: newQty })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Simple reload to update totals cleanly
                } else {
                    alert(data.message || 'Error updating cart.');
                    location.reload();
                }
            })
            .catch(err => {
                console.error(err);
                alert('Stock limit exceeded or network error.');
                location.reload();
            });
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/customer/cart.blade.php ENDPATH**/ ?>