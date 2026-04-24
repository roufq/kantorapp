<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KantorApp | Sign In</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/7.4.47/css/materialdesignicons.min.css">
    <link rel="shortcut icon" href="<?php echo e(asset('favicon.png')); ?>?v=<?php echo e(time()); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('apple-touch-icon.png')); ?>?v=<?php echo e(time()); ?>">
    
    <style>
        :root {
            --bg-color: #f0fff4; /* Soft Mint */
            --card-bg: #ffffff;
            --card-border: #f1f5f9;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --primary: #10b981;
            --primary-hover: #059669;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f0fff4 0%, #ebf8ff 100%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card-container {
            width: 100%;
            max-width: 480px;
        }

        .card {
            background: var(--card-bg);
            border: none;
            border-radius: 24px;
            padding: 50px 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #10b981, #0ea5e9);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-text {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -1px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .logo-text span {
            color: var(--primary);
        }

        .subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 8px;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            background: #f8fafc;
            border: 2px solid #f1f5f9;
            color: var(--text-main);
            padding: 14px 18px;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.1);
        }

        .btn-submit {
            width: 100%;
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 16px;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(14, 165, 233, 0.2);
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(14, 165, 233, 0.3);
        }

        .extra-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            font-size: 13px;
        }

        .extra-links a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .social-login {
            margin-top: 40px;
            text-align: center;
        }

        .social-title {
            position: relative;
            font-size: 12px;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 25px;
            text-transform: uppercase;
        }

        .social-title::before, .social-title::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 30%;
            height: 1px;
            background: #e2e8f0;
        }

        .social-title::before { left: 0; }
        .social-title::after { right: 0; }

        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: #fff;
            border: 2px solid #f1f5f9;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
        }

        .btn-google:hover {
            background: #f8fafc;
            border-color: #e2e8f0;
        }

        .alert-error {
            background: #fff1f2;
            color: #e11d48;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid #ffe4e6;
        }

        .alert-error ul { list-style: none; padding: 0; }
    </style>
</head>
<body>

    <div class="card-container">
        <div class="card">
            <div class="header">
                <div class="logo-text">
                    <img src="<?php echo e(asset('assets/img/logo.png')); ?>" alt="Logo" style="width: 42px; height: 42px; border-radius: 10px;">
                    KantorApp
                </div>
                <p class="subtitle">Welcome back! Please enter your details.</p>
            </div>

            <?php if($errors->any()): ?>
                <div class="alert-error">
                    <ul>
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('login')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>" placeholder="name@company.com" required autofocus>
                </div>

                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <label class="form-label" style="margin-bottom: 0;">Password</label>
                        <?php if(Route::has('password.request')): ?>
                            <a href="<?php echo e(route('password.request')); ?>" style="font-size: 12px; color: var(--primary); text-decoration: none; font-weight: 600;">Forgot?</a>
                        <?php endif; ?>
                    </div>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-submit">Sign In Account</button>
            </form>

            <div class="social-login">
                <div class="social-title">Secure Portal</div>
                <button class="btn-google">
                    <svg width="20" height="20" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.21c.81.63 1.83 1.01 2.81 1.01V12h3.42z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.66l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    Continue with Google
                </button>
            </div>
            
            <p style="text-align: center; margin-top: 30px; font-size: 13px; color: var(--text-muted); font-weight: 500;">
                Trouble logging in? <a href="#" style="color: var(--primary); text-decoration: none; font-weight: 700;">Contact IT Support</a>
            </p>
        </div>
    </div>

</body>
</html>
<?php /**PATH D:\www\kantorapp\resources\views/auth/login.blade.php ENDPATH**/ ?>