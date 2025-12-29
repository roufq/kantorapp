<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Login | Office App</title>
    <meta content="Login to access the portal" name="description">
    <link rel="shortcut icon" href="<?php echo e(asset('NewAsset/assets/images/favicon.ico')); ?>">
    <link href="<?php echo e(asset('NewAsset/assets/css/bootstrap.min.css')); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo e(asset('NewAsset/assets/css/icons.css')); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo e(asset('NewAsset/assets/css/style.css')); ?>" rel="stylesheet" type="text/css">
</head>
<body class="fixed-left">
    <div class="accountbg"></div>
    <div class="wrapper-page">
        <div class="card">
            <div class="card-body">
                <h3 class="text-center mt-0 m-b-15">
                    <a href="<?php echo e(url('/')); ?>" class="logo logo-admin">
                        OFFICE APP
                        <!-- <img src="<?php echo e(asset('NewAsset/assets/images/logo.png')); ?>" height="24" alt="logo"> -->
                    </a>
                </h3>
                <div class="p-3">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form class="form-horizontal m-t-20" action="<?php echo e(route('login')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus placeholder="Email">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" type="password" name="password" required placeholder="Password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="remember" name="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>
                                    <label class="custom-control-label" for="remember">Remember me</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-center row m-t-20">
                            <div class="col-12">
                                <button class="btn btn-danger btn-block waves-effect waves-light" type="submit">Log In</button>
                            </div>
                        </div>
                        <?php if(Route::has('password.request')): ?>
                        <div class="form-group m-t-10 mb-0 row">
                            <div class="col-sm-12 text-center">
                                <a href="<?php echo e(route('password.request')); ?>" class="text-muted"><i class="mdi mdi-lock"></i> <small>Lupa password?</small></a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo e(asset('NewAsset/assets/js/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/popper.min.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/modernizr.min.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/detect.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/fastclick.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/jquery.slimscroll.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/jquery.blockUI.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/waves.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/jquery.nicescroll.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/jquery.scrollTo.min.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/app.js')); ?>"></script>
</body>
</html>
<?php /**PATH D:\www\kantorapp\resources\views/auth/login.blade.php ENDPATH**/ ?>