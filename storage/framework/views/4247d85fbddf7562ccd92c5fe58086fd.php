<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Two-Factor Authentication Code</title>
</head>
<body>
    <h1>Your Two-Factor Authentication Code</h1>

    <p>Hello,</p>

    <p>Your verification code for two-factor authentication is:</p>

    <h2 style="font-size: 24px; font-weight: bold; color: #333; text-align: center; padding: 20px; border: 2px solid #007bff; border-radius: 5px;">
        <?php echo e($code); ?>

    </h2>

    <p>This code will expire in 10 minutes.</p>

    <p>If you didn't request this code, please ignore this email.</p>

    <p>Best regards,<br>
    <?php echo e(config('app.name')); ?> Team</p>
</body>
</html>
<?php /**PATH D:\www\kantorapp\resources\views/emails/two-factor-code.blade.php ENDPATH**/ ?>