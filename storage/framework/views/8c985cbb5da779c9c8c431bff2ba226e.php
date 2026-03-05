

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <div>
        <h1 class="m-0">Profil Saya</h1>
        <p class="text-secondary mb-0">Detail akun dan informasi lokasi</p>
      </div>
    </div>
  </div>

      <div class="content">
        <div class="container-fluid">
          <div class="row g-3">
        <div class="col-12">
          <div class="card text-center">
            <div class="card-body">
              <?php
                $avatarPath = $user->profile_photo_path;
                $avatarUrl = asset('assets/img/user2-160x160.jpg');
                if ($avatarPath) {
                  $normalized = str_replace('\\','/',$avatarPath);
                  if (\Illuminate\Support\Facades\Storage::disk('public')->exists($normalized)) {
                    $avatarUrl = asset('storage/' . ltrim($normalized, '/'));
                  }
                }
              ?>
              <div class="mb-3">
                <img src="<?php echo e($avatarUrl); ?>" class="rounded-circle shadow" alt="User avatar" width="120" height="120">
              </div>
              <h5 class="fw-bold mb-1"><?php echo e($user->name); ?></h5>
              <div class="text-secondary mb-2"><?php echo e($roles->implode(', ')); ?></div>
              <div class="small text-secondary">Member since <?php echo e($user->created_at->format('M. Y')); ?></div>
              <div class="mt-3">
                <?php if(session('success')): ?>
                  <div class="alert alert-success py-2"><?php echo e(session('success')); ?></div>
                <?php endif; ?>
                <?php if($errors->any()): ?>
                  <div class="alert alert-danger py-2">
                    <ul class="mb-0">
                      <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                  </div>
                <?php endif; ?>
                <form action="<?php echo e(route('profile.photo')); ?>" method="POST" enctype="multipart/form-data">
                  <?php echo csrf_field(); ?>
                  <div class="mb-2">
                    <input type="file" name="photo" class="form-control" required>
                  </div>
                  <button class="btn btn-primary w-100" type="submit">Update Foto</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">Informasi Akun</h5>
            </div>
            <div class="card-body">
              <dl class="row mb-0">
                <dt class="col-sm-4">Nama</dt>
                <dd class="col-sm-8"><?php echo e($user->name); ?></dd>

                <dt class="col-sm-4">Email</dt>
                <dd class="col-sm-8"><?php echo e($user->email); ?></dd>

                <dt class="col-sm-4">Role</dt>
                <dd class="col-sm-8"><?php echo e($roles->implode(', ')); ?></dd>

                <dt class="col-sm-4">Lokasi</dt>
                <dd class="col-sm-8"><?php echo e($user->location?->name ?? '-'); ?></dd>

                <dt class="col-sm-4">Telepon</dt>
                <dd class="col-sm-8"><?php echo e($user->phone_number ?? '-'); ?></dd>

                <dt class="col-sm-4">Karyawan</dt>
                <dd class="col-sm-8"><?php echo e($user->karyawan?->nama ?? '-'); ?></dd>
              </dl>
            </div>
          </div>

          <div class="card mt-3">
            <div class="card-header">
              <h5 class="mb-0">Ubah Password</h5>
            </div>
            <div class="card-body">
              <form action="<?php echo e(route('profile.password.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                  <label for="current_password" class="form-label">Password Saat Ini</label>
                  <input type="password" name="current_password" id="current_password" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label for="password" class="form-label">Password Baru</label>
                  <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                  <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Password</button>
              </form>
            </div>
          </div>

          <div class="card mt-3">
            <div class="card-header">
              <h5 class="mb-0">Sesi Browser</h5>
            </div>
            <div class="card-body">
              <p class="text-secondary">Kelola dan logout dari sesi aktif Anda di browser dan perangkat lain.</p>
              <?php if(count($sessions) > 0): ?>
                <div class="list-group list-group-flush">
                  <?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                      <div>
                        <div class="fw-bold">
                          <?php echo e($session->ip_address); ?>

                        </div>
                        <div class="text-secondary" style="font-size: 0.9rem; max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                          <?php echo e($session->user_agent); ?>

                        </div>
                        <div class="text-secondary">
                          Last active: <?php echo e(\Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans()); ?>

                          <?php if($session->id === request()->session()->getId()): ?>
                            <span class="badge bg-success ms-2">Sesi ini</span>
                          <?php endif; ?>
                        </div>
                      </div>
                      <?php if($session->id !== request()->session()->getId()): ?>
                        <form action="<?php echo e(route('profile.session.logout', $session->id)); ?>" method="POST">
                          <?php echo csrf_field(); ?>
                          <?php echo method_field('DELETE'); ?>
                          <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
                        </form>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\profile\show.blade.php ENDPATH**/ ?>