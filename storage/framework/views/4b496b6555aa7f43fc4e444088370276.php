<?php $__env->startSection('content'); ?>
<?php
  $sections = ['lateness', 'attendance', 'overtime', 'tasks'];
  $activeSection = in_array($section ?? '', $sections, true) ? $section : 'lateness';
  $daysOptions = [7, 30, 90, 180];
?>

<div class="bg-light p-3 mb-3 rounded border">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
      <h1 class="h4 mb-1">KPI Dasar</h1>
      <p class="text-muted mb-0">Keterlambatan, kehadiran, overtime, dan produktivitas tugas.</p>
    </div>
    <div>
      <a href="<?php echo e(route('dashboard')); ?>" class="text-decoration-none">Kembali ke Dashboard</a>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="<?php echo e(route('kpi.index')); ?>" class="d-flex flex-wrap align-items-end gap-2">
      <div>
        <label class="form-label fw-semibold mb-1">Periode KPI</label>
        <select name="days" class="form-select">
          <?php $__currentLoopData = $daysOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($opt); ?>" <?php echo e((int) $days === $opt ? 'selected' : ''); ?>><?php echo e($opt); ?> hari</option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <input type="hidden" name="section" value="<?php echo e($activeSection); ?>">
      <button type="submit" class="btn btn-primary">Terapkan</button>
    </form>
  </div>
</div>

<?php
  $summaryCards = [
    [
      'label' => 'Keterlambatan',
      'value' => number_format($kpiMetrics['lateness_rate'] ?? 0, 2) . '%',
      'meta' => ($kpiMetrics['late_count'] ?? 0) . ' / ' . ($kpiMetrics['attendance_count'] ?? 0),
      'color' => 'danger',
      'icon' => 'mdi-clock-alert',
    ],
    [
      'label' => 'Kehadiran',
      'value' => number_format($kpiMetrics['attendance_rate'] ?? 0, 2) . '%',
      'meta' => ($kpiMetrics['attendance_count'] ?? 0) . ' hadir',
      'color' => 'success',
      'icon' => 'mdi-account-check',
    ],
    [
      'label' => 'Overtime Disetujui',
      'value' => number_format($kpiMetrics['overtime_hours'] ?? 0, 2) . ' jam',
      'meta' => $kpiMetrics['period_label'] ?? '',
      'color' => 'warning',
      'icon' => 'mdi-timer',
    ],
    [
      'label' => 'Produktivitas Tugas',
      'value' => number_format($kpiMetrics['task_productivity_rate'] ?? 0, 2) . '%',
      'meta' => ($kpiMetrics['tasks_completed'] ?? 0) . ' / ' . ($kpiMetrics['tasks_created'] ?? 0),
      'color' => 'primary',
      'icon' => 'mdi-clipboard-check',
    ],
  ];
?>

<div class="row">
  <?php $__currentLoopData = $summaryCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-md-6 col-xl-3">
      <div class="card m-b-30">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
              <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center bg-<?php echo e($card['color']); ?> text-white">
                <i class="mdi <?php echo e($card['icon']); ?>"></i>
              </div>
            </div>
            <div class="flex-grow-1 text-right">
              <p class="text-muted mb-1"><?php echo e($card['label']); ?></p>
              <h4 class="mb-0"><?php echo e($card['value']); ?></h4>
              <?php if(!empty($card['meta'])): ?>
                <small class="text-muted"><?php echo e($card['meta']); ?></small>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="card">
  <div class="card-header">
    <ul class="nav nav-tabs card-header-tabs" role="tablist">
      <li class="nav-item">
        <a class="nav-link <?php echo e($activeSection === 'lateness' ? 'active' : ''); ?>" data-toggle="tab" href="#tab-lateness" role="tab">Keterlambatan</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo e($activeSection === 'attendance' ? 'active' : ''); ?>" data-toggle="tab" href="#tab-attendance" role="tab">Kehadiran</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo e($activeSection === 'overtime' ? 'active' : ''); ?>" data-toggle="tab" href="#tab-overtime" role="tab">Overtime</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo e($activeSection === 'tasks' ? 'active' : ''); ?>" data-toggle="tab" href="#tab-tasks" role="tab">Produktivitas Tugas</a>
      </li>
    </ul>
  </div>
  <div class="card-body">
    <div class="tab-content">
      <div class="tab-pane fade <?php echo e($activeSection === 'lateness' ? 'show active' : ''); ?>" id="tab-lateness" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
          <h5 class="mb-0">Daftar Keterlambatan</h5>
          <div class="d-flex gap-2">
            <a href="<?php echo e(route('kpi.export', ['section' => 'lateness', 'days' => $days, 'format' => 'xlsx'])); ?>" class="btn btn-sm btn-success">Export XLSX</a>
            <a href="<?php echo e(route('kpi.export', ['section' => 'lateness', 'days' => $days, 'format' => 'csv'])); ?>" class="btn btn-sm btn-outline-success">Export CSV</a>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-striped align-middle mb-0">
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Nama</th>
                <th>Lokasi</th>
                <th>Shift</th>
                <th>Check In</th>
                <th>Check Out</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $lateAttendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($loop->iteration + ($lateAttendances->currentPage() - 1) * $lateAttendances->perPage()); ?></td>
                  <td><?php echo e(optional($att->user)->name ?? '-'); ?></td>
                  <td><?php echo e(optional($att->location)->name ?? '-'); ?></td>
                  <td><?php echo e(optional($att->shift)->name ?? '-'); ?></td>
                  <td><?php echo e(optional($att->check_in_time)->format('Y-m-d H:i') ?? '-'); ?></td>
                  <td><?php echo e(optional($att->check_out_time)->format('Y-m-d H:i') ?? '-'); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="text-center text-muted">Tidak ada data keterlambatan.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <?php if($lateAttendances->hasPages()): ?>
          <div class="mt-3">
            <?php echo e($lateAttendances->appends(['section' => 'lateness', 'days' => $days])->links()); ?>

          </div>
        <?php endif; ?>
      </div>

      <div class="tab-pane fade <?php echo e($activeSection === 'attendance' ? 'show active' : ''); ?>" id="tab-attendance" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
          <h5 class="mb-0">Daftar Kehadiran</h5>
          <div class="d-flex gap-2">
            <a href="<?php echo e(route('kpi.export', ['section' => 'attendance', 'days' => $days, 'format' => 'xlsx'])); ?>" class="btn btn-sm btn-success">Export XLSX</a>
            <a href="<?php echo e(route('kpi.export', ['section' => 'attendance', 'days' => $days, 'format' => 'csv'])); ?>" class="btn btn-sm btn-outline-success">Export CSV</a>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-striped align-middle mb-0">
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Nama</th>
                <th>Lokasi</th>
                <th>Shift</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Terlambat</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $attendanceList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($loop->iteration + ($attendanceList->currentPage() - 1) * $attendanceList->perPage()); ?></td>
                  <td><?php echo e(optional($att->user)->name ?? '-'); ?></td>
                  <td><?php echo e(optional($att->location)->name ?? '-'); ?></td>
                  <td><?php echo e(optional($att->shift)->name ?? '-'); ?></td>
                  <td><?php echo e(optional($att->check_in_time)->format('Y-m-d H:i') ?? '-'); ?></td>
                  <td><?php echo e(optional($att->check_out_time)->format('Y-m-d H:i') ?? '-'); ?></td>
                  <td><?php echo e($att->is_late ? 'Ya' : 'Tidak'); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="text-center text-muted">Tidak ada data kehadiran.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <?php if($attendanceList->hasPages()): ?>
          <div class="mt-3">
            <?php echo e($attendanceList->appends(['section' => 'attendance', 'days' => $days])->links()); ?>

          </div>
        <?php endif; ?>
      </div>

      <div class="tab-pane fade <?php echo e($activeSection === 'overtime' ? 'show active' : ''); ?>" id="tab-overtime" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
          <h5 class="mb-0">Overtime Disetujui</h5>
          <div class="d-flex gap-2">
            <a href="<?php echo e(route('kpi.export', ['section' => 'overtime', 'days' => $days, 'format' => 'xlsx'])); ?>" class="btn btn-sm btn-success">Export XLSX</a>
            <a href="<?php echo e(route('kpi.export', ['section' => 'overtime', 'days' => $days, 'format' => 'csv'])); ?>" class="btn btn-sm btn-outline-success">Export CSV</a>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-striped align-middle mb-0">
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Durasi (jam)</th>
                <th>Alasan</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $overtimeList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($loop->iteration + ($overtimeList->currentPage() - 1) * $overtimeList->perPage()); ?></td>
                  <td><?php echo e(optional($ot->user)->name ?? '-'); ?></td>
                  <td><?php echo e(optional($ot->date)->format('Y-m-d') ?? '-'); ?></td>
                  <td><?php echo e(number_format((float) $ot->duration_hours, 2)); ?></td>
                  <td><?php echo e($ot->reason ?? '-'); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="text-center text-muted">Tidak ada data overtime.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <?php if($overtimeList->hasPages()): ?>
          <div class="mt-3">
            <?php echo e($overtimeList->appends(['section' => 'overtime', 'days' => $days])->links()); ?>

          </div>
        <?php endif; ?>
      </div>

      <div class="tab-pane fade <?php echo e($activeSection === 'tasks' ? 'show active' : ''); ?>" id="tab-tasks" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
          <h5 class="mb-0">Tugas Dibuat (Periode)</h5>
          <div class="d-flex gap-2">
            <a href="<?php echo e(route('kpi.export', ['section' => 'tasks_created', 'days' => $days, 'format' => 'xlsx'])); ?>" class="btn btn-sm btn-success">Export XLSX</a>
            <a href="<?php echo e(route('kpi.export', ['section' => 'tasks_created', 'days' => $days, 'format' => 'csv'])); ?>" class="btn btn-sm btn-outline-success">Export CSV</a>
          </div>
        </div>
        <div class="table-responsive mb-4">
          <table class="table table-sm table-striped align-middle mb-0">
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Judul</th>
                <th>Assignee</th>
                <th>Status</th>
                <th>Dibuat</th>
                <th>Jatuh Tempo</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $tasksCreatedList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($loop->iteration + ($tasksCreatedList->currentPage() - 1) * $tasksCreatedList->perPage()); ?></td>
                  <td><?php echo e($task->title); ?></td>
                  <td><?php echo e(optional($task->assignee)->name ?? '-'); ?></td>
                  <td><?php echo e($task->status); ?></td>
                  <td><?php echo e(optional($task->created_at)->format('Y-m-d') ?? '-'); ?></td>
                  <td><?php echo e(optional($task->due_date)->format('Y-m-d') ?? '-'); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="text-center text-muted">Tidak ada tugas dibuat dalam periode ini.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <?php if($tasksCreatedList->hasPages()): ?>
          <div class="mb-4">
            <?php echo e($tasksCreatedList->appends(['section' => 'tasks', 'days' => $days])->links()); ?>

          </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
          <h5 class="mb-0">Tugas Selesai (Periode)</h5>
          <div class="d-flex gap-2">
            <a href="<?php echo e(route('kpi.export', ['section' => 'tasks_completed', 'days' => $days, 'format' => 'xlsx'])); ?>" class="btn btn-sm btn-success">Export XLSX</a>
            <a href="<?php echo e(route('kpi.export', ['section' => 'tasks_completed', 'days' => $days, 'format' => 'csv'])); ?>" class="btn btn-sm btn-outline-success">Export CSV</a>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-striped align-middle mb-0">
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Judul</th>
                <th>Assignee</th>
                <th>Status</th>
                <th>Selesai</th>
                <th>Dibuat</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $tasksCompletedList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($loop->iteration + ($tasksCompletedList->currentPage() - 1) * $tasksCompletedList->perPage()); ?></td>
                  <td><?php echo e($task->title); ?></td>
                  <td><?php echo e(optional($task->assignee)->name ?? '-'); ?></td>
                  <td><?php echo e($task->status); ?></td>
                  <td><?php echo e(optional($task->updated_at)->format('Y-m-d') ?? '-'); ?></td>
                  <td><?php echo e(optional($task->created_at)->format('Y-m-d') ?? '-'); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="text-center text-muted">Tidak ada tugas selesai dalam periode ini.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <?php if($tasksCompletedList->hasPages()): ?>
          <div class="mt-3">
            <?php echo e($tasksCompletedList->appends(['section' => 'tasks', 'days' => $days])->links()); ?>

          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/reports/kpi.blade.php ENDPATH**/ ?>