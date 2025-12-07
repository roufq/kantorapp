<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Weekly Rosters</h1>
          <p class="text-muted mb-0">Daftar roster mingguan per lokasi (factory/non-office).</p>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
            <li class="breadcrumb-item active">Rosters</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <?php if(session('success')): ?> <div class="alert alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title">Roster Per Lokasi</h3>
          <a href="<?php echo e(route('shifts.rosters.create')); ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i> Buat Roster</a>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Lokasi</th>
                <th>Shift</th>
                <th>Minggu</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $rosters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr>
                <td><?php echo e($r->location->name ?? '-'); ?></td>
                <td><?php echo e($r->locationShift->shift->name ?? 'Shift'); ?></td>
                <td><?php echo e($r->week_start->toDateString()); ?> s/d <?php echo e($r->week_end->toDateString()); ?></td>
                <td><?php echo $r->locked ? '<span class="badge badge-secondary">Locked</span>' : '<span class="badge badge-success">Active</span>'; ?></td>
                <td class="d-flex gap-2">
                  <a href="<?php echo e(route('shifts.rosters.show', $r)); ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                  <a href="<?php echo e(route('shifts.rosters.edit', $r)); ?>" class="btn btn-sm btn-outline-info">Edit / Rolling</a>
                  <form action="<?php echo e(route('shifts.rosters.destroy', $r)); ?>" method="POST" onsubmit="return confirm('Hapus roster ini?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr><td colspan="5" class="text-center">Belum ada roster.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <div class="card-footer">
          <?php echo e($rosters->links()); ?>

        </div>
      </div>
    </div>
  </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/shifts/rosters/index.blade.php ENDPATH**/ ?>