

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">Verify Your Email Address</div>

            <div class="card-body">
                <?php if(session('status') === 'verification-link-sent'): ?>
                    <div class="alert alert-success" role="alert">
                        A new verification link has been sent to your email address.
                    </div>
                <?php endif; ?>

                <p>Thanks for signing in! Before getting started, please verify your email address by clicking on the link we just emailed to you. If you didn’t receive the email, we will gladly send you another.</p>

                <form method="POST" action="<?php echo e(route('verification.send')); ?>" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-primary">Resend Verification Email</button>
                </form>

                <form method="POST" action="<?php echo e(route('logout')); ?>" class="d-inline ms-2">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-link">Log Out</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\auth\verify-email.blade.php ENDPATH**/ ?>