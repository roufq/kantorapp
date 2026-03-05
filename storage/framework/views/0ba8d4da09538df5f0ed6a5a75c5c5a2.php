

<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Buat Laporan</h1>
            <p class="text-muted mb-0">Karyawan melapor ke admin lokasi, lanjut ke Super Admin.</p>
        </div>
        <div>
            <a href="<?php echo e(route('reports.index')); ?>" class="text-decoration-none">Kembali</a>
        </div>
    </div>
</div>
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
          <form action="<?php echo e(route('reports.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php if(auth()->user()->hasRole('Super Admin')): ?>
              <div class="mb-3">
                <label class="form-label">Lokasi</label>
                <select name="location_id" class="form-select" required>
                  <option value="">Pilih lokasi</option>
                  <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($loc->id); ?>" <?php if(old('location_id')==$loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            <?php else: ?>
              <div class="mb-3">
                <label class="form-label">Lokasi</label>
                <input type="text" class="form-control" value="<?php echo e(auth()->user()->location?->name ?? '-'); ?>" disabled>
              </div>
            <?php endif; ?>

            <div class="mb-3">
              <label class="form-label">Judul</label>
              <input type="text" name="title" value="<?php echo e(old('title')); ?>" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Deskripsi</label>
              <textarea name="description" rows="4" class="form-control" required><?php echo e(old('description')); ?></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">Lampiran (boleh banyak, maks 10 MB/file)</label>
              <input type="file" name="attachments[]" class="form-control" multiple>
            </div>

            <div class="d-flex gap-2">
              <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-light">Batal</a>
              <button type="submit" class="btn btn-primary">Kirim Laporan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\reports\create.blade.php ENDPATH**/ ?>