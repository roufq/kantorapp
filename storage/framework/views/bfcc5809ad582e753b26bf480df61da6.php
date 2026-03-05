

<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Detail Karyawan</h3>
        <p class="text-muted mb-0"><?php echo e($karyawan->nama); ?></p>
    </div>
    <a href="<?php echo e(route('karyawans.index')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Employee Details</h3>
                <div class="card-tools">
                    <a href="<?php echo e(route('karyawans.edit', $karyawan)); ?>" class="btn btn-sm btn-primary">Edit</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID:</strong> <?php echo e($karyawan->id); ?></p>
                        <p><strong>Nama:</strong> <?php echo e($karyawan->nama); ?></p>
                        <p><strong>Email:</strong> <?php echo e($karyawan->email); ?></p>
                        <p><strong>Telepon:</strong> <?php echo e($karyawan->telepon); ?></p>
                        <p><strong>Jabatan:</strong> <?php echo e($karyawan->jabatan); ?></p>
                        <p><strong>Departemen:</strong> <?php echo e($karyawan->departemen); ?></p>
                        <p><strong>Tanggal Lahir:</strong> <?php echo e($karyawan->tanggal_lahir ? $karyawan->tanggal_lahir->format('d M Y') : '-'); ?></p>
                        <p><strong>Tanggal Masuk Kerja:</strong> <?php echo e($karyawan->tanggal_masuk_kerja ? $karyawan->tanggal_masuk_kerja->format('d M Y') : '-'); ?></p>
                        <p><strong>Divisi:</strong> <?php echo e($karyawan->division->nama ?? 'N/A'); ?></p>
                        <p><strong>Lokasi:</strong> <?php echo e($karyawan->location->name ?? 'N/A'); ?></p>

                        <p><strong>Super Admin:</strong> <?php echo e($karyawan->master ? $karyawan->master->name : 'N/A'); ?></p>
                        <p><strong>Created:</strong> <?php echo e($karyawan->created_at->format('d M Y H:i')); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Alamat:</strong></p>
                        <p><?php echo e($karyawan->alamat ?: '-'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\karyawans\show.blade.php ENDPATH**/ ?>