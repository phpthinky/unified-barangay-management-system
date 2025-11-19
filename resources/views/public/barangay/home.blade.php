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

<!-- Officials Section - Organizational Chart -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="fw-bold text-center mb-5">
            <i class="fas fa-sitemap text-primary me-2"></i> Barangay Organizational Chart
        </h2>

        @if($officials->count() > 0)
            <div class="row g-4 justify-content-center">
                @foreach($officials as $official)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                @if($official->avatar)
                                    <img src="{{ asset('storage/' . $official->avatar) }}"
                                         alt="{{ $official->name }}"
                                         class="rounded-circle"
                                         style="width: 100px; height: 100px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center"
                                         style="width: 100px; height: 100px;">
                                        <i class="fas fa-user text-muted fa-3x"></i>
                                    </div>
                                @endif
                            </div>
                            <h6 class="fw-bold mb-1">{{ $official->name }}</h6>
                            <span class="badge bg-{{ $official->getPositionBadgeClass() }} mb-2">
                                {{ $official->position }}
                            </span>
                            @if($official->committee)
                            <p class="text-muted small mb-2">
                                <i class="fas fa-briefcase"></i> {{ $official->committee }}
                            </p>
                            @endif
                            @if($official->contact_number)
                            <p class="text-muted small mb-2">
                                <i class="fas fa-phone"></i> {{ $official->contact_number }}
                            </p>
                            @endif
                            <p class="text-xs text-muted mb-0">
                                <i class="fas fa-calendar"></i> {{ $official->term_start->format('Y') }} - {{ $official->term_end->format('Y') }}
                            </p>
                            @if($official->description)
                            <p class="text-muted small mt-2 mb-0">
                                {{ \Illuminate\Support\Str::limit($official->description, 80) }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-sitemap fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No organizational chart available</h5>
                <p class="text-muted">The organizational chart will be displayed here once officials are added.</p>
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