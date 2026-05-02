

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;"><?php echo e(__('Attendance Recap')); ?></h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;"><?php echo e(__('Condensed summary of presence, leaves, and offs.')); ?></p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-arrow-left me-2 fs-5 align-middle"></i><?php echo e(__('Back')); ?>

          </a>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="card shadow-sm border-0 p-4 rounded-4 border border-light shadow-lg mb-4">
          <form method="GET">
              <div class="row g-3">
                  <div class="col-xl-2 col-md-4">
                      <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2"><?php echo e(__('Start Date')); ?></label>
                      <input type="date" name="start_date" class="form-control bg-light border-0 text-dark rounded-pill px-4 shadow-none" value="<?php echo e(request('start_date', $start)); ?>">
                  </div>
                  <div class="col-xl-2 col-md-4">
                      <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2"><?php echo e(__('End Date')); ?></label>
                      <input type="date" name="end_date" class="form-control bg-light border-0 text-dark rounded-pill px-4 shadow-none" value="<?php echo e(request('end_date', $end)); ?>">
                  </div>

                  <?php if(auth()->user()->hasRole('Super Admin')): ?>
                  <div class="col-xl-2 col-md-4">
                      <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2"><?php echo e(__('Location')); ?></label>
                      <select name="location_id" class="form-select bg-light border-0 text-dark rounded-pill px-4 shadow-none">
                          <option value=""><?php echo e(__('All')); ?></option>
                          <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($loc->id); ?>" <?php if(request('location_id')==$loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name); ?></option>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </select>
                  </div>
                  <?php endif; ?>

                  <div class="col-xl-2 col-md-4">
                      <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1 mb-2"><?php echo e(__('User (optional)')); ?></label>
                      <?php
                        $uQuery = \App\Models\User::orderBy('name');
                        if(auth()->user()->hasRole('Location Admin')){ $uQuery->where('location_id', auth()->user()->location_id); }
                        if(auth()->user()->hasRole('Employee')){ $uQuery->where('id', auth()->id()); }
                        $users = $uQuery->get();
                      ?>
                      <select name="user_id" class="form-select bg-light border-0 text-dark rounded-pill px-4 shadow-none">
                          <option value=""><?php echo e(__('All')); ?></option>
                          <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($u->id); ?>" <?php if(request('user_id')==$u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </select>
                  </div>

                  <div class="col-xl-4 col-md-8 d-flex align-items-end gap-2">
                      <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-lg flex-grow-1">
                          <i class="mdi mdi-filter-variant me-1"></i><?php echo e(__('Apply')); ?>

                      </button>
                      <button type="submit" formaction="<?php echo e(route('attendance.recap.export', array_merge(request()->query(), ['format' => 'xlsx']))); ?>" class="btn btn-success rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px;" title="<?php echo e(__('Export Excel')); ?>">
                        <i class="mdi mdi-file-excel-box fs-5"></i>
                      </button>
                      <button type="submit" formaction="<?php echo e(route('attendance.recap.export', array_merge(request()->query(), ['format' => 'csv']))); ?>" class="btn btn-outline-success rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px;" title="<?php echo e(__('Export CSV')); ?>">
                        <i class="mdi mdi-file-delimited-outline fs-5"></i>
                      </button>
                      <a href="<?php echo e(route('attendance.recap')); ?>" class="btn btn-outline-secondary rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px;" title="<?php echo e(__('Reset')); ?>">
                          <i class="mdi mdi-refresh fs-5"></i>
                      </a>
                  </div>
              </div>
          </form>
      </div>

      <div class="card shadow-sm border-0 mb-4" style="border-radius: 24px !important;">
          <div class="card-header border-bottom border-light p-4 bg-transparent">
              <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-chart-box-outline text-info me-2 fs-4"></i><?php echo e(__('Recap')); ?></h5>
          </div>
          <div class="card-body p-0">
              <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0 text-white">
                      <thead class="bg-white bg-opacity-5">
                          <tr>
                              <th class="ps-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1" style="width: 70px"><?php echo e(__('No')); ?></th>
                              <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1"><?php echo e(__('User')); ?></th>
                              <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Location')); ?></th>
                              <th class="py-3 text-center text-muted small fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Total Days')); ?></th>
                              <th class="py-3 text-center text-muted small fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Holiday')); ?></th>
                              <th class="py-3 text-center text-muted small fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Weekly Off')); ?></th>
                              <th class="py-3 text-center text-muted small fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Leave')); ?></th>
                              <th class="py-3 text-center text-muted small fw-bold text-uppercase letter-spacing-1 bg-white bg-opacity-5"><?php echo e(__('Working Days')); ?></th>
                              <th class="py-3 text-center text-muted small fw-bold text-uppercase letter-spacing-1 text-success"><?php echo e(__('Present')); ?></th>
                              <th class="pe-4 py-3 text-center text-muted small fw-bold text-uppercase letter-spacing-1 text-danger"><?php echo e(__('Alpha')); ?></th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                              <tr class="border-bottom border-white border-opacity-5">
                                  <td class="ps-4 text-muted fw-bold"><?php echo e($loop->iteration); ?></td>
                                  <td><div class="fw-bold"><?php echo e($r['user']->name); ?></div></td>
                                  <td><div class="text-muted smaller"><i class="mdi mdi-map-marker-outline me-1"></i><?php echo e(optional($r['location'])->name); ?></div></td>
                                  <td class="text-center"><?php echo e($r['totalDays']); ?></td>
                                  <td class="text-center text-info"><?php echo e($r['holidayDays']); ?></td>
                                  <td class="text-center text-warning"><?php echo e($r['weeklyOffDays']); ?></td>
                                  <td class="text-center text-primary"><?php echo e($r['leaveDays']); ?></td>
                                  <td class="text-center bg-white bg-opacity-5 fw-bold"><?php echo e($r['workingDays']); ?></td>
                                  <td class="text-center"><span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 fs-6"><?php echo e($r['presentDays']); ?></span></td>
                                  <td class="pe-4 text-center">
                                      <?php if($r['alphaDays'] > 0): ?>
                                        <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 fs-6"><?php echo e($r['alphaDays']); ?></span>
                                      <?php else: ?>
                                        <span class="text-muted opacity-50">-</span>
                                      <?php endif; ?>
                                  </td>
                              </tr>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                              <tr>
                                  <td colspan="10" class="text-center py-5">
                                      <i class="mdi mdi-chart-areaspline fs-1 text-muted d-block mb-2"></i>
                                      <span class="text-muted"><?php echo e(__('No data')); ?></span>
                                  </td>
                              </tr>
                          <?php endif; ?>
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\attendances\recap.blade.php ENDPATH**/ ?>