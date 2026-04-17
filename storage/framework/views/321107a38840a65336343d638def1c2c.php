<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?php echo e(__('Messenger')); ?>

          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;"><?php echo e(__('Communication hub for team collaboration.')); ?></p>
        </div>
      </div>

      <?php
        $activeUserId = request('user_id') ?? ($contacts->keys()->first() ?? null);
        $activeMessages = ($activeUserId && $conversations->has($activeUserId)) ? $conversations[$activeUserId] : collect();
        $activeContact = $activeUserId && $contacts->has($activeUserId) ? $contacts[$activeUserId] : null;
        $activeDisplayName = $activeContact['user']->name ?? null;
      ?>

      <div class="row g-4 h-100 mb-5" style="min-height: 600px;">
        <!-- Chat Contacts -->
        <div class="col-xl-4 col-lg-5">
            <div class="card h-100 border-0 shadow-sm overflow-hidden rounded-4">
                <div class="p-4 border-bottom border-light">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="mdi mdi-forum-outline me-2 text-primary"></i>Chats</h5>
                        <span class="badge bg-light text-muted border px-2 py-1"><?php echo e($contacts->count()); ?> active</span>
                    </div>
                    <div class="position-relative">
                        <i class="mdi mdi-magnify position-absolute text-muted" style="top: 10px; left: 15px;"></i>
                        <input type="text" class="form-control rounded-pill border-light bg-light shadow-none ps-5" id="conversation-search" placeholder="Search team members...">
                    </div>
                </div>
                <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;" id="conversation-list">
                    <?php $__empty_1 = true; $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contactUserId => $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                          $displayName = $contact['user']->name;
                          $last = $contact['last_message'];
                          $lastText = $last?->message ?? 'No messages yet';
                          $lastTime = $last?->created_at?->diffForHumans() ?? '';
                        ?>
                        <a href="<?php echo e(route('messages.index', ['user_id' => $contactUserId])); ?>" class="chat-tile-new text-decoration-none <?php echo e($contactUserId == $activeUserId ? 'active' : ''); ?>" data-name="<?php echo e(strtolower($displayName)); ?>">
                            <div class="avatar-sm me-3">
                                <span class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" style="width: 44px; height: 44px; background: linear-gradient(135deg, #0ea5e9, #38bdf8);">
                                    <?php echo e(strtoupper(substr($displayName, 0, 1))); ?>

                                </span>
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-0 fw-bold text-dark text-truncate"><?php echo e($displayName); ?></h6>
                                    <small class="text-muted smallest"><?php echo e($lastTime); ?></small>
                                </div>
                                <div class="text-muted smallest text-truncate opacity-75 mt-1"><?php echo e($lastText); ?></div>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-5 text-center text-muted">
                            <i class="mdi mdi-message-off-outline fs-1 opacity-25 d-block mb-3"></i>
                            <p class="mb-0 small fw-bold">No active conversations.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Chat History -->
        <div class="col-xl-8 col-lg-7">
            <div class="card h-100 border-0 shadow-sm rounded-4 d-flex flex-column">
                <div class="card-header border-bottom border-light p-4 bg-transparent d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <?php if($activeDisplayName): ?>
                            <div class="bg-success rounded-circle me-3" style="width: 10px; height: 10px;"></div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark"><?php echo e($activeDisplayName); ?></h6>
                                <small class="text-success fw-bold smallest">ONLINE</small>
                            </div>
                        <?php else: ?>
                            <h6 class="mb-0 fw-bold text-muted">Select a conversation</h6>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card-body p-4 d-flex flex-column flex-grow-1" style="background-color: #f8fafc; height: 500px; overflow-y: auto;" id="chat-thread">
                    <?php if($activeMessages->isEmpty()): ?>
                        <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center opacity-50">
                            <i class="mdi mdi-forum-outline text-muted" style="font-size: 5rem;"></i>
                            <p class="mt-4 fw-bold text-muted">Pick a teammate to start chatting</p>
                        </div>
                    <?php else: ?>
                        <?php $__currentLoopData = $activeMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $isMine = $msg->sender_id == auth()->id(); ?>
                            <div class="d-flex <?php echo e($isMine ? 'justify-content-end' : 'justify-content-start'); ?> mb-3">
                                <div class="message-box <?php echo e($isMine ? 'mine' : 'theirs'); ?>">
                                    <?php if(!$isMine): ?>
                                        <div class="smaller fw-bold mb-1 opacity-50"><?php echo e($activeDisplayName); ?></div>
                                    <?php endif; ?>
                                    <div class="message-text"><?php echo e($msg->message); ?></div>
                                    <div class="text-end smallest opacity-50 mt-1"><?php echo e($msg->created_at->format('H:i')); ?></div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>

                <div class="p-4 border-top border-light bg-transparent">
                    <form action="<?php echo e(route('messages.store')); ?>" method="POST" class="input-group bg-light rounded-pill p-1 border">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="receiver_id" value="<?php echo e($activeUserId); ?>">
                        <input type="text" name="message" class="form-control border-0 bg-transparent shadow-none px-4" placeholder="Type your message..." required autocomplete="off">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold"><i class="mdi mdi-send"></i></button>
                    </form>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
    .chat-tile-new {
        display: flex;
        padding: 20px;
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s;
        align-items: center;
    }
    .chat-tile-new:hover { background-color: #f8fafc; }
    .chat-tile-new.active { background-color: var(--soft-celeste); border-right: 4px solid var(--primary); }
    
    .message-box {
        max-width: 70%;
        padding: 14px 18px;
        border-radius: 20px;
        font-size: 0.9rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.02);
    }
    .message-box.mine {
        background-color: var(--primary);
        color: white;
        border-bottom-right-radius: 4px;
    }
    .message-box.theirs {
        background-color: white;
        color: var(--text-main);
        border-bottom-left-radius: 4px;
        border: 1px solid #f1f5f9;
    }
    .smallest { font-size: 0.7rem; }
</style>

<?php $__env->startPush('scripts'); ?>
<script>
    const thread = document.getElementById('chat-thread');
    if (thread) thread.scrollTop = thread.scrollHeight;
    
    const search = document.getElementById('conversation-search');
    if (search) {
        search.addEventListener('input', function(e) {
            const val = e.target.value.toLowerCase();
            document.querySelectorAll('.chat-tile-new').forEach(tile => {
                const name = tile.getAttribute('data-name');
                tile.style.display = name.includes(val) ? 'flex' : 'none';
            });
        });
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/messages/index.blade.php ENDPATH**/ ?>