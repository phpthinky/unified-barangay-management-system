@extends('layouts.public')

@section('title', $barangay->name . ' - UBMS')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('public.barangays') }}">Barangays</a></li>
<li class="breadcrumb-item active">{{ $barangay->name }}</li>
@endsection

@section('content')
<!-- Barangay Header -->
<section class="bg-primary text-white py-5">
    <div class="container text-center">
        <h1 class="display-5 fw-bold mb-3">{{ $barangay->name }}</h1>
        <p class="lead mb-0">
            {{ $barangay->description ?? 'Welcome to ' . $barangay->name . ' Barangay Portal' }}
        </p>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <i class="fas fa-users fa-2x text-primary mb-3"></i>
                        <h3 class="fw-bold">{{ $stats['total_residents'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">Total Residents</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <i class="fas fa-home fa-2x text-primary mb-3"></i>
                        <h3 class="fw-bold">{{ $stats['total_households'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">Households</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <i class="fas fa-file-alt fa-2x text-primary mb-3"></i>
                        <h3 class="fw-bold">{{ $stats['documents_issued'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">Documents Issued</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <i class="fas fa-briefcase fa-2x text-primary mb-3"></i>
                        <h3 class="fw-bold">{{ $stats['active_permits'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">Active Permits</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Announcements Section -->
@if($announcements->count() > 0)
<section class="py-5">
    <div class="container">
        <h2 class="fw-bold text-center mb-5">
            <i class="fas fa-bullhorn text-primary me-2"></i> Announcements
        </h2>

        <div class="row g-4">
            @foreach($announcements as $announcement)
            <div class="col-12">
                <div class="card shadow-sm border-0 {{ $announcement->pin_to_top ? 'border-start border-primary border-4' : '' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    @if($announcement->pin_to_top)
                                    <i class="fas fa-thumbtack text-primary me-2"></i>
                                    @endif
                                    <h5 class="mb-0 fw-bold">{{ $announcement->title }}</h5>
                                </div>
                                <div class="d-flex gap-2 mb-2 flex-wrap">
                                    <span class="badge bg-{{ $announcement->getPriorityBadgeClass() }}">
                                        <i class="fas fa-flag"></i> {{ ucfirst($announcement->priority) }}
                                    </span>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i> {{ $announcement->published_at?->format('F d, Y') ?? $announcement->created_at->format('F d, Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <p class="text-muted mb-0">{{ $announcement->getExcerpt(200) }}</p>

                        @if(strlen(strip_tags($announcement->content)) > 200)
                        <button class="btn btn-sm btn-link p-0 mt-2" type="button"
                                data-bs-toggle="collapse" data-bs-target="#announcement-{{ $announcement->id }}">
                            Read more <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="collapse mt-2" id="announcement-{{ $announcement->id }}">
                            <div class="card card-body bg-light">
                                {!! nl2br(e($announcement->content)) !!}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Officials Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="fw-bold text-center mb-5">
            <i class="fas fa-user-tie text-primary me-2"></i> Barangay Officials
        </h2>
        
        <!-- Barangay Captain -->
        @if($captain)
        <div class="row mb-5">
            <div class="col-12">
                <h4 class="text-center mb-4 text-primary">Barangay Captain</h4>
                <div class="card border-0 shadow-lg mx-auto" style="max-width: 400px;">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            @if($captain->avatar)
                                <img src="{{ asset('storage/' . $captain->avatar) }}" 
                                     alt="{{ $captain->name }}" 
                                     class="rounded-circle" 
                                     style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center" 
                                     style="width: 120px; height: 120px;">
                                    <i class="fas fa-user text-white fa-3x"></i>
                                </div>
                            @endif
                        </div>
                        <h5 class="fw-bold mb-1">Hon. {{ $captain->name }}</h5>
                        <p class="text-muted mb-2">Barangay Captain</p>
                        @if($captain->term_start && $captain->term_end)
                        <p class="text-sm text-muted">
                            Term: {{ $captain->term_start->format('Y') }} - {{ $captain->term_end->format('Y') }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Barangay Councilors -->
        @if($councilors->count() > 0)
        <div class="row mb-5">
            <div class="col-12">
                <h4 class="text-center mb-4 text-success">Barangay Councilors</h4>
                <div class="row g-4">
                    @foreach($councilors as $councilor)
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    @if($councilor->avatar)
                                        <img src="{{ asset('storage/' . $councilor->avatar) }}" 
                                             alt="{{ $councilor->name }}" 
                                             class="rounded-circle" 
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" 
                                             style="width: 80px; height: 80px;">
                                            <i class="fas fa-user text-muted fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                                <h6 class="fw-bold mb-1">Kagawad {{ $councilor->name }}</h6>
                                <p class="text-muted small mb-2">
                                    {{ $councilor->committee_display ?? 'Barangay Councilor' }}
                                </p>
                                @if($councilor->term_start && $councilor->term_end)
                                <p class="text-xs text-muted">
                                    Term: {{ $councilor->term_start->format('Y') }} - {{ $councilor->term_end->format('Y') }}
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
        @if($staff->count() > 0 || $secretary || $treasurer)
        <div class="row mb-5">
            <div class="col-12">
                <h4 class="text-center mb-4 text-purple">Barangay Staff</h4>
                <div class="row g-4">
                    <!-- Secretary -->
                    @if($secretary)
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    @if($secretary->avatar)
                                        <img src="{{ asset('storage/' . $secretary->avatar) }}" 
                                             alt="{{ $secretary->name }}" 
                                             class="rounded-circle" 
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" 
                                             style="width: 80px; height: 80px;">
                                            <i class="fas fa-user text-muted fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                                <h6 class="fw-bold mb-1">{{ $secretary->name }}</h6>
                                <p class="text-muted small mb-2">Barangay Secretary</p>
                                @if($secretary->term_start && $secretary->term_end)
                                <p class="text-xs text-muted">
                                    Term: {{ $secretary->term_start->format('Y') }} - {{ $secretary->term_end->format('Y') }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Treasurer -->
                    @if($treasurer)
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    @if($treasurer->avatar)
                                        <img src="{{ asset('storage/' . $treasurer->avatar) }}" 
                                             alt="{{ $treasurer->name }}" 
                                             class="rounded-circle" 
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" 
                                             style="width: 80px; height: 80px;">
                                            <i class="fas fa-user text-muted fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                                <h6 class="fw-bold mb-1">{{ $treasurer->name }}</h6>
                                <p class="text-muted small mb-2">Barangay Treasurer</p>
                                @if($treasurer->term_start && $treasurer->term_end)
                                <p class="text-xs text-muted">
                                    Term: {{ $treasurer->term_start->format('Y') }} - {{ $treasurer->term_end->format('Y') }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Other Staff -->
                    @foreach($staff as $member)
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    @if($member->avatar)
                                        <img src="{{ asset('storage/' . $member->avatar) }}" 
                                             alt="{{ $member->name }}" 
                                             class="rounded-circle" 
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" 
                                             style="width: 80px; height: 80px;">
                                            <i class="fas fa-user text-muted fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                                <h6 class="fw-bold mb-1">{{ $member->name }}</h6>
                                <p class="text-muted small mb-2">
                                    {{ $member->position_title ?? 'Barangay Staff' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Lupon Members -->
        @if($luponMembers->count() > 0)
        <div class="row">
            <div class="col-12">
                <h4 class="text-center mb-4 text-warning">Lupon ng Tagapamayapa</h4>
                <div class="row g-4">
                    @foreach($luponMembers as $lupon)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-3">
                                <div class="mb-2">
                                    @if($lupon->avatar)
                                        <img src="{{ asset('storage/' . $lupon->avatar) }}" 
                                             alt="{{ $lupon->name }}" 
                                             class="rounded-circle" 
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-user text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <h6 class="fw-bold mb-1 small">{{ $lupon->name }}</h6>
                                <p class="text-muted small mb-0">
                                    {{ $lupon->position_title ?? 'Lupon Member' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- No Officials Message -->
        @if(!$captain && $councilors->count() === 0 && $staff->count() === 0 && $luponMembers->count() === 0)
        <div class="text-center py-5">
            <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No officials listed for this barangay.</h5>
            <p class="text-muted">Please check back later for updates.</p>
        </div>
        @endif

    </div>
</section>

<!-- Quick Links Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <a href="{{ route('public.barangay.officials', $barangay) }}" class="card border-0 shadow-sm text-decoration-none h-100">
                    <div class="card-body p-4">
                        <i class="fas fa-users fa-2x text-primary mb-3"></i>
                        <h5 class="fw-bold">View All Officials</h5>
                        <p class="text-muted mb-0">Complete list of barangay officials</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('public.barangay.services', $barangay) }}" class="card border-0 shadow-sm text-decoration-none h-100">
                    <div class="card-body p-4">
                        <i class="fas fa-concierge-bell fa-2x text-success mb-3"></i>
                        <h5 class="fw-bold">Services</h5>
                        <p class="text-muted mb-0">Available barangay services</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('public.barangay.contact', $barangay) }}" class="card border-0 shadow-sm text-decoration-none h-100">
                    <div class="card-body p-4">
                        <i class="fas fa-phone fa-2x text-info mb-3"></i>
                        <h5 class="fw-bold">Contact Us</h5>
                        <p class="text-muted mb-0">Get in touch with the barangay</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.text-purple {
    color: #6f42c1 !important;
}
.card {
    transition: transform 0.2s ease-in-out;
}
.card:hover {
    transform: translateY(-5px);
}
</style>
@endpush