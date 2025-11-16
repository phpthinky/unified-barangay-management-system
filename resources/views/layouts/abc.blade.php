{{-- FILE: resources/views/layouts/app.blade.php --}}
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
        }
        .sidebar-nav-link:hover,
        .sidebar-nav-link.active {
            background: #495057; color: #fff;
        }
        .sidebar-nav-link i { width: 20px; margin-right: .5rem; }
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
        .sidebar-divider{
            padding: .5rem;
            border-bottom: 1px solid #495057;
            color: white;
            text-decoration: none;
            display: block;
            font-size: 12px;
        }
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
        <a href="{{ route('home') }}" class="sidebar-brand">
            <i class="bi bi-building"></i> {{ config('app.name', 'UBMS') }}
        </a>
<ul class="sidebar-nav flex-grow-1">
    @auth
        @if(auth()->user()->isAbcPresident())
            <li class="sidebar-nav-item">
                <a href="{{ route('abc.dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('abc.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> ABC Dashboard
                </a>
            </li>
            
            <li class="sidebar-nav-item">
                <a href="{{ route('abc.barangays.index') }}" class="sidebar-nav-link">
                    <i class="fas fa-building"></i> Barangays
                </a>
            </li>
            
            <li class="sidebar-nav-item">
                <a href="{{ route('abc.users.index') }}" class="sidebar-nav-link {{ request()->routeIs('abc.users.*') ? 'active' : '' }}">
                    <i class="fas fa-user-cog"></i> Users
                </a>
            </li>
            
            <li class="sidebar-nav-item">
                <a href="{{ route('abc.reports.index') }}" class="sidebar-nav-link">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            </li>
            
            <li class="sidebar-nav-item">
                <a href="{{ route('abc.reports.summary') }}" class="sidebar-nav-link">
                    <i class="fas fa-file-alt"></i> Summary
                </a>
            </li>
            
            <li class="sidebar-nav-item">
                <a href="{{ route('abc.active-sessions.index') }}" class="sidebar-nav-link">
                    <i class="fas fa-user-clock"></i> Active Sessions
                </a>
            </li>
        @endif

        <!-- User Info Divider -->
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
            <a href="{{ route('abc.profile') }}" class="sidebar-nav-link">
                <i class="bi bi-person"></i> Profile
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
                <i class="bi bi-box-arrow-right"></i> Login
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
            @includeWhen(session('success'), 'partials.alert', ['type' => 'success', 'message' => session('success')])
            @includeWhen(session('error'), 'partials.alert', ['type' => 'danger', 'message' => session('error')])
            @includeWhen(session('warning'), 'partials.alert', ['type' => 'warning', 'message' => session('warning')])
            @includeWhen(session('info'), 'partials.alert', ['type' => 'info', 'message' => session('info')])

            @yield('content')
        </div>

        <footer class="bg-dark text-light py-4 mt-5">
            <div class="container">
                <p class="mb-0">&copy; {{ date('Y') }} Municipality of Sablayan. All rights reserved.</p>
            </div>
        </footer>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

  <!-- Scripts - CORRECT ORDER -->
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
    </script>

    @stack('scripts')
</body>
</html>
