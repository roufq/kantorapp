
<?php $__env->startSection('title'); ?>
<div class="row">
  <div class="col-sm-12">
    <div class="page-title-box">
      <div class="btn-group float-right">
        <ol class="breadcrumb hide-phone p-0 m-0">
          <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </div>
      <h4 class="page-title">Dashboard</h4>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<?php
  $statCards = [];
  $secondaryCards = [];
  if($user->hasRole('Super Admin')) {
    $statCards = [];
  } elseif($user->hasRole('Admin Lokasi')) {
    $statCards = [
      ['label' => 'Absences Today', 'value' => $absencesTodayCount ?? 0, 'icon' => 'mdi-alert-circle', 'color' => 'info'],
    ];
    $secondaryCards = [
      ['label' => 'Clock In (Saya)', 'value' => ($todayAttendance && $todayAttendance->check_in_time) ? $todayAttendance->check_in_time->format('H:i') : '--', 'icon' => 'mdi-clock-start', 'color' => 'info'],
      ['label' => 'Clock Out (Saya)', 'value' => ($todayAttendance && $todayAttendance->check_out_time) ? $todayAttendance->check_out_time->format('H:i') : '--', 'icon' => 'mdi-clock-end', 'color' => 'dark'],
    ];
  } else {
    $statCards = [
      ['label' => 'My Tasks', 'value' => $totalTasks, 'icon' => 'mdi-check-circle', 'color' => 'success'],
    ];
    if($user->hasRole('Super Admin') || $user->hasRole('Admin Lokasi') || $user->hasRole('Karyawan')) {
      $statCards[] = ['label' => 'Today\'s Shift', 'value' => '', 'icon' => 'mdi-calendar-clock', 'color' => 'info', 'extra' => true];
      $statCards[] = ['label' => 'Clock In', 'value' => ($todayAttendance && $todayAttendance->check_in_time) ? $todayAttendance->check_in_time->format('H:i') : '--', 'icon' => 'mdi-clock-start', 'color' => 'secondary'];
      $secondaryCards = [
        ['label' => 'Clock Out', 'value' => ($todayAttendance && $todayAttendance->check_out_time) ? $todayAttendance->check_out_time->format('H:i') : '--', 'icon' => 'mdi-clock-end', 'color' => 'dark'],
      ];
    }
  }
?>

<?php if(!empty($statCards)): ?>
  <div class="row">
    <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="col-md-6 col-xl-3">
        <div class="card m-b-30">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center bg-<?php echo e($card['color'] ?? 'primary'); ?> text-white">
                  <i class="mdi <?php echo e($card['icon'] ?? 'mdi-information'); ?>"></i>
                </div>
              </div>
              <div class="flex-grow-1 text-right">
                <p class="text-muted mb-1"><?php echo e($card['label']); ?></p>
                <?php if(!empty($card['extra'])): ?>
                  <h5 class="mb-0">
                    <?php if(isset($todayAssignmentTime) && $todayAssignmentTime === 'Hari libur Anda'): ?>
                      <?php echo e($todayAssignmentTime); ?>

                    <?php elseif($todayAssignment && $todayAssignment->shift): ?>
                      <?php echo e($todayAssignment->shift->name); ?>

                      <?php if(!empty($todayAssignmentTime)): ?>
                        <span class="d-block text-muted small"><?php echo e($todayAssignmentTime); ?></span>
                      <?php endif; ?>
                    <?php elseif(!empty($todayAssignmentTime)): ?>
                      <span class="text-muted small"><?php echo e($todayAssignmentTime); ?></span>
                    <?php else: ?>
                      --
                    <?php endif; ?>
                  </h5>
                <?php else: ?>
                  <h4 class="mb-0"><?php echo e($card['value']); ?></h4>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
<?php endif; ?>

<?php if(!empty($secondaryCards)): ?>
  <div class="row">
    <?php $__currentLoopData = $secondaryCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="col-md-6 col-xl-3">
        <div class="card m-b-30">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center bg-<?php echo e($card['color'] ?? 'primary'); ?> text-white">
                  <i class="mdi <?php echo e($card['icon'] ?? 'mdi-information'); ?>"></i>
                </div>
              </div>
              <div class="flex-grow-1 text-right">
                <p class="text-muted mb-1"><?php echo e($card['label']); ?></p>
                <h4 class="mb-0"><?php echo e($card['value']); ?></h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
<?php endif; ?>

<?php if(auth()->user()->hasRole('Karyawan')): ?>
  <div class="row">
    <div class="col-md-12">
      <div class="card m-b-30">
        <div class="card-header">
          <h5 class="card-title mb-0">Lokasi & Jadwal Hari Ini</h5>
        </div>
        <div class="card-body">
          <div class="row">
           <div class="col-md-4">
             <div class="font-weight-bold">Lokasi</div>
             <div class="text-muted"><?php echo e($userLocationName ?? '-'); ?></div>
           </div>
           <div class="col-md-4">
             <div class="font-weight-bold">Shift (rencana)</div>
             <div class="text-muted">
                <?php if(isset($todayAssignmentTime) && $todayAssignmentTime === 'Hari libur Anda'): ?>
                  <?php echo e($todayAssignmentTime); ?>

                <?php elseif($todayAssignment && $todayAssignment->shift): ?>
                  <?php echo e($todayAssignment->shift->name); ?> <?php if(!empty($todayAssignmentTime)): ?> (<?php echo e($todayAssignmentTime); ?>) <?php endif; ?>
                <?php elseif(!empty($todayAssignmentTime)): ?>
                  <?php echo e($todayAssignmentTime); ?>

                <?php else: ?>
                  --
                <?php endif; ?>
                <?php if(!empty($todayPlannedDate)): ?>
                  <div class="text-muted small">Tanggal: <?php echo e($todayPlannedDate); ?></div>
                <?php endif; ?>
              </div>
            </div>
            <div class="col-md-4">
              <div class="font-weight-bold">Clock In / Clock Out</div>
              <div class="text-muted">
                <?php
                  $ci = $todayAttendance && $todayAttendance->check_in_time ? $todayAttendance->check_in_time->format('H:i') : '--';
                  $co = $todayAttendance && $todayAttendance->check_out_time ? $todayAttendance->check_out_time->format('H:i') : '--';
                ?>
                <?php if(!empty($todayPlannedDate)): ?>
                  <div class="text-muted small"><?php echo e($todayPlannedDate); ?></div>
                <?php endif; ?>
                <?php echo e($ci); ?> / <?php echo e($co); ?>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php if($user->hasRole('Admin Lokasi') && $attendanceList && $attendanceList->count() > 0): ?>
  <div class="row mt-3">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
          <h5 class="card-title mb-0">Clock In/Out Hari Ini</h5>
          <small class="text-muted">Data sesuai lokasi Anda.</small>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle mb-0">
              <thead>
                <tr>
                  <th style="width:50px">No</th>
                  <?php if($user->hasRole('Admin Lokasi')): ?>
                    <th>Nama</th>
                  <?php endif; ?>
                  <th>Clock In</th>
                  <th>Clock Out</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $attendanceList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <?php if($user->hasRole('Admin Lokasi')): ?>
                      <td class="text-break"><?php echo e(optional($att->user)->name ?? 'Unknown'); ?></td>
                    <?php endif; ?>
                    <td><?php echo e($att->check_in_time ? $att->check_in_time->format('H:i') : '--'); ?></td>
                    <td><?php echo e($att->check_out_time ? $att->check_out_time->format('H:i') : '--'); ?></td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php if(!$user->hasRole('Karyawan')): ?>
  <div class="row mb-2">
    <div class="col-md-12">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <form method="GET" action="<?php echo e(route('dashboard')); ?>" class="d-flex align-items-center gap-2">
          <label class="mb-0 text-muted small">Periode KPI</label>
          <select name="kpi_days" class="form-select form-select-sm" style="width: 120px;">
            <?php $__currentLoopData = [7, 30, 90, 180]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $days): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($days); ?>" <?php echo e((int) ($kpiDays ?? 30) === $days ? 'selected' : ''); ?>><?php echo e($days); ?> hari</option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <?php if(request('search')): ?>
            <input type="hidden" name="search" value="<?php echo e(request('search')); ?>">
          <?php endif; ?>
          <button type="submit" class="btn btn-sm btn-outline-primary">Terapkan</button>
        </form>
        <a class="btn btn-sm btn-outline-secondary" href="<?php echo e(route('kpi.index', ['days' => $kpiDays ?? 30])); ?>">Detail KPI</a>
      </div>
    </div>
  </div>
  <?php
    $kpiCards = [
      [
        'label' => 'Keterlambatan (' . ($kpiDays ?? 30) . 'd)',
        'value' => number_format($kpiMetrics['lateness_rate'] ?? 0, 2) . '%',
        'icon' => 'mdi-clock-alert',
        'color' => 'danger',
        'meta' => ($kpiMetrics['late_count'] ?? 0) . ' / ' . ($kpiMetrics['attendance_count'] ?? 0),
        'link' => route('kpi.index', ['section' => 'lateness', 'days' => $kpiDays ?? 30]),
      ],
      [
        'label' => 'Kehadiran (' . ($kpiDays ?? 30) . 'd)',
        'value' => number_format($kpiMetrics['attendance_rate_30d'] ?? 0, 2) . '%',
        'icon' => 'mdi-account-check',
        'color' => 'success',
        'meta' => ($kpiMetrics['attendance_count'] ?? 0) . ' hadir',
        'link' => route('kpi.index', ['section' => 'attendance', 'days' => $kpiDays ?? 30]),
      ],
      [
        'label' => 'Overtime Disetujui (' . ($kpiDays ?? 30) . 'd)',
        'value' => number_format($kpiMetrics['overtime_hours_30d'] ?? 0, 2) . ' jam',
        'icon' => 'mdi-timer',
        'color' => 'warning',
        'meta' => $kpiMetrics['period_label'] ?? '',
        'link' => route('kpi.index', ['section' => 'overtime', 'days' => $kpiDays ?? 30]),
      ],
      [
        'label' => 'Produktivitas Tugas (' . ($kpiDays ?? 30) . 'd)',
        'value' => number_format($kpiMetrics['task_productivity_rate'] ?? 0, 2) . '%',
        'icon' => 'mdi-clipboard-check',
        'color' => 'primary',
        'meta' => ($kpiMetrics['tasks_completed'] ?? 0) . ' / ' . ($kpiMetrics['tasks_created'] ?? 0),
        'link' => route('kpi.index', ['section' => 'tasks', 'days' => $kpiDays ?? 30]),
      ],
    ];
  ?>
  <div class="row">
    <?php $__currentLoopData = $kpiCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="col-md-6 col-xl-3">
        <div class="card m-b-30">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center bg-<?php echo e($card['color'] ?? 'primary'); ?> text-white">
                  <i class="mdi <?php echo e($card['icon'] ?? 'mdi-information'); ?>"></i>
                </div>
              </div>
              <div class="flex-grow-1 text-right">
                <p class="text-muted mb-1"><?php echo e($card['label']); ?></p>
                <h4 class="mb-0"><?php echo e($card['value']); ?></h4>
                <?php if(!empty($card['meta'])): ?>
                  <small class="text-muted"><?php echo e($card['meta']); ?></small>
                <?php endif; ?>
                <?php if(!empty($card['link'])): ?>
                  <div class="mt-1">
                    <a href="<?php echo e($card['link']); ?>" class="small text-decoration-none">Lihat detail</a>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
<?php endif; ?>

<?php
  $chartTotalTasks = (int) ($chartMetrics['total_tasks'] ?? 0);
  $chartCompletedTasks = (int) ($chartMetrics['completed_tasks'] ?? 0);
  $chartCompletionRate = $chartTotalTasks > 0 ? round(($chartCompletedTasks / $chartTotalTasks) * 100, 2) : 0;
  $chartAttendanceTotal = (int) ($chartMetrics['attendance_total'] ?? 0);
  $chartCheckedIn = (int) ($chartMetrics['checked_in_today'] ?? 0);
  $chartAttendanceRate = $chartAttendanceTotal > 0 ? round(($chartCheckedIn / $chartAttendanceTotal) * 100, 2) : 0;
?>

<div class="row mb-2">
  <div class="col-12">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div>
        <h5 class="mb-1">Ringkasan Utama</h5>
        <small class="text-muted">Gambaran cepat performa tugas, kehadiran, dan overtime.</small>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6 col-xl-3">
    <div class="card m-b-30">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="text-muted">Task Completion Rate</div>
          <span class="fw-semibold"><?php echo e(number_format($chartCompletionRate, 2)); ?>%</span>
        </div>
        <canvas id="chartTaskCompletion" height="140"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card m-b-30">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="text-muted">Attendance Rate (Today)</div>
          <span class="fw-semibold"><?php echo e(number_format($chartAttendanceRate, 2)); ?>%</span>
        </div>
        <canvas id="chartAttendanceToday" height="140"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card m-b-30">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="text-muted">Total Tasks</div>
          <span class="fw-semibold"><?php echo e($chartMetrics['total_tasks'] ?? 0); ?></span>
        </div>
        <canvas id="chartTotalTasks" height="140"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card m-b-30">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="text-muted">Overtime (Last 30d)</div>
          <span class="fw-semibold"><?php echo e(number_format($chartMetrics['overtime_hours_30d'] ?? 0, 2)); ?> hrs</span>
        </div>
        <canvas id="chartOvertime30d" height="140"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="row mt-2 mb-2">
  <div class="col-12">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div>
        <h5 class="mb-1">Distribusi Lokasi</h5>
        <small class="text-muted">Detail jumlah karyawan dan user per lokasi.</small>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <div class="card m-b-30">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="text-muted">Total Employees per Lokasi</div>
          <span class="fw-semibold"><?php echo e($chartMetrics['total_employees'] ?? 0); ?></span>
        </div>
        <canvas id="chartEmployeesByLocation" height="180"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card m-b-30">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="text-muted">Total Users per Lokasi</div>
          <span class="fw-semibold"><?php echo e($chartMetrics['total_users'] ?? 0); ?></span>
        </div>
        <canvas id="chartUsersByLocation" height="180"></canvas>
      </div>
    </div>
  </div>
</div>
<!-- 
<div class="row mt-2">
  <div class="col-md-4">
    <div class="card m-b-30">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="text-muted">Total Super Admins</div>
          <span class="fw-semibold"><?php echo e($chartMetrics['total_masters'] ?? 0); ?></span>
        </div>
        <canvas id="chartTotalMasters" height="120"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card m-b-30">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="text-muted">Unread Messages</div>
          <span class="fw-semibold"><?php echo e($chartMetrics['unread_messages'] ?? 0); ?></span>
        </div>
        <canvas id="chartUnreadMessages" height="120"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card m-b-30">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="text-muted">Total Divisions</div>
          <span class="fw-semibold"><?php echo e($chartMetrics['total_divisions'] ?? 0); ?></span>
        </div>
        <canvas id="chartTotalDivisions" height="120"></canvas>
      </div>
    </div>
  </div>
</div> -->
<?php if(!$user->hasRole('Karyawan')): ?>
  <!--begin::Row-->
  <div class="row" id="monthly-recap">
    <div class="col-md-12">
      <div class="card mb-4" id="monthly-recap-card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title">Monthly Recap Report</h5>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-report-toggle="collapse" aria-label="Collapse">
              <i class="bi bi-dash-lg collapse-icon"></i>
              <i class="bi bi-plus-lg expand-icon d-none"></i>
            </button>
            <div class="btn-group">
              <button
                type="button"
                class="btn btn-tool dropdown-toggle"
                data-bs-toggle="dropdown"
                aria-label="Actions"
              >
                <i class="bi bi-wrench"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" role="menu">
                <a href="#" class="dropdown-item report-action" data-report-action="refresh">Refresh data</a>
                <a href="#" class="dropdown-item report-action" data-report-action="clear-search">Clear search</a>
                <a href="#" class="dropdown-item report-action" data-report-action="export-csv">Export CSV (visible)</a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item report-action" data-report-action="print">Print table</a>
              </div>
            </div>
            <button type="button" class="btn btn-tool" data-report-toggle="remove" aria-label="Close">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body" id="monthly-recap-body">
          <!-- Search Form -->
          <div class="mb-3">
            <form method="GET" action="<?php echo e(route('dashboard')); ?>" class="d-flex" id="monthly-recap-search">
              <input type="text" name="search" class="form-control me-2" placeholder="Search by task title or assignee name..." value="<?php echo e(request('search')); ?>">
              <button type="submit" class="btn btn-outline-primary">Search</button>
              <?php if(request('search')): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-secondary ms-2">Clear</a>
              <?php endif; ?>
            </form>
          </div>
          <?php if($tasks->hasPages()): ?>
            <div class="d-flex justify-content-center mt-4">
              <?php echo e($tasks->appends(request()->query())->links()); ?>

            </div>
          <?php endif; ?>
          <div class="table-responsive" id="monthly-recap-table">
            <?php if($user->hasRole('Super Admin')): ?>
              <table class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th style="width:50px">No</th>
                    <th>Employee Name</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Due Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                      <td><?php echo e($loop->iteration); ?></td>
                      <td class="text-break"><?php echo e($task->assignee->name ?? 'Unassigned'); ?></td>
                      <td class="text-break"><?php echo e($task->title); ?></td>
                      <td class="text-break"><?php echo e(Str::limit($task->description, 50)); ?></td>
                      <td>
                        <?php switch($task->status):
                          case ('pending'): ?>
                            <span class="badge text-bg-warning">Pending</span>
                            <?php break; ?>
                          <?php case ('in_progress'): ?>
                            <span class="badge text-bg-info">In Progress</span>
                            <?php break; ?>
                          <?php case ('completed'): ?>
                            <span class="badge text-bg-success">Completed</span>
                            <?php break; ?>
                          <?php default: ?>
                            <span class="badge text-bg-secondary"><?php echo e($task->status); ?></span>
                        <?php endswitch; ?>
                      </td>
                      <td class="text-break"><?php echo e($task->due_date ? $task->due_date->format('Y-m-d') : 'No due date'); ?></td>
                    </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
              </table>
            <?php else: ?>
              <table class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th style="width:50px">No</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Due Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                      <td><?php echo e($loop->iteration); ?></td>
                      <td class="text-break"><?php echo e($task->title); ?></td>
                      <td class="text-break"><?php echo e(Str::limit($task->description, 50)); ?></td>
                      <td>
                        <?php switch($task->status):
                          case ('pending'): ?>
                            <span class="badge text-bg-warning">Pending</span>
                            <?php break; ?>
                          <?php case ('in_progress'): ?>
                            <span class="badge text-bg-info">In Progress</span>
                            <?php break; ?>
                          <?php case ('completed'): ?>
                            <span class="badge text-bg-success">Completed</span>
                            <?php break; ?>
                          <?php default: ?>
                            <span class="badge text-bg-secondary"><?php echo e($task->status); ?></span>
                        <?php endswitch; ?>
                      </td>
                      <td class="text-break"><?php echo e($task->due_date ? $task->due_date->format('Y-m-d') : 'No due date'); ?></td>
                    </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>
        </div>
        <!-- ./card-body -->
        <?php if($tasks->hasPages()): ?>
          <div class="d-flex justify-content-center mt-3">
            <?php echo e($tasks->appends(request()->query())->links()); ?>

          </div>
        <?php endif; ?>
        
      </div>
      <!-- /.card -->
    </div>
    <!-- /.col -->
  </div>
  <!--end::Row-->
<?php endif; ?>
<!--begin::Row-->

<!--end::Row-->
<!--begin::Row: Quick Actions and Info-->
<div class="row mt-3">
  <div class="col-md-4">
    <?php if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi') || auth()->user()->hasRole('Karyawan')): ?>
    <div class="card m-b-30">
      <div class="card-header"><h3 class="card-title">Quick Actions</h3></div>
      <div class="card-body">
        <div class="d-grid gap-2">
          <a class="btn btn-outline-primary" href="<?php echo e(route('attendance.checkin')); ?>">Attendance: Check In/Out</a>
          <?php if($user->hasRole('Admin Lokasi') && $user->location_id): ?>
            <a class="btn btn-outline-secondary" href="<?php echo e(route('locations.settings', $user->location_id)); ?>">My Location Settings</a>
          <?php endif; ?>
          <a class="btn btn-outline-info" href="<?php echo e(route('tasks.create')); ?>">Create Task</a>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
  <?php if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi') || auth()->user()->hasRole('Karyawan')): ?>
  <div class="col-md-8">
    <div class="card m-b-30">
      <div class="card-header"><h3 class="card-title">Messages</h3></div>
      <div class="card-body">
        <p class="mb-2">Unread: <strong><?php echo e($unreadMessages); ?></strong></p>
        <a class="btn btn-sm btn-primary" href="<?php echo e(route('messages.index')); ?>">Open Messages</a>

        <?php if(auth()->user()->hasTwoFactorEnabled()): ?>
            <div class="alert alert-warning mt-3">
                <strong>Security Recommendation:</strong> Enable two-factor authentication to better protect your account.
                <a href="<?php echo e(route('2fa.setup')); ?>" class="btn btn-sm btn-warning ms-2">Enable 2FA</a>
            </div>
        <?php else: ?>
            <div class="alert alert-success mt-3">
                <strong>✓ Two-Factor Authentication Enabled</strong>
                <small class="d-block">Method: <?php echo e(ucfirst(auth()->user()->two_factor_method)); ?></small>
                <form action="<?php echo e(route('2fa.disable')); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger ms-2"
                            onclick="return confirm('Are you sure you want to disable 2FA?')">Disable 2FA</button>
                </form>
            </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>
<!--end::Row-->
<?php if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi') || auth()->user()->hasRole('Karyawan')): ?>
<!--begin::Row: Notices-->
<div class="row mt-3">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Notifikasi Libur / Izin</h3></div>
      <div class="card-body">
        <?php if(!empty($todayNotices)): ?>
          <div class="mb-2">
            <strong>Hari ini:</strong>
            <ul class="mb-0">
              <?php $__currentLoopData = $todayNotices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($n); ?></li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          </div>
        <?php else: ?>
          <p class="mb-2 text-muted">Tidak ada notifikasi untuk hari ini.</p>
        <?php endif; ?>

        <?php if(!empty($upcomingNotices)): ?>
          <div class="mt-2">
            <strong>Mendatang:</strong>
            <ul class="mb-0">
              <?php $__currentLoopData = $upcomingNotices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                  <span class="badge text-bg-light"><?php echo e($item['date']); ?></span>
                  <?php $__currentLoopData = $item['labels']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="badge text-bg-secondary"><?php echo e($label); ?></span>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          </div>
        <?php else: ?>
          <p class="mb-0 text-muted">Tidak ada notifikasi dalam 14 hari ke depan.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <!-- /.col -->
</div>
<!--end::Row-->
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(asset('NewAsset/assets/plugins/chart.js/Chart.bundle.js')); ?>"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') {
      return;
    }

    const metrics = <?php echo json_encode($chartMetrics ?? [], 15, 512) ?>;
    const totalTasks = Number(metrics.total_tasks || 0);
    const completedTasks = Number(metrics.completed_tasks || 0);
    const totalEmployees = Number(metrics.total_employees || 0);
    const checkedInToday = Number(metrics.checked_in_today || 0);
    const attendanceTotal = Number(metrics.attendance_total || 0);
    const totalMasters = Number(metrics.total_masters || 0);
    const totalDivisions = Number(metrics.total_divisions || 0);
    const totalMessages = Number(metrics.total_messages || 0);
    const unreadMessages = Number(metrics.unread_messages || 0);
    const overtimeHours30d = Number(metrics.overtime_hours_30d || 0);
    const locationLabels = <?php echo json_encode($locationLabels ?? [], 15, 512) ?>;
    const employeesByLocation = <?php echo json_encode($employeeCountsByLocation ?? [], 15, 512) ?>;
    const usersByLocation = <?php echo json_encode($userCountsByLocation ?? [], 15, 512) ?>;

    const completionCtx = document.getElementById('chartTaskCompletion');
    if (completionCtx) {
      const remainingTasks = Math.max(totalTasks - completedTasks, 0);
      new Chart(completionCtx, {
        type: 'doughnut',
        data: {
          labels: ['Completed', 'Remaining'],
          datasets: [{
            data: [completedTasks, remainingTasks],
            backgroundColor: ['#1abc9c', '#e9ecef'],
            borderWidth: 0
          }]
        },
        options: {
          cutoutPercentage: 65,
          legend: { display: false },
          tooltips: {
            callbacks: {
              label: function (tooltipItem, data) {
                const label = data.labels[tooltipItem.index] || '';
                const value = data.datasets[0].data[tooltipItem.index] || 0;
                return label + ': ' + value;
              }
            }
          }
        }
      });
    }

    const attendanceCtx = document.getElementById('chartAttendanceToday');
    if (attendanceCtx) {
      const absentCount = Math.max(attendanceTotal - checkedInToday, 0);
      new Chart(attendanceCtx, {
        type: 'doughnut',
        data: {
          labels: ['Checked In', 'Not Yet'],
          datasets: [{
            data: [checkedInToday, absentCount],
            backgroundColor: ['#4d79f6', '#e9ecef'],
            borderWidth: 0
          }]
        },
        options: {
          cutoutPercentage: 65,
          legend: { display: false },
          tooltips: {
            callbacks: {
              label: function (tooltipItem, data) {
                const label = data.labels[tooltipItem.index] || '';
                const value = data.datasets[0].data[tooltipItem.index] || 0;
                return label + ': ' + value;
              }
            }
          }
        }
      });
    }

    const tasksCtx = document.getElementById('chartTotalTasks');
    if (tasksCtx) {
      new Chart(tasksCtx, {
        type: 'bar',
        data: {
          labels: ['Total', 'Completed'],
          datasets: [{
            data: [totalTasks, completedTasks],
            backgroundColor: ['#f4c166', '#6fd3b3']
          }]
        },
        options: {
          legend: { display: false },
          scales: {
            yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }],
            xAxes: [{ gridLines: { display: false } }]
          }
        }
      });
    }

    const mastersCtx = document.getElementById('chartTotalMasters');
    if (mastersCtx) {
      new Chart(mastersCtx, {
        type: 'bar',
        data: {
          labels: ['Super Admins'],
          datasets: [{
            data: [totalMasters],
            backgroundColor: '#7c5cff'
          }]
        },
        options: {
          legend: { display: false },
          scales: {
            yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }],
            xAxes: [{ gridLines: { display: false } }]
          }
        }
      });
    }

    const unreadCtx = document.getElementById('chartUnreadMessages');
    if (unreadCtx) {
      const readCount = Math.max(totalMessages - unreadMessages, 0);
      new Chart(unreadCtx, {
        type: 'doughnut',
        data: {
          labels: ['Unread', 'Read'],
          datasets: [{
            data: [unreadMessages, readCount],
            backgroundColor: ['#f6b93b', '#e9ecef'],
            borderWidth: 0
          }]
        },
        options: {
          cutoutPercentage: 65,
          legend: { display: false },
          tooltips: {
            callbacks: {
              label: function (tooltipItem, data) {
                const label = data.labels[tooltipItem.index] || '';
                const value = data.datasets[0].data[tooltipItem.index] || 0;
                return label + ': ' + value;
              }
            }
          }
        }
      });
    }

    const employeesByLocationCtx = document.getElementById('chartEmployeesByLocation');
    if (employeesByLocationCtx) {
      new Chart(employeesByLocationCtx, {
        type: 'horizontalBar',
        data: {
          labels: locationLabels,
          datasets: [{
            data: employeesByLocation,
            backgroundColor: '#00b5b8'
          }]
        },
        options: {
          legend: { display: false },
          scales: {
            xAxes: [{ ticks: { beginAtZero: true, precision: 0 }, gridLines: { display: true } }],
            yAxes: [{ gridLines: { display: false } }]
          }
        }
      });
    }

    const usersByLocationCtx = document.getElementById('chartUsersByLocation');
    if (usersByLocationCtx) {
      new Chart(usersByLocationCtx, {
        type: 'horizontalBar',
        data: {
          labels: locationLabels,
          datasets: [{
            data: usersByLocation,
            backgroundColor: '#38ada9'
          }]
        },
        options: {
          legend: { display: false },
          scales: {
            xAxes: [{ ticks: { beginAtZero: true, precision: 0 }, gridLines: { display: true } }],
            yAxes: [{ gridLines: { display: false } }]
          }
        }
      });
    }

    const divisionsCtx = document.getElementById('chartTotalDivisions');
    if (divisionsCtx) {
      new Chart(divisionsCtx, {
        type: 'bar',
        data: {
          labels: ['Divisions'],
          datasets: [{
            data: [totalDivisions],
            backgroundColor: '#576574'
          }]
        },
        options: {
          legend: { display: false },
          scales: {
            yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }],
            xAxes: [{ gridLines: { display: false } }]
          }
        }
      });
    }

    const overtimeCtx = document.getElementById('chartOvertime30d');
    if (overtimeCtx) {
      new Chart(overtimeCtx, {
        type: 'bar',
        data: {
          labels: ['Hours'],
          datasets: [{
            data: [overtimeHours30d],
            backgroundColor: '#f368e0'
          }]
        },
        options: {
          legend: { display: false },
          scales: {
            yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }],
            xAxes: [{ gridLines: { display: false } }]
          }
        }
      });
    }
  });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const recapCard = document.getElementById('monthly-recap-card');
    if (!recapCard) return;

    const body = document.getElementById('monthly-recap-body');
    const searchForm = document.getElementById('monthly-recap-search');
    const tableWrap = document.getElementById('monthly-recap-table');
    const collapseBtn = recapCard.querySelector('[data-report-toggle="collapse"]');
    const removeBtn = recapCard.querySelector('[data-report-toggle="remove"]');

    function toggleCollapse() {
      if (!body) return;
      const collapsed = body.classList.toggle('d-none');
      collapseBtn.querySelector('.collapse-icon')?.classList.toggle('d-none', collapsed);
      collapseBtn.querySelector('.expand-icon')?.classList.toggle('d-none', !collapsed);
    }

    function removeCard() {
      recapCard.classList.add('d-none');
    }

    collapseBtn?.addEventListener('click', toggleCollapse);
    removeBtn?.addEventListener('click', removeCard);

    recapCard.querySelectorAll('.report-action').forEach(function (actionLink) {
      actionLink.addEventListener('click', function (e) {
        e.preventDefault();
        const action = this.dataset.reportAction;
        if (action === 'refresh') {
          window.location.reload();
        } else if (action === 'clear-search' && searchForm) {
          const input = searchForm.querySelector('input[name="search"]');
          if (input) {
            input.value = '';
            searchForm.submit();
          }
        } else if (action === 'export-csv') {
          exportTableToCsv();
        } else if (action === 'print') {
          window.print();
        }
      });
    });

    function exportTableToCsv() {
      if (!tableWrap) return;
      const table = tableWrap.querySelector('table');
      if (!table) return;
      let csv = [];
      table.querySelectorAll('tr').forEach(function (row) {
        const cols = Array.from(row.querySelectorAll('th,td')).map(function (cell) {
          return '"' + (cell.innerText || '').replace(/"/g, '""') + '"';
        });
        csv.push(cols.join(','));
      });
      const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = 'monthly-recap.csv';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);
    }
  });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/dashboard.blade.php ENDPATH**/ ?>