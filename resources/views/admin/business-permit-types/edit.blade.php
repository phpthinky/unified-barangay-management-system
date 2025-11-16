@extends('layouts.admin')

@section('title', 'Edit Business Permit Type')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit: {{ $businessPermitType->name }}</h1>
        <div>
            <a href="{{ route('admin.business-permit-types.show', $businessPermitType) }}" class="btn btn-info">
                <i class="fas fa-eye me-2"></i>View Details
            </a>
            <a href="{{ route('admin.business-permit-types.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <form action="{{ route('admin.business-permit-types.update', $businessPermitType) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Permit Type Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $businessPermitType->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Slug</label>
                                <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" 
                                       value="{{ old('slug', $businessPermitType->slug) }}" placeholder="auto-generated">
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $businessPermitType->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                    <option value="">Select Category</option>
                                    <option value="micro" {{ old('category', $businessPermitType->category) == 'micro' ? 'selected' : '' }}>Micro Enterprise</option>
                                    <option value="small" {{ old('category', $businessPermitType->category) == 'small' ? 'selected' : '' }}>Small Enterprise</option>
                                    <option value="medium" {{ old('category', $businessPermitType->category) == 'medium' ? 'selected' : '' }}>Medium Enterprise</option>
                                    <option value="large" {{ old('category', $businessPermitType->category) == 'large' ? 'selected' : '' }}>Large Enterprise</option>
                                    <option value="home_based" {{ old('category', $businessPermitType->category) == 'home_based' ? 'selected' : '' }}>Home Based</option>
                                    <option value="street_vendor" {{ old('category', $businessPermitType->category) == 'street_vendor' ? 'selected' : '' }}>Street Vendor</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Base Fee (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="base_fee" class="form-control @error('base_fee') is-invalid @enderror" 
                                       value="{{ old('base_fee', $businessPermitType->base_fee) }}" min="0" max="999999.99" required>
                                @error('base_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" 
                                       value="{{ old('sort_order', $businessPermitType->sort_order) }}" min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Processing Days <span class="text-danger">*</span></label>
                                <input type="number" name="processing_days" class="form-control @error('processing_days') is-invalid @enderror" 
                                       value="{{ old('processing_days', $businessPermitType->processing_days) }}" min="1" max="365" required>
                                @error('processing_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Validity (Months) <span class="text-danger">*</span></label>
                                <input type="number" name="validity_months" class="form-control @error('validity_months') is-invalid @enderror" 
                                       value="{{ old('validity_months', $businessPermitType->validity_months) }}" min="1" max="60" required>
                                @error('validity_months')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Fees -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-money-bill me-2"></i>Additional Fees</h5>
                    </div>
                    <div class="card-body">
                        <div id="additional-fees-container">
                            @php
                                $additionalFees = old('additional_fees', $businessPermitType->additional_fees ?? []);
                            @endphp
                            @if($additionalFees && count($additionalFees) > 0)
                                @foreach($additionalFees as $index => $fee)
                                    <div class="row mb-2 fee-row">
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" 
                                                   name="additional_fees[{{ $index }}][name]" 
                                                   value="{{ $fee['name'] ?? '' }}"
                                                   placeholder="Fee name">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" step="0.01" class="form-control" 
                                                   name="additional_fees[{{ $index }}][amount]" 
                                                   value="{{ $fee['amount'] ?? '' }}"
                                                   placeholder="Amount">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-danger w-100 remove-fee">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" id="add-fee" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-plus me-1"></i>Add Fee
                        </button>
                    </div>
                </div>

                <!-- Requirements -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-list-check me-2"></i>Requirements</h5>
                    </div>
                    <div class="card-body">
                        <div id="requirements-container">
                            @php
                                $requirements = old('requirements', $businessPermitType->requirements ?? []);
                            @endphp
                            @if($requirements && count($requirements) > 0)
                                @foreach($requirements as $requirement)
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="requirements[]" 
                                               value="{{ $requirement }}" placeholder="Requirement">
                                        <button type="button" class="btn btn-outline-danger remove-requirement">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="requirements[]" placeholder="Requirement">
                                    <button type="button" class="btn btn-outline-danger remove-requirement">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <button type="button" id="add-requirement" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-plus me-1"></i>Add Requirement
                        </button>
                    </div>
                </div>

                <!-- Template Fields -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Template Fields</h5>
                    </div>
                    <div class="card-body">
                        <div id="template-fields-container">
                            @php
                                $templateFields = old('template_fields', $businessPermitType->template_fields ?? []);
                            @endphp
                            @if($templateFields && count($templateFields) > 0)
                                @foreach($templateFields as $field)
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="template_fields[]" 
                                               value="{{ $field }}" placeholder="Field name">
                                        <button type="button" class="btn btn-outline-danger remove-template-field">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="template_fields[]" placeholder="Field name">
                                    <button type="button" class="btn btn-outline-danger remove-template-field">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <button type="button" id="add-template-field" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-plus me-1"></i>Add Field
                        </button>
                    </div>
                </div>

                <!-- Template Content -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-file-code me-2"></i>Template Content</h5>
                    </div>
                    <div class="card-body">
                        <textarea name="template_content" rows="10" class="form-control @error('template_content') is-invalid @enderror">{{ old('template_content', $businessPermitType->template_content) }}</textarea>
                        @error('template_content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Use @verbatim{{field_name}}@endverbatim to insert dynamic fields</small>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Additional Requirements -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Additional Clearances</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input type="checkbox" name="requires_inspection" value="1" 
                                   class="form-check-input" id="requires_inspection" 
                                   {{ old('requires_inspection', $businessPermitType->requires_inspection) ? 'checked' : '' }}>
                            <label class="form-check-label" for="requires_inspection">
                                Requires Business Inspection
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="requires_fire_safety" value="1" 
                                   class="form-check-input" id="requires_fire_safety" 
                                   {{ old('requires_fire_safety', $businessPermitType->requires_fire_safety) ? 'checked' : '' }}>
                            <label class="form-check-label" for="requires_fire_safety">
                                Requires Fire Safety Certificate
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="requires_health_permit" value="1" 
                                   class="form-check-input" id="requires_health_permit" 
                                   {{ old('requires_health_permit', $businessPermitType->requires_health_permit) ? 'checked' : '' }}>
                            <label class="form-check-label" for="requires_health_permit">
                                Requires Health Permit
                            </label>
                        </div>
                        <div class="form-check mb-0">
                            <input type="checkbox" name="requires_environmental_clearance" value="1" 
                                   class="form-check-input" id="requires_environmental_clearance" 
                                   {{ old('requires_environmental_clearance', $businessPermitType->requires_environmental_clearance) ? 'checked' : '' }}>
                            <label class="form-check-label" for="requires_environmental_clearance">
                                Requires Environmental Clearance
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-toggle-on me-2"></i>Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" value="1" 
                                   class="form-check-input" id="is_active" 
                                   {{ old('is_active', $businessPermitType->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                        <small class="text-muted">Active permit types can be selected by applicants</small>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Permit Type
                            </button>
                            <a href="{{ route('admin.business-permit-types.show', $businessPermitType) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            @if($businessPermitType->businessPermits()->count() == 0)
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash me-2"></i>Delete
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Delete Modal -->
@if($businessPermitType->businessPermits()->count() == 0)
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete Permit Type</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.business-permit-types.destroy', $businessPermitType) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong>{{ $businessPermitType->name }}</strong>?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameField = document.getElementById('name');
    const slugField = document.getElementById('slug');
    
    nameField.addEventListener('blur', function() {
        if (!slugField.value || slugField.value === '{{ $businessPermitType->slug }}') {
            slugField.value = this.value.toLowerCase()
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }
    });
    
    let feeIndex = {{ $additionalFees ? count($additionalFees) : 0 }};
    document.getElementById('add-fee').addEventListener('click', function() {
        const container = document.getElementById('additional-fees-container');
        const row = document.createElement('div');
        row.className = 'row mb-2 fee-row';
        row.innerHTML = `
            <div class="col-md-7">
                <input type="text" class="form-control" name="additional_fees[${feeIndex}][name]" placeholder="Fee name">
            </div>
            <div class="col-md-4">
                <input type="number" step="0.01" class="form-control" name="additional_fees[${feeIndex}][amount]" placeholder="Amount">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger w-100 remove-fee">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
        feeIndex++;
    });
    
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-fee')) e.target.closest('.fee-row').remove();
        if (e.target.closest('.remove-requirement')) e.target.closest('.input-group').remove();
        if (e.target.closest('.remove-template-field')) e.target.closest('.input-group').remove();
    });
    
    document.getElementById('add-requirement').addEventListener('click', function() {
        const container = document.getElementById('requirements-container');
        const group = document.createElement('div');
        group.className = 'input-group mb-2';
        group.innerHTML = `
            <input type="text" class="form-control" name="requirements[]" placeholder="Requirement">
            <button type="button" class="btn btn-outline-danger remove-requirement">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(group);
    });
    
    document.getElementById('add-template-field').addEventListener('click', function() {
        const container = document.getElementById('template-fields-container');
        const group = document.createElement('div');
        group.className = 'input-group mb-2';
        group.innerHTML = `
            <input type="text" class="form-control" name="template_fields[]" placeholder="Field name">
            <button type="button" class="btn btn-outline-danger remove-template-field">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(group);
    });
});
</script>
@endpush