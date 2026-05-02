<div class="card mb-4 shadow-sm border border-white border-opacity-5 rounded-4 transition-all shift-card">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="d-flex align-items-start gap-3">
                <div class="form-check custom-check pt-1">
                    <input class="form-check-input bg-dark bg-opacity-50 border-light" 
                           type="checkbox" 
                           id="shift_<?php echo e($shift->id); ?>" 
                           name="shift_ids[]" 
                           value="<?php echo e($shift->id); ?>" 
                           <?php echo e($checked ? 'checked' : ''); ?>>
                </div>
                <div>
                    <label class="form-check-label text-dark fw-bold mb-1" for="shift_<?php echo e($shift->id); ?>">
                        <?php echo e($shift->name); ?> <span class="text-muted fw-normal">(<?php echo e($shift->code); ?>)</span>
                    </label>
                    <div class="text-muted smaller d-flex align-items-center">
                        <i class="mdi mdi-clock-outline me-1"></i>
                        <?php echo e(__('Default: ')); ?> <?php echo e($shift->getFormattedSchedule()); ?> 
                        <?php if($shift->day): ?><span class="mx-1">•</span> <?php echo e($shift->getDayName()); ?><?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-3 align-items-center flex-wrap">
                <select name="shift_category[<?php echo e($shift->id); ?>]" class="form-select form-select-sm bg-dark bg-opacity-50 border-light text-white rounded-pill px-3 shadow-none smaller" style="width: auto;">
                    <option value="office" <?php if($currentCategory === 'office'): echo 'selected'; endif; ?>>Office</option>
                    <option value="non_office" <?php if($currentCategory === 'non_office'): echo 'selected'; endif; ?>>Non Office</option>
                </select>
                <?php if($shift->category === 'office'): ?>
                <div class="form-check custom-radio d-flex align-items-center bg-white bg-opacity-5 rounded-pill px-3 py-1 border border-light">
                    <input class="form-check-input bg-dark bg-opacity-50 border-light me-2" 
                           type="radio" 
                           name="default_shift_id" 
                           id="default_<?php echo e($shift->id); ?>" 
                           value="<?php echo e($shift->id); ?>" 
                           <?php if(old('default_shift_id') == $shift->id): echo 'checked'; endif; ?>>
                    <label class="form-check-label text-dark smaller fw-bold mb-0" for="default_<?php echo e($shift->id); ?>"><?php echo e(__('Default Office')); ?></label>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mt-4 pt-4 border-top border-white border-opacity-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small fw-bold text-uppercase letter-spacing-1">
                    <i class="mdi mdi-layers-outline me-1"></i><?php echo e(__('Time Slots (per location)')); ?>

                </span>
                <button type="button" class="btn btn-outline-info btn-sm rounded-pill px-3 fw-bold smaller add-slot" data-shift="<?php echo e($shift->id); ?>">
                    <i class="mdi mdi-plus me-1"></i><?php echo e(__('Add Slot')); ?>

                </button>
            </div>
            
            <div class="slot-container" data-shift="<?php echo e($shift->id); ?>" data-next-index="<?php echo e($nextIndex); ?>">
                <?php $__currentLoopData = $slots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="row g-2 align-items-end slot-row mb-3 p-3 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-5 mx-0">
                        <div class="col-md-4">
                            <label class="form-label text-muted smaller fw-bold text-uppercase mb-2"><?php echo e(__('Day')); ?></label>
                            <?php $selectedDays = $slot['days'] ?? (isset($slot['day']) ? [$slot['day']] : []); ?>
                            <select name="shift_times[<?php echo e($shift->id); ?>][<?php echo e($idx); ?>][days][]" class="form-select multi-day-select" multiple>
                                <option value=""><?php echo e(__('Pick days (empty = all)')); ?></option>
                                <?php $__currentLoopData = ['monday'=>__('Monday'),'tuesday'=>__('Tuesday'),'wednesday'=>__('Wednesday'),'thursday'=>__('Thursday'),'friday'=>__('Friday'),'saturday'=>__('Saturday'),'sunday'=>__('Sunday')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dKey=>$dLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($dKey); ?>" <?php if(in_array($dKey, $selectedDays ?? [])): echo 'selected'; endif; ?>><?php echo e($dLabel); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted smaller fw-bold text-uppercase mb-2"><?php echo e(__('Start')); ?></label>
                            <input type="time" name="shift_times[<?php echo e($shift->id); ?>][<?php echo e($idx); ?>][start]" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-pill px-3 shadow-none smaller" value="<?php echo e($slot['start'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted smaller fw-bold text-uppercase mb-2"><?php echo e(__('End')); ?></label>
                            <input type="time" name="shift_times[<?php echo e($shift->id); ?>][<?php echo e($idx); ?>][end]" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-pill px-3 shadow-none smaller" value="<?php echo e($slot['end'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-danger w-100 rounded-pill px-3 smaller fw-bold remove-slot mb-1">
                                <i class="mdi mdi-trash-can-outline me-1"></i><?php echo e(__('Delete')); ?>

                            </button>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>
<?php /**PATH D:\www\kantorapp\resources\views\location-shifts\_shift_card.blade.php ENDPATH**/ ?>