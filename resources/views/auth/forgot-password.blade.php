<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="SharpEdge India - Password Reset">
    <meta name="author" content="">
    <title>SharpEdge India | Reset Password</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-black: #0a0a0a;
            --dark-gray: #121212;
            --medium-gray: #2a2a2a;
            --light-gray: #3a3a3a;
            --text-light: #e0e0e0;
            --text-lighter: #ffffff;
            --accent-blue: #4a6fa5;
            --accent-steel: #a0b3d6;
            --success-green: #1cc88a;
            --warning-yellow: #f6c23e;
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
        
        .password-card {
            background: var(--dark-gray);
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            border: 1px solid var(--medium-gray);
            position: relative;
            overflow: hidden;
            animation: cardEntrance 0.8s ease-out;
        }
        
        /* Premium accent line */
        .password-card::before {
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
            margin-bottom: 25px;
            position: relative;
        }
        
        .logo-image-container {
            width: 100%;
            max-width: 300px;
            margin: 0 auto 15px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 90px;
        }
        
        .logo-img {
            max-width: 100%;
            max-height: 70px;
            object-fit: contain;
        }
        
        .logo-text-fallback {
            display: none;
            flex-direction: column;
            align-items: center;
        }
        
        .logo-main-text {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-lighter);
            margin-bottom: 5px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        
        .logo-subtext {
            color: var(--accent-steel);
            font-size: 14px;
            font-style: italic;
            margin-bottom: 5px;
        }
        
        .system-title {
            color: var(--text-light);
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 1px;
            position: relative;
            display: inline-block;
            padding-bottom: 10px;
            margin-top: 5px;
        }
        
        .system-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--accent-blue), transparent);
        }
        
        .page-title {
            color: var(--text-lighter);
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 25px;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }
        
        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, var(--accent-blue), var(--accent-steel));
            border-radius: 3px;
        }
        
        .description {
            color: var(--text-light);
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 30px;
            text-align: center;
            opacity: 0.9;
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
            padding: 14px 18px;
            font-size: 16px;
            transition: all 0.3s ease;
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
        
        .btn-reset {
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
            margin-top: 10px;
        }
        
        .btn-reset:hover {
            background-color: #3a5a8c;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(74, 111, 165, 0.3);
        }
        
        .btn-reset:active {
            transform: translateY(0);
        }
        
        .btn-back {
            background-color: var(--medium-gray);
            border: 1px solid var(--light-gray);
            color: var(--text-light);
            padding: 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .btn-back:hover {
            background-color: var(--light-gray);
            color: var(--text-lighter);
            text-decoration: none;
        }
        
        .btn-back i {
            margin-right: 8px;
        }
        
        .alert-success {
            background-color: rgba(28, 200, 138, 0.1);
            border: 1px solid rgba(28, 200, 138, 0.3);
            color: #d4edda;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            animation: slideDown 0.5s ease;
        }
        
        .alert-success i {
            color: var(--success-green);
            margin-right: 10px;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #f8d7da;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            animation: slideDown 0.5s ease;
        }
        
        .alert-danger i {
            color: #dc3545;
            margin-right: 10px;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .copyright {
            color: #888888;
            font-size: 14px;
            letter-spacing: 0.4px;
            margin-top: 35px;
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
        
        /* Form elements animation */
        .description, .form-group, .alert, .btn-reset, .btn-back {
            opacity: 0;
            transform: translateY(20px);
            animation: slideUp 0.5s ease forwards;
        }
        
        .description { animation-delay: 0.1s; }
        .alert { animation-delay: 0.2s; }
        .form-group { animation-delay: 0.3s; }
        .btn-reset { animation-delay: 0.4s; }
        .btn-back { animation-delay: 0.5s; }
        
        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive design */
        @media (max-width: 576px) {
            .password-card {
                padding: 30px 25px;
                max-width: 100%;
            }
            
            .logo-image-container {
                max-width: 250px;
                padding: 12px;
                min-height: 80px;
            }
            
            .logo-img {
                max-height: 60px;
            }
            
            .logo-main-text {
                font-size: 20px;
            }
            
            .logo-subtext {
                font-size: 13px;
            }
            
            .system-title {
                font-size: 16px;
            }
            
            .page-title {
                font-size: 20px;
            }
            
            .description {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="password-card">
        <div class="logo">
            <!-- SharpEdge Logo -->
            <div class="logo-image-container">
                <img src="https://sharpedge.xinksoft.site/public/sharpedge_logo/Sharpedge.jpeg" 
                     alt="SharpEdge India Logo" 
                     class="logo-img"
                     onerror="this.style.display='none'; document.querySelector('.logo-text-fallback').style.display='flex';">
                
                <!-- Fallback text logo if image fails to load -->
                <div class="logo-text-fallback">
                    <div class="logo-main-text">THE SHARP EDGE INDIA</div>
                    <div class="logo-subtext">Success for your business...</div>
                </div>
            </div>
            
            <div class="system-title">Service Management System</div>
        </div>

        <h1 class="page-title">Reset Your Password</h1>
        
        <div class="description">
            Forgot your password? No problem. Just enter your email address below and we'll email you a password reset link that will allow you to choose a new one.
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle"></i>
                {{ session('status') }}
            </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach ($errors->all() as $error)
                    <div><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" id="resetForm">
            @csrf

            <div class="form-group mb-4">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i>Email Address
                </label>
                <div class="input-group">
                    <input type="email" class="form-control" id="email" name="email" 
                           value="{{ old('email') }}" required autofocus 
                           placeholder="Enter your registered email address">
                    <span class="input-group-text">
                        <i class="fas fa-user"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn btn-reset">
                <i class="fas fa-paper-plane me-2"></i> Email Password Reset Link
            </button>

            <a href="{{ route('login') }}" class="btn btn-back">
                <i class="fas fa-arrow-left me-2"></i> Back to Login
            </a>

            <div class="copyright">
                <small>© 2025 The Sharp Edge India. All rights reserved.</small><br>
                <small>Visit us at <a href="https://www.thesharpedgeindia.com" target="_blank">www.thesharpedgeindia.com</a></small>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form submission with loading state
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending Reset Link...';
            submitBtn.disabled = true;
            
            // Simulate network delay for demo (remove in production)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        });
        
        // Image fallback handling
        const logoImg = document.querySelector('.logo-img');
        if (logoImg) {
            logoImg.addEventListener('error', function() {
                this.style.display = 'none';
                document.querySelector('.logo-text-fallback').style.display = 'flex';
            });
            
            // Initial opacity for fade in
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
                const animatedElements = document.querySelectorAll('.description, .form-group, .alert, .btn-reset, .btn-back');
                animatedElements.forEach((el, index) => {
                    el.style.animationDelay = (index * 0.1) + 's';
                });
            }, 300);
        });
        
        // Success message animation if present
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            successAlert.addEventListener('click', function() {
                this.style.opacity = '0';
                this.style.transform = 'translateY(-10px)';
                this.style.transition = 'all 0.5s ease';
                setTimeout(() => {
                    this.style.display = 'none';
                }, 500);
            });
            
            // Auto-hide after 8 seconds
            setTimeout(() => {
                if (successAlert) {
                    successAlert.style.opacity = '0';
                    successAlert.style.transform = 'translateY(-10px)';
                    successAlert.style.transition = 'all 0.5s ease';
                    setTimeout(() => {
                        successAlert.style.display = 'none';
                    }, 500);
                }
            }, 8000);
        }
    </script>
</body>
</html>