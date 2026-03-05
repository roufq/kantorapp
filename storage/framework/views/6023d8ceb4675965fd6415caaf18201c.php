<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Attendance Report</h3>
        <p class="text-muted mb-0">Laporan kehadiran berdasarkan filter.</p>
    </div>
    <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><?php echo e(__('Attendance Report')); ?></div>

                <div class="card-body">
                    <?php if(auth()->guard()->check()): ?>
                        <?php
                            $effectiveName = isset($effectiveLocation) && $effectiveLocation ? ($effectiveLocation->name ?? null) : (auth()->user()->location->name ?? null);
                        ?>
                        <?php if(auth()->user()->hasRole('Admin Lokasi')): ?>
                            <div class="alert alert-info">
                                Anda melihat laporan sebagai <strong>Admin Lokasi</strong><?php echo e($effectiveName ? ' - ' . e($effectiveName) : ''); ?>.
                                <?php if(!empty($effectiveTemporary)): ?>
                                    <br><small class="text-muted">Catatan: perubahan lokasi sementara untuk hari ini.</small>
                                <?php endif; ?>
                            </div>
                        <?php elseif(auth()->user()->hasRole('Karyawan')): ?>
                            <div class="alert alert-secondary">
                                Anda melihat laporan sebagai <strong>Karyawan</strong><?php echo e($effectiveName ? ' di lokasi ' . e($effectiveName) : ''); ?>.
                                <?php if(!empty($effectiveTemporary)): ?>
                                    <br><small class="text-muted">Catatan: perubahan lokasi sementara untuk hari ini.</small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <form method="GET" action="<?php echo e(route('attendance.report')); ?>" class="mb-4">
                        <div class="row g-3">
                            <div class="col-xl-3 col-lg-6">
                                <label for="user_id" class="form-label">User</label>
                                <select name="user_id" id="user_id" class="form-control">
                                    <option value="">All Users</option>
                                    <?php $__currentLoopData = \App\Models\User::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>>
                                            <?php echo e($user->name); ?> (<?php echo e($user->employee->nama ?? 'N/A'); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-xl-3 col-lg-6">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo e(request('start_date')); ?>">
                            </div>
                            <div class="col-xl-3 col-lg-6">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo e(request('end_date')); ?>">
                            </div>
                            <div class="col-xl-3 col-lg-6">
                                <label for="shift_id" class="form-label">Shift</label>
                                <select name="shift_id" id="shift_id" class="form-control">
                                    <option value="">All Shifts</option>
                                    <?php $__currentLoopData = \App\Models\Shift::active()->orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($shift->id); ?>" <?php echo e(request('shift_id') == $shift->id ? 'selected' : ''); ?>>
                                            <?php echo e($shift->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-xl-3 col-lg-6">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary form-control">Filter</button>
                            </div>
                        </div>
                    </form>

                    
                    <div class="mb-3 d-flex flex-wrap gap-2">
                        <a href="<?php echo e(route('attendance.export', array_merge(request()->query(), ['format' => 'xlsx']))); ?>" class="btn btn-success">
                            <i class="fas fa-download"></i> Export Excel
                        </a>
                        <a href="<?php echo e(route('attendance.export', array_merge(request()->query(), ['format' => 'csv']))); ?>" class="btn btn-outline-success">
                            <i class="fas fa-file-csv"></i> Export CSV
                        </a>
                    </div>

                    
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width:50px">No</th>
                                <th>User</th>
                                <th>Employee Name</th>
                                <th>Shift</th>
                                <th>Roster (Slot)</th>
                                <th>Roster Status</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Location</th>
                                <th>Late</th>
                                <th>Approval Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($loop->iteration + ($attendances->currentPage()-1)*$attendances->perPage()); ?></td>
                                    <td><?php echo e($attendance->user->name); ?></td>
                                    <td><?php echo e($attendance->user->employee->nama ?? 'N/A'); ?></td>
                                    <td><?php echo e(optional($attendance->shift)->name ?? '-'); ?></td>
                                    <?php
                                        $rKey = $attendance->user_id . '|' . $attendance->check_in_time->toDateString();
                                        $rCollection = $rosterEntries[$rKey] ?? collect();
                                        // pilih entri yang match shift_assignment jika ada, else pertama
                                        $rEntry = $rCollection->firstWhere('shift_assignment_id', $attendance->shift_assignment_id) ?? $rCollection->first();
                                        $slot = $rEntry?->slot_index;
                                        $slotRange = null;
                                        if ($rEntry && $rEntry->roster && $rEntry->roster->locationShift) {
                                            $slots = $rEntry->roster->locationShift->time_slots ?? [];
                                            if (isset($slots['start'], $slots['end'])) {
                                                $slots = [ $slots ];
                                            }
                                            if (isset($slots[$slot])) {
                                                $slotRange = ($slots[$slot]['start'] ?? '?') . ' - ' . ($slots[$slot]['end'] ?? '?');
                                            }
                                        }
                                    ?>
                                    <td>
                                        <?php if($slotRange): ?>
                                            Slot <?php echo e(($slot ?? 0)+1); ?><br><small class="text-muted"><?php echo e($slotRange); ?></small>
                                        <?php elseif($rEntry): ?>
                                            Slot <?php echo e(($slot ?? 0)+1); ?>

                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($rEntry): ?>
                                            <?php if($rEntry->status === 'off'): ?>
                                                <span class="badge bg-secondary">OFF</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Scheduled</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted">No roster</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($attendance->check_in_time->format('Y-m-d H:i:s')); ?></td>
                                    <td><?php echo e($attendance->check_out_time ? $attendance->check_out_time->format('Y-m-d H:i:s') : 'Not checked out'); ?></td>
                                    <td>
                                        <?php $locStr = $attendance->location; ?>
                                        <?php if($locStr): ?>
                                            <?php echo e($locStr); ?>

                                            <?php $p = explode(',', $locStr); ?>
                                            <?php if(count($p) === 2): ?>
                                                <?php $plat = trim($p[0]); $plng = trim($p[1]); ?>
                                                <br>
                                                <a href="https://www.google.com/maps?q=<?php echo e($plat); ?>,<?php echo e($plng); ?>" target="_blank" rel="noopener">Lihat di Peta</a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($attendance->is_late ? 'Yes' : 'No'); ?></td>
                                    <td>
                                        <form method="POST" action="<?php echo e(route('attendance.update.approval', $attendance->id)); ?>" style="display: inline;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <select name="approval_status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="pending" <?php echo e($attendance->approval_status == 'pending' ? 'selected' : ''); ?>>Pending</option>
                                                <option value="approved" <?php echo e($attendance->approval_status == 'approved' ? 'selected' : ''); ?>>Approved</option>
                                                <option value="rejected" <?php echo e($attendance->approval_status == 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                                            </select>
                                        </form>
                                    </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>

                    
                    <?php echo e($attendances->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\attendances\report.blade.php ENDPATH**/ ?>