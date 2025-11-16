@extends('layouts.abc')

@section('title', $barangay->name . ' - Barangay Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('abc.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('abc.barangays.index') }}">Barangays</a></li>
                    <li class="breadcrumb-item active">{{ $barangay->name }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">{{ $barangay->name }}</h1>
            <p class="mb-0 text-muted">Barangay Management Details</p>
        </div>
        <div>
            <a href="{{ route('abc.barangays.edit', $barangay) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Barangay
            </a>
            <a href="{{ route('abc.barangays.index') }}" class="btn btn-secondary ml-2">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Status Alert -->
    @if(!$barangay->is_active)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Inactive Barangay:</strong> This barangay is currently inactive and not accessible to the public.
        </div>
    @endif

    <div class="row">
        <!-- Basic Information Card -->
        <div class="col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                </div>
                <div class="card-body text-center">
                    @if($barangay->logo)
                        <img src="{{ asset('uploads/logos/' . $barangay->logo) }}" 
                             alt="{{ $barangay->name }}" class="img-fluid rounded-circle mb-3" 
                             style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                             style="width: 120px; height: 120px; font-size: 2rem;">
                            {{ strtoupper(substr($barangay->name, 0, 2)) }}
                        </div>
                    @endif
                    
                    <h5 class="mb-1">{{ $barangay->name }}</h5>
                    <p class="text-muted mb-3">{{ $barangay->slug }}</p>
                    
                    <div class="text-left">
                        @if($barangay->contact_number)
                            <p class="mb-2">
                                <i class="fas fa-phone text-muted mr-2"></i>
                                {{ $barangay->contact_number }}
                            </p>
                        @endif
                        
                        @if($barangay->email)
                            <p class="mb-2">
                                <i class="fas fa-envelope text-muted mr-2"></i>
                                {{ $barangay->email }}
                            </p>
                        @endif
                        
                        @if($barangay->address)
                            <p class="mb-2">
                                <i class="fas fa-map-marker-alt text-muted mr-2"></i>
                                {{ $barangay->address }}
                            </p>
                        @endif
                        
                        <p class="mb-2">
                            <i class="fas fa-calendar text-muted mr-2"></i>
                            Created: {{ $barangay->created_at->format('M d, Y') }}
                        </p>
                        
                        <p class="mb-0">
                            <i class="fas fa-{{ $barangay->is_active ? 'check-circle text-success' : 'times-circle text-danger' }} mr-2"></i>
                            {{ $barangay->is_active ? 'Active' : 'Inactive' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- QR Code Card -->
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">QR Code & Public URL</h6>
                    <button class="btn btn-sm btn-outline-secondary" onclick="generateQr({{ $barangay->id }})">
                        <i class="fas fa-sync"></i>
                    </button>
                </div>
                <div class="card-body text-center">
                    @if($barangay->qr_code)
                        <img src="{{ asset('uploads/qr-codes/' . $barangay->qr_code) }}" 
                             alt="QR Code" class="img-fluid mb-3" style="max-width: 200px;">
                    @else
                        <p class="text-muted">No QR code generated</p>
                    @endif
                    
                    <div class="bg-light p-2 rounded text-left">
                        <strong>Public URL:</strong><br>
                        <a href="{{ url('/b/' . $barangay->slug) }}" target="_blank" class="text-decoration-none">
                            {{ url('/b/' . $barangay->slug) }}
                        </a>
                    </div>
                    
                    <div class="bg-light p-2 rounded text-left mt-2">
                        <strong>Registration URL:</strong><br>
                        <a href="{{ url('/b/' . $barangay->slug . '/register') }}" target="_blank" class="text-decoration-none">
                            {{ url('/b/' . $barangay->slug . '/register') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics and Details -->
        <div class="col-xl-8">
            <!-- Statistics Row -->
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Officials</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['officials']) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Residents</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_residents']) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-home fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Verified Residents</div>
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto">
                                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ number_format($stats['verified_residents']) }}</div>
                                        </div>
                                        <div class="col">
                                            <div class="progress progress-sm mr-2">
                                                <div class="progress-bar bg-info" role="progressbar" 
                                                     style="width: {{ $stats['total_residents'] > 0 ? ($stats['verified_residents'] / $stats['total_residents']) * 100 : 0 }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Verification</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['pending_residents']) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services Statistics -->
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <div class="card-body text-center">
                            <i class="fas fa-file-alt fa-3x text-primary mb-3"></i>
                            <h5>{{ number_format($stats['document_requests']) }}</h5>
                            <p class="text-muted mb-0">Document Requests</p>
                            <small class="text-warning">{{ number_format($stats['pending_documents']) }} pending</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <div class="card-body text-center">
                            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                            <h5>{{ number_format($stats['complaints']) }}</h5>
                            <p class="text-muted mb-0">Complaints</p>
                            <small class="text-warning">{{ number_format($stats['active_complaints']) }} active</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <div class="card-body text-center">
                            <i class="fas fa-certificate fa-3x text-success mb-3"></i>
                            <h5>{{ number_format($stats['business_permits']) }}</h5>
                            <p class="text-muted mb-0">Business Permits</p>
                            <small class="text-success">{{ number_format($stats['active_permits']) }} active</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description and Social Media -->
            @if($barangay->description || $barangay->social_media)
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Additional Information</h6>
                    </div>
                    <div class="card-body">
                        @if($barangay->description)
                            <h6>Description</h6>
                            <p class="text-muted">{{ $barangay->description }}</p>
                        @endif

                        @if($barangay->social_media && count(array_filter($barangay->social_media)))
                            <h6>Social Media</h6>
                            <div class="d-flex">
                                @if(isset($barangay->social_media['facebook']) && $barangay->social_media['facebook'])
                                    <a href="{{ $barangay->social_media['facebook'] }}" target="_blank" class="btn btn-outline-primary btn-sm mr-2">
                                        <i class="fab fa-facebook"></i> Facebook
                                    </a>
                                @endif
                                @if(isset($barangay->social_media['instagram']) && $barangay->social_media['instagram'])
                                    <a href="{{ $barangay->social_media['instagram'] }}" target="_blank" class="btn btn-outline-danger btn-sm">
                                        <i class="fab fa-instagram"></i> Instagram
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Recent Officials -->
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Barangay Officials & Staff</h6>
                    <a href="{{ route('abc.users.index', ['barangay' => $barangay->id]) }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    @if($barangay->users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($barangay->users->take(10) as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($user->profile_photo)
                                                        <img src="{{ asset('uploads/photos/' . $user->profile_photo) }}" 
                                                             class="rounded-circle mr-2" style="width: 30px; height: 30px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mr-2" 
                                                             style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @foreach($user->roles as $role)
                                                    <span class="badge badge-primary badge-sm">{{ $role->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ $user->contact_number }}</td>
                                            <td>
                                                @if($user->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h6>No Officials Assigned</h6>
                            <p class="text-muted mb-3">No barangay officials or staff have been assigned yet.</p>
                            <a href="{{ route('abc.users.create', ['barangay' => $barangay->id]) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add Official
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function generateQr(barangayId) {
    fetch(`/admin/barangays/${barangayId}/generate-qr`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error generating QR code');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error generating QR code');
    });
}
</script>
@endpush