@extends('layouts.barangay')

@section('title', 'Create Document Type')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-plus-circle"></i> Create New Document Type</h2>
                <a href="{{ route('barangay.document-types.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>

            <form action="{{ route('barangay.document-types.store') }}" method="POST">
                @csrf

                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Basic Information</h5>
                            </div>
                            <div class="card-body">
                                <!-- Name -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">Document Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Slug -->
                                <div class="mb-3">
                                    <label for="slug" class="form-label">Slug (Optional)</label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                           id="slug" name="slug" value="{{ old('slug') }}">
                                    <small class="text-muted">Leave blank to auto-generate from name</small>
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Category -->
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" name="category" required>
                                        <option value="">-- Select Category --</option>
                                        <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>General</option>
                                        <option value="identification" {{ old('category') == 'identification' ? 'selected' : '' }}>Identification</option>
                                        <option value="business" {{ old('category') == 'business' ? 'selected' : '' }}>Business</option>
                                        <option value="permit" {{ old('category') == 'permit' ? 'selected' : '' }}>Permit</option>
                                        <option value="employment" {{ old('category') == 'employment' ? 'selected' : '' }}>Employment</option>
                                        <option value="agricultural" {{ old('category') == 'agricultural' ? 'selected' : '' }}>Agricultural</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Document Format -->
                                <div class="mb-3">
                                    <label for="document_format" class="form-label">Document Format / Print Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('document_format') is-invalid @enderror" 
                                            id="document_format" name="document_format" required>
                                        <option value="certificate" {{ old('document_format', 'certificate') == 'certificate' ? 'selected' : '' }}>
                                            📄 Standard Certificate (8.5" x 11" or A4)
                                        </option>
                                        <option value="half_sheet" {{ old('document_format') == 'half_sheet' ? 'selected' : '' }}>
                                            📑 Half Sheet / Short Bond
                                        </option>
                                        <option value="legal" {{ old('document_format') == 'legal' ? 'selected' : '' }}>
                                            📋 Legal Size (8.5" x 14")
                                        </option>
                                        <option value="id_card" {{ old('document_format') == 'id_card' ? 'selected' : '' }}>
                                            🪪 ID Card Size (3.375" x 2.125")
                                        </option>
                                        <option value="custom" {{ old('document_format') == 'custom' ? 'selected' : '' }}>
                                            ⚙️ Custom Format
                                        </option>
                                    </select>
                                    <small class="form-text text-muted">
                                        Select the paper size/format for printing this document. Most certificates use standard A4/Letter size.
                                    </small>
                                    @error('document_format')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Format Notes -->
                                <div class="mb-3" id="format_notes_div" style="display: none;">
                                    <label for="format_notes" class="form-label">Format Notes (Optional)</label>
                                    <textarea class="form-control @error('format_notes') is-invalid @enderror" 
                                              id="format_notes" name="format_notes" rows="2"
                                              placeholder="e.g., Print in landscape, Use colored paper, Requires PVC card printer">{{ old('format_notes') }}</textarea>
                                    <small class="form-text text-muted">
                                        Add any special printing instructions or notes about this format.
                                    </small>
                                    @error('format_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Requirements -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">Requirements</h5>
                            </div>
                            <div class="card-body">
                                <div id="requirements-container">
                                    <div class="requirement-item mb-2">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="requirements[]" 
                                                   placeholder="e.g., Valid ID, Birth Certificate">
                                            <button type="button" class="btn btn-danger remove-requirement">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary" id="add-requirement">
                                    <i class="fas fa-plus"></i> Add Requirement
                                </button>
                            </div>
                        </div>

                        <!-- Form Fields -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">Form Fields (Dynamic Fields for Residents)</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    <strong>Note:</strong> These fields will appear when residents request this document. 
                                    Basic info (name, address, etc.) is auto-filled from their profile.
                                </div>
                                
                                <div id="form-fields-container">
                                    <!-- Form fields will be added here dynamically -->
                                </div>
                                
                                <button type="button" class="btn btn-sm btn-primary" id="add-form-field">
                                    <i class="fas fa-plus"></i> Add Form Field
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-4">
                        <!-- Settings -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">Settings</h5>
                            </div>
                            <div class="card-body">
                                <!-- Fee -->
                                <div class="mb-3">
                                    <label for="fee" class="form-label">Fee (₱) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('fee') is-invalid @enderror" 
                                           id="fee" name="fee" value="{{ old('fee', 0) }}" required>
                                    @error('fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Processing Days -->
                                <div class="mb-3">
                                    <label for="processing_days" class="form-label">Processing Days <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('processing_days') is-invalid @enderror" 
                                           id="processing_days" name="processing_days" value="{{ old('processing_days', 3) }}" required>
                                    @error('processing_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Sort Order -->
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                           id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr>

                                <!-- Enable Printing -->
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="enable_printing" 
                                               name="enable_printing" value="1" {{ old('enable_printing', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="enable_printing">
                                            <strong>Enable Printing</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> When disabled, the "Print" button will be hidden for this document type.
                                    </small>
                                </div>

                                <!-- Requires Verification -->
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="requires_verification" 
                                               name="requires_verification" value="1" {{ old('requires_verification', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="requires_verification">
                                            Requires Verification
                                        </label>
                                    </div>
                                </div>

                                <!-- Is Active -->
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="is_active" 
                                               name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('barangay.document-types.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Document Type
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    const slug = this.value.toLowerCase()
        .replace(/[^\w ]+/g, '')
        .replace(/ +/g, '-');
    document.getElementById('slug').value = slug;
});

// Show/hide format notes
document.getElementById('document_format').addEventListener('change', function() {
    const formatNotesDiv = document.getElementById('format_notes_div');
    if (this.value === 'custom' || this.value === 'id_card') {
        formatNotesDiv.style.display = 'block';
    } else {
        formatNotesDiv.style.display = 'none';
    }
});

// Initialize format notes visibility
document.addEventListener('DOMContentLoaded', function() {
    const formatSelect = document.getElementById('document_format');
    const formatNotesDiv = document.getElementById('format_notes_div');
    if (formatSelect.value === 'custom' || formatSelect.value === 'id_card') {
        formatNotesDiv.style.display = 'block';
    }
});

// Requirements Management
document.getElementById('add-requirement').addEventListener('click', function() {
    const container = document.getElementById('requirements-container');
    const newRequirement = document.createElement('div');
    newRequirement.className = 'requirement-item mb-2';
    newRequirement.innerHTML = `
        <div class="input-group">
            <input type="text" class="form-control" name="requirements[]" 
                   placeholder="e.g., Valid ID, Birth Certificate">
            <button type="button" class="btn btn-danger remove-requirement">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(newRequirement);
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-requirement') || e.target.parentElement.classList.contains('remove-requirement')) {
        const button = e.target.classList.contains('remove-requirement') ? e.target : e.target.parentElement;
        button.closest('.requirement-item').remove();
    }
});

// Form Fields Management
let formFieldIndex = 0;

document.getElementById('add-form-field').addEventListener('click', function() {
    addFormField();
});

function addFormField(fieldData = null) {
    const container = document.getElementById('form-fields-container');
    const fieldDiv = document.createElement('div');
    fieldDiv.className = 'card mb-3 form-field-item';
    fieldDiv.innerHTML = `
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Field Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="form_fields[${formFieldIndex}][name]" 
                           value="${fieldData?.name || ''}" placeholder="e.g., purpose" required>
                    <small class="text-muted">No spaces, use underscore</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Label <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="form_fields[${formFieldIndex}][label]" 
                           value="${fieldData?.label || ''}" placeholder="e.g., Purpose" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type <span class="text-danger">*</span></label>
                    <select class="form-select field-type-select" name="form_fields[${formFieldIndex}][type]" required>
                        <option value="text" ${fieldData?.type === 'text' ? 'selected' : ''}>Text</option>
                        <option value="textarea" ${fieldData?.type === 'textarea' ? 'selected' : ''}>Textarea</option>
                        <option value="number" ${fieldData?.type === 'number' ? 'selected' : ''}>Number</option>
                        <option value="date" ${fieldData?.type === 'date' ? 'selected' : ''}>Date</option>
                        <option value="select" ${fieldData?.type === 'select' ? 'selected' : ''}>Select Dropdown</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-form-field w-100">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" 
                               name="form_fields[${formFieldIndex}][required]" 
                               value="1" ${fieldData?.required ? 'checked' : ''}>
                        <label class="form-check-label">Required Field</label>
                    </div>
                </div>
                <div class="col-md-6 options-container" style="display: ${fieldData?.type === 'select' ? 'block' : 'none'};">
                    <label class="form-label">Options (comma-separated)</label>
                    <input type="text" class="form-control" name="form_fields[${formFieldIndex}][options]" 
                           value="${fieldData?.options ? fieldData.options.join(', ') : ''}"
                           placeholder="e.g., Single, Married, Widowed">
                </div>
            </div>
        </div>
    `;
    container.appendChild(fieldDiv);
    formFieldIndex++;
    
    // Add event listener for type change
    const typeSelect = fieldDiv.querySelector('.field-type-select');
    const optionsContainer = fieldDiv.querySelector('.options-container');
    
    typeSelect.addEventListener('change', function() {
        if (this.value === 'select') {
            optionsContainer.style.display = 'block';
        } else {
            optionsContainer.style.display = 'none';
        }
    });
}

// Remove form field
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-form-field') || e.target.parentElement.classList.contains('remove-form-field')) {
        const button = e.target.classList.contains('remove-form-field') ? e.target : e.target.parentElement;
        button.closest('.form-field-item').remove();
    }
});
</script>
@endpush
@endsection