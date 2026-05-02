<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?php echo e(__('Time Off')); ?>

          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;"><?php echo e(__('Manage leave requests and attendance exceptions.')); ?></p>
        </div>
      </div>

      <?php if(isset($balanceSummary)): ?>
        <div class="card p-4 soft-card-mint border-0 mb-5">
            <div class="d-flex align-items-center">
                <div class="avatar-sm me-4 rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="mdi mdi-calendar-check fs-2 text-success"></i>
                </div>
                <div>
                    <h5 class="text-dark fw-bold mb-1"><?php echo e(__('Yearly Leave Balance')); ?> (<?php echo e($balanceSummary['year']); ?>)</h5>
                    <div class="d-flex gap-3 mt-1">
                        <span class="badge bg-white text-info border px-3">Total: <?php echo e($balanceSummary['quota'] + $balanceSummary['carry_over']); ?> d</span>
                        <span class="badge bg-white text-danger border px-3">Used: <?php echo e($balanceSummary['used']); ?> d</span>
                        <span class="badge bg-white text-success border px-3">Left: <?php echo e($balanceSummary['remaining']); ?> d</span>
                    </div>
                </div>
            </div>
        </div>
      <?php endif; ?>

      <div class="row g-4">
        <!-- Leave Request Form -->
        <div class="col-xl-4 col-lg-5">
          <div class="card p-4 h-100 border-0 shadow-sm">
            <h5 class="text-dark fw-bold mb-4 pb-2 border-bottom border-light d-flex align-items-center">
              <i class="mdi mdi-file-document-edit-outline me-2 text-primary"></i><?php echo e(__('Request Form')); ?>

            </h5>

            <form method="POST" action="<?php echo e(route('leaves.store')); ?>">
              <?php echo csrf_field(); ?>
              <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Start Date')); ?></label>
                    <input type="date" name="start_date" class="form-control rounded-pill px-4 border-light shadow-none fw-bold" required />
                  </div>

                  <div class="col-12">
                    <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('End Date')); ?></label>
                    <input type="date" name="end_date" class="form-control rounded-pill px-4 border-light shadow-none fw-bold" required />
                  </div>

                  <div class="col-12">
                    <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Type of Absence')); ?></label>
                    <select name="type" class="form-select rounded-pill px-4 border-light shadow-none fw-bold" required>
                      <option value="sick"><?php echo e(__('Sick Leave')); ?></option>
                      <option value="annual"><?php echo e(__('Annual Leave')); ?></option>
                      <option value="unpaid"><?php echo e(__('Unpaid')); ?></option>
                      <option value="other"><?php echo e(__('Other')); ?></option>
                    </select>
                  </div>

                  <div class="col-12">
                    <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Short Reason')); ?></label>
                    <input type="text" name="reason" class="form-control rounded-pill px-4 border-light shadow-none" placeholder="<?php echo e(__('e.g. Health checkup')); ?>" />
                  </div>

                  <div class="col-12 mt-4 pt-2">
                    <button type="submit" class="btn btn-primary rounded-pill w-100 py-3 fw-bold shadow-lg">
                      <i class="mdi mdi-send-check me-2"></i><?php echo e(__('SUBMIT REQUEST')); ?>

                    </button>
                  </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Leave List -->
        <div class="col-xl-8 col-lg-7">
          <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom border-light p-4 bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-history text-info me-2"></i><?php echo e(__('History')); ?></h5>
                <span class="text-muted small fw-bold"><?php echo e($leaves->total()); ?> Records</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4"><?php echo e(__('No')); ?></th>
                                <th><?php echo e(__('Employee')); ?></th>
                                <th><?php echo e(__('Duration')); ?></th>
                                <th><?php echo e(__('Type')); ?></th>
                                <th><?php echo e(__('Status')); ?></th>
                                <th class="pe-4 text-end"><?php echo e(__('Actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $leaves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                              <?php
                                $statusBadge = match($lv->status) {
                                  'approved' => 'badge-success',
                                  'rejected' => 'badge-danger',
                                  default => 'badge-warning'
                                };
                              ?>
                                <tr>
                                    <td class="ps-4 text-muted fw-bold" style="width: 50px;"><?php echo e($loop->iteration + ($leaves->currentPage()-1)*$leaves->perPage()); ?></td>
                                    <td>
                                      <div class="d-flex align-items-center">
                                          <div class="avatar-xs me-2 rounded-circle bg-light border d-flex align-items-center justify-content-center fw-bold text-primary" style="width:30px; height:30px; font-size: 0.7rem;">
                                              <?php echo e(substr(optional($lv->user)->name, 0, 1)); ?>

                                          </div>
                                          <span class="fw-bold text-dark"><?php echo e(optional($lv->user)->name); ?></span>
                                      </div>
                                    </td>
                                    <td>
                                        <div class="text-dark fw-semibold small"><?php echo e($lv->start_date->format('d M')); ?> — <?php echo e($lv->end_date->format('d M Y')); ?></div>
                                        <div class="text-muted smallest"><?php echo e($lv->start_date->diffInDays($lv->end_date) + 1); ?> Days</div>
                                    </td>
                                    <td><span class="badge badge-info"><?php echo e(strtoupper($lv->type)); ?></span></td>
                                    <td><span class="badge <?php echo e($statusBadge); ?>"><?php echo e(strtoupper($lv->status)); ?></span></td>
                                    <td class="pe-4 text-end">
                                      <?php if(auth()->user()->hasRole('Super Admin') || (auth()->user()->hasRole('Location Admin') && auth()->user()->location_id === $lv->location_id)): ?>
                                        <?php if($lv->status === 'pending'): ?>
                                          <div class="d-flex gap-2 justify-content-end">
                                            <form method="POST" action="<?php echo e(route('leaves.updateStatus', $lv)); ?>" class="d-inline">
                                              <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                              <input type="hidden" name="status" value="approved" />
                                              <button class="btn btn-sm btn-light border rounded-pill px-3 fw-bold text-success shadow-none"><i class="mdi mdi-check"></i></button>
                                            </form>
                                            <form method="POST" action="<?php echo e(route('leaves.updateStatus', $lv)); ?>" class="d-inline">
                                              <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                              <input type="hidden" name="status" value="rejected" />
                                              <button class="btn btn-sm btn-light border rounded-pill px-3 fw-bold text-danger shadow-none"><i class="mdi mdi-close"></i></button>
                                            </form>
                                          </div>
                                        <?php endif; ?>
                                      <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted py-5 text-center">
                                            <i class="mdi mdi-file-document-outline fs-1 d-block mb-3 opacity-25"></i>
                                            <p class="mb-0"><?php echo e(__('No entries found.')); ?></p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if($leaves->hasPages()): ?>
                <div class="card-footer bg-transparent border-top border-light p-4 d-flex justify-content-end">
                    <?php echo e($leaves->links()); ?>

                </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\leaves\index.blade.php ENDPATH**/ ?>