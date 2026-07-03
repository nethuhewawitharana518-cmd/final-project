<?php $__env->startSection('title', 'Forgot Password'); ?>
<?php $__env->startSection('meta_description', 'Request a password reset link for your FoodRescue account.'); ?>

<?php $__env->startSection('content'); ?>
<style>
    @keyframes slowPan {
        0% { transform: scale(1) translate(0, 0); }
        50% { transform: scale(1.1) translate(-2%, -2%); }
        100% { transform: scale(1) translate(0, 0); }
    }
    .cinematic-bg {
        position: absolute;
        width: 105%;
        height: 105%;
        object-fit: cover;
        top: -2.5%;
        left: -2.5%;
        z-index: 0;
        animation: slowPan 40s ease-in-out infinite;
    }
</style>

<section class="min-vh-100 d-flex align-items-center position-relative overflow-hidden py-5">
    <!-- Cinematic Slow-Motion Background -->
    <img src="<?php echo e(asset('assets/images/impact_bg.png')); ?>" class="cinematic-bg" alt="Cinematic Background">
    
    <!-- Dark Overlay 75% Opacity -->
    <div class="position-absolute w-100 h-100" style="background: rgba(18, 18, 18, 0.75); top: 0; left: 0; z-index: 1;"></div>
    
    <!-- Form Container -->
    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card p-4 p-md-5 rounded-3" style="background: rgba(30, 30, 30, 0.85); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.7);">
                    <div class="text-center mb-4">
                        <div class="fs-1 mb-2">🔑</div>
                        <h3 class="fw-bold mb-1" style="color: #fff;">Reset Password</h3>
                        <p class="text-muted small">Enter your email and we'll send you a password reset link</p>
                    </div>

                    <?php if(session('success')): ?>
                        <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
                            <i class="fa fa-check-circle"></i>
                            <div class="small"><?php echo e(session('success')); ?></div>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('password.email')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-4">
                            <label for="email" class="form-label text-dark small fw-semibold">Email Address</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-envelope"></i></span>
                                <input type="email" name="email" id="email" class="form-control border-start-0 ps-0 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    placeholder="yourname@example.com" value="<?php echo e(old('email')); ?>" required autofocus>
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-semibold py-3 shadow-sm mb-3">
                            <i class="fa fa-paper-plane me-2"></i>Send Reset Link
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="<?php echo e(route('login')); ?>" class="text-primary small fw-semibold text-decoration-none"><i class="fa fa-arrow-left me-2"></i>Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/auth/forgot-password.blade.php ENDPATH**/ ?>