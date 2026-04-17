<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Area Restricted | KantorApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #030015;
            color: #fff;
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }
        .container-404 {
            text-align: center;
            position: relative;
            z-index: 2;
        }
        .glitch-text {
            font-size: 8rem;
            font-weight: 900;
            margin: 0;
            letter-spacing: -5px;
            background: linear-gradient(90deg, #06b6d4, #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: glitch 1.5s infinite linear alternate-reverse;
        }
        @keyframes glitch {
            0% { text-shadow: 2px 2px #ec4899; }
            50% { text-shadow: -2px -2px #06b6d4; }
            100% { text-shadow: 2px -2px #a855f7; }
        }
        .btn-home {
            background: #f1f5f9;
            backdrop-filter: blur(10px);
            border: 1px solid #e2e8f0;
            color: #fff;
            padding: 12px 30px;
            border-radius: 50rem;
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 600;
            margin-top: 2rem;
            display: inline-block;
        }
        .btn-home:hover {
            background: rgba(6, 182, 212, 0.2);
            border-color: #06b6d4;
            color: #fff;
            transform: translateY(-3px);
        }
        .ambient-light { position: absolute; border-radius: 50%; filter: blur(100px); pointer-events: none; z-index: 1; }
        .light-left { background: rgba(168, 85, 247, 0.3); width: 30vw; height: 30vw; top: -10vw; left: -10vw; }
        .light-right { background: rgba(6, 182, 212, 0.2); width: 25vw; height: 25vw; bottom: -5vw; right: -5vw; }
    </style>
</head>
<body>
    <div class="ambient-light light-left"></div>
    <div class="ambient-light light-right"></div>
    
    <div class="container-404">
        <h1 class="glitch-text">404</h1>
        <h3 class="fw-bold mb-3">ACCESS DENIED</h3>
        <p class="text-muted">The system could not locate the requested coordinates.<br>You might have drifted outside the geofence.</p>
        <a href="/" class="btn-home">RETURN TO TERMINAL</a>
    </div>
</body>
</html>
<?php /**PATH D:\www\kantorapp\resources\views/errors/404.blade.php ENDPATH**/ ?>