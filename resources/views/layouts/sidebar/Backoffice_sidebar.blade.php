@php
    $user = Auth::user();

    // Latest 3 notices for sidebar list (Backoffice role)
    $sidebarNotices = \App\Models\Notice::active()
        ->forRole('backoffice')
        ->currentlyVisible()
        ->latest()
        ->take(3)
        ->get();

    // Total notice count for badge (all active + visible)
    $noticeCount = \App\Models\Notice::active()
        ->forRole('backoffice')
        ->currentlyVisible()
        ->count();

    // Task count for My Tasks badge
    $newTaskCount = \App\Models\Task::where('assigned_to', auth()->user()->staff->id ?? 0)
        ->where('status', 'assigned')
        ->count();
    $hasNewTasks = $newTaskCount > 0;

    // Overdue follow‑ups count for badge
    $overdueFollowUpsCount = \App\Models\PaymentFollowUp::whereDate('next_follow_up_date', '<', \Carbon\Carbon::today())->count();
@endphp

<nav class="sidebar" id="sidebar">
    <div class="logo">
        <div class="logo-square" style="width:55px; height:55px;">
            <img src="{{ asset('storage/SRP_LOGO/WhatsApp Image 2026-04-13 at 18.39.41.jpeg') }}" 
                 alt="Logo" 
                 style="height: 60px;width: 89px;">
        </div>
        <h1 class="logo-text">The SharpEdge India</h1>
    </div>

    <div class="user-info-sidebar d-flex align-items-center">
        <div class="user-avatar-sidebar">
            {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
        </div>
        <div>
            <p class="mb-0 fw-bold user-name">{{ $user->name ?? 'User' }}</p>
            <p class="mb-0 small user-role">Back Office Executive</p>
        </div>
    </div>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('backoffice.dashboard') ? 'active' : '' }}"
               href="{{ route('backoffice.dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        {{-- Partner Websites Menu Item --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('backoffice.partner-websites.*') ? 'active' : '' }}"
               href="{{ route('backoffice.partner-websites.index') }}">
                <i class="bi bi-link-45deg"></i>
                <span>Partner Websites</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link d-flex align-items-center
                {{ request()->routeIs('backoffice.tasks.*') ? 'active' : '' }}
                {{ $hasNewTasks ? 'blink-task' : '' }}"
               href="{{ route('backoffice.tasks.index') }}">
                <i class="bi bi-list-task me-2"></i>
                <span class="flex-grow-1">My Tasks</span>
                @if($newTaskCount > 0)
                    <span class="badge bg-danger ms-auto">{{ $newTaskCount }}</span>
                @endif
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('backoffice.documents.*') ? 'active' : '' }}"
               href="{{ route('backoffice.documents.index') }}">
                <i class="bi bi-folder"></i>
                <span>Client Documents</span>
            </a>
        </li>

        {{-- NEW: Payment Follow-ups --}}
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center 
                      {{ request()->routeIs('backoffice.payment-follow-ups.*') ? 'active' : '' }}"
               href="{{ route('backoffice.payment-follow-ups.index') }}">
                <i class="bi bi-credit-card me-2"></i>
                <span class="flex-grow-1">Payment Follow‑ups</span>
                @if($overdueFollowUpsCount > 0)
                    <span class="badge bg-danger ms-auto">{{ $overdueFollowUpsCount }}</span>
                @endif
            </a>
        </li>

        {{-- Computations Main Menu Item --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('backoffice.computations.index') ? 'active' : '' }}"
               href="{{ route('backoffice.computations.index') }}">
                <i class="bi bi-calculator-fill"></i>
                <span>Computations</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('backoffice.leave-requests.*') ? 'active' : '' }}"
               href="{{ route('backoffice.leave-requests.index') }}">
                <i class="bi bi-calendar2-check"></i>
                <span>Leave Requests</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('backoffice.holidays.calendar') ? 'active' : '' }}"
               href="{{ route('backoffice.holidays.calendar') }}">
                <i class="bi bi-calendar3"></i>
                <span>Holiday Calendar</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('backoffice.support-tickets.*') ? 'active' : '' }}"
               href="{{ route('backoffice.support-tickets.index') }}">
                <i class="bi bi-ticket"></i>
                <span>Support Tickets</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link d-flex align-items-center {{ request()->routeIs('backoffice.notices.*') ? 'active' : '' }}"
               href="{{ route('backoffice.notices.index') }}">
                <i class="bi bi-bell"></i>
                <span>Notices</span>
                @if($noticeCount > 0)
                    <span class="badge bg-danger ms-auto">{{ $noticeCount }}</span>
                @endif
            </a>
        </li>
    </ul>

    {{-- Latest Notices Section --}}
    @if($sidebarNotices->count() > 0)
        <div class="sidebar-notices-section mt-auto">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="text-white mb-0" style="font-size: 0.85rem; font-weight: 600;">
                    Latest Notices
                </h6>
                <a href="{{ route('backoffice.notices.index') }}"
                   style="font-size: 0.75rem; text-decoration: none;">
                    View All
                </a>
            </div>

            <div class="sidebar-notices-list">
                @foreach($sidebarNotices as $notice)
                    <a href="{{ route('backoffice.notices.index') }}"
                       class="sidebar-notice-item d-block text-decoration-none">
                        <div class="sidebar-notice-title">
                            {{ $notice->title }}
                        </div>
                        <div class="sidebar-notice-meta">
                            {{ $notice->start_date ? $notice->start_date->format('M d, Y') : $notice->created_at->format('M d, Y') }}
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</nav>


    /* (existing styles remain unchanged) */


<style>
    @keyframes blinkGlowForce {
        0% { opacity: 0; }
        50% { opacity: 1; }
        100% { opacity: 0; }
    }

    .nav-link.blink-task {
        position: relative;
        overflow: hidden;
    }
    .nav-link.blink-task::before {
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(13, 110, 253, 0.25);
        box-shadow: 0 0 14px rgba(13, 110, 253, 0.85);
        opacity: 0;
        animation: blinkGlowForce 1.2s infinite;
        border-radius: 8px;
        z-index: -1;
    }

    /* Sidebar Notices Section */
    .sidebar-notices-section {
        padding: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(0, 0, 0, 0.08);
    }

    .sidebar-notices-list {
        max-height: 200px;
        overflow-y: auto;
    }

    .sidebar-notice-item {
        padding: 0.65rem;
        background: rgba(255, 255, 255, 0.06);
        border-radius: 8px;
        margin-bottom: 0.5rem;
        border-left: 3px solid #0d6efd;
        transition: 0.2s ease;
    }

    .sidebar-notice-item:hover {
        background: rgba(255, 255, 255, 0.12);
    }

    .sidebar-notice-title {
        color: #fff;
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
        color: rgba(255, 255, 255, 0.6);
    }

    /* Badge */
    .nav-link .badge {
        font-size: 0.65rem;
        padding: 0.15rem 0.4rem;
        border-radius: 10px;
        min-width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @keyframes sidebarBlink {
        0%   { background-color: rgba(13,110,253,0.15); }
        50%  { background-color: rgba(13,110,253,0.45); }
        100% { background-color: rgba(13,110,253,0.15); }
    }

    .blink-task {
        animation: sidebarBlink 1.2s infinite;
        border-radius: 6px;
    }
</style>