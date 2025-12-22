

<?php $__env->startSection('content'); ?>
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Edit Lokasi</h3>
        <p class="text-muted mb-0">Perbarui detail lokasi agar data operasional tetap akurat.</p>
    </div>
    <a href="<?php echo e(route('locations.index')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h3 class="card-title">Edit Location Information</h3>
                        </div>
                        <!-- /.card-header -->

                        <!-- form start -->
                        <form action="<?php echo e(route('locations.update', $location)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="brand_name">Brand Name</label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['brand_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="brand_name" name="brand_name" value="<?php echo e(old('brand_name', $location->brand_name)); ?>">
                                            <?php $__errorArgs = ['brand_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert"><strong><?php echo e($message); ?></strong></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="brand_logo_url">Brand Logo URL/Path</label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['brand_logo_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="brand_logo_url" name="brand_logo_url" value="<?php echo e(old('brand_logo_url', $location->brand_logo_url)); ?>" placeholder="/storage/logos/main.png or https://...">
                                            <?php $__errorArgs = ['brand_logo_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert"><strong><?php echo e($message); ?></strong></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <?php $logo = $location->brand_logo_url ? (Str::startsWith($location->brand_logo_url, ['http://','https://']) ? $location->brand_logo_url : asset($location->brand_logo_url)) : null; ?>
                                        <?php if($logo): ?>
                                        <img src="<?php echo e($logo); ?>" alt="Logo" class="img-thumbnail" style="max-height:48px;">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="primary_color">Primary Color (e.g., #0d6efd)</label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['primary_color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="primary_color" name="primary_color" value="<?php echo e(old('primary_color', $location->primary_color)); ?>">
                                            <?php $__errorArgs = ['primary_color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert"><strong><?php echo e($message); ?></strong></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="secondary_color">Secondary Color</label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['secondary_color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="secondary_color" name="secondary_color" value="<?php echo e(old('secondary_color', $location->secondary_color)); ?>">
                                            <?php $__errorArgs = ['secondary_color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert"><strong><?php echo e($message); ?></strong></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="custom_css_url">Custom CSS URL</label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['custom_css_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="custom_css_url" name="custom_css_url" value="<?php echo e(old('custom_css_url', $location->custom_css_url)); ?>">
                                            <?php $__errorArgs = ['custom_css_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert"><strong><?php echo e($message); ?></strong></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="custom_js_url">Custom JS URL</label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['custom_js_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="custom_js_url" name="custom_js_url" value="<?php echo e(old('custom_js_url', $location->custom_js_url)); ?>">
                                            <?php $__errorArgs = ['custom_js_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert"><strong><?php echo e($message); ?></strong></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name" name="name" value="<?php echo e(old('name', $location->name)); ?>" required>
                                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert">
                                                <strong><?php echo e($message); ?></strong>
                                            </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="code">Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="code" name="code" value="<?php echo e(old('code', $location->code)); ?>" required maxlength="10">
                                            <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert">
                                                <strong><?php echo e($message); ?></strong>
                                            </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <small class="form-text text-muted">Unique code for the location (max 10 characters)</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="address" name="address" rows="3"><?php echo e(old('address', $location->address)); ?></textarea>
                                            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert">
                                                <strong><?php echo e($message); ?></strong>
                                            </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="timezone">Timezone</label>
                                            <select class="form-control <?php $__errorArgs = ['timezone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="timezone" name="timezone">
                                                <option value="Asia/Jakarta" <?php echo e(old('timezone', $location->timezone) == 'Asia/Jakarta' ? 'selected' : ''); ?>>Asia/Jakarta (WIB)</option>
                                                <option value="Asia/Makassar" <?php echo e(old('timezone', $location->timezone) == 'Asia/Makassar' ? 'selected' : ''); ?>>Asia/Makassar (WITA)</option>
                                                <option value="Asia/Jayapura" <?php echo e(old('timezone', $location->timezone) == 'Asia/Jayapura' ? 'selected' : ''); ?>>Asia/Jayapura (WIT)</option>
                                            </select>
                                            <?php $__errorArgs = ['timezone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert">
                                                <strong><?php echo e($message); ?></strong>
                                            </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="latitude">Latitude</label>
                                            <input type="number" step="any" class="form-control <?php $__errorArgs = ['latitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="latitude" name="latitude" value="<?php echo e(old('latitude', $location->latitude)); ?>" placeholder="-6.2088">
                                            <?php $__errorArgs = ['latitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert">
                                                <strong><?php echo e($message); ?></strong>
                                            </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <small class="form-text text-muted">Optional: Latitude coordinate for geo-fencing</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="longitude">Longitude</label>
                                            <input type="number" step="any" class="form-control <?php $__errorArgs = ['longitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="longitude" name="longitude" value="<?php echo e(old('longitude', $location->longitude)); ?>" placeholder="106.8456">
                                            <?php $__errorArgs = ['longitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert">
                                                <strong><?php echo e($message); ?></strong>
                                            </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <small class="form-text text-muted">Optional: Longitude coordinate for geo-fencing</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="radius">Radius (meters)</label>
                                            <input type="number" class="form-control <?php $__errorArgs = ['radius'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="radius" name="radius" value="<?php echo e(old('radius', $location->radius ?? 50)); ?>" min="1" max="10000">
                                            <?php $__errorArgs = ['radius'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert">
                                                <strong><?php echo e($message); ?></strong>
                                            </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <small class="form-text text-muted">Radius in meters for attendance check-in (default: 50m)</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-12">
                                        <div id="loc-map" style="height: 340px; border-radius: 6px; overflow: hidden; border: 1px solid #dee2e6; position:relative;">
                                            <div id="loc-map-status" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#6c757d;font-size:14px;">Loading map…</div>
                                        </div>
                                        <small class="text-muted d-block mt-1">Tip: drag marker atau klik peta untuk memilih titik. Gunakan pencarian untuk mencari alamat.</small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="shift_enabled" name="shift_enabled" value="1" <?php echo e(old('shift_enabled', $location->shift_enabled) ? 'checked' : ''); ?>>
                                        <label for="shift_enabled" class="custom-control-label">
                                            Enable Shift System
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Enable shift-based attendance for this location. If disabled, employees will use fixed working hours.</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="default_shift_id">Schedule Type</label>
                                            <select class="form-control <?php $__errorArgs = ['default_shift_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="default_shift_id" name="default_shift_id">
                                                <option value="">Select Shift</option>
                                                <?php ($selectedShiftId = old('default_shift_id', $defaultShiftId ?? null)); ?>
                                                <?php if(isset($assignedShiftsMultiple)): ?>
                                                <optgroup label="Assigned to Location — Multiple">
                                                    <?php $__currentLoopData = $assignedShiftsMultiple; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($s->id); ?>" data-type="multiple" data-scope="assigned" <?php echo e((string)$selectedShiftId === (string)$s->id ? 'selected' : ''); ?>>
                                                            <?php echo e($s->name); ?> (<?php echo e($s->getFormattedSchedule()); ?>)
                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </optgroup>
                                                <?php endif; ?>
                                                <?php if(isset($assignedShiftsSingle)): ?>
                                                <optgroup label="Assigned to Location — Single">
                                                    <?php $__currentLoopData = $assignedShiftsSingle; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($s->id); ?>" data-type="single" data-scope="assigned" <?php echo e((string)$selectedShiftId === (string)$s->id ? 'selected' : ''); ?>>
                                                            <?php echo e($s->name); ?> (<?php echo e($s->getFormattedSchedule()); ?>)
                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </optgroup>
                                                <?php endif; ?>
                                                <?php if(isset($allActiveShiftsMultiple)): ?>
                                                <optgroup label="All Active Shifts — Multiple">
                                                    <?php $__currentLoopData = $allActiveShiftsMultiple; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($s->id); ?>" data-type="multiple" data-scope="global" <?php echo e((string)$selectedShiftId === (string)$s->id ? 'selected' : ''); ?>>
                                                            <?php echo e($s->name); ?> (<?php echo e($s->getFormattedSchedule()); ?>)
                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </optgroup>
                                                <?php endif; ?>
                                                <?php if(isset($allActiveShiftsSingle)): ?>
                                                <optgroup label="All Active Shifts — Single">
                                                    <?php $__currentLoopData = $allActiveShiftsSingle; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($s->id); ?>" data-type="single" data-scope="global" <?php echo e((string)$selectedShiftId === (string)$s->id ? 'selected' : ''); ?>>
                                                            <?php echo e($s->name); ?> (<?php echo e($s->getFormattedSchedule()); ?>)
                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </optgroup>
                                                <?php endif; ?>
                                            </select>
                                            <?php $__errorArgs = ['default_shift_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback" role="alert">
                                                <strong><?php echo e($message); ?></strong>
                                            </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <small class="form-text text-muted">Saat "Enable Shift System" dicentang tampil opsi Multiple Shift, jika tidak maka tampil Single Shift. Nilai yang disimpan adalah shift_id terpilih.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" id="daily_schedule_group" style="display: none;">
                                    <label for="daily_schedule">Daily Schedule</label>
                                    <textarea class="form-control <?php $__errorArgs = ['daily_schedule'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="daily_schedule" name="daily_schedule" rows="5" placeholder='Example: {"monday": {"shift_id": 1}, "tuesday": {"shift_id": 2}}'><?php echo e(old('daily_schedule', $location->daily_schedule ? json_encode($location->daily_schedule, JSON_PRETTY_PRINT) : '')); ?></textarea>
                                    <?php $__errorArgs = ['daily_schedule'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-feedback" role="alert">
                                        <strong><?php echo e($message); ?></strong>
                                    </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="form-text text-muted">Define working schedule per day. For flexible schedules, use JSON format. For fixed schedules, assign shift IDs to days.</small>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo e(old('is_active', $location->is_active) ? 'checked' : ''); ?>>
                                        <label for="is_active" class="custom-control-label">
                                            Active
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Inactive locations cannot be used for new assignments</small>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-save"></i> Update Location
                                </button>
                                <?php if(auth()->user()->hasRole('Super Admin')): ?>
                                <a href="<?php echo e(route('locations.settings', $location)); ?>" class="btn btn-primary">
                                    <i class="bi bi-gear"></i> Settings
                                </a>
                                <?php endif; ?>
                                <a href="<?php echo e(route('locations.show', $location)); ?>" class="btn btn-info">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                <a href="<?php echo e(route('locations.index')); ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link rel="stylesheet" href="<?php echo e(asset('vendor/leaflet/leaflet.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('vendor/leaflet/Control.Geocoder.css')); ?>" />
<style>
    .leaflet-control-geocoder {
        max-width: 420px
    }

    .leaflet-control-geocoder-form input {
        width: 320px;
        min-width: 220px;
        font-size: 14px;
        color: #212529;
        background: #fff;
        padding: 6px 10px
    }

    .leaflet-control-geocoder .leaflet-control-geocoder-alternatives {
        max-height: 260px;
        overflow: auto
    }
</style>
<script src="<?php echo e(asset('vendor/leaflet/leaflet.js')); ?>"></script>
<script src="<?php echo e(asset('vendor/leaflet/Control.Geocoder.js')); ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter shift options based on Enable Shift System (multiple vs single)
        const shiftEnabled = document.getElementById('shift_enabled');
        const defaultShift = document.getElementById('default_shift_id');

        function filterShiftOptions() {
            if (!defaultShift) return;
            const want = (shiftEnabled && shiftEnabled.checked) ? 'multiple' : 'single';
            const opts = Array.from(defaultShift.options);
            opts.forEach((opt) => {
                if (!opt.value) return; // keep placeholder
                const t = opt.getAttribute('data-type');
                const show = !t || t === want;
                opt.hidden = !show;
            });
            // Clear selection if hidden
            const sel = defaultShift.selectedOptions[0];
            if (sel && sel.hidden) defaultShift.value = '';
        }
        if (shiftEnabled) shiftEnabled.addEventListener('change', filterShiftOptions);
        filterShiftOptions();

        function initMapWhenReady() {
            if (!(window.L && L.map)) {
                const st = document.getElementById('loc-map-status');
                if (st) st.textContent = 'Loading map assets…';
                return setTimeout(initMapWhenReady, 150);
            }

            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const radiusInput = document.getElementById('radius');

            const fallback = {
                lat: -7.8121780996020185,
                lng: 110.35047828093953,
                zoom: 12
            };
            const initLat = parseFloat(latInput?.value) || fallback.lat;
            const initLng = parseFloat(lngInput?.value) || fallback.lng;

            const map = L.map('loc-map').setView([initLat, initLng], fallback.zoom);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const st = document.getElementById('loc-map-status');
            if (st) st.style.display = 'none';

            let marker = L.marker([initLat, initLng], {
                draggable: true
            }).addTo(map);
            let circle = null;

            function redrawCircle() {
                const r = parseFloat(radiusInput?.value) || 50;
                if (circle) map.removeLayer(circle);
                circle = L.circle(marker.getLatLng(), {
                    radius: r,
                    color: '#0d6efd',
                    fillOpacity: 0.08
                }).addTo(map);
            }
            redrawCircle();

            marker.on('dragend', () => {
                const p = marker.getLatLng();
                if (latInput) latInput.value = p.lat;
                if (lngInput) lngInput.value = p.lng;
                redrawCircle();
            });
            map.on('click', (e) => {
                marker.setLatLng(e.latlng);
                if (latInput) latInput.value = e.latlng.lat;
                if (lngInput) lngInput.value = e.latlng.lng;
                redrawCircle();
            });

            if (latInput && lngInput) {
                latInput.addEventListener('change', () => {
                    const lat = parseFloat(latInput.value);
                    const lng = parseFloat(lngInput.value);
                    if (isFinite(lat) && isFinite(lng)) {
                        const ll = L.latLng(lat, lng);
                        marker.setLatLng(ll);
                        map.setView(ll);
                        redrawCircle();
                    }
                });
                lngInput.addEventListener('change', () => latInput.dispatchEvent(new Event('change')));
            }
            if (radiusInput) radiusInput.addEventListener('change', redrawCircle);

            if (L.Control && L.Control.Geocoder) {
                const geocodeParams = {
                    limit: 6,
                    addressdetails: 1,
                    "accept-language": "id,en"
                };
                if (typeof window.APP_GEO_COUNTRY_CODES === "string" && window.APP_GEO_COUNTRY_CODES.trim().length) {
                    geocodeParams.countrycodes = window.APP_GEO_COUNTRY_CODES.trim();
                } else {
                    geocodeParams.countrycodes = "id";
                }
                const geocoderService = L.Control.Geocoder.nominatim({
                    serviceUrl: "https://nominatim.openstreetmap.org/",
                    geocodingQueryParams: geocodeParams
                });
                const geocoder = L.Control.geocoder({
                    defaultMarkGeocode: false,
                    collapsed: false,
                    placeholder: "Cari alamat atau tempat�",
                    position: "topleft",
                    geocoder: geocoderService,
                    suggestMinLength: 3,
                    suggestTimeout: 200
                }).on("markgeocode", function(e) {
                    const ll = e.geocode.center;
                    map.setView(ll, 17);
                    marker.setLatLng(ll);
                    if (latInput) latInput.value = ll.lat;
                    if (lngInput) lngInput.value = ll.lng;
                    redrawCircle();
                });
                geocoder.addTo(map);
            }
        }
        if (document.readyState === 'complete') initMapWhenReady();
        else {
            window.addEventListener('load', initMapWhenReady);
            setTimeout(initMapWhenReady, 800);
        }
    });
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.appnew', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\www\kantorapp\resources\views/locations/edit.blade.php ENDPATH**/ ?>