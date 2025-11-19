@extends('layouts.admin')

@section('title', 'Add Barangay Official')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2><i class="fas fa-user-plus"></i> Add Barangay Official</h2>
        <p class="text-muted">Add a new official to the organizational chart</p>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.barangay-officials.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Barangay Selection -->
                        <div class="mb-3">
                            <label for="barangay_id" class="form-label">Barangay <span class="text-danger">*</span></label>
                            <select name="barangay_id" id="barangay_id" class="form-select @error('barangay_id') is-invalid @enderror" required>
                                <option value="">Select Barangay</option>
                                @foreach($barangays as $barangay)
                                <option value="{{ $barangay->id }}" {{ old('barangay_id') == $barangay->id ? 'selected' : '' }}>
                                    {{ $barangay->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('barangay_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g., Juan Dela Cruz">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Position -->
                        <div class="mb-3">
                            <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                            <input type="text" name="position" id="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position') }}" required placeholder="e.g., Punong Barangay, Kagawad 1, Secretary">
                            <small class="text-muted">Examples: Punong Barangay, Kagawad 1-7, Secretary, Treasurer, SK Chairperson</small>
                            @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Committee (Optional) -->
                        <div class="mb-3">
                            <label for="committee" class="form-label">Committee / Area of Responsibility</label>
                            <input type="text" name="committee" id="committee" class="form-control @error('committee') is-invalid @enderror" value="{{ old('committee') }}" placeholder="e.g., Health & Sanitation, Peace & Order">
                            <small class="text-muted">Applicable for Kagawad/Councilors</small>
                            @error('committee')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Display Order -->
                        <div class="mb-3">
                            <label for="display_order" class="form-label">Display Order <span class="text-danger">*</span></label>
                            <input type="number" name="display_order" id="display_order" class="form-control @error('display_order') is-invalid @enderror" value="{{ old('display_order', 0) }}" required min="0" max="999">
                            <small class="text-muted">Lower numbers appear first in org chart (0-999)</small>
                            @error('display_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Term Dates -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="term_start" class="form-label">Term Start <span class="text-danger">*</span></label>
                                <input type="date" name="term_start" id="term_start" class="form-control @error('term_start') is-invalid @enderror" value="{{ old('term_start') }}" required>
                                @error('term_start')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="term_end" class="form-label">Term End <span class="text-danger">*</span></label>
                                <input type="date" name="term_end" id="term_end" class="form-control @error('term_end') is-invalid @enderror" value="{{ old('term_end') }}" required>
                                @error('term_end')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contact_number" class="form-label">Contact Number</label>
                                <input type="text" name="contact_number" id="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number') }}" placeholder="09XX XXX XXXX">
                                @error('contact_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="official@example.com">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Photo Upload -->
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Photo</label>
                            <input type="file" name="avatar" id="avatar" class="form-control @error('avatar') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
                            <small class="text-muted">Maximum 2MB, JPG/PNG only</small>
                            @error('avatar')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description / Bio</label>
                            <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Brief description or responsibilities...">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">
                                Active (Currently serving)
                            </label>
                        </div>

                        <hr>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Official
                            </button>
                            <a href="{{ route('admin.barangay-officials.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Sidebar -->
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h6><i class="fas fa-info-circle"></i> Guidelines</h6>
                    <hr>
                    <p class="small"><strong>Position Examples:</strong></p>
                    <ul class="small">
                        <li>Punong Barangay / Barangay Captain</li>
                        <li>Kagawad 1, Kagawad 2, etc.</li>
                        <li>Barangay Secretary</li>
                        <li>Barangay Treasurer</li>
                        <li>SK Chairperson</li>
                    </ul>

                    <p class="small mt-3"><strong>Display Order:</strong></p>
                    <ul class="small">
                        <li>0 - Captain (appears first)</li>
                        <li>10-70 - Kagawad 1-7</li>
                        <li>80 - Secretary</li>
                        <li>90 - Treasurer</li>
                        <li>100 - SK Chairperson</li>
                    </ul>

                    <p class="small mt-3"><strong>Committee Examples:</strong></p>
                    <ul class="small">
                        <li>Health & Sanitation</li>
                        <li>Peace & Order</li>
                        <li>Education & Culture</li>
                        <li>Infrastructure</li>
                        <li>Finance & Budget</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
