

<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Edit Laporan</h1>
            <p class="text-muted mb-0">Perbarui isi laporan sebelum dikirim atau direview.</p>
        </div>
        <div>
            <a href="<?php echo e(route('reports.index')); ?>" class="text-decoration-none">Kembali</a>
        </div>
    </div>
</div>
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <?php if($errors->any()): ?>
        <div class="alert alert-danger">
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
              <label class="form-label">Lokasi</label>
              <input type="text" class="form-control" value="<?php echo e($report->location?->name ?? '-'); ?>" disabled>
            </div>

            <div class="mb-3">
              <label class="form-label">Judul</label>
              <input type="text" name="title" value="<?php echo e(old('title', $report->title)); ?>" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Deskripsi</label>
              <textarea name="description" rows="4" class="form-control" required><?php echo e(old('description', $report->description)); ?></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">Tambah Lampiran (opsional, 10 MB/file)</label>
              <input type="file" name="attachments[]" class="form-control" multiple>
              <?php if($report->attachments->isNotEmpty()): ?>
                <div class="text-secondary small mt-2">Lampiran yang sudah ada tetap tersimpan.</div>
              <?php endif; ?>
            </div>

            <div class="d-flex gap-2">
              <a href="<?php echo e(route('reports.show', $report)); ?>" class="btn btn-light">Batal</a>
              <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\reports\edit.blade.php ENDPATH**/ ?>