<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h2 { margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background: #f5f5f5; }
        .summary-table td { border: none; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2>Rekap Jam Kerja Bulanan</h2>
    <div>Periode: <?php echo e($startDate); ?> s.d <?php echo e($endDate); ?></div>
    <div>Karyawan: <?php echo e($employeeName ?? $employeeId); ?></div>

    <table class="summary-table" style="margin-top: 8px;">
        <tr>
            <td><strong>Target (menit)</strong>: <?php echo e($slotSummary['target_minutes'] ?? '-'); ?></td>
            <td><strong>Slot Approved (menit)</strong>: <?php echo e($slotSummary['slot_minutes']); ?></td>
            <td><strong>Kehadiran (menit)</strong>: <?php echo e($slotSummary['attendance_minutes']); ?></td>
            <td><strong>Sisa (menit)</strong>: <?php echo e($slotSummary['remaining'] ?? '-'); ?></td>
        </tr>
    </table>

    <h3 style="margin-top: 16px;">Detail Slot Approved</h3>
    <table>
        <thead>
            <tr>
                <th>Task</th>
                <th>Slot</th>
                <th>Menit</th>
                <th>Approved</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $slotDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($slot->task->title ?? 'Task #'.$slot->task_id); ?></td>
                    <td><?php echo e($slot->name); ?></td>
                    <td class="text-right"><?php echo e($slot->minutes); ?></td>
                    <td><?php echo e(optional($slot->approved_at)->format('d M Y H:i')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="text-center">Belum ada slot approved pada rentang ini.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h3 style="margin-top: 16px;">Detail Kehadiran</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Durasi (menit)</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $attendanceDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e(optional($att->check_in_time)->format('d M Y')); ?></td>
                    <td><?php echo e(optional($att->check_in_time)->format('H:i')); ?></td>
                    <td><?php echo e(optional($att->check_out_time)->format('H:i')); ?></td>
                    <td class="text-right"><?php echo e($att->duration_minutes ?? 0); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="text-center">Belum ada data kehadiran pada rentang ini.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html><?php /**PATH D:\www\kantorapp\resources\views/work-recaps/pdf.blade.php ENDPATH**/ ?>