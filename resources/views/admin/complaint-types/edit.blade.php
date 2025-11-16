@extends('layouts.admin')

@section('title', 'Edit Complaint Type - ' . $complaintType->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Edit Complaint Type</h2>
    <div>
        <a href="{{ route('admin.complaint-types.show', $complaintType) }}" class="btn btn-info">
            <i class="fas fa-eye"></i> View Details
        </a>
        <a href="{{ route('admin.complaint-types.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.complaint-types.update', $complaintType) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3">Basic Information</h5>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $complaintType->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                               id="slug" name="slug" value="{{ old('slug', $complaintType->slug) }}">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">URL-friendly version of the name</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $complaintType->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-control @error('category') is-invalid @enderror" 
                                id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="civil" {{ old('category', $complaintType->category) == 'civil' ? 'selected' : '' }}>Civil</option>
                            <option value="criminal" {{ old('category', $complaintType->category) == 'criminal' ? 'selected' : '' }}>Criminal</option>
                            <option value="administrative" {{ old('category', $complaintType->category) == 'administrative' ? 'selected' : '' }}>Administrative</option>
                            <option value="barangay" {{ old('category', $complaintType->category) == 'barangay' ? 'selected' : '' }}>Barangay</option>
                            <option value="others" {{ old('category', $complaintType->category) == 'others' ? 'selected' : '' }}>Others</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h5 class="mb-3">Processing Configuration</h5>
                    
                    <div class="mb-3">
                        <label for="default_handler_type" class="form-label">Default Handler <span class="text-danger">*</span></label>
                        <select class="form-control @error('default_handler_type') is-invalid @enderror" 
                                id="default_handler_type" name="default_handler_type" required>
                            <option value="">Select Default Handler</option>
                            <option value="captain" {{ old('default_handler_type', $complaintType->default_handler_type) == 'captain' ? 'selected' : '' }}>Barangay Captain</option>
                            <option value="secretary" {{ old('default_handler_type', $complaintType->default_handler_type) == 'secretary' ? 'selected' : '' }}>Barangay Secretary</option>
                            <option value="lupon" {{ old('default_handler_type', $complaintType->default_handler_type) == 'lupon' ? 'selected' : '' }}>Lupon Member</option>
                            <option value="any_staff" {{ old('default_handler_type', $complaintType->default_handler_type) == 'any_staff' ? 'selected' : '' }}>Any Barangay Staff</option>
                        </select>
                        @error('default_handler_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="estimated_resolution_days" class="form-label">
                            Estimated Resolution Days <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control @error('estimated_resolution_days') is-invalid @enderror" 
                               id="estimated_resolution_days" name="estimated_resolution_days" 
                               value="{{ old('estimated_resolution_days', $complaintType->estimated_resolution_days) }}" 
                               min="1" max="365" required>
                        @error('estimated_resolution_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="requires_hearing" 
                                   name="requires_hearing" value="1" 
                                   {{ old('requires_hearing', $complaintType->requires_hearing) ? 'checked' : '' }}>
                            <label class="form-check-label" for="requires_hearing">
                                Requires Hearing/Mediation
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            Check if this complaint type typically requires formal hearings or mediation sessions
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                               id="sort_order" name="sort_order" 
                               value="{{ old('sort_order', $complaintType->sort_order) }}" min="0">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Lower numbers appear first in lists</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" 
                                   name="is_active" value="1" 
                                   {{ old('is_active', $complaintType->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            Active complaint types can be selected when filing new complaints
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Required Information Fields -->
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="mb-3">Required Information</h5>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Specify what information is required when filing this type of complaint
                    </div>
                    
                    <div id="required-information-container">
                        @php
                            $requiredInfo = old('required_information', $complaintType->required_information ?? []);
                        @endphp
                        
                        @if(count($requiredInfo) > 0)
                            @foreach($requiredInfo as $info)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" 
                                           name="required_information[]" 
                                           value="{{ $info }}"
                                           placeholder="e.g., Incident date, Location, Witness names">
                                    <button type="button" class="btn btn-outline-danger remove-field">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" 
                                       name="required_information[]" 
                                       placeholder="e.g., Incident date, Location, Witness names">
                                <button type="button" class="btn btn-outline-danger remove-field">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                    
                    <button type="button" id="add-required-field" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-plus"></i> Add Required Field
                    </button>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <a href="{{ route('admin.complaint-types.show', $complaintType) }}" class="btn btn-secondary me-md-2">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Complaint Type
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate slug from name
    const nameField = document.getElementById('name');
    const slugField = document.getElementById('slug');
    
    nameField.addEventListener('blur', function() {
        if (!slugField.value) {
            slugField.value = this.value.toLowerCase()
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }
    });
    
    // Dynamic required information fields
    const container = document.getElementById('required-information-container');
    const addButton = document.getElementById('add-required-field');
    
    addButton.addEventListener('click', function() {
        const inputGroup = document.createElement('div');
        inputGroup.className = 'input-group mb-2';
        inputGroup.innerHTML = `
            <input type="text" class="form-control" 
                   name="required_information[]" 
                   placeholder="e.g., Incident date, Location, Witness names">
            <button type="button" class="btn btn-outline-danger remove-field">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(inputGroup);
    });
    
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-field') || 
            e.target.parentElement.classList.contains('remove-field')) {
            const btn = e.target.classList.contains('remove-field') ? e.target : e.target.parentElement;
            btn.closest('.input-group').remove();
        }
    });
});
</script>
@endpush