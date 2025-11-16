{{-- After successful submission --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-qrcode"></i> Your Document Request QR Code</h5>
    </div>
    <div class="card-body text-center">
        <div class="mb-3">
            <img src="{{ $documentRequest->qr_code_url }}" 
                 alt="QR Code" class="img-fluid" style="max-width: 250px;">
        </div>
        
        <h6>Tracking Number: <strong>{{ $documentRequest->tracking_number }}</strong></h6>
        <p class="text-muted">
            <i class="fas fa-info-circle"></i> 
            Use this QR code to check your request status or present it at the barangay office.
        </p>
        
        <div class="d-flex justify-content-center gap-2 flex-wrap">
            <a href="{{ $documentRequest->qr_code_url }}" 
               download="document_request_{{ $documentRequest->tracking_number }}.png" 
               class="btn btn-primary">
                <i class="fas fa-download"></i> Download QR Code
            </a>
            
            <a href="{{ route('track.request', $documentRequest->tracking_number) }}" 
               target="_blank" class="btn btn-outline-primary">
                <i class="fas fa-external-link-alt"></i> View Status Online
            </a>
            
            <button onclick="printQRCode()" class="btn btn-outline-secondary">
                <i class="fas fa-print"></i> Print QR Code
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function printQRCode() {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head><title>Document Request QR Code</title></head>
            <body style="text-align: center; padding: 20px;">
                <h2>Document Request QR Code</h2>
                <img src="{{ $documentRequest->qr_code_url }}" style="max-width: 300px;">
                <p><strong>Tracking Number:</strong> {{ $documentRequest->tracking_number }}</p>
                <p><strong>Document Type:</strong> {{ $documentRequest->documentType->name }}</p>
                <p><strong>Submitted:</strong> {{ $documentRequest->submitted_at->format('M j, Y') }}</p>
                <p>Scan this QR code to check your request status</p>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}
</script>
@endpush