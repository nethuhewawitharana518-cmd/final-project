<?php $__env->startSection('title', 'Verify OTP'); ?>

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
                        <div class="fs-1 mb-2">🔐</div>
                        <h3 class="fw-bold mb-1" style="color: #fff;">Verify OTP Code</h3>
                        <p class="text-muted small">Please enter the 6-digit verification code sent to your email <b><?php echo e(session('reset_email')); ?></b></p>
                    </div>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
                            <i class="fa fa-triangle-exclamation text-danger"></i>
                            <div class="small"><?php echo e(session('error')); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if(session('success')): ?>
                        <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
                            <i class="fa fa-check-circle"></i>
                            <div class="small"><?php echo e(session('success')); ?></div>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('password.otp.verify')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-4">
                            <label for="otp" class="form-label text-dark small fw-semibold">Verification Code</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-key"></i></span>
                                <input type="text" name="otp" id="otp" class="form-control border-start-0 ps-0 <?php $__errorArgs = ['otp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    placeholder="Enter 6-digit OTP" required maxlength="6" autofocus style="letter-spacing: 0.1em; font-weight: bold; text-align: center;">
                                <?php $__errorArgs = ['otp'];
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
                            Verify Code
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="<?php echo e(route('password.request')); ?>" class="text-primary small fw-semibold text-decoration-none"><i class="fa fa-arrow-left me-2"></i>Request New OTP</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projectn_dark\resources\views/auth/forgot-password-otp.blade.php ENDPATH**/ ?>