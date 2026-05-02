<?php $__env->startSection('content'); ?>
      <div class="row mb-4 align-items-center">
        <div class="col-lg-6">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;"><?php echo e(__('Edit Report')); ?></h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;"><?php echo e(__('Update report content before sending or reviewing.')); ?></p>
        </div>
        <div class="col-lg-6 text-lg-end mt-3 mt-lg-0">
          <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-arrow-left me-2 fs-5 align-middle"></i><?php echo e(__('Back')); ?>

          </a>
        </div>
      </div>

<div class="container-fluid">
  <?php if($errors->any()): ?>
    <div class="alert alert-danger rounded-pill px-4">
      <ul class="mb-0">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <form action="<?php echo e(route('reports.update', $report)); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="mb-3">
          <label class="form-label fw-bold text-dark small"><?php echo e(__('Location')); ?></label>
          <input type="text" class="form-control rounded-pill px-4" value="<?php echo e($report->location?->name ?? '-'); ?>" disabled>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold text-dark small"><?php echo e(__('Title')); ?></label>
          <input type="text" name="title" value="<?php echo e(old('title', $report->title)); ?>" class="form-control rounded-pill px-4" required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold text-dark small"><?php echo e(__('Description')); ?></label>
          <textarea name="description" rows="4" class="form-control" required style="border-radius: 15px !important;"><?php echo e(old('description', $report->description)); ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold text-dark small"><?php echo e(__('Add Attachment (optional, 10 MB/file)')); ?></label>
          <input type="file" name="attachments[]" class="form-control rounded-pill px-4" multiple>
          <?php if($report->attachments->isNotEmpty()): ?>
            <div class="text-info small mt-2"><i class="mdi mdi-information-outline me-1"></i><?php echo e(__('Existing attachments are still stored.')); ?></div>
          <?php endif; ?>
        </div>

        <div class="d-flex gap-3 mt-4">
          <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">
            <i class="mdi mdi-content-save-outline me-2 fs-5 align-middle"></i><?php echo e(__('Save Changes')); ?>

          </button>
          <a href="<?php echo e(route('reports.show', $report)); ?>" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
            <?php echo e(__('Cancel')); ?>

          </a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\reports\edit.blade.php ENDPATH**/ ?>