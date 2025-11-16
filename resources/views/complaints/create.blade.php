@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h3 class="mb-0">File a Complaint</h3>
            <p class="text-muted mb-0">Sablayan, Occidental Mindoro</p>
        </div>
        <div class="card-body">
            <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Barangay</label>
                            <select name="barangay_id" class="form-select" required>
                                <option value="">Select Barangay</option>
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay->id }}" 
                                        {{ auth()->user()->barangay_id == $barangay->id ? 'selected' : '' }}>
                                        {{ $barangay->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Complaint Type</label>
                            <select name="type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="noise_disturbance">Noise Disturbance</option>
                                <option value="property_dispute">Property Dispute</option>
                                <option value="sanitation">Sanitation Issue</option>
                                <option value="public_safety">Public Safety Concern</option>
                                <option value="violence">Violence/Abuse</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Complaint Title</label>
                    <input type="text" name="title" class="form-control" required
                           placeholder="Brief title of your complaint">
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4" required
                              placeholder="Detailed description of the complaint"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" required
                                   placeholder="Where did this happen?">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Landmark (Optional)</label>
                            <input type="text" name="landmark" class="form-control"
                                   placeholder="Nearby landmark or reference point">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Photo Evidence (Optional)</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                    <small class="text-muted">Max 2MB (JPG, PNG)</small>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-2"></i> Submit Complaint
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection