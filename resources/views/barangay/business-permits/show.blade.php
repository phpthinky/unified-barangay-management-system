@extends('layouts.admin')

@section('title', 'Permit Type Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ $businessPermitType->name }}</h1>
        <div>
            <a href="{{ route('admin.business-permit-types.edit', $businessPermitType) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('admin.business-permit-types.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Total Permits</div>
                            <h3 class="mb-0">{{ $stats['total_permits'] }}</h3>
                        </div>
                        <i class="fas fa-file-contract fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Pending</div>
                            <h3 class="mb-0">{{ $stats['pending_permits'] }}</h3>
                        </div>
                        <i class="fas fa-clock fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Active</div>
                            <h3 class="mb-0">{{ $stats['active_permits'] }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Total Revenue</div>
                            <h3 class="mb-0">₱{{ number_format($stats['total_revenue'], 2) }}</h3>
                        </div>
                        <i class="fas fa-money-bill-wave fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Name</small>
                            <p class="mb-0 fw-bold">{{ $businessPermitType->name }}</p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <small class="text-muted">Category</small>
                            <p class="mb-0">
                                <span class="badge bg-info">{{ $businessPermitType->category_display }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <small class="text-muted">Status</small>
                            <p class="mb-0">
                                <span class="badge bg-{{ $businessPermitType->is_active ? 'success' : 'secondary' }}">
                                    {{ $businessPermitType->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    @if($businessPermitType->description)
                    <div class="mb-3">
                        <small class="text-muted">Description</small>
                        <p class="mb-0">{{ $businessPermitType->description }}</p>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <small class="text-muted">Base Fee</small>
                            <p class="mb-0 fw-bold text-success">₱{{ number_format($businessPermitType->base_fee, 2) }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted">Processing Time</small>
                            <p class="mb-0">{{ $businessPermitType->processing_days }} days</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted">Validity</small>
                            <p class="mb-0">{{ $businessPermitType->validity_months }} months</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Fees -->
            @if(!empty($businessPermitType->additional_fees) && count($businessPermitType->additional_fees) > 0)
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-money-bill me-2"></i>Additional Fees</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fee Name</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($businessPermitType->additional_fees as $fee)
                                <tr>
                                    <td>{{ $fee['name'] ?? 'N/A' }}</td>
                                    <td class="text-end">₱{{ number_format($fee['amount'] ?? 0, 2) }}</td>
                                </tr>
                                @endforeach
                                <tr class="fw-bold">
                                    <td>Total Fees</td>
                                    <td class="text-end text-success">
                                        ₱{{ number_format($businessPermitType->total_fees, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Requirements -->
            @if(!empty($businessPermitType->requirements) && count($businessPermitType->requirements) > 0)
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-list-check me-2"></i>Requirements</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @foreach($businessPermitType->requirements as $requirement)
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>{{ $requirement }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Recent Permits -->
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Permits</h5>
                </div>
                <div class="card-body">
                    @if($recentPermits->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Applicant</th>
                                    <th>Barangay</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPermits as $permit)
                                <tr>
                                    <td>{{ $permit->applicant->name }}</td>
                                    <td>{{ $permit->barangay->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $permit->status == 'approved' ? 'success' : ($permit->status == 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($permit->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $permit->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center py-4 mb-0">No permits issued yet</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Additional Clearances -->
            <div class="card shadow mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Additional Clearances</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-{{ $businessPermitType->requires_inspection ? 'check-circle text-success' : 'times-circle text-muted' }} me-2"></i>
                        <span>Business Inspection</span>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-{{ $businessPermitType->requires_fire_safety ? 'check-circle text-success' : 'times-circle text-muted' }} me-2"></i>
                        <span>Fire Safety Certificate</span>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-{{ $businessPermitType->requires_health_permit ? 'check-circle text-success' : 'times-circle text-muted' }} me-2"></i>
                        <span>Health Permit</span>
                    </div>
                    <div class="d-flex align-items-center mb-0">
                        <i class="fas fa-{{ $businessPermitType->requires_environmental_clearance ? 'check-circle text-success' : 'times-circle text-muted' }} me-2"></i>
                        <span>Environmental Clearance</span>
                    </div>
                </div>
            </div>

            <!-- Template Fields -->
            @if(!empty($businessPermitType->template_fields) && count($businessPermitType->template_fields) > 0)
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Template Fields</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @foreach($businessPermitType->template_fields as $field)
                        <li class="mb-2">
                            <i class="fas fa-angle-right text-muted me-2"></i>{{ $field }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.business-permit-types.edit', $businessPermitType) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Permit Type
                        </a>
                        @if($businessPermitType->businessPermits()->count() == 0)
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-2"></i>Delete
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
@if($businessPermitType->businessPermits()->count() == 0)
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete Permit Type</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.business-permit-types.destroy', $businessPermitType) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong>{{ $businessPermitType->name }}</strong>?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection