{{-- resources/views/public/index.blade.php --}}
@extends('layouts.public')

@section('title', 'UBMS - Unified Barangay Management System')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    {{ $settings->system_name ?? 'Unified Barangay Management System' }}
                </h1>
                <p class="lead mb-4">
                    {{ $settings->system_description ?? 'Streamlining barangay services, resident management, and community engagement through digital innovation.' }}
                </p>
                <div class="d-flex gap-3">
                    <a href="{{ route('public.barangays') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-map-marker-alt me-2"></i>Find Your Barangay
                    </a>
                    <a href="{{ route('public.services') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-concierge-bell me-2"></i>Our Services
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="text-center">
                    <i class="fas fa-city" style="font-size: 15rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-5 bg-light">
    <div class="container">
        {{-- ... unchanged statistics cards ... --}}
    </div>
</section>

<!-- Featured Barangays Section -->
<section class="py-5">
    <div class="container">
        {{-- ... barangays listing ... --}}
        
        @if($barangays->count() > 6)
        <div class="row">
            <div class="col-12 text-center">
                <a href="{{ route('public.barangays') }}" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-eye me-2"></i>View All Barangays
                </a>
            </div>
        </div>
        @endif
    </div>
</section>

<!-- Services Overview Section -->
<section class="py-5 bg-light">
    <div class="container">
        {{-- ... services cards ... --}}
        
        <div class="row">
            <div class="col-12 text-center">
                <a href="{{ route('public.services') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-list me-2"></i>View All Services
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Quick Access Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card border-primary h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-search text-primary mb-3" style="font-size: 3rem;"></i>
                        <h4 class="card-title">Track Your Request</h4>
                        <p class="card-text">Enter your tracking number to check the status of your document or permit request.</p>
                        
                        <form action="{{ route('track.request', '') }}" method="GET" onsubmit="this.action += document.getElementById('track-input').value" class="mt-3">
                            <div class="input-group">
                                <input type="text" class="form-control" id="track-input" placeholder="Enter tracking number" required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card border-success h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-qrcode text-success mb-3" style="font-size: 3rem;"></i>
                        <h4 class="card-title">Verify Document</h4>
                        <p class="card-text">Scan or enter the QR code from your document to verify its authenticity.</p>
                        
                        <form action="{{ route('verify.document', '') }}" method="GET" onsubmit="this.action += document.getElementById('qr-input').value" class="mt-3">
                            <div class="input-group">
                                <input type="text" class="form-control" id="qr-input" placeholder="Enter QR code" required>
                                <button class="btn btn-success" type="submit">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
