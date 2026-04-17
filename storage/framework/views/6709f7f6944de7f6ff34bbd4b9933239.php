<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-5">
          <h1 class="fw-bold mb-1" style="font-size: 2.2rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?php echo e(__('Holiday Calendar')); ?>

          </h1>
          <p class="text-muted mb-0" style="font-size: 1.1rem; opacity: 0.8;"><?php echo e(__('Schedule observed national holidays and regional closures.')); ?></p>
        </div>
        <div class="col-lg-7 text-lg-end mt-4 mt-lg-0">
          <form method="GET" action="<?php echo e(route('holidays.index')); ?>" class="d-flex flex-wrap gap-2 justify-content-lg-end align-items-center">
            <select name="type" class="form-select form-select-sm rounded-pill px-4 border-light shadow-soft fw-bold" style="width:auto">
              <option value=""><?php echo e(__('Any Category')); ?></option>
              <option value="national" <?php if(request('type')==='national'): echo 'selected'; endif; ?>><?php echo e(__('National Only')); ?></option>
              <option value="local" <?php if(request('type')==='local'): echo 'selected'; endif; ?>><?php echo e(__('Regional Only')); ?></option>
            </select>
            
            <?php if(auth()->user()->hasRole('Super Admin')): ?>
              <select name="location_id" class="form-select form-select-sm rounded-pill px-4 border-light shadow-soft fw-bold" style="width:auto">
                <option value=""><?php echo e(__('All Locations')); ?></option>
                <?php $__currentLoopData = \App\Models\Location::orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($loc->id); ?>" <?php if(request('location_id') == $loc->id): echo 'selected'; endif; ?>><?php echo e($loc->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            <?php endif; ?>

            <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-soft" type="submit"><?php echo e(__('Filter')); ?></button>
            <a href="<?php echo e(route('holidays.index')); ?>" class="btn btn-light rounded-pill px-3 fw-bold border text-muted" title="<?php echo e(__('Reset Filters')); ?>">
                <i class="mdi mdi-refresh"></i>
            </a>
          </form>
        </div>
      </div>

      <div class="row g-4 mb-5">
        <!-- Add Holiday Form -->
        <div class="col-xl-4 col-lg-5">
          <div class="card border-0 shadow-soft rounded-5 sticky-top bg-white" style="top: 20px;">
            <div class="card-body p-5">
                <h5 class="text-dark fw-bold mb-4 d-flex align-items-center">
                  <i class="mdi mdi-calendar-plus text-primary me-2 fs-4"></i>
                  <?php echo e(__('Register New Holiday')); ?>

                </h5>

                <?php if($errors->any()): ?>
                  <div class="alert alert-danger border-0 rounded-4 p-3 mb-4 shadow-sm">
                    <ul class="mb-0 small fw-bold">
                      <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <li><?php echo e($error); ?></li> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                  </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('holidays.store')); ?>">
                  <?php echo csrf_field(); ?>
                  <div class="mb-4">
                    <label class="form-label text-muted status-badge mb-2 d-block ms-1"><?php echo e(__('Observed Date')); ?></label>
                    <input type="date" name="date" class="form-control rounded-pill px-4 border-light shadow-none fw-bold" required />
                  </div>

                  <div class="mb-4">
                    <label class="form-label text-muted status-badge mb-2 d-block ms-1"><?php echo e(__('Event Description')); ?></label>
                    <input type="text" name="name" class="form-control rounded-pill px-4 border-light shadow-none fw-bold" placeholder="<?php echo e(__('e.g. Lunar New Year')); ?>" required />
                  </div>

                  <?php if(auth()->user()->hasRole('Super Admin')): ?>
                    <div class="mb-4">
                      <label class="form-label text-muted status-badge mb-2 d-block ms-1"><?php echo e(__('Targeted Deployment Scope')); ?></label>
                      <select name="scope" class="form-select rounded-pill px-4 border-light shadow-none fw-bold">
                        <option value="national">🌍 <?php echo e(__('All Operational Sites (National)')); ?></option>
                        <?php $__currentLoopData = \App\Models\Location::orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <option value="<?php echo e($loc->id); ?>">📍 <?php echo e($loc->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </select>
                      <div class="smallest text-muted mt-2 fw-medium italic ps-1">* <?php echo e(__('Select "All Sites" to commit globally.')); ?></div>
                    </div>
                  <?php else: ?>
                    <div class="mb-4 p-3 bg-light bg-opacity-50 rounded-4 border border-light d-flex align-items-center justify-content-between">
                       <span class="text-muted status-badge"><?php echo e(__('LOCATION SCOPE')); ?></span>
                       <span class="badge badge-indigo border-0"><?php echo e(auth()->user()->location->name ?? 'Primary Site'); ?></span>
                    </div>
                  <?php endif; ?>

                  <button type="submit" class="btn btn-primary rounded-pill w-100 py-3 fw-bold shadow-soft mt-3">
                    <i class="mdi mdi-content-save-check-outline me-2"></i><?php echo e(__('Commit to Calendar')); ?>

                  </button>
                </form>
            </div>
          </div>
        </div>

        <!-- Holiday List -->
        <div class="col-xl-8 col-lg-7">
          <div class="card border-0 shadow-soft rounded-5 overflow-hidden h-100 bg-white">
            <div class="card-header border-bottom border-light p-4 bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-dark fw-bold"><i class="mdi mdi-format-list-bulleted text-info me-2"></i><?php echo e(__('Programmed Occasions')); ?></h5>
                <span class="badge badge-mint border-0 status-badge shadow-none"><?php echo e($holidays->total() ?? 0); ?> entries</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light bg-opacity-50">
                            <tr>
                                <th class="ps-4 py-3 border-0 status-badge text-muted">ID</th>
                                <th class="py-3 border-0 status-badge text-muted"><?php echo e(__('Chronology')); ?></th>
                                <th class="py-3 border-0 status-badge text-muted"><?php echo e(__('Event Framework')); ?></th>
                                <th class="py-3 border-0 status-badge text-muted"><?php echo e(__('Classification')); ?></th>
                                <th class="pe-4 text-end py-3 border-0 status-badge text-muted"><?php echo e(__('Operations')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $holidays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="transition-base">
                                    <td class="ps-4 text-muted fw-bold smallest" style="width: 60px;"><?php echo e($loop->iteration + (method_exists($holidays,'currentPage') ? ($holidays->currentPage()-1)*$holidays->perPage() : 0)); ?></td>
                                    <td>
                                        <div class="text-dark fw-800 smaller"><?php echo e($h->date->format('d M Y')); ?></div>
                                        <div class="text-muted smallest fw-bold text-uppercase letter-spacing-1"><?php echo e($h->date->format('l')); ?></div>
                                    </td>
                                    <td><span class="text-dark fw-bold smaller"><?php echo e($h->name); ?></span></td>
                                    <td>
                                        <span class="badge <?php echo e($h->is_national ? 'badge-sky' : 'badge-honey'); ?> border-0 px-3 py-1 fw-bold status-badge">
                                            <?php echo e($h->is_national ? __('National') : __('Regional')); ?>

                                        </span>
                                        <?php if(!$h->is_national): ?>
                                            <div class="smallest text-muted mt-1 fw-bold px-1"><i class="mdi mdi-map-marker-outline me-1"></i><?php echo e(optional(\App\Models\Location::find($h->location_id))->name); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <form method="POST" action="<?php echo e(route('holidays.destroy', $h)); ?>" class="d-inline" onsubmit="return confirm('<?php echo e(__('Are you sure?')); ?>')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-light text-dark border bg-white rounded-pill px-3 fw-bold shadow-soft smallest">
                                                <i class="mdi mdi-trash-can-outline text-danger me-1"></i><?php echo e(__('Void')); ?>

                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="py-5 opacity-25">
                                            <i class="mdi mdi-calendar-blank fs-1 d-block mb-3"></i>
                                            <p class="mb-0 fw-bold"><?php echo e(__('No scheduled events in this operational scope.')); ?></p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if(method_exists($holidays, 'links')): ?>
                <div class="card-footer bg-white border-top border-light p-4 d-flex justify-content-end">
                    <?php echo e($holidays->links()); ?>

                </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
    .status-badge { font-size: 0.65rem; font-weight: 800; letter-spacing: 0.05rem; text-transform: uppercase; }
    .smaller { font-size: 0.85rem; }
    .smallest { font-size: 0.7rem; }
    .fw-800 { font-weight: 800; }
    .letter-spacing-1 { letter-spacing: 0.5px; }
    .shadow-soft { box-shadow: 0 10px 40px rgba(0,0,0,0.04) !important; }
    .transition-base { transition: all 0.2s ease; }
    tr.transition-base:hover { background-color: rgba(248, 250, 252, 0.8); }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/holidays/index.blade.php ENDPATH**/ ?>