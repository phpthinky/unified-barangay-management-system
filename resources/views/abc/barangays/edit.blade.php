@extends('layouts.abc')

@section('title', 'Edit ' . $barangay->name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('abc.barangays.index') }}">Barangays</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('abc.barangays.show', $barangay) }}">{{ $barangay->name }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">Edit {{ $barangay->name }}</h1>
            <p class="mb-0 text-muted">Update barangay information and settings</p>
        </div>
        <div>
            <a href="{{ route('abc.barangays.show', $barangay) }}" class="btn btn-secondary">
                <i class="fas fa-eye"></i> View Details
            </a>
            <a href="{{ route('abc.barangays.index') }}" class="btn btn-outline-secondary ml-2">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Barangay Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('abc.barangays.update', $barangay) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label required">Barangay Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $barangay->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="slug" class="form-label">URL Slug</label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                           id="slug" name="slug" value="{{ old('slug', $barangay->slug) }}">
                                    <small class="form-text text-muted">Used in public URL: /b/{{ $barangay->slug }}</small>
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_number" class="form-label">Contact Number</label>
                                    <input type="text" class="form-control @error('contact_number') is-invalid @enderror" 
                                           id="contact_number" name="contact_number" value="{{ old('contact_number', $barangay->contact_number) }}">
                                    @error('contact_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $barangay->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="form-group">
                            <label for="address" class="form-label">Complete Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3">{{ old('address', $barangay->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description', $barangay->description) }}</textarea>
                            <small class="form-text text-muted">Brief description about the barangay</small>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Logo Display -->
                        @if($barangay->logo)
                            <div class="form-group">
                                <label class="form-label">Current Logo</label>
                                <div class="mb-3">
                                    <img src="{{ asset('uploads/logos/' . $barangay->logo) }}" 
                                         alt="Current Logo" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            </div>
                        @endif

                        <!-- Logo Upload -->
                        <div class="form-group">
                            <label for="logo" class="form-label">{{ $barangay->logo ? 'Update' : 'Upload' }} Logo</label>
                            <input type="file" class="form-control-file @error('logo') is-invalid @enderror" 
                                   id="logo" name="logo" accept="image/jpeg,image/png,image/jpg">
                            <small class="form-text text-muted">
                                {{ $barangay->logo ? 'Leave empty to keep current logo. ' : '' }}Upload PNG, JPG, or JPEG. Max size: 2MB
                            </small>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Geographic Coordinates -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" 
                                           id="latitude" name="latitude" value="{{ old('latitude', $barangay->latitude) }}">
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                           id="longitude" name="longitude" value="{{ old('longitude', $barangay->longitude) }}">
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Social Media Links -->
                        <div class="form-group">
                            <label class="form-label">Social Media Links</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fab fa-facebook text-primary"></i></span>
                                        </div>
                                        <input type="url" class="form-control" name="social_media[facebook]" 
                                               value="{{ old('social_media.facebook', $barangay->social_media['facebook'] ?? '') }}" 
                                               placeholder="Facebook Page URL">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fab fa-instagram text-danger"></i></span>
                                        </div>
                                        <input type="url" class="form-control" name="social_media[instagram]" 
                                               value="{{ old('social_media.instagram', $barangay->social_media['instagram'] ?? '') }}" 
                                               placeholder="Instagram Profile URL">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" 
                                       {{ old('is_active', $barangay->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    <strong>Active Status</strong>
                                    <small class="text-muted d-block">When inactive, the barangay will not be accessible to the public</small>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Barangay
                            </button>
                            <a href="{{ route('abc.barangays.show', $barangay) }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Side Panel -->
        <div class="col-xl-4">
            <!-- Current URLs Card -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-info">Current URLs</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Public URL:</strong>
                        <div class="bg-light p-2 rounded">
                            <code>{{ url('/b/' . $barangay->slug) }}</code>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Registration URL:</strong>
                        <div class="bg-light p-2 rounded">
                            <code>{{ url('/b/' . $barangay->slug . '/register') }}</code>
                        </div>
                    </div>

                    @if($barangay->qr_code)
                        <div class="text-center">
                            <strong>Current QR Code:</strong>
                            <div class="mt-2">
                                <img src="{{ asset('uploads/qr-codes/' . $barangay->qr_code) }}" 
                                     alt="QR Code" class="img-fluid" style="max-width: 150px;">
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-success">Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Users:</span>
                        <strong>{{ $barangay->users->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Residents:</span>
                        <strong>{{ $barangay->residentProfiles->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Created:</span>
                        <strong>{{ $barangay->created_at->format('M d, Y') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Last Updated:</span>
                        <strong>{{ $barangay->updated_at->format('M d, Y') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card border-danger shadow">
                <div class="card-header bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">Danger Zone</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Permanent actions that cannot be undone.</p>
                    
                    <button class="btn btn-outline-warning btn-sm btn-block mb-2" onclick="regenerateQr()">
                        <i class="fas fa-sync"></i> Regenerate QR Code
                    </button>
                    
                    @if($barangay->users->count() == 0)
                        <button class="btn btn-outline-danger btn-sm btn-block" onclick="confirmDelete()">
                            <i class="fas fa-trash"></i> Delete Barangay
                        </button>
                    @else
                        <button class="btn btn-outline-danger btn-sm btn-block" disabled title="Cannot delete barangay with existing users">
                            <i class="fas fa-trash"></i> Delete Barangay
                        </button>
                        <small class="text-muted">Cannot delete: {{ $barangay->users->count() }} user(s) exist</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong>{{ $barangay->name }}</strong>? This action cannot be undone.</p>
                <div class="alert alert-danger">
                    <strong>Warning:</strong> This will permanently remove all barangay data including documents, complaints, and permits.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('abc.barangays.destroy', $barangay) }}" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Barangay</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    $('#deleteModal').modal('show');
}

function regenerateQr() {
    if (confirm('Are you sure you want to regenerate the QR code? The old QR code will no longer work.')) {
        fetch(`/admin/barangays/{{ $barangay->id }}/generate-qr`, {
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
                alert('Error regenerating QR code');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error regenerating QR code');
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate slug if name changes and slug is empty
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    nameInput.addEventListener('input', function() {
        if (!slugInput.value.trim()) {
            const slug = this.value.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');
            slugInput.value = slug;
        }
    });
});
</script>
@endpush