@extends('layouts.public')

@section('title', 'Barangays - UBMS')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
<li class="breadcrumb-item active">Barangays</li>
@endsection

@section('content')
<!-- Page Header -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3">Our Barangays</h1>
                <p class="lead mb-0">
                    Discover the communities we serve. Find your barangay and access local services.
                </p>
            </div>
            <div class="col-lg-4 text-end">
                <i class="fas fa-map-marker-alt" style="font-size: 8rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Search and Filter -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="searchBarangay" placeholder="Search barangay by name...">
                </div>
            </div>
            <div class="col-lg-3">
                <select class="form-select" id="sortBarangay">
                    <option value="name">Sort by Name</option>
                    <option value="registered">Sort by Registered</option>
                    <option value="households">Sort by Households</option>
                </select>
            </div>
            <div class="col-lg-3">
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary" id="gridView">
                        <i class="fas fa-th"></i>
                    </button>
                    <button class="btn btn-outline-secondary active" id="listView">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Barangays Grid/List -->
<section class="py-5">
    <div class="container">
        @if($barangays->count() > 0)
            <!-- Stats Summary -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="alert alert-info">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <strong>{{ $barangays->count() }}</strong><br>
                                <small>Total Barangays</small>
                            </div>
                            <div class="col-md-3">
                                <strong>{{ number_format($barangays->sum(fn($b) => $b->residentProfiles()->count())) }}</strong><br>
                                <small>Total Registered</small>
                            </div>
                            <div class="col-md-3">
                                <strong>{{ number_format($barangays->sum(fn($b) => $b->residentProfiles()->verified()->count())) }}</strong><br>
                                <small>Total Verified</small>
                            </div>
                            <div class="col-md-3">
                                <strong>{{ number_format($barangays->sum(fn($b) => $b->residentProfiles()->householdHeads()->count())) }}</strong><br>
                                <small>Total Households</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barangays List -->
            <div id="barangaysList">
                <div class="row" id="barangaysContainer">
                    @foreach($barangays as $barangay)
                    <div class="col-12 mb-4 barangay-item" data-name="{{ strtolower($barangay->name) }}" data-registered="{{ $barangay->residentProfiles()->count() }}" data-households="{{ $barangay->residentProfiles()->householdHeads()->count() }}">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-lg-3 text-center mb-3 mb-lg-0">
                                        @if($barangay->logo_url)
                                        <img src="{{ $barangay->logo_url }}" class="img-fluid rounded-circle" style="width: 120px; height: 120px; object-fit: cover;" alt="{{ $barangay->name }}">
                                        @else
                                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                            <i class="fas fa-map-marker-alt fa-3x"></i>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <h4 class="fw-bold mb-2">{{ $barangay->name }}</h4>
                                        <p class="text-muted mb-3">
                                            {{ Str::limit($barangay->description ?? 'A vibrant community committed to serving its residents with excellence and dedication.', 150) }}
                                        </p>
                                        
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="border-end">
                                                    <strong class="text-primary">{{ number_format($barangay->residentProfiles()->count()) }}</strong><br>
                                                    <small class="text-muted">Registered</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="border-end">
                                                    <strong class="text-success">{{ number_format($barangay->residentProfiles()->verified()->count()) }}</strong><br>
                                                    <small class="text-muted">Verified</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <strong class="text-info">{{ number_format($barangay->residentProfiles()->householdHeads()->count()) }}</strong><br>
                                                <small class="text-muted">Households</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-3 text-center">
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('public.barangay.home', $barangay) }}" class="btn btn-primary">
                                                <i class="fas fa-home me-2"></i>Visit Barangay
                                            </a>
                                            <a href="{{ route('public.barangay.services', $barangay) }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-concierge-bell me-2"></i>Services
                                            </a>

                                            <a href="{{ route('public.barangay.register', $barangay->slug) }}" class="btn btn-outline-success">
                                                <i class="fas fa-user-plus me-2"></i>Register
                                            </a>

                                        </div>
                                        
                                        @if($barangay->contact_number || $barangay->email)
                                        <div class="mt-3 pt-3 border-top">
                                            <small class="text-muted d-block mb-1">Quick Contact</small>
                                            @if($barangay->contact_number)
                                            <div class="mb-1">
                                                <i class="fas fa-phone text-success me-1"></i>
                                                <small>{{ $barangay->contact_number }}</small>
                                            </div>
                                            @endif
                                            @if($barangay->email)
                                            <div>
                                                <i class="fas fa-envelope text-primary me-1"></i>
                                                <small>{{ $barangay->email }}</small>
                                            </div>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Grid View Template (Initially Hidden) -->
            <div id="barangaysGrid" style="display: none;">
                <div class="row" id="barangaysGridContainer">
                    @foreach($barangays as $barangay)
                    <div class="col-lg-4 col-md-6 mb-4 barangay-item-grid" data-name="{{ strtolower($barangay->name) }}" data-registered="{{ $barangay->residentProfiles()->count() }}" data-households="{{ $barangay->residentProfiles()->householdHeads()->count() }}">
                        <div class="card h-100 border-0 shadow-sm">
                            @if($barangay->logo_url)
                            <img src="{{ $barangay->logo_url }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $barangay->name }}">
                            @else
                            <div class="card-img-top bg-primary d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-map-marker-alt text-white" style="font-size: 4rem;"></i>
                            </div>
                            @endif
                            
                            <div class="card-body">
                                <h5 class="card-title fw-bold">{{ $barangay->name }}</h5>
                                <p class="card-text text-muted">
                                    {{ Str::limit($barangay->description ?? 'A vibrant community committed to serving its residents.', 80) }}
                                </p>
                                
                                <div class="row text-center mb-3">
                                    <div class="col-4">
                                        <small class="text-muted d-block">Registered</small>
                                        <strong class="text-primary">{{ number_format($barangay->residentProfiles()->count()) }}</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Verified</small>
                                        <strong class="text-success">{{ number_format($barangay->residentProfiles()->verified()->count()) }}</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Households</small>
                                        <strong class="text-info">{{ number_format($barangay->residentProfiles()->householdHeads()->count()) }}</strong>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-footer bg-transparent">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('public.barangay.home', $barangay) }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-right me-2"></i>Visit Barangay
                                    </a>
                                    <div class="row">
                                        <div class="col-6">
                                            <a href="{{ route('public.barangay.services', $barangay) }}" class="btn btn-outline-secondary btn-sm w-100">
                                                <i class="fas fa-concierge-bell"></i>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ route('public.barangay.register', $barangay) }}" class="btn btn-outline-success btn-sm w-100">
                                                <i class="fas fa-user-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- No Results Message -->
            <div id="noResults" style="display: none;">
                <div class="text-center py-5">
                    <i class="fas fa-search text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">No Barangays Found</h4>
                    <p class="text-muted">Try adjusting your search criteria.</p>
                </div>
            </div>

        @else
        <!-- Empty State -->
        <div class="text-center py-5">
            <i class="fas fa-map-marker-alt text-muted" style="font-size: 6rem;"></i>
            <h3 class="mt-4 text-muted">No Barangays Available</h3>
            <p class="text-muted">There are currently no active barangays in the system. Please check back later.</p>
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Return to Home
            </a>
        </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
    $(document).ready(function() {
        // Search functionality
        $('#searchBarangay').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            let hasResults = false;
            
            $('.barangay-item, .barangay-item-grid').each(function() {
                const barangayName = $(this).data('name');
                if (barangayName.includes(searchTerm)) {
                    $(this).show();
                    hasResults = true;
                } else {
                    $(this).hide();
                }
            });
            
            $('#noResults').toggle(!hasResults);
        });
        
        // Sort functionality
        $('#sortBarangay').on('change', function() {
            const sortBy = $(this).val();
            const isListView = $('#barangaysList').is(':visible');
            const container = isListView ? '#barangaysContainer' : '#barangaysGridContainer';
            const itemClass = isListView ? '.barangay-item' : '.barangay-item-grid';
            
            const items = $(container).children(itemClass).get();
            
            items.sort(function(a, b) {
                if (sortBy === 'name') {
                    return $(a).data('name').localeCompare($(b).data('name'));
                } else if (sortBy === 'registered') {
                    return $(b).data('registered') - $(a).data('registered');
                } else if (sortBy === 'households') {
                    return $(b).data('households') - $(a).data('households');
                }
            });
            
            $(container).html(items);
        });
        
        // View toggle
        $('#listView').on('click', function() {
            $(this).addClass('active');
            $('#gridView').removeClass('active');
            $('#barangaysList').show();
            $('#barangaysGrid').hide();
        });
        
        $('#gridView').on('click', function() {
            $(this).addClass('active');
            $('#listView').removeClass('active');
            $('#barangaysList').hide();
            $('#barangaysGrid').show();
        });
    });
</script>
@endpush
@endsection