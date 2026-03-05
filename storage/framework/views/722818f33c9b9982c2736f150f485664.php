

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid">
      <div class="bg-light p-3 mb-3 rounded border">
        <div>
          <h3 class="mb-1">Shift Scheduler (Non-Office)</h3>
          <p class="text-muted mb-0">Buat jadwal cepat untuk karyawan non-office, termasuk libur mingguan bergilir.</p>
        </div>
      </div>
      <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
      <?php endif; ?>
      <?php if($errors->any()): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li><?php echo e($err); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Generator Jadwal Non-Office</h3>
        </div>
        <form method="POST" action="<?php echo e(route('shifts.scheduler.generate')); ?>">
          <?php echo csrf_field(); ?>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Pilih Lokasi</label>
                <select name="location_id" class="form-control" onchange="this.form.submit()">
                  <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($loc->id); ?>" <?php if($locationId==$loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name); ?> (<?php echo e($loc->code); ?>)</option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Rentang Tanggal</label>
                <div class="d-flex gap-2">
                  <input type="date" name="date_start" class="form-control" value="<?php echo e(old('date_start') ?? now()->toDateString()); ?>">
                  <input type="date" name="date_end" class="form-control" value="<?php echo e(old('date_end') ?? now()->addWeek()->toDateString()); ?>">
                </div>
              </div>
              <div class="col-md-4">
                <label class="form-label">Weekly Off (opsional)</label>
                <div class="d-flex gap-2">
                  <input type="number" min="0" name="weekly_off_every" class="form-control" placeholder="Setiap n hari" value="<?php echo e(old('weekly_off_every', 7)); ?>">
                  <input type="text" name="weekly_off_label" class="form-control" placeholder="Label OFF" value="<?php echo e(old('weekly_off_label','OFF')); ?>">
                </div>
                <small class="text-muted">0 = tidak ada libur otomatis. Contoh: 6 → 1 hari off setiap 6 hari.</small>
              </div>
            </div>

            <hr>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Pilih Karyawan (lokasi ini)</label>
                <select name="user_ids[]" class="form-control" multiple size="8">
                  <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?> (<?php echo e($u->email); ?>)</option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <option disabled>Belum ada karyawan di lokasi ini</option>
                  <?php endif; ?>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Pilih Shift (Location Shift)</label>
                <select name="location_shift_id" class="form-control" size="8">
                  <?php $__empty_1 = true; $__currentLoopData = $locationShifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <option value="<?php echo e($ls->id); ?>">
                      <?php echo e($ls->shift->name ?? 'Shift'); ?> (<?php echo e($ls->id); ?>) - <?php echo e($ls->shift->category ?? '-'); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <option disabled>Belum ada shift terhubung ke lokasi ini</option>
                  <?php endif; ?>
                </select>
                <small class="text-muted d-block mt-2">Shift diambil dari Location Shifts agar slot/jam sudah sesuai lokasi.</small>
              </div>
            </div>
          </div>
          <div class="card-footer d-flex justify-content-between">
            <a href="<?php echo e(route('shifts.rosters.index')); ?>" class="btn btn-outline-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-magic me-1"></i> Generate Jadwal
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\shifts\scheduler.blade.php ENDPATH**/ ?>