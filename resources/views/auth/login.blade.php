<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="SharpEdge India - Premium Service Management System">
    <meta name="author" content="">
    
    <!-- PWA Manifest and Icons - FIXED PATHS -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#000000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SharpEdge">
    
    <!-- Apple Touch Icons - Using FULL URL with proper sizes -->
    <link rel="apple-touch-icon" href="/storage/sharpedge.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/storage/sharpedge.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/storage/sharpedge.png">
    <link rel="apple-touch-icon" sizes="167x167" href="/storage/sharpedge.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/storage/sharpedge.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/storage/sharpedge.png">
    
    <!-- Microsoft Tiles -->
    <meta name="msapplication-TileColor" content="#000000">
    <meta name="msapplication-TileImage" content="/storage/sharpedge.png">
    
    <!-- Safari Pinned Tab (optional - needs monochrome SVG) -->
    <!-- <link rel="mask-icon" href="/icons/safari-pinned-tab.svg" color="#4a6fa5"> -->
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="/storage/sharpedge.png" type="image/png">
    
    <title>SharpEdge India | Service Management System - Login</title>
        
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-black: #000000;
            --dark-gray: #121212;
            --medium-gray: #2a2a2a;
            --light-gray: #3a3a3a;
            --text-light: #e0e0e0;
            --text-lighter: #ffffff;
            --accent-blue: #4a6fa5;
            --accent-steel: #a0b3d6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background-color: var(--primary-black);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        
        /* Subtle background pattern */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(74, 111, 165, 0.03) 0%, transparent 20%),
                radial-gradient(circle at 80% 20%, rgba(74, 111, 165, 0.03) 0%, transparent 20%);
            z-index: -1;
        }
        
        .login-card {
            background: var(--dark-gray);
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            border: 1px solid var(--medium-gray);
            position: relative;
            overflow: hidden;
            animation: cardEntrance 0.8s ease-out;
        }
        
        /* Premium accent line */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-blue), var(--accent-steel));
            z-index: 10;
        }
        
        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        
        /* LARGER LOGO CONTAINER */
        .logo-image-container {
            width: 100%;
            max-width: 350px;
            margin: 0 auto 20px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 120px;
        }
        
        /* FIXED LOGO PATH - Using the correct storage path */
        .logo-img {
            max-width: 100%;
            max-height: 100px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        
        .logo-img:hover {
            transform: scale(1.05);
        }
        
        .logo-text-fallback {
            display: none;
            flex-direction: column;
            align-items: center;
        }
        
        /* Larger fallback text */
        .logo-main-text {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-lighter);
            margin-bottom: 8px;
            letter-spacing: 1.8px;
            text-transform: uppercase;
        }
        
        .logo-subtext {
            color: var(--accent-steel);
            font-size: 16px;
            font-style: italic;
            margin-bottom: 5px;
        }
        
        .system-title {
            color: var(--text-light);
            font-size: 20px;
            font-weight: 600;
            letter-spacing: 1px;
            position: relative;
            display: inline-block;
            padding-bottom: 10px;
            margin-top: 10px;
        }
        
        .system-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 70px;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--accent-blue), transparent);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--text-light);
            margin-bottom: 10px;
            font-size: 15px;
            letter-spacing: 0.4px;
            display: flex;
            align-items: center;
        }
        
        .form-label i {
            margin-right: 10px;
            color: var(--accent-steel);
            width: 20px;
            text-align: center;
            font-size: 16px;
        }
        
        .form-control {
            background-color: #1a1a1a;
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            color: var(--text-lighter);
            padding: 16px 20px;
            font-size: 16px;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
        }
        
        .form-control::placeholder {
            color: #777;
            font-size: 15px;
        }
        
        .form-control:focus {
            background-color: #1a1a1a;
            border-color: var(--accent-blue);
            color: var(--text-lighter);
            box-shadow: 0 0 0 3px rgba(74, 111, 165, 0.2);
            outline: none;
        }
        
        .input-group-text {
            background-color: #1a1a1a;
            border: 1px solid var(--light-gray);
            border-right: none;
            color: var(--accent-steel);
            transition: all 0.3s ease;
            font-size: 16px;
        }
        
        .form-control:focus + .input-group-text,
        .form-control:focus ~ .input-group-text {
            border-color: var(--accent-blue);
            color: var(--text-lighter);
        }
        
        .btn-login {
            background-color: var(--accent-blue);
            border: none;
            color: var(--text-lighter);
            padding: 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 17px;
            transition: all 0.3s ease;
            letter-spacing: 0.6px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .btn-login:hover {
            background-color: #3a5a8c;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(74, 111, 165, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .form-check-input {
            background-color: #1a1a1a;
            border: 1px solid var(--light-gray);
            cursor: pointer;
            width: 18px;
            height: 18px;
        }
        
        .form-check-input:checked {
            background-color: var(--accent-blue);
            border-color: var(--accent-blue);
        }
        
        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(74, 111, 165, 0.15);
        }
        
        .form-check-label {
            color: var(--text-light);
            font-size: 15px;
            cursor: pointer;
            user-select: none;
            margin-left: 8px;
        }
        
        .forgot-password-link {
            color: var(--accent-steel);
            text-decoration: none;
            font-size: 15px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .forgot-password-link:hover {
            color: var(--text-lighter);
            text-decoration: underline;
        }
        
        .role-badges {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 25px;
            flex-wrap: wrap;
        }
        
        .role-badge {
            padding: 10px 24px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.4px;
            border: 1px solid transparent;
            min-width: 110px;
            text-align: center;
        }
        
        .role-badge:hover {
            transform: translateY(-2px);
        }
        
        .role-admin {
            background-color: var(--medium-gray);
            color: #b0b0b0;
            border-color: var(--light-gray);
        }
        
        .role-admin.active, .role-admin:hover {
            background-color: var(--accent-blue);
            color: var(--text-lighter);
            border-color: var(--accent-blue);
        }
        
        .role-backoffice {
            background-color: var(--medium-gray);
            color: #b0b0b0;
            border-color: var(--light-gray);
        }
        
        .role-backoffice.active, .role-backoffice:hover {
            background-color: #1cc88a;
            color: white;
            border-color: #1cc88a;
        }
        
        .role-marketing {
            background-color: var(--medium-gray);
            color: #b0b0b0;
            border-color: var(--light-gray);
        }
        
        .role-marketing.active, .role-marketing:hover {
            background-color: #36b9cc;
            color: white;
            border-color: #36b9cc;
        }
        
        .role-client {
            background-color: var(--medium-gray);
            color: #b0b0b0;
            border-color: var(--light-gray);
        }
        
        .role-client.active, .role-client:hover {
            background-color: #f6c23e;
            color: #212529;
            border-color: #f6c23e;
        }
        
        .copyright {
            color: #888888;
            font-size: 14px;
            letter-spacing: 0.4px;
            margin-top: 30px;
            text-align: center;
            line-height: 1.6;
        }
        
        .copyright a {
            color: var(--accent-steel);
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: 500;
        }
        
        .copyright a:hover {
            color: var(--text-lighter);
            text-decoration: underline;
        }
        
        /* PWA Install Button */
        .install-pwa-btn {
            background: linear-gradient(135deg, var(--accent-blue), #3a5a8c);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 600;
            display: none;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(74, 111, 165, 0.3);
            z-index: 1000;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            width: fit-content;
            margin: 20px auto 0;
        }
        
        .install-pwa-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(74, 111, 165, 0.5);
        }
        
        .install-pwa-btn i {
            font-size: 18px;
        }
        
        /* PWA Splash Screen Animation */
        @keyframes splashPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Focus animation */
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(74, 111, 165, 0.3); }
            70% { box-shadow: 0 0 0 8px rgba(74, 111, 165, 0); }
            100% { box-shadow: 0 0 0 0 rgba(74, 111, 165, 0); }
        }
        
        .form-control:focus {
            animation: pulse 2s infinite;
        }
        
        /* Responsive design */
        @media (max-width: 576px) {
            .login-card {
                padding: 30px 25px;
                max-width: 100%;
            }
            
            .logo-image-container {
                max-width: 280px;
                padding: 18px;
                min-height: 100px;
            }
            
            .logo-img {
                max-height: 80px;
            }
            
            .logo-main-text {
                font-size: 24px;
            }
            
            .logo-subtext {
                font-size: 15px;
            }
            
            .system-title {
                font-size: 18px;
            }
            
            .role-badges {
                gap: 10px;
            }
            
            .role-badge {
                padding: 8px 18px;
                font-size: 12px;
                min-width: 95px;
            }
            
            .install-pwa-btn {
                padding: 10px 18px;
                font-size: 13px;
                width: 100%;
                justify-content: center;
            }
        }
        
        /* Form elements animation */
        .form-group, .form-check, .role-badges-container {
            opacity: 0;
            transform: translateY(20px);
            animation: slideUp 0.5s ease forwards;
        }
        
        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-check { animation-delay: 0.3s; }
        .btn-login { animation-delay: 0.4s; }
        .role-badges-container { animation-delay: 0.5s; }
        
        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Button container for better spacing */
        .button-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="logo">
            <!-- SharpEdge Logo with correct storage path -->
            <div class="logo-image-container">
                <img src="/storage/sharpedge.png" 
                     alt="SharpEdge India Logo" 
                     class="logo-img"
                     onerror="this.style.display='none'; document.querySelector('.logo-text-fallback').style.display='flex';">
                
                <!-- Fallback text logo if image fails to load -->
                <div class="logo-text-fallback">
                    <div class="logo-main-text">THE SHARP EDGE INDIA</div>
                    <div class="logo-subtext">Success for your business...</div>
                </div>
            </div>
            
            <div class="system-title">Sharpedge</div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach ($errors->all() as $error)
                    <div><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            <div class="form-group mb-4">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i>Email Address
                </label>
                <div class="input-group">
                    <input type="email" class="form-control" id="email" name="email" 
                           value="{{ old('email') }}" required autofocus 
                           placeholder="Enter your email address">
                    <span class="input-group-text">
                        <i class="fas fa-user"></i>
                    </span>
                </div>
            </div>

            <div class="form-group mb-4">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i>Password
                </label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" 
                           required placeholder="Enter your password">
                    <span class="input-group-text" id="passwordToggle" style="cursor: pointer;">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember Me</label>
                </div>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="forgot-password-link">
                        Forgot Password?
                    </a>
                @endif
            </div>

            <div class="button-container">
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i> Sign In
                </button>

                <!-- PWA Install Button -->
                <button type="button" class="install-pwa-btn" id="installPWAButton">
                    <i class="fas fa-download"></i>
                    <span>Install SharpEdge App</span>   
                </button>
            </div>

            <div class="copyright">
                <small>© 2025 The Sharp Edge India. All rights reserved.</small><br>
                <small>Visit us at <a href="https://www.thesharpedgeindia.com" target="_blank">www.thesharpedgeindia.com</a></small>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Show/hide password toggle
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordInput = document.getElementById('password');
        
        if (passwordToggle) {
            passwordToggle.addEventListener('click', function() {
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                    this.title = 'Hide Password';
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                    this.title = 'Show Password';
                }
            });
        }
        
        // Role badges selection
        const roleBadges = document.querySelectorAll('.role-badge');
        
        roleBadges.forEach(badge => {
            badge.addEventListener('click', function() {
                roleBadges.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const selectedRole = this.getAttribute('data-role');
                console.log('Selected role:', selectedRole);
            });
        });
        
        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Signing In...';
            submitBtn.disabled = true;
            
            const selectedRole = document.querySelector('.role-badge.active')?.getAttribute('data-role') || 'backoffice';
            console.log('Form submission with role:', selectedRole);
        });
        
        // Image fallback handling
        const logoImg = document.querySelector('.logo-img');
        if (logoImg) {
            logoImg.addEventListener('error', function() {
                this.style.display = 'none';
                document.querySelector('.logo-text-fallback').style.display = 'flex';
            });
            
            logoImg.addEventListener('load', function() {
                this.style.opacity = '1';
            });
            
            logoImg.style.opacity = '0';
            logoImg.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                if (logoImg.complete) {
                    logoImg.style.opacity = '1';
                }
            }, 100);
        }
        
        // Auto-focus email field
        document.getElementById('email').focus();
        
        // Trigger animations on page load
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const animatedElements = document.querySelectorAll('.form-group, .form-check, .role-badges-container');
                animatedElements.forEach((el, index) => {
                    el.style.animationDelay = (index * 0.1) + 's';
                });
            }, 100);
        });

        // ===== PWA INSTALLATION FUNCTIONALITY - FIXED =====
        let deferredPrompt;
        const installButton = document.getElementById('installPWAButton');
        
        // Check if app is already installed
        function isAppInstalled() {
            return window.matchMedia('(display-mode: standalone)').matches || 
                   window.navigator.standalone === true;
        }

        // Show install button when PWA is available
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            if (!isAppInstalled() && installButton) {
                installButton.style.display = 'flex';
                installButton.style.animation = 'splashPulse 2s infinite';
                console.log('PWA install prompt available');
            }
        });

        // Handle install button click
        if (installButton) {
            installButton.addEventListener('click', async () => {
                if (!deferredPrompt) {
                    alert('To install this app:\n\n' +
                          'Chrome/Edge: Click ⋮ menu → "Install SharpEdge"\n' +
                          'Safari: Share menu → "Add to Home Screen"');
                    return;
                }
                
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                console.log(`Install prompt outcome: ${outcome}`);
                
                deferredPrompt = null;
                installButton.style.display = 'none';
            });
        }

        // Listen for app installed event
        window.addEventListener('appinstalled', (evt) => {
            console.log('SharpEdge App installed successfully');
            if (installButton) {
                installButton.style.display = 'none';
            }
            
            // Show success message
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: #10b981;
                color: white;
                padding: 15px 25px;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 500;
                box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
                z-index: 1000;
                animation: slideIn 0.3s ease-out;
            `;
            toast.innerHTML = '<i class="fas fa-check-circle me-2"></i> SharpEdge App installed successfully!';
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.1s ease-out';
                setTimeout(() => toast.remove(), 100);
            }, 1000);
        });
        
        // Hide install button if app is already installed
        if (isAppInstalled() && installButton) {
            console.log('App running in standalone mode');
            installButton.style.display = 'none';
        }

        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(reg => {
                        console.log('ServiceWorker registered:', reg.scope);
                        
                        // Check for updates
                        reg.addEventListener('updatefound', () => {
                            console.log('New service worker found');
                        });
                    })
                    .catch(err => {
                        console.log('ServiceWorker registration failed:', err);
                    });
            });
        }

        // Test image accessibility
        fetch('/storage/sharpedge.png')
            .then(response => {
                if (response.ok) {
                    console.log('Logo image is accessible');
                } else {
                    console.warn('Logo image might not be accessible:', response.status);
                }
            })
            .catch(error => {
                console.error('Error accessing logo:', error);
            });
    </script>  

    <style>
        @keyframes slideOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(20px); }
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</body>
</html>