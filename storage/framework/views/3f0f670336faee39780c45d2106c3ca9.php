<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Detail Pesan</h3>
        <p class="text-muted mb-0">Chat dengan <?php echo e($user->name); ?></p>
    </div>
    <a href="<?php echo e(route('messages.index')); ?>" class="btn btn-outline-secondary btn-sm">Back</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Chat dengan <?php echo e($user->name); ?></h3>
            </div>
            <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="message mb-3 <?php echo e($message->sender_id == auth()->id() ? 'text-end' : ''); ?>">
                        <div class="d-inline-block p-2 rounded <?php echo e($message->sender_id == auth()->id() ? 'bg-primary text-white' : 'bg-light'); ?>">
                            <p class="mb-1"><?php echo e($message->message); ?></p>
                            <small class="text-muted"><?php echo e($message->created_at->format('d M Y H:i')); ?></small>
                            <?php if(auth()->user()->role === 'master'): ?>
                                <form action="<?php echo e(route('messages.destroy', $message->id)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Delete this message?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger ms-2">Delete</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="card-footer">
                <form action="<?php echo e(route('messages.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="receiver_id" value="<?php echo e($user->id); ?>">
                    <div class="input-group">
                        <textarea name="message" class="form-control" rows="2" placeholder="Type your message..." required></textarea>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\messages\show.blade.php ENDPATH**/ ?>