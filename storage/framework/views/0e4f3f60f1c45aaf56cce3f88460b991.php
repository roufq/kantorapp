<?php use Carbon\Carbon; ?>


<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border">
    <div>
        <h3 class="mb-1">Rekap Jam Kerja Bulanan</h3>
        <p class="text-muted mb-0">Total menit dari slot tugas yang disetujui + kehadiran (jika ada).</p>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <?php if(auth()->user()->hasRole('Super Admin')): ?>
                <div class="col-md-3">
                    <label class="form-label">Lokasi</label>
                    <select name="location_id" class="form-select">
                        <option value="">Pilih lokasi</option>
                        <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($loc->id); ?>" <?php if($locationFilter == $loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name ?? $loc->nama ?? 'Lokasi '.$loc->id); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php endif; ?>
            <div class="col-md-3">
                <label class="form-label">Karyawan</label>
                <select name="employee_id" class="form-select">
                    <option value="">Pilih karyawan</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>" <?php if($employeeId == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->nama ?? $emp->id); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" value="<?php echo e($startDate); ?>" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="end_date" value="<?php echo e($endDate); ?>" class="form-control">
            </div>
            <div class="col-12 d-flex flex-wrap align-items-center gap-2 mt-1">
                <button type="submit" class="btn btn-primary waves-effect waves-light">Terapkan</button>
                <a href="<?php echo e(route('work-recaps.index')); ?>" class="btn btn-outline-secondary waves-effect">Reset</a>
                <?php if($employeeId): ?>
                    <button type="submit" name="export" value="1" class="btn btn-outline-success waves-effect">Export PDF</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php if($employeeId): ?>
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">Target (menit)</div>
                <div class="h4 mb-0"><?php echo e($slotSummary['target_minutes'] ?? '—'); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">Slot Approved (menit)</div>
                <div class="h4 mb-0"><?php echo e($slotSummary['slot_minutes']); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">Kehadiran (menit)</div>
                <div class="h4 mb-0"><?php echo e($slotSummary['attendance_minutes']); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">Sisa (menit)</div>
                <div class="h4 mb-0"><?php echo e($slotSummary['remaining'] ?? '—'); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h5 class="card-title mb-0">Detail Slot Approved</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Task</th>
                        <th>Slot</th>
                        <th>Menit</th>
                        <th>Approved</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $slotDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>
                            <td><?php echo e($slot->task->title ?? 'Task #'.$slot->task_id); ?></td>
                            <td><?php echo e($slot->name); ?></td>
                            <td><?php echo e($slot->minutes); ?></td>
                            <td><?php echo e(optional($slot->approved_at)->format('d M Y H:i')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="text-center text-muted">Belum ada slot approved pada rentang ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Detail Kehadiran</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Tanggal</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Durasi (menit)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $attendanceDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>
                            <td><?php echo e(optional($att->check_in_time)->format('d M Y')); ?></td>
                            <td><?php echo e(optional($att->check_in_time)->format('H:i')); ?></td>
                            <td><?php echo e(optional($att->check_out_time)->format('H:i')); ?></td>
                            <td><?php echo e($att->duration_minutes ?? 0); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="text-center text-muted">Belum ada data kehadiran pada rentang ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php else: ?>
    <div class="alert alert-info">Pilih karyawan untuk melihat ringkasan dan detail.</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/work-recaps/index.blade.php ENDPATH**/ ?>