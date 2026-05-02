<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Edit Contract</h3>
        <p class="text-muted mb-0"><?php echo e($employee_contract->employee?->nama ?? '-'); ?></p>
    </div>
    <a href="<?php echo e(route('employee-contracts.index')); ?>" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo e(route('employee-contracts.update', $employee_contract)); ?>" class="row g-3">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="col-md-6">
                <label class="form-label">Employees</label>
                <select name="employee_id" class="form-select" required>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>" <?php if(old('employee_id', $employee_contract->employee_id) == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Contract Type</label>
                <input type="text" name="contract_type" class="form-control" value="<?php echo e(old('contract_type', $employee_contract->contract_type)); ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="<?php echo e(old('start_date', optional($employee_contract->start_date)->format('Y-m-d'))); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="<?php echo e(old('end_date', optional($employee_contract->end_date)->format('Y-m-d'))); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="active" <?php if(old('status', $employee_contract->status) === 'active'): echo 'selected'; endif; ?>>Active</option>
                    <option value="ended" <?php if(old('status', $employee_contract->status) === 'ended'): echo 'selected'; endif; ?>>Ended</option>
                    <option value="terminated" <?php if(old('status', $employee_contract->status) === 'terminated'): echo 'selected'; endif; ?>>Terminated</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3"><?php echo e(old('notes', $employee_contract->notes)); ?></textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Save</button>
                <a href="<?php echo e(route('employee-contracts.index')); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\employee-contracts\edit.blade.php ENDPATH**/ ?>