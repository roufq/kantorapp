

<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Buat Pengajuan Lembur</h1>
            <p class="text-muted mb-0">Ajukan permintaan lembur dengan durasi dan alasan yang jelas.</p>
        </div>
        <div>
            <a href="<?php echo e(route('overtime.index')); ?>" class="text-decoration-none">Kembali</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Request Overtime</h3>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('overtime.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" id="date" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Start Time (WIB)</label>
                                <input type="time" name="start_time" class="form-control time-24" id="start_time" required step="60" pattern="[0-9]{2}:[0-9]{2}" lang="id-ID" inputmode="numeric" placeholder="HH:MM">
                                <small class="form-text text-muted">Format: HH:MM (24-jam)</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">End Time (WIB)</label>
                                <input type="time" name="end_time" class="form-control time-24" id="end_time" required step="60" pattern="[0-9]{2}:[0-9]{2}" lang="id-ID" inputmode="numeric" placeholder="HH:MM">
                                <small class="form-text text-muted">Format: HH:MM (24-jam)</small>
                            </div>
                        </div>
                    </div>
                    <?php if(isset($limits)): ?>
                    <div class="alert alert-secondary">
                        Batas lembur: <?php echo e($limits['daily']); ?> jam/hari, <?php echo e($limits['weekly']); ?> jam/minggu.
                    </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason for Overtime</label>
                        <textarea name="reason" class="form-control" id="reason" rows="4" required placeholder="Please explain why you need to work overtime..."></textarea>
                    </div>
                    <?php $isEmployee = auth()->user()->hasRole('Karyawan'); ?>

                    <?php if($isEmployee): ?>
                        <div class="mb-3">
                            <label class="form-label">Approvers</label>
                            <div class="card card-body bg-light">
                                <p class="mb-2">Pengajuan Anda akan dikirim ke:</p>
                                <ul class="mb-0">
                                    <?php $__currentLoopData = $autoApprovers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php ($approver = $entry['user']); ?>
                                        <li>
                                            Level <?php echo e($entry['level']); ?> - <?php echo e($approver->name); ?>

                                            <?php if($approver->hasRole('Admin Lokasi')): ?>
                                                <span class="badge bg-info ms-1">Admin Lokasi</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary ms-1">Super Admin</span>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                            <small class="form-text text-muted">Jika tidak ada Admin Lokasi untuk Anda, permintaan akan diteruskan ke Super Admin.</small>
                        </div>
                    <?php else: ?>
                        <div class="mb-3">
                            <label class="form-label">Select up to 2 Super Admins for Approval</label>
                            <div class="row">
                                <?php $__currentLoopData = $masters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $master): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input master-checkbox" type="checkbox" name="selected_masters[]" value="<?php echo e($master->id); ?>" id="master<?php echo e($master->id); ?>">
                                            <label class="form-check-label" for="master<?php echo e($master->id); ?>">
                                                <?php echo e($master->name); ?>

                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <small class="form-text text-muted">Select at least 1 approver (max 2).</small>
                        </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary" id="submitBtn" <?php if(!$isEmployee): ?> disabled <?php endif; ?>>Submit Request</button>
                    <a href="<?php echo e(route('overtime.index')); ?>" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if(!auth()->user()->hasRole('Karyawan')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.master-checkbox');
    const submitBtn = document.getElementById('submitBtn');

    function updateSubmitButton() {
        const checkedCount = document.querySelectorAll('.master-checkbox:checked').length;
        submitBtn.disabled = checkedCount === 0 || checkedCount > 2;
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.master-checkbox:checked').length;
            if (checkedCount > 2) {
                this.checked = false;
                alert('You can select at most 2 approvers.');
            }
            updateSubmitButton();
        });
    });
});
</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\overtime\create.blade.php ENDPATH**/ ?>