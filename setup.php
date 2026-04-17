<?php

/**
 * KantorApp - Simple Setup Checker
 * This script helps you verify that your server is ready for KantorApp.
 */

define('MIN_PHP_VERSION', '8.2.0');
$required_extensions = ['pdo_mysql', 'mbstring', 'xml', 'curl', 'zip', 'gd', 'fileinfo', 'openssl'];

$results = [
    'php' => ['label' => 'PHP Version', 'value' => PHP_VERSION, 'status' => version_compare(PHP_VERSION, MIN_PHP_VERSION, '>=')],
    'extensions' => [],
    'writable' => [
        'storage' => is_writable(__DIR__ . '/storage'),
        'bootstrap/cache' => is_writable(__DIR__ . '/bootstrap/cache'),
        '.env' => file_exists(__DIR__ . '/.env') ? is_writable(__DIR__ . '/.env') : is_writable(__DIR__),
    ],
];

foreach ($required_extensions as $ext) {
    $results['extensions'][$ext] = extension_loaded($ext);
}

// Simple HTML View
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KantorApp | Basic Installer Check</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
    <link rel="shortcut icon" href="favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background: #030015; color: #fff; font-family: 'Inter', sans-serif; padding: 50px; }
        .card { background: rgba(255,255,255,0.05); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.5); }
        .success { color: #10b981; }
        .error { color: #ef4444; }
        .btn-primary { background: linear-gradient(135deg, #06b6d4, #a855f7); border: none; padding: 12px 30px; border-radius: 50rem; font-weight: 700; transition: all 0.3s; }
        .btn-primary:hover { box-shadow: 0 0 20px rgba(6,182,212,0.5); transform: translateY(-2px); }
        .logo-img { width: 32px; height: 32px; border-radius: 8px; object-fit: contain; box-shadow: 0 0 15px rgba(0, 242, 254, 0.4); vertical-align: middle; margin-right: 10px; }
    </style>
</head>
<body>
    <div class="container" style="max-width: 800px;">
        <div class="card p-5">
            <h1 class="mb-2 fw-bold d-flex align-items-center"><img src="favicon.png" alt="Logo" class="logo-img"> <span style="color:#fff">Kantor</span><span style="color:#06b6d4">App</span></h1>
            <p class="text-white-50 mb-5 lead opacity-75">Environmental Requirement Checklist</p>

            <h5 class="mb-3">1. Server Environment</h5>
            <div class="d-flex justify-content-between border-bottom border-white border-opacity-10 py-2">
                <span>PHP Version (min. <?= MIN_PHP_VERSION ?>)</span>
                <span class="<?= $results['php']['status'] ? 'success' : 'error' ?> fw-bold"><?= $results['php']['value'] ?> <?= $results['php']['status'] ? '<i class="mdi mdi-check-circle"></i>' : '<i class="mdi mdi-close-circle"></i>' ?></span>
            </div>

            <h5 class="mt-5 mb-3">2. Required Extensions</h5>
            <div class="row">
                <?php foreach($results['extensions'] as $ext => $loaded): ?>
                <div class="col-md-6 d-flex justify-content-between py-1 px-3">
                    <span class="text-white-50"><?= $ext ?></span>
                    <span class="<?= $loaded ? 'success' : 'error' ?> fw-bold"><?= $loaded ? '<i class="mdi mdi-check"></i>' : '<i class="mdi mdi-close"></i>' ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <h5 class="mt-5 mb-3">3. File Permissions</h5>
            <div class="row">
                <?php foreach($results['writable'] as $path => $writable): ?>
                <div class="col-md-6 d-flex justify-content-between py-1 px-3">
                    <span class="text-white-50"><?= $path ?> (writable)</span>
                    <span class="<?= $writable ? 'success' : 'error' ?> fw-bold"><?= $writable ? '<i class="mdi mdi-check"></i>' : '<i class="mdi mdi-close"></i>' ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-5 text-center">
                <?php if ($results['php']['status'] && !in_array(false, $results['extensions']) && !in_array(false, $results['results']['writable'] ?? $results['writable'])): ?>
                    <div class="alert alert-success bg-success bg-opacity-10 border-success border-opacity-25 mb-4">
                        <i class="mdi mdi-check-decagram me-2"></i> All checks passed! You are ready to install KantorApp.
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger bg-danger bg-opacity-10 border-danger border-opacity-25 mb-4">
                        <i class="mdi mdi-alert-octagon me-2"></i> Some requirements are missing. Please fix them before proceeding.
                    </div>
                <?php endif; ?>
                
                <a href="documentation/index.html" class="btn btn-primary shadow-lg">VIEW FULL DOCUMENTATION</a>
            </div>
        </div>
        
        <p class="text-center mt-4 text-muted small">Once setup is complete, please delete this file (<code>setup.php</code>) for security.</p>
    </div>
</body>
</html>
