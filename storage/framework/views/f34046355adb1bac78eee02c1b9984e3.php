<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Divisi</h3>
        <p class="text-muted mb-0">Kelola daftar divisi untuk struktur organisasi.</p>
    </div>
    <a href="<?php echo e(route('divisions.create')); ?>" class="btn btn-primary btn-sm">Tambah Divisi</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Divisions</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th style="width:50px">No</th>
                            <th>Nama</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $divisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $division): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($loop->iteration + ($divisions->currentPage()-1)*$divisions->perPage()); ?></td>
                                <td><?php echo e($division->nama); ?></td>
                                <td>
                                    <a href="<?php echo e(route('divisions.show', $division)); ?>" class="btn btn-sm btn-outline-info">View</a>
                                    <a href="<?php echo e(route('divisions.edit', $division)); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="<?php echo e(route('divisions.destroy', $division)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
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
                <?php echo e($divisions->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\divisions\index.blade.php ENDPATH**/ ?>