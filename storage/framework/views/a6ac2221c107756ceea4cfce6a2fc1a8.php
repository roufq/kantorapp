<?php $__env->startSection('content'); ?>
<!-- Header Row -->
<div class="row mb-4 align-items-center">
    <div class="col-lg-7">
        <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;"><?php echo e($report->title); ?></h2>
        <p class="text-muted mb-0" style="font-size: 1.05rem;">
            <?php echo e(__('Ticket Number')); ?>: <span class="text-primary fw-bold"><?php echo e($report->ticket_number); ?></span>
        </p>
    </div>
    <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
        <div class="d-flex justify-content-lg-end gap-2 flex-wrap">
            <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-light text-dark border bg-white rounded-pill px-4 fw-bold shadow-sm">
                <i class="mdi mdi-keyboard-backspace me-2 fs-5 align-middle"></i><?php echo e(__('Back')); ?>

            </a>
            <?php if(auth()->user()->hasRole(['Super Admin','Location Admin','Employee'])): ?>
                <a href="<?php echo e(route('reports.create')); ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-soft">
                    <i class="mdi mdi-plus-circle-outline me-2 fs-5 align-middle"></i><?php echo e(__('New Report')); ?>

                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="alert badge-success bg-opacity-10 border-0 p-3 mb-4 rounded-4 d-flex align-items-center">
        <i class="mdi mdi-check-circle-outline fs-4 me-2"></i>
        <span class="fw-bold smaller" style="text-transform: none; letter-spacing: 0;"><?php echo e(session('success')); ?></span>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Main Content -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-light mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted smaller fw-bold text-uppercase"><?php echo e(__('Status')); ?></span>
                        <span class="badge <?php echo e($report->status === 'approved' ? 'badge-success' : ($report->status === 'rejected' ? 'badge-danger' : 'badge-warning')); ?>">
                            <?php echo e(ucfirst($report->status)); ?>

                        </span>
                    </div>
                    <div class="text-end smaller text-muted fw-bold">
                        <span class="d-block"><?php echo e(__('CREATED')); ?>: <span class="text-dark"><?php echo e($report->created_at->format('d M Y H:i')); ?></span></span>
                    </div>
                </div>

                <div class="row mb-5 g-4">
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 border border-light h-100 bg-white shadow-soft">
                            <div class="text-muted smaller fw-bold text-uppercase mb-2"><?php echo e(__('Reporter Identity')); ?></div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 48px; height: 48px; font-size: 1rem;">
                                    <?php echo e(substr($report->reporter?->name ?? '?', 0, 1)); ?>

                                </div>
                                <div>
                                    <div class="text-dark fw-bold"><?php echo e($report->reporter?->name); ?></div>
                                    <div class="text-primary smaller fw-bold"><?php echo e($report->reporter?->getRoleNames()->first()); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 border border-light h-100 bg-white shadow-soft">
                            <div class="text-muted smaller fw-bold text-uppercase mb-2"><?php echo e(__('Verification Locale')); ?></div>
                            <div class="text-dark fw-bold d-flex align-items-center mb-1">
                                <i class="mdi mdi-map-marker-radius text-danger me-2 fs-5"></i> 
                                <?php echo e($report->location?->name ?? 'Headquarters'); ?>

                            </div>
                            <div class="smaller text-muted italic">Verified via geo-tagging</div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-muted smaller fw-bold mb-2 text-uppercase letter-spacing-1"><?php echo e(__('Assigned Reviewer')); ?></label>
                    <div class="p-3 bg-light rounded-4 border border-light d-flex align-items-center gap-2">
                        <i class="mdi mdi-shield-account-outline text-info fs-5"></i>
                        <span class="text-dark fw-bold small"><?php echo e($report->assignedAdmin?->name ?? __('Direct to Executive Team')); ?></span>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="text-muted smaller fw-bold mb-2 text-uppercase letter-spacing-1"><?php echo e(__('Detailed Narrative')); ?></label>
                    <div class="p-4 bg-light rounded-4 border border-light" style="background-color: #fbfcff !important;">
                        <p class="text-dark mb-0" style="line-height: 1.8; font-size: 1rem; opacity: 0.9; white-space: pre-line;"><?php echo e($report->description); ?></p>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="text-muted smaller fw-bold mb-3 text-uppercase letter-spacing-1"><?php echo e(__('Evidence & Media')); ?></label>
                    <?php if($report->attachments->isEmpty()): ?>
                        <div class="text-center py-4 bg-light rounded-4 border border-dashed">
                            <i class="mdi mdi-file-hidden-outline fs-2 text-muted opacity-50"></i>
                            <p class="text-muted smaller italic mt-2"><?php echo e(__('No evidence provided.')); ?></p>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php $__currentLoopData = $report->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $normalized = ltrim(str_replace('\\','/',$att->file_path), '/');
                                    $url = asset('storage/' . $normalized);
                                    $isImage = \Illuminate\Support\Str::startsWith($att->mime_type ?? '', 'image/');
                                ?>
                                <div class="col-md-6">
                                    <div class="card overflow-hidden h-100 border-light shadow-soft" style="border-radius: 20px;">
                                        <?php if($isImage): ?>
                                            <div class="position-relative overflow-hidden" style="height: 200px;">
                                                <img src="<?php echo e($url); ?>" alt="" class="w-100 h-100 object-fit-cover">
                                                <div class="position-absolute bottom-0 start-0 w-100 p-2 bg-dark bg-opacity-25" style="backdrop-filter: blur(4px);">
                                                    <div class="text-white smaller text-truncate fw-bold"><?php echo e($att->original_name); ?></div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="text-dark fw-bold smaller text-truncate" style="max-width: 10rem;"><?php echo e($att->original_name); ?></div>
                                                <div class="smaller text-muted"><?php echo e(number_format($att->file_size / 1024, 1)); ?> KB</div>
                                            </div>
                                            <a href="<?php echo e(route('reports.attachments.download', $att)); ?>" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center shadow-soft" style="width: 36px; height: 36px;">
                                                <i class="mdi mdi-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if($pendingApproval && ((auth()->user()->hasRole('Location Admin') && $pendingApproval->approver_role === 'admin_lokasi') || (auth()->user()->hasRole('Super Admin') && $pendingApproval->approver_role === 'super_admin'))): ?>
                    <div class="mt-5 pt-4 border-top border-light">
                        <?php echo $__env->make('reports.partials._approval_form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="card-footer bg-white border-top border-light p-4 d-flex justify-content-end gap-2">
                <?php if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Location Admin')): ?>
                    <a href="<?php echo e(route('reports.edit', $report)); ?>" class="btn btn-sm btn-outline-info rounded-pill px-4 fw-bold">
                        <i class="mdi mdi-pencil-outline me-1"></i><?php echo e(__('Revise')); ?>

                    </a>
                    <form action="<?php echo e(route('reports.destroy', $report)); ?>" method="POST" class="d-inline" onsubmit="return confirm('<?php echo e(__('Delete report?')); ?>')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-4 fw-bold">
                            <i class="mdi mdi-trash-can-outline me-1"></i><?php echo e(__('Discard')); ?>

                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Side Panel -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-light h-100">
            <div class="card-header bg-white border-bottom border-light p-4">
                <h5 class="mb-0 text-dark fw-bold"><i class="mdi mdi-history me-2 text-warning"></i><?php echo e(__('Approval Ledger')); ?></h5>
            </div>
            <div class="card-body p-4">
                <div class="timeline">
                    <?php $__empty_1 = true; $__currentLoopData = $report->approvals->sortBy('step_order'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="mb-4 position-relative ps-4 border-start border-light pb-2">
                            <div class="position-absolute top-0 start-0 translate-middle-x rounded-circle" 
                                 style="width: 14px; height: 14px; margin-left: -1px; background: <?php echo e($appr->status === 'approved' ? 'var(--primary)' : ($appr->status === 'rejected' ? '#f43f5e' : '#cbd5e1')); ?>; box-shadow: 0 0 0 4px #fff, 0 4px 10px rgba(0,0,0,0.05);">
                            </div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="text-dark fw-bold smaller text-uppercase"><?php echo e(str_replace('_', ' ', $appr->approver_role)); ?></span>
                                <span class="badge <?php echo e($appr->status === 'approved' ? 'badge-success' : ($appr->status === 'rejected' ? 'badge-danger' : 'badge-secondary')); ?> rounded-pill" style="font-size: 0.6rem !important;">
                                    <?php echo e(ucfirst($appr->status)); ?>

                                </span>
                            </div>
                            <div class="text-muted smaller mb-2 fw-bold">
                                BY: <span class="text-primary"><?php echo e($appr->approver?->name ?? 'Awaiting'); ?></span>
                            </div>
                            <?php if($appr->decided_at): ?>
                                <div class="smaller text-muted mb-3"><i class="mdi mdi-clock-outline me-1"></i><?php echo e($appr->decided_at->format('d M, H:i')); ?></div>
                            <?php endif; ?>
                            <?php if($appr->notes): ?>
                                <div class="p-3 bg-light rounded-3 smaller text-dark-blue italic border border-light">
                                    <i class="mdi mdi-comment-text-multiple-outline me-2 text-muted"></i> "<?php echo e($appr->notes); ?>"
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-4">
                            <p class="text-muted smaller italic mb-0"><?php echo e(__('Ledger is currently empty.')); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .smaller { font-size: 0.75rem; }
    .letter-spacing-1 { letter-spacing: 0.5px; }
    .shadow-soft { box-shadow: 0 10px 30px rgba(0,0,0,0.03) !important; }
    .text-dark-blue { color: #1e3a8a; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/reports/show.blade.php ENDPATH**/ ?>