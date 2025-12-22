

<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Tambah User</h1>
            <p class="text-muted mb-0">Buat akun login untuk karyawan yang sudah terdaftar.</p>
        </div>
        <div>
            <a href="<?php echo e(route('users.index')); ?>" class="text-decoration-none">Kembali</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Add Employee</h3>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('users.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label for="karyawan_id" class="form-label">Select Employee</label>
                        <select name="karyawan_id" class="form-control" id="karyawan_id" required>
                            <option value="">Select Employee</option>
                            <?php $__currentLoopData = $karyawans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $karyawan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($karyawan->id); ?>" data-name="<?php echo e($karyawan->nama); ?>" data-email="<?php echo e($karyawan->email); ?>"><?php echo e($karyawan->nama); ?> - <?php echo e($karyawan->email); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['karyawan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" id="name" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="email" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="password" required>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
                    </div>
                    <div class="mb-3">
                        <label for="location_id" class="form-label">Location</label>
                        <?php if(auth()->user()->hasRole('Admin Lokasi')): ?>
                            <input type="text" class="form-control" value="<?php echo e(optional(auth()->user()->location)->name); ?>" disabled>
                            <input type="hidden" name="location_id" value="<?php echo e(auth()->user()->location_id); ?>">
                        <?php else: ?>
                            <select name="location_id" class="form-control" id="location_id">
                                <option value="">Select Location</option>
                                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($location->id); ?>"><?php echo e($location->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Employee</button>
                    <a href="<?php echo e(route('users.index')); ?>" class="btn btn-secondary">Cancel</a>
                </form>

                <script>
                    document.getElementById('karyawan_id').addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        document.getElementById('name').value = selectedOption.getAttribute('data-name') || '';
                        document.getElementById('email').value = selectedOption.getAttribute('data-email') || '';
                    });
                </script>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/users/create.blade.php ENDPATH**/ ?>