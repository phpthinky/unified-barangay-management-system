<!-- FILE: resources/views/public/barangay.blade.php -->
@extends('layouts.public')

@section('title', $barangay->name . ' - ' . ($siteSettings->municipality_name ?? config('app.name')))

@section('content')
<div class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-4 fw-bold">{{ $barangay->name }}</h1>
                <p class="lead">{{ $siteSettings->municipality_name ?? 'Municipality' }}</p>
                <p>Welcome to our barangay portal</p>
                <a href="{{ route('public.barangay.register', $barangay->slug) }}" class="btn btn-light btn-lg">
                    <i class="fas fa-user-plus me-2"></i>Register as Resident
                </a>
            </div>
            @if($barangay->logo)
            <div class="col-md-4 text-center">
                <img src="{{ asset('storage/' . $barangay->logo) }}" 
                     alt="{{ $barangay->name }} Logo" class="img-fluid" style="max-height: 200px;">
            </div>
            @endif
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-md-8">
            @if(isset($announcements) && $announcements->count() > 0)
                <h2 class="mb-4">Latest Announcements</h2>
                @foreach($announcements as $announcement)
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">{{ $announcement->title }}</h5>
                        <p class="card-text">{{ $announcement->content }}</p>
                        <small class="text-muted">
                            Posted on {{ $announcement->published_at->format('F j, Y') }}
                        </small>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                    <h3>No announcements yet</h3>
                    <p class="text-muted">Check back later for updates from the barangay</p>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-qrcode me-2"></i>Quick Access</h5>
                </div>
                <div class="card-body text-center">
                    @if($barangay->qr_code_path)
                    <img src="{{ asset('storage/' . $barangay->qr_code_path) }}" 
                         alt="QR Code" class="img-fluid mb-3" style="max-width: 150px;">
                    @endif
                    <p class="small text-muted">Scan QR code or use link below to register</p>
                    <a href="{{ route('public.barangay.register', $barangay->slug) }}" class="btn btn-primary btn-sm">
                        Register Now
                    </a>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-list me-2"></i>Available Services</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-file-alt text-primary me-2"></i>Barangay Clearance</li>
                        <li class="mb-2"><i class="fas fa-certificate text-success me-2"></i>Certificate of Indigency</li>
                        <li class="mb-2"><i class="fas fa-home text-info me-2"></i>Residency Certificate</li>
                        <li class="mb-2"><i class="fas fa-store text-warning me-2"></i>Business Permits</li>
                        <li class="mb-2"><i class="fas fa-exclamation-triangle text-danger me-2"></i>File Complaints</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-map-marker-alt me-2"></i>Location</h5>
                </div>
                <div class="card-body p-0">
                    @if($barangay->map_embed)
                        <div class="ratio ratio-16x9">
                            {!! $barangay->map_embed !!}
                        </div>
                    @else
                        <p class="p-3 text-muted">Location map not available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
