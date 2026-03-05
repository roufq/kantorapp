

<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Tambah Karyawan</h3>
        <p class="text-muted mb-0">Isi data karyawan baru untuk keperluan HR dan penugasan.</p>
    </div>
    <a href="<?php echo e(route('karyawans.index')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Add Employee</h3>
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
                <form action="<?php echo e(route('karyawans.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" id="nama" value="<?php echo e(old('nama')); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="email" value="<?php echo e(old('email')); ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telepon" class="form-label">Telepon</label>
                            <input type="text" name="telepon" class="form-control" id="telepon" value="<?php echo e(old('telepon')); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control" id="jabatan" value="<?php echo e(old('jabatan')); ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="departemen" class="form-label">Departemen</label>
                            <input type="text" name="departemen" class="form-control" id="departemen" value="<?php echo e(old('departemen')); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" id="tanggal_lahir" value="<?php echo e(old('tanggal_lahir')); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_masuk_kerja" class="form-label">Tanggal Masuk Kerja</label>
                            <input type="date" name="tanggal_masuk_kerja" class="form-control" id="tanggal_masuk_kerja" value="<?php echo e(old('tanggal_masuk_kerja')); ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="divisi_id" class="form-label">Divisi</label>
                        <select name="divisi_id" class="form-control" id="divisi_id" required>
                            <option value="">Pilih Divisi</option>
                            <?php $__currentLoopData = $divisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $division): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($division->id); ?>" <?php echo e(old('divisi_id') == $division->id ? 'selected' : ''); ?>><?php echo e($division->nama); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="location_id" class="form-label">Lokasi</label>
                            <?php if(auth()->user()->hasRole('Admin Lokasi')): ?>
                                <input type="text" class="form-control" value="<?php echo e(optional(auth()->user()->location)->name); ?>" disabled>
                                <input type="hidden" name="location_id" value="<?php echo e(auth()->user()->location_id); ?>">
                            <?php else: ?>
                                <select name="location_id" class="form-control" id="location_id" required>
                                    <option value="">Pilih Lokasi</option>
                                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($location->id); ?>" <?php echo e(old('location_id') == $location->id ? 'selected' : ''); ?>><?php echo e($location->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            <?php endif; ?>
                        </div>

                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" id="alamat" rows="3"><?php echo e(old('alamat')); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Employee</button>
                    <a href="<?php echo e(route('karyawans.index')); ?>" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\karyawans\create.blade.php ENDPATH**/ ?>