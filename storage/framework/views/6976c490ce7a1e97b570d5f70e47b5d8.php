

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid">
      <div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
          <h3 class="mb-1">Edit / Rolling Roster</h3>
          <p class="text-muted mb-0"><?php echo e($roster->location->name ?? '-'); ?> | <?php echo e($roster->locationShift->shift->name ?? 'Shift'); ?></p>
        </div>
        <a href="<?php echo e(route('shifts.rosters.index')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
      </div>
      <?php if(session('success')): ?> <div class="alert alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>
      <?php if($errors->any()): ?>
        <div class="alert alert-danger">
          <ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($err); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
        </div>
      <?php endif; ?>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Tukar Karyawan per Tanggal & Slot</h3>
        </div>
        <div class="card-body">
          <form method="POST" action="<?php echo e(route('shifts.rosters.update', $roster)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Tanggal</label>
                <input type="date" name="swap_date" class="form-control" value="<?php echo e($roster->week_start->toDateString()); ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label">Karyawan A</label>
                <select name="swap_user_a" class="form-select">
                  <?php $__currentLoopData = $rosterUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Karyawan B</label>
                <select name="swap_user_b" class="form-select">
                  <?php $__currentLoopData = $rosterUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            </div>
            <div class="mt-3 d-flex justify-content-between">
              <a href="<?php echo e(route('shifts.rosters.show', $roster)); ?>" class="btn btn-outline-secondary">Kembali</a>
              <button type="submit" class="btn btn-primary">Tukar Karyawan</button>
            </div>
          </form>

          <hr>
          <h5>Roster Minggu Ini</h5>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th style="width:50px">No</th>
                  <th>Tanggal</th>
                  <th>Slot</th>
                  <th>Jam</th>
                  <th>Karyawan</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $roster->entries->sortBy(['date','slot_index']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <td><?php echo e($e->date->toDateString()); ?></td>
                    <td><?php echo e($e->slot_index + 1); ?></td>
                    <td>
                      <?php if(isset($slotMap[$e->slot_index])): ?>
                        <?php echo e($slotMap[$e->slot_index]['start'] ?? '?'); ?> - <?php echo e($slotMap[$e->slot_index]['end'] ?? '?'); ?>

                      <?php else: ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </td>
                    <td><?php echo e($e->user->name ?? '-'); ?></td>
                    <td><?php echo e($e->status); ?></td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\shifts\rosters\edit.blade.php ENDPATH**/ ?>