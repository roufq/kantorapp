<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border">
  <div>
    <h3 class="mb-1">Weekly Off</h3>
    <p class="text-muted mb-0">Atur hari libur mingguan untuk karyawan atau lokasi.</p>
  </div>
</div>
<div class="row">
  <div class="col-md-5">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Tambah Weekly Off</h3>
        <form method="GET" action="<?php echo e(route('weekly-offs.index')); ?>" class="d-flex gap-2">
          <select name="day_of_week" class="form-select form-select-sm" style="width:auto">
            <option value="">Hari: Semua</option>
            <?php $__currentLoopData = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($d); ?>" <?php if(request('day_of_week')===$d): echo 'selected'; endif; ?>><?php echo e(ucfirst($d)); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <button class="btn btn-sm btn-outline-primary" type="submit">Filter</button>
        </form>
      </div>
      <div class="card-body">
        <form method="POST" action="<?php echo e(route('weekly-offs.store')); ?>" class="row g-2">
          <?php echo csrf_field(); ?>
          <div class="col-md-6">
            <label class="form-label">Hari</label>
            <select class="form-select" name="day_of_week" required>
              <?php $__currentLoopData = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($d); ?>"><?php echo e(ucfirst($d)); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Untuk User (opsional)</label>
            <?php
              $usersQuery = \App\Models\User::orderBy('name');
              if (auth()->user()->hasRole('Admin Lokasi')) {
                $usersQuery->where('location_id', auth()->user()->location_id);
              }
              $allUsers = $usersQuery->get();
            ?>
            <select class="form-select" name="user_id" id="weekly_off_user_id">
              <option value="">- semua user (lokasi) -</option>
              <?php $__currentLoopData = $allUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($u->id); ?>" data-user-location="<?php echo e($u->location_id); ?>"><?php echo e($u->name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <?php if(auth()->user()->hasRole('Super Admin')): ?>
          <div class="col-12">
            <label class="form-label">Lokasi (untuk off level lokasi)</label>
            <select class="form-select" name="location_id" id="weekly_off_location_id">
              <option value="">- none -</option>
              <?php $__currentLoopData = \App\Models\Location::orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($loc->id); ?>"><?php echo e($loc->name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <?php endif; ?>
          <div class="col-12">
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-7">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Daftar Weekly Off</h3></div>
      <div class="card-body table-responsive">
        <table class="table table-sm table-bordered">
          <thead>
            <tr>
              <th style="width:50px">No</th><th>Hari</th><th>User</th><th>Lokasi</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $offs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($loop->iteration + (method_exists($offs,'currentPage') ? ($offs->currentPage()-1)*$offs->perPage() : 0)); ?></td>
              <td><?php echo e(ucfirst($o->day_of_week)); ?></td>
              <td><?php echo e($o->user_id ? optional(\App\Models\User::find($o->user_id))->name : '-'); ?></td>
              <td><?php echo e($o->location_id ? optional(\App\Models\Location::find($o->location_id))->name : (auth()->user()->hasRole('Admin Lokasi') ? 'Lokasi Saya' : '-')); ?></td>
              <td>
                <form method="POST" action="<?php echo e(route('weekly-offs.destroy', $o)); ?>" onsubmit="return confirm('Hapus weekly off ini?')">
                  <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                  <button class="btn btn-sm btn-danger">Hapus</button>
                </form>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="4" class="text-center">Belum ada data</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
        <?php if(method_exists($offs, 'links')): ?>
          <div class="mt-2"><?php echo e($offs->links()); ?></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
  document.addEventListener('DOMContentLoaded', function(){
    const locSelect = document.getElementById('weekly_off_location_id');
    const userSelect = document.getElementById('weekly_off_user_id');
    if (!locSelect || !userSelect) return;
    const originalOptions = Array.from(userSelect.options);
    function filterUsers(){
      const locId = locSelect.value;
      // Preserve first option (placeholder)
      const placeholder = originalOptions[0].cloneNode(true);
      userSelect.innerHTML = '';
      userSelect.appendChild(placeholder);
      originalOptions.slice(1).forEach(opt => {
        const userLoc = opt.getAttribute('data-user-location');
        if (!locId || userLoc === locId) {
          userSelect.appendChild(opt.cloneNode(true));
        }
      });
      userSelect.value = '';
    }
    locSelect.addEventListener('change', filterUsers);
    // Initialize on load
    filterUsers();
  });
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/weekly-offs/index.blade.php ENDPATH**/ ?>