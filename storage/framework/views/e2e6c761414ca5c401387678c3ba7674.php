<?php $__env->startSection('title'); ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-6"><h3 class="mb-0">Leaves</h3></div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-end">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Leaves</li>
      </ol>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="row">
  <div class="col-md-5">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Ajukan Izin/Cuti</h3></div>
      <div class="card-body">
        <form method="POST" action="<?php echo e(route('leaves.store')); ?>" class="row g-2">
          <?php echo csrf_field(); ?>
          <div class="col-md-6">
            <label class="form-label">Mulai</label>
            <input type="date" name="start_date" class="form-control" required />
          </div>
          <div class="col-md-6">
            <label class="form-label">Selesai</label>
            <input type="date" name="end_date" class="form-control" required />
          </div>
          <div class="col-md-6">
            <label class="form-label">Tipe</label>
            <select name="type" class="form-select" required>
              <option value="sick">Sakit</option>
              <option value="annual">Cuti Tahunan</option>
              <option value="unpaid">Unpaid</option>
              <option value="other">Lainnya</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Alasan (opsional)</label>
            <input type="text" name="reason" class="form-control" />
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary">Kirim</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-7">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Daftar Izin/Cuti</h3></div>
      <div class="card-body table-responsive">
        <table class="table table-sm table-bordered">
          <thead>
            <tr>
              <th>Karyawan</th><th>Rentang</th><th>Tipe</th><th>Status</th><th>Alasan</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $leaves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e(optional($lv->user)->name); ?></td>
              <td><?php echo e($lv->start_date->format('Y-m-d')); ?> s/d <?php echo e($lv->end_date->format('Y-m-d')); ?></td>
              <td><?php echo e(ucfirst($lv->type)); ?></td>
              <td>
                <?php switch($lv->status):
                  case ('pending'): ?> <span class="badge text-bg-warning">Pending</span> <?php break; ?>
                  <?php case ('approved'): ?> <span class="badge text-bg-success">Approved</span> <?php break; ?>
                  <?php case ('rejected'): ?> <span class="badge text-bg-danger">Rejected</span> <?php break; ?>
                <?php endswitch; ?>
              </td>
              <td><?php echo e($lv->reason); ?></td>
              <td>
                <?php if(auth()->user()->hasRole('Super Admin') || (auth()->user()->hasRole('Admin Lokasi') && auth()->user()->location_id === $lv->location_id)): ?>
                  <div class="d-flex gap-1">
                    <form method="POST" action="<?php echo e(route('leaves.updateStatus', $lv)); ?>">
                      <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                      <input type="hidden" name="status" value="approved" />
                      <button class="btn btn-sm btn-success">Approve</button>
                    </form>
                    <form method="POST" action="<?php echo e(route('leaves.updateStatus', $lv)); ?>">
                      <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                      <input type="hidden" name="status" value="rejected" />
                      <button class="btn btn-sm btn-danger">Reject</button>
                    </form>
                  </div>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" class="text-center">Belum ada data</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
        <?php echo e($leaves->links()); ?>

      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/leaves/index.blade.php ENDPATH**/ ?>