@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<div class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold">{{ $settings->system_name ?? 'Unified Barangay Management System' }}</h1>
                <p class="lead">Streamlining barangay services for the {{ $settings->municipality_name ?? 'Municipality of Sablayan' }}</p>
                <p class="mb-4">Access essential barangay services online - request documents, file complaints, apply for business permits, and track your applications in real-time.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('services') }}" class="btn btn-light btn-lg">
                        <i class="bi bi-list-ul"></i> View Services
                    </a>
                    <a href="{{ route('barangays') }}" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-geo-alt"></i> Find Your Barangay
                    </a>
                    <button class="btn btn-warning btn-lg" onclick="showTrackModal()">
                        <i class="bi bi-search"></i> Track Request
                    </button>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <div class="bg-white bg-opacity-10 rounded p-4">
                    <i class="bi bi-building display-1"></i>
                    <h3>{{ $stats['total_barangays'] }}</h3>
                    <p class="mb-0">Active Barangays</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-people text-primary display-4"></i>
                        <h3 class="mt-3">{{ number_format($stats['total_residents']) }}</h3>
                        <p class="text-muted">Registered Residents</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-file-earmark-text text-info display-4"></i>
                        <h3 class="mt-3">{{ number_format($stats['documents_issued']) }}</h3>
                        <p class="text-muted">Documents Issued</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-briefcase text-success display-4"></i>
                        <h3 class="mt-3">{{ number_format($stats['permits_issued']) }}</h3>
                        <p class="text-muted">Permits Approved</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-geo-alt text-warning display-4"></i>
                        <h3 class="mt-3">{{ $stats['total_barangays'] }}</h3>
                        <p class="text-muted">Barangays Served</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Services Section -->
<div class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="display-5 fw-bold">Available Services</h2>
                <p class="lead">Convenient online access to essential barangay services</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-file-earmark-text text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <h5>Document Requests</h5>
                        <p class="text-muted">Request barangay clearances, certificates, and other official documents online.</p>
                        <ul class="list-unstyled small text-start">
                            <li><i class="bi bi-check text-success"></i> Barangay Clearance</li>
                            <li><i class="bi bi-check text-success"></i> Certificate of Indigency</li>
                            <li><i class="bi bi-check text-success"></i> Barangay ID</li>
                            <li><i class="bi bi-check text-success"></i> Business Clearance</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="bg-warning bg-opacity-10 rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-chat-square-text text-warning" style="font-size: 2rem;"></i>
                        </div>
                        <h5>Complaint Management</h5>
                        <p class="text-muted">File and track complaints with proper mediation and resolution process.</p>
                        <ul class="list-unstyled small text-start">
                            <li><i class="bi bi-check text-success"></i> Noise Complaints</li>
                            <li><i class="bi bi-check text-success"></i> Property Disputes</li>
                            <li><i class="bi bi-check text-success"></i> Lupon Mediation</li>
                            <li><i class="bi bi-check text-success"></i> Real-time Updates</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="bg-success bg-opacity-10 rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-briefcase text-success" style="font-size: 2rem;"></i>
                        </div>
                        <h5>Business Permits</h5>
                        <p class="text-muted">Apply for business permits and licenses with streamlined approval process.</p>
                        <ul class="list-unstyled small text-start">
                            <li><i class="bi bi-check text-success"></i> Sari-Sari Store Permit</li>
                            <li><i class="bi bi-check text-success"></i> Home-Based Business</li>
                            <li><i class="bi bi-check text-success"></i> Online Tracking</li>
                            <li><i class="bi bi-check text-success"></i> Digital Permits</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('services') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-arrow-right"></i> View All Services
            </a>
        </div>
    </div>
</div>

<!-- Barangays Section -->
<div class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="display-5 fw-bold">Our Barangays</h2>
                <p class="lead">Serving communities across {{ $settings->municipality_name ?? 'Sablayan' }}</p>
            </div>
        </div>
        
        <div class="row">
            @foreach($barangays->take(6) as $barangay)
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            @if($barangay->logo)
                                <img src="{{ $barangay->logo_url }}" alt="{{ $barangay->name }}" class="rounded me-3" width="50" height="50">
                            @else
                                <div class="bg-primary rounded me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-building text-white"></i>
                                </div>
                            @endif
                            <div>
                                <h5 class="card-title mb-0">{{ $barangay->name }}</h5>
                                <small class="text-muted">{{ $barangay->address ?? 'Sablayan, Occidental Mindoro' }}</small>
                            </div>
                        </div>
                        
                        @if($barangay->description)
                        <p class="card-text small text-muted">{{ Str::limit($barangay->description, 100) }}</p>
                        @endif
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('public.barangay.home', $barangay) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-info-circle"></i> Learn More
                                </a>
                                <a href="{{ route('public.barangay.register', $barangay) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-person-plus"></i> Register
                                </a>
                            </div>
                            @if($barangay->qr_code)
                            <div class="text-end">
                                <img src="{{ $barangay->qr_code_url }}" alt="QR Code" width="40" height="40" class="border rounded">
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('barangays') }}" class="btn btn-outline-primary btn-lg">
                <i class="bi bi-geo-alt"></i> View All Barangays
            </a>
        </div>
    </div>
</div>

<!-- How It Works Section -->
<div class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="display-5 fw-bold">How It Works</h2>
                <p class="lead">Simple steps to access barangay services</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-3 text-center mb-4">
                <div class="bg-primary rounded-circle mx-auto mb-3 text-white d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <span class="fs-2 fw-bold">1</span>
                </div>
                <h5>Register</h5>
                <p class="text-muted">Create your account using your barangay's registration link</p>
            </div>
            
            <div class="col-md-3 text-center mb-4">
                <div class="bg-info rounded-circle mx-auto mb-3 text-white d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <span class="fs-2 fw-bold">2</span>
                </div>
                <h5>Verify</h5>
                <p class="text-muted">Wait for barangay staff to verify your profile and documents</p>
            </div>
            
            <div class="col-md-3 text-center mb-4">
                <div class="bg-success rounded-circle mx-auto mb-3 text-white d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <span class="fs-2 fw-bold">3</span>
                </div>
                <h5>Request</h5>
                <p class="text-muted">Submit requests for documents, file complaints, or apply for permits</p>
            </div>
            
            <div class="col-md-3 text-center mb-4">
                <div class="bg-warning rounded-circle mx-auto mb-3 text-white d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <span class="fs-2 fw-bold">4</span>
                </div>
                <h5>Track & Download</h5>
                <p class="text-muted">Monitor your request status and download approved documents</p>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2>Ready to Get Started?</h2>
                <p class="lead mb-0">Join thousands of residents already using our digital services</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('barangays') }}" class="btn btn-light btn-lg">
                    <i class="bi bi-arrow-right"></i> Find Your Barangay
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Track Request Modal -->
<div class="modal fade" id="trackModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Track Your Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="trackForm" onsubmit="trackRequest(event)">
                    <div class="mb-3">
                        <label for="trackingNumber" class="form-label">Tracking Number</label>
                        <input type="text" class="form-control" id="trackingNumber" placeholder="Enter your tracking number" required>
                        <div class="form-text">
                            Find your tracking number in your email or receipt (e.g., DOC-2024-01-ABC123)
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Track Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showTrackModal() {
    new bootstrap.Modal(document.getElementById('trackModal')).show();
}

function trackRequest(event) {
    event.preventDefault();
    const trackingNumber = document.getElementById('trackingNumber').value;
    if (trackingNumber) {
        window.location.href = "{{ route('track.request', '') }}/" + trackingNumber;
    }
}
</script>
@endpush
@endsection