<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- REMOVED: <meta http-equiv="refresh" content="10"> -->
    <title>@yield('title') - SharpEdge Marketing</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- jQuery (needed) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-dark: #1d4ed8;
            --secondary-color: #10b981;
            --warning-color: #f59e0b;
            --info-color: #0ea5e9;
            --danger-color: #ef4444;
            --light-color: #f8fafc;
            --dark-color: #0f172a;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --sharpedge-blue: #1d4ed8;
            --sharpedge-light: #60a5fa;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            overflow-x: hidden;
        }
        
        /* Top Header */
        .top-header {
            position: fixed;
            top: 0;
            left: 260px;
            right: 0;
            height: 70px;
            background: white;
            border-bottom: 1px solid var(--gray-200);
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: left 0.3s ease;
        }
        
        .page-title h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.25rem;
        }
        
        .page-title p {
            color: var(--gray-500);
            font-size: 0.875rem;
            margin-bottom: 0;
        }
        
        /* User Dropdown Styles */
        .user-dropdown {
            position: relative;
        }
        
        .user-dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            color: var(--gray-700);
            transition: all 0.2s ease;
            cursor: pointer;
            border: 1px solid transparent;
        }
        
        .user-dropdown-toggle:hover {
            background-color: var(--gray-100);
            border-color: var(--gray-200);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .user-avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .user-info {
            text-align: left;
        }
        
        .user-info h6 {
            color: var(--dark-color);
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.125rem;
        }
        
        .user-info p {
            color: var(--gray-500);
            font-size: 0.75rem;
            font-weight: 400;
            margin-bottom: 0;
        }
        
        .user-dropdown-arrow {
            color: var(--gray-400);
            font-size: 0.875rem;
        }
        
        .user-dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 220px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: 1px solid var(--gray-200);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }
        
        .user-dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .user-dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            text-decoration: none;
            color: var(--gray-700);
            transition: all 0.2s ease;
            border-bottom: 1px solid var(--gray-100);
        }
        
        .user-dropdown-item:last-child {
            border-bottom: none;
        }
        
        .user-dropdown-item:hover {
            background-color: var(--gray-100);
            color: var(--primary-color);
        }
        
        .user-dropdown-item i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
        }
        
        .user-dropdown-divider {
            height: 1px;
            background: var(--gray-200);
            margin: 8px 0;
        }
        
        .dropdown-item-danger {
            color: var(--danger-color) !important;
        }
        
        .dropdown-item-danger:hover {
            background-color: rgba(239, 68, 68, 0.1) !important;
        }
        
        /* Dark Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: var(--gray-900);
            color: #cbd5e1;
            z-index: 1000;
            transition: transform 0.3s ease;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            background: var(--gray-900);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .logo-text {
            font-weight: 700;
            font-size: 18px;
            color: white;
            letter-spacing: 0.5px;
        }
        
        /* User Profile in Sidebar */
        .user-profile {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .sidebar-user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .sidebar-user-avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .user-info h4 {
            color: white;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.125rem;
        }
        
        .user-info p {
            color: var(--gray-400);
            font-size: 0.75rem;
            font-weight: 400;
        }
        
        /* Menu Items */
        .sidebar-menu {
            padding: 1rem 0;
        }
        
        .menu-item {
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--gray-400);
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        
        .menu-item:hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            border-left: 3px solid var(--primary-color);
        }
        
        .menu-item.active {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            border-left: 3px solid var(--primary-color);
        }
        
        .menu-item i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        
        .menu-item span {
            font-size: 0.875rem;
            font-weight: 500;
            flex: 1;
        }
        
        .menu-item .badge {
            font-size: 0.65rem;
            padding: 0.125rem 0.375rem;
            border-radius: 10px;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 260px;
            margin-top: 70px;
            padding: 25px;
            transition: all 0.3s ease;
            min-height: 100vh;
        }
        
        /* Header (inside main content - for page titles) */
        .header {
            margin-bottom: 2rem;
        }
        
        .header h2 {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.25rem;
        }
        
        .header p {
            color: var(--gray-500);
            font-size: 0.875rem;
        }
        
        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--gray-200);
            height: 100%;
            transition: transform 0.2s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .card-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0;
        }
        
        .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
        }
        
        .bg-primary { background-color: var(--primary-color); }
        .bg-success { background-color: var(--secondary-color); }
        .bg-warning { background-color: var(--warning-color); }
        .bg-info { background-color: var(--info-color); }
        
        .card-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin: 0.75rem 0;
        }
        
        .card-change {
            display: flex;
            align-items: center;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .card-change.up {
            color: var(--secondary-color);
        }
        
        .card-change.down {
            color: var(--danger-color);
        }
        
        .card-change i {
            margin-right: 0.25rem;
        }
        
        /* Content Section */
        .content-section {
            margin-top: 1.5rem;
        }
        
        .section-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--gray-200);
            height: 100%;
        }
        
        .section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1.5rem;
        }
        
        /* Tables */
        .table {
            background: white;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        .table thead {
            background-color: var(--gray-100);
        }
        
        .table th {
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-600);
            padding: 1rem 1.5rem;
            border-bottom: 2px solid var(--gray-200);
        }
        
        .table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            color: var(--gray-700);
            font-size: 0.875rem;
        }
        
        .table tbody tr:hover {
            background-color: var(--gray-100);
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .status-pending {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }
        
        .status-in-progress {
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--primary-color);
        }
        
        .status-completed {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--secondary-color);
        }
        
        /* Buttons */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
        }
        
        .btn-outline-primary {
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-outline-success {
            border: 1px solid var(--secondary-color);
            color: var(--secondary-color);
        }
        
        .btn-outline-success:hover {
            background: var(--secondary-color);
            color: white;
        }
        
        /* Footer */
        .footer {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--gray-200);
            text-align: center;
            color: var(--gray-500);
            font-size: 0.875rem;
        }
        
        .footer strong {
            color: var(--dark-color);
            font-weight: 600;
        }
        
        /* Mobile Toggle */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
            background: var(--gray-900);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }
        
        /* Sidebar Overlay for Mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        /* Sidebar Notices Section */
        .sidebar-notices-section {
            margin-top: auto;
            padding: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            background: rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        
        .sidebar-section-header h6 {
            color: white;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0;
        }
        
        .view-all-link {
            color: var(--primary-color);
            font-size: 0.75rem;
            text-decoration: none;
            transition: opacity 0.2s ease;
        }
        
        .view-all-link:hover {
            opacity: 0.8;
        }
        
        .sidebar-notices-list {
            max-height: 200px;
            overflow-y: auto;
        }
        
        .sidebar-notice-item {
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 6px;
            margin-bottom: 0.5rem;
            border-left: 3px solid var(--primary-color);
            transition: background 0.2s ease;
            cursor: pointer;
        }
        
        .sidebar-notice-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-notice-title {
            color: white;
            font-size: 0.8rem;
            font-weight: 500;
            line-height: 1.3;
            margin-bottom: 0.25rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .sidebar-notice-meta {
            font-size: 0.7rem;
            color: var(--gray-400);
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
            font-family: 'Inter', sans-serif;
            border: 1px solid var(--gray-200);
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
            border: 3px solid var(--gray-200);
            border-top-color: var(--sharpedge-blue);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .refresh-indicator span {
            color: var(--gray-700);
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

        /* Pause animation on hover */
        .refresh-indicator:hover .refresh-progress {
            animation-play-state: paused;
        }

        /* Pause/Play button styling */
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
            background: var(--secondary-color);
        }

        /* Paused state for indicator */
        .refresh-indicator.paused .refresh-progress {
            animation: none;
            border-color: var(--secondary-color);
            border-top-color: var(--secondary-color);
        }

        .refresh-indicator.paused span {
            color: var(--secondary-color);
        }
        
        /* ========== RESPONSIVE STYLES ========== */
        
        /* Large Devices (992px and down) */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                width: 260px;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .top-header {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
                margin-top: 70px;
                padding: 20px;
            }
            
            .mobile-toggle {
                display: block;
            }
            
            .user-info h6,
            .user-info p {
                display: none;
            }
            
            .page-title h1 {
                font-size: 1.25rem;
            }
            
            .page-title p {
                font-size: 0.75rem;
            }
            
            .user-dropdown-toggle {
                padding: 8px 12px;
            }
        }
        
        /* Medium Devices (768px and down) */
        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
                margin-top: 70px;
            }
            
            .header h2 {
                font-size: 1.5rem;
            }
            
            .stats-card {
                padding: 1.25rem;
            }
            
            .card-value {
                font-size: 1.75rem;
            }
            
            .section-card {
                padding: 1.25rem;
            }
            
            .table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
                -webkit-overflow-scrolling: touch;
            }
            
            .user-dropdown-menu {
                position: fixed;
                top: 70px;
                right: 10px;
                left: 10px;
                width: auto;
            }
            
            .row.g-4 {
                margin-left: -0.5rem;
                margin-right: -0.5rem;
            }
            
            .col-xl-3, .col-lg-6, .col-md-6 {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            
            .top-header {
                padding: 0 15px;
            }
        }
        
        /* Small Devices (576px and down) */
        @media (max-width: 576px) {
            .main-content {
                padding: 12px;
                margin-top: 70px;
            }
            
            .sidebar {
                width: 100%;
                max-width: 280px;
            }
            
            .mobile-toggle {
                top: 10px;
                left: 10px;
                padding: 8px 12px;
                font-size: 1.1rem;
            }
            
            .stats-card {
                padding: 1rem;
            }
            
            .card-value {
                font-size: 1.5rem;
            }
            
            .section-card {
                padding: 1rem;
            }
            
            .header h2 {
                font-size: 1.25rem;
            }
            
            .header p {
                font-size: 0.75rem;
            }
            
            .top-header {
                height: 60px;
                padding: 0 10px;
            }
            
            .user-avatar {
                width: 35px;
                height: 35px;
                font-size: 12px;
            }
            
            .page-title h1 {
                font-size: 1.1rem;
            }
            
            .page-title p {
                display: none;
            }
            
            .menu-item {
                padding: 1rem 1.5rem;
            }
            
            .sidebar-user-avatar {
                width: 45px;
                height: 45px;
            }
            
            .user-info h4 {
                font-size: 0.95rem;
            }
            
            .sidebar-notices-section {
                padding: 1rem;
            }
            
            .sidebar-notice-item {
                padding: 1rem;
            }
            
            .btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.8rem;
            }
        }
        
        /* Extra Small Devices (375px and down) */
        @media (max-width: 375px) {
            .main-content {
                padding: 10px;
            }
            
            .mobile-toggle {
                top: 8px;
                left: 8px;
                padding: 6px 10px;
                font-size: 1rem;
            }
            
            .user-dropdown-toggle {
                padding: 6px 8px;
            }
            
            .user-avatar {
                width: 32px;
                height: 32px;
            }
            
            .top-header {
                height: 55px;
            }
            
            .logo-text {
                font-size: 1.1rem;
            }
            
            .logo-icon {
                width: 30px;
                height: 30px;
                font-size: 1rem;
            }
        }
        
        /* Print Styles */
        @media print {
            .sidebar,
            .top-header,
            .mobile-toggle,
            .user-dropdown,
            .refresh-indicator,
            .refresh-toggle-btn {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0 !important;
                margin-top: 0 !important;
                padding: 0 !important;
            }
            
            body {
                background: white !important;
                color: black !important;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    @php
        $user = Auth::user();
        
        $initials = '';
        if ($user && $user->name) {
            $nameParts = explode(' ', $user->name);
            foreach ($nameParts as $part) {
                if (!empty($part)) {
                    $initials .= strtoupper(substr($part, 0, 1));
                }
            }
            $initials = substr($initials, 0, 2);
        } else {
            $initials = 'U';
        }
        
        $roleName = $user && $user->role ? ucfirst($user->role->name) : 'Marketing Staff';
    @endphp

    <!-- Sidebar Overlay (for mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle" id="mobileToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Include Sidebar -->
    @include('layouts.sidebar.marketing_sidebar')

    <!-- Top Header with User Dropdown -->
    <div class="top-header" id="topHeader">
        <div class="page-title">
            <h1>@yield('page-title', 'Dashboard')</h1>
            <p>
                @hasSection('page-subtitle')
                    @yield('page-subtitle')
                @else
                    Welcome back, {{ $user->name ?? 'User' }}!
                @endif
            </p>
        </div>
        
        <div class="user-dropdown" id="userDropdown">
            <div class="user-dropdown-toggle" id="userDropdownToggle">
                <div class="user-avatar">
                    @if($user && $user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile" class="user-avatar-img">
                    @else
                        <span>{{ $initials }}</span>
                    @endif
                </div>
                <div class="user-info">
                    <h6>{{ $user->name ?? 'User' }}</h6>
                    <p>{{ $roleName }}</p>
                </div>
                <div class="user-dropdown-arrow">
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
            
            <div class="user-dropdown-menu" id="userDropdownMenu">
                <div class="user-dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                    @csrf
                    <a href="#" class="user-dropdown-item dropdown-item-danger" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        @yield('content')
        
        <!-- Footer (optional - can be uncommented if needed) -->
        <!-- <footer class="footer">
            <p class="mb-0">© {{ date('Y') }} <strong>SharpEdge Marketing</strong>. All rights reserved.</p>
        </footer> -->
    </div>

    <!-- Auto-refresh indicator -->
    <div class="refresh-indicator" id="refreshIndicator">
        <div class="refresh-progress"></div>
        <span>Auto-refresh in <span id="refresh-counter">30</span>s</span>
    </div>

    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Mobile sidebar toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        if (mobileToggle) {
            mobileToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
                document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
            });
        }
        
        // Close sidebar when clicking on overlay
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
                document.body.style.overflow = '';
            });
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (event) => {
            if (!sidebar || !mobileToggle) return;

            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggle = mobileToggle.contains(event.target);

            if (window.innerWidth <= 992 && !isClickInsideSidebar && !isClickOnToggle) {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
        
        // Close sidebar when pressing escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && window.innerWidth <= 992) {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
        
        // User dropdown toggle
        const userDropdownToggle = document.getElementById('userDropdownToggle');
        const userDropdownMenu = document.getElementById('userDropdownMenu');
        
        if (userDropdownToggle) {
            userDropdownToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdownMenu.classList.toggle('show');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!userDropdownToggle.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                    userDropdownMenu.classList.remove('show');
                }
            });
            
            // Close dropdown on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    userDropdownMenu.classList.remove('show');
                }
            });
        }
        
        // Menu active state - close sidebar on mobile when menu item is clicked
        const menuItems = document.querySelectorAll('.menu-item');
        menuItems.forEach(item => {
            item.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    document.body.style.overflow = '';
                }
                // Close user dropdown if open
                if (userDropdownMenu) {
                    userDropdownMenu.classList.remove('show');
                }
            });
        });
        
        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', () => {
            // Clear the timeout if it's already set
            clearTimeout(resizeTimer);
            
            // Set a timeout to run after resize is complete
            resizeTimer = setTimeout(() => {
                // If screen is larger than 992px and sidebar is hidden, show it
                if (window.innerWidth > 992) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    document.body.style.overflow = '';
                }
            }, 250);
        });
        
        // Prevent body scroll when sidebar is open on mobile
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    const isSidebarOpen = sidebar.classList.contains('show');
                    document.body.style.overflow = isSidebarOpen && window.innerWidth <= 992 ? 'hidden' : '';
                }
            });
        });
        
        observer.observe(sidebar, {
            attributes: true,
            attributeFilter: ['class']
        });

        // ===== AUTO-REFRESH FUNCTIONALITY =====
        document.addEventListener('DOMContentLoaded', function() {
            let refreshTime = 30; // seconds
            let refreshEnabled = true;
            let countdownInterval;
            const counterElement = document.getElementById('refresh-counter');
            const refreshIndicator = document.getElementById('refreshIndicator');
            
            if (!counterElement) {
                console.error('Refresh counter element not found!');
                return;
            }

            // Function to start countdown
            function startCountdown() {
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                }
                
                countdownInterval = setInterval(() => {
                    if (!refreshEnabled) return;
                    
                    refreshTime--;
                    counterElement.textContent = refreshTime;
                    
                    if (refreshTime <= 0) {
                        clearInterval(countdownInterval);
                        counterElement.textContent = "0";
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    }
                }, 1000);
            }

            // Start initial countdown
            startCountdown();

            // Reset timer on user activity
            const resetRefreshTimer = () => {
                if (!refreshEnabled) return;
                
                refreshTime = 30;
                counterElement.textContent = refreshTime;
                
                // Restart countdown
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                    startCountdown();
                }
            };

            // Listen for user activity
            ['click', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(eventType => {
                document.addEventListener(eventType, resetRefreshTimer);
            });

            // Create toggle button
            const toggleBtn = document.createElement('button');
            toggleBtn.innerHTML = '<i class="fas fa-pause"></i>';
            toggleBtn.className = 'refresh-toggle-btn';
            toggleBtn.id = 'refreshToggleBtn';
            toggleBtn.setAttribute('title', 'Pause auto-refresh');
            
            toggleBtn.addEventListener('click', () => {
                if (refreshEnabled) {
                    // Pause refresh
                    clearInterval(countdownInterval);
                    refreshEnabled = false;
                    toggleBtn.innerHTML = '<i class="fas fa-play"></i>';
                    toggleBtn.classList.add('paused');
                    toggleBtn.setAttribute('title', 'Resume auto-refresh');
                    
                    // Update indicator
                    refreshIndicator.classList.add('paused');
                    refreshIndicator.innerHTML = `
                        <div class="refresh-progress"></div>
                        <span>Auto-refresh paused</span>
                    `;
                } else {
                    // Resume refresh
                    refreshEnabled = true;
                    refreshTime = 30;
                    toggleBtn.innerHTML = '<i class="fas fa-pause"></i>';
                    toggleBtn.classList.remove('paused');
                    toggleBtn.setAttribute('title', 'Pause auto-refresh');
                    
                    // Restore indicator
                    refreshIndicator.classList.remove('paused');
                    refreshIndicator.innerHTML = `
                        <div class="refresh-progress"></div>
                        <span>Auto-refresh in <span id="refresh-counter">30</span>s</span>
                    `;
                    
                    // Restart countdown
                    startCountdown();
                }
            });

            // Add the button to the page
            document.body.appendChild(toggleBtn);

            // Clean up on page unload
            window.addEventListener('beforeunload', () => {
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                }
            });

            // Log that auto-refresh is running
            console.log('Auto-refresh initialized - page will reload in 30 seconds');
        });
    </script>
    
    
    
    @yield('scripts')
</body>
</html>