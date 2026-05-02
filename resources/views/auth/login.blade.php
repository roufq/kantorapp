<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KantorApp | Sign In</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/7.4.47/css/materialdesignicons.min.css">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('kantorapp/css/app-modern.css') }}?v={{ time() }}">
</head>
<body class="login-body">

    <div class="login-card-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" style="width: 42px; height: 42px; border-radius: 10px;">
                    KantorApp
                </div>
                <p class="auth-subtitle">Welcome back! Please enter your details.</p>
            </div>

            @if($errors->any())
                <div class="alert-auth-error">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="auth-form-group">
                    <label class="auth-label">Email Address</label>
                    <input type="email" name="email" class="auth-input" value="{{ old('email') }}" placeholder="name@company.com" required autofocus>
                </div>

                <div class="auth-form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <label class="auth-label" style="margin-bottom: 0;">Password</label>
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}" style="font-size: 12px; color: var(--primary); text-decoration: none; font-weight: 600;">Forgot?</a>
                        @endif
                    </div>
                    <input type="password" name="password" class="auth-input" placeholder="••••••••" required>
                </div>

                <button type="submit" class="auth-btn-submit">Sign In Account</button>
            </form>

            <div class="auth-social-login">
                <div class="auth-social-title">Secure Portal</div>
                <button class="auth-btn-google" type="button">
                    <svg width="20" height="20" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.21c.81.63 1.83 1.01 2.81 1.01V12h3.42z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.66l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    Continue with Google
                </button>
            </div>
            
            <p style="text-align: center; margin-top: 30px; font-size: 13px; color: #64748b; font-weight: 500;">
                Trouble logging in? <a href="#" style="color: var(--primary); text-decoration: none; font-weight: 700;">Contact IT Support</a>
            </p>
        </div>
    </div>

</body>
</html>
