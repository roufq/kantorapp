

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
          <h3 class="mb-1">Laporan</h3>
          <p class="text-muted mb-0">Pantau laporan karyawan dan approval</p>
        </div>
        <div class="d-flex gap-2">
          <?php if(auth()->user()->hasRole(['Super Admin','Admin Lokasi','Karyawan'])): ?>
          <a href="<?php echo e(route('reports.create')); ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Buat Laporan
          </a>
          <?php endif; ?>
          <?php if(auth()->user()->hasRole(['Super Admin','Admin Lokasi','Karyawan'])): ?>
          <a href="<?php echo e(route('reports.employee-performance')); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-graph-up me-1"></i>Performa Karyawan
          </a>
          <?php endif; ?>
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <form method="GET" class="row g-2 mb-3">
            <div class="col-md-5">
              <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="Cari tiket / judul / deskripsi">
            </div>
            <div class="col-md-3">
              <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <?php $__currentLoopData = ['pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($key); ?>" <?php if(request('status')===$key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
              <button class="btn btn-secondary" type="submit"><i class="bi bi-search me-1"></i>Filter</button>
              <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-light">Reset</a>
            </div>
          </form>

          <div class="table-responsive">
            <table class="table table-striped align-middle">
              <thead>
                <tr>
                  <th style="width:50px">No</th>
                  <th>Tiket</th>
                  <th>Judul</th>
                  <th>Pelapor</th>
                  <th>Lokasi</th>
                  <th>Status</th>
                  <th>Dibuat</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <tr>
                    <td><?php echo e($loop->iteration + ($reports->currentPage()-1)*$reports->perPage()); ?></td>
                    <td class="fw-semibold"><?php echo e($report->ticket_number); ?></td>
                    <td><?php echo e($report->title); ?></td>
                    <td><?php echo e($report->reporter?->name ?? '-'); ?></td>
                    <td><?php echo e($report->location?->name ?? '-'); ?></td>
                    <td>
                      <span class="badge text-bg-<?php echo e($report->status === 'approved' ? 'success' : ($report->status === 'rejected' ? 'danger' : 'warning')); ?>">
                        <?php echo e(ucfirst($report->status)); ?>

                      </span>
                    </td>
                    <td><?php echo e($report->created_at->format('d M Y H:i')); ?></td>
                    <td class="text-end">
                      <a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('reports.show', $report)); ?>">Lihat</a>
                      <?php if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi')): ?>
                        <a class="btn btn-sm btn-outline-secondary" href="<?php echo e(route('reports.edit', $report)); ?>">Edit</a>
                        <form action="<?php echo e(route('reports.destroy', $report)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus laporan ini?')">
                          <?php echo csrf_field(); ?>
                          <?php echo method_field('DELETE'); ?>
                          <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                        </form>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <tr>
                    <td colspan="7" class="text-center text-secondary">Belum ada laporan.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <div>
            <?php echo e($reports->links()); ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/reports/index.blade.php ENDPATH**/ ?>