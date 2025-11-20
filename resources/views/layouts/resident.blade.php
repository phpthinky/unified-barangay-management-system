{{-- FILE: resources/views/layouts/resident.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'UBMS') }} - @yield('title', 'Dashboard')</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Plugins -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

    <!-- Custom -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

    <style>
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            width: 250px;
            background: #343a40;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 1040;
        }
        .sidebar.collapsed { transform: translateX(-250px); }
        .main-content {
            margin-left: 250px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }
        .main-content.expanded { margin-left: 0; }
        .sidebar-brand {
            padding: 1rem;
            border-bottom: 1px solid #495057;
            color: white;
            text-decoration: none;
            display: block;
        }
        .sidebar-nav { list-style: none; margin: 0; padding: 0; }
        .sidebar-nav-item { border-bottom: 1px solid #495057; }
        .sidebar-nav-link {
            display: block; padding: 0.75rem 1rem;
            color: #adb5bd; text-decoration: none;
            transition: all 0.3s ease;
        }
        .sidebar-nav-link:hover,
        .sidebar-nav-link.active {
            background: #495057; color: #fff;
        }
        .sidebar-nav-link i { width: 20px; margin-right: .5rem; }
        
        /* Submenu styles */
        .sidebar-nav-submenu {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 0;
            overflow: hidden;
            background: #2c3136;
            transition: max-height 0.3s ease;
        }
        .sidebar-nav-submenu.show {
            max-height: 500px;
        }
        .sidebar-nav-submenu-link {
            display: block;
            padding: 0.5rem 1rem 0.5rem 3rem;
            color: #adb5bd;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .sidebar-nav-submenu-link:hover,
        .sidebar-nav-submenu-link.active {
            background: #495057;
            color: #fff;
            padding-left: 3.5rem;
        }
        .sidebar-nav-link .fa-chevron-down {
            float: right;
            transition: transform 0.3s ease;
            font-size: 0.8rem;
        }
        .sidebar-nav-link.expanded .fa-chevron-down {
            transform: rotate(180deg);
        }
        
        .topbar {
            background: white;
            padding: .5rem 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
        }
        .sidebar-toggle { border: none; background: none; font-size: 1.2rem; }
        .sidebar-overlay {
            position: fixed; top:0; left:0; width:100%; height:100%;
            background: rgba(0,0,0,.5); z-index:1035; display:none;
        }
        .sidebar-overlay.show { display:block; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-250px); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <a href="{{ route('resident.dashboard') }}" class="sidebar-brand">
            <i class="bi bi-building"></i> {{ config('app.name', 'UBMS') }}
        </a>

        <ul class="sidebar-nav flex-grow-1">
            @auth
                @if(auth()->user()->isResident())
                    <!-- Dashboard -->
                    <li class="sidebar-nav-item">
                        <a href="{{ route('resident.dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('resident.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-person-circle"></i> Dashboard
                        </a>
                    </li>

                    <!-- My Documents -->
                    <li class="sidebar-nav-item">
                        <a href="#" class="sidebar-nav-link {{ request()->routeIs('resident.documents.*') ? 'active expanded' : '' }}" onclick="toggleSubmenu(event, 'documentsMenu')">
                            <i class="bi bi-file-earmark"></i> My Documents
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <ul class="sidebar-nav-submenu {{ request()->routeIs('resident.documents.*') ? 'show' : '' }}" id="documentsMenu">
                            <li>
                                <a href="{{ route('resident.documents.index') }}" class="sidebar-nav-submenu-link {{ request()->routeIs('resident.documents.index') && !request('status') ? 'active' : '' }}">
                                    <i class="bi bi-list-ul"></i> All Requests
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('resident.documents.create') }}" class="sidebar-nav-submenu-link {{ request()->routeIs('resident.documents.create') ? 'active' : '' }}">
                                    <i class="bi bi-plus-circle"></i> New Request
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('resident.documents.index', ['status' => 'pending']) }}" class="sidebar-nav-submenu-link {{ request('status') == 'pending' ? 'active' : '' }}">
                                    <i class="bi bi-clock-history"></i> Pending
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('resident.documents.index', ['status' => 'processing']) }}" class="sidebar-nav-submenu-link {{ request('status') == 'processing' ? 'active' : '' }}">
                                    <i class="bi bi-hourglass-split"></i> Processing
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('resident.documents.index', ['status' => 'approved']) }}" class="sidebar-nav-submenu-link {{ request('status') == 'approved' ? 'active' : '' }}">
                                    <i class="bi bi-check-circle"></i> Approved
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('resident.documents.index', ['status' => 'rejected']) }}" class="sidebar-nav-submenu-link {{ request('status') == 'rejected' ? 'active' : '' }}">
                                    <i class="bi bi-x-circle"></i> Rejected
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- My Complaints -->
                    <li class="sidebar-nav-item">
                        <a href="#" class="sidebar-nav-link {{ request()->routeIs('resident.complaints.*') ? 'active expanded' : '' }}" onclick="toggleSubmenu(event, 'complaintsMenu')">
                            <i class="bi bi-chat-left-text"></i> My Complaints
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <ul class="sidebar-nav-submenu {{ request()->routeIs('resident.complaints.*') ? 'show' : '' }}" id="complaintsMenu">
                            <li>
                                <a href="{{ route('resident.complaints.index') }}" class="sidebar-nav-submenu-link {{ request()->routeIs('resident.complaints.index') && !request('status') ? 'active' : '' }}">
                                    <i class="bi bi-list-ul"></i> All Complaints
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('resident.complaints.create') }}" class="sidebar-nav-submenu-link {{ request()->routeIs('resident.complaints.create') ? 'active' : '' }}">
                                    <i class="bi bi-plus-circle"></i> File New Complaint
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('resident.complaints.index', ['status' => 'pending']) }}" class="sidebar-nav-submenu-link {{ request('status') == 'pending' ? 'active' : '' }}">
                                    <i class="bi bi-clock-history"></i> Pending Review
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('resident.complaints.index', ['status' => 'captain_mediation']) }}" class="sidebar-nav-submenu-link {{ request('status') == 'captain_mediation' ? 'active' : '' }}">
                                    <i class="bi bi-people"></i> In Mediation
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('resident.complaints.index', ['status' => 'for_lupon']) }}" class="sidebar-nav-submenu-link {{ request('status') == 'for_lupon' ? 'active' : '' }}">
                                    <i class="bi bi-calendar-event"></i> Lupon Hearing
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('resident.complaints.index', ['status' => 'resolved']) }}" class="sidebar-nav-submenu-link {{ request('status') == 'resolved' ? 'active' : '' }}">
                                    <i class="bi bi-check2-all"></i> Resolved/Settled
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('resident.complaints.index', ['status' => 'dismissed']) }}" class="sidebar-nav-submenu-link {{ request('status') == 'dismissed' ? 'active' : '' }}">
                                    <i class="bi bi-x-octagon"></i> Dismissed
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <li class="sidebar-divider"></li>
                <li class="sidebar-user-info">
                    <div class="px-3 py-2 text-light">
                        <div class="d-flex align-items-center mb-1">
                            <i class="bi bi-person-circle me-2"></i>
                            <strong>{{ auth()->user()->name }}</strong>
                        </div>
                        <small class="text-white d-block ps-4">
                            {{ auth()->user()->getRoleNames()->first() ? ucwords(str_replace('-', ' ', auth()->user()->getRoleNames()->first())) : 'User' }}
                        </small>
                    </div>
                </li>
                <li class="sidebar-divider"></li>

                <!-- Profile & Logout -->
                <li class="sidebar-nav-item">
                    <a href="{{ route('resident.profile') }}" class="sidebar-nav-link {{ request()->routeIs('resident.profile') ? 'active' : '' }}">
                        <i class="bi bi-person"></i> Account Setting
                    </a>
                </li>

                <li class="sidebar-nav-item">
                    <a class="sidebar-nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            @else
                <li class="sidebar-nav-item">
                    <a href="{{ route('login') }}" class="sidebar-nav-link">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </a>
                </li>
            @endauth
        </ul>

       
    </nav>

    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="topbar d-flex justify-content-between align-items-center">
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
            <div>{{ auth()->check() ? 'Welcome, '.auth()->user()->name : '' }}</div>
        </div>

        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>

        <footer class="bg-dark text-light py-4 mt-5">
            <div class="container">
                <p class="mb-0">&copy; {{ date('Y') }} Municipality of Sablayan. All rights reserved.</p>
            </div>
        </footer>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            sidebarToggle.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                } else {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                }
            });

            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });
        });

        function toggleSubmenu(event, menuId) {
            event.preventDefault();
            const submenu = document.getElementById(menuId);
            const link = event.currentTarget;
            
            // Toggle submenu
            submenu.classList.toggle('show');
            link.classList.toggle('expanded');
        }
    </script>

    @stack('scripts')
</body>
</html>