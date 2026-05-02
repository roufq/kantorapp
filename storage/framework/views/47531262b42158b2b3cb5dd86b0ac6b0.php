

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;"><?php echo e((auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Location Admin')) ? __('Assign Task') : __('Create Task')); ?></h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;">
            <?php echo e((auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Location Admin')) ? __('Super Admin or Location Admin can assign tasks to employees/location admins, or themselves.') : __('Create task for yourself and monitor progress.')); ?>

          </p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <a href="<?php echo e(route('tasks.index')); ?>" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-arrow-left me-2 fs-5 align-middle"></i><?php echo e(__('Back')); ?>

          </a>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 24px !important;">
                <div class="card-body p-4">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger rounded-3 p-3 mb-4 shadow-sm border-0 bg-danger bg-opacity-25 text-white">
                            <div class="fw-bold mb-2"><i class="mdi mdi-alert-circle-outline me-1"></i><?php echo e(__('Validation failed:')); ?></div>
                            <ul class="mb-0 small">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('tasks.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="row g-4">
                            <div class="col-lg-7">
                                <div class="card shadow-sm border-0 p-4 rounded-4 border border-light h-100">
                                    <h5 class="text-dark fw-bold mb-4 d-flex align-items-center">
                                        <i class="mdi mdi-information-outline me-2 text-info"></i><?php echo e(__('Task Information')); ?>

                                    </h5>
                                    <div class="mb-3">
                                        <label for="title" class="form-label text-muted small fw-bold"><?php echo e(__('Title')); ?></label>
                                        <input type="text" name="title" class="form-control rounded-pill px-4 shadow-sm" id="title" value="<?php echo e(old('title')); ?>" required placeholder="Enter task title">
                                    </div>
                                    <div class="mb-0">
                                        <label for="description" class="form-label text-muted small fw-bold"><?php echo e(__('Description')); ?></label>
                                        <textarea name="description" class="form-control" id="description" rows="12" required placeholder="Describe the task details..." style="border-radius: 15px !important;"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="card shadow-sm border-0 p-4 rounded-4 border border-light h-100">
                                    <h5 class="text-dark fw-bold mb-4 d-flex align-items-center">
                                        <i class="mdi mdi-account-cog-outline me-2 text-warning"></i><?php echo e(__('Assignment & Schedule')); ?>

                                    </h5>
                                    
                                    <?php if(auth()->user()->hasRole('Super Admin')): ?>
                                    <div class="mb-3">
                                        <label for="location_filter" class="form-label text-muted small fw-bold"><?php echo e(__('Location')); ?></label>
                                        <select id="location_filter" class="form-select rounded-pill px-4 shadow-sm">
                                            <option value=""><?php echo e(__('-- All locations --')); ?></option>
                                            <?php $__currentLoopData = $locations ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $shiftNames = $location->shifts->pluck('name')->map(fn ($n) => strtolower($n));
                                                    $shiftLabel = $shiftNames->contains('factory multiple shifts')
                                                        ? __('Factory')
                                                        : ($shiftNames->contains('office standard shift') ? __('Office') : __('Unknown Shift'));
                                                ?>
                                                <option value="<?php echo e($location->id); ?>">
                                                    <?php echo e($location->name); ?> (<?php echo e($shiftLabel); ?>)
                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <small class="text-muted italic smaller mt-1 d-block"><?php echo e(__('Select location to filter users and task catalog.')); ?></small>
                                    </div>
                                    <?php endif; ?>

                                    <div class="mb-3">
                                        <label for="task_catalog_id" class="form-label text-muted small fw-bold"><?php echo e(__('Task Catalog')); ?></label>
                                        <select name="task_catalog_id" id="task_catalog_id" class="form-select rounded-pill px-4 shadow-sm" required>
                                            <option value=""><?php echo e(__('-- Select Task Catalog --')); ?></option>
                                            <?php $__currentLoopData = $catalogs ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catalog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($catalog->id); ?>" data-jobdesk-id="<?php echo e($catalog->jobdesk_id); ?>" data-location-id="<?php echo e($catalog->jobdesk->location_id ?? ''); ?>" data-unit="<?php echo e($catalog->unit); ?>" data-value="<?php echo e($catalog->value); ?>" <?php if(old('task_catalog_id') == $catalog->id): echo 'selected'; endif; ?>>
                                                    <?php echo e($catalog->name); ?> (<?php echo e($catalog->unit); ?> <?php echo e($catalog->value); ?>)
                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <small class="text-muted italic smaller mt-1 d-block"><?php echo e(__('Catalog will be filtered by the selected employee\'s jobdesk.')); ?></small>
                                    </div>

                                    <div class="mb-3" id="durationField">
                                        <label for="duration_minutes" class="form-label text-muted small fw-bold"><?php echo e(__('Duration (minutes):')); ?></label>
                                        <input type="number" name="duration_minutes" class="form-control rounded-pill px-4 shadow-sm" id="duration_minutes" min="1" placeholder="<?php echo e(__('e.g. 240 for 4 hours')); ?>" value="<?php echo e(old('duration_minutes')); ?>">
                                        <small class="text-muted italic smaller mt-1 d-block" id="durationHelp"><?php echo e(__('Total minutes to be split into progress slots. Due date still applies as final target.')); ?></small>
                                    </div>

                                    <?php if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Location Admin')): ?>
                                    <div class="mb-3">
                                        <label for="assigned_to" class="form-label text-muted small fw-bold"><?php echo e(__('Assign To')); ?></label>
                                        <div class="assigned-to-dropdown position-relative">
                                            <input type="hidden" name="assigned_to" id="assigned_to" value="<?php echo e(old('assigned_to')); ?>" required>
                                            <button type="button" class="form-select text-start assigned-to-toggle rounded-pill px-4 shadow-sm" data-placeholder="<?php echo e(__('-- Select user --')); ?>"><?php echo e(__('-- Select user --')); ?></button>
                                            <div class="assigned-to-panel card shadow-sm border-0 shadow-lg p-2 d-none border border-light" style="position:absolute; z-index:1000; width:100%; left:0; top:105%; border-radius: 15px;">
                                                <input type="text" class="form-control form-control-sm mb-2 rounded-pill px-3 bg-dark bg-opacity-50 border-light text-white shadow-none assigned-to-filter" placeholder="Search name/email...">
                                                <div class="list-group assigned-to-list" style="max-height:220px; overflow:auto; scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.1) transparent;">
                                                    <button type="button" class="list-group-item list-group-item-action bg-white text-dark border-0 py-2 smaller" data-user-id="" data-user-label="<?php echo e(__('-- Select user --')); ?>"><?php echo e(__('-- Select user --')); ?></button>
                                                    <button type="button" class="list-group-item list-group-item-action bg-transparent text-info border-0 py-2 smaller italic fw-bold" data-user-id="<?php echo e(auth()->id()); ?>" data-location-id="<?php echo e(auth()->user()->location_id ?? ''); ?>" data-user-label="<?php echo e(__('-- Assign to Myself')); ?> (<?php echo e(auth()->user()->name); ?>)">
                                                        <i class="mdi mdi-account-circle-outline me-1"></i> <?php echo e(__('-- Assign to Myself')); ?> (<?php echo e(auth()->user()->name); ?>)
                                                    </button>
                                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $jobdeskIds = $userJobdeskMap[$user->id] ?? [];
                                                        ?>
                                                        <button type="button" class="list-group-item list-group-item-action bg-white text-dark border-0 py-2 smaller"
                                                            data-user-id="<?php echo e($user->id); ?>"
                                                            data-location-id="<?php echo e($user->location_id ?? ''); ?>"
                                                            data-user-label="<?php echo e($user->name); ?> @ <?php echo e($user->email); ?>"
                                                            data-jobdesks="<?php echo e(implode(',', $jobdeskIds)); ?>">
                                                            <?php echo e($user->name); ?> <span class="text-muted smaller">(<?php echo e($user->email); ?>)</span>
                                                        </button>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted italic smaller mt-1 d-block"><?php echo e(__('Click to open dropdown, then type to search name/email.')); ?></small>
                                    </div>
                                    <?php endif; ?>

                                    <div class="mb-0">
                                        <label for="due_date" class="form-label text-muted small fw-bold"><?php echo e(__('Due Date')); ?></label>
                                        <input type="date" name="due_date" class="form-control rounded-pill px-4 shadow-sm" id="due_date" value="<?php echo e(old('due_date')); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 p-4 card shadow-sm border-0 rounded-4 border border-info border-opacity-10">
                            <h5 class="text-dark fw-bold mb-2 d-flex align-items-center">
                                <i class="mdi mdi-layers-triple-outline me-2 text-info"></i><?php echo e(__('Progress Slots (required, total % = 100%)')); ?>

                            </h5>
                            <p class="text-muted smaller mb-4"><?php echo e(__('Once created, 0-100% progress is updated via task detail page using link evidence.')); ?></p>
                            
                            <?php
                                $oldSlots = old('slots', [['name' => '', 'percentage' => '', 'minutes' => '', 'order' => 0]]);
                            ?>
                            
                            <div id="slotList" data-initial-count="<?php echo e(count($oldSlots)); ?>" class="mb-3">
                                <?php $__currentLoopData = $oldSlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="row g-3 mb-3 slot-row align-items-center">
                                        <div class="col-md-5">
                                            <?php if($idx === 0): ?>
                                                <label class="form-label text-muted smaller fw-bold"><?php echo e(__('Name / Target')); ?></label>
                                            <?php endif; ?>
                                            <input type="text" name="slots[<?php echo e($idx); ?>][name]" class="form-control rounded-pill px-3 shadow-sm" placeholder="<?php echo e(__('e.g. UI Design')); ?>" required value="<?php echo e($slot['name']); ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <?php if($idx === 0): ?>
                                                <label class="form-label text-muted smaller fw-bold"><?php echo e(__('Percentage (%)')); ?></label>
                                            <?php endif; ?>
                                            <input type="number" name="slots[<?php echo e($idx); ?>][percentage]" class="form-control rounded-pill px-3 shadow-sm" min="0.01" max="100" step="0.01" placeholder="25" required value="<?php echo e($slot['percentage']); ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <?php if($idx === 0): ?>
                                                <label class="form-label text-muted smaller fw-bold"><?php echo e(__('Minutes')); ?></label>
                                            <?php endif; ?>
                                            <input type="number" name="slots[<?php echo e($idx); ?>][minutes]" class="form-control rounded-pill px-3 shadow-sm" min="1" placeholder="60" required value="<?php echo e($slot['minutes']); ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <?php if($idx === 0): ?>
                                                <label class="form-label text-muted smaller fw-bold"><?php echo e(__('Order')); ?></label>
                                            <?php endif; ?>
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="number" name="slots[<?php echo e($idx); ?>][order]" class="form-control rounded-pill px-3 shadow-sm" min="0" value="<?php echo e($slot['order'] ?? $idx); ?>">
                                                <button type="button" class="btn btn-outline-danger rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 remove-slot shadow-sm p-0" style="width: 32px; height: 32px;">
                                                    <i class="mdi mdi-close"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            
                            <div id="slotSummary" class="small fw-bold mb-3 p-2 px-3 rounded-pill bg-white bg-opacity-5 border border-white border-opacity-5 d-inline-block"></div>
                            
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-0">
                                <button type="button" class="btn btn-outline-info rounded-pill px-4 fw-bold shadow-sm" id="addSlotBtn">
                                    <i class="mdi mdi-plus-circle-outline me-1"></i><?php echo e(__('Add Slot')); ?>

                                </button>
                                <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-bold shadow-sm" id="clearSlotsBtn">
                                    <i class="mdi mdi-trash-can-outline me-1"></i><?php echo e(__('Delete All Slots')); ?>

                                </button>
                                <span class="smaller text-muted italic ms-lg-3">
                                    <i class="mdi mdi-information-outline me-1"></i><?php echo e(__('Min. 1 slot. Total percentage must be 100%, total minutes must match duration (if filled).')); ?>

                                </span>
                            </div>
                        </div>

                        <div class="text-end mt-5 pt-4 border-top border-white border-opacity-5">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-lg fs-5">
                                <i class="mdi mdi-check-all me-2"></i><?php echo e((auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Location Admin')) ? __('Assign Task') : __('Create Task')); ?>

                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.italic { font-style: italic; }
.smaller { font-size: 0.8rem; }
.letter-spacing-1 { letter-spacing: 1px; }
.list-group-item-action:hover { background: #f1f5f9 !important; color: #06b6d4 !important; }
.assigned-to-list::-webkit-scrollbar { width: 5px; }
.assigned-to-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
</style>

<?php $__env->startPush('scripts'); ?>
<script>
  window.userJobdeskMap = <?php echo json_encode($userJobdeskMap ?? []); ?>;
  // Translations for JS
  window.i18n = {
      nameTarget: "<?php echo e(__('Name / Target')); ?>",
      percentage: "<?php echo e(__('Percentage (%)')); ?>",
      minutes: "<?php echo e(__('Minutes')); ?>",
      order: "<?php echo e(__('Order')); ?>",
      uiDesignPlaceholder: "<?php echo e(__('e.g. UI Design')); ?>",
  };
</script>
<script src="<?php echo e(asset('js/tasks-create.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\tasks\create.blade.php ENDPATH**/ ?>