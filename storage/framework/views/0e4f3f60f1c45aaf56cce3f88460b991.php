<?php use Carbon\Carbon; ?>


<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?php echo e(__('Productivity Recap')); ?>

          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;"><?php echo e(__('Consolidated view of approved tasks and attendance records.')); ?></p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
          <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-light border rounded-pill px-4 fw-bold text-muted shadow-sm">
            <i class="mdi mdi-arrow-left me-2"></i><?php echo e(__('Dashboard')); ?>

          </a>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="card mb-5 border-0 shadow-sm rounded-4">
          <div class="card-body p-4">
              <form method="GET">
                  <div class="row g-3">
                      <?php if(auth()->user()->hasRole('Super Admin')): ?>
                      <div class="col-xl-3 col-md-6">
                          <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Site')); ?></label>
                          <select name="location_id" class="form-select rounded-pill px-4 border-light shadow-none fw-bold">
                              <option value=""><?php echo e(__('Global Sites')); ?></option>
                              <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($loc->id); ?>" <?php if($locationFilter == $loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name ?? $loc->nama ?? __('Location').' '.$loc->id); ?></option>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          </select>
                      </div>
                      <?php endif; ?>

                      <div class="col-xl-3 col-md-6">
                          <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Member')); ?></label>
                          <select name="employee_id" class="form-select rounded-pill px-4 border-light shadow-none fw-bold">
                              <option value=""><?php echo e(__('Select Colleague')); ?></option>
                              <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($emp->id); ?>" <?php if($employeeId == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->nama ?? $emp->id); ?></option>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          </select>
                      </div>

                      <div class="col-xl-2 col-md-6">
                          <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('From')); ?></label>
                          <input type="date" name="start_date" value="<?php echo e($startDate); ?>" class="form-control rounded-pill px-4 border-light shadow-none">
                      </div>

                      <div class="col-xl-2 col-md-6">
                          <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('To')); ?></label>
                          <input type="date" name="end_date" value="<?php echo e($endDate); ?>" class="form-control rounded-pill px-4 border-light shadow-none">
                      </div>

                      <div class="col-xl-2 col-md-12 d-flex align-items-end gap-2">
                          <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold flex-grow-1 shadow-sm"><?php echo e(__('Recap')); ?></button>
                          <?php if($employeeId): ?>
                            <button type="submit" name="export" value="1" class="btn btn-danger rounded-pill px-3 shadow-sm" title="<?php echo e(__('Export PDF')); ?>">
                              <i class="mdi mdi-file-pdf-box"></i>
                            </button>
                          <?php endif; ?>
                      </div>
                  </div>
              </form>
          </div>
      </div>

      <?php if($employeeId): ?>
      <!-- Statistics Row -->
      <div class="row g-4 mb-5">
          <div class="col-md-3">
              <div class="card p-4 rounded-4 border-0 shadow-sm h-100">
                  <div class="text-muted small fw-bold text-uppercase mb-1"><?php echo e(__('Quota Target')); ?></div>
                  <div class="h3 mb-0 text-dark fw-bold"><?php echo e(number_format($slotSummary['target_minutes'] ?? 0)); ?> <small class="text-muted fw-normal" style="font-size: 0.6em;">min</small></div>
              </div>
          </div>
          <div class="col-md-3">
              <div class="card p-4 rounded-4 border-0 shadow-sm h-100 soft-card-celeste">
                  <div class="text-info small fw-bold text-uppercase mb-1"><?php echo e(__('Slot Verified')); ?></div>
                  <div class="h3 mb-0 text-dark fw-bold"><?php echo e(number_format($slotSummary['slot_minutes'])); ?> <small class="text-muted fw-normal" style="font-size: 0.6em;">min</small></div>
              </div>
          </div>
          <div class="col-md-3">
              <div class="card p-4 rounded-4 border-0 shadow-sm h-100 soft-card-mint">
                  <div class="text-success small fw-bold text-uppercase mb-1"><?php echo e(__('Logged Presence')); ?></div>
                  <div class="h3 mb-0 text-dark fw-bold"><?php echo e(number_format($slotSummary['attendance_minutes'])); ?> <small class="text-muted fw-normal" style="font-size: 0.6em;">min</small></div>
              </div>
          </div>
          <div class="col-md-3">
              <div class="card p-4 rounded-4 border-0 shadow-sm h-100 soft-card-rose">
                  <div class="text-danger small fw-bold text-uppercase mb-1"><?php echo e(__('Remaining')); ?></div>
                  <div class="h3 mb-0 text-dark fw-bold"><?php echo e(number_format($slotSummary['remaining'] ?? 0)); ?> <small class="text-muted fw-normal" style="font-size: 0.6em;">min</small></div>
              </div>
          </div>
      </div>

      <!-- Approved Slots Table -->
      <div class="card border-0 shadow-sm overflow-hidden mb-5">
          <div class="card-header border-bottom border-light p-4 bg-transparent d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-checkbox-marked-circle-outline text-info me-2"></i><?php echo e(__('Verified Task Breakdown')); ?></h5>
          </div>
          <div class="card-body p-0">
              <div class="table-responsive">
                  <table class="table align-middle mb-0">
                      <thead>
                          <tr>
                              <th class="ps-4">No</th>
                              <th><?php echo e(__('Activity')); ?></th>
                              <th><?php echo e(__('Scope')); ?></th>
                              <th class="text-center"><?php echo e(__('Verified Time')); ?></th>
                              <th class="pe-4 text-end"><?php echo e(__('Date Approved')); ?></th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php $__empty_1 = true; $__currentLoopData = $slotDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                              <tr>
                                  <td class="ps-4 text-muted fw-bold" style="width: 60px;"><?php echo e($loop->iteration); ?></td>
                                  <td><div class="fw-bold text-dark"><?php echo e($slot->task->title ?? __('Task').' #'.$slot->task_id); ?></div></td>
                                  <td><span class="badge badge-info"><?php echo e($slot->name); ?></span></td>
                                  <td class="text-center"><span class="fw-bold text-dark"><?php echo e($slot->minutes); ?> m</span></td>
                                  <td class="pe-4 text-end">
                                      <div class="text-dark fw-bold"><?php echo e(optional($slot->approved_at)->format('d M Y')); ?></div>
                                      <div class="text-muted smallest"><?php echo e(optional($slot->approved_at)->format('H:i')); ?></div>
                                  </td>
                              </tr>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                              <tr>
                                  <td colspan="5" class="text-center py-5 opacity-50">
                                      <p class="mb-0 italic"><?php echo e(__('No verified task data.')); ?></p>
                                  </td>
                              </tr>
                          <?php endif; ?>
                      </tbody>
                  </table>
              </div>
          </div>
      </div>

      <!-- Attendance Table -->
      <div class="card border-0 shadow-sm overflow-hidden">
          <div class="card-header border-bottom border-light p-4 bg-transparent d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-clock-check-outline text-success me-2"></i><?php echo e(__('Activity Log Details')); ?></h5>
          </div>
          <div class="card-body p-0">
              <div class="table-responsive">
                  <table class="table align-middle mb-0">
                      <thead>
                          <tr>
                              <th class="ps-4">No</th>
                              <th><?php echo e(__('Work Date')); ?></th>
                              <th class="text-center"><?php echo e(__('Check-In')); ?></th>
                              <th class="text-center"><?php echo e(__('Check-Out')); ?></th>
                              <th class="pe-4 text-end"><?php echo e(__('Duration')); ?></th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php $__empty_1 = true; $__currentLoopData = $attendanceDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                              <tr>
                                  <td class="ps-4 text-muted fw-bold" style="width: 60px;"><?php echo e($loop->iteration); ?></td>
                                  <td><div class="fw-bold text-dark"><?php echo e(optional($att->check_in_time)->format('Y-m-d')); ?></div></td>
                                  <td class="text-center"><span class="badge badge-success px-3"><?php echo e(optional($att->check_in_time)->format('H:i')); ?></span></td>
                                  <td class="text-center">
                                      <?php if($att->check_out_time): ?>
                                        <span class="badge badge-warning px-3"><?php echo e($att->check_out_time->format('H:i')); ?></span>
                                      <?php else: ?>
                                        <span class="text-muted smallest italic">Pending...</span>
                                      <?php endif; ?>
                                  </td>
                                  <td class="pe-4 text-end"><span class="fw-bold text-dark"><?php echo e(number_format($att->duration_minutes ?? 0)); ?> min</span></td>
                                </tr>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                              <tr>
                                  <td colspan="5" class="text-center py-5 opacity-50">
                                      <p class="mb-0 italic"><?php echo e(__('No presence data logged.')); ?></p>
                                  </td>
                              </tr>
                          <?php endif; ?>
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
      <?php else: ?>
          <div class="card border-0 shadow-sm p-5 rounded-4 text-center">
              <div class="py-5">
                  <i class="mdi mdi-account-card-outline text-muted opacity-25" style="font-size: 5rem;"></i>
                  <h4 class="text-dark fw-bold mt-4">Analytic Insight</h4>
                  <p class="text-muted mx-auto" style="max-width: 440px;">Select a team member to access their consolidated work duration and performance metrics.</p>
              </div>
          </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/work-recaps/index.blade.php ENDPATH**/ ?>