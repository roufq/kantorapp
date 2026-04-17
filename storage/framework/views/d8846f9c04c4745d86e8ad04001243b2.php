

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Setup Authenticator App</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Step 1:</strong> Install an authenticator app like Google Authenticator, Authy, or Microsoft Authenticator on your phone.
                    </div>

                    <div class="alert alert-info">
                        <strong>Step 2:</strong> Scan the QR code below with your authenticator app:
                    </div>

                    <div class="text-center mb-4">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo e(urlencode($qrCodeUrl)); ?>" alt="QR Code" class="img-fluid">
                    </div>

                    <div class="alert alert-warning">
                        <strong>Can't scan the QR code?</strong> Enter this code manually: <code><?php echo e($qrCodeUrl); ?></code>
                    </div>

                    <div class="alert alert-info">
                        <strong>Step 3:</strong> Enter the 6-digit code from your authenticator app below:
                    </div>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('2fa.verify.post')); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label for="code" class="form-label">Verification Code</label>
                            <input type="text" name="code" id="code" class="form-control"
                                   placeholder="Enter 6-digit code" maxlength="6" required>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Verify & Enable 2FA</button>
                            <a href="<?php echo e(route('2fa.setup')); ?>" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-focus the code input
document.getElementById('code').focus();

// Auto-submit when 6 digits are entered
document.getElementById('code').addEventListener('input', function() {
    if (this.value.length === 6) {
        this.form.submit();
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/auth/2fa/verify-app.blade.php ENDPATH**/ ?>