{{-- resources/views/resident/complaints/create.blade.php --}}
@extends('layouts.resident')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-file-alt"></i> File a Complaint</h2>
                <a href="{{ route('resident.complaints.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>

            <form action="{{ route('resident.complaints.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Respondent Information --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user"></i> Respondent Information</h5>
                        <small>Who is this complaint against?</small>
                    </div>
                    <div class="card-body">
                        <!-- Respondent Type Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Respondent Type <span class="text-danger">*</span></label>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="respondent_type" 
                                       id="type_named" value="named" 
                                       {{ old('respondent_type', 'named') == 'named' ? 'checked' : '' }}
                                       onchange="toggleRespondentFields()">
                                <label class="form-check-label" for="type_named">
                                    <strong>Enter Respondent Name</strong> - You know the person's name
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="respondent_type" 
                                       id="type_unknown" value="unknown"
                                       {{ old('respondent_type') == 'unknown' ? 'checked' : '' }}
                                       onchange="toggleRespondentFields()">
                                <label class="form-check-label" for="type_unknown">
                                    <strong>Unknown Suspect</strong> - Identity is unknown
                                </label>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle"></i> Barangay officials will verify and locate the respondent
                            </small>
                        </div>

                        <!-- For Named Respondent -->
                        <div id="named-section">
                            <div class="mb-3">
                                <label for="respondent_name" class="form-label">
                                    Respondent Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('respondent_name') is-invalid @enderror" 
                                       id="respondent_name" name="respondent_name" 
                                       value="{{ old('respondent_name') }}"
                                       placeholder="Enter full name or known name">
                                @error('respondent_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="respondent_alias" class="form-label">
                                    Alias / Nickname (if any)
                                </label>
                                <input type="text" class="form-control @error('respondent_alias') is-invalid @enderror" 
                                       id="respondent_alias" name="respondent_alias" 
                                       value="{{ old('respondent_alias') }}"
                                       placeholder="e.g., Boy, Toto, etc.">
                                @error('respondent_alias')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="respondent_address" class="form-label">
                                    Known Address
                                </label>
                                <textarea class="form-control @error('respondent_address') is-invalid @enderror" 
                                          id="respondent_address" name="respondent_address" rows="2" 
                                          placeholder="e.g., Purok 3, near sari-sari store">{{ old('respondent_address') }}</textarea>
                                @error('respondent_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="respondent_contact" class="form-label">
                                    Contact Number (if known)
                                </label>
                                <input type="text" class="form-control @error('respondent_contact') is-invalid @enderror" 
                                       id="respondent_contact" name="respondent_contact" 
                                       value="{{ old('respondent_contact') }}"
                                       placeholder="09XX-XXX-XXXX">
                                @error('respondent_contact')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- For Unknown Suspect -->
                        <div id="unknown-section" style="display: none;">
                            <div class="mb-3">
                                <label for="respondent_description" class="form-label">
                                    Physical Description <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('respondent_description') is-invalid @enderror" 
                                          id="respondent_description" name="respondent_description" rows="4"
                                          placeholder="Describe appearance, clothing, vehicle, etc.">{{ old('respondent_description') }}</textarea>
                                @error('respondent_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    Example: Male, 25-30 years old, slim build, wearing red shirt, riding black motorcycle
                                </small>
                            </div>
                        </div>

                        <!-- Warning Alert -->
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Note:</strong> 
                            Please provide accurate information. Barangay officials will verify and locate the respondent based on your details.
                        </div>
                    </div>
                </div>

                {{-- Complaint Details --}}
                <div class="card mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Complaint Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="complaint_type_id" class="form-label">Type of Complaint <span class="text-danger">*</span></label>
                            <select class="form-select @error('complaint_type_id') is-invalid @enderror" 
                                    id="complaint_type_id" name="complaint_type_id" required>
                                <option value="">-- Select Type --</option>
                                @foreach($complaintTypes as $type)
                                <option value="{{ $type->id }}" {{ old('complaint_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('complaint_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" name="subject" value="{{ old('subject') }}" 
                                   placeholder="Brief summary of complaint" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Detailed Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="5" 
                                      placeholder="Provide full details of what happened..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="incident_date" class="form-label">Incident Date</label>
                                <input type="date" class="form-control @error('incident_date') is-invalid @enderror" 
                                       id="incident_date" name="incident_date" value="{{ old('incident_date') }}"
                                       max="{{ date('Y-m-d') }}">
                                @error('incident_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="incident_location" class="form-label">Incident Location</label>
                                <input type="text" class="form-control @error('incident_location') is-invalid @enderror" 
                                       id="incident_location" name="incident_location" 
                                       value="{{ old('incident_location') }}" 
                                       placeholder="Where did this happen?">
                                @error('incident_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="evidence_files" class="form-label">Evidence Files (Optional)</label>
                            <input type="file" class="form-control @error('evidence_files.*') is-invalid @enderror" 
                                   id="evidence_files" name="evidence_files[]" multiple 
                                   accept=".jpg,.jpeg,.png,.pdf">
                            @error('evidence_files.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Upload photos, documents, or evidence (Max: 5MB each, JPG, PNG, PDF)</small>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-danger btn-lg">
                        <i class="fas fa-paper-plane"></i> Submit Complaint
                    </button>
                    <a href="{{ route('resident.complaints.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleRespondentFields() {
    const type = document.querySelector('input[name="respondent_type"]:checked').value;
    const namedSection = document.getElementById('named-section');
    const unknownSection = document.getElementById('unknown-section');
    const respondentNameField = document.getElementById('respondent_name');
    const respondentDescField = document.getElementById('respondent_description');
    
    if (type === 'named') {
        namedSection.style.display = 'block';
        unknownSection.style.display = 'none';
        respondentNameField.required = true;
        respondentDescField.required = false;
    } else {
        namedSection.style.display = 'none';
        unknownSection.style.display = 'block';
        respondentNameField.required = false;
        respondentDescField.required = true;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleRespondentFields();
});
</script>
@endpush
@endsection