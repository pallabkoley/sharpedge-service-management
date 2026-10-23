{{-- resources/views/Backoffice/Header/Header.blade.php --}}

<header class="header d-flex justify-content-between align-items-center">

    {{-- Page Title --}}
    <div>
        <h2 class="mb-0">@yield('page_title', 'Dashboard')</h2>

        @hasSection('breadcrumb')
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    @yield('breadcrumb')
                </ol>
            </nav>
        @endif
    </div>

    {{-- User Profile Dropdown --}}
    <div class="dropdown" id="profileDropdown">

        <div class="profile-dropdown-toggle dropdown-toggle"
             data-bs-toggle="dropdown"
             aria-expanded="false"
             style="cursor:pointer;">

            <div class="d-flex align-items-center">

                <div class="user-avatar-sidebar me-2">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>

                <div>
                    <div class="fw-bold">
                        {{ auth()->user()->name ?? 'User' }}
                    </div>

                    <small class="text-muted">
                        Back Office Executive
                    </small>
                </div>

            </div>
        </div>

        <div class="dropdown-menu dropdown-menu-end shadow-sm">

            <div class="px-3 py-2 border-bottom">
                <div class="fw-bold">
                    {{ auth()->user()->name ?? 'User' }}
                </div>

                <small class="text-muted">
                    {{ auth()->user()->email ?? '' }}
                </small>
            </div>

            <a class="dropdown-item" href="#">
                <i class="bi bi-person me-2"></i>
                Profile
            </a>

            <div class="dropdown-divider"></div>

            <form method="POST"
                  action="{{ route('logout') }}">
                @csrf

                <button type="submit"
                        class="dropdown-item text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Logout
                </button>
            </form>

        </div>

    </div>

</header>

<style>
    .dropdown-menu{
        min-width:220px;
        border-radius:10px;
        border:1px solid #e2e8f0;
    }

    .dropdown-item i{
        width:18px;
        text-align:center;
    }

    .dropdown-item:active{
        background:#f1f5f9;
        color:#1e293b;
    }

    .dropdown-item.text-danger:active{
        background:#fee2e2;
        color:#dc2626;
    }

    .profile-dropdown-toggle:hover{
        opacity:.9;
    }
</style>