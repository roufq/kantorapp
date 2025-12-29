

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Setup Two-Factor Authentication</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">Add an extra layer of security to your account by enabling two-factor authentication.</p>

                    <form action="<?php echo e(route('2fa.setup.post')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php
                            $methods = $availableMethods ?? ['email', 'app'];
                        ?>

                        <div class="mb-3">
                            <label for="method" class="form-label">Authentication Method</label>
                            <select name="method" id="method" class="form-control" required>
                                <option value="">Select a method</option>
                                <?php if(in_array('app', $methods)): ?>
                                    <option value="app">Authenticator App (Recommended)</option>
                                <?php endif; ?>
                                <?php if(in_array('email', $methods)): ?>
                                    <option value="email">Email</option>
                                <?php endif; ?>
                                <?php if(in_array('sms', $methods)): ?>
                                    <option value="sms">SMS</option>
                                <?php endif; ?>
                            </select>
                            <?php if(!in_array('sms', $methods)): ?>
                                <small class="text-muted">SMS is unavailable because SMS provider is not configured.</small>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3" id="phone-field" style="display: none;">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="tel" name="phone_number" id="phone_number" class="form-control"
                                   placeholder="+1234567890">
                            <small class="form-text text-muted">Required for SMS authentication</small>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Continue</button>
                            <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-secondary">Skip for Now</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('method').addEventListener('change', function() {
    const phoneField = document.getElementById('phone-field');
    if (this.value === 'sms') {
        phoneField.style.display = 'block';
        document.getElementById('phone_number').required = true;
    } else {
        phoneField.style.display = 'none';
        document.getElementById('phone_number').required = false;
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/auth/2fa/setup.blade.php ENDPATH**/ ?>