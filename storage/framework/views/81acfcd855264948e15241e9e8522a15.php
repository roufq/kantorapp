<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid">
      <div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
          <h3 class="mb-1">Weekly Roster</h3>
          <p class="text-muted mb-0">List of weekly rosters per location (factory/non-office).</p>
        </div>
        <a href="<?php echo e(route('shifts.rosters.create')); ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i> Create Roster</a>
      </div>
      <?php if(session('success')): ?> <div class="alert alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title">Roster Per Location</h3>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Location</th>
                <th>Shift</th>
                <th>Week</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $rosters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr>
                <td><?php echo e($loop->iteration + ($rosters->currentPage()-1)*$rosters->perPage()); ?></td>
                <td><?php echo e($r->location->name ?? '-'); ?></td>
                <td><?php echo e($r->locationShift->shift->name ?? 'Shift'); ?></td>
                <td><?php echo e($r->week_start->toDateString()); ?> to <?php echo e($r->week_end->toDateString()); ?></td>
                <td><?php echo $r->locked ? '<span class="badge badge-secondary">Locked</span>' : '<span class="badge badge-success">Active</span>'; ?></td>
                <td class="d-flex gap-2">
                  <a href="<?php echo e(route('shifts.rosters.show', $r)); ?>" class="btn btn-sm btn-outline-primary">Details</a>
                  <a href="<?php echo e(route('shifts.rosters.edit', $r)); ?>" class="btn btn-sm btn-outline-info">Edit / Rolling</a>
                  <form action="<?php echo e(route('shifts.rosters.destroy', $r)); ?>" method="POST" onsubmit="return confirm('Delete this roster?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr><td colspan="6" class="text-center">No rosters found.</td></tr>
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

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/shifts/rosters/index.blade.php ENDPATH**/ ?>