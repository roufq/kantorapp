

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;"><?php echo e(__('Overtime Detail')); ?></h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;"><?php echo e(__('Overtime request for date')); ?> <?php echo e($overtime->date->format('d M Y')); ?></p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <a href="<?php echo e(route('overtime.index')); ?>" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-arrow-left me-2 fs-5 align-middle"></i><?php echo e(__('Back')); ?>

          </a>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-lg-8">
          <div class="card shadow-sm border-0 mb-4" style="border-radius: 24px !important;">
            <div class="card-header border-bottom border-light p-4 d-flex justify-content-between align-items-center" style="background: #f8fafc;">
                 <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-information-outline me-2 text-info"></i><?php echo e(__('Overtime Request Details')); ?></h5>
                 <?php if(auth()->user()->hasRole('Employee') && $overtime->user_id === auth()->id() && $overtime->status === 'pending'): ?>
                    <a href="<?php echo e(route('overtime.edit', $overtime)); ?>" class="btn btn-sm btn-outline-info rounded-pill px-3 fw-bold">
                        <i class="mdi mdi-pencil-outline me-1"></i><?php echo e(__('Edit')); ?>

                    </a>
                 <?php endif; ?>
            </div>
            <div class="card-body p-4">
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="text-muted smaller fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Employee:')); ?></label>
                            <div class="text-dark fw-bold fs-5"><?php echo e($overtime->user->name); ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted smaller fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Date:')); ?></label>
                            <div class="text-dark"><i class="mdi mdi-calendar-outline me-1 text-info"></i><?php echo e($overtime->date->format('d M Y')); ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted smaller fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Time:')); ?></label>
                            <div class="text-dark"><i class="mdi mdi-clock-outline me-1 text-warning"></i><?php echo e($overtime->start_time_wib); ?> - <?php echo e($overtime->end_time_wib); ?> WIB</div>
                        </div>
                        <div class="mb-0">
                            <label class="text-muted smaller fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Duration:')); ?></label>
                            <div class="text-dark fw-bold"><?php echo e(number_format($overtime->duration_minutes, 0)); ?> <?php echo e(__('minutes')); ?></div>
                        </div>
                    </div>
                    <?php
                        $statusColor = match($overtime->status) {
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default => 'warning'
                        };
                    ?>
                    <div class="col-md-6 border-start border-light ps-md-4">
                        <div class="mb-3">
                            <label class="text-muted smaller fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Status:')); ?></label>
                            <div class="mt-1">
                                <span class="badge rounded-pill bg-<?php echo e($statusColor); ?> bg-opacity-25 text-<?php echo e($statusColor); ?> fw-bold px-3 py-2 shadow-sm">
                                    <i class="mdi mdi-circle-medium me-1"></i><?php echo e(strtoupper(__($overtime->status))); ?>

                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted smaller fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Created:')); ?></label>
                            <div class="text-muted small"><?php echo e($overtime->created_at->format('d M Y H:i')); ?></div>
                        </div>
                        <div class="mb-0">
                            <label class="text-muted smaller fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Updated:')); ?></label>
                            <div class="text-muted small"><?php echo e($overtime->updated_at->format('d M Y H:i')); ?></div>
                        </div>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="text-muted smaller fw-bold text-uppercase letter-spacing-1 mb-2 d-block"><?php echo e(__('Reason:')); ?></label>
                    <div class="p-3 bg-dark bg-opacity-25 rounded-3 border border-white border-opacity-5">
                        <p class="text-dark mb-0 italic" style="line-height: 1.6;">"<?php echo e($overtime->reason); ?>"</p>
                    </div>
                </div>
            </div>
          </div>

          <?php if(auth()->user()->hasAnyRole(['Super Admin', 'Location Admin']) && in_array(auth()->id(), $overtime->selected_masters)): ?>
            <?php
                $userApproval = $overtime->approvals->where('master_id', auth()->id())->first();
            ?>
            <?php if($userApproval && $userApproval->status === 'pending'): ?>
                <div class="card shadow-sm border-0" style="border-radius: 24px !important;">
                    <div class="card-header border-bottom border-light p-4" style="background: #f8fafc;">
                        <h5 class="mb-0 text-dark fw-bold"><i class="mdi mdi-check-circle-outline me-2 text-success"></i><?php echo e(__('Make Decision')); ?></h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="<?php echo e(route('overtime.approve', $overtime)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Decision')); ?></label>
                                    <select name="status" class="form-select rounded-pill px-4 shadow-sm" required>
                                        <option value="approved"><?php echo e(__('Approve')); ?></option>
                                        <option value="rejected"><?php echo e(__('Reject')); ?></option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Notes (Optional)')); ?></label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="<?php echo e(__('Add any notes...')); ?>" style="border-radius: 15px !important;"></textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-lg">
                                        <i class="mdi mdi-check-all me-2"></i><?php echo e(__('Submit Decision')); ?>

                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>

        <div class="col-lg-4">
          <div class="card shadow-sm border-0 h-100" style="border-radius: 24px !important;">
            <div class="card-header border-bottom border-light p-4 d-flex align-items-center" style="background: #f8fafc;">
                <h5 class="mb-0 text-dark fw-bold"><i class="mdi mdi-shield-check-outline me-2 text-warning"></i><?php echo e(__('Approval Status')); ?></h5>
            </div>
            <div class="card-body p-4">
                <div class="d-flex flex-column gap-3">
                    <?php $__currentLoopData = $overtime->selected_masters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $masterId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $master = \App\Models\User::find($masterId);
                            $approval = $overtime->approvals->where('master_id', $masterId)->first();
                            $appStatusColor = match($approval?->status) {
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'secondary'
                            };
                        ?>
                        <div class="card shadow-sm border-0 p-3 rounded-4 border border-light shadow-sm transition-all hover-lift">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="text-dark fw-bold small"><i class="mdi mdi-account-circle-outline me-1 text-info"></i><?php echo e($master->name); ?></div>
                                <span class="badge rounded-pill bg-<?php echo e($appStatusColor); ?> bg-opacity-25 text-<?php echo e($appStatusColor); ?> fw-bold px-2 py-1 smaller">
                                    <?php echo e(strtoupper($approval ? __($approval->status) : __('Pending'))); ?>

                                </span>
                            </div>
                            
                            <?php if($approval && $approval->approved_at): ?>
                                <div class="smaller text-muted mt-2 border-top border-white border-opacity-5 pt-2">
                                    <i class="mdi mdi-clock-check-outline me-1"></i><?php echo e(__('Approved at:')); ?> <?php echo e($approval->approved_at->format('d M Y H:i')); ?>

                                </div>
                            <?php endif; ?>
                            
                            <?php if($approval && $approval->notes): ?>
                                <div class="mt-2 text-info smaller italic border-start border-info border-2 ps-2">
                                    <i class="mdi mdi-comment-outline me-1"></i>"<?php echo e($approval->notes); ?>"
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.italic { font-style: italic; }
.smaller { font-size: 0.8rem; }
.letter-spacing-1 { letter-spacing: 1px; }
.hover-lift:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.4) !important; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\overtime\show.blade.php ENDPATH**/ ?>