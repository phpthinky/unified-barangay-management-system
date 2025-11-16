@extends('layouts.abc')

@section('title', 'User Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">User Profile</h1>
        <div>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit User
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Users
            </a>
        </div>
    </div>

    <div class="row">
        <!-- User Information -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" 
                                 alt="{{ $user->name }}" 
                                 class="rounded-circle img-thumbnail" 
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center" 
                                 style="width: 150px; height: 150px;">
                                <i class="fas fa-user text-white fa-3x"></i>
                            </div>
                        @endif
                    </div>
                    
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-2">{{ $user->position_title ?: ucwords(str_replace('-', ' ', $user->getRoleNames()->first())) }}</p>
                    
                    @if($user->barangay)
                        <p class="mb-3">
                            <span class="badge bg-info">{{ $user->barangay->name }}</span>
                        </p>
                    @endif

                    <!-- Status Badges -->
                    <div class="mb-3">
                        @if($user->is_active && !$user->is_archived)
                            <span class="badge bg-success">Active</span>
                        @elseif($user->is_archived)
                            <span class="badge bg-danger">Archived</span>
                        @else
                            <span class="badge bg-warning">Inactive</span>
                        @endif

                        @if($user->hasRole('barangay-councilor'))
                            <span class="badge bg-primary">Kagawad {{ $user->councilor_number }}</span>
                        @endif
                    </div>

                    <!-- Quick Actions -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </a>
                        @if(!$user->hasRole('municipality-admin'))
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-{{ $user->is_active ? 'warning' : 'success' }} w-100">
                                <i class="fas fa-power-off me-2"></i>{{ $user->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-address-card me-2"></i>Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Email</small>
                        <strong>{{ $user->email }}</strong>
                    </div>
                    @if($user->phone_number)
                    <div class="mb-3">
                        <small class="text-muted d-block">Phone</small>
                        <strong>{{ $user->phone_number }}</strong>
                    </div>
                    @endif
                    @if($user->address)
                    <div class="mb-0">
                        <small class="text-muted d-block">Address</small>
                        <strong>{{ $user->address }}</strong>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Term Information (if applicable) -->
            @if($user->term_start || $user->term_end)
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar me-2"></i>Term Information</h5>
                </div>
                <div class="card-body">
                    @if($user->term_start)
                    <div class="mb-2">
                        <small class="text-muted d-block">Term Start</small>
                        <strong>{{ $user->term_start->format('F d, Y') }}</strong>
                    </div>
                    @endif
                    @if($user->term_end)
                    <div class="mb-0">
                        <small class="text-muted d-block">Term End</small>
                        <strong>{{ $user->term_end->format('F d, Y') }}</strong>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Councilor Information -->
            @if($user->hasRole('barangay-councilor'))
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Councilor Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted d-block">Position</small>
                        <strong>Kagawad {{ $user->councilor_number }}</strong>
                    </div>
                    @if($user->committee)
                    <div class="mb-0">
                        <small class="text-muted d-block">Committee</small>
                        <strong>{{ $user->committee_display }}</strong>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Statistics & Activity -->
        <div class="col-lg-8">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small">Documents</div>
                                    <h4 class="mb-0">{{ $stats['documents_processed'] }}</h4>
                                </div>
                                <i class="fas fa-file-alt fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small">Complaints</div>
                                    <h4 class="mb-0">{{ $stats['complaints_handled'] }}</h4>
                                </div>
                                <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small">Permits</div>
                                    <h4 class="mb-0">{{ $stats['permits_processed'] }}</h4>
                                </div>
                                <i class="fas fa-certificate fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small">Verified</div>
                                    <h4 class="mb-0">{{ $stats['residents_verified'] }}</h4>
                                </div>
                                <i class="fas fa-user-check fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Tabs -->
            <div class="card shadow">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#documents-tab">
                                <i class="fas fa-file-alt me-2"></i>Documents
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#complaints-tab">
                                <i class="fas fa-exclamation-triangle me-2"></i>Complaints
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#permits-tab">
                                <i class="fas fa-certificate me-2"></i>Permits
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Documents Tab -->
                        <div class="tab-pane fade show active" id="documents-tab">
                            @if($recentActivity['documents']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Document Type</th>
                                            <th>Requestor</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentActivity['documents'] as $doc)
                                        <tr>
                                            <td>{{ $doc->documentType->name }}</td>
                                            <td>{{ $doc->user->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $doc->status == 'approved' ? 'success' : ($doc->status == 'rejected' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($doc->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $doc->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-muted text-center py-4">No document activity yet</p>
                            @endif
                        </div>

                        <!-- Complaints Tab -->
                        <div class="tab-pane fade" id="complaints-tab">
                            @if($recentActivity['complaints']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Complainant</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentActivity['complaints'] as $complaint)
                                        <tr>
                                            <td>{{ $complaint->complaintType->name }}</td>
                                            <td>{{ $complaint->complainant->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $complaint->status == 'resolved' ? 'success' : ($complaint->status == 'in_process' ? 'warning' : 'info') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-muted text-center py-4">No complaint activity yet</p>
                            @endif
                        </div>

                        <!-- Permits Tab -->
                        <div class="tab-pane fade" id="permits-tab">
                            @if($recentActivity['permits']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Permit Type</th>
                                            <th>Applicant</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentActivity['permits'] as $permit)
                                        <tr>
                                            <td>{{ $permit->businessPermitType->name }}</td>
                                            <td>{{ $permit->applicant->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $permit->status == 'approved' ? 'success' : ($permit->status == 'rejected' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($permit->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $permit->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-muted text-center py-4">No permit activity yet</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection