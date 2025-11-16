@extends('layouts.barangay')

@section('title', 'Process Business Permit')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Process Business Permit</h1>
        <a href="{{ route('barangay.business-permits.show', $businessPermit) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>

    <div class="row">
        <!-- Left Column - Permit Details -->
        <div class="col-lg-8">
            <!-- Business Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Business Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Tracking Number</small>
                            <p class="mb-0 fw-bold">{{ $businessPermit->tracking_number }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Date Applied</small>
                            <p class="mb-0">{{ $businessPermit->created_at->format('F d, Y') }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Business Name</small>
                            <p class="mb-0 fw-bold">{{ $businessPermit->business_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Business Type</small>
                            <p class="mb-0">{{ $businessPermit->business_type }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Business Address</small>
                        <p class="mb-0">{{ $businessPermit->business_address }}</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Owner Name</small>
                            <p class="mb-0">{{ $businessPermit->owner_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Contact Number</small>
                            <p class="mb-0">{{ $businessPermit->contact_number }}</p>
                        </div>
                    </div>

                    @if($businessPermit->email)
                    <div class="mb-3">
                        <small class="text-muted">Email</small>
                        <p class="mb-0">{{ $businessPermit->email }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Applicant Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Applicant Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Name</small>
                            <p class="mb-0">{{ $businessPermit->applicant->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Email</small>
                            <p class="mb-0">{{ $businessPermit->applicant->email }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Contact Number</small>
                            <p class="mb-0">{{ $businessPermit->applicant->contact_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Address</small>
                            <p class="mb-0">{{ $businessPermit->applicant->address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permit Type Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-certificate me-2"></i>Permit Type Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Permit Type</small>
                            <p class="mb-0 fw-bold">{{ $businessPermit->businessPermitType->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Category</small>
                            <p class="mb-0">
                                <span class="badge bg-info">
                                    {{ ucwords(str_replace('_', ' ', $businessPermit->businessPermitType->category)) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <small class="text-muted">Base Fee</small>
                            <p class="mb-0 text-success fw-bold">₱{{ number_format($businessPermit->businessPermitType->base_fee, 2) }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted">Processing Time</small>
                            <p class="mb-0">{{ $businessPermit->businessPermitType->processing_days }} days</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted">Validity</small>
                            <p class="mb-0">{{ $businessPermit->businessPermitType->validity_months }} months</p>
                        </div>
                    </div>

                    @php
                        $additionalFees = is_array($businessPermit->businessPermitType->additional_fees) ? $businessPermit->businessPermitType->additional_fees : [];
                    @endphp
                    @if(count($additionalFees) > 0)
                    <hr>
                    <h6>Fee Breakdown:</h6>
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <td>Base Fee</td>
                                <td class="text-end">₱{{ number_format($businessPermit->businessPermitType->base_fee, 2) }}</td>
                            </tr>
                            @foreach($additionalFees as $fee)
                            <tr>
                                <td>{{ is_array($fee) ? ($fee['name'] ?? 'N/A') : 'N/A' }}</td>
                                <td class="text-end">₱{{ is_array($fee) ? number_format($fee['amount'] ?? 0, 2) : '0.00' }}</td>
                            </tr>
                            @endforeach
                            <tr class="fw-bold">
                                <td>Total</td>
                                <td class="text-end text-success">₱{{ number_format($businessPermit->total_fees, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    @endif

                    @php
                        $requirements = is_array($businessPermit->businessPermitType->requirements) ? $businessPermit->businessPermitType->requirements : [];
                    @endphp
                    @if(count($requirements) > 0)
                    <hr>
                    <h6>Requirements:</h6>
                    <ul class="mb-0">
                        @foreach($requirements as $requirement)
                        <li>{{ $requirement }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Actions -->
        <div class="col-lg-4">
            <!-- Approve Form -->
            <div class="card shadow mb-4 border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Approve Permit</h5>
                </div>
                <form action="{{ route('barangay.business-permits.approve', $businessPermit) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Permit Number (Optional)</label>
                            <input type="text" name="permit_number" class="form-control @error('permit_number') is-invalid @enderror" 
                                   placeholder="Auto-generated if blank" value="{{ old('permit_number') }}">
                            @error('permit_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Leave blank to auto-generate</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Expiration Date (Optional)</label>
                            <input type="date" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror" 
                                   value="{{ old('expires_at', now()->addMonths($businessPermit->businessPermitType->validity_months)->format('Y-m-d')) }}" 
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            @error('expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Default: {{ $businessPermit->businessPermitType->validity_months }} months from now</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Remarks (Optional)</label>
                            <textarea name="remarks" rows="3" class="form-control @error('remarks') is-invalid @enderror" 
                                      placeholder="Additional notes...">{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Are you sure you want to approve this permit?')">
                            <i class="fas fa-check me-2"></i>Approve Permit
                        </button>
                    </div>
                </form>
            </div>

            <!-- Reject Form -->
            <div class="card shadow border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-times-circle me-2"></i>Reject Permit</h5>
                </div>
                <form action="{{ route('barangay.business-permits.reject', $businessPermit) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" rows="4" class="form-control @error('rejection_reason') is-invalid @enderror" 
                                      placeholder="Please specify the reason for rejection..." required>{{ old('rejection_reason') }}</textarea>
                            @error('rejection_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to reject this permit?')">
                            <i class="fas fa-times me-2"></i>Reject Permit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection