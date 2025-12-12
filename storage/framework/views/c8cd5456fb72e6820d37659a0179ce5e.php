<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1 class="h4 mb-1">Kalender Shift (Rosters)</h1>
    <p class="text-muted mb-0">Lihat jadwal masuk per lokasi untuk satu minggu berjalan.</p>
  </div>
  <a href="<?php echo e(route('shifts.rosters.index')); ?>" class="text-decoration-none">Kembali ke daftar roster</a>
</div>

<div class="card">
  <div class="card-body">
    <form method="GET" action="<?php echo e(route('shifts.rosters.calendar')); ?>" class="row g-2 align-items-end">
      <?php if($locations->count() > 0): ?>
        <div class="col-md-4">
          <label class="form-label">Lokasi</label>
          <select name="location_id" class="form-control" onchange="this.form.submit()">
            <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($loc->id); ?>" <?php if($loc->id == $locationId): ?> selected <?php endif; ?>><?php echo e($loc->name); ?> (<?php echo e($loc->code); ?>)</option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      <?php endif; ?>
      <div class="col-md-4">
        <label class="form-label">Mulai Minggu</label>
        <input type="date" name="week_start" class="form-control" value="<?php echo e($weekStart->toDateString()); ?>" onchange="this.form.submit()">
      </div>
    </form>

    <div class="table-responsive mt-3">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th style="width:140px">Tanggal</th>
            <th>Jadwal & Karyawan</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $weekData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $rows): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td class="fw-semibold"><?php echo e(\Carbon\Carbon::parse($date)->format('D, d M')); ?></td>
              <td>
                <?php if($rows->isEmpty()): ?>
                  <span class="text-muted">Tidak ada jadwal.</span>
                <?php else: ?>
                  <div class="d-flex flex-column gap-1">
                    <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <div class="border rounded p-2 bg-light">
                        <div class="d-flex justify-content-between flex-wrap">
                          <div>
                            <?php if($row['status'] === 'missing'): ?>
                              <div class="fw-semibold text-danger"><?php echo e($row['time']); ?></div>
                            <?php else: ?>
                              <div class="fw-semibold"><?php echo e($row['user']->name ?? '-'); ?></div>
                              <div class="small text-muted"><?php echo e($row['time']); ?></div>
                            <?php endif; ?>
                          </div>
                          <div>
                            <?php if($row['status'] === 'off'): ?>
                              <span class="badge bg-secondary">OFF</span>
                            <?php elseif($row['status'] === 'leave'): ?>
                              <span class="badge bg-danger">Leave</span>
                            <?php elseif($row['status'] === 'missing'): ?>
                              <span class="badge bg-warning text-dark">Belum ada jadwal</span>
                            <?php else: ?>
                              <span class="badge bg-success text-uppercase"><?php echo e($row['status'] ?? 'scheduled'); ?></span>
                            <?php endif; ?>
                          </div>
                        </div>
                        <?php if(!empty($row['notes'])): ?>
                          <div class="small mt-1"><?php echo e($row['notes']); ?></div>
                        <?php endif; ?>
                      </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </div>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="2" class="text-center text-muted">Tidak ada data.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/shifts/rosters/calendar.blade.php ENDPATH**/ ?>