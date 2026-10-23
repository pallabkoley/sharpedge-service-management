<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <div class="logo-icon">SE</div>
            <div class="logo-text">SharpEdge</div>
        </div>
    </div>
    
    <!-- User Profile -->
    <div class="user-profile">
        <div class="user-avatar">JM</div>
        <div class="user-info">
            <h4>John Marketing</h4>
            <p>Marketing Staff</p>
        </div>
    </div>
    
    <!-- Menu Items -->
    <div class="sidebar-menu">
        <a href="{{ route('marketing.dashboard') }}" class="menu-item {{ request()->routeIs('marketing.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('marketing.clients.index') }}" class="menu-item {{ request()->routeIs('marketing.clients.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Clients</span>
        </a>
        <a href="{{ route('marketing.clients.index') }}" class="menu-item" data-bs-toggle="modal" data-bs-target="#addClientModal">
            <i class="fas fa-user-plus"></i>
            <span>Add Client</span>
        </a>
        <a href="{{ route('marketing.clients.index') }}" class="menu-item">
            <i class="fas fa-list"></i>
            <span>Client List</span>
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-shopping-cart"></i>
            <span>Buy Service</span>
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-file-invoice"></i>
            <span>Invoices</span>
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-credit-card"></i>
            <span>Payments</span>
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-ticket-alt"></i>
            <span>Tickets</span>
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </div>
</div>