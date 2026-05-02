<table>
    <thead>
    <tr>
        <th>Date</th>
        <th>Slot</th>
        <th>Time</th>
        <th>Employees</th>
        <th>Status</th>
        <th>Notes</th>
    </tr>
    </thead>
    <tbody>
    <?php $__currentLoopData = $roster->entries->sortBy(['date','slot_index']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($e->date->toDateString()); ?></td>
            <td><?php echo e($e->status === 'off' ? 'OFF' : $e->slot_index + 1); ?></td>
            <td>
                <?php if($e->status === 'off'): ?>
                    Day off
                <?php elseif(isset($slotMap[$e->slot_index])): ?>
                    <?php echo e($slotMap[$e->slot_index]['start'] ?? '?'); ?> - <?php echo e($slotMap[$e->slot_index]['end'] ?? '?'); ?>

                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
            <td><?php echo e($e->user->name ?? '-'); ?></td>
            <td><?php echo e($e->status); ?></td>
            <td><?php echo e($e->notes); ?></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php /**PATH D:\www\kantorapp\resources\views\exports\weekly_roster.blade.php ENDPATH**/ ?>