<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ServicePro') }} - Service Management System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Main Container */
        .service-dashboard {
            max-width: 1400px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header/Navigation */
        .service-header {
            padding: 20px 40px;
            background: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            transform: rotate(10deg);
            transition: transform 0.3s;
        }

        .logo-icon:hover {
            transform: rotate(0deg);
        }

        .logo-text h1 {
            font-size: 24px;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }

        .logo-text span {
            font-size: 12px;
            color: #666;
            font-weight: 500;
        }

        .nav-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .nav-btn {
            padding: 12px 28px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 2px solid transparent;
        }

        .nav-btn i {
            font-size: 16px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        .btn-outline {
            background: transparent;
            color: #667eea;
            border-color: #667eea;
        }

        .btn-outline:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .btn-dashboard {
            background: #28a745;
            color: white;
        }

        .btn-dashboard:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
        }

        /* Hero/Banner Section */
        .hero-section {
            position: relative;
            height: 400px;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                        url('https://images.unsplash.com/photo-1581091226033-d5c48150dbaa?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .hero-content {
            max-width: 800px;
            padding: 0 20px;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-content h2 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-content p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
            line-height: 1.6;
        }

        .hero-stats {
            display: flex;
            gap: 40px;
            justify-content: center;
            margin-top: 40px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 36px;
            font-weight: 800;
            display: block;
            line-height: 1;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Main Content */
        .main-content {
            padding: 60px 40px;
            background: #f8fafd;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h3 {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .section-title p {
            color: #666;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Services Grid */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .service-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transform: scaleX(0);
            transition: transform 0.3s;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .service-card:hover::before {
            transform: scaleX(1);
        }

        .service-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: white;
            font-size: 32px;
            transform: rotate(0deg);
            transition: all 0.3s;
        }

        .service-card:hover .service-icon {
            transform: rotate(360deg);
        }

        .service-card h4 {
            font-size: 20px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .service-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .service-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: gap 0.3s;
        }

        .service-link:hover {
            gap: 10px;
        }

        /* Features Section */
        .features-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .feature-item {
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
            font-size: 20px;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .feature-text h5 {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .feature-text p {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            padding: 60px;
            text-align: center;
            color: white;
            margin-top: 60px;
        }

        .cta-section h3 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .cta-section p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .cta-btn {
            padding: 15px 40px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .cta-btn-primary {
            background: white;
            color: #667eea;
        }

        .cta-btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.3);
        }

        .cta-btn-outline {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .cta-btn-outline:hover {
            background: white;
            color: #667eea;
            transform: translateY(-3px);
        }

        /* Footer */
        .service-footer {
            background: white;
            padding: 40px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-col h6 {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 10px;
        }

        .footer-col ul li a {
            color: #666;
            text-decoration: none;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-col ul li a:hover {
            color: #667eea;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            margin-top: 30px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            color: #666;
            font-size: 14px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .service-header {
                padding: 20px;
            }

            .hero-content h2 {
                font-size: 36px;
            }

            .hero-stats {
                flex-direction: column;
                gap: 20px;
            }

            .main-content {
                padding: 40px 20px;
            }

            .cta-section {
                padding: 40px 20px;
            }

            .cta-section h3 {
                font-size: 28px;
            }
        }

        /* Auto-refresh indicator */
        .refresh-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            padding: 10px 20px;
            border-radius: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            z-index: 1000;
        }

        .refresh-progress {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 3px solid #f0f0f0;
            border-top-color: #667eea;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="service-dashboard">
        <!-- Header -->
        <header class="service-header">
            <div class="logo-area">
                <div class="logo-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="logo-text">
                    <h1>Sharpedge</h1>
                    <span>Enterprise Service Management</span>
                </div>
            </div>

            <nav class="nav-buttons">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="nav-btn btn-dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="nav-btn btn-outline">
                            <i class="fas fa-sign-in-alt"></i>
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="nav-btn btn-primary">
                                <i class="fas fa-user-plus"></i>
                                Register
                            </a>
                        @endif
                    @endauth
                @endif
                <a href="#contact" class="nav-btn btn-outline">
                    <i class="fas fa-headset"></i>
                    Contact
                </a>
            </nav>
        </header>

        <!-- Hero/Banner Section -->
        <section class="hero-section">
            <div class="hero-content">
                <h2>Streamline Your Service Operations</h2>
                <p>Complete service management solution for modern businesses. Automate workflows, track performance, and deliver exceptional service.</p>
                
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">10K+</span>
                        <span class="stat-label">Active Users</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">50K+</span>
                        <span class="stat-label">Services Completed</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">99.9%</span>
                        <span class="stat-label">Uptime</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <main class="main-content">
            <div class="section-title">
                <h3>Our Services</h3>
                <p>Comprehensive service management solutions tailored to your business needs</p>
            </div>

            <!-- Services Grid -->
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4>Help Desk</h4>
                    <p>Centralized ticket management system with automated routing and priority handling</p>
                    <a href="#" class="service-link">
                        Learn More <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h4>Field Service</h4>
                    <p>Schedule and dispatch field technicians with real-time tracking and mobile app</p>
                    <a href="#" class="service-link">
                        Learn More <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4>Asset Management</h4>
                    <p>Track and manage all your assets with maintenance schedules and depreciation</p>
                    <a href="#" class="service-link">
                        Learn More <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <h4>Contract Management</h4>
                    <p>Manage SLAs, warranties, and service contracts with automated renewals</p>
                    <a href="#" class="service-link">
                        Learn More <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Features Section -->
            <div class="features-section">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="feature-text">
                        <h5>Real-time Updates</h5>
                        <p>Live tracking of service requests and technician locations</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="feature-text">
                        <h5>Mobile Ready</h5>
                        <p>Fully responsive design with dedicated mobile apps</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="feature-text">
                        <h5>Enterprise Security</h5>
                        <p>Bank-level encryption and compliance standards</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="feature-text">
                        <h5>Advanced Analytics</h5>
                        <p>Comprehensive reports and performance metrics</p>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="cta-section">
                <h3>Ready to Transform Your Service Management?</h3>
                <p>Join thousands of businesses that trust ServicePro for their service operations</p>
                <div class="cta-buttons">
                    <a href="{{ route('register') }}" class="cta-btn cta-btn-primary">
                        <i class="fas fa-rocket"></i>
                        Get Started Free
                    </a>
                    <a href="#" class="cta-btn cta-btn-outline">
                        <i class="fas fa-calendar-alt"></i>
                        Schedule Demo
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="service-footer">
            <div class="footer-content">
                <div class="footer-col">
                    <h6>Company</h6>
                    <ul>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> About Us</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Careers</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Press</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Blog</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h6>Solutions</h6>
                    <ul>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Help Desk</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Field Service</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Asset Management</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Contract Management</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h6>Support</h6>
                    <ul>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Documentation</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> API Reference</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Tutorials</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> FAQ</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h6>Connect</h6>
                    <ul>
                        <li><a href="#"><i class="fab fa-twitter"></i> Twitter</a></li>
                        <li><a href="#"><i class="fab fa-linkedin"></i> LinkedIn</a></li>
                        <li><a href="#"><i class="fab fa-facebook"></i> Facebook</a></li>
                        <li><a href="#"><i class="fab fa-youtube"></i> YouTube</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} ServicePro. All rights reserved. | Enterprise Service Management System</p>
            </div>
        </footer>
    </div>

    <!-- Auto-refresh indicator (optional) -->
    <div class="refresh-indicator">
        <div class="refresh-progress"></div>
        <span>Auto-refresh in <span id="refresh-counter">30</span>s</span>
    </div>

    <!-- Auto-refresh script -->
    <script>
        // Auto-refresh functionality
        let refreshTime = 30; // seconds
        const counterElement = document.getElementById('refresh-counter');
        
        // Update counter every second
        const countdown = setInterval(() => {
            refreshTime--;
            if (counterElement) {
                counterElement.textContent = refreshTime;
            }
            
            if (refreshTime <= 0) {
                clearInterval(countdown);
                location.reload();
            }
        }, 1000);

        // Optional: Stop refresh on user interaction
        document.addEventListener('click', () => {
            // Uncomment to stop auto-refresh on user click
            // clearInterval(countdown);
        });
    </script>
</body>
</html>