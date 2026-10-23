@php
    // Get the authenticated user
    $user = Auth::user();

    // Generate initials for avatar
    $initials = '';

    if ($user && $user->name) {

        $nameParts = explode(' ', $user->name);

        foreach ($nameParts as $part) {

            if (!empty($part)) {
                $initials .= strtoupper(substr($part, 0, 1));
            }
        }

        // Limit to 2 initials
        $initials = substr($initials, 0, 2);

    } else {

        $initials = 'U';
    }


    // Get user role name
    $roleName = $user && $user->role
        ? ucfirst($user->role->name)
        : 'Marketing Staff';


    // Generate profile image URL
    $profileImageUrl = null;

    if ($user && $user->profile_image) {

        // Check if profile_image contains the full path or just filename
        if (strpos($user->profile_image, '/') !== false) {

            // Already has path structure
            $profileImageUrl = asset(
                'storage/' . $user->profile_image
            );

        } else {

            // Just filename
            $profileImageUrl = asset(
                'storage/Staff/profile_images/' . $user->profile_image
            );
        }
    }


    // Get latest notices for sidebar
    $sidebarNotices = \App\Models\Notice::active()
        ->forRole('marketing')
        ->currentlyVisible()
        ->latest()
        ->take(3)
        ->get();
@endphp


<!-- =========================================================
     SIDEBAR
========================================================= -->

<div class="sidebar" id="sidebar">


    <!-- =====================================================
         SIDEBAR HEADER
    ====================================================== -->

    <div class="sidebar-header">

        <div class="logo d-flex flex-column align-items-center">

            <img
                src="{{ asset('storage/SRP_LOGO/WhatsApp Image 2026-04-13 at 18.39.41.jpeg') }}"
                alt="Logo"
                style="height:auto;width:191px;border-radius:6px;"
            >

            <div class="logo-text mt-2">
                The
                <span style="color:#93c5fd;">
                    SharpEdge
                </span>
                India
            </div>

        </div>

    </div>


    <!-- =====================================================
         USER PROFILE
    ====================================================== -->

    <div class="user-profile">

        <div class="sidebar-user-avatar">

            @if($profileImageUrl)

                <img
                    src="{{ $profileImageUrl }}"
                    alt="Profile"
                    class="sidebar-user-avatar-img"
                    onerror="this.style.display='none'; this.parentElement.innerHTML='<span>{{ $initials }}</span>';"
                >

            @else

                <span>
                    {{ $initials }}
                </span>

            @endif

        </div>


        <div class="user-info">

            <h4>
                {{ $user->name ?? 'User' }}
            </h4>

            <p>
                {{ $roleName }}
            </p>

        </div>

    </div>


    <!-- =====================================================
         MENU ITEMS
    ====================================================== -->

    <div class="sidebar-menu">


        <!-- =================================================
             DASHBOARD
        ================================================== -->

        <a
            href="{{ route('marketing.dashboard') }}"
            class="menu-item {{ request()->routeIs('marketing.dashboard') ? 'active' : '' }}"
        >
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>


        <!-- =================================================
             CLIENTS
        ================================================== -->

        <a
            href="{{ route('marketing.clients.index') }}"
            class="menu-item {{ request()->routeIs('marketing.clients.*') ? 'active' : '' }}"
        >
            <i class="fas fa-users"></i>
            <span>Clients</span>
        </a>


        <!-- =================================================
             MEETINGS
        ================================================== -->

        <a
            href="{{ route('marketing.meetings.index') }}"
            class="menu-item {{ request()->routeIs('marketing.meetings.*') ? 'active' : '' }}"
        >
            <i class="fas fa-handshake"></i>
            <span>Meetings</span>
        </a>


        <!-- =================================================
             ORDERS
        ================================================== -->

        <a
            href="{{ route('marketing.orders.index') }}"
            class="menu-item {{ request()->routeIs('marketing.orders.*') ? 'active' : '' }}"
        >
            <i class="fas fa-shopping-cart"></i>
            <span>Orders</span>
        </a>


        <!-- =================================================
             DOCUMENTS
        ================================================== -->

        <a
            href="{{ route('marketing.documents.index') }}"
            class="menu-item {{ request()->routeIs('marketing.documents.*') ? 'active' : '' }}"
        >
            <i class="fas fa-folder-open"></i>
            <span>Documents</span>
        </a>


        <!-- =================================================
             LEAVE REQUESTS
        ================================================== -->

        <a
            href="{{ route('marketing.leave-requests.index') }}"
            class="menu-item {{ request()->routeIs('marketing.leave-requests.*') ? 'active' : '' }}"
        >
            <i class="fas fa-calendar-alt"></i>
            <span>Leave Requests</span>
        </a>


        <!-- =================================================
             HOLIDAY CALENDAR
        ================================================== -->

        <a
            href="{{ route('marketing.holidays.calendar') }}"
            class="menu-item {{ request()->routeIs('marketing.holidays.calendar') ? 'active' : '' }}"
        >
            <i class="fas fa-calendar-check"></i>
            <span>Holiday Calendar</span>
        </a>


        <!-- =================================================
             PAYMENTS
        ================================================== -->

        <a
            href="{{ route('marketing.payments.index') }}"
            class="menu-item {{ request()->routeIs('marketing.payments.*') ? 'active' : '' }}"
        >
            <i class="fas fa-credit-card"></i>
            <span>Payments</span>
        </a>


        <!-- =================================================
             PAYMENT FOLLOW UPS
        ================================================== -->

        <a
            href="{{ route('marketing.payment-follow-ups.index') }}"
            class="menu-item {{ request()->routeIs('marketing.payment-follow-ups.*') ? 'active' : '' }}"
        >
            <i class="fas fa-phone-volume"></i>
            <span>Payment Follow Ups</span>
        </a>


        <!-- =================================================
             NOTICES
        ================================================== -->

        <a
            href="{{ route('marketing.notices.index') }}"
            class="menu-item {{ request()->routeIs('marketing.notices.*') ? 'active' : '' }}"
        >
            <i class="fas fa-bell"></i>

            <span>
                Notices
            </span>

            @if($sidebarNotices->count() > 0)

                <span class="badge bg-danger ms-auto">
                    {{ $sidebarNotices->count() }}
                </span>

            @endif

        </a>


        <!-- =================================================
             SUPPORT TICKETS
        ================================================== -->

        <a
            href="{{ route('marketing.support-tickets.index') }}"
            class="menu-item {{ request()->routeIs('marketing.support-tickets.*') ? 'active' : '' }}"
        >
            <i class="fas fa-ticket-alt"></i>
            <span>Support Tickets</span>
        </a>


    </div>


    <!-- =====================================================
         SIDEBAR NOTICES SECTION
    ====================================================== -->

    @if($sidebarNotices->count() > 0)

        <div class="sidebar-notices-section">

            <div class="sidebar-section-header">

                <h6>
                    Latest Notices
                </h6>

                <a
                    href="{{ route('marketing.notices.index') }}"
                    class="view-all-link"
                >
                    View All
                </a>

            </div>


            <div class="sidebar-notices-list">

                @foreach($sidebarNotices as $notice)

                    <div class="sidebar-notice-item">

                        <div class="sidebar-notice-title">
                            {{ $notice->title }}
                        </div>

                        <div class="sidebar-notice-meta">

                            <span>
                                {{
                                    $notice->start_date
                                        ? $notice->start_date->format('M d, Y')
                                        : $notice->created_at->format('M d, Y')
                                }}
                            </span>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    @endif

</div>


<!-- =========================================================
     SIDEBAR STYLES
========================================================= -->

<style>

    /* =====================================================
       Sidebar Notices Section
    ====================================================== */

    .sidebar-notices-section {

        margin-top: auto;

        padding: 1.5rem;

        border-top:
            1px solid rgba(255, 255, 255, 0.05);

        background:
            rgba(0, 0, 0, 0.1);
    }


    /* =====================================================
       Sidebar Section Header
    ====================================================== */

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


    /* =====================================================
       View All Link
    ====================================================== */

    .view-all-link {

        color: var(--primary-color);

        font-size: 0.75rem;

        text-decoration: none;

        transition:
            opacity 0.2s ease;
    }


    .view-all-link:hover {

        opacity: 0.8;
    }


    /* =====================================================
       Notices List
    ====================================================== */

    .sidebar-notices-list {

        max-height: 200px;

        overflow-y: auto;
    }


    /* =====================================================
       Notice Item
    ====================================================== */

    .sidebar-notice-item {

        padding: 0.75rem;

        background:
            rgba(255, 255, 255, 0.05);

        border-radius: 6px;

        margin-bottom: 0.5rem;

        border-left:
            3px solid var(--primary-color);

        transition:
            background 0.2s ease;

        cursor: pointer;
    }


    .sidebar-notice-item:hover {

        background:
            rgba(255, 255, 255, 0.1);
    }


    /* =====================================================
       Notice Title
    ====================================================== */

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


    /* =====================================================
       Notice Meta
    ====================================================== */

    .sidebar-notice-meta {

        font-size: 0.7rem;

        color: var(--gray-400);
    }


    /* =====================================================
       Notice Badge
    ====================================================== */

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

</style>