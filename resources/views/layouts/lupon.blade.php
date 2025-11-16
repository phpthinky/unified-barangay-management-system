{{-- FILE: resources/views/layouts/lupon.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'UBMS') }} - Lupon - @yield('title', 'Dashboard')</title>

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
            background: #1a1d20;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 1040;
        }
        .sidebar.collapsed { transform: translateX(-250px); }
        .main-content {
            margin-left: 250px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            background: #f8f9fa;
        }
        .main-content.expanded { margin-left: 0; }
        .sidebar-brand {
            padding: 1.25rem;
            border-bottom: 1px solid #2d3338;
            color: white;
            text-decoration: none;
            display: block;
            font-weight: 600;
            background: #0d6efd;
        }
        .sidebar-brand:hover {
            color: white;
            background: #0b5ed7;
        }
        .sidebar-nav { list-style: none; margin: 0; padding: 0; }
        .sidebar-nav-item { border-bottom: 1px solid #2d3338; }
        .sidebar-nav-link {
            display: flex;
            align-items: center;
            padding: 0.875rem 1rem;
            color: #adb5bd; 
            text-decoration: none;
            transition: all 0.2s;
        }
        .sidebar-nav-link:hover {
            background: #2d3338; 
            color: #fff;
            padding-left: 1.25rem;
        }
        .sidebar-nav-link.active {
            background: #0d6efd; 
            color: #fff;
            border-left: 4px solid #fff;
        }
        .sidebar-nav-link i { 
            width: 24px; 
            margin-right: .75rem; 
            text-align: center;
        }
        .topbar {
            background: white;
            padding: .75rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
            border-bottom: 2px solid #0d6efd;
        }
        .sidebar-toggle { 
            border: none; 
            background: none; 
            font-size: 1.3rem;
            color: #495057;
            cursor: pointer;
        }
        .sidebar-toggle:hover {
            color: #0d6efd;
        }
        .sidebar-overlay {
            position: fixed; top:0; left:0; width:100%; height:100%;
            background: rgba(0,0,0,.5); z-index:1035; display:none;
        }
        .sidebar-overlay.show { display:block; }
        .sidebar-divider{
            padding: .75rem 1rem;
            border-bottom: 1px solid #2d3338;
            color: #6c757d;
            text-decoration: none;
            display: block;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: .5rem;
        }
        .sidebar-user-info {
            background: #2d3338;
            border-bottom: 1px solid #495057;
        }
        .badge-notification {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #dc3545;
            color: white;
            border-radius: 10px;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-250px); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
        
        /* Card enhancements */
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
        }
        
        /* Alert auto-dismiss */
        .alert-dismissible {
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
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
            <!-- Dashboard -->
            <li class="sidebar-nav-item">
                <a href="{{ route('lupon.dashboard') }}" 
                   class="sidebar-nav-link {{ request()->routeIs('lupon.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Main Menu Divider -->
            <li class="sidebar-divider">
                Complaint Management
            </li>

            <!-- My Complaints -->
            <li class="sidebar-nav-item">
                <a href="{{ route('lupon.complaints.index') }}" 
                   class="sidebar-nav-link {{ request()->routeIs('lupon.complaints.*') ? 'active' : '' }}">
                    <i class="fas fa-folder-open"></i>
                    <span>My Assigned Cases</span>
                </a>
            </li>

            <!-- Hearings -->
            <li class="sidebar-nav-item">
                <a href="{{ route('lupon.hearings.index') }}" 
                   class="sidebar-nav-link {{ request()->routeIs('lupon.hearings.*') ? 'active' : '' }}">
                    <i class="fas fa-gavel"></i>
                    <span>Hearings</span>
                    @if(isset($stats['scheduled_hearings']) && $stats['scheduled_hearings'] > 0)
                        <span class="badge bg-warning ms-auto">{{ $stats['scheduled_hearings'] }}</span>
                    @endif
                </a>
            </li>

            <!-- Quick Filters Divider -->
            <li class="sidebar-divider">
                Quick Filters
            </li>

            <!-- For Lupon (Awaiting Schedule) -->
            <li class="sidebar-nav-item">
                <a href="{{ route('lupon.complaints.index', ['status' => 'for_lupon']) }}" 
                   class="sidebar-nav-link">
                    <i class="fas fa-hourglass-half"></i>
                    <span>Awaiting Schedule</span>
                </a>
            </li>

            <!-- Ongoing Hearings -->
            <li class="sidebar-nav-item">
                <a href="{{ route('lupon.complaints.index') }}?workflow_status=ongoing" 
                   class="sidebar-nav-link">
                    <i class="fas fa-spinner"></i>
                    <span>Ongoing Hearings</span>
                </a>
            </li>

            <!-- Needs Decision -->
            <li class="sidebar-nav-item">
                <a href="{{ route('lupon.complaints.index', ['needs_decision' => true]) }}" 
                   class="sidebar-nav-link">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Needs Decision</span>
                </a>
            </li>

            <!-- Resolved Cases -->
            <li class="sidebar-nav-item">
                <a href="{{ route('lupon.complaints.index', ['status' => 'resolved_by_lupon']) }}" 
                   class="sidebar-nav-link">
                    <i class="fas fa-check-circle"></i>
                    <span>Resolved Cases</span>
                </a>
            </li>

            <!-- User Info Section -->
            <li class="sidebar-divider mt-auto"></li>
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

            <!-- Account Settings -->
            <li class="sidebar-nav-item">
                <a href="{{ route('lupon.profile') }}" class="sidebar-nav-link">
                    <i class="fas fa-user-cog"></i>
                    <span>Account Settings</span>
                </a>
            </li>

            <!-- Logout -->
            <li class="sidebar-nav-item">
                <a class="sidebar-nav-link" href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Topbar -->
        <div class="topbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle me-3" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="mb-0">@yield('title', 'Dashboard')</h5>
            </div>
            <div class="d-flex align-items-center">
                <span class="text-muted me-3">
                    <i class="fas fa-calendar-alt me-1"></i>{{ now()->format('M d, Y') }}
                </span>
                <span class="badge bg-primary">
                    <i class="fas fa-user me-1"></i>Lupon Member
                </span>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="container-fluid px-4">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Please correct the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="bg-white border-top mt-5 py-4">
            <div class="container-fluid px-4">
                <div class="row">
                    <div class="col-md-6">
                        <p class="text-muted mb-0">
                            <strong>{{ config('app.name', 'UBMS') }}</strong> - Lupon ng Tagapamayapa
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="text-muted mb-0">
                            &copy; {{ date('Y') }} Municipality of Sablayan. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

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

            // Sidebar toggle functionality
            sidebarToggle.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                } else {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                }
            });

            // Close sidebar on overlay click (mobile)
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });

            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });

            // Initialize Select2 (if any)
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    width: '100%'
                });
            }

            // Initialize Flatpickr datetime pickers
            if (typeof flatpickr !== 'undefined') {
                flatpickr('input[type="datetime-local"]', {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    time_24hr: false
                });

                flatpickr('input[type="date"]', {
                    dateFormat: "Y-m-d"
                });
            }

            // Confirmation dialogs
            document.querySelectorAll('[data-confirm]').forEach(function(element) {
                element.addEventListener('click', function(e) {
                    if (!confirm(this.getAttribute('data-confirm'))) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>