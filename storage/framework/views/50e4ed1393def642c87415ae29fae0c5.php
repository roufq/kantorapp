

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
          <h3 class="mb-1"><?php echo e($report->title); ?></h3>
          <p class="text-muted mb-0">Tiket: <?php echo e($report->ticket_number); ?></p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
          <?php if(auth()->user()->hasRole(['Super Admin','Admin Lokasi','Karyawan'])): ?>
          <a href="<?php echo e(route('reports.create')); ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Buat Laporan</a>
          <?php endif; ?>
          <?php if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi')): ?>
          <a href="<?php echo e(route('reports.edit', $report)); ?>" class="btn btn-outline-secondary btn-sm">Edit</a>
          <form action="<?php echo e(route('reports.destroy', $report)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus laporan ini?')">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
          </form>
          <?php endif; ?>
        </div>
      </div>
      <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
      <?php endif; ?>
      <?php if($errors->any()): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="row g-3">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                  <div class="text-secondary small">Status</div>
                  <span class="badge text-bg-<?php echo e($report->status === 'approved' ? 'success' : ($report->status === 'rejected' ? 'danger' : 'warning')); ?>">
                    <?php echo e(ucfirst($report->status)); ?>

                  </span>
                </div>
                <div class="text-end small text-secondary">
                  Dibuat: <?php echo e($report->created_at->format('d M Y H:i')); ?><br>
                  <?php if($report->finalized_at): ?>
                    Final: <?php echo e($report->finalized_at->format('d M Y H:i')); ?>

                  <?php endif; ?>
                </div>
              </div>

              <div class="mb-3">
                <div class="text-secondary small">Pelapor</div>
                <div><?php echo e($report->reporter?->name); ?> (<?php echo e($report->reporter?->getRoleNames()->first()); ?>)</div>
              </div>
              <div class="mb-3">
                <div class="text-secondary small">Lokasi</div>
                <div><?php echo e($report->location?->name ?? '-'); ?></div>
              </div>
              <div class="mb-3">
                <div class="text-secondary small">Admin Lokasi Assigned</div>
                <div><?php echo e($report->assignedAdmin?->name ?? 'Tidak ada / langsung ke Super Admin'); ?></div>
              </div>

              <div class="mb-3">
                <div class="text-secondary small">Deskripsi</div>
                <p class="mb-0"><?php echo e($report->description); ?></p>
              </div>

              <div class="mb-3">
                <div class="text-secondary small mb-1">Lampiran</div>
                <?php if($report->attachments->isEmpty()): ?>
                  <div class="text-secondary">Tidak ada lampiran.</div>
                <?php else: ?>
                  <div class="row g-2">
                    <?php $__currentLoopData = $report->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <?php
                        $normalized = ltrim(str_replace('\\','/',$att->file_path), '/');
                        $url = asset('storage/' . $normalized);
                        $isImage = \Illuminate\Support\Str::startsWith($att->mime_type ?? '', 'image/');
                      ?>
                      <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                          <div>
                            <i class="bi bi-paperclip me-1"></i><?php echo e($att->original_name); ?>

                            <span class="text-secondary small ms-2"><?php echo e(number_format($att->file_size / 1024, 1)); ?> KB</span>
                          </div>
                          <a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('reports.attachments.download', $att)); ?>"><i class="bi bi-download"></i></a>
                        </div>
                        <?php if($isImage): ?>
                          <div class="mb-3">
                            <img src="<?php echo e($url); ?>" alt="<?php echo e($att->original_name); ?>" class="img-fluid rounded shadow-sm">
                          </div>
                        <?php endif; ?>
                      </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </div>
                <?php endif; ?>
              </div>

              <?php if($pendingApproval && auth()->user()->hasRole('Admin Lokasi') && $pendingApproval->approver_role === 'admin_lokasi'): ?>
                <?php echo $__env->make('reports.partials._approval_form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
              <?php elseif($pendingApproval && auth()->user()->hasRole('Super Admin') && $pendingApproval->approver_role === 'super_admin'): ?>
                <?php echo $__env->make('reports.partials._approval_form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h6 class="mb-0">Log Approval</h6>
            </div>
            <div class="card-body">
              <div class="timeline">
                <?php $__currentLoopData = $report->approvals->sortBy('step_order'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="timeline-item mb-3">
                    <div class="d-flex justify-content-between">
                      <strong><?php echo e(strtoupper($appr->approver_role)); ?></strong>
                      <span class="badge text-bg-<?php echo e($appr->status === 'approved' ? 'success' : ($appr->status === 'rejected' ? 'danger' : 'secondary')); ?>">
                        <?php echo e(ucfirst($appr->status)); ?>

                      </span>
                    </div>
                    <div class="small text-secondary">
                      Approver: <?php echo e($appr->approver?->name ?? '-'); ?><br>
                      <?php if($appr->decided_at): ?> <?php echo e($appr->decided_at->format('d M Y H:i')); ?> <?php endif; ?>
                    </div>
                    <?php if($appr->notes): ?>
                      <div class="mt-1"><?php echo e($appr->notes); ?></div>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\reports\show.blade.php ENDPATH**/ ?>