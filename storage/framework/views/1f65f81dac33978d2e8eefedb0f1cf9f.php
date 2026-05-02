<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-7">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?php echo e(__('Job Descriptions')); ?>

          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;"><?php echo e(__('Define roles, responsibilities, and task catalogs for team members.')); ?></p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
          <a href="<?php echo e(route('jobdesks.create')); ?>" class="btn btn-primary rounded-pill px-4 fw-bold shadow-lg">
            <i class="mdi mdi-plus-circle-outline me-2"></i><?php echo e(__('Add New Jobdesk')); ?>

          </a>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="card mb-5 border-0 shadow-sm rounded-4">
          <div class="card-body p-4">
              <form method="GET" action="<?php echo e(route('jobdesks.index')); ?>" class="row g-3 align-items-end">
                <div class="col-md-5">
                  <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Location Filter')); ?></label>
                  <select name="location_id" class="form-select rounded-pill px-4 border-light shadow-none fw-bold">
                    <option value=""><?php echo e(__('All Locations')); ?></option>
                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($loc->id); ?>" <?php if(request('location_id') == $loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name ?? $loc->nama ?? 'Location '.$loc->id); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label text-muted small fw-bold text-uppercase mb-2"><?php echo e(__('Active Status')); ?></label>
                  <select name="status" class="form-select rounded-pill px-4 border-light shadow-none fw-bold">
                    <option value=""><?php echo e(__('All Status')); ?></option>
                    <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>><?php echo e(__('Active')); ?></option>
                    <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>><?php echo e(__('Inactive')); ?></option>
                  </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                  <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold flex-grow-1"><?php echo e(__('Filter')); ?></button>
                  <a href="<?php echo e(route('jobdesks.index')); ?>" class="btn btn-light border rounded-pill px-4 fw-bold text-muted flex-grow-1"><?php echo e(__('Reset')); ?></a>
                </div>
              </form>
          </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header border-bottom border-light p-4 bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-clipboard-text-outline text-info me-2"></i><?php echo e(__('Role Repository')); ?></h5>
                <span class="text-muted small fw-bold"><?php echo e($jobdesks->total()); ?> entries</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">No</th>
                                <th><?php echo e(__('Role Name')); ?></th>
                                <th><?php echo e(__('Location')); ?></th>
                                <th><?php echo e(__('Role Scope')); ?></th>
                                <th class="text-center"><?php echo e(__('Status')); ?></th>
                                <th class="pe-4 text-end"><?php echo e(__('Actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $jobdesks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jobdesk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted" style="width: 60px;"><?php echo e($loop->iteration + ($jobdesks->currentPage()-1)*$jobdesks->perPage()); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3 bg-light border rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 40px; height: 40px;">
                                                <i class="mdi mdi-briefcase-outline"></i>
                                            </div>
                                            <span class="fw-bold text-dark fs-6"><?php echo e($jobdesk->name); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info bg-opacity-10 text-info border-info border-opacity-25 border px-3">
                                            <i class="mdi mdi-map-marker-outline me-1"></i><?php echo e($jobdesk->location?->name ?? $jobdesk->location?->nama ?? __('Global')); ?>

                                        </span>
                                    </td>
                                    <td><span class="text-muted small fw-semibold"><?php echo e($jobdesk->role_scope ?? '-'); ?></span></td>
                                    <td class="text-center">
                                        <span class="badge <?php echo e($jobdesk->is_active ? 'badge-success' : 'badge-danger'); ?>">
                                            <?php echo e($jobdesk->is_active ? __('Active') : __('Inactive')); ?>

                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border rounded-pill px-3 shadow-none fw-bold" type="button" data-bs-toggle="dropdown">
                                                Options <i class="mdi mdi-chevron-down ms-1"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2">
                                                <li><a class="dropdown-item fw-bold rounded-2" href="<?php echo e(route('jobdesks.show', $jobdesk)); ?>"><i class="mdi mdi-eye-outline me-2 text-info"></i> Details</a></li>
                                                <li><a class="dropdown-item fw-bold rounded-2" href="<?php echo e(route('jobdesks.edit', $jobdesk)); ?>"><i class="mdi mdi-pencil-outline me-2 text-primary"></i> Edit</a></li>
                                                <li><a class="dropdown-item fw-bold rounded-2" href="<?php echo e(route('jobdesks.catalogs.index', $jobdesk)); ?>"><i class="mdi mdi-format-list-bulleted me-2 text-warning"></i> Catalog</a></li>
                                                <li><a class="dropdown-item fw-bold rounded-2" href="<?php echo e(route('jobdesks.assignments.index', $jobdesk)); ?>"><i class="mdi mdi-account-group-outline me-2 text-success"></i> Team</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                  <form action="<?php echo e(route('jobdesks.destroy', $jobdesk)); ?>" method="POST" onsubmit="return confirm('<?php echo e(__('Are you sure?')); ?>')">
                                                      <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                      <button type="submit" class="dropdown-item fw-bold text-danger rounded-2"><i class="mdi mdi-trash-can-outline me-2"></i> Delete</button>
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
                                            <i class="mdi mdi-clipboard-off-outline fs-1 d-block mb-3"></i>
                                            <p class="mb-0"><?php echo e(__('No roles found.')); ?></p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if($jobdesks->hasPages()): ?>
                <div class="card-footer border-top border-light p-4 bg-transparent d-flex justify-content-end">
                    <?php echo e($jobdesks->links()); ?>

                </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
    .dropdown-item:active { background-color: var(--primary) !important; color: white !important; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\jobdesks\index.blade.php ENDPATH**/ ?>