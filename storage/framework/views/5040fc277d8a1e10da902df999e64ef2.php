

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid">
      <div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
          <h3 class="mb-1">Buat Roster Mingguan</h3>
          <p class="text-muted mb-0">Pilih lokasi, shift lokasi, karyawan, dan pola off.</p>
        </div>
        <a href="<?php echo e(route('shifts.rosters.index')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
      </div>
      <?php if($errors->any()): ?>
        <div class="alert alert-danger">
          <ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($err); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
        </div>
      <?php endif; ?>
      <div class="card">
        <div class="card-body">
          
          <form method="GET" action="<?php echo e(route('shifts.rosters.create')); ?>" class="mb-3">
            <div class="row g-3 align-items-end">
              <div class="col-md-4">
                <label class="form-label">Lokasi</label>
                <select name="location_id" class="form-control" onchange="this.form.submit()">
                  <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($loc->id); ?>" <?php if($locationId==$loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name); ?> (<?php echo e($loc->code); ?>)</option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Minggu (mulai)</label>
                <input type="date" name="week_start" class="form-control" value="<?php echo e($weekStart->toDateString()); ?>">
              </div>
              <div class="col-md-4">
                <button class="btn btn-outline-primary" type="submit">Muat Lokasi</button>
              </div>
            </div>
          </form>

          <?php
            $defaultLocationShiftId = old('location_shift_id') ?? ($locationShifts->first()->id ?? null);
            $oldUsers = collect(old('user_ids', []))->map(fn($v)=> (int)$v)->toArray();
            if (empty($oldUsers) && $users->count()) {
                $oldUsers = $users->pluck('id')->toArray(); // auto-select semua karyawan jika belum ada pilihan
            }
          ?>
          <form method="POST" action="<?php echo e(route('shifts.rosters.store')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="location_id" value="<?php echo e($locationId); ?>">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Minggu</label>
                <div class="d-flex gap-2">
                  <input type="date" name="week_start" class="form-control" value="<?php echo e($weekStart->toDateString()); ?>">
                  <input type="date" name="week_end" class="form-control" value="<?php echo e($weekEnd->toDateString()); ?>">
                </div>
              </div>
              <div class="col-md-4">
                <label class="form-label">Weekly Off</label>
                <div class="d-flex gap-2">
                  <input type="number" min="1" name="weekly_off_every" class="form-control" value="<?php echo e(old('weekly_off_every', 6)); ?>">
                  <input type="text" name="weekly_off_label" class="form-control" value="<?php echo e(old('weekly_off_label','OFF')); ?>">
                </div>
                <small class="text-muted">Contoh: 6 berarti 1 hari off setiap 6 hari kerja.</small>
              </div>
            </div>

            <hr>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Shift (Location Shift)</label>
                <select name="location_shift_id" class="form-control" size="8" required>
                  <?php $__empty_1 = true; $__currentLoopData = $locationShifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <option value="<?php echo e($ls->id); ?>" <?php if($defaultLocationShiftId==$ls->id): echo 'selected'; endif; ?>><?php echo e($ls->shift->name ?? 'Shift'); ?> (<?php echo e($ls->id); ?>) - <?php echo e($ls->shift->category ?? '-'); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <option disabled>Belum ada shift di lokasi ini</option>
                  <?php endif; ?>
                </select>
                <small class="text-muted d-block mt-1">Hanya shift kategori Non Office (Factory) yang ditampilkan.</small>
              </div>
              <div class="col-md-6">
                <label class="form-label">Karyawan (lokasi ini)</label>
                <select name="user_ids[]" class="form-control" multiple size="8" required>
                  <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <option value="<?php echo e($u->id); ?>" <?php if(in_array($u->id, $oldUsers)): echo 'selected'; endif; ?>><?php echo e($u->name); ?> (<?php echo e($u->email); ?>)</option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <option disabled>Belum ada karyawan di lokasi ini</option>
                  <?php endif; ?>
                </select>
                <small class="text-muted d-block mt-1">Jika belum dipilih, semua karyawan lokasi ini otomatis dipilih.</small>
              </div>
            </div>

            <div class="mt-3 d-flex justify-content-between">
              <a href="<?php echo e(route('shifts.rosters.index')); ?>" class="btn btn-outline-secondary">Kembali</a>
              <button class="btn btn-primary" type="submit"><i class="fas fa-magic me-1"></i> Generate Roster</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/shifts/rosters/create.blade.php ENDPATH**/ ?>