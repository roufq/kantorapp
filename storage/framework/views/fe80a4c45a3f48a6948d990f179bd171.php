<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?php echo e(__('Holiday Schedules')); ?>

          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;"><?php echo e(__('Manage standard weekly rest days for teams and regional sites.')); ?></p>
        </div>
      </div>

      <div class="row g-4">
        <!-- Add Weekly Off Form -->
        <div class="col-lg-5">
          <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="text-dark fw-bold mb-0"><i class="mdi mdi-calendar-plus text-info me-2"></i><?php echo e(__('Define New Off-Day')); ?></h5>
                </div>

                <form method="POST" action="<?php echo e(route('weekly-offs.store')); ?>">
                  <?php echo csrf_field(); ?>
                  <div class="row g-4">
                    <div class="col-12">
                      <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Day Selection')); ?></label>
                      <select class="form-select rounded-pill px-4 border-light shadow-none fw-bold" name="day_of_week" required>
                        <?php $__currentLoopData = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <option value="<?php echo e($d); ?>"><?php echo e(__(ucfirst($d))); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </select>
                    </div>

                    <div class="col-12">
                      <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Target Member (Optional)')); ?></label>
                      <?php
                        $usersQuery = \App\Models\User::orderBy('name');
                        if (auth()->user()->hasRole('Location Admin')) {
                          $usersQuery->where('location_id', auth()->user()->location_id);
                        }
                        $allUsers = $usersQuery->get();
                      ?>
                      <select class="form-select rounded-pill px-4 border-light shadow-none fw-bold" name="user_id" id="weekly_off_user_id">
                        <option value=""><?php echo e(__('— Entire Site —')); ?></option>
                        <?php $__currentLoopData = $allUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <option value="<?php echo e($u->id); ?>" data-user-location="<?php echo e($u->location_id); ?>"><?php echo e($u->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </select>
                    </div>

                    <?php if(auth()->user()->hasRole('Super Admin')): ?>
                    <div class="col-12">
                      <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Operational Site')); ?></label>
                      <select class="form-select rounded-pill px-4 border-light shadow-none fw-bold" name="location_id" id="weekly_off_location_id">
                        <option value=""><?php echo e(__('— None (Global Default) —')); ?></option>
                        <?php $__currentLoopData = \App\Models\Location::orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <option value="<?php echo e($loc->id); ?>"><?php echo e($loc->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </select>
                    </div>
                    <?php endif; ?>

                    <div class="col-12 mt-5">
                      <button type="submit" class="btn btn-dark w-100 rounded-pill py-3 fw-bold shadow-sm">
                        <i class="mdi mdi-content-save-outline me-2"></i><?php echo e(__('Register Schedule')); ?>

                      </button>
                    </div>
                  </div>
                </form>
            </div>
          </div>
        </div>

        <!-- Weekly Off List -->
        <div class="col-lg-7">
          <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-header border-bottom border-light p-4 bg-transparent d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-calendar-range text-info me-2"></i><?php echo e(__('Active Exemptions')); ?></h5>
              <form method="GET" action="<?php echo e(route('weekly-offs.index')); ?>" class="d-flex gap-2">
                    <select name="day_of_week" class="form-select form-select-sm rounded-pill px-3 border-light shadow-none fw-bold smallest" style="width:auto">
                        <option value=""><?php echo e(__('All Days')); ?></option>
                        <?php $__currentLoopData = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($d); ?>" <?php if(request('day_of_week')===$d): echo 'selected'; endif; ?>><?php echo e(__(ucfirst($d))); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <button class="btn btn-sm btn-dark rounded-pill px-3 fw-bold" type="submit">Go</button>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">No</th>
                                <th><?php echo e(__('Day')); ?></th>
                                <th><?php echo e(__('Scope')); ?></th>
                                <th><?php echo e(__('Site')); ?></th>
                                <th class="pe-4 text-end"><?php echo e(__('Action')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $offs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted" style="width: 60px;"><?php echo e($loop->iteration + (method_exists($offs,'currentPage') ? ($offs->currentPage()-1)*$offs->perPage() : 0)); ?></td>
                                <td><span class="badge badge-info px-3"><?php echo e(__(ucfirst($o->day_of_week))); ?></span></td>
                                <td>
                                    <?php if($o->user_id): ?>
                                        <div class="fw-bold text-dark small"><?php echo e(optional(\App\Models\User::find($o->user_id))->name); ?></div>
                                    <?php else: ?>
                                        <span class="text-muted smallest fw-bold text-uppercase opacity-50"><?php echo e(__('Team-wide')); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($o->location_id): ?>
                                        <span class="text-dark small fw-bold"><?php echo e(optional(\App\Models\Location::find($o->location_id))->name); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted smallest"><?php echo e(auth()->user()->hasRole('Location Admin') ? __('Regional') : 'Global'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-end">
                                    <form method="POST" action="<?php echo e(route('weekly-offs.destroy', $o)); ?>" class="d-inline" onsubmit="return confirm('<?php echo e(__('Are you sure?')); ?>')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-sm btn-light border rounded-pill px-3 fw-bold text-danger shadow-none">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="py-5 opacity-25">
                                        <i class="mdi mdi-calendar-blank-outline fs-1 d-block mb-3"></i>
                                        <p class="mb-0"><?php echo e(__('No schedules defined.')); ?></p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if(method_exists($offs, 'links')): ?>
                <div class="card-footer border-top border-light p-4 bg-transparent d-flex justify-content-end">
                    <?php echo e($offs->links()); ?>

                </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
    .smallest { font-size: 0.7rem; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const locSelect = document.getElementById('weekly_off_location_id');
    const userSelect = document.getElementById('weekly_off_user_id');
    if (!locSelect || !userSelect) return;
    const originalOptions = Array.from(userSelect.options);
    function filterUsers(){
      const locId = locSelect.value;
      userSelect.innerHTML = '';
      userSelect.appendChild(originalOptions[0].cloneNode(true));
      originalOptions.slice(1).forEach(opt => {
        const userLoc = opt.getAttribute('data-user-location');
        if (!locId || userLoc === locId) {
          userSelect.appendChild(opt.cloneNode(true));
        }
      });
      userSelect.value = '';
    }
    locSelect.addEventListener('change', filterUsers);
    filterUsers();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\weekly-offs\index.blade.php ENDPATH**/ ?>