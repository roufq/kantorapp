<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border">
    <div>
        <h3 class="mb-1">Karyawan</h3>
        <p class="text-muted mb-0">Kelola data karyawan, jabatan, dan lokasi kerja.</p>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Employees</h3>
                <div class="card-tools">
                    <a href="<?php echo e(route('karyawans.create')); ?>" class="btn btn-sm btn-primary">Add Employee</a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Jabatan</th>
                            <th>Divisi</th>
                            <th>Tgl Masuk</th>
                            <th>Lokasi</th>

                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                       
                            <tr>
                                <td><?php echo e($loop->iteration + ($employees->currentPage()-1)*$employees->perPage()); ?></td>
                                <td><?php echo e($employee->nama); ?></td>
                                <td><?php echo e($employee->email); ?></td>
                                <td><?php echo e($employee->jabatan); ?></td>
                                <td><?php echo e($employee->division->nama ?? 'N/A'); ?></td>
                                <td><?php echo e($employee->tanggal_masuk_kerja ? $employee->tanggal_masuk_kerja->format('d M Y') : '-'); ?></td>
                                <td><?php echo e($employee->location->name ?? 'N/A'); ?></td>

                                <td>
                                    <a href="<?php echo e(route('karyawans.show', $employee)); ?>" class="btn btn-sm btn-outline-info">View</a>
                                    <a href="<?php echo e(route('karyawans.edit', $employee)); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="<?php echo e(route('karyawans.destroy', $employee)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <?php echo e($employees->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/karyawans/index.blade.php ENDPATH**/ ?>