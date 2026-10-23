<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- ✅ CSRF TOKEN – REQUIRED FOR AJAX REQUESTS --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>SharpEdge | Back Office Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        :root {
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --sidebar-active: #60a5fa;
            --card-shadow: 0 12px 24px rgba(0, 0, 0, 0.08), 0 4px 8px rgba(0, 0, 0, 0.04);
            --primary-color: #3b82f6;
            --logo-blue: #2563eb;
            --sharpedge-blue: #1d4ed8;
            --sharpedge-light: #60a5fa;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Sidebar styling */
   .sidebar {
    background-color: var(--sidebar-bg);
    color: #fff;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    width: 265px;
    z-index: 100;
    padding: 0;
    transition: all 0.3s;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    overflow-y: auto;      /* ✅ enables vertical scrolling */
    overflow-x: hidden;    /* optional: prevent horizontal scroll */
}
        
        /* Chrome, Safari, Edge ব্রাউজারের জন্য */
.your-scrollable-div::-webkit-scrollbar {
    display: none;
}

/* Firefox ব্রাউজারের জন্য */
.your-scrollable-div {
    scrollbar-width: none;
}

/* পুরনো IE/Edge ব্রাউজারের জন্য (নিরাপত্তার জন্য) */
.your-scrollable-div {
    -ms-overflow-style: none;
}

.sidebar {
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.sidebar::-webkit-scrollbar {
    display: none;
}
        .sidebar .logo {
            padding: 20px;
            border-bottom: 1px solid #334155;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }
        
        .logo-square {
            width: 40px;
            height: 40px;
            background-color: var(--logo-blue);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }
        
        .logo-letter {
            color: white;
            font-weight: 800;
            font-size: 22px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .logo-text {
            font-size: 22px;
            font-weight: 600;
            color: #f8fafc;
            letter-spacing: 0.5px;
            margin: 0;
            transition: opacity 0.3s;
        }
        
        .user-info-sidebar {
            padding: 15px 20px;
            border-bottom: 1px solid #334155;
            margin-bottom: 10px;
        }
        
        .user-avatar-sidebar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
            margin-right: 10px;
        }
        
        .sidebar .nav-item {
            margin: 3px 10px;
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
            font-size: 18px;
        }
        
        .main-content {
            margin-left: 270px;
            padding: 20px;
            transition: all 0.3s;
            overflow-x: auto;
            overflow-y: auto;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 30px;
            flex-shrink: 0;
        }
        
        .header h2 {
            font-weight: 700;
            color: #1e293b;
        }
        
        .profile-dropdown-toggle {
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 8px;
            transition: background-color 0.2s;
        }
        
        .profile-dropdown-toggle:hover {
            background-color: #f1f5f9;
        }
        
        .profile-dropdown-menu {
            min-width: 200px;
            padding: 8px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-top: 8px;
        }
        
        .profile-dropdown-item {
            padding: 10px 16px;
            color: #475569;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        
        .profile-dropdown-item:hover {
            background-color: #f1f5f9;
            color: #1e293b;
        }
        
        .profile-dropdown-item.logout:hover {
            color: #dc2626;
        }
        
        .profile-dropdown-divider {
            border-top: 1px solid #e2e8f0;
            margin: 8px 0;
        }
        
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
            transform: translateY(-6px);
            box-shadow: 0 18px 30px rgba(0, 0, 0, 0.12), 0 6px 12px rgba(0, 0, 0, 0.06);
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
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.5px;
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
        
        .content-section {
            margin-top: 30px;
        }
        
        .section-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            height: 100%;
            border: none;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        
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
        
        .table th {
            background-color: #3B4F6B;
            color: #ffffff;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-bottom: none;
            padding: 16px 18px;
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
        
        .status-waiting {
            background-color: #e0e7ff;
            color: #4f46e5;
        }
        
        .status-overdue {
            background-color: #fee2e2;
            color: #dc2626;
        }
        
        .task-actions {
            display: flex;
            gap: 8px;
        }
        
        .btn-task-action {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background-color: #f1f5f9;
            color: #475569;
            transition: all 0.3s;
        }
        
        .btn-task-action:hover {
            background-color: var(--sidebar-active);
            color: white;
        }
        
        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            background-color: #f8fafc;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .upload-area:hover {
            border-color: var(--sidebar-active);
            background-color: #f0f9ff;
        }
        
        .upload-icon {
            font-size: 40px;
            color: var(--sidebar-active);
            margin-bottom: 15px;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            color: #64748b;
            font-size: 14px;
            border-top: 1px solid #e2e8f0;
            margin-top: 40px;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 6px;
            padding: 6px 10px;
            border: 1px solid #cbd5e1;
            transition: border-color 0.2s;
        }
        
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--sidebar-active);
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .dataTables_wrapper .dataTables_paginate .page-link {
            border-radius: 6px;
            margin: 0 2px;
            border: 1px solid #e2e8f0;
            color: #475569;
        }
        
        .dataTables_wrapper .dataTables_paginate .page-link:hover {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
        }
        
        .dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
            background-color: var(--sidebar-active);
            border-color: var(--sidebar-active);
            color: white;
        }
        
        .dataTables_wrapper .dataTables_length select {
            border-radius: 6px;
            padding: 4px 8px;
            border: 1px solid #cbd5e1;
        }
        
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

        .refresh-indicator:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            transition: all 0.3s;
        }

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

        .refresh-indicator:hover .refresh-progress {
            animation-play-state: paused;
        }

        .refresh-toggle-btn {
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
            border: 2px solid white;
        }

        .refresh-toggle-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        }

        .refresh-toggle-btn.paused {
            background: #10b981;
        }

        .refresh-indicator.paused .refresh-progress {
            animation: none;
            border-color: #10b981;
            border-top-color: #10b981;
        }

        .refresh-indicator.paused span {
            color: #10b981;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                overflow: hidden;
            }
            .sidebar .logo {
                justify-content: center;
                padding: 20px 10px;
            }
            .sidebar .logo .logo-text {
                display: none;
                opacity: 0;
                width: 0;
            }
            .sidebar .logo-square {
                margin-right: 0;
            }
            .sidebar .user-info-sidebar .user-name,
            .sidebar .user-info-sidebar .user-role {
                display: none;
            }
            .sidebar .nav-link span {
                display: none;
            }
            .sidebar .nav-link i {
                margin-right: 0;
                font-size: 20px;
            }
            .sidebar .nav-link {
                justify-content: center;
                padding: 15px;
            }
            .main-content {
                margin-left: 70px;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .mobile-menu-btn {
                display: block;
            }
        }
        
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
        
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }
        }
        
        .priority-badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .priority-high {
            background-color: #fee2e2;
            color: #dc2626;
        }
        
        .priority-medium {
            background-color: #fef3c7;
            color: #d97706;
        }
        
        .priority-low {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .recent-uploads {
            max-height: 200px;
            overflow-y: auto;
        }
        
        .upload-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .upload-item:last-child {
            border-bottom: none;
        }
        
        .file-icon {
            font-size: 18px;
            margin-right: 10px;
        }
        
        .file-name {
            flex-grow: 1;
            font-size: 14px;
            color: #4b5563;
        }
        
        .file-time {
            font-size: 12px;
            color: #9ca3af;
        }
        
        .task-overview {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .task-progress {
            margin-top: 20px;
        }
        
        .progress-item {
            margin-bottom: 15px;
        }
        
        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .progress-bar {
            height: 8px;
            border-radius: 4px;
            background-color: #e5e7eb;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 4px;
        }
        
        .progress-completed {
            background-color: #10b981;
            width: 52%;
        }
        
        .progress-in-progress {
            background-color: #3b82f6;
            width: 37%;
        }
        
        .progress-overdue {
            background-color: #ef4444;
            width: 10%;
        }
        
        .progress-pending {
            background-color: #f59e0b;
            width: 8%;
        }
        
        .role-indicator {
            display: inline-block;
            padding: 3px 8px;
            background-color: #dbeafe;
            color: #1d4ed8;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f7fa;
        }
        
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            background-color: white;
        }
        
        .login-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
        }
        
        .login-logo .logo-square {
            margin-right: 15px;
        }
        
        .login-logo-text {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="bi bi-list"></i>
    </button>

    <!-- Sidebar -->
    @include('layouts.sidebar.Backoffice_sidebar')

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header -->
        @include('Backoffice.Header.Header')
        
        <!-- Content -->
        @yield('content')
        @yield('modals')
        
        <!-- Footer (optional) -->
        {{-- <footer class="footer">
            <p class="mb-0">© {{ date('Y') }} SharpEdge Back Office Panel. All rights reserved. | Version 2.1.0</p>
            <p class="mb-0 small text-muted mt-1">Back Office Role: Executor Only - No Approvals, No Allocations</p>
        </footer> --}}
    </div>

    <!-- Auto-refresh indicator -->
    <div class="refresh-indicator" id="refreshIndicator">
        <div class="refresh-progress"></div>
        <span>Auto-refresh in <span id="refresh-counter">30</span>s</span>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function () {
            if ($('#tasksTable').length && !$.fn.DataTable.isDataTable('#tasksTable')) {
                $('#tasksTable').DataTable({
                    pageLength: 10,
                    lengthChange: true,
                    lengthMenu: [10, 25, 50, 100],
                    ordering: true,
                    searching: true,
                    responsive: true,
                    autoWidth: false,
                    columnDefs: [{ orderable: false, targets: -1 }],
                    language: {
                        lengthMenu: "Show _MENU_ tasks",
                        search: "Search tasks:",
                        info: "Showing _START_ to _END_ of _TOTAL_ tasks",
                        infoEmpty: "No tasks found",
                        zeroRecords: "No matching tasks found",
                        paginate: { previous: "Previous", next: "Next" }
                    }
                });
            }
        });
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Bootstrap dropdowns
            const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
            const dropdownList = [...dropdownElementList].map(dropdownToggleEl => new bootstrap.Dropdown(dropdownToggleEl));
            
            document.addEventListener('click', function(event) {
                const profileDropdown = document.getElementById('profileDropdown');
                if (profileDropdown) {
                    const dropdownMenu = profileDropdown.querySelector('.dropdown-menu');
                    const dropdownToggle = profileDropdown.querySelector('.dropdown-toggle');
                    if (!dropdownToggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
                        const dropdownInstance = bootstrap.Dropdown.getInstance(dropdownToggle);
                        if (dropdownInstance) dropdownInstance.hide();
                    }
                }
            });

            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    document.getElementById('sidebar').classList.toggle('show');
                });
            }

            document.addEventListener('click', function(event) {
                const sidebar = document.getElementById('sidebar');
                const mobileMenuBtn = document.getElementById('mobileMenuBtn');
                if (sidebar && mobileMenuBtn && window.innerWidth <= 768) {
                    const isClickInsideSidebar = sidebar.contains(event.target);
                    const isClickOnMobileBtn = mobileMenuBtn.contains(event.target);
                    if (!isClickInsideSidebar && !isClickOnMobileBtn) {
                        sidebar.classList.remove('show');
                    }
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

            // Auto-refresh
            let refreshTime = 30;
            let refreshEnabled = true;
            let countdownInterval;
            const counterElement = document.getElementById('refresh-counter');
            const refreshIndicator = document.getElementById('refreshIndicator');
            
            if (counterElement) {
                function startCountdown() {
                    if (countdownInterval) clearInterval(countdownInterval);
                    countdownInterval = setInterval(() => {
                        if (!refreshEnabled) return;
                        refreshTime--;
                        counterElement.textContent = refreshTime;
                        if (refreshTime <= 0) {
                            clearInterval(countdownInterval);
                            counterElement.textContent = "0";
                            setTimeout(() => location.reload(), 500);
                        }
                    }, 1000);
                }
                startCountdown();

                const resetRefreshTimer = () => {
                    if (!refreshEnabled) return;
                    refreshTime = 30;
                    counterElement.textContent = refreshTime;
                    if (countdownInterval) {
                        clearInterval(countdownInterval);
                        startCountdown();
                    }
                };

                ['click', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(eventType => {
                    document.addEventListener(eventType, resetRefreshTimer);
                });

                const toggleBtn = document.createElement('button');
                toggleBtn.innerHTML = '<i class="bi bi-pause-fill"></i>';
                toggleBtn.className = 'refresh-toggle-btn';
                toggleBtn.id = 'refreshToggleBtn';
                toggleBtn.setAttribute('title', 'Pause auto-refresh');
                toggleBtn.addEventListener('click', () => {
                    if (refreshEnabled) {
                        clearInterval(countdownInterval);
                        refreshEnabled = false;
                        toggleBtn.innerHTML = '<i class="bi bi-play-fill"></i>';
                        toggleBtn.classList.add('paused');
                        toggleBtn.setAttribute('title', 'Resume auto-refresh');
                        refreshIndicator.classList.add('paused');
                        refreshIndicator.innerHTML = `
                            <div class="refresh-progress"></div>
                            <span>Auto-refresh paused</span>
                        `;
                    } else {
                        refreshEnabled = true;
                        refreshTime = 30;
                        toggleBtn.innerHTML = '<i class="bi bi-pause-fill"></i>';
                        toggleBtn.classList.remove('paused');
                        toggleBtn.setAttribute('title', 'Pause auto-refresh');
                        refreshIndicator.classList.remove('paused');
                        refreshIndicator.innerHTML = `
                            <div class="refresh-progress"></div>
                            <span>Auto-refresh in <span id="refresh-counter">30</span>s</span>
                        `;
                        startCountdown();
                    }
                });
                document.body.appendChild(toggleBtn);

                window.addEventListener('beforeunload', () => {
                    if (countdownInterval) clearInterval(countdownInterval);
                });
                console.log('Auto-refresh initialized - page will reload in 30 seconds');
            }
        });

        // Task action stubs
        function startTask(taskId) { alert(`Starting Task #${taskId}...`); }
        function uploadTaskDocument(taskId) { alert(`Uploading document for Task #${taskId}`); }
        function completeTask(taskId) { alert(`Completing Task #${taskId}`); }
        function callClient(taskId) { alert(`Calling client for Task #${taskId}`); }
        function addNotes(taskId) { alert(`Adding notes to Task #${taskId}`); }
        function viewTaskDetails(taskId) { alert(`Viewing details for Task #${taskId}`); }
        function reopenTask(taskId) { alert(`Reopening Task #${taskId}`); }
        function sendReminder(taskId) { alert(`Sending reminder for Task #${taskId}`); }
        function uploadDocument() { alert("Opening file browser"); }
        function logout() { document.getElementById('logout-form').submit(); }

        console.log("New task assigned: Document Verification for Client XYZ");
    </script>
    
    @yield('scripts')
</body>
</html>