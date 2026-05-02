<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?php echo e(__('Colleague Identity')); ?>

          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;"><?php echo e(__('Detailed professional profile and operational assignment.')); ?></p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
          <div class="d-flex justify-content-lg-end gap-3">
            <a href="<?php echo e(route('employees.index')); ?>" class="btn btn-light border rounded-pill px-4 fw-bold text-muted shadow-sm">
                <i class="mdi mdi-arrow-left me-2"></i><?php echo e(__('Staff Directory')); ?>

            </a>
            <a href="<?php echo e(route('employees.edit', $employee)); ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg">
                <i class="mdi mdi-pencil-outline me-2"></i><?php echo e(__('Edit Profile')); ?>

            </a>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm p-4 text-center h-100">
                <div class="mb-4 mt-3">
                    <div class="Avatar-Large rounded-circle d-flex align-items-center justify-content-center fw-bold mx-auto text-primary" style="width: 120px; height: 120px; background-color: var(--soft-celeste); font-size: 2.5rem;">
                        <?php echo e(strtoupper(substr($employee->nama, 0, 1))); ?><?php echo e(strtoupper(substr(strrchr($employee->nama, ' '), 1, 1)) ?: ''); ?>

                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-1"><?php echo e($employee->nama); ?></h3>
                <div class="text-muted small mb-4 fw-bold"><?php echo e($employee->jabatan); ?></div>
                
                <div class="row g-2 mb-4">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-4">
                            <div class="text-muted smallest fw-bold mb-1 text-uppercase letter-spacing-1"><?php echo e(__('Division')); ?></div>
                            <div class="text-dark fw-bold small text-truncate"><?php echo e($employee->division->nama ?? '—'); ?></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-4">
                            <div class="text-muted smallest fw-bold mb-1 text-uppercase letter-spacing-1"><?php echo e(__('Site')); ?></div>
                            <div class="text-dark fw-bold small text-truncate text-info"><?php echo e($employee->location->name ?? '—'); ?></div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-top border-light mt-auto">
                    <div class="d-flex justify-content-between align-items-center text-muted smallest fw-bold">
                        <span><?php echo e(__('REGISTERED SINCE')); ?></span>
                        <span><?php echo e($employee->created_at->format('M Y')); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-header border-bottom border-light p-4 bg-transparent d-flex align-items-center">
                    <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-card-account-details-outline text-primary me-2 fs-4"></i><?php echo e(__('Professional Records')); ?></h5>
                </div>
                <div class="card-body p-4 p-lg-5">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-4 rounded-4 bg-light h-100 transition-all border border-transparent hover-lift">
                                <label class="text-muted smallest fw-bold text-uppercase mb-2 d-block"><?php echo e(__('Direct Contact')); ?></label>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="p-2 soft-card-mint rounded-pill me-3">
                                        <i class="mdi mdi-email-outline fs-5"></i>
                                    </div>
                                    <div class="fw-bold text-dark"><?php echo e($employee->email); ?></div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2 soft-card-celeste rounded-pill me-3">
                                        <i class="mdi mdi-phone-outline fs-5"></i>
                                    </div>
                                    <div class="fw-bold text-dark"><?php echo e($employee->telepon ?: '—'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-4 rounded-4 bg-light h-100 transition-all border border-transparent hover-lift">
                                <label class="text-muted smallest fw-bold text-uppercase mb-2 d-block"><?php echo e(__('Chronological')); ?></label>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="p-2 soft-card-lavender rounded-pill me-3">
                                        <i class="mdi mdi-cake-variant-outline fs-5"></i>
                                    </div>
                                    <div class="fw-bold text-dark"><?php echo e($employee->tanggal_lahir ? $employee->tanggal_lahir->format('d M Y') : '—'); ?></div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="p-2 soft-card-peach rounded-pill me-3">
                                        <i class="mdi mdi-login-variant fs-5"></i>
                                    </div>
                                    <div class="fw-bold text-dark"><?php echo e($employee->tanggal_masuk_kerja ? $employee->tanggal_masuk_kerja->format('d M Y') : '—'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-4 rounded-4 bg-light transition-all border border-transparent hover-lift">
                                <label class="text-muted smallest fw-bold text-uppercase mb-2 d-block"><?php echo e(__('Residential Address')); ?></label>
                                <div class="text-dark fw-medium"><?php echo e($employee->alamat ?: __('No address listed on file.')); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 p-4 soft-card-rose rounded-4 border-0 d-flex align-items-start">
                        <i class="mdi mdi-alert-circle-outline fs-4 me-3"></i>
                        <div>
                            <div class="fw-bold small"><?php echo e(__('Administrative Note')); ?></div>
                            <div class="smallest opacity-75 fw-medium mt-1"><?php echo e(__('Records were last audited on')); ?> <?php echo e($employee->updated_at->format('d M Y')); ?>. <?php echo e(__('Ensure all information matches official documentation.')); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
    .smallest { font-size: 0.7rem; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\employees\show.blade.php ENDPATH**/ ?>