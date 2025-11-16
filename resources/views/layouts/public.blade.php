<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'UBMS - Unified Barangay Management System')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --light-bg: #f8f9fa;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 80px 0;
        }
        
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
            margin-bottom: 20px;
        }
        
        .stats-card .icon {
            font-size: 3rem;
            color: var(--secondary-color);
            margin-bottom: 10px;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        footer {
            background-color: var(--primary-color);
            color: white;
            padding: 40px 0;
            margin-top: 50px;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .alert {
            border-radius: 8px;
        }
        
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        
        .breadcrumb {
            background-color: transparent;
            padding: 0;
        }
        
        .breadcrumb-item a {
            color: var(--secondary-color);
            text-decoration: none;
        }
        
        .breadcrumb-item a:hover {
            color: var(--primary-color);
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-city me-2"></i>UBMS
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('public.about') }}">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('public.barangays') }}">Barangays</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('public.services') }}">Services</a>
                    </li>

                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-search me-1"></i>Track Request
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                               
            <form class="px-3 py-2" style="width: 300px;" method="GET" onsubmit="return submitTracking(event)">
                <div class="mb-2">
                    <input type="text" class="form-control" id="tracking-input" placeholder="Enter tracking number" required>
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-100">Track Request</button>
            </form>
                            </li>
                        </ul>
                    </li>
                     @auth
                            @if(auth()->user()->isMunicipalityAdmin())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-speedometer2"></i> Dashboard
                                    </a>
                                </li>
                                
                            @elseif(auth()->user()->isAbcPresident())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('abc.dashboard') }}">
                                        <i class="bi bi-bar-chart"></i> Dashboard
                                    </a>
                                </li>
                            @elseif(auth()->user()->isBarangayStaff())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('barangay.dashboard') }}">
                                        <i class="bi bi-house-door"></i> Dashboard
                                    </a>
                                </li>
                            @elseif(auth()->user()->isLupon())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('lupon.dashboard') }}">
                                        <i class="bi bi-balance-scale"></i> Dashboard
                                    </a>
                                </li>
                            @elseif(auth()->user()->isResident())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('resident.dashboard') }}">
                                        <i class="bi bi-person-circle"></i> Dashboard
                                    </a>
                                </li>
                            @endif
                        @endauth

                        @guest

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">
                                        <i class="fas fa-sign-in-alt me-1"></i>Login
                                    </a>
                                </li>
                        @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb -->
    @if (!request()->routeIs('public.index'))
    <div class="container mt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                @yield('breadcrumb')
            </ol>
        </nav>
    </div>
    @endif

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-city me-2"></i>UBMS</h5>
                    <p class="mb-0">Unified Barangay Management System - Streamlining barangay services and resident management.</p>
                </div>
                <div class="col-md-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('public.about') }}" class="text-light">About</a></li>
                        <li><a href="{{ route('public.services') }}" class="text-light">Services</a></li>
                        <li><a href="{{ route('public.barangays') }}" class="text-light">Barangays</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">Help Center</a></li>
                        <li><a href="#" class="text-light">Contact Us</a></li>
                        <li><a href="#" class="text-light">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; {{ date('Y') }} UBMS. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <small>Powered by Laravel & Bootstrap</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
function submitTracking(event) {
    event.preventDefault();
    const trackingNumber = document.getElementById('tracking-input').value.trim();
    if (trackingNumber) {
        window.location.href = '{{ route("track.request", "") }}' + '/' + trackingNumber;
    }
    return false;
}
</script>
    @yield('scripts')
</body>
</html>