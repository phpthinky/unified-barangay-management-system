{{-- FILE: resources/views/admin/requests/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Request Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Request #{{ $request->reference_number }}</h4>
                <a href="{{ route('admin.requests.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
            <div class="card-body">
                <!-- Request Details -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Request Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Reference #:</th>
                                <td>{{ $request->reference_number }}</td>
                            </tr>
                            <tr>
                                <th>Document Type:</th>
                                <td>{{ $request->documentType->name }}</td>
                            </tr>
                            <tr>
                                <th>Fee:</th>
                                <td>{{ $request->documentType->formatted_fee }}</td>
                            </tr>
                            <tr>
                                <th>Purpose:</th>
                                <td>{{ $request->purpose }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span class="badge {{ $request->status_badge }}">
                                        {{ $request->status_text }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Barangay:</th>
                                <td>{{ $request->barangay->name }}</td>
                            </tr>
                            <tr>
                                <th>Submitted:</th>
                                <td>{{ $request->created_at->format('F j, Y \a\t g:i A') }}</td>
                            </tr>
                            @if($request->processed_at)
                            <tr>
                                <th>Processed:</th>
                                <td>{{ $request->processed_at->format('F j, Y \a\t g:i A') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Resident Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="120">Name:</th>
                                <td>{{ $request->user->residentProfile->full_name }}</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ $request->user->email }}</td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td>{{ $request->user->phone }}</td>
                            </tr>
                            <tr>
                                <th>Address:</th>
                                <td>{{ $request->user->residentProfile->full_address }}</td>
                            </tr>
                            <tr>
                                <th>Age:</th>
                                <td>{{ $request->user->residentProfile->age }} years old</td>
                            </tr>
                            <tr>
                                <th>Gender:</th>
                                <td>{{ $request->user->residentProfile->gender }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($request->additional_details)
                <div class="mb-4">
                    <h5>Additional Details</h5>
                    <div class="border p-3 bg-light">
                        {{ $request->additional_details }}
                    </div>
                </div>
                @endif

                @if($request->file_upload_path)
                <div class="mb-4">
                    <h5>Uploaded File</h5>
                    <a href="{{ asset($request->file_upload_path) }}" target="_blank" class="btn btn-outline-primary">
                        <i class="fas fa-eye"></i> View Uploaded File
                    </a>
                </div>
                @endif

                @if($request->rejection_reason)
                <div class="mb-4">
                    <h5>Rejection Reason</h5>
                    <div class="alert alert-danger">
                        {{ $request->rejection_reason }}
                    </div>
                </div>
                @endif

                @if($request->notes)
                <div class="mb-4">
                    <h5>Processing Notes</h5>
                    <div class="border p-3 bg-light">
                        {{ $request->notes }}
                    </div>
                </div>
                @endif

                @if($request->generated_pdf_path)
                <div class="mb-4">
                    <h5>Generated Document</h5>
                    <a href="{{ asset($request->generated_pdf_path) }}" target="_blank" class="btn btn-success">
                        <i class="fas fa-file-pdf"></i> View Generated PDF
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Update Status Form -->
        <div class="card">
            <div class="card-header">
                <h5>Update Request Status</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.requests.update-status', $request) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                            <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $request->status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="approved" {{ $request->status == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $request->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="rejection-reason-div" style="display: none;">
                        <label for="rejection_reason" class="form-label">Rejection Reason</label>
                        <textarea class="form-control @error('rejection_reason') is-invalid @enderror" 
                                  id="rejection_reason" name="rejection_reason" rows="3">{{ old('rejection_reason', $request->rejection_reason) }}</textarea>
                        @error('rejection_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Admin Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3" 
                                  placeholder="Add any processing notes...">{{ old('notes', $request->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                </form>
            </div>
        </div>

        <!-- Document Type Info -->
        <div class="card mt-4">
            <div class="card-header">
                <h5>Document Type Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th>Name:</th>
                        <td>{{ $request->documentType->name }}</td>
                    </tr>
                    <tr>
                        <th>Fee:</th>
                        <td>{{ $request->documentType->formatted_fee }}</td>
                    </tr>
                    <tr>
                        <th>Processing Time:</th>
                        <td>{{ $request->documentType->processing_time_text }}</td>
                    </tr>
                </table>
                
                @if($request->documentType->requirements && count($request->documentType->requirements) > 0)
                <div class="mt-3">
                    <strong>Requirements:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($request->documentType->requirements as $requirement)
                            <li class="small">{{ $requirement }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>

        <!-- Processing History -->
        @if($request->processedBy)
        <div class="card mt-4">
            <div class="card-header">
                <h5>Processing History</h5>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    Last processed by <strong>{{ $request->processedBy->name }}</strong>
                    <br>{{ $request->processed_at->format('F j, Y \a\t g:i A') }}
                </small>
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h5>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($request->status === 'pending')
                        <button type="button" class="btn btn-info btn-sm" onclick="updateStatus('processing')">
                            <i class="fas fa-clock"></i> Mark as Processing
                        </button>
                    @endif
                    
                    @if(in_array($request->status, ['pending', 'processing']))
                        <button type="button" class="btn btn-success btn-sm" onclick="updateStatus('approved')">
                            <i class="fas fa-check"></i> Approve Request
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="updateStatus('rejected')">
                            <i class="fas fa-times"></i> Reject Request
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('status').addEventListener('change', function() {
    const rejectionDiv = document.getElementById('rejection-reason-div');
    const rejectionTextarea = document.getElementById('rejection_reason');
    
    if (this.value === 'rejected') {
        rejectionDiv.style.display = 'block';
        rejectionTextarea.required = true;
    } else {
        rejectionDiv.style.display = 'none';
        rejectionTextarea.required = false;
    }
});

function updateStatus(status) {
    document.getElementById('status').value = status;
    if (status === 'rejected') {
        document.getElementById('rejection-reason-div').style.display = 'block';
        document.getElementById('rejection_reason').required = true;
        document.getElementById('rejection_reason').focus();
    } else {
        document.getElementById('rejection-reason-div').style.display = 'none';
        document.getElementById('rejection_reason').required = false;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    if (statusSelect.value === 'rejected') {
        document.getElementById('rejection-reason-div').style.display = 'block';
        document.getElementById('rejection_reason').required = true;
    }
});
</script>
@endpush
@endsection