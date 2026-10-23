<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SharpEdge - Admin Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <!-- jQuery (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
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
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        .header {
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 30px;
        }
        
        .header h2 {
            font-weight: 700;
            color: #1e293b;
        }
        
        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: #ef4444;
            color: white;
            border-radius: 50%;
            padding: 3px 6px;
            font-size: 10px;
            min-width: 18px;
            text-align: center;
            font-weight: 600;
        }
        
        .notification-icon {
            position: relative;
            margin-right: 15px;
            cursor: pointer;
        }
        
        .notification-icon i {
            font-size: 1.5rem;
            color: #64748b;
        }
        
        .notification-dropdown {
            width: 350px;
            max-height: 400px;
            overflow-y: auto;
            padding: 0;
        }
        
        .notification-header {
            background: linear-gradient(135deg, var(--sharpedge-blue), var(--sharpedge-light));
            color: white;
            padding: 12px 15px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .notification-item {
            padding: 12px 15px;
            border-bottom: 1px solid #e2e8f0;
            transition: all 0.2s;
        }
        
        .notification-item:hover {
            background-color: #f8fafc;
        }
        
        .notification-item.unread {
            background-color: #f0f9ff;
            border-left: 3px solid var(--sharpedge-blue);
        }
        
        .notification-time {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 4px;
        }
        
        .notification-footer {
            padding: 10px 15px;
            text-align: center;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }
        
        .notification-footer a {
            color: var(--sharpedge-blue);
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
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
            background-color: #f8fafc;
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
        
        .status-availability-requested {
            background-color: #fef3c7;
            color: #d97706;
        }
        
        .status-availability-provided {
            background-color: #dbeafe;
            color: #1d4ed8;
        }
        
        .status-scheduled {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-completed {
            background-color: #10b981;
            color: white;
        }
        
        .status-cancelled {
            background-color: #fee2e2;
            color: #ef4444;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            color: #64748b;
            font-size: 14px;
            border-top: 1px solid #e2e8f0;
            margin-top: 40px;
        }
        
        /* DataTables Custom Styling – Clean */
        .dataTables_wrapper .dataTables_length {
            float: left;
            margin-bottom: 15px;
        }
        
        .dataTables_wrapper .dataTables_filter {
            float: right;
            margin-bottom: 15px;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 6px 12px;
        }
        
        .dataTables_wrapper .dataTables_info {
            float: left;
            margin-top: 10px;
            color: #64748b;
            font-size: 14px;
        }
        
        .dataTables_wrapper .dataTables_paginate {
            float: right;
            margin-top: 10px;
        }
        
        .dataTables_wrapper .dataTables_paginate .pagination {
            gap: 6px;
        }
        
        .dataTables_wrapper .dataTables_paginate .page-link {
            border-radius: 8px !important;
            padding: 6px 12px !important;
            border: 1px solid #e2e8f0 !important;
            color: #1e293b !important;
            font-weight: 500;
        }
        
        .dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
            background-color: var(--sharpedge-blue) !important;
            border-color: var(--sharpedge-blue) !important;
            color: #fff !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .page-item.disabled .page-link {
            color: #94a3b8 !important;
            background-color: #f8fafc !important;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                overflow: hidden;
            }
            
            .logo-text {
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
            
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                float: none;
                text-align: center;
                margin-bottom: 10px;
            }
            
            .dataTables_wrapper .dataTables_paginate {
                margin-top: 20px;
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
        
        .stats-card .card-icon.bg-primary {
            background-color: var(--sharpedge-blue) !important;
        }
        
        .btn-primary {
            background-color: var(--sharpedge-blue);
            border-color: var(--sharpedge-blue);
        }
        
        .btn-primary:hover {
            background-color: #1e40af;
            border-color: #1e40af;
        }
        
        .header-user {
            background-color: var(--sharpedge-blue);
        }
        
        #categoriesTable thead th {
            background-color: #2f3d53 !important;
            color: #e5e7eb !important;
            font-weight: 600;
            border-bottom: 1px solid #334155;
        }
        
        .section-card .table thead th {
            background-color: #364966 !important;
            color: #e5e7eb !important;
            font-weight: 600;
            border-bottom: 1px solid #41536b;
        }
        
        .section-card .table thead {
            box-shadow: inset 0 -1px 0 rgba(255,255,255,0.06);
        }
        
        .datatable-table {
            margin-top: 10px;
        }
        
        .dataTable {
            border-collapse: collapse !important;
            width: 100% !important;
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
    @include('layouts.sidebar.sidebar')

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        @yield('content')
        
        <!-- Footer -->
        <footer class="footer">
            <p class="mb-0">© {{ date('Y') }} <strong>SharpEdge</strong>. All rights reserved. | Admin Panel v2.4</p>
        </footer>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Mobile menu toggle
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebar = document.getElementById('sidebar');

            if (mobileMenuBtn && sidebar) {
                mobileMenuBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    sidebar.classList.toggle('show');
                });

                document.addEventListener('click', function (event) {
                    const isClickInsideSidebar = sidebar.contains(event.target);
                    const isClickOnMobileBtn = mobileMenuBtn.contains(event.target);

                    if (window.innerWidth <= 768 && !isClickInsideSidebar && !isClickOnMobileBtn) {
                        sidebar.classList.remove('show');
                    }
                });
            }

            // Activate sidebar navigation items
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function () {
                    navLinks.forEach(item => item.classList.remove('active'));
                    this.classList.add('active');

                    if (window.innerWidth <= 768 && sidebar) {
                        sidebar.classList.remove('show');
                    }
                });
            });

            // Initialize DataTables for any table with class 'datatable'
            $('table.datatable').each(function () {
                const table = $(this);

                if ($.fn.DataTable.isDataTable(table)) {
                    return;
                }

                table.DataTable({
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    dom: 'lfrtip',
                    language: {
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "Showing 0 to 0 of 0 entries",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        paginate: {
                            previous: "Previous",
                            next: "Next"
                        },
                        search: "Search:",
                        searchPlaceholder: "Type to filter..."
                    },
                    searching: true,
                    responsive: true
                });
            });

            // Mark notification as read
            window.markAsRead = function(notificationId) {
                fetch(`/admin/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const notificationItem = document.getElementById(`notification-${notificationId}`);
                        if (notificationItem) {
                            notificationItem.classList.remove('unread');
                        }
                        updateNotificationBadge();
                    }
                });
            };

            window.markAllAsRead = function() {
                fetch('/admin/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelectorAll('.notification-item').forEach(item => {
                            item.classList.remove('unread');
                        });
                        updateNotificationBadge();
                    }
                });
            };

            function updateNotificationBadge() {
                const unreadCount = document.querySelectorAll('.notification-item.unread').length;
                const badge = document.querySelector('.notification-badge');
                if (badge) {
                    if (unreadCount > 0) {
                        badge.textContent = unreadCount;
                        badge.style.display = 'inline';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>