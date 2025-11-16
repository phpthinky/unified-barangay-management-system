@extends('layouts.public')

@section('title', 'About UBMS - Unified Barangay Management System')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
<li class="breadcrumb-item active">About</li>
@endsection

@section('content')
<!-- Page Header -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3">About UBMS</h1>
                <p class="lead mb-0">
                    {{ $settings->system_description ?? 'Revolutionizing barangay governance through innovative digital solutions.' }}
                </p>
            </div>
            <div class="col-lg-4 text-end">
                <i class="fas fa-info-circle" style="font-size: 8rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-5">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-bullseye fa-2x"></i>
                        </div>
                        <h3 class="fw-bold mb-3">Our Mission</h3>
                        <p class="lead text-muted">
                            {{ $settings->mission ?? 'To provide efficient, transparent, and accessible digital services that empower barangay communities and enhance the quality of life for all residents through innovative technology solutions.' }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-5">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-eye fa-2x"></i>
                        </div>
                        <h3 class="fw-bold mb-3">Our Vision</h3>
                        <p class="lead text-muted">
                            {{ $settings->vision ?? 'To be the leading platform for digital barangay governance, fostering connected communities where every resident has seamless access to essential services and participates actively in local development.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Key Features -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="fw-bold mb-3">Key Features</h2>
                <p class="lead text-muted">Comprehensive solutions for modern barangay management</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-mobile-alt fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Mobile Responsive</h5>
                        <p class="text-muted">Fully responsive design accessible on all devices - desktop, tablet, and mobile.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Resident Management</h5>
                        <p class="text-muted">Comprehensive database of all residents with verification, profiling, and demographic analytics.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-file-alt fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Document Processing</h5>
                        <p class="text-muted">Streamlined online document requests with digital signatures and QR code verification.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-store fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Business Permits</h5>
                        <p class="text-muted">Simplified business permit application process with automated workflows and tracking.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="fw-bold mb-3">How It Works</h2>
                <p class="lead text-muted">Simple steps to access barangay services</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <span class="fw-bold fs-2">1</span>
                    </div>
                    <h5 class="fw-bold mb-3">Find Your Barangay</h5>
                    <p class="text-muted">Browse the list of available barangays and select your community.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <span class="fw-bold fs-2">2</span>
                    </div>
                    <h5 class="fw-bold mb-3">Register Account</h5>
                    <p class="text-muted">Create your resident account by providing required information and documents.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <span class="fw-bold fs-2">3</span>
                    </div>
                    <h5 class="fw-bold mb-3">Get Verified</h5>
                    <p class="text-muted">Wait for barangay staff to verify your account and approve your registration.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <span class="fw-bold fs-2">4</span>
                    </div>
                    <h5 class="fw-bold mb-3">Access Services</h5>
                    <p class="text-muted">Start requesting documents, permits, and accessing all available services.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Benefits -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="fw-bold mb-3">Benefits for Everyone</h2>
                <p class="lead text-muted">Creating value for residents, officials, and communities</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-users"></i>
                            </div>
                            <h4 class="fw-bold mb-0">For Residents</h4>
                        </div>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>24/7 online service access</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Reduced waiting times</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Real-time request tracking</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Digital document storage</li>
                            <li class="mb-0"><i class="fas fa-check text-success me-2"></i>Transparent processes</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h4 class="fw-bold mb-0">For Officials</h4>
                        </div>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Streamlined workflows</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Automated reporting</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Better resource management</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Data-driven insights</li>
                            <li class="mb-0"><i class="fas fa-check text-success me-2"></i>Reduced paperwork</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-city"></i>
                            </div>
                            <h4 class="fw-bold mb-0">For Communities</h4>
                        </div>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Enhanced transparency</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Improved service delivery</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Better governance</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Digital transformation</li>
                            <li class="mb-0"><i class="fas fa-check text-success me-2"></i>Community engagement</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Information -->
@if($settings && ($settings->contact_email || $settings->contact_phone || $settings->address))
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="fw-bold mb-5">Get In Touch</h2>
            </div>
        </div>
        
        <div class="row justify-content-center">
            @if($settings->contact_email)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h5 class="fw-bold">Email Us</h5>
                    <p class="text-muted mb-0">{{ $settings->contact_email }}</p>
                </div>
            </div>
            @endif
            
            @if($settings->contact_phone)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h5 class="fw-bold">Call Us</h5>
                    <p class="text-muted mb-0">{{ $settings->contact_phone }}</p>
                </div>
            </div>
            @endif
            
            @if($settings->address)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h5 class="fw-bold">Visit Us</h5>
                    <p class="text-muted mb-0">{{ $settings->address }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endif
@endsection
