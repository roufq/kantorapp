<?php $__env->startSection('content'); ?>
<?php
  $daysOptions = [7, 30, 90, 180];
?>
<div class="bg-light p-3 mb-3 rounded border">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
      <h1 class="h4 mb-1">Employee Performance</h1>
      <p class="text-muted mb-0">KPI summary per employee in the selected period.</p>
    </div>
    <div>
      <a href="<?php echo e(route('reports.index')); ?>" class="text-decoration-none">Back to Reports</a>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
      <div class="text-muted small">Export summary of employee performance based on filters.</div>
      <a class="btn btn-sm btn-success" href="<?php echo e(route('reports.employee-performance.export', request()->query())); ?>">
        Export Excel
      </a>
    </div>
    <form method="GET" action="<?php echo e(route('reports.employee-performance')); ?>" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label fw-semibold">Period</label>
        <select name="days" class="form-select">
          <?php $__currentLoopData = $daysOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($opt); ?>" <?php echo e((int) $days === $opt ? 'selected' : ''); ?>><?php echo e($opt); ?> days</option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <?php if(!empty($locations)): ?>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Location</label>
          <select name="location_id" class="form-select">
            <option value="">All</option>
            <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($loc->id); ?>" <?php echo e((string) $locationId === (string) $loc->id ? 'selected' : ''); ?>><?php echo e($loc->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      <?php endif; ?>
      <div class="col-md-3">
        <label class="form-label fw-semibold">Employees</label>
        <select name="employee_id" class="form-select">
          <option value="">All</option>
          <?php $__currentLoopData = $employeeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($emp->id); ?>" <?php echo e((string) $employeeId === (string) $emp->id ? 'selected' : ''); ?>><?php echo e($emp->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold">Search Name</label>
        <input type="text" name="search" value="<?php echo e($search); ?>" class="form-control" placeholder="Employee Name">
      </div>
      <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Apply</button>
        <a href="<?php echo e(route('reports.employee-performance')); ?>" class="btn btn-outline-secondary">Reset</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped align-middle mb-0">
        <thead>
          <tr>
            <th style="width:50px">No</th>
            <th>Name</th>
            <th>Location</th>
            <th>Attendance</th>
            <th>Lateness</th>
            <th>Overtime</th>
            <th>Min Presence</th>
            <th>Output Efficiency</th>
            <th>Task Productivity</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
              $metrics = $metricsByUser[$emp->id] ?? [
                'attendance_rate' => 0,
                'attendance_days' => 0,
                'attendance_count' => 0,
                'late_count' => 0,
                'lateness_rate' => 0,
                'overtime_hours' => 0,
                'tasks_created' => 0,
                'tasks_completed' => 0,
                'task_productivity_rate' => 0,
                'output_points' => 0,
                'target_points' => 0,
                'output_efficiency_rate' => 0,
                'attendance_minutes' => 0,
                'min_attendance_minutes' => 0,
                'min_attendance_met' => null,
              ];
            ?>
            <tr>
              <td><?php echo e($loop->iteration + ($users->currentPage() - 1) * $users->perPage()); ?></td>
              <td class="text-break"><?php echo e($emp->name); ?></td>
              <td><?php echo e(optional($emp->location)->name ?? '-'); ?></td>
              <td>
                <span class="fw-semibold"><?php echo e(number_format($metrics['attendance_rate'], 2)); ?>%</span>
                <div class="text-muted small"><?php echo e($metrics['attendance_days']); ?> days</div>
              </td>
              <td>
                <span class="fw-semibold"><?php echo e(number_format($metrics['lateness_rate'], 2)); ?>%</span>
                <div class="text-muted small"><?php echo e($metrics['late_count']); ?> of <?php echo e($metrics['attendance_count']); ?></div>
              </td>
              <td>
                <span class="fw-semibold"><?php echo e(number_format($metrics['overtime_hours'], 2)); ?> hrs</span>
              </td>
              <td>
                <?php if(is_null($metrics['min_attendance_met'])): ?>
                  <span class="text-muted">-</span>
                <?php else: ?>
                  <span class="fw-semibold"><?php echo e($metrics['attendance_minutes']); ?> / <?php echo e($metrics['min_attendance_minutes']); ?> mins</span>
                  <div class="text-muted small"><?php echo e($metrics['min_attendance_met'] ? 'Met' : 'Not Met'); ?></div>
                <?php endif; ?>
              </td>
              <td>
                <span class="fw-semibold"><?php echo e(number_format($metrics['output_efficiency_rate'], 2)); ?>%</span>
                <div class="text-muted small"><?php echo e(number_format((float) $metrics['output_points'], 2)); ?> / <?php echo e($metrics['target_points']); ?> points</div>
              </td>
              <td>
                <span class="fw-semibold"><?php echo e(number_format($metrics['task_productivity_rate'], 2)); ?>%</span>
                <div class="text-muted small"><?php echo e($metrics['tasks_completed']); ?> / <?php echo e($metrics['tasks_created']); ?></div>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="9" class="text-center text-muted">No employee data found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php if($users->hasPages()): ?>
    <div class="p-3">
      <?php echo e($users->links()); ?>

    </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\reports\employee-performance.blade.php ENDPATH**/ ?>