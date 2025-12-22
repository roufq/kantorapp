

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Detail Roster</h1>
          <p class="text-muted mb-0"><?php echo e($roster->location->name ?? '-'); ?> | <?php echo e($roster->locationShift->shift->name ?? 'Shift'); ?></p>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('shifts.rosters.index')); ?>">Rosters</a></li>
            <li class="breadcrumb-item active">Detail</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <?php if(session('success')): ?> <div class="alert alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Minggu <?php echo e($roster->week_start->toDateString()); ?> s/d <?php echo e($roster->week_end->toDateString()); ?></h3>
          <div class="card-tools">
            <a class="btn btn-sm btn-success" href="<?php echo e(route('shifts.rosters.export', $roster)); ?>">
              <i class="bi bi-download me-1"></i> Export Excel
            </a>
          </div>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-bordered align-middle">
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Tanggal</th>
                <th>Slot</th>
                <th>Jam</th>
                <th>Karyawan</th>
                <th>Status</th>
                <th>Catatan</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $roster->entries->sortBy(['date','slot_index']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($loop->iteration); ?></td>
                  <td><?php echo e($e->date->toDateString()); ?></td>
                  <td>
                    <?php if($e->status === 'off'): ?>
                      <span class="text-muted">OFF</span>
                    <?php else: ?>
                      <?php echo e($e->slot_index + 1); ?>

                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if($e->status === 'off'): ?>
                      <span class="text-muted">Hari libur</span>
                    <?php elseif(isset($slotMap[$e->slot_index])): ?>
                      <?php echo e($slotMap[$e->slot_index]['start'] ?? '?'); ?> - <?php echo e($slotMap[$e->slot_index]['end'] ?? '?'); ?>

                    <?php else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo e($e->user->name ?? '-'); ?></td>
                  <td>
                    <?php if($e->status === 'off'): ?>
                      <span class="badge badge-secondary">OFF</span>
                    <?php else: ?>
                      <span class="badge badge-success">Scheduled</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo e($e->notes); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="text-center text-muted">Belum ada entri untuk roster ini.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <div class="card-footer d-flex justify-content-between">
          <a href="<?php echo e(route('shifts.rosters.index')); ?>" class="btn btn-outline-secondary">Kembali</a>
          <a href="<?php echo e(route('shifts.rosters.edit', $roster)); ?>" class="btn btn-info">Edit / Rolling</a>
        </div>
      </div>
    </div>
  </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/shifts/rosters/show.blade.php ENDPATH**/ ?>