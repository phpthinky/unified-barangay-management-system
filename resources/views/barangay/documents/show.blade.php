{{-- FILE: resources/views/barangay/documents/show.blade.php --}}
@extends('layouts.barangay')

@section('title', 'Document Request Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Document Request Details</h2>
        <p class="text-muted mb-0">Tracking #: <strong>{{ $documentRequest->tracking_number }}</strong></p>
    </div>
    <div>
        <a href="{{ route('barangay.documents.index') }}" class="btn btn-secondary">
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
                        <strong>Amount Paid:</strong>
                        <p>₱{{ number_format($documentRequest->amount_paid, 2) }}</p>
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Purpose:</strong>
                    <div class="border p-3 rounded">
                        {{ $documentRequest->purpose }}
                    </div>
                </div>

                @if($documentRequest->form_data && count($documentRequest->form_data) > 0)
                <div class="mb-3">
                    <strong>Additional Information:</strong>
                    <div class="border p-3 rounded">
                        @foreach($documentRequest->form_data as $key => $value)
                            <div class="mb-1">
                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> 
                                {{ is_array($value) ? implode(', ', $value) : $value }}
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Date Submitted:</strong>
                        <p>{{ $documentRequest->submitted_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Processing Time:</strong>
                        @if($documentRequest->status == 'approved')
                            <p class="text-success">{{ $documentRequest->approved_at->diffInDays($documentRequest->submitted_at) }} days</p>
                        @elseif($documentRequest->status == 'processing')
                            <p class="text-info">Processing for {{ $documentRequest->processed_at ? $documentRequest->processed_at->diffForHumans() : 'N/A' }}</p>
                        @else
                            <p>Submitted {{ $documentRequest->submitted_at->diffForHumans() }}</p>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Copies Requested:</strong>
                        <p>{{ $documentRequest->copies_requested }} {{ Str::plural('copy', $documentRequest->copies_requested) }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Payment Method:</strong>
                        <p>{{ ucfirst($documentRequest->payment_method) }}</p>
                    </div>
                </div>

                @if($documentRequest->uploaded_files && count($documentRequest->uploaded_files) > 0)
                <div class="mb-3">
                    <strong>Uploaded Documents:</strong>
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
                    @if($documentRequest->processor && $documentRequest->processed_at)
                        <small class="text-muted">
                            Added by {{ $documentRequest->processor->name }} on {{ $documentRequest->processed_at->format('M j, Y g:i A') }}
                        </small>
                    @endif
                </div>
                @endif

                @if($documentRequest->status == 'rejected' && $documentRequest->rejection_reason)
                <hr>
                <div class="mb-3">
                    <strong>Rejection Reason:</strong>
                    <div class="border p-3 rounded bg-danger-subtle">
                        {{ $documentRequest->rejection_reason }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Resident Information -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Resident Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> {{ $documentRequest->user->first_name }} {{ $documentRequest->user->last_name }}</p>
                        <p><strong>Email:</strong> {{ $documentRequest->user->email }}</p>
                        <p><strong>Phone:</strong> {{ $documentRequest->user->phone ?? 'Not provided' }}</p>
                    </div>
                    <div class="col-md-6">
                        @if($documentRequest->user->residentProfile)
                            <p><strong>Address:</strong> {{ $documentRequest->user->residentProfile->full_address ?? 'Not provided' }}</p>
                            <p><strong>Age:</strong> {{ $documentRequest->user->residentProfile->age ?? 'Not provided' }}</p>
                            <p><strong>Civil Status:</strong> {{ $documentRequest->user->residentProfile->civil_status ?? 'Not provided' }}</p>
                        @else
                            <p><strong>Address:</strong> {{ $documentRequest->user->address ?? 'Not provided' }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Actions -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                @if($documentRequest->status == 'pending')
                    <div class="d-grid gap-2">
                        <form action="{{ route('barangay.documents.process', $documentRequest) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Start processing this request?')">
                                <i class="fas fa-play"></i> Start Processing
                            </button>
                        </form>
                        <form action="{{ route('barangay.documents.approve', $documentRequest) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Approve this request and generate PDF?')">
                                <i class="fas fa-check"></i> Approve Request
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger w-100" onclick="showRejectModal()">
                            <i class="fas fa-times"></i> Reject Request
                        </button>
                    </div>
                @elseif($documentRequest->status == 'processing')
                    <div class="d-grid gap-2">
                        <form action="{{ route('barangay.documents.approve', $documentRequest) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Approve this request and generate PDF?')">
                                <i class="fas fa-check"></i> Approve Request
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger w-100" onclick="showRejectModal()">
                            <i class="fas fa-times"></i> Reject Request
                        </button>
                    </div>
                @elseif($documentRequest->status == 'approved')
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h6 class="mb-0"><i class="fas fa-print"></i> Print Official Document</h6>
    </div>
    <div class="card-body text-center">
        <div class="mb-3">
            <i class="fas fa-print fa-2x text-success"></i>
        </div>
        <p class="text-muted">Print official document for the resident.</p>
        
        <div class="d-grid gap-2">
            <a href="{{ route('barangay.documents.print', $documentRequest) }}" 
               target="_blank" class="btn btn-success">
                <i class="fas fa-print"></i> Print Document
            </a>
            <a href="{{ route('barangay.documents.print', $documentRequest) }}?autoprint=1" 
               target="_blank" class="btn btn-outline-success">
                <i class="fas fa-bolt"></i> Auto-Print
            </a>
        </div>
        
        <div class="mt-3">
            <small class="text-muted">
                <i class="fas fa-lightbulb"></i> 
                Use "Auto-Print" for instant printing when resident is present.
            </small>
        </div>
    </div>
</div>
@endif
            </div>
        </div>

        <!-- Document Type Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Document Type Details</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Processing Time:</small>
                    <div>{{ $documentRequest->documentType->processing_days }} days</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Standard Fee:</small>
                    <div>₱{{ number_format($documentRequest->documentType->fee, 2) }}</div>
                </div>
                @if($documentRequest->documentType->requirements)
                <div>
                    <small class="text-muted">Requirements:</small>
                    <div class="mt-1">
                        {{-- $documentRequest->documentType->requirements --}}
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if($documentRequest->status == 'approved' && $documentRequest->qr_code)
        <!-- QR Code Preview -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">QR Code</h6>
            </div>
            <div class="card-body text-center">
                <img src="{{ asset('uploads/qr-codes/' . $documentRequest->qr_code) }}" 
                     alt="QR Code" class="img-fluid" style="max-width: 200px;">
                <div class="mt-2">
                    <small class="text-muted">Scan to verify document authenticity</small>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('barangay.documents.reject', $documentRequest) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reject_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject_reason" name="reason" rows="4" 
                                  placeholder="Please provide a clear reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.badge {
    color: white !important;
    font-weight: 600;
}

.badge-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
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

@push('scripts')
<script>
function showRejectModal() {
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endpush
@endsection