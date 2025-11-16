@extends('layouts.resident')

@section('title', 'Request Document')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            <!-- RBI Eligibility Check -->
            @php
                $inhabitant = \App\Models\BarangayInhabitant::where('user_id', auth()->id())
                                                             ->where('barangay_id', auth()->user()->barangay_id)
                                                             ->first();
            @endphp

            @if(!$inhabitant)
            <div class="alert alert-warning">
                <h5><i class="fas fa-exclamation-triangle"></i> Not Registered in RBI</h5>
                <p>You are not yet registered in the <strong>Registry of Barangay Inhabitants (RBI)</strong>.</p>
                <p class="mb-0">
                    <strong>What to do:</strong> Please visit the barangay hall to register first before requesting documents. 
                    Bring a valid ID and proof of residency.
                </p>
                <div class="mt-3">
                    <a href="{{ route('resident.documents.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to My Requests
                    </a>
                </div>
            </div>
            @else
                <!-- Show eligibility warnings if applicable -->
                @php
                    $eligibility = $inhabitant->checkClearanceEligibility();
                    $pendingComplaints = $inhabitant->getPendingComplaintsCount();
                @endphp

                @if(!$eligibility['eligible'])
                <div class="alert alert-danger">
                    <h5><i class="fas fa-times-circle"></i> Not Eligible for Barangay Clearance</h5>
                    <p>You currently cannot request a barangay clearance due to the following:</p>
                    <ul class="mb-2">
                        @foreach($eligibility['reasons'] as $reason)
                            <li>{{ $reason }}</li>
                        @endforeach
                    </ul>
                    <hr>
                    <p class="mb-0">
                        <strong>What to do:</strong> Please resolve the issues above before requesting a clearance. 
                        You may still request other non-clearance documents.
                    </p>
                </div>
                @endif

                @if($pendingComplaints > 0)
                <div class="alert alert-warning">
                    <h5><i class="fas fa-gavel"></i> Pending Complaint Cases</h5>
                    <p>You have <strong>{{ $pendingComplaints }}</strong> pending complaint case(s) filed against you.</p>
                    <p class="mb-0">
                        <strong>Note:</strong> These must be resolved before you can request a barangay clearance. 
                        Other documents may still be available.
                    </p>
                </div>
                @endif

            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-file-alt"></i> Request Barangay Document</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Error!</strong> {{ session('error') }}
                        @if(session('reasons'))
                        <ul class="mb-0 mt-2">
                            @foreach(session('reasons') as $reason)
                                <li>{{ $reason }}</li>
                            @endforeach
                        </ul>
                        @endif
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <form action="{{ route('resident.documents.store') }}" method="POST" enctype="multipart/form-data" id="documentRequestForm">
                        @csrf

                        <!-- Document Type Selection -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="document_type_id" class="form-label fw-bold">Select Document Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('document_type_id') is-invalid @enderror" 
                                        id="document_type_id" name="document_type_id" required>
                                    <option value="">-- Select Document Type --</option>
                                    @foreach($documentTypes as $type)
                                        <option value="{{ $type->id }}" 
                                                data-fee="{{ $type->fee }}"
                                                data-processing-days="{{ $type->processing_days }}"
                                                data-requirements='@json($type->requirements ?? [])'
                                                data-form-fields='@json($type->form_fields ?? [])'
                                                {{ old('document_type_id', $selectedType?->id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }} - ₱{{ number_format($type->fee, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('document_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Document Info Display -->
                        <div id="documentInfo" class="alert alert-info" style="display:none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Fee:</strong> <span id="docFee">₱0.00</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Processing Time:</strong> <span id="docProcessingDays">0</span> day(s)
                                </div>
                                <div class="col-md-4">
                                    <strong>Copies:</strong> <span id="totalCopies">1</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <strong>Requirements:</strong>
                                <ul id="requirementsList" class="mb-0"></ul>
                            </div>
                            <div class="mt-2">
                                <strong class="text-primary">Total Amount: <span id="totalAmount">₱0.00</span></strong>
                            </div>
                        </div>

                        <!-- Dynamic Form Fields Container -->
                        <div id="dynamicFormFields" class="mb-4"></div>

                        <!-- Number of Copies -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="copies_requested" class="form-label fw-bold">Number of Copies <span class="text-danger">*</span></label>
                                <select class="form-select @error('copies_requested') is-invalid @enderror" 
                                        id="copies_requested" name="copies_requested" required>
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ old('copies_requested', 1) == $i ? 'selected' : '' }}>
                                            {{ $i }} {{ Str::plural('copy', $i) }}
                                        </option>
                                    @endfor
                                </select>
                                @error('copies_requested')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Purpose (General) -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="general_purpose" class="form-label fw-bold">General Purpose of Request <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('purpose') is-invalid @enderror" 
                                       id="general_purpose" name="purpose" rows="2" required
                                       placeholder="e.g., For employment, School enrollment, Business permit, etc.">{{ old('purpose') }}</textarea>
                                @error('purpose')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Supporting Documents -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="supporting_files" class="form-label fw-bold">Supporting Documents (Optional)</label>
                                <input type="file" class="form-control @error('supporting_files.*') is-invalid @enderror" 
                                       id="supporting_files" name="supporting_files[]" multiple
                                       accept=".jpg,.jpeg,.png,.pdf">
                                <small class="form-text text-muted">Upload required documents (JPG, PNG, PDF - Max: 2MB each)</small>
                                @error('supporting_files.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Resident Information Preview -->
                        <div class="card mb-4 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-user"></i> Your Information (Auto-filled in forms)</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success mb-3">
                                    <i class="fas fa-check-circle"></i> 
                                    <strong>Good news!</strong> The form fields below will be automatically filled with this information. You can still edit them if needed.
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Full Name:</strong> {{ $residentProfile->full_name }}</p>
                                        <p><strong>Age:</strong> {{ $residentProfile->age ?? 'Not specified' }}</p>
                                        <p><strong>Civil Status:</strong> {{ ucfirst($residentProfile->civil_status) }}</p>
                                        <p><strong>Sex:</strong> {{ $residentProfile->sex }}</p>
                                        @if($residentProfile->occupation)
                                        <p><strong>Occupation:</strong> {{ $residentProfile->occupation }}</p>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Address:</strong> {{ $residentProfile->full_address }}</p>
                                        <p><strong>Place of Birth:</strong> {{ $residentProfile->place_of_birth }}</p>
                                        <p><strong>Date of Birth:</strong> {{ $residentProfile->date_of_birth->format('F d, Y') }}</p>
                                        <p><strong>Citizenship:</strong> {{ $residentProfile->citizenship }}</p>
                                        <p><strong>Barangay:</strong> {{ $residentProfile->barangay->name }}</p>
                                    </div>
                                </div>
                                <hr>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    If any information is incorrect, please contact the barangay office to update your records before submitting your request.
                                </small>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="agree_terms" required>
                                <label class="form-check-label" for="agree_terms">
                                    I certify that all information provided is true and accurate. I understand that providing false information may result in the rejection of this request and possible legal action.
                                </label>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('resident.documents.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to My Requests
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="fas fa-paper-plane"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const documentTypeSelect = document.getElementById('document_type_id');
    const copiesSelect = document.getElementById('copies_requested');
    const dynamicFormFields = document.getElementById('dynamicFormFields');
    const documentInfo = document.getElementById('documentInfo');
    const submitBtn = document.getElementById('submitBtn');

    // Resident data from PHP - FIXED: Added proper escaping for special characters
    const residentData = {
        name: '{{ addslashes($residentProfile->full_name) }}',
        full_name: '{{ addslashes($residentProfile->full_name) }}',
        surname: '{{ addslashes($residentProfile->last_name) }}',
        last_name: '{{ addslashes($residentProfile->last_name) }}',
        first_name: '{{ addslashes($residentProfile->first_name) }}',
        middle_name: '{{ addslashes($residentProfile->middle_name ?? "") }}',
        birthday: '{{ $residentProfile->date_of_birth->format("Y-m-d") }}',
        date_of_birth: '{{ $residentProfile->date_of_birth->format("Y-m-d") }}',
        place_of_birth: '{{ addslashes($residentProfile->place_of_birth) }}',
        address: '{{ addslashes($residentProfile->full_address) }}',
        age: '{{ $residentProfile->age ?? "" }}',
        sex: '{{ $residentProfile->sex }}',
        civil_status: '{{ ucfirst($residentProfile->civil_status) }}',
        citizenship: '{{ addslashes($residentProfile->citizenship) }}',
        occupation: '{{ addslashes($residentProfile->occupation ?? "") }}'
    };

    // Function to auto-fill field based on resident data
    function getAutoFillValue(fieldName) {
        // Direct match
        if (residentData[fieldName]) {
            return residentData[fieldName];
        }
        
        // Smart matching for common field name variations
        const fieldLower = fieldName.toLowerCase();
        
        // Name variations
        if (fieldLower.includes('name') && !fieldLower.includes('business') && !fieldLower.includes('operator')) {
            return residentData.name;
        }
        
        // Birthday variations
        if (fieldLower.includes('birthday') || fieldLower.includes('birth_date')) {
            return residentData.birthday;
        }
        
        // Birth place variations
        if (fieldLower.includes('place') && fieldLower.includes('birth')) {
            return residentData.place_of_birth;
        }
        
        // Address variations
        if (fieldLower.includes('address')) {
            return residentData.address;
        }
        
        return '';
    }

    // Function to render dynamic form fields
    function renderDynamicFields(formFields) {
        dynamicFormFields.innerHTML = '';
        
        if (!formFields || !Array.isArray(formFields) || formFields.length === 0) {
            return;
        }

        const title = document.createElement('h5');
        title.className = 'mb-3 text-primary';
        title.innerHTML = '<i class="fas fa-edit"></i> Document Specific Information';
        dynamicFormFields.appendChild(title);

        const infoNote = document.createElement('div');
        infoNote.className = 'alert alert-info mb-3';
        infoNote.innerHTML = '<i class="fas fa-info-circle"></i> <small>Some fields are auto-filled from your profile. You can edit them if needed.</small>';
        dynamicFormFields.appendChild(infoNote);

        const row = document.createElement('div');
        row.className = 'row';

        formFields.forEach(function(field) {
            const colDiv = document.createElement('div');
            colDiv.className = 'col-md-6 mb-3';

            const formGroup = document.createElement('div');
            
            const label = document.createElement('label');
            label.className = 'form-label fw-bold';
            label.textContent = field.label;
            if (field.required) {
                const required = document.createElement('span');
                required.className = 'text-danger';
                required.textContent = ' *';
                label.appendChild(required);
            }
            formGroup.appendChild(label);

            let input;
            const fieldName = 'form_data[' + field.name + ']';
            const autoFillValue = getAutoFillValue(field.name);

            switch(field.type) {
                case 'textarea':
                    input = document.createElement('textarea');
                    input.className = 'form-control';
                    input.rows = 2;
                    if (autoFillValue) {
                        input.value = autoFillValue;
                    }
                    break;
                    
                case 'select':
                    input = document.createElement('select');
                    input.className = 'form-select';
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = '-- Select --';
                    input.appendChild(defaultOption);
                    
                    if (field.options && Array.isArray(field.options)) {
                        field.options.forEach(function(option) {
                            const opt = document.createElement('option');
                            opt.value = option;
                            opt.textContent = option;
                            // Auto-select if matches resident data
                            if (autoFillValue && option.toLowerCase() === autoFillValue.toLowerCase()) {
                                opt.selected = true;
                            }
                            input.appendChild(opt);
                        });
                    }
                    break;
                    
                case 'date':
                    input = document.createElement('input');
                    input.type = 'date';
                    input.className = 'form-control';
                    if (autoFillValue) {
                        input.value = autoFillValue;
                    }
                    break;
                    
                case 'number':
                    input = document.createElement('input');
                    input.type = 'number';
                    input.className = 'form-control';
                    input.min = 0;
                    if (autoFillValue) {
                        input.value = autoFillValue;
                    }
                    break;
                    
                default:
                    input = document.createElement('input');
                    input.type = 'text';
                    input.className = 'form-control';
                    if (autoFillValue) {
                        input.value = autoFillValue;
                        input.style.backgroundColor = '#f0f8ff'; // Light blue background for auto-filled
                    }
            }

            input.name = fieldName;
            input.id = field.name;
            if (field.required) {
                input.required = true;
            }

            // Add placeholder if specified
            if (field.placeholder) {
                input.placeholder = field.placeholder;
            }

            formGroup.appendChild(input);
            colDiv.appendChild(formGroup);
            row.appendChild(colDiv);
        });

        dynamicFormFields.appendChild(row);
    }

    // Function to update document info
    function updateDocumentInfo() {
        const selectedOption = documentTypeSelect.options[documentTypeSelect.selectedIndex];
        
        if (!documentTypeSelect.value) {
            documentInfo.style.display = 'none';
            dynamicFormFields.innerHTML = '';
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Request';
            return;
        }

        try {
            const fee = parseFloat(selectedOption.dataset.fee || 0);
            const processingDays = selectedOption.dataset.processingDays || 0;
            
            let requirements = [];
            let formFields = [];
            
            // Parse requirements
            try {
                const reqData = selectedOption.getAttribute('data-requirements');
                requirements = reqData ? JSON.parse(reqData) : [];
                if (!Array.isArray(requirements)) {
                    requirements = [];
                }
            } catch (e) {
                console.error('Error parsing requirements:', e);
                requirements = [];
            }
            
            // Parse form fields
            try {
                const fieldsData = selectedOption.getAttribute('data-form-fields');
                formFields = fieldsData ? JSON.parse(fieldsData) : [];
                if (!Array.isArray(formFields)) {
                    formFields = [];
                }
            } catch (e) {
                console.error('Error parsing form fields:', e);
                formFields = [];
            }
            
            const copies = parseInt(copiesSelect.value) || 1;
            const totalAmount = fee * copies;

            // Update display
            document.getElementById('docFee').textContent = '₱' + fee.toFixed(2);
            document.getElementById('docProcessingDays').textContent = processingDays;
            document.getElementById('totalCopies').textContent = copies;
            document.getElementById('totalAmount').textContent = '₱' + totalAmount.toFixed(2);

            // Update requirements list
            const requirementsList = document.getElementById('requirementsList');
            requirementsList.innerHTML = '';
            if (requirements.length > 0) {
                requirements.forEach(function(req) {
                    const li = document.createElement('li');
                    li.textContent = req;
                    requirementsList.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.textContent = 'No specific requirements';
                requirementsList.appendChild(li);
            }

            // Update submit button
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Request (₱' + totalAmount.toFixed(2) + ')';

            // Render dynamic form fields
            renderDynamicFields(formFields);

            documentInfo.style.display = 'block';
        } catch (error) {
            console.error('Error updating document info:', error);
            alert('There was an error loading the document information. Please refresh the page and try again.');
        }
    }

    // Event listeners
    documentTypeSelect.addEventListener('change', updateDocumentInfo);
    copiesSelect.addEventListener('change', updateDocumentInfo);

    // Form submission validation
    document.getElementById('documentRequestForm').addEventListener('submit', function(e) {
        const agreeTerms = document.getElementById('agree_terms');
        if (!agreeTerms.checked) {
            e.preventDefault();
            alert('Please agree to the terms and conditions before submitting your request.');
            return false;
        }
    });

    // Initialize on page load
    updateDocumentInfo();
});
</script>
@endpush
@endsection