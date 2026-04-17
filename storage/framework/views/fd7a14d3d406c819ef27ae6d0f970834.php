<div class="mt-4 p-4 card shadow-sm border-0 rounded-4 border border-info border-opacity-20 shadow-lg">
  <h5 class="text-dark fw-bold mb-3"><i class="mdi mdi-checkbox-marked-circle-outline me-2 text-success"></i><?php echo e(__('Approval Action')); ?></h5>
  <form action="<?php echo e(route('reports.approve', $report)); ?>" method="POST" class="d-flex flex-column gap-3">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PATCH'); ?>
    <div>
      <label class="form-label text-muted small fw-bold"><?php echo e(__('Notes (optional)')); ?></label>
      <textarea name="notes" rows="3" class="form-control rounded-3 bg-dark bg-opacity-50 border-light text-white" placeholder="<?php echo e(__('Add notes')); ?>"><?php echo e(old('notes')); ?></textarea>
    </div>
    <div class="d-flex gap-3 mt-2">
      <button type="submit" name="status" value="approved" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm flex-grow-1">
        <i class="mdi mdi-check-circle me-1"></i> <?php echo e(__('Approve')); ?>

      </button>
      <button type="submit" name="status" value="rejected" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm flex-grow-1">
        <i class="mdi mdi-close-circle me-1"></i> <?php echo e(__('Reject')); ?>

      </button>
    </div>
  </form>
</div>
<?php /**PATH D:\www\kantorapp\resources\views/reports/partials/_approval_form.blade.php ENDPATH**/ ?>