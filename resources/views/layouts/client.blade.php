<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    

    <title>@yield('title', 'SharpEdge - Client Dashboard')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <style>
        :root {
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --sidebar-active: #3b82f6;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            --primary-color: #3b82f6;
            --sharpedge-blue: #1d4ed8;
            --sharpedge-light: #60a5fa;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        /* SharpEdge Logo */
        .sharpedge-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            color: #fff;
            text-decoration: none;
        }
        .logo-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--sharpedge-blue), var(--sharpedge-light));
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 18px;
            color: white;
        }
        .logo-text {
            font-size: 22px;
            letter-spacing: 0.5px;
        }
        .logo-text span {
            color: var(--sharpedge-light);
        }
        
        /* Sidebar */
        .sidebar {
            background-color: var(--sidebar-bg);
            color: #fff;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 100;
            padding: 0;
            transition: all 0.3s;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar .logo-container {
            padding: 20px;
            border-bottom: 1px solid #334155;
            margin-bottom: 20px;
        }
        .sidebar .nav-item {
            margin: 5px 10px;
        }
        .sidebar .nav-link {
            color: #cbd5e1;
            padding: 12px 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: #fff;
        }
        .sidebar .nav-link.active {
            background-color: var(--sidebar-hover);
            color: #fff;
            border-left: 4px solid var(--sidebar-active);
        }
        .sidebar .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }
        
        /* Main content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        /* Header */
        .header {
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 30px;
        }
        .header h2 {
            font-weight: 700;
            color: #1e293b;
        }
        
        /* Stats cards */
        .stats-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            height: 100%;
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        .card-value {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            margin: 10px 0 5px 0;
        }
        .card-change {
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        .card-change.up {
            color: #10b981;
        }
        .card-change.down {
            color: #ef4444;
        }
        
        /* Sections - DataTables ready */
        .content-section {
            margin-top: 30px;
        }
        .section-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0,0,0,.06);
            padding: 0;
            overflow: hidden;
            height: 100%;
            border: none;
        }
        .section-card > *:not(.table-responsive) {
            padding: 20px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        /* Chart placeholder */
        .chart-placeholder {
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc;
            border-radius: 8px;
            color: #64748b;
            font-style: italic;
        }
        
        /* Table */
        .table th {
            background-color: transparent;
            color: #64748b;
            font-weight: 600;
            font-size: 14px;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 15px;
        }
        .table td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            color: #475569;
            font-size: 14px;
            vertical-align: middle;
        }
        .table tr:hover {
            background-color: #f8fafc;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
        }
        .status-in-progress {
            background-color: #dbeafe;
            color: #1d4ed8;
        }
        .status-completed {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            color: #64748b;
            font-size: 14px;
            border-top: 1px solid #e2e8f0;
            margin-top: 40px;
        }
        
        /* Mobile menu */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 101;
            background-color: var(--sidebar-bg);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 12px;
            font-size: 20px;
        }
        
        @media (max-width: 992px) {
            .sidebar { width: 70px; overflow: hidden; }
            .logo-text { display: none; }
            .sidebar .nav-link span { display: none; }
            .sidebar .nav-link i { margin-right: 0; font-size: 20px; }
            .sidebar .nav-link { justify-content: center; padding: 15px; }
            .main-content { margin-left: 70px; }
        }
        
        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 15px; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .mobile-menu-btn { display: block; }
        }
        
        .stats-card .card-icon.bg-primary { background-color: var(--sharpedge-blue) !important; }
        .btn-primary { background-color: var(--sharpedge-blue); border-color: var(--sharpedge-blue); }
        .btn-primary:hover { background-color: #1e40af; border-color: #1e40af; }
        .header-user { background-color: var(--sharpedge-blue); }

        /* ===== HEADER COLOR OVERRIDE ===== */
        .table thead th,
        table.dataTable thead th,
        table.dataTable thead td {
            background-color: #364966 !important;
            color: #ffffff !important;
            border-color: #364966 !important;
        }

        table.dataTable thead .sorting,
        table.dataTable thead .sorting_asc,
        table.dataTable thead .sorting_desc {
            color: #ffffff !important;
        }

        .table thead tr:hover {
            background-color: #364966 !important;
        }

        .table thead th:first-child {
            border-top-left-radius: 10px;
        }
        .table thead th:last-child {
            border-top-right-radius: 10px;
        }

        /* ===== DATATABLES CONTAINER LAYOUT FIX (ADDED) ===== */
        /* Fix datatable container layout */
        .section-card .dataTables_wrapper {
            padding: 0;
        }

        /* Top controls row (length + search) */
        .section-card .dataTables_wrapper .row:first-child {
            margin: 0;
            padding: 20px 20px 10px 20px;
        }

        /* Bottom controls row (info + pagination) */
        .section-card .dataTables_wrapper .row:last-child {
            margin: 0;
            padding: 10px 20px 20px 20px;
        }

        /* Prevent table overflow gap */
        .section-card .table {
            margin-bottom: 0;
        }

        /* Align search right cleanly */
        .dataTables_filter {
            text-align: right !important;
        }

        /* Align dropdown left */
        .dataTables_length {
            text-align: left !important;
        }

        /* Fix spacing between title and table */
        .section-title {
            padding: 20px 20px 0 20px;
            margin-bottom: 0;
        }

        /* ===== AUTO-REFRESH INDICATOR STYLES ===== */
        .refresh-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            padding: 12px 20px;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 9999;
            font-family: 'Segoe UI', sans-serif;
            border: 1px solid #e2e8f0;
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.95);
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .refresh-progress {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 3px solid #e2e8f0;
            border-top-color: var(--sharpedge-blue);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .refresh-indicator span {
            color: #1e293b;
            font-size: 14px;
            font-weight: 500;
        }

        .refresh-indicator #refresh-counter {
            font-weight: 700;
            color: var(--sharpedge-blue);
            font-size: 16px;
        }

        /* Hover effect */
        .refresh-indicator:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            transition: all 0.3s;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .refresh-indicator {
                bottom: 15px;
                right: 15px;
                padding: 10px 15px;
            }
            
            .refresh-progress {
                width: 25px;
                height: 25px;
            }
        }

        /* Optional: Pause animation on hover */
        .refresh-indicator:hover .refresh-progress {
            animation-play-state: paused;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="bi bi-list"></i>
    </button>

    <!-- Sidebar -->
    @include('layouts.sidebar.Client_sidebar')

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header -->
        @include('layouts.Header.client_header')

        <!-- Dynamic Page Content -->
        @yield('content')

        <!-- Footer -->
        <footer class="footer">
            <p class="mb-0">© {{ date('Y') }} <strong>{{ config('app.name', 'SharpEdge') }}</strong>. All rights reserved. | Client Panel v2.4</p>
        </footer>
    </div>
    
    <!-- Auto-refresh indicator -->
    <div class="refresh-indicator">
        <div class="refresh-progress"></div>
        <span>Auto-refresh in <span id="refresh-counter">10</span>s</span>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Mobile menu script -->
    <script>
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const btn = document.getElementById('mobileMenuBtn');
            if (window.innerWidth <= 768 && !sidebar.contains(event.target) && !btn.contains(event.target)) {
                sidebar.classList.remove('show');
            }
        });

        const navLinks = document.querySelectorAll('.sidebar .nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navLinks.forEach(item => item.classList.remove('active'));
                this.classList.add('active');
                if (window.innerWidth <= 768) {
                    document.getElementById('sidebar').classList.remove('show');
                }
            });
        });
    </script>

    <!-- jQuery + DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tables = document.querySelectorAll(".datatable");
            tables.forEach(table => {
                new DataTable(table, {
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    ordering: true,
                    searching: true,
                    paging: true,
                    info: true,
                    responsive: true
                });
            });
        });
    </script>

    
    <script>
        (function() {
           
            let refreshTime = 10; 
            let refreshEnabled = true;
            const counterElement = document.getElementById('refresh-counter');
            
            if (!counterElement) {
                console.error('Refresh counter element not found!');
                return;
            }

            // Update counter every second
            const countdown = setInterval(() => {
                if (!refreshEnabled) return;
                
                refreshTime--;
                counterElement.textContent = refreshTime;
                
                if (refreshTime <= 0) {
                    clearInterval(countdown);
                    // Show a message before reloading (optional)
                    counterElement.textContent = "0";
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                }
            }, 1000);

            // Optional: Pause refresh when user interacts with the page
            let inactivityTimer;
            const resetInactivityTimer = () => {
                if (!refreshEnabled) return;
                
                // Reset the refresh timer on user activity
                refreshTime = 10;
                counterElement.textContent = refreshTime;
            };

            // Listen for user activity
            ['click', 'mousemove', 'keypress', 'scroll'].forEach(eventType => {
                document.addEventListener(eventType, resetInactivityTimer);
            });

            // Optional: Permanently stop refresh on explicit user action
            const stopRefreshBtn = document.createElement('button');
            stopRefreshBtn.innerHTML = '<i class="bi bi-pause-fill"></i>';
            stopRefreshBtn.style.cssText = `
                position: fixed;
                bottom: 80px;
                right: 20px;
                background: var(--sharpedge-blue);
                color: white;
                border: none;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                box-shadow: 0 4px 10px rgba(0,0,0,0.2);
                z-index: 9999;
                transition: all 0.3s;
            `;
            
            stopRefreshBtn.addEventListener('click', () => {
                if (refreshEnabled) {
                    clearInterval(countdown);
                    refreshEnabled = false;
                    stopRefreshBtn.innerHTML = '<i class="bi bi-play-fill"></i>';
                    stopRefreshBtn.style.background = '#10b981';
                    counterElement.parentElement.innerHTML = 'Auto-refresh paused';
                } else {
                    location.reload(); // Reload immediately when play is clicked
                }
            });

            // Add the button to the page
            document.body.appendChild(stopRefreshBtn);

            // Clean up on page unload
            window.addEventListener('beforeunload', () => {
                clearInterval(countdown);
            });

            // Debug: Log that auto-refresh is running
            console.log('Auto-refresh initialized - page will reload in 30 seconds');
        })();
    </script>

    @stack('scripts')
</body>
</html>