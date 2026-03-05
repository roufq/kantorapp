
<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
  <div>
    <h3 class="mb-1">Attendance Recap</h3>
    <p class="text-muted mb-0">Ringkasan kehadiran berdasarkan filter.</p>
  </div>
</div>
<div class="card">
  <div class="card-header"><h3 class="card-title">Filter</h3></div>
  <div class="card-body">
    <form method="GET" class="row g-2">
      <div class="col-md-3">
        <label class="form-label">Start Date</label>
        <input type="date" name="start_date" class="form-control" value="<?php echo e(request('start_date', $start)); ?>"/>
      </div>
      <div class="col-md-3">
        <label class="form-label">End Date</label>
        <input type="date" name="end_date" class="form-control" value="<?php echo e(request('end_date', $end)); ?>"/>
      </div>
      <?php if(auth()->user()->hasRole('Super Admin')): ?>
      <div class="col-md-3">
        <label class="form-label">Location</label>
        <select name="location_id" class="form-select">
          <option value="">All</option>
          <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($loc->id); ?>" <?php if(request('location_id')==$loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <?php endif; ?>
      <div class="col-md-3">
        <label class="form-label">User (opsional)</label>
        <?php
          $uQuery = \App\Models\User::orderBy('name');
          if(auth()->user()->hasRole('Admin Lokasi')){ $uQuery->where('location_id', auth()->user()->location_id); }
          if(auth()->user()->hasRole('Karyawan')){ $uQuery->where('id', auth()->id()); }
          $users = $uQuery->get();
        ?>
        <select name="user_id" class="form-select">
          <option value="">All</option>
          <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($u->id); ?>" <?php if(request('user_id')==$u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  </div>
  <div class="col-12 d-flex align-items-end gap-2 mt-3">
    <button class="btn btn-primary" type="submit">Apply</button>
    <button class="btn btn-success" type="submit" formaction="<?php echo e(route('attendance.recap.export', array_merge(request()->query(), ['format' => 'xlsx']))); ?>">Export Excel</button>
    <button class="btn btn-outline-success" type="submit" formaction="<?php echo e(route('attendance.recap.export', array_merge(request()->query(), ['format' => 'csv']))); ?>">Export CSV</button>
  </div>
</form>
  </div>
</div>

<div class="card">
  <div class="card-header"><h3 class="card-title">Recap</h3></div>
  <div class="card-body table-responsive">
    <table class="table table-bordered table-striped table-sm">
      <thead>
        <tr>
          <th style="width:50px">No</th>
          <th>User</th>
          <th>Location</th>
          <th>Total Days</th>
          <th>Holiday</th>
          <th>Weekly Off</th>
          <th>Leave</th>
          <th>Working Days</th>
          <th>Present</th>
          <th>Alfa</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td><?php echo e($loop->iteration); ?></td>
          <td><?php echo e($r['user']->name); ?></td>
          <td><?php echo e(optional($r['location'])->name); ?></td>
          <td><?php echo e($r['totalDays']); ?></td>
          <td><?php echo e($r['holidayDays']); ?></td>
          <td><?php echo e($r['weeklyOffDays']); ?></td>
          <td><?php echo e($r['leaveDays']); ?></td>
          <td><?php echo e($r['workingDays']); ?></td>
          <td><?php echo e($r['presentDays']); ?></td>
          <td><?php echo e($r['alphaDays']); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="9" class="text-center">No data</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\attendances\recap.blade.php ENDPATH**/ ?>