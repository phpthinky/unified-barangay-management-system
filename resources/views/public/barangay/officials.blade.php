@extends('layouts.public')

@section('title', $barangay->name . ' - Barangay Officials')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('public.barangays') }}">Barangays</a></li>
<li class="breadcrumb-item"><a href="{{ route('public.barangay.home', $barangay) }}">{{ $barangay->name }}</a></li>
<li class="breadcrumb-item active">Officials</li>
@endsection

@section('content')
<!-- Barangay Header -->
<section class="bg-primary text-white py-5">
    <div class="container text-center">
        <h1 class="display-5 fw-bold mb-3">Barangay Officials</h1>
        <p class="lead mb-0">{{ $barangay->name }}</p>
        <p class="mb-0 mt-2">
            <small>Term: {{ $captain ? ($captain->term_start ? $captain->term_start->format('Y') . ' - ' . ($captain->term_end ? $captain->term_end->format('Y') : 'Present') : 'N/A') : 'N/A' }}</small>
        </p>
    </div>
</section>

<!-- Officials Organization Chart -->
<section class="py-5">
    <div class="container">
        <!-- Barangay Captain -->
        @if($captain)
        <div class="card shadow-lg border-0 mb-5">
            <div class="card-header bg-primary text-white py-3">
                <h2 class="h4 mb-0">
                    <i class="fas fa-crown me-2"></i>Barangay Captain
                </h2>
            </div>
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        <div class="position-relative d-inline-block">
                            @if($captain->avatar)
                                <img src="{{ asset('storage/' . $captain->avatar) }}" 
                                     alt="{{ $captain->name }}" 
                                     class="rounded-circle img-thumbnail" 
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                                     style="width: 150px; height: 150px;">
                                    <i class="fas fa-user text-white fa-3x"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-9 text-center text-md-start">
                        <h3 class="fw-bold text-primary mb-2">Hon. {{ $captain->name }}</h3>
                        <p class="text-muted mb-3">{{ $captain->position_title ?: 'Barangay Captain' }}</p>
                        <div class="row">
                            <div class="col-lg-6">
                                <p class="mb-2">
                                    <i class="far fa-calendar text-primary me-2"></i>
                                    <strong>Term:</strong> 
                                    @if($captain->term_start && $captain->term_end)
                                        {{ $captain->term_start->format('F Y') }} - {{ $captain->term_end->format('F Y') }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                            @if($captain->email)
                            <div class="col-lg-6">
                                <p class="mb-2">
                                    <i class="far fa-envelope text-primary me-2"></i>
                                    <strong>Email:</strong> {{ $captain->email }}
                                </p>
                            </div>
                            @endif
                            @if($captain->contact_number)
                            <div class="col-lg-6">
                                <p class="mb-2">
                                    <i class="fas fa-phone text-primary me-2"></i>
                                    <strong>Contact:</strong> {{ $captain->contact_number }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Barangay Councilors -->
        @if($councilors->count() > 0)
        <div class="card shadow-lg border-0 mb-5">
            <div class="card-header bg-success text-white py-3">
                <h2 class="h4 mb-0">
                    <i class="fas fa-users me-2"></i>Barangay Councilors
                </h2>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    @foreach($councilors as $councilor)
                    <div class="col-lg-4 col-md-6">
                        <div class="card border h-100 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    @if($councilor->avatar)
                                        <img src="{{ asset('storage/' . $councilor->avatar) }}" 
                                             alt="{{ $councilor->name }}" 
                                             class="rounded-circle img-thumbnail" 
                                             style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" 
                                             style="width: 100px; height: 100px;">
                                            <i class="fas fa-user text-muted fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                                <h5 class="fw-bold mb-1">Kagawad {{ $councilor->name }}</h5>
                                <p class="text-success fw-medium mb-2">
                                    {{ $councilor->committee_display ?? 'Barangay Councilor' }}
                                </p>
                                @if($councilor->term_start && $councilor->term_end)
                                <p class="text-muted small mb-0">
                                    <i class="far fa-calendar me-1"></i>
                                    {{ $councilor->term_start->format('Y') }} - {{ $councilor->term_end->format('Y') }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Barangay Staff -->
        <div class="card shadow-lg border-0 mb-5">
            <div class="card-header bg-purple text-white py-3">
                <h2 class="h4 mb-0">
                    <i class="fas fa-user-tie me-2"></i>Barangay Staff
                </h2>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- Secretary -->
                    @if($secretary)
                    <div class="col-lg-4 col-md-6">
                        <div class="card border h-100 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    @if($secretary->avatar)
                                        <img src="{{ asset('storage/' . $secretary->avatar) }}" 
                                             alt="{{ $secretary->name }}" 
                                             class="rounded-circle img-thumbnail" 
                                             style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" 
                                             style="width: 100px; height: 100px;">
                                            <i class="fas fa-user text-muted fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                                <h5 class="fw-bold mb-1">{{ $secretary->name }}</h5>
                                <p class="text-purple fw-medium mb-2">Barangay Secretary</p>
                                @if($secretary->term_start && $secretary->term_end)
                                <p class="text-muted small mb-0">
                                    <i class="far fa-calendar me-1"></i>
                                    {{ $secretary->term_start->format('Y') }} - {{ $secretary->term_end->format('Y') }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Treasurer -->
                    @if($treasurer)
                    <div class="col-lg-4 col-md-6">
                        <div class="card border h-100 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    @if($treasurer->avatar)
                                        <img src="{{ asset('storage/' . $treasurer->avatar) }}" 
                                             alt="{{ $treasurer->name }}" 
                                             class="rounded-circle img-thumbnail" 
                                             style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" 
                                             style="width: 100px; height: 100px;">
                                            <i class="fas fa-user text-muted fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                                <h5 class="fw-bold mb-1">{{ $treasurer->name }}</h5>
                                <p class="text-purple fw-medium mb-2">Barangay Treasurer</p>
                                @if($treasurer->term_start && $treasurer->term_end)
                                <p class="text-muted small mb-0">
                                    <i class="far fa-calendar me-1"></i>
                                    {{ $treasurer->term_start->format('Y') }} - {{ $treasurer->term_end->format('Y') }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Other Staff -->
                    @foreach($staff as $member)
                    <div class="col-lg-4 col-md-6">
                        <div class="card border h-100 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    @if($member->avatar)
                                        <img src="{{ asset('storage/' . $member->avatar) }}" 
                                             alt="{{ $member->name }}" 
                                             class="rounded-circle img-thumbnail" 
                                             style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" 
                                             style="width: 100px; height: 100px;">
                                            <i class="fas fa-user text-muted fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                                <h5 class="fw-bold mb-1">{{ $member->name }}</h5>
                                <p class="text-purple fw-medium mb-2">
                                    {{ $member->position_title ?: 'Barangay Staff' }}
                                </p>
                                @if($member->term_start && $member->term_end)
                                <p class="text-muted small mb-0">
                                    <i class="far fa-calendar me-1"></i>
                                    {{ $member->term_start->format('Y') }} - {{ $member->term_end->format('Y') }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Lupon Members -->
        @if($luponMembers->count() > 0)
        <div class="card shadow-lg border-0 mb-5">
            <div class="card-header bg-warning text-dark py-3">
                <h2 class="h4 mb-0">
                    <i class="fas fa-balance-scale me-2"></i>Lupon ng Tagapamayapa
                </h2>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    @foreach($luponMembers as $lupon)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card border h-100 shadow-sm">
                            <div class="card-body text-center p-3">
                                <div class="mb-2">
                                    @if($lupon->avatar)
                                        <img src="{{ asset('storage/' . $lupon->avatar) }}" 
                                             alt="{{ $lupon->name }}" 
                                             class="rounded-circle img-thumbnail" 
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" 
                                             style="width: 80px; height: 80px;">
                                            <i class="fas fa-user text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <h6 class="fw-bold mb-1 small">{{ $lupon->name }}</h6>
                                <p class="text-warning fw-medium small mb-0">
                                    {{ $lupon->position_title ?: 'Lupon Member' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

<!-- Organizational Chart View -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-dark text-white py-3">
                <h2 class="h4 mb-0 text-center">
                    <i class="fas fa-sitemap me-2"></i>Organizational Structure
                </h2>
            </div>
            <div class="card-body p-4">
                <div class="org-structure text-center">
                    <!-- Captain Level -->
                    <div class="mb-4">
                        <div class="d-inline-block bg-primary text-white rounded p-3 mx-auto">
                            <h5 class="fw-bold mb-1">BARANGAY CAPTAIN</h5>
                            @if($captain)
                                <p class="mb-0 small">{{ $captain->name }}</p>
                            @else
                                <p class="mb-0 small">Vacant</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Councilors Level -->
                    @if($councilors->count() > 0)
                    <div class="mb-4">
                        <div class="bg-success text-white rounded p-3 mx-auto" style="max-width: 800px;">
                            <h5 class="fw-bold mb-3">BARANGAY COUNCILORS</h5>
                            <div class="row g-2">
                                @foreach($councilors as $councilor)
                                <div class="col-md-3 col-6">
                                    <div class="bg-white text-dark rounded p-2">
                                        <p class="fw-bold mb-1 small">Kagawad</p>
                                        <p class="mb-0 small text-truncate">{{ $councilor->name }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Staff Level -->
                    <div class="row g-3 justify-content-center">
                        <div class="col-md-4">
                            <div class="bg-purple text-white rounded p-3 h-100">
                                <h6 class="fw-bold mb-2">SECRETARY</h6>
                                @if($secretary)
                                    <p class="mb-0 small">{{ $secretary->name }}</p>
                                @else
                                    <p class="mb-0 small">Vacant</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-purple text-white rounded p-3 h-100">
                                <h6 class="fw-bold mb-2">TREASURER</h6>
                                @if($treasurer)
                                    <p class="mb-0 small">{{ $treasurer->name }}</p>
                                @else
                                    <p class="mb-0 small">Vacant</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-warning text-dark rounded p-3 h-100">
                                <h6 class="fw-bold mb-2">LUPON MEMBERS</h6>
                                <p class="mb-0 small">{{ $luponMembers->count() }} Members</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Information Section -->
<section class="py-5">
    <div class="container">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-info text-white py-3">
                <h2 class="h4 mb-0">
                    <i class="fas fa-address-card me-2"></i>Contact Information
                </h2>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="bg-light rounded p-4 h-100">
                            <h4 class="h5 fw-bold mb-3">
                                <i class="fas fa-building me-2"></i>Barangay Hall
                            </h4>
                            <div class="space-y-3">
                                <p class="mb-3">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    <strong>Address:</strong><br>
                                    {{ $barangay->address ?? 'Barangay Hall, ' . $barangay->name }}
                                </p>
                                <p class="mb-3">
                                    <i class="fas fa-phone text-success me-2"></i>
                                    <strong>Phone:</strong><br>
                                    {{ $barangay->contact_number ?? 'N/A' }}
                                </p>
                                <p class="mb-0">
                                    <i class="far fa-envelope text-purple me-2"></i>
                                    <strong>Email:</strong><br>
                                    {{ $barangay->email ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light rounded p-4 h-100">
                            <h4 class="h5 fw-bold mb-3">
                                <i class="fas fa-clock me-2"></i>Office Hours
                            </h4>
                            <div class="space-y-2">
                                <p class="mb-2">
                                    <strong>Monday - Friday:</strong><br>
                                    8:00 AM - 5:00 PM
                                </p>
                                <p class="mb-2">
                                    <strong>Saturday:</strong><br>
                                    8:00 AM - 12:00 PM
                                </p>
                                <p class="mb-0">
                                    <strong>Sunday:</strong><br>
                                    Closed
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.bg-purple {
    background-color: #6f42c1 !important;
}

.text-purple {
    color: #6f42c1 !important;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.org-structure {
    position: relative;
}

.org-structure::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    width: 2px;
    height: 100%;
    background: #dee2e6;
    transform: translateX(-50%);
    z-index: 0;
}

.org-structure > div {
    position: relative;
    z-index: 1;
}

@media (max-width: 768px) {
    .org-structure::before {
        display: none;
    }
}
</style>
@endpush