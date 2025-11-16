@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h3 class="mb-0">Request Barangay Document</h3>
            <p class="text-muted mb-0">{{ $barangayName }}</p>
        </div>
        <div class="card-body">
            <!-- Auto-detected Address -->
            <div class="mb-4 p-3 border rounded bg-light">
                <h6 class="mb-2 text-muted">Your Registered Address</h6>
                <p class="mb-0">{{ $fullAddress }}</p>
                <a href="{{ route('profile.edit') }}" class="small">Update address</a>
            </div>

            @if(!$barangayId)
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    We couldn't match your barangay with our official records. Please 
                    <a href="{{ route('profile.edit') }}" class="alert-link">update your profile</a>.
                </div>
            @else
            <form action="{{ route('requests.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="barangay_id" value="{{ $barangayId }}">
                
                <!-- Document Type -->
                <div class="mb-3">
                    <label class="form-label">Document Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select" required>
                        <option value="">Select Document</option>
                        <option value="clearance">Barangay Clearance</option>
                        <option value="indigency">Certificate of Indigency</option>
                        <option value="business_permit">Business Permit</option>
                        <option value="residency">Certificate of Residency</option>
                        <option value="good_moral">Good Moral Certificate</option>
                        <option value="cedula">Community Tax Certificate (Cedula)</option>
                    </select>
                </div>

                <!-- Purpose -->
                <div class="mb-3">
                    <label class="form-label">Purpose <span class="text-danger">*</span></label>
                    <input type="text" name="purpose" class="form-control" required
                           placeholder="What is this document for? (e.g., Job Application)">
                </div>

                <!-- Additional Notes -->
                <div class="mb-3">
                    <label class="form-label">Additional Notes</label>
                    <textarea name="additional_notes" class="form-control" rows="2"
                              placeholder="Special requests or additional information (optional)"></textarea>
                </div>

                <!-- Supporting Documents -->
                <div class="mb-4">
                    <label class="form-label">Supporting Documents</label>
                    <input type="file" name="attachments[]" class="form-control" multiple
                           accept=".pdf,.jpg,.jpeg,.png">
                    <small class="text-muted">Upload any required files (Max 5MB each, max 3 files)</small>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-2"></i> Submit Request
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection