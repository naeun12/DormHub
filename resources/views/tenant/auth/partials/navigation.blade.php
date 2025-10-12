@include('tenant.auth.partials.header')


@auth('tenant')
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
    <div class="container-fluid">
        <!-- Hamburger (mobile only) -->
        <button class="navbar-toggler me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#tenantNav"
            aria-controls="tenantNav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>


        <!-- Logo -->
        <a class="navbar-brand d-none d-lg-flex align-items-center mx-auto mx-lg-0 justify-content-center justify-content-lg-start flex-shrink-0" href="#">
    <img src="{{ asset('images/Logo/logo.png') }}" alt="Logo" width="100" class="me-2" style="max-width: 100px; height: auto;">
    <span class="logo-text d-none d-sm-inline">DormHub</span>
</a>




        <!-- Desktop Nav Links (centered) -->
        <div class="collapse navbar-collapse justify-content-center d-none d-lg-flex" id="navbarMain">
            <ul class="navbar-nav gap-4">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('homepage') ? 'active' : '' }}"
                        href="{{ route('homepage', ['tenant_id' => session('tenant_id')]) }}">
                        <i class="bi bi-house-door-fill me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dorm.map') ? 'active' : '' }}"
                        href="{{ route('dorm.map', ['tenant_id' => session('tenant_id')]) }}">
                        <i class="bi bi-geo-alt-fill me-1"></i> Dorm Location
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dormitories') ? 'active' : '' }}"
                        href="{{ route('dormitories', ['tenant_id' => session('tenant_id')]) }}">
                        <i class="bi bi-building me-1"></i> Dormitories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('tenant.message') ? 'active' : '' }}"
                        href="{{ route('tenant.message', ['tenant_id' => session('tenant_id')]) }}">
                        <i class="bi bi-chat-left-text me-1"></i> Message
                    </a>
                </li>
            </ul>
        </div>


        <!-- Right side (Notifications + Profile) -->
        <div class="d-flex align-items-center gap-2 ms-auto">
            <!-- Notifications -->
           <div class="dropdown">
    <button class="btn position-relative p-0 border-0 bg-transparent" type="button"
        id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-bell-fill fs-5"></i>
        @if ($unread_count > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7rem; padding: 0.2em 0.4em;">
                {{ $unread_count }}
            </span>
        @endif
    </button>


    <!-- Responsive dropdown -->
    <ul class="dropdown-menu dropdown-menu-end shadow-lg mt-2" aria-labelledby="notificationDropdown" style="min-width: auto; max-width: 90vw;">
        <li class="dropdown-header fw-bold text-center text-primary">Notifications</li>
       
        <div class="d-flex flex-column" style="max-height: 300px; overflow-y: auto;">
            @forelse($notifications as $notif)
                <li>
                    <a href="#" class="dropdown-item d-flex align-items-start py-2">
                        <i class="bi bi-bell-fill text-primary me-2 mt-1"></i>
                        <div>
                            <div class="fw-semibold">{{ $notif->title }}</div>
                            <small class="text-muted">{{ $notif->message }}</small>
                        </div>
                    </a>
                </li>
            @empty
                <li class="text-center text-muted px-3 py-2">No notifications yet.</li>
            @endforelse
        </div>


        <li><hr class="dropdown-divider"></li>
        <li class="text-center">
            <a href="{{ route('view.notifications.tenant', ['tenant_id' => session('tenant_id')]) }}"
                class="dropdown-item text-primary fw-bold">See All Notifications</a>
        </li>
    </ul>
</div>




            <!-- Profile -->
            <div class="dropdown">
                <a class="d-flex align-items-center text-black text-decoration-none dropdown-toggle"
                    href="#" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ asset('storage/' . session('profilePicUrl')) }}"
                        alt="User Avatar" width="40" height="40" class="rounded-circle object-fit-cover">
                </a>
                <ul class="dropdown-menu dropdown-menu-end mt-2" aria-labelledby="userDropdown">
                    <li>
                        <a href="{{ route('tenant.update', ['tenant_id' => session('tenant_id')]) }}" class="dropdown-item">View Profile</a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('tenant.logout') }}" method="POST" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="dropdown-item">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>


<!-- Offcanvas Sidebar for Mobile -->
<!-- Offcanvas Sidebar for Mobile -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="tenantNav" aria-labelledby="tenantNavLabel" style="width: 250px;">
    <div class="offcanvas-header d-flex align-items-center">
        <img src="{{ asset('images/Logo/logo.png') }}" alt="Logo" width="60" class="me-2">
        <span class="fs-5">DormHub</span>
        <button type="button" class="btn-close text-reset ms-auto" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <ul class="navbar-nav flex-column gap-3">
            <li>
                <a class="nav-link py-2 d-flex align-items-center {{ request()->routeIs('homepage') ? 'active fw-bold' : '' }}"
                   href="{{ route('homepage', ['tenant_id' => session('tenant_id')]) }}">
                    <i class="bi bi-house-door-fill me-2"></i> Home
                </a>
            </li>
            <li>
                <a class="nav-link py-2 d-flex align-items-center {{ request()->routeIs('dorm.map') ? 'active fw-bold' : '' }}"
                   href="{{ route('dorm.map', ['tenant_id' => session('tenant_id')]) }}">
                    <i class="bi bi-geo-alt-fill me-2"></i> Dorm Location
                </a>
            </li>
            <li>
                <a class="nav-link py-2 d-flex align-items-center {{ request()->routeIs('dormitories') ? 'active fw-bold' : '' }}"
                   href="{{ route('dormitories', ['tenant_id' => session('tenant_id')]) }}">
                    <i class="bi bi-building me-2"></i> Dormitories
                </a>
            </li>
            <li>
                <a class="nav-link py-2 d-flex align-items-center {{ request()->routeIs('tenant.message') ? 'active fw-bold' : '' }}"
                   href="{{ route('tenant.message', ['tenant_id' => session('tenant_id')]) }}">
                    <i class="bi bi-chat-left-text me-2"></i> Message
                </a>
            </li>
        </ul>
    </div>
</div>


<!-- Page content with proper top spacing -->
<div class="pt-5 pt-lg-6 mb-5" style="padding-top: 80px;">
    @yield('content')
</div>


<style>
    @media (max-width: 575.98px) {
        .pt-5 { padding-top: 70px !important; }
        .logo-text { display: none; }
    }
    .object-fit-cover { object-fit: cover; }
</style>
@endauth
