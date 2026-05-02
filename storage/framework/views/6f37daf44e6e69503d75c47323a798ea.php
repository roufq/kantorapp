<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?php echo e(__('Organization Sites')); ?>

          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;"><?php echo e(__('Manage physical locations and operational branches.')); ?></p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
          <a href="<?php echo e(route('locations.create')); ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg">
            <i class="mdi mdi-plus-circle-outline me-2"></i><?php echo e(__('Add Location')); ?>

          </a>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="card mb-5 border-0 shadow-sm rounded-4">
          <div class="card-body p-4">
              <form method="GET" action="<?php echo e(route('locations.index')); ?>">
                  <div class="row g-3">
                      <div class="col-md-5">
                          <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Search Site')); ?></label>
                          <input type="text" name="search" class="form-control rounded-pill px-4 border-light shadow-none" placeholder="<?php echo e(__('Search by name or code')); ?>" value="<?php echo e(request('search')); ?>">
                      </div>

                      <div class="col-md-3">
                          <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Status')); ?></label>
                          <select name="status" class="form-select rounded-pill px-4 border-light shadow-none fw-bold">
                              <option value=""><?php echo e(__('All Status')); ?></option>
                              <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>><?php echo e(__('Active')); ?></option>
                              <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>><?php echo e(__('Inactive')); ?></option>
                          </select>
                      </div>

                      <div class="col-md-4 d-flex align-items-end gap-2">
                          <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold flex-grow-1">
                              <i class="mdi mdi-filter-variant me-1"></i><?php echo e(__('Filter')); ?>

                          </button>
                          <a href="<?php echo e(route('locations.index')); ?>" class="btn btn-light border rounded-pill px-4 fw-bold text-muted flex-grow-1">
                              <i class="mdi mdi-refresh me-1"></i><?php echo e(__('Reset')); ?>

                          </a>
                      </div>
                  </div>
              </form>
          </div>
      </div>

      <!-- Data Table -->
      <div class="card border-0 shadow-sm overflow-hidden">
          <div class="card-header border-bottom border-light p-4 bg-transparent d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-map-marker-radius text-info me-2"></i><?php echo e(__('Global Site List')); ?></h5>
              <span class="text-muted small fw-bold"><?php echo e($locations->total()); ?> Locations</span>
          </div>
          <div class="card-body p-0">
              <div class="table-responsive">
                  <table class="table align-middle mb-0">
                      <thead>
                          <tr>
                              <th class="ps-4">No</th>
                              <th><?php echo e(__('Site')); ?></th>
                              <th><?php echo e(__('Code')); ?></th>
                              <th><?php echo e(__('Timezone')); ?></th>
                              <th class="text-center"><?php echo e(__('Status')); ?></th>
                              <th class="pe-4 text-end"><?php echo e(__('Actions')); ?></th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php $__empty_1 = true; $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                              <tr>
                                  <td class="ps-4 text-muted fw-bold" style="width: 60px;"><?php echo e($loop->iteration + ($locations->currentPage()-1)*$locations->perPage()); ?></td>
                                  <td>
                                      <div class="d-flex align-items-center">
                                          <div class="avatar-sm me-3 bg-light border rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 42px; height: 42px;">
                                              <i class="mdi mdi-office-building-marker"></i>
                                          </div>
                                          <div>
                                              <div class="fw-bold text-dark"><?php echo e($location->name); ?></div>
                                              <div class="text-muted smaller"><?php echo e(Str::limit($location->address ?: '-', 40)); ?></div>
                                          </div>
                                      </div>
                                  </td>
                                  <td><span class="badge badge-info"><?php echo e($location->code); ?></span></td>
                                  <td><span class="text-dark small fw-medium"><?php echo e($location->timezone); ?></span></td>
                                  <td class="text-center">
                                      <span class="badge <?php echo e($location->is_active ? 'badge-success' : 'badge-danger'); ?>">
                                          <?php echo e(strtoupper($location->is_active ? __('Active') : __('Inactive'))); ?>

                                      </span>
                                  </td>
                                  <td class="pe-4 text-end">
                                      <div class="dropdown">
                                          <button class="btn btn-sm btn-light border rounded-pill px-3 shadow-none fw-bold" type="button" data-bs-toggle="dropdown">
                                              Options <i class="mdi mdi-chevron-down ms-1"></i>
                                          </button>
                                          <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2">
                                              <li><a class="dropdown-item fw-bold rounded-2" href="<?php echo e(route('locations.show', $location)); ?>"><i class="mdi mdi-eye-outline me-2 text-info"></i> Details</a></li>
                                              <?php if(auth()->user()->hasRole('Super Admin')): ?>
                                                <li><a class="dropdown-item fw-bold rounded-2" href="<?php echo e(route('locations.settings', $location)); ?>"><i class="mdi mdi-cog-outline me-2 text-warning"></i> Settings</a></li>
                                              <?php endif; ?>
                                              <li><a class="dropdown-item fw-bold rounded-2" href="<?php echo e(route('locations.edit', $location)); ?>"><i class="mdi mdi-pencil-outline me-2 text-primary"></i> Edit</a></li>
                                              <li><hr class="dropdown-divider"></li>
                                              <li>
                                                  <form action="<?php echo e(route('locations.destroy', $location)); ?>" method="POST" onsubmit="return confirm('<?php echo e(__('Are you sure?')); ?>')">
                                                      <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                      <button type="submit" class="dropdown-item fw-bold text-danger rounded-2">
                                                          <i class="mdi mdi-trash-can-outline me-2"></i> Delete
                                                      </button>
                                                  </form>
                                              </li>
                                          </ul>
                                      </div>
                                  </td>
                              </tr>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                              <tr>
                                  <td colspan="6" class="text-center py-5">
                                      <div class="py-5 opacity-25">
                                          <i class="mdi mdi-map-marker-off fs-1 d-block mb-3"></i>
                                          <p class="mb-0"><?php echo e(__('No locations found.')); ?></p>
                                      </div>
                                  </td>
                              </tr>
                          <?php endif; ?>
                      </tbody>
                  </table>
              </div>
          </div>
          <?php if($locations->hasPages()): ?>
              <div class="card-footer bg-transparent border-top border-light p-4 d-flex justify-content-end">
                  <?php echo e($locations->appends(request()->query())->links()); ?>

              </div>
          <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\locations\index.blade.php ENDPATH**/ ?>