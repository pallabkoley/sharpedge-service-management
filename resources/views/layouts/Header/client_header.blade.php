<header class="header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2>@yield('page-title', 'Dashboard')</h2>
            <p class="text-muted mb-0">
                @yield('page-subtitle', 'Welcome back, ' . (Auth::user()->name ?? 'Guest') . '. Here\'s your overview.')
            </p>
        </div>
        <div class="d-flex align-items-center">
            <!-- User dropdown -->
            <div class="dropdown">
                <button class="btn btn-link p-0 border-0 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar header-user text-white rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 45px; height: 45px;">
                        <span class="fw-bold">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </span>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <span class="dropdown-item-text fw-bold">{{ Auth::user()->name ?? 'Admin User' }}</span>
                        <span class="dropdown-item-text text-muted small">{{ Auth::user()->role->name ?? 'Administrator' }}</span>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            <div class="ms-2 d-none d-md-block">
                <p class="mb-0 fw-bold">{{ Auth::user()->name ?? 'Admin User' }}</p>
                <p class="mb-0 text-muted small">{{ Auth::user()->role->name ?? 'Administrator' }}</p>
            </div>
        </div>
    </div>
</header>