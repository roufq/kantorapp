<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Edit Approval Rule</h3>
        <p class="text-muted mb-0">Rule ID: <?php echo e($approval_rule->id); ?></p>
    </div>
    <a href="<?php echo e(route('approval-rules.index')); ?>" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo e(route('approval-rules.update', $approval_rule)); ?>" class="row g-3">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="col-md-4">
                <label class="form-label">Scope</label>
                <input type="text" name="scope" class="form-control" value="<?php echo e(old('scope', $approval_rule->scope)); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Department (optional)</label>
                <input type="text" name="department" class="form-control" value="<?php echo e(old('department', $approval_rule->department)); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Min Value</label>
                <input type="number" name="min_value" class="form-control" min="0" value="<?php echo e(old('min_value', $approval_rule->min_value)); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Approval Level</label>
                <select name="approval_level" class="form-select" required>
                    <option value="location_admin" <?php if(old('approval_level', $approval_rule->approval_level) === 'location_admin'): echo 'selected'; endif; ?>>Location Admin</option>
                    <option value="super_admin" <?php if(old('approval_level', $approval_rule->approval_level) === 'super_admin'): echo 'selected'; endif; ?>>Super Admin</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-center">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" <?php if(old('is_active', $approval_rule->is_active)): echo 'checked'; endif; ?>>
                    <label class="form-check-label" for="isActive">Active</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3"><?php echo e(old('notes', $approval_rule->notes)); ?></textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Save</button>
                <a href="<?php echo e(route('approval-rules.index')); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\approval-rules\edit.blade.php ENDPATH**/ ?>