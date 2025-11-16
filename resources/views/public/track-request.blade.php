{{-- resources/views/public/track-request.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Document Request - {{ $found ? $documentRequest->tracking_number : 'Not Found' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .status-badge { font-size: 0.9rem; }
        .mobile-view { max-width: 100%; padding: 15px; }
        .qr-scan-info { background: #f8f9fa; border-radius: 10px; padding: 15px; margin: 15px 0; }
        .timeline-mobile { font-size: 0.9rem; }
    </style>
</head>
<body class="{{ $isMobile ? 'mobile-view' : '' }}">
    <div class="container py-3">
        @if($isQrAccess)
        <div class="text-center mb-3">
            <i class="fas fa-qrcode fa-2x text-primary"></i>
            <h5 class="mt-2">Document Request Status</h5>
            <p class="text-muted">Scanned via QR Code</p>
        </div>
        @endif

        @if(!$found)
            <div class="alert alert-danger text-center">
                <i class="fas fa-times-circle fa-2x mb-2"></i>
                <h5>Document Not Found</h5>
                <p>No document request found with tracking number: <strong>{{ $tracking_number }}</strong></p>
                <a href="{{ url('/') }}" class="btn btn-primary">Return to Homepage</a>
            </div>
        @else
            <!-- Quick Status Overview -->
            <div class="card mb-3">
                <div class="card-body text-center">
                    <span class="badge {{ $documentRequest->status_badge['class'] }} status-badge">
                        <i class="fas fa-{{ $documentRequest->status_icon }}"></i>
                        {{ $documentRequest->status_badge['text'] }}
                    </span>
                    <h6 class="mt-2">{{ $documentRequest->tracking_number }}</h6>
                    <p class="text-muted mb-0">{{ $documentRequest->documentType->name }}</p>
                </div>
            </div>

            <!-- Request Details -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Request Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Resident</small>
                            <p class="mb-2"><strong>{{ $documentRequest->user->first_name }} {{ $documentRequest->user->last_name }}</strong></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Barangay</small>
                            <p class="mb-2"><strong>{{ $documentRequest->barangay->name }}</strong></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Submitted</small>
                            <p class="mb-2"><strong>{{ $documentRequest->submitted_at->format('M j, Y') }}</strong></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Purpose</small>
                            <p class="mb-0"><strong>{{ Str::limit($documentRequest->purpose, 20) }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Timeline (Mobile Optimized) -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Status Timeline</h6>
                </div>
                <div class="card-body timeline-mobile">
                    @foreach(['submitted_at' => 'Submitted', 'processed_at' => 'Processing', 'approved_at' => 'Approved'] as $field => $label)
                        @if($documentRequest->$field)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-success"><i class="fas fa-check-circle"></i> {{ $label }}</span>
                                <small class="text-muted">{{ $documentRequest->$field->format('M j, Y g:i A') }}</small>
                            </div>
                        @else
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted"><i class="far fa-clock"></i> {{ $label }}</span>
                                <small class="text-muted">Pending</small>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Next Steps -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Next Steps</h6>
                </div>
                <div class="card-body">
                    @if($documentRequest->status == 'pending')
                        <p class="mb-1">✅ Your request is in the queue</p>
                        <p class="mb-1">⏳ Barangay staff will process it soon</p>
                        <p class="mb-0">📱 Check back later for updates</p>
                    @elseif($documentRequest->status == 'processing')
                        <p class="mb-1">✅ Your request is being processed</p>
                        <p class="mb-1">⏳ Expected completion: {{ $documentRequest->documentType->processing_days }} days</p>
                        <p class="mb-0">🏢 Visit barangay office when completed</p>
                    @elseif($documentRequest->status == 'approved')
                        <p class="mb-1">✅ Your document is ready!</p>
                        <p class="mb-1">🖨️ You can now print your document</p>
                        <p class="mb-0">📞 Contact barangay for pickup details</p>
                    @elseif($documentRequest->status == 'rejected')
                        <p class="mb-1">❌ Request was not approved</p>
                        <p class="mb-0">💬 Reason: {{ $documentRequest->rejection_reason }}</p>
                    @endif
                    
                    <hr>
                    <p class="text-center mb-0">
                        <small class="text-muted">
                            Barangay {{ $documentRequest->barangay->name }} Office
                        </small>
                    </p>
                </div>
            </div>

            <!-- Quick Actions for Mobile -->
            @if($isMobile)
            <div class="fixed-bottom bg-white p-3 border-top">
                <div class="d-grid gap-2">
                    <a href="tel:{{ $documentRequest->barangay->contact_number ?? '' }}" class="btn btn-primary">
                        <i class="fas fa-phone"></i> Call Barangay
                    </a>
                    <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-home"></i> Back to Home
                    </a>
                </div>
            </div>
            @endif
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>