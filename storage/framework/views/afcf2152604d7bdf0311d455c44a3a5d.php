<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">
      <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
          <h2 class="text-dark mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;"><?php echo e(__('Location Shifts')); ?></h2>
          <p class="text-muted mb-0" style="font-size: 1.05rem;"><?php echo e(__('Manage shifts assigned to each location.')); ?></p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
          <a href="<?php echo e(route('location-shifts.create')); ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
            <i class="mdi mdi-plus-circle-outline me-2"></i><?php echo e(__('Assign Shifts to Location')); ?>

          </a>
        </div>
      </div>

      <div class="card shadow-sm border-0 p-4 rounded-4 border border-light mb-4 shadow-sm">
          <form method="GET" action="<?php echo e(route('location-shifts.index')); ?>" class="row g-3 align-items-end">
              <div class="col-md-4">
                  <label for="location_id" class="form-label text-muted small fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Filter by Location:')); ?></label>
                  <select name="location_id" id="location_id" class="form-select bg-dark bg-opacity-50 border-light text-white rounded-pill px-4 shadow-none">
                      <option value=""><?php echo e(__('All Locations')); ?></option>
                      <?php $__currentLoopData = $allLocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <option value="<?php echo e($location->id); ?>" <?php echo e(request('location_id') == $location->id ? 'selected' : ''); ?>>
                              <?php echo e($location->name); ?> (<?php echo e($location->code); ?>)
                          </option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
              </div>
              <div class="col-md-5">
                  <label for="search" class="form-label text-muted small fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Search:')); ?></label>
                  <div class="input-group">
                      <span class="input-group-text bg-dark border-0 border-opacity-10 text-muted px-3"><i class="mdi mdi-magnify"></i></span>
                      <input type="text" name="search" id="search" class="form-control bg-dark bg-opacity-50 border-light text-white rounded-end-pill px-4 shadow-none"
                             value="<?php echo e(request('search')); ?>" placeholder="<?php echo e(__('Location name or code')); ?>">
                  </div>
              </div>
              <div class="col-md-3 d-flex gap-2">
                  <button type="submit" class="btn btn-info rounded-pill px-4 fw-bold shadow-sm flex-grow-1">
                      <i class="mdi mdi-filter-variant me-1"></i><?php echo e(__('Filter')); ?>

                  </button>
                  <a href="<?php echo e(route('location-shifts.index')); ?>" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                      <i class="mdi mdi-close me-1"></i><?php echo e(__('Clear')); ?>

                  </a>
              </div>
          </form>
      </div>

      <div class="card shadow-sm border-0" style="border-radius: 24px !important;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-white">
                    <thead class="bg-white bg-opacity-5">
                        <tr>
                            <th class="ps-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1" style="width: 80px"><?php echo e(__('No')); ?></th>
                            <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Location')); ?></th>
                            <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Code')); ?></th>
                            <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1"><?php echo e(__('Assigned Shifts')); ?></th>
                            <th class="pe-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1 text-end"><?php echo e(__('Actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="border-bottom border-white border-opacity-5">
                                <td class="ps-4 fw-bold text-muted"><?php echo e($loop->iteration + ($locations->currentPage()-1)*$locations->perPage()); ?></td>
                                <td class="fw-bold"><?php echo e($location->name); ?></td>
                                <td><span class="badge rounded-pill bg-dark border border-light px-3"><?php echo e($location->code); ?></span></td>
                                <td>
                                    <?php if($location->shifts->count() > 0): ?>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php $__currentLoopData = $location->shifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info border-opacity-25 d-flex align-items-center py-2 px-3">
                                                    <span class="me-2"><?php echo e($shift->name); ?></span>
                                                    <a href="<?php echo e(route('location-shifts.detach-shift', [$location, $shift])); ?>"
                                                       class="text-danger hover-scale transition-all"
                                                       onclick="return confirm('<?php echo e(__('Remove this shift from location?')); ?>')">
                                                        <i class="mdi mdi-close-circle fs-6"></i>
                                                    </a>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small italic"><?php echo e(__('No shifts assigned')); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="<?php echo e(route('location-shifts.edit', $location)); ?>" class="btn btn-outline-info btn-sm rounded-pill px-3 fw-bold">
                                            <i class="mdi mdi-pencil-box-multiple-outline me-1"></i><?php echo e(__('Manage Shifts')); ?>

                                        </a>
                                        <a href="<?php echo e(route('location-shifts.show', $location)); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold">
                                            <i class="mdi mdi-eye-outline me-1"></i><?php echo e(__('View')); ?>

                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="mdi mdi-map-marker-off-outline fs-1 text-muted d-block mb-2"></i>
                                    <span class="text-muted"><?php echo e(__('No locations found.')); ?></span>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php if($locations->hasPages()): ?>
            <div class="card-footer border-top border-light p-4 bg-transparent d-flex justify-content-center">
                <?php echo e($locations->appends(request()->query())->links()); ?>

            </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<style>
.italic { font-style: italic; }
.smaller { font-size: 0.8rem; }
.letter-spacing-1 { letter-spacing: 1px; }
.hover-scale:hover { transform: scale(1.2); }
.table-hover tbody tr:hover { background-color: rgba(255,255,255,0.02) !important; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\location-shifts\index.blade.php ENDPATH**/ ?>