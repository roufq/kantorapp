<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border">
  <div>
    <h3 class="mb-1">Hari Libur</h3>
    <p class="text-muted mb-0">Kelola daftar hari libur nasional maupun lokal.</p>
  </div>
</div>
<div class="row">
  <div class="col-12 mb-3">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Tambah Holiday</h3>
        <form method="GET" action="<?php echo e(route('holidays.index')); ?>" class="d-flex gap-2">
          <select name="type" class="form-select form-select-sm" style="width:auto">
            <option value="">Jenis: Semua</option>
            <option value="national" <?php if(request('type')==='national'): echo 'selected'; endif; ?>>Nasional</option>
            <option value="local" <?php if(request('type')==='local'): echo 'selected'; endif; ?>>Lokal</option>
          </select>
          <?php if(auth()->user()->hasRole('Super Admin')): ?>
          <select name="location_id" class="form-select form-select-sm" style="width:auto">
            <option value="">Lokasi: Semua</option>
            <?php $__currentLoopData = \App\Models\Location::orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($loc->id); ?>" <?php if(request('location_id')==$loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <?php endif; ?>
          <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="form-control form-control-sm" style="width:auto"/>
          <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="form-control form-control-sm" style="width:auto"/>
          <button class="btn btn-sm btn-outline-primary" type="submit">Filter</button>
        </form>
      </div>
      <div class="card-body">
        <?php if($errors->any()): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          </div>
        <?php endif; ?>
        <?php if(session('success')): ?>
          <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo e(route('holidays.store')); ?>" class="row g-2">
          <?php echo csrf_field(); ?>
          <div class="col-md-4">
            <label class="form-label">Tanggal</label>
            <input type="date" name="date" class="form-control" required />
          </div>
          <div class="col-md-8">
            <label class="form-label">Nama Libur</label>
            <input type="text" name="name" class="form-control" placeholder="Contoh: Hari Kemerdekaan" required />
          </div>
          <?php if(auth()->user()->hasRole('Super Admin')): ?>
          <div class="col-md-4">
            <div class="form-check mt-4">
              <input class="form-check-input" type="checkbox" name="is_national" id="is_national" />
              <label class="form-check-label" for="is_national">Libur Nasional</label>
            </div>
          </div>
          <div class="col-md-8">
            <label class="form-label">Location (Opsional untuk non-nasional)</label>
            <select name="location_id" id="location_id" class="form-select">
              <option value="">- none - (nasional)</option>
              <?php $__currentLoopData = \App\Models\Location::orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($loc->id); ?>"><?php echo e($loc->name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <?php else: ?>
            <div class="col-md-12 small text-muted">Admin Lokasi: Holiday akan tersimpan untuk lokasi Anda.</div>
          <?php endif; ?>
          <div class="col-12">
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Daftar Holiday</h3></div>
      <div class="card-body table-responsive">
        <table class="table table-sm table-bordered">
          <thead>
            <tr>
              <th style="width:50px">No</th><th>Tanggal</th><th>Nama</th><th>Jenis</th><th>Lokasi</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $holidays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($loop->iteration + (method_exists($holidays,'currentPage') ? ($holidays->currentPage()-1)*$holidays->perPage() : 0)); ?></td>
              <td><?php echo e($h->date->format('Y-m-d')); ?></td>
              <td><?php echo e($h->name); ?></td>
              <td><?php echo e($h->is_national ? 'Nasional' : 'Lokal'); ?></td>
              <td><?php echo e($h->is_national ? 'Semua Lokasi' : (optional(\App\Models\Location::find($h->location_id))->name ?? '-')); ?></td>
              <td>
                <form method="POST" action="<?php echo e(route('holidays.destroy', $h)); ?>" onsubmit="return confirm('Hapus holiday ini?')">
                  <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                  <button class="btn btn-sm btn-danger">Hapus</button>
                </form>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5" class="text-center">Belum ada data</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
        <?php if(method_exists($holidays, 'links')): ?>
          <div class="mt-2"><?php echo e($holidays->links()); ?></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
  document.addEventListener('DOMContentLoaded', function(){
    const national = document.getElementById('is_national');
    const loc = document.getElementById('location_id');
    if (national && loc) {
      function toggle(){
        if (national.checked) {
          loc.value = '';
          loc.setAttribute('disabled', 'disabled');
        } else {
          loc.removeAttribute('disabled');
        }
      }
      national.addEventListener('change', toggle);
      toggle();
    }
  });
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/holidays/index.blade.php ENDPATH**/ ?>