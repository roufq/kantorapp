<div class="mt-4">
  <h6>Approval</h6>
  <form action="<?php echo e(route('reports.approve', $report)); ?>" method="POST" class="d-flex flex-column gap-2">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PATCH'); ?>
    <div>
      <label class="form-label">Catatan (opsional)</label>
      <textarea name="notes" rows="2" class="form-control" placeholder="Tambahkan catatan"><?php echo e(old('notes')); ?></textarea>
    </div>
    <div class="d-flex gap-2">
      <button type="submit" name="status" value="approved" class="btn btn-success">Approve</button>
      <button type="submit" name="status" value="rejected" class="btn btn-danger">Reject</button>
    </div>
  </form>
</div>
<?php /**PATH D:\www\kantorapp\resources\views\reports\partials\_approval_form.blade.php ENDPATH**/ ?>