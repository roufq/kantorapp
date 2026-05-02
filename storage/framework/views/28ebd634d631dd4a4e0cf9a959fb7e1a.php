<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
  <div class="content pt-4">
    <div class="container-fluid">
      <div class="row mb-5 align-items-end">
        <div class="col-lg-7">
          <div class="d-flex align-items-center mb-2">
            <div class="rounded-4 bg-primary bg-opacity-10 p-2 me-3">
                <i class="mdi mdi-calendar-multiselect fs-3 text-primary"></i>
            </div>
            <h1 class="fw-800 mb-0" style="font-size: clamp(1.8rem, 4vw, 2.8rem); background: linear-gradient(135deg, #1e293b 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
              <?php echo e(__('Operational Roster')); ?>

            </h1>
          </div>
          <p class="text-muted mb-0 ps-1" style="font-size: 1.1rem; opacity: 0.7;"><?php echo e(__('Advanced visual intelligence for workforce scheduling and availability metrics.')); ?></p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
          <a href="<?php echo e(route('shifts.rosters.index')); ?>" class="btn btn-white border-0 rounded-pill px-4 py-3 fw-bold shadow-soft">
            <i class="mdi mdi-format-list-bulleted me-2 text-primary"></i><?php echo e(__('Go to Roster Directory')); ?>

          </a>
        </div>
      </div>

      <div class="card mb-4 border-0 shadow-soft rounded-5 glass-filter">
        <div class="card-body p-4">
          <form method="GET" action="<?php echo e(route('shifts.rosters.calendar')); ?>" class="row g-4 align-items-end">
            <div class="col-md-5">
              <label class="form-label text-muted smaller fw-bold text-uppercase ms-1 mb-2"><?php echo e(__('Select Operational Site')); ?></label>
              <div class="input-group rounded-pill overflow-hidden border border-light shadow-sm">
                <span class="input-group-text bg-white border-0 ps-3"><i class="mdi mdi-map-marker-radius text-primary"></i></span>
                <select name="location_id" class="form-select border-0 bg-white fw-bold shadow-none" onchange="this.form.submit()">
                  <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($loc->id); ?>" <?php if($loc->id == $locationId): ?> selected <?php endif; ?>><?php echo e($loc->name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            </div>
            <div class="col-md-5">
              <label class="form-label text-muted smaller fw-bold text-uppercase ms-1 mb-2"><?php echo e(__('Timeline Navigation')); ?></label>
              <div class="input-group rounded-pill overflow-hidden border border-light shadow-sm">
                <span class="input-group-text bg-white border-0 ps-3"><i class="mdi mdi-calendar-search text-primary"></i></span>
                <input type="date" name="week_start" class="form-control border-0 bg-white fw-bold shadow-none" value="<?php echo e($focusDate->toDateString()); ?>" onchange="this.form.submit()">
              </div>
            </div>
            <div class="col-md-2 text-md-end">
                <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-bold shadow-soft">
                    <?php echo e(__('Sync View')); ?>

                </button>
            </div>
          </form>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-lg-3">
          <div class="card p-4 border-0 shadow-soft rounded-5 h-100" style="background: linear-gradient(180deg, #f5f3ff 0%, #ffffff 100%);">
            <div id="calendar-notes-panel">
                <h6 class="fw-800 mb-4 text-dark d-flex align-items-center"><i class="mdi mdi-information-outline me-2 text-primary fs-4"></i><?php echo e(__('Calendar Intelligence')); ?></h6>
                <div class="fc-external-list">
                <div class="note-pill">Schedules are synced per location.</div>
                <div class="note-pill" style="border-left-color: #f43f5e;">Check special permits in red.</div>
                <div class="note-pill" style="border-left-color: #0ea5e9;">Holidays are marked automatically.</div>
                </div>
            </div>

            <div id="personnel-pulse-panel" style="display: none;">
                <h6 class="fw-800 mb-2 text-dark d-flex align-items-center"><i class="mdi mdi-account-group-outline me-2 text-primary fs-4"></i><?php echo e(__('Personnel Pulse')); ?></h6>
                <p class="smaller text-muted mb-4" id="pulse-date-label">Selected Date</p>
                <div id="pulse-list" class="d-flex flex-column gap-2">
                    <!-- Dynamic List -->
                </div>
                <button class="btn btn-link btn-sm mt-3 p-0 text-primary fw-bold" onclick="resetPulseView()">
                    <i class="mdi mdi-arrow-left me-1"></i> Back to Intelligence
                </button>
            </div>
            
            <div class="mt-auto pt-4 border-top border-light border-opacity-50">
              <div class="smaller fw-bold text-muted text-uppercase mb-3 letter-spacing-1"><?php echo e(__('LEGEND')); ?></div>
              <div class="d-flex flex-column gap-3">
                <div class="legend-item"><span class="legend-dot" style="background:#0ea5e9"></span> Active Shift</div>
                <div class="legend-item"><span class="legend-dot" style="background:#f5f3ff; border: 1px solid #ddd;"></span> OFF Day</div>
                <div class="legend-item"><span class="legend-dot" style="background:#fff1f2"></span> Leave / Permit</div>
                <div class="legend-item"><span class="legend-dot" style="background:#ffffff; border:1px dashed #cbd5e1"></span> No Schedule</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-9">
          <div id="calendar" data-events='<?php echo json_encode($calendarEvents, 15, 512) ?>' data-focus-date="<?php echo e($focusDate->toDateString()); ?>"></div>
        </div>
      </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views\shifts\rosters\calendar.blade.php ENDPATH**/ ?>