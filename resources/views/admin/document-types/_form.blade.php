{{-- FILE: resources/views/admin/document-types/_form.blade.php (COMPLETE) --}}
<form action="{{ $action }}" method="POST">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="mb-3">
                <label for="name" class="form-label">Document Type Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name', $documentType->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="mb-3">
                <label for="fee" class="form-label">Fee (₱) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" class="form-control @error('fee') is-invalid @enderror" 
                       id="fee" name="fee" value="{{ old('fee', $documentType->fee) }}" required>
                @error('fee')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="processing_time_days" class="form-label">Processing Time (Days) <span class="text-danger">*</span></label>
                <input type="number" min="1" class="form-control @error('processing_time_days') is-invalid @enderror" 
                       id="processing_time_days" name="processing_time_days" 
                       value="{{ old('processing_time_days', $documentType->processing_time_days ?? 1) }}" required>
                @error('processing_time_days')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control @error('description') is-invalid @enderror" 
                  id="description" name="description" rows="3">{{ old('description', $documentType->description) }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="requirements_text" class="form-label">Requirements (one per line)</label>
        <textarea class="form-control @error('requirements_text') is-invalid @enderror" 
                  id="requirements_text" name="requirements_text" rows="5" 
                  placeholder="Valid ID&#10;Proof of Residency&#10;2x2 Picture">{{ old('requirements_text', $documentType->requirements ? implode("\n", $documentType->requirements) : '') }}</textarea>
        @error('requirements_text')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="requires_file_upload" 
                           name="requires_file_upload" value="1" 
                           {{ old('requires_file_upload', $documentType->requires_file_upload) ? 'checked' : '' }}>
                    <label class="form-check-label" for="requires_file_upload">
                        Requires File Upload
                    </label>
                </div>
            </div>

            <div class="mb-3" id="file_upload_label_div" style="display: none;">
                <label for="file_upload_label" class="form-label">File Upload Label</label>
                <input type="text" class="form-control @error('file_upload_label') is-invalid @enderror" 
                       id="file_upload_label" name="file_upload_label" 
                       value="{{ old('file_upload_label', $documentType->file_upload_label) }}"
                       placeholder="e.g. Upload Birth Certificate">
                @error('file_upload_label')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="qr_enabled" 
                           name="qr_enabled" value="1" 
                           {{ old('qr_enabled', $documentType->qr_enabled ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="qr_enabled">
                        Enable QR Code in PDF
                    </label>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" 
                           name="is_active" value="1" 
                           {{ old('is_active', $documentType->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Active (available for residents)
                    </label>
                </div>
            </div>
        </div>
    </div>
<!-- ADD THIS TO YOUR document_types/create.blade.php and edit.blade.php -->
<!-- Place it after the 'category' field -->

<div class="mb-3">
    <label for="document_format" class="form-label">Document Format / Print Type <span class="text-danger">*</span></label>
    <select class="form-select @error('document_format') is-invalid @enderror" 
            id="document_format" name="document_format" required>
        <option value="certificate" {{ old('document_format', $documentType->document_format ?? 'certificate') == 'certificate' ? 'selected' : '' }}>
            📄 Standard Certificate (8.5" x 11" or A4)
        </option>
        <option value="half_sheet" {{ old('document_format', $documentType->document_format) == 'half_sheet' ? 'selected' : '' }}>
            📑 Half Sheet / Short Bond
        </option>
        <option value="legal" {{ old('document_format', $documentType->document_format) == 'legal' ? 'selected' : '' }}>
            📋 Legal Size (8.5" x 14")
        </option>
        <option value="id_card" {{ old('document_format', $documentType->document_format) == 'id_card' ? 'selected' : '' }}>
            🪪 ID Card Size (3.375" x 2.125")
        </option>
        <option value="custom" {{ old('document_format', $documentType->document_format) == 'custom' ? 'selected' : '' }}>
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

<div class="mb-3" id="format_notes_div" style="display: none;">
    <label for="format_notes" class="form-label">Format Notes (Optional)</label>
    <textarea class="form-control @error('format_notes') is-invalid @enderror" 
              id="format_notes" name="format_notes" rows="2"
              placeholder="e.g., Print in landscape, Use colored paper, etc.">{{ old('format_notes', $documentType->format_notes ?? '') }}</textarea>
    <small class="form-text text-muted">
        Add any special printing instructions or notes about this format.
    </small>
    @error('format_notes')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <div class="form-check form-switch">
        <input type="checkbox" class="form-check-input" id="enable_printing" 
               name="enable_printing" value="1"
               {{ old('enable_printing', $documentType->enable_printing ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="enable_printing">
            <strong>Enable Printing</strong>
        </label>
    </div>
    <small class="form-text text-muted">
        <i class="fas fa-info-circle"></i> When disabled, the "Print" button will be hidden for this document type. 
        Useful for documents that require special handling or external printing.
    </small>
</div>

    <div class="mb-3">
        <label for="template_content" class="form-label">PDF Template Content</label>
        <textarea class="form-control @error('template_content') is-invalid @enderror" 
                  id="template_content" name="template_content" rows="8"
                  placeholder="Use placeholders: {resident_name}, {barangay_name}, {date}, {purpose}">{{ old('template_content', $documentType->template_content) }}</textarea>
        <small class="form-text text-muted">
            Available placeholders: <code>{resident_name}</code>, <code>{barangay_name}</code>, <code>{municipality_name}</code>, <code>{date}</code>, <code>{purpose}</code>, <code>{reference_number}</code>, <code>{captain_name}</code>
        </small>
        @error('template_content')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="card bg-light mb-3">
        <div class="card-header">
            <h6 class="mb-0">Template Example</h6>
        </div>
        <div class="card-body">
            <small class="text-muted">
                <strong>Sample Barangay Clearance Template:</strong><br>
                <code style="white-space: pre-line;">BARANGAY CLEARANCE

This is to certify that {resident_name}, a resident of {barangay_name}, {municipality_name}, is known to be of good moral character and law-abiding citizen in the community.

This certification is issued upon request of the above-named person for {purpose} and for whatever legal purpose it may serve.

Issued this {date} at {barangay_name}, {municipality_name}.

Reference No.: {reference_number}</code>
            </small>
        </div>
    </div>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <a href="{{ route('barangay.document-types.index') }}" class="btn btn-secondary me-md-2">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ $documentType->exists ? 'Update' : 'Create' }} Document Type
        </button>
    </div>
</form>

@push('scripts')

<script>
// Show/hide format notes based on selection
document.getElementById('document_format').addEventListener('change', function() {
    const formatNotesDiv = document.getElementById('format_notes_div');
    if (this.value === 'custom' || this.value === 'id_card') {
        formatNotesDiv.style.display = 'block';
    } else {
        formatNotesDiv.style.display = 'none';
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const formatSelect = document.getElementById('document_format');
    const formatNotesDiv = document.getElementById('format_notes_div');
    if (formatSelect.value === 'custom' || formatSelect.value === 'id_card') {
        formatNotesDiv.style.display = 'block';
    }
});
</script>
<script>
document.getElementById('requires_file_upload').addEventListener('change', function() {
    const labelDiv = document.getElementById('file_upload_label_div');
    const labelInput = document.getElementById('file_upload_label');
    
    if (this.checked) {
        labelDiv.style.display = 'block';
        labelInput.required = true;
    } else {
        labelDiv.style.display = 'none';
        labelInput.required = false;
        labelInput.value = '';
    }
});

// Auto-generate template based on document name
document.getElementById('name').addEventListener('blur', function() {
    const templateTextarea = document.getElementById('template_content');
    const name = this.value.toUpperCase();
    
    // Only auto-generate if template is empty
    if (!templateTextarea.value.trim()) {
        let template = '';
        
        if (name.includes('CLEARANCE')) {
            template = `${name}

This is to certify that {resident_name}, a resident of {barangay_name}, {municipality_name}, is known to be of good moral character and law-abiding citizen in the community.

This certification is issued upon request of the above-named person for {purpose} and for whatever legal purpose it may serve.

Issued this {date} at {barangay_name}, {municipality_name}.

Reference No.: {reference_number}`;
        } else if (name.includes('CERTIFICATE')) {
            template = `${name}

This is to certify that {resident_name} is a bona fide resident of {barangay_name}, {municipality_name}.

This certification is issued for {purpose} and for whatever legal purpose it may serve.

Issued this {date} at {barangay_name}, {municipality_name}.

Reference No.: {reference_number}`;
        } else if (name.includes('INDIGENCY')) {
            template = `CERTIFICATE OF INDIGENCY

This is to certify that {resident_name}, a resident of {barangay_name}, {municipality_name}, belongs to the indigent families in this barangay.

This certification is issued for {purpose} and for whatever legal purpose it may serve.

Issued this {date} at {barangay_name}, {municipality_name}.

Reference No.: {reference_number}`;
        } else {
            template = `${name}

This is to certify that {resident_name}, a resident of {barangay_name}, {municipality_name}.

This certification is issued for {purpose} and for whatever legal purpose it may serve.

Issued this {date} at {barangay_name}, {municipality_name}.

Reference No.: {reference_number}`;
        }
        
        templateTextarea.value = template;
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('requires_file_upload');
    if (checkbox.checked) {
        document.getElementById('file_upload_label_div').style.display = 'block';
        document.getElementById('file_upload_label').required = true;
    }
});
</script>
@endpush