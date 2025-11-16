{{-- FILE: resources/views/resident/documents/show.blade.php --}}
@extends('layouts.resident')

@section('title', 'Document Request Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Document Request Details</h2>
        <p class="text-muted mb-0">Tracking #: <strong>{{ $documentRequest->tracking_number }}</strong></p>
    </div>
    <div>
        <a href="{{ route('resident.documents.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Requests
        </a>
    </div>
</div>

<div class="row">
    <!-- Main Request Information -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Request Information</h5>
                <span class="badge {{ $documentRequest->status_badge['class'] }}">{{ $documentRequest->status_badge['text'] }}</span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Document Type:</strong>
                        <p>{{ $documentRequest->documentType->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Copies Requested:</strong>
                        <p>{{ $documentRequest->copies_requested }} {{ Str::plural('copy', $documentRequest->copies_requested) }}</p>
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Purpose:</strong>
                    <div class="border p-3 rounded">
                        {{ $documentRequest->purpose }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Date Submitted:</strong>
                        <p>{{ $documentRequest->submitted_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Expected Processing Time:</strong>
                        <p>{{ $documentRequest->documentType->processing_days }} days</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Amount Paid:</strong>
                        <p class="text-success">₱{{ number_format($documentRequest->amount_paid, 2) }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Payment Method:</strong>
                        <p>{{ ucfirst($documentRequest->payment_method) }}</p>
                    </div>
                </div>

                @if($documentRequest->uploaded_files && count($documentRequest->uploaded_files) > 0)
                <div class="mb-3">
                    <strong>Uploaded Supporting Documents:</strong>
                    <div class="mt-2">
                        @foreach($documentRequest->uploaded_files as $file)
                            <a href="{{ asset('uploads/documents/' . $file) }}" 
                               target="_blank" class="btn btn-sm btn-outline-info me-2 mb-1">
                                <i class="fas fa-file"></i> {{ basename($file) }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($documentRequest->processing_notes)
                <hr>
                <div class="mb-3">
                    <strong>Processing Notes:</strong>
                    <div class="border p-3 rounded bg-light">
                        {{ $documentRequest->processing_notes }}
                    </div>
                    @if($documentRequest->processor)
                        <small class="text-muted">
                            Updated by {{ $documentRequest->processor->name }} 
                            @if($documentRequest->processed_at)
                                on {{ $documentRequest->processed_at->format('M j, Y g:i A') }}
                            @endif
                        </small>
                    @endif
                </div>
                @endif

                @if($documentRequest->status == 'rejected' && $documentRequest->rejection_reason)
                <hr>
                <div class="mb-3">
                    <strong>Rejection Reason:</strong>
                    <div class="alert alert-danger">
                        {{ $documentRequest->rejection_reason }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Status Timeline -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Request Timeline</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <!-- Submitted -->
                    <div class="timeline-item completed">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <h6>Request Submitted</h6>
                            <p class="text-muted mb-0">{{ $documentRequest->submitted_at->format('M j, Y g:i A') }}</p>
                        </div>
                    </div>

                    <!-- Processing -->
                    <div class="timeline-item {{ in_array($documentRequest->status, ['processing', 'approved', 'rejected']) ? 'completed' : 'pending' }}">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <h6>Processing Started</h6>
                            @if($documentRequest->processed_at)
                                <p class="text-muted mb-0">{{ $documentRequest->processed_at->format('M j, Y g:i A') }}</p>
                            @else
                                <p class="text-muted mb-0">Waiting to be processed</p>
                            @endif
                        </div>
                    </div>

                    <!-- Completed -->
                    <div class="timeline-item {{ $documentRequest->status == 'approved' ? 'completed' : ($documentRequest->status == 'rejected' ? 'rejected' : 'pending') }}">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <h6>
                                @if($documentRequest->status == 'approved')
                                    Request Approved
                                @elseif($documentRequest->status == 'rejected')
                                    Request Rejected
                                @else
                                    Awaiting Completion
                                @endif
                            </h6>
                            @if($documentRequest->approved_at)
                                <p class="text-muted mb-0">{{ $documentRequest->approved_at->format('M j, Y g:i A') }}</p>
                            @elseif($documentRequest->status == 'rejected')
                                <p class="text-muted mb-0">Request was rejected</p>
                            @else
                                <p class="text-muted mb-0">Processing in progress</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
       {{-- In resources/views/resident/documents/show.blade.php --}}
@if($documentRequest->status == 'approved')
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-check-circle"></i> Document Approved</h5>
    </div>
    <div class="card-body text-center">
        <div class="mb-3">
            <i class="fas fa-file-certificate fa-3x text-success"></i>
        </div>
        <h6>Your document is ready for pickup!</h6>
        <p class="text-muted">
            Your {{ $documentRequest->documentType->name }} has been approved and is ready at the barangay office.
        </p>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Important:</strong> Please visit the Barangay {{ $documentRequest->barangay->name }} Office to claim your printed document. Bring valid ID for verification.
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <p><strong>Tracking Number:</strong><br>{{ $documentRequest->tracking_number }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Approved Date:</strong><br>{{ $documentRequest->approved_at->format('M j, Y g:i A') }}</p>
            </div>
        </div>
    </div>
</div>
@endif
<!-- QR Code Tracking -->
<!-- QR Code Tracking -->
       <div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0">Track Your Request</h6>
    </div>
    <div class="card-body text-center">
        <div class="mb-3">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('track.request', $documentRequest->tracking_number)) }}" 
                 alt="QR Code" class="img-fluid" style="max-width: 200px;">
        </div>
        <p class="text-muted mb-0">Scan this QR code to quickly check your request status</p>
        
        <!-- Add download option -->
        <div class="mt-3">
            <a href="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode(route('track.request', $documentRequest->tracking_number)) }}&download=1" 
               download="document-request-{{ $documentRequest->tracking_number }}.png" 
               class="btn btn-sm btn-outline-primary">
                <i class="fas fa-download"></i> Download QR Code
            </a>
        </div>
    </div>
</div>
        <!-- Request Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Request Summary</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Status:</small>
                    <div><span class="badge {{ $documentRequest->status_badge['class'] }}">{{ $documentRequest->status_badge['text'] }}</span></div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Processing Time:</small>
                    <div>{{ $documentRequest->documentType->processing_days }} days</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Total Amount:</small>
                    <div>₱{{ number_format($documentRequest->amount_paid, 2) }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Barangay:</small>
                    <div>{{ $documentRequest->barangay->name }}</div>
                </div>
            </div>
        </div>

        <!-- Help & Support -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Need Help?</h6>
            </div>
            <div class="card-body">
                <p class="text-muted">If you have questions about your request, you can:</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-phone"></i> Call your barangay office</li>
                    <li><i class="fas fa-map-marker-alt"></i> Visit the barangay hall</li>
                    <li><i class="fas fa-envelope"></i> Send an email inquiry</li>
                </ul>
                <small class="text-muted">Reference your tracking number: <strong>{{ $documentRequest->tracking_number }}</strong></small>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline:before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #6c757d;
    background: white;
}

.timeline-item.completed .timeline-marker {
    border-color: #28a745;
    background: #28a745;
}

.timeline-item.rejected .timeline-marker {
    border-color: #dc3545;
    background: #dc3545;
}

.timeline-item.pending .timeline-marker {
    border-color: #6c757d;
    background: white;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}

/* Fix for badge text readability */
.badge {
    color: white !important;
    font-weight: 600;
}

/* Specific badge color overrides if needed */
.badge-warning {
    background-color: #ffc107 !important;
    color: #212529 !important; /* Dark text for warning badges */
}

.badge-info {
    background-color: #0dcaf0 !important;
}

.badge-success {
    background-color: #198754 !important;
}

.badge-danger {
    background-color: #dc3545 !important;
}

.badge-primary {
    background-color: #0d6efd !important;
}

.badge-secondary {
    background-color: #6c757d !important;
}
</style>
@endpush

@endsection