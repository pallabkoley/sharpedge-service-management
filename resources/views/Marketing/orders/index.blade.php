@extends('layouts.marketing')

@section('title', 'My Orders - SharpEdge Marketing')

@section('styles')
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* Statistics Cards */
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }
    
    .stat-info h3 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0.25rem;
        line-height: 1;
    }
    
    .stat-info p {
        color: var(--gray-500);
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0;
    }
    
    /* Main Content Card */
    .main-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }
    
    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .header-left h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0.25rem;
    }
    
    .header-left p {
        color: var(--gray-500);
        font-size: 0.875rem;
        margin-bottom: 0;
    }
    
    /* Header Actions - Search and Add Button */
    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    
    /* Search box on the right side */
    .header-search-box {
        position: relative;
        width: 250px;
    }
    
    .header-search-box input {
        width: 100%;
        padding: 0.625rem 1rem 0.625rem 2.5rem;
        border: 1px solid var(--gray-300);
        border-radius: 0.5rem;
        font-size: 0.875rem;
        color: var(--gray-700);
    }
    
    .header-search-box i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
    }
    
    /* Filters */
    .filters {
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .status-filter {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .status-btn {
        padding: 0.5rem 1rem;
        border: 1px solid var(--gray-300);
        border-radius: 0.5rem;
        background: white;
        color: var(--gray-600);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }
    
    .status-btn.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .status-btn:hover {
        background: var(--primary-light);
        color: white;
        border-color: var(--primary-color);
    }
    
    /* Table Styles - Simple Black Header */
    .table-container {
        padding: 0;
        overflow-x: auto;
    }
    
    .orders-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    /* Black Table Header - SIMPLE VERSION */
    .orders-table thead {
        background-color: #3b4f6b !important;
    }
    
    .orders-table th {
        padding: 1rem 1rem;
        text-align: left;
        font-weight: 600;
        color: white !important;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: none;
        white-space: nowrap;
    }
    
    /* Table body styles */
    .orders-table td {
        padding: 1rem 1rem;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
        color: var(--gray-700);
        font-size: 0.875rem;
        background: white;
    }
    
    .orders-table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .orders-table tbody tr:hover {
        background-color: var(--gray-50);
    }
    
    /* ID Column - Order Code */
    .order-code-box {
        display: inline-block;
        background: rgba(59, 130, 246, 0.1);
        color: var(--primary-color);
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-family: 'Courier New', monospace;
        font-weight: 600;
        font-size: 0.875rem;
        border: 1px solid rgba(59, 130, 246, 0.2);
    }
    
    /* Service Column */
    .service-info {
        display: flex;
        flex-direction: column;
    }
    
    .service-name {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.25rem;
    }
    
    .service-code {
        font-size: 0.75rem;
        color: var(--gray-500);
    }
    
    /* Amount Column */
    .amount-cell {
        font-weight: 700;
        color: var(--dark-color);
        font-size: 1rem;
    }
    
    /* Transaction ID Column */
    .transaction-id-cell {
        font-family: 'Courier New', monospace;
        font-size: 0.8rem;
        color: var(--gray-600);
        word-break: break-all;
        max-width: 150px;
    }
    
    /* INVOICE Column - Updated Styles */
    .invoice-cell {
        text-align: center;
        vertical-align: middle;
    }
    
    .invoice-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: linear-gradient(135deg, #e53e3e, #c53030);
        color: white !important;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(229, 62, 62, 0.2);
    }
    
    .invoice-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(229, 62, 62, 0.3);
        color: white !important;
        text-decoration: none;
    }
    
    .invoice-btn i {
        font-size: 0.875rem;
    }
    
    /* Status Badge with Dot */
    .status-with-dot {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    
    .status-dot.pending {
        background: var(--warning-color);
    }
    
    .status-dot.payment-submitted {
        background: var(--info-color);
    }
    
    .status-dot.payment-approved {
        background: var(--primary-color);
    }
    
    .status-dot.in-progress {
        background: var(--secondary-color);
    }
    
    .status-dot.completed {
        background: var(--success-color);
    }
    
    .status-dot.cancelled {
        background: var(--danger-color);
    }
    
    .status-dot.payment-rejected {
        background: var(--danger-color);
    }
    
    .status-dot.paid {
        background: var(--success-color);
    }
    
    .status-text {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    
    /* Status Text Colors */
    .status-pending-text {
        color: var(--warning-color);
    }
    
    .status-payment-submitted-text {
        color: var(--info-color);
    }
    
    .status-payment-approved-text {
        color: var(--primary-color);
    }
    
    .status-in-progress-text {
        color: var(--secondary-color);
    }
    
    .status-completed-text {
        color: var(--success-color);
    }
    
    .status-cancelled-text {
        color: var(--danger-color);
    }
    
    .status-payment-rejected-text {
        color: var(--danger-color);
    }
    
    .status-paid-text {
        color: var(--success-color);
    }
    
    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 0.5rem;
        border: none;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        text-decoration: none;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        color: white;
    }
    
    /* View button - Blue */
    .action-btn.view {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    }
    
    .action-btn.view:hover {
        background: linear-gradient(135deg, var(--primary-dark), #1d4ed8);
    }
    
    /* Delete button - Red */
    .action-btn.delete {
        background: linear-gradient(135deg, var(--danger-color), #dc2626);
    }
    
    .action-btn.delete:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
    }
    
    /* Document Upload button - Green */
    .action-btn.documents {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    
    .action-btn.documents:hover {
        background: linear-gradient(135deg, #059669, #047857);
    }
    
    /* Add Order Button */
    .btn-add {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none;
    }
    
    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        color: white;
    }
    
    /* Pagination Styles */
    .pagination-container {
        padding: 1.5rem;
        border-top: 1px solid var(--gray-200);
        background: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .pagination-info {
        color: var(--gray-600);
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .pagination {
        display: flex;
        gap: 0.5rem;
        list-style: none;
        padding: 0;
        margin: 0;
        flex-wrap: wrap;
    }
    
    .page-item {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .page-link {
        border: 1px solid var(--gray-300);
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        color: var(--gray-700);
        background: white;
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 0.875rem;
        min-width: 40px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .page-item.active .page-link {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
        font-weight: 600;
    }
    
    .page-item.disabled .page-link {
        color: var(--gray-400);
        cursor: not-allowed;
        background: var(--gray-100);
    }
    
    .page-item .page-link:hover:not(.active):not(.disabled) {
        background: var(--gray-100);
        border-color: var(--gray-400);
    }
    
    .page-item:first-child .page-link,
    .page-item:last-child .page-link {
        padding: 0.5rem 1rem;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }
    
    .empty-state i {
        font-size: 4rem;
        color: var(--gray-300);
        margin-bottom: 1rem;
    }
    
    .empty-state h4 {
        color: var(--gray-600);
        margin-bottom: 0.5rem;
    }
    
    .empty-state p {
        color: var(--gray-500);
        margin-bottom: 1.5rem;
    }
    
    /* Document Upload Modal Styles */
    .document-upload-modal .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .document-upload-modal .modal-title {
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .document-upload-modal .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
    
    .document-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .document-table thead {
        background-color: #f8f9fa;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .document-table th {
        padding: 1rem;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        text-align: left;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background-color: #f8f9fa;
    }
    
    .document-table td {
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
        vertical-align: top;
    }
    
    .document-row {
        transition: background-color 0.2s ease;
    }
    
    .document-row:hover {
        background-color: #f8f9fa;
    }
    
    .document-name {
        font-weight: 500;
        color: #212529;
    }
    
    .document-mandatory {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .document-mandatory.mandatory {
        background-color: #fee2e2;
        color: #dc2626;
    }
    
    .document-mandatory.optional {
        background-color: #dbeafe;
        color: #1d4ed8;
    }
    
    .document-status {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .document-status.pending {
        background-color: #fef3c7;
        color: #d97706;
    }
    
    .document-status.uploaded {
        background-color: #d1fae5;
        color: #059669;
    }

    /* 🔒 New style for verified/approved status */
    .document-status.verified,
    .document-status.approved {
        background-color: #cfe2ff;
        color: #0a58ca;
    }
    
    .upload-section {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .file-input-wrapper {
        position: relative;
    }
    
    .file-input-wrapper input[type="file"] {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        cursor: pointer;
    }
    
    .file-input-wrapper input[type="file"]::file-selector-button {
        padding: 0.25rem 0.75rem;
        background: #f3f4f6;
        border: 1px solid #d1d5db;
        border-radius: 0.25rem;
        cursor: pointer;
        margin-right: 0.5rem;
    }
    
    .upload-btn {
        padding: 0.5rem 1rem;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        border: none;
        border-radius: 0.375rem;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
        width: 100%;
    }
    
    .upload-btn:hover:not(:disabled) {
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
        transform: translateY(-1px);
    }
    
    .upload-btn:disabled {
        background: #9ca3af;
        cursor: not-allowed;
    }
    
    .uploaded-files-list {
        margin-top: 0.5rem;
    }
    
    .uploaded-files-header {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .uploaded-file-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem;
        background-color: #f9fafb;
        border-radius: 0.375rem;
        margin-bottom: 0.25rem;
    }
    
    .uploaded-file-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex: 1;
    }
    
    .uploaded-file-name {
        font-size: 0.875rem;
        color: #374151;
        word-break: break-all;
    }
    
    .uploaded-file-size {
        font-size: 0.75rem;
        color: #9ca3af;
    }
    
    .uploaded-file-actions {
        display: flex;
        gap: 0.25rem;
    }
    
    .file-action-btn {
        padding: 0.25rem 0.5rem;
        border: none;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        text-decoration: none;
    }
    
    .view-file-btn {
        background-color: #dbeafe;
        color: #1d4ed8;
    }
    
    .view-file-btn:hover {
        background-color: #bfdbfe;
        color: #1e40af;
    }
    
    .delete-file-btn {
        background-color: #fee2e2;
        color: #dc2626;
    }
    
    .delete-file-btn:hover {
        background-color: #fecaca;
        color: #b91c1c;
    }
    
    .delete-all-btn {
        padding: 0.25rem 0.5rem;
        background-color: #fee2e2;
        color: #dc2626;
        border: none;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        margin-top: 0.5rem;
    }
    
    .delete-all-btn:hover:not(:disabled) {
        background-color: #fecaca;
    }
    
    .delete-all-btn:disabled {
        background-color: #e9ecef;
        color: #adb5bd;
        cursor: not-allowed;
    }
    
    .upload-progress {
        font-size: 0.875rem;
        color: #6b7280;
        text-align: center;
        padding: 0.5rem;
        background: #f9fafb;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
    }
    
    .upload-progress .progress-count {
        font-weight: 600;
        color: #3b82f6;
    }
    
    .loading-spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 0.5rem;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .stats-cards {
            grid-template-columns: 1fr;
        }
        
        .filters {
            flex-direction: column;
            align-items: stretch;
        }
        
        .header-actions {
            flex-direction: column;
            width: 100%;
            gap: 0.75rem;
        }
        
        .header-search-box {
            width: 100%;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        .orders-table {
            min-width: 1200px;
        }
        
        .transaction-id-cell {
            max-width: 120px;
        }
        
        .pagination-container {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }
        
        .pagination-info {
            text-align: center;
            width: 100%;
        }
        
        .pagination {
            justify-content: center;
            width: 100%;
        }
        
        .upload-section {
            flex-direction: column;
            align-items: stretch;
        }
        
        .file-input-wrapper {
            width: 100%;
        }
        
        .uploaded-file-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .uploaded-file-actions {
            align-self: flex-end;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="header">
        <h1>My Orders</h1>
        <p>View and manage all your service orders</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $orders->total() }}</h3>
                <p>Total Orders</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--warning-color), #e58e0b);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $orders->where('order_status', 'PENDING_PAYMENT')->count() }}</h3>
                <p>Pending Payment</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--secondary-color), #0da271);">
                <i class="fas fa-cogs"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $orders->where('order_status', 'IN_PROGRESS')->count() }}</h3>
                <p>In Progress</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--success-color), #0d9e5d);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $orders->where('order_status', 'PAID')->count() }}</h3>
                <p>Paid</p>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="main-card">
        <div class="card-header">
            <div class="header-left">
                <h2>Order List</h2>
                <p>All service orders created by you</p>
            </div>
            
            <!-- Search and Add Button on the right -->
            <div class="header-actions">
                <form method="GET" action="{{ route('marketing.orders.index') }}" class="header-search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search orders..." 
                           value="{{ request('search') }}" autocomplete="off">
                </form>
                <a href="{{ route('marketing.orders.create') }}" class="btn btn-add">
                    <i class="fas fa-plus"></i> Buy New Services
                </a>
            </div>
        </div>
        
        <!-- Filters - Status Buttons -->
        <div class="filters">
            <div class="status-filter">
                <a href="{{ route('marketing.orders.index', ['search' => request('search')]) }}" 
                   class="status-btn {{ !request('status') || request('status') == 'all' ? 'active' : '' }}">
                    All
                </a>
                <a href="{{ route('marketing.orders.index', ['status' => 'PENDING_PAYMENT', 'search' => request('search')]) }}" 
                   class="status-btn {{ request('status') == 'PENDING_PAYMENT' ? 'active' : '' }}">
                    Pending Payment
                </a>
                <a href="{{ route('marketing.orders.index', ['status' => 'IN_PROGRESS', 'search' => request('search')]) }}" 
                   class="status-btn {{ request('status') == 'IN_PROGRESS' ? 'active' : '' }}">
                    In Progress
                </a>
                <a href="{{ route('marketing.orders.index', ['status' => 'PAID', 'search' => request('search')]) }}" 
                   class="status-btn {{ request('status') == 'PAID' ? 'active' : '' }}">
                    Paid
                </a>
                <a href="{{ route('marketing.orders.index', ['status' => 'CANCELLED', 'search' => request('search')]) }}" 
                   class="status-btn {{ request('status') == 'CANCELLED' ? 'active' : '' }}">
                    Cancelled
                </a>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="table-container">
            @if($orders->count() > 0)
            <table class="orders-table" id="ordersTable">
                <thead>
                    <tr>
                        <th>ORDER ID</th>
                        <th>CLIENT</th>
                        <th>SERVICE</th>
                        <th>DATE</th>
                        <th>AMOUNT</th>
                        <th>TRANSACTION ID</th>
                        <th>INVOICE</th>
                        <th>STATUS</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <!-- ORDER ID Column -->
                        <td>
                            <span class="order-code-box">
                                {{ $order->order_id }}
                            </span>
                        </td>
                        
                        <!-- CLIENT Column -->
                        <td>
                            <strong>{{ $order->client->name ?? 'N/A' }}</strong>
                        </td>
                        
                        <!-- SERVICE Column -->
                        <td>
                            <div class="service-info">
                                <div class="service-name">
                                    {{ $order->items->first()->service->service_name ?? 'N/A' }}
                                </div>
                                <div class="service-code">
                                    {{ $order->items->first()->service->service_code ?? '' }}
                                </div>
                            </div>
                        </td>
                        
                        <!-- DATE Column -->
                        <td>{{ $order->created_at->format('d M, Y') }}</td>
                        
                        <!-- AMOUNT Column -->
                        <td class="amount-cell">
                            <strong>Total:</strong> ₹{{ number_format($order->final_amount, 2) }}

                            @php
                                $paid = $order->paid_amount;
                                $due  = $order->due_amount;
                            @endphp

                            <div class="d-flex flex-column mt-1">
                                <!-- Paid -->
                                <small class="text-success">
                                    <i class="fas fa-check-circle"></i>
                                    Paid: ₹{{ number_format($paid, 2) }}
                                </small>

                                <!-- Due -->
                                @if($due > 0)
                                    <small class="text-danger fw-bold">
                                        Due: ₹{{ number_format($due, 2) }}
                                    </small>
                                @else
                                    <small class="text-success fw-bold">
                                        Fully Paid
                                    </small>
                                @endif
                            </div>
                        </td>
                        
                        <!-- TRANSACTION ID Column -->
                        <td class="transaction-id-cell">
                            @if($order->payments && $order->payments->isNotEmpty())
                                @php
                                    $latestPayment = $order->payments->sortByDesc('created_at')->first();
                                @endphp
                                {{ $latestPayment->transaction_id ?? 'N/A' }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        
                        <!-- INVOICE Column -->
                        <td class="invoice-cell">
                            @if($order->invoice_generated && $order->invoice_path)
                                <a href="{{ asset('storage/'.$order->invoice_path) }}"
                                   class="invoice-btn"
                                   target="_blank"
                                   title="Download Invoice">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                            @elseif($order->order_status === 'PAID' && $order->invoice_generated && !$order->invoice_path)
                                <span class="text-warning" title="Invoice being generated">
                                    <i class="fas fa-clock"></i> Processing
                                </span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        
                        <!-- STATUS Column -->
                        <td>
                            @php
                                $statusMap = [
                                    'PENDING_PAYMENT' => ['dot' => 'pending', 'text' => 'pending-text', 'label' => 'PENDING PAYMENT'],
                                    'PAYMENT_SUBMITTED' => ['dot' => 'payment-submitted', 'text' => 'payment-submitted-text', 'label' => 'PAYMENT SUBMITTED'],
                                    'PAYMENT_APPROVED' => ['dot' => 'payment-approved', 'text' => 'payment-approved-text', 'label' => 'PAYMENT APPROVED'],
                                    'PAYMENT_REJECTED' => ['dot' => 'payment-rejected', 'text' => 'payment-rejected-text', 'label' => 'PAYMENT REJECTED'],
                                    'IN_PROGRESS' => ['dot' => 'in-progress', 'text' => 'in-progress-text', 'label' => 'IN PROGRESS'],
                                    'COMPLETED' => ['dot' => 'completed', 'text' => 'completed-text', 'label' => 'COMPLETED'],
                                    'CANCELLED' => ['dot' => 'cancelled', 'text' => 'cancelled-text', 'label' => 'CANCELLED'],
                                    'PAID' => ['dot' => 'paid', 'text' => 'paid-text', 'label' => 'PAID'],
                                ];
                                $statusInfo = $statusMap[$order->order_status] ?? ['dot' => 'pending', 'text' => 'pending-text', 'label' => str_replace('_', ' ', $order->order_status)];
                            @endphp
                            
                            <div class="status-with-dot">
                                <span class="status-dot {{ $statusInfo['dot'] }}"></span>
                                <span class="status-text status-{{ $statusInfo['text'] }}">
                                    {{ $statusInfo['label'] }}
                                </span>
                            </div>
                        </td>
                        
                        <!-- ACTIONS Column -->
                        <td>
                            <div class="action-buttons">
                                {{-- View Order --}}
                                <a href="{{ route('marketing.orders.show', $order) }}"
                                   class="action-btn view"
                                   title="View Order">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                {{-- Document Upload (only for PAID orders) --}}
                                @if($order->order_status === 'PAID')
                                    <button type="button"
                                            class="action-btn documents upload-documents"
                                            data-order-id="{{ $order->id }}"
                                            data-order-code="{{ $order->order_id }}"
                                            data-client-name="{{ $order->client->name ?? 'N/A' }}"
                                            data-service-name="{{ $order->items->first()->service->service_name ?? 'N/A' }}"
                                            title="Upload Documents">
                                        <i class="fas fa-file-upload"></i>
                                    </button>
                                @endif
                        
                                {{-- Cancel Order (only before payment) --}}
                                @if($order->order_status === 'PENDING_PAYMENT')
                                    <button type="button"
                                            class="action-btn delete delete-order"
                                            data-order-id="{{ $order->id }}"
                                            data-order-code="{{ $order->order_id }}"
                                            title="Cancel Order">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Pagination -->
            <div class="pagination-container">
                <div class="pagination-info">
                    Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} entries
                </div>
                
                @if($orders->hasPages())
                <nav>
                    <ul class="pagination">
                        {{-- Previous Page Link --}}
                        @if($orders->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach($orders->links()->elements[0] as $page => $url)
                            @if($page == $orders->currentPage())
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if($orders->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>
                @endif
            </div>
            
            @else
            <!-- Empty State -->
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <h4>No Orders Found</h4>
                <p>You haven't created any service orders yet.</p>
                @if(request('search') || request('status'))
                    <a href="{{ route('marketing.orders.index') }}" class="btn btn-add">
                        <i class="fas fa-times"></i> Clear Filters
                    </a>
                @else
                    <a href="{{ route('marketing.orders.create') }}" class="btn btn-add">
                        <i class="fas fa-plus"></i> Create Your First Order
                    </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Order Modal -->
<div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Confirm Cancel Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel order <strong id="deleteOrderCode"></strong>?</p>
                <p class="text-danger">This action cannot be undone. The order will be permanently removed.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Order</button>
                <form id="deleteOrderForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Cancel Order</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Document Upload Modal -->
<div class="modal fade document-upload-modal" id="documentUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-upload"></i> Upload Documents
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Order Info -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="mb-2">
                            <strong>Order ID:</strong>
                            <span id="modalOrderId" class="ms-2"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <strong>Client:</strong>
                            <span id="modalClientName" class="ms-2"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <strong>Service:</strong>
                            <span id="modalServiceName" class="ms-2"></span>
                        </div>
                    </div>
                </div>

                <!-- Progress Indicator -->
                <div class="upload-progress d-none">
                    <span class="progress-count">0</span> of <span class="total-docs">0</span> document types uploaded
                </div>

                <!-- Documents Table -->
                <div class="table-responsive">
                    <table class="document-table">
                        <thead>
                            <tr>
                                <th width="30%">Document Name</th>
                                <th width="15%">Type</th>
                                <th width="15%">Status</th>
                                <th width="40%">Files & Actions</th>
                            </tr>
                        </thead>
                        <tbody id="documentsTableBody">
                            <!-- Documents will be loaded here -->
                        </tbody>
                    </table>
                </div>

                <!-- Loading State -->
                <div id="documentsLoading" class="text-center py-4">
                    <div class="loading-spinner"></div>
                    <p class="mt-2">Loading documents...</p>
                </div>

                <!-- Empty State -->
                <div id="noDocuments" class="text-center py-4 d-none">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <h5>No documents required</h5>
                    <p class="text-muted">No documents are required for this service.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-credit-card"></i> Add Payment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- Order Info -->
                    <div class="order-summary bg-light p-3 rounded mb-3">
                        <div class="row">
                            <div class="col-6">
                                <strong>Order:</strong>
                                <span id="paymentOrderCode"></span>
                            </div>
                            <div class="col-6">
                                <strong>Due Amount:</strong>
                                <span id="paymentDueAmount" class="text-danger fw-bold"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Amount -->
                    <div class="mb-3">
                        <label class="form-label">Payment Amount *</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" 
                                   step="0.01"
                                   name="amount" 
                                   id="paymentAmount"
                                   class="form-control" 
                                   required>
                        </div>
                        <small class="text-muted">Enter amount to pay (max: <span id="maxAmount"></span>)</small>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-3">
                        <label class="form-label">Payment Method *</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="">Select Method</option>
                            <option value="Cash">Cash</option>
                            <option value="UPI">UPI</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>

                    <!-- Payment Date -->
                    <div class="mb-3">
                        <label class="form-label">Payment Date *</label>
                        <input type="date" 
                               name="payment_date" 
                               class="form-control" 
                               value="{{ date('Y-m-d') }}"
                               required>
                    </div>

                    <!-- Transaction ID -->
                    <div class="mb-3">
                        <label class="form-label">Transaction ID (Optional)</label>
                        <input type="text" 
                               name="transaction_id" 
                               class="form-control"
                               placeholder="Enter transaction reference">
                    </div>

                    <!-- Payment Proof -->
                    <div class="mb-3">
                        <label class="form-label">Payment Proof</label>
                        <input type="file" 
                               name="payment_proof" 
                               class="form-control"
                               accept=".jpg,.jpeg,.png,.pdf">
                        <small class="text-muted">Max 2MB (JPG, PNG, PDF)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitPayment">
                        <i class="fas fa-paper-plane"></i> Add Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payment Status Modal -->
<div class="modal fade" id="paymentStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle"></i> Payment Status
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div class="spinner-border text-primary" role="status" id="statusSpinner">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="paymentStatusContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- ✅ DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Toastr
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    // Show success/error messages from session
    @if(session('success'))
        toastr.success('{{ session('success') }}');
    @endif
    @if(session('error'))
        toastr.error('{{ session('error') }}');
    @endif
    @if($errors->any())
        @foreach($errors->all() as $error)
            toastr.error('{{ $error }}');
        @endforeach
    @endif

    // ✅ DataTables Initialization
    $('#ordersTable').DataTable({
        order: [[0, 'desc']],
        paging: false,
        searching: false,
        info: false,
        columnDefs: [
            {
                targets: 0, // ORDER ID column
                type: 'string',
                render: function(data, type, row) {
                    if (type === 'sort' || type === 'type') {
                        // Extract numeric part from ORD-YYYYMMDD-XXXXX
                        const match = data.match(/ORD-(\d{8})-(.+)/);
                        if (match) {
                            return match[1] + '-' + match[2];
                        }
                        return data;
                    }
                    return data;
                }
            }
        ]
    });

    // Delete Order Button Click
    $(document).on('click', '.delete-order', function() {
        const orderId = $(this).data('order-id');
        const orderCode = $(this).data('order-code');
        
        $('#deleteOrderCode').text(orderCode);
        $('#deleteOrderForm').attr('action', "{{ route('marketing.orders.destroy', ':id') }}".replace(':id', orderId));
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteOrderModal'));
        deleteModal.show();
    });

    // Handle delete form submission
    $('#deleteOrderForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const url = form.attr('action');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: form.serialize() + '&_method=DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#deleteOrderModal').modal('hide');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Failed to cancel order. Please try again.');
                console.error('Delete error:', xhr.responseText);
            }
        });
    });

    // Auto-submit search on Enter key
    $('input[name="search"]').on('keypress', function(e) {
        if (e.which === 13) {
            $(this).closest('form').submit();
        }
    });

    // Auto-hide toastr messages after 5 seconds
    setTimeout(function() {
        $('.toast').fadeOut('slow');
    }, 5000);

    // Document Upload Functionality
    let currentOrderId = null;

    $(document).on('click', '.upload-documents', function() {
        const orderId = $(this).data('order-id');
        const orderCode = $(this).data('order-code');
        const clientName = $(this).data('client-name');
        const serviceName = $(this).data('service-name');
        
        currentOrderId = orderId;
        
        $('#modalOrderId').text(orderCode);
        $('#modalClientName').text(clientName);
        $('#modalServiceName').text(serviceName);
        
        $('#documentsLoading').removeClass('d-none');
        $('#noDocuments').addClass('d-none');
        $('#documentsTableBody').empty();
        $('.upload-progress').addClass('d-none');
        
        const documentModal = new bootstrap.Modal(document.getElementById('documentUploadModal'));
        documentModal.show();
        
        loadOrderDocuments(orderId);
    });

    function loadOrderDocuments(orderId) {
        $.ajax({
            url: `/marketing/orders/${orderId}/documents/load`,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#documentsLoading').addClass('d-none');
                
                if (response.requirements.length === 0) {
                    $('#noDocuments').removeClass('d-none');
                    return;
                }
                
                const uploadedCount = Object.keys(response.uploaded_docs).length;
                const totalCount = response.requirements.length;
                
                if (totalCount > 0) {
                    $('.upload-progress').removeClass('d-none');
                    $('.progress-count').text(uploadedCount);
                    $('.total-docs').text(totalCount);
                }
                
                renderDocumentsTable(response.requirements, response.uploaded_docs, orderId);
            },
            error: function(xhr) {
                $('#documentsLoading').addClass('d-none');
                toastr.error('Failed to load documents. Please try again.');
                console.error('Load documents error:', xhr.responseText);
            }
        });
    }

    function renderDocumentsTable(requirements, uploadedDocs, orderId) {
        const tableBody = $('#documentsTableBody');
        tableBody.empty();
        
        requirements.forEach(requirement => {
            const documentId = requirement.document_master_id;
            const uploadedDoc = uploadedDocs[documentId];
            const isUploaded = uploadedDoc && uploadedDoc.files && uploadedDoc.files.length > 0;
            const files = isUploaded ? uploadedDoc.files : [];
            const isLocked = uploadedDoc && ['VERIFIED', 'APPROVED'].includes(uploadedDoc.status);
            
            let filesListHtml = '';
            if (files.length > 0) {
                filesListHtml = `
                    <div class="uploaded-files-header">
                        <span>Uploaded Files (${files.length}):</span>
                    </div>
                    <div class="uploaded-files-list">
                `;
                
                files.forEach(file => {
                    const fileSize = formatFileSize(file.file_size);
                    
                    filesListHtml += `
                        <div class="uploaded-file-item" data-file-id="${file.id}">
                            <div class="uploaded-file-info">
                                <i class="fas fa-file ${getFileIcon(file.mime_type)}"></i>
                                <div>
                                    <div class="uploaded-file-name">${file.file_name}</div>
                                    <div class="uploaded-file-size">${fileSize}</div>
                                </div>
                            </div>
                            <div class="uploaded-file-actions">
                                <a href="/storage/${file.file_path}" 
                                   class="file-action-btn view-file-btn" 
                                   target="_blank"
                                   title="View File">
                                    <i class="fas fa-eye"></i>
                                </a>
                                ${!isLocked ? `
                                <button type="button" 
                                        class="file-action-btn delete-file-btn"
                                        data-file-id="${file.id}"
                                        title="Delete File">
                                    <i class="fas fa-trash"></i>
                                </button>
                                ` : ''}
                            </div>
                        </div>
                    `;
                });
                
                filesListHtml += `</div>`;
                
                if (!isLocked) {
                    filesListHtml += `
                        <button type="button" 
                                class="delete-all-btn"
                                data-order-document-id="${uploadedDoc.id}"
                                data-document-master-id="${documentId}">
                            <i class="fas fa-trash"></i> Delete All Files
                        </button>
                    `;
                } else {
                    filesListHtml += `
                        <div class="text-muted small mt-2">
                            <i class="fas fa-lock"></i> Verified documents cannot be modified
                        </div>
                    `;
                }
            }
            
            let statusDisplay = 'Pending';
            let statusClass = 'pending';
            if (isUploaded) {
                statusDisplay = 'Uploaded';
                statusClass = 'uploaded';
            }
            if (uploadedDoc && uploadedDoc.status) {
                statusDisplay = uploadedDoc.status;
                if (uploadedDoc.status === 'VERIFIED' || uploadedDoc.status === 'APPROVED') {
                    statusClass = 'verified';
                }
            }
            
            const row = `
                <tr class="document-row" id="docRow_${documentId}">
                    <td>
                        <div class="document-name">
                            ${requirement.document_master.document_name}
                            ${requirement.description ? `<br><small class="text-muted">${requirement.description}</small>` : ''}
                        </div>
                    </td>
                    <td>
                        <span class="document-mandatory ${requirement.is_mandatory ? 'mandatory' : 'optional'}">
                            ${requirement.is_mandatory ? 'Mandatory' : 'Optional'}
                        </span>
                    </td>
                    <td>
                        <span class="document-status ${statusClass}">
                            ${statusDisplay}
                        </span>
                        ${isUploaded ? `<br><small>${files.length} file(s)</small>` : ''}
                    </td>
                    <td>
                        <div class="upload-section">
                            ${filesListHtml}
                            ${!isLocked ? `
                            <div class="file-input-wrapper mt-2">
                                <input type="file" 
                                       id="files_${documentId}" 
                                       class="document-files"
                                       data-document-master-id="${documentId}"
                                       multiple
                                       accept=".pdf,.jpg,.jpeg,.png,.zip,.rar,.xlsm,.doc,.docx,.xls,.xlsx">
                            </div>
                            <button type="button" 
                                    class="upload-btn upload-doc-files"
                                    data-order-id="${orderId}"
                                    data-document-master-id="${documentId}"
                                    ${isUploaded ? 'data-order-document-id="' + uploadedDoc.id + '"' : ''}>
                                ${isUploaded ? 'Add More Files' : 'Upload Files'}
                            </button>
                            ` : ''}
                            <div class="upload-status mt-1" id="status_${documentId}" style="font-size: 0.75rem;"></div>
                        </div>
                    </td>
                </tr>
            `;
            
            tableBody.append(row);
            
            if (isLocked) {
                $(`#files_${documentId}`).prop('disabled', true);
            }
        });
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function getFileIcon(mimeType) {
        if (mimeType.includes('pdf')) return 'text-danger';
        if (mimeType.includes('image')) return 'text-success';
        if (mimeType.includes('zip') || mimeType.includes('rar')) return 'text-warning';
        if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return 'text-success';
        if (mimeType.includes('word')) return 'text-primary';
        return 'text-secondary';
    }

    $(document).on('click', '.upload-doc-files', function() {
        const orderId = $(this).data('order-id');
        const documentMasterId = $(this).data('document-master-id');
        const fileInput = $(`#files_${documentMasterId}`)[0];
        
        const row = $(this).closest('.document-row');
        const statusSpan = row.find('.document-status');
        if (statusSpan.text().includes('VERIFIED') || statusSpan.text().includes('APPROVED')) {
            toastr.warning('This document is verified and cannot be modified.');
            return;
        }
        
        if (!fileInput.files.length) {
            toastr.warning('Please select at least one file');
            return;
        }
        
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('document_master_id', documentMasterId);
        for (let i = 0; i < fileInput.files.length; i++) {
            formData.append('files[]', fileInput.files[i]);
        }
        
        const uploadBtn = $(this);
        const originalText = uploadBtn.text();
        uploadBtn.prop('disabled', true);
        uploadBtn.html('<span class="loading-spinner"></span> Uploading...');
        
        const statusDiv = $(`#status_${documentMasterId}`);
        statusDiv.text(`Uploading ${fileInput.files.length} file(s)...`).css('color', '#3b82f6');
        
        $.ajax({
            url: '/marketing/orders/documents/upload',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    statusDiv.text('✓ Files uploaded successfully').css('color', '#059669');
                    fileInput.value = '';
                    setTimeout(() => { loadOrderDocuments(orderId); }, 1000);
                    toastr.success('Files uploaded successfully!');
                }
            },
            error: function(xhr) {
                uploadBtn.prop('disabled', false);
                uploadBtn.text(originalText);
                statusDiv.text('✗ Upload failed').css('color', '#dc2626');
                let errorMessage = 'Upload failed. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                toastr.error(errorMessage);
            }
        });
    });

    $(document).on('click', '.delete-file-btn', function() {
        const fileId = $(this).data('file-id');
        const row = $(this).closest('.document-row');
        const statusSpan = row.find('.document-status');
        if (statusSpan.text().includes('VERIFIED') || statusSpan.text().includes('APPROVED')) {
            toastr.warning('Verified documents cannot be modified.');
            return;
        }
        if (!confirm('Are you sure you want to delete this file?')) return;
        const fileItem = $(this).closest('.uploaded-file-item');
        fileItem.addClass('opacity-50');
        $.ajax({
            url: `/marketing/orders/documents/file/${fileId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('File deleted successfully!');
                    setTimeout(() => { loadOrderDocuments(currentOrderId); }, 500);
                }
            },
            error: function(xhr) {
                fileItem.removeClass('opacity-50');
                let msg = "Failed to delete file. Please try again.";
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                else if (xhr.status === 419) msg = "Session expired. Please refresh the page and try again.";
                else if (xhr.status === 403) msg = "You don't have permission to delete this file.";
                else if (xhr.status === 404) msg = "File not found. It may have already been deleted.";
                else if (xhr.status === 500) msg = "Server error. Please try again later.";
                toastr.error(msg);
            }
        });
    });

    $(document).on('click', '.delete-all-btn', function() {
        const orderDocumentId = $(this).data('order-document-id');
        const row = $(this).closest('.document-row');
        const statusSpan = row.find('.document-status');
        if (statusSpan.text().includes('VERIFIED') || statusSpan.text().includes('APPROVED')) {
            toastr.warning('Verified documents cannot be modified.');
            return;
        }
        if (!confirm('Are you sure you want to delete all files for this document?')) return;
        const deleteBtn = $(this);
        deleteBtn.prop('disabled', true);
        deleteBtn.html('<span class="loading-spinner"></span> Deleting...');
        $.ajax({
            url: `/marketing/orders/documents/${orderDocumentId}/delete-all`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('All files deleted successfully!');
                    setTimeout(() => { loadOrderDocuments(currentOrderId); }, 500);
                }
            },
            error: function(xhr) {
                deleteBtn.prop('disabled', false);
                deleteBtn.html('<i class="fas fa-trash"></i> Delete All Files');
                let msg = "Failed to delete files. Please try again.";
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                else if (xhr.status === 419) msg = "Session expired. Please refresh the page and try again.";
                else if (xhr.status === 403) msg = "You don't have permission to delete these files.";
                else if (xhr.status === 404) msg = "Document not found.";
                else if (xhr.status === 500) msg = "Server error. Please try again later.";
                toastr.error(msg);
            }
        });
    });

    $(document).on('change', '.document-files', function() {
        const documentMasterId = $(this).data('document-master-id');
        const uploadBtn = $(this).closest('.upload-section').find('.upload-doc-files');
        const fileCount = this.files.length;
        if (fileCount > 0) {
            uploadBtn.prop('disabled', false);
            uploadBtn.text(`Upload ${fileCount} File(s)`);
        } else {
            uploadBtn.prop('disabled', true);
            uploadBtn.text('Upload Files');
        }
    });

    $('#documentUploadModal').on('hidden.bs.modal', function() {
        $('#documentsTableBody').empty();
        $('#documentsLoading').removeClass('d-none');
        $('#noDocuments').addClass('d-none');
        $('.upload-progress').addClass('d-none');
        currentOrderId = null;
    });

    $(document).ajaxError(function(event, xhr, settings, error) {
        if (settings.url.includes('/documents/file/') || settings.url.includes('/documents/delete-all')) {
            console.log("AJAX ERROR DETECTED:");
            console.log("URL:", settings.url);
            console.log("Method:", settings.type);
            console.log("Status:", xhr.status);
            console.log("Response:", xhr.responseText);
        }
    });

    // Payment Modal Handler
    $(document).on('click', '.add-payment', function() {
        const orderId = $(this).data('order-id');
        const orderCode = $(this).data('order-code');
        const dueAmount = $(this).data('due-amount');
        
        $('#paymentOrderCode').text(orderCode);
        $('#paymentDueAmount').text('₹' + Number(dueAmount).toFixed(2));
        $('#maxAmount').text('₹' + Number(dueAmount).toFixed(2));
        $('#paymentAmount').attr('max', dueAmount);
        $('#paymentAmount').val(dueAmount);
        $('#paymentForm').attr('action', "{{ route('marketing.orders.pay-due', ':id') }}".replace(':id', orderId));
        const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        paymentModal.show();
    });

    $('#paymentAmount').on('input', function() {
        const max = parseFloat($(this).attr('max'));
        const value = parseFloat($(this).val());
        if (value > max) {
            $(this).val(max.toFixed(2));
            toastr.warning('Amount cannot exceed due amount');
        }
    });

    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#submitPayment');
        const formData = new FormData(this);
        const amount = parseFloat($('#paymentAmount').val());
        const maxAmount = parseFloat($('#paymentAmount').attr('max'));
        
        if (isNaN(amount) || amount <= 0) {
            toastr.error('Please enter a valid amount');
            return;
        }
        if (amount > maxAmount) {
            toastr.error('Amount cannot exceed due amount');
            return;
        }
        
        submitBtn.prop('disabled', true);
        submitBtn.html('<span class="loading-spinner"></span> Submitting...');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#paymentModal').modal('hide');
                    toastr.success(response.message);
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                } else {
                    toastr.error(response.message || 'Payment failed');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Payment failed. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors) {
                        errorMessage = Object.values(errors).flat().join('\n');
                    }
                } else if (xhr.status === 403) {
                    errorMessage = 'You are not authorized to make this payment.';
                } else if (xhr.status === 404) {
                    errorMessage = 'Order not found.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error. Please try again later.';
                }
                toastr.error(errorMessage);
                console.error('Payment error:', xhr.responseText);
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                submitBtn.html('<i class="fas fa-paper-plane"></i> Add Payment');
            }
        });
    });

    $(document).on('click', '.check-status', function() {
        const orderId = $(this).data('order-id');
        $('#paymentStatusContent').html('<p class="text-center">Loading payment status...</p>');
        $('#statusSpinner').show();
        const statusModal = new bootstrap.Modal(document.getElementById('paymentStatusModal'));
        statusModal.show();
        $.ajax({
            url: "{{ route('marketing.orders.payment-status', ':id') }}".replace(':id', orderId),
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#statusSpinner').hide();
                let statusHtml = `
                    <div class="payment-status-details">
                        <table class="table table-sm">
                            <tr><th>Order Status:</th><td><span class="badge ${getStatusBadgeClass(response.order_status)}">${response.order_status}</span></td></tr>
                            <tr><th>Payment Status:</th><td><span class="badge ${getPaymentStatusBadgeClass(response.payment_status)}">${response.payment_status}</span></td></tr>
                            <tr><th>Total Amount:</th><td>₹${Number(response.total_amount).toFixed(2)}</td></tr>
                            <tr><th>Paid Amount:</th><td>₹${Number(response.paid_amount).toFixed(2)}</td></tr>
                            <tr><th>Due Amount:</th><td class="${response.due_amount > 0 ? 'text-danger' : 'text-success'} fw-bold">₹${Number(response.due_amount).toFixed(2)}</td></tr>
                        </table>
                        ${response.has_pending_payment ? `<div class="alert alert-warning mt-3"><i class="fas fa-clock"></i> Your payment is pending admin approval. Please wait for verification.</div>` : ''}
                    </div>
                `;
                $('#paymentStatusContent').html(statusHtml);
            },
            error: function(xhr) {
                $('#statusSpinner').hide();
                $('#paymentStatusContent').html(`<div class="alert alert-danger">Failed to load payment status. Please try again.</div>`);
                console.error('Status check error:', xhr.responseText);
            }
        });
    });

    function getStatusBadgeClass(status) {
        const classes = {
            'PAID': 'bg-success',
            'PARTIAL_PAYMENT': 'bg-warning text-dark',
            'PENDING_PAYMENT': 'bg-danger',
            'PAYMENT_SUBMITTED': 'bg-info',
            'CREDIT_PENDING': 'bg-secondary'
        };
        return classes[status] || 'bg-secondary';
    }

    function getPaymentStatusBadgeClass(status) {
        const classes = {
            'Approved': 'bg-success',
            'Pending': 'bg-warning text-dark',
            'Rejected': 'bg-danger'
        };
        return classes[status] || 'bg-secondary';
    }

    $('#paymentModal').on('hidden.bs.modal', function() {
        $('#paymentForm')[0].reset();
        $('#paymentAmount').val('');
        $('#submitPayment').prop('disabled', false);
        $('#submitPayment').html('<i class="fas fa-paper-plane"></i> Add Payment');
    });
});
</script>
@endsection