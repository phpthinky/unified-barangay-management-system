@extends('layouts.abc')

@section('title', 'Create New Barangay')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('abc.barangays.index') }}">Barangays</a></li>
                    <li class="breadcrumb-item active">Create New</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">Create New Barangay</h1>
            <p class="mb-0 text-muted">Add a new barangay to the municipality</p>
        </div>
        <a href="{{ route('abc.barangays.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Barangay Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('abc.barangays.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label required">Barangay Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="slug" class="form-label">URL Slug</label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                           id="slug" name="slug" value="{{ old('slug') }}">
                                    <small class="form-text text-muted">Leave empty to auto-generate from name. Used in public URL: /b/slug</small>
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
                                           id="contact_number" name="contact_number" value="{{ old('contact_number') }}">
                                    @error('contact_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}">
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
                                      id="address" name="address" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            <small class="form-text text-muted">Brief description about the barangay</small>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Logo Upload -->
                        <div class="form-group">
                            <label for="logo" class="form-label">Barangay Logo</label>
                            <input type="file" class="form-control-file @error('logo') is-invalid @enderror" 
                                   id="logo" name="logo" accept="image/jpeg,image/png,image/jpg">
                            <small class="form-text text-muted">Upload PNG, JPG, or JPEG. Max size: 2MB</small>
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
                                           id="latitude" name="latitude" value="{{ old('latitude') }}">
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                           id="longitude" name="longitude" value="{{ old('longitude') }}">
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
                                               value="{{ old('social_media.facebook') }}" placeholder="Facebook Page URL">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fab fa-instagram text-danger"></i></span>
                                        </div>
                                        <input type="url" class="form-control" name="social_media[instagram]" 
                                               value="{{ old('social_media.instagram') }}" placeholder="Instagram Profile URL">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Barangay
                            </button>
                            <a href="{{ route('abc.barangays.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="col-xl-4">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-info">Quick Help</h6>
                </div>
                <div class="card-body">
                    <h6>What happens after creating a barangay?</h6>
                    <ul class="mb-3">
                        <li>A unique public URL will be generated</li>
                        <li>QR code will be automatically created</li>
                        <li>Residents can register via the public link</li>
                        <li>Officials can be assigned to manage the barangay</li>
                    </ul>

                    <h6>URL Slug Guidelines:</h6>
                    <ul class="mb-3">
                        <li>Use lowercase letters and hyphens</li>
                        <li>No spaces or special characters</li>
                        <li>Keep it short and memorable</li>
                        <li>Example: "san-jose" for "San Jose"</li>
                    </ul>

                    <h6>Logo Requirements:</h6>
                    <ul class="mb-0">
                        <li>Square format recommended</li>
                        <li>Minimum 300x300 pixels</li>
                        <li>Clear and professional appearance</li>
                        <li>PNG format for transparency</li>
                    </ul>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="card shadow mt-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-success">URL Preview</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Public Registration URL:</strong></p>
                    <div class="bg-light p-2 rounded">
                        <code id="url-preview">{{ url('/b/') }}/<span id="slug-preview">your-barangay-slug</span></code>
                    </div>
                    <small class="text-muted mt-2">Residents will use this URL to register</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const slugPreview = document.getElementById('slug-preview');

    function updateSlugPreview() {
        const slugValue = slugInput.value || nameInput.value.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
            .replace(/\s+/g, '-') // Replace spaces with hyphens
            .replace(/-+/g, '-') // Replace multiple hyphens with single
            .replace(/^-|-$/g, ''); // Remove leading/trailing hyphens
        
        slugPreview.textContent = slugValue || 'your-barangay-slug';
    }

    nameInput.addEventListener('input', function() {
        if (!slugInput.value) {
            updateSlugPreview();
        }
    });

    slugInput.addEventListener('input', updateSlugPreview);
});
</script>
@endpush