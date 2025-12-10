<?php $__env->startSection('title'); ?>
<div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Dashboard</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<!-- Info boxes -->
<?php if($user->hasRole('Super Admin')): ?>
  <div class="row">
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-primary shadow-sm">
          <i class="bi bi-person-fill-gear"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Total Super Admins</span>
          <span class="info-box-number"><?php echo e($totalMasters); ?></span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-danger shadow-sm">
          <i class="bi bi-people-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Total Employees</span>
          <span class="info-box-number"><?php echo e($totalEmployees); ?></span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-success shadow-sm">
          <i class="bi bi-check-circle-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Total Tasks</span>
          <span class="info-box-number"><?php echo e($totalTasks); ?></span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-warning shadow-sm">
          <i class="bi bi-chat-text-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Unread Messages</span>
          <span class="info-box-number"><?php echo e($unreadMessages); ?></span>
        </div>
      </div>
    </div>
  </div>
<?php elseif($user->hasRole('Admin Lokasi')): ?>
  <div class="row">
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-danger shadow-sm">
          <i class="bi bi-people-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Employees (My Location)</span>
          <span class="info-box-number"><?php echo e($totalKaryawans); ?></span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-success shadow-sm">
          <i class="bi bi-check-circle-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Total Tasks</span>
          <span class="info-box-number"><?php echo e($totalTasks); ?></span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-warning shadow-sm">
          <i class="bi bi-chat-text-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Unread Messages</span>
          <span class="info-box-number"><?php echo e($unreadMessages); ?></span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-danger shadow-sm">
          <i class="bi bi-x-octagon-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Absences Today</span>
          <span class="info-box-number"><?php echo e($absencesTodayCount ?? 0); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-light shadow-sm">
          <i class="bi bi-clock-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Clock In (Saya)</span>
          <span class="info-box-number">
            <?php if($todayAttendance && $todayAttendance->check_in_time): ?>
              <?php echo e($todayAttendance->check_in_time->format('H:i')); ?>

            <?php else: ?>
              --
            <?php endif; ?>
          </span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-dark shadow-sm">
          <i class="bi bi-clock-history"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Clock Out (Saya)</span>
          <span class="info-box-number">
            <?php if($todayAttendance && $todayAttendance->check_out_time): ?>
              <?php echo e($todayAttendance->check_out_time->format('H:i')); ?>

            <?php else: ?>
              --
            <?php endif; ?>
          </span>
        </div>
      </div>
    </div>
  </div>
<?php else: ?>
  <div class="row">
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-success shadow-sm">
          <i class="bi bi-check-circle-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">My Tasks</span>
          <span class="info-box-number"><?php echo e($totalTasks); ?></span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-warning shadow-sm">
          <i class="bi bi-chat-text-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Unread Messages</span>
          <span class="info-box-number"><?php echo e($unreadMessages); ?></span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
          <span class="info-box-icon text-bg-info shadow-sm">
            <i class="bi bi-calendar3"></i>
          </span>
          <div class="info-box-content">
           <span class="info-box-text">Today's Shift</span>
           <span class="info-box-number">
              <?php if(isset($todayAssignmentTime) && $todayAssignmentTime === 'Hari libur Anda'): ?>
                <div><?php echo e($todayAssignmentTime); ?></div>
              <?php elseif($todayAssignment && $todayAssignment->shift): ?>
                <?php echo e($todayAssignment->shift->name); ?>

                <?php if(!empty($todayAssignmentTime)): ?>
                  <div class="text-muted small"><?php echo e($todayAssignmentTime); ?></div>
                <?php endif; ?>
              <?php elseif(!empty($todayAssignmentTime)): ?>
                <div class="text-muted small"><?php echo e($todayAssignmentTime); ?></div>
              <?php else: ?>
                --
              <?php endif; ?>
           </span>
         </div>
       </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-light shadow-sm">
          <i class="bi bi-clock-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Clock In</span>
          <span class="info-box-number">
            <?php if($todayAttendance && $todayAttendance->check_in_time): ?>
              <?php echo e($todayAttendance->check_in_time->format('H:i')); ?>

            <?php else: ?>
              --
            <?php endif; ?>
          </span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon text-bg-dark shadow-sm">
          <i class="bi bi-clock-history"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Clock Out</span>
          <span class="info-box-number">
            <?php if($todayAttendance && $todayAttendance->check_out_time): ?>
              <?php echo e($todayAttendance->check_out_time->format('H:i')); ?>

            <?php else: ?>
              --
            <?php endif; ?>
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Lokasi & Jadwal Hari Ini -->
  <div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-header">
          <h5 class="card-title mb-0">Lokasi & Jadwal Hari Ini</h5>
        </div>
        <div class="card-body">
          <div class="row">
           <div class="col-md-4">
             <div class="fw-semibold">Lokasi</div>
             <div class="text-muted"><?php echo e($userLocationName ?? '-'); ?></div>
           </div>
           <div class="col-md-4">
             <div class="fw-semibold">Shift (rencana)</div>
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
              <div class="fw-semibold">Clock In / Clock Out</div>
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
  <!-- Additional Info boxes -->
  <div class="row">
    <div class="col-12 col-sm-6 col-md-4">
      <div class="info-box">
        <span class="info-box-icon text-bg-info shadow-sm">
          <i class="bi bi-people-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Total Users</span>
          <span class="info-box-number"><?php echo e($totalUsers); ?></span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
      <div class="info-box">
        <span class="info-box-icon text-bg-secondary shadow-sm">
          <i class="bi bi-building-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Total Divisions</span>
          <span class="info-box-number"><?php echo e($totalDivisions); ?></span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
      <div class="info-box">
        <span class="info-box-icon text-bg-dark shadow-sm">
          <i class="bi bi-person-badge-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Total Employees</span>
          <span class="info-box-number"><?php echo e($totalKaryawans); ?></span>
        </div>
      </div>
    </div>
  </div>
  <!-- Location Performance Metrics -->
  <div class="row">
    <div class="col-12 col-sm-6 col-md-4">
      <div class="info-box">
        <span class="info-box-icon text-bg-primary shadow-sm">
          <i class="bi bi-clipboard2-check"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Task Completion Rate</span>
          <span class="info-box-number"><?php echo e(number_format($locationMetrics['task_completion_rate'] ?? 0, 2)); ?>%</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
      <div class="info-box">
        <span class="info-box-icon text-bg-success shadow-sm">
          <i class="bi bi-person-check-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Attendance Rate (Today)</span>
          <span class="info-box-number"><?php echo e(number_format($locationMetrics['attendance_rate_today'] ?? 0, 2)); ?>%</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
      <div class="info-box">
        <span class="info-box-icon text-bg-warning shadow-sm">
          <i class="bi bi-alarm-fill"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Overtime (Last 30d)</span>
          <span class="info-box-number"><?php echo e(number_format($locationMetrics['overtime_hours_30d'] ?? 0, 2)); ?> hrs</span>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
<?php if(!$user->hasRole('Karyawan')): ?>
  <!--begin::Row-->
  <div class="row">
    <div class="col-md-12">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title">Monthly Recap Report</h5>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
              <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
              <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
            </button>
            <div class="btn-group">
              <button
                type="button"
                class="btn btn-tool dropdown-toggle"
                data-bs-toggle="dropdown"
              >
                <i class="bi bi-wrench"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" role="menu">
                <a href="#" class="dropdown-item">Action</a>
                <a href="#" class="dropdown-item">Another action</a>
                <a href="#" class="dropdown-item"> Something else here </a>
                <a class="dropdown-divider"></a>
                <a href="#" class="dropdown-item">Separated link</a>
              </div>
            </div>
            <button type="button" class="btn btn-tool" data-lte-toggle="card-remove">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <!-- Search Form -->
          <div class="mb-3">
            <form method="GET" action="<?php echo e(route('dashboard')); ?>" class="d-flex">
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
          <div class="table-responsive">
            <?php if($user->hasRole('Super Admin')): ?>
              <table class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
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
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Due Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
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
    <div class="card">
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
  </div>
  <div class="col-md-8">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Messages</h3></div>
      <div class="card-body">
        <p class="mb-2">Unread: <strong><?php echo e($unreadMessages); ?></strong></p>
        <a class="btn btn-sm btn-primary" href="<?php echo e(route('messages.index')); ?>">Open Messages</a>

        <?php if(!auth()->user()->hasTwoFactorEnabled()): ?>
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
</div>
<!--end::Row-->

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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/dashboard.blade.php ENDPATH**/ ?>