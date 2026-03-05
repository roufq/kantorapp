
<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
  <div>
    <h3 class="mb-1">Absence Report</h3>
    <p class="text-muted mb-0">Rekap absensi berdasarkan filter.</p>
  </div>
  <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="card">
  <div class="card-header"><h3 class="card-title">Filter</h3></div>
  <div class="card-body">
    <form method="GET" class="row g-2">
      <div class="col-md-3">
        <label class="form-label">Start Date</label>
        <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" class="form-control"/>
      </div>
      <div class="col-md-3">
        <label class="form-label">End Date</label>
        <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>" class="form-control"/>
      </div>
      <?php if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi')): ?>
      <div class="col-md-3">
        <label class="form-label">User</label>
        <select name="user_id" class="form-select">
          <option value="">All</option>
          <?php
            $uQuery = \App\Models\User::orderBy('name');
            if(auth()->user()->hasRole('Admin Lokasi')){ $uQuery->where('location_id', auth()->user()->location_id); }
            $users = $uQuery->get();
          ?>
          <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($u->id); ?>" <?php if(request('user_id')==$u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <?php endif; ?>
      <div class="col-md-3 d-flex align-items-end">
        <button class="btn btn-primary" type="submit">Apply</button>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header"><h3 class="card-title">Absences</h3></div>
  <div class="card-body table-responsive">
    <table class="table table-bordered table-striped table-sm">
      <thead>
        <tr>
          <th style="width:50px">No</th>
          <th>Date</th>
          <th>User</th>
          <th>Location</th>
          <th>Type</th>
          <th>Notes</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $absences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td><?php echo e($loop->iteration + ($absences->currentPage()-1)*$absences->perPage()); ?></td>
            <td><?php echo e($a->date->format('Y-m-d')); ?></td>
            <td><?php echo e(optional($a->user)->name); ?></td>
            <td><?php echo e(optional($a->location)->name); ?></td>
            <td><span class="badge text-bg-danger"><?php echo e(strtoupper($a->type)); ?></span></td>
            <td><?php echo e($a->notes); ?></td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="5" class="text-center">No data</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="mt-2"><?php echo e($absences->links()); ?></div>
  </div>
  
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\attendances\absences.blade.php ENDPATH**/ ?>