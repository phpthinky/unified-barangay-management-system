@extends('layouts.public')

@section('title', 'Services - UBMS')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
<li class="breadcrumb-item active">Services</li>
@endsection

@section('content')
<!-- Page Header -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3">Our Services</h1>
                <p class="lead mb-0">
                    Comprehensive digital services designed to make barangay transactions easier, faster, and more accessible for everyone.
                </p>
            </div>
            <div class="col-lg-4 text-end">
                <i class="fas fa-concierge-bell" style="font-size: 8rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Service Categories -->
<section class="py-5">
    <div class="container">
        <!-- Document Services -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="fw-bold mb-4">
                    <i class="fas fa-file-alt text-primary me-3"></i>
                    Document Services
                </h2>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-id-badge fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Barangay Clearance</h5>
                        <p class="text-muted">Official clearance certificate for various purposes including employment, travel, and legal requirements.</p>
                        <ul class="list-unstyled text-start">
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Online application</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Real-time tracking</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Digital verification</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-home fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Certificate of Residency</h5>
                        <p class="text-muted">Proof of residence certificate for legal, educational, and business purposes.</p>
                        <ul class="list-unstyled text-start">
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Instant processing</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Legal validity</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>QR verification</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-heart fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Indigency Certificate</h5>
                        <p class="text-muted">Certificate for low-income residents to access government benefits and assistance programs.</p>
                        <ul class="list-unstyled text-start">
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Social service access</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Government benefits</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Medical assistance</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-user-check fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Good Moral Certificate</h5>
                        <p class="text-muted">Character reference certificate for employment, education, and migration purposes.</p>
                        <ul class="list-unstyled text-start">
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Employment requirement</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Educational purposes</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Migration documents</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-building fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Business Clearance</h5>
                        <p class="text-muted">Required clearance for business registration and permit applications.</p>
                        <ul class="list-unstyled text-start">
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Business registration</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Permit requirements</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Legal compliance</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-file-contract fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Other Certificates</h5>
                        <p class="text-muted">Various other official documents and certificates as needed by residents.</p>
                        <ul class="list-unstyled text-start">
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Custom requests</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Special purposes</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Legal documents</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Business Services -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="fw-bold mb-4">
                    <i class="fas fa-store text-success me-3"></i>
                    Business Services
                </h2>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-certificate fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Business Permit</h5>
                        <p class="text-muted">Complete business permit application and processing service with online tracking.</p>
                        <ul class="list-unstyled text-start">
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Online application</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Document upload</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Status tracking</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-calendar-check fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Permit Renewal</h5>
                        <p class="text-muted">Easy renewal process for existing business permits with automated reminders.</p>
                        <ul class="list-unstyled text-start">
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Automatic reminders</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Quick renewal</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Digital records</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-search fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Business Inspection</h5>
                        <p class="text-muted">Scheduled business inspections and compliance verification services.</p>
                        <ul class="list-unstyled text-start">
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Scheduled inspections</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Compliance check</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Digital reports</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Community Services -->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="fw-bold mb-4">
                    <i class="fas fa-users text-warning me-3"></i>
                    Community Services
                </h2>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-balance-scale fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Complaint Resolution</h5>
                        <p class="text-muted">File and track complaints through the Katarungang Pambarangay system.</p>
                        <ul class="list-unstyled text-start">
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Online filing</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Mediation services</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Case tracking</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-bullhorn fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Announcements</h5>
                        <p class="text-muted">Stay updated with important barangay announcements and community news.</p>
                        <ul class="list-unstyled text-start">
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Real-time updates</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Event notifications</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Emergency alerts</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-calendar-alt fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Events & Programs</h5>
                        <p class="text-muted">Information about community events, programs, and activities.</p>
                        <ul class="list-unstyled text-start">
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Community events</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Health programs</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Educational activities</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How to Access Services -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="fw-bold mb-3">How to Access Our Services</h2>
                <p class="lead text-muted">Simple steps to get started with UBMS services</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <span class="fw-bold fs-2">1</span>
                    </div>
                    <h5 class="fw-bold mb-3">Find Your Barangay</h5>
                    <p class="text-muted">Browse our list of barangays and select your community to access specific services.</p>
                    <a href="{{ route('public.barangays') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-search me-1"></i>Find Barangay
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <span class="fw-bold fs-2">2</span>
                    </div>
                    <h5 class="fw-bold mb-3">Register Account</h5>
                    <p class="text-muted">Create your resident account by providing required information and documents for verification.</p>
                    <button class="btn btn-outline-success btn-sm" onclick="alert('Please select your barangay first')">
                        <i class="fas fa-user-plus me-1"></i>Register Now
                    </button>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <span class="fw-bold fs-2">3</span>
                    </div>
                    <h5 class="fw-bold mb-3">Get Verified</h5>
                    <p class="text-muted">Wait for barangay staff to verify your account and activate your access to all services.</p>
                    <span class="badge bg-info">Verification Required</span>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <span class="fw-bold fs-2">4</span>
                    </div>
                    <h5 class="fw-bold mb-3">Access Services</h5>
                    <p class="text-muted">Start requesting documents, filing complaints, applying for permits, and more!</p>
                    <a href="{{ route('login') }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-sign-in-alt me-1"></i>Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Service Hours & Contact -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-5">
                        <i class="fas fa-clock text-primary mb-3" style="font-size: 3rem;"></i>
                        <h4 class="fw-bold mb-3">Service Hours</h4>
                        <div class="text-start">
                            <div class="row mb-2">
                                <div class="col-6"><strong>Online Services:</strong></div>
                                <div class="col-6">24/7 Available</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6"><strong>Office Hours:</strong></div>
                                <div class="col-6">8:00 AM - 5:00 PM</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6"><strong>Working Days:</strong></div>
                                <div class="col-6">Monday - Friday</div>
                            </div>
                            <div class="row">
                                <div class="col-6"><strong>Holidays:</strong></div>
                                <div class="col-6">Closed</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-5">
                        <i class="fas fa-question-circle text-success mb-3" style="font-size: 3rem;"></i>
                        <h4 class="fw-bold mb-3">Need Help?</h4>
                        <p class="text-muted mb-4">Our support team is here to assist you with any questions about our services.</p>
                        
                        @if($settings && ($settings->support_email || $settings->support_phone))
                        <div class="text-start">
                            @if($settings->support_email)
                            <div class="mb-2">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <a href="mailto:{{ $settings->support_email }}">{{ $settings->support_email }}</a>
                            </div>
                            @endif
                            @if($settings->support_phone)
                            <div class="mb-2">
                                <i class="fas fa-phone text-success me-2"></i>
                                <a href="tel:{{ $settings->support_phone }}">{{ $settings->support_phone }}</a>
                            </div>
                            @endif
                        </div>
                        @else
                        <div class="alert alert-info">
                            <small>Contact your local barangay office for assistance.</small>
                        </div>
                        @endif
                        
                        <a href="{{ route('public.barangays') }}" class="btn btn-success mt-3">
                            <i class="fas fa-phone me-2"></i>Contact Barangay
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection