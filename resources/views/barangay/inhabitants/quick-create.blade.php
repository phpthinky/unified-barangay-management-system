{{-- FILE: resources/views/barangay/inhabitants/quick-create.blade.php --}}
@extends('layouts.barangay')

@section('title', 'Quick Add to RBI - ' . $resident->user->first_name . ' ' . $resident->user->last_name)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('barangay.residents.index') }}">Residents</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('barangay.residents.show', $resident->id) }}">{{ $resident->user->first_name }} {{ $resident->user->last_name }}</a>
                    </li>
                    <li class="breadcrumb-item active">Quick Add to RBI</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-plus me-2"></i> Quick Add to RBI Registry
            </h1>
            <p class="text-muted">Pre-filled form using resident's existing information</p>
        </div>
        <div class="d-flex">
            <a href="{{ route('barangay.residents.show', $resident->id) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Resident
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">REGISTRY OF BARANGAY INHABITANTS BY HOUSEHOLD - QUICK REGISTRATION</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('barangay.inhabitants.quick-store', $resident->id) }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Resident Information Summary -->
                        <div class="alert alert-info mb-4">
                            <h6><i class="fas fa-info-circle me-2"></i>Resident Information Summary</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Name:</strong> {{ $resident->user->first_name }} {{ $resident->user->middle_name }} {{ $resident->user->last_name }} {{ $resident->user->suffix }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Birth Date:</strong> {{ $resident->user->birth_date ? $resident->user->birth_date->format('M d, Y') : 'Not set' }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Gender:</strong> {{ ucfirst($resident->user->gender ?? 'Not set') }}
                                </div>
                            </div>
                        </div>

                        <!-- NAME (1) -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">NAME (1)</h6>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">LAST NAME <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control" 
                                           value="{{ old('last_name', $resident->user->last_name) }}" required readonly>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">FIRST NAME (1-2) <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control" 
                                           value="{{ old('first_name', $resident->user->first_name) }}" required readonly>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">MIDDLE NAME (1-3)</label>
                                    <input type="text" name="middle_name" class="form-control" 
                                           value="{{ old('middle_name', $resident->user->middle_name) }}" readonly>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">EXT. (1-4)</label>
                                    <input type="text" name="ext" class="form-control" 
                                           value="{{ old('ext', $resident->user->suffix) }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- ADDRESS (2) -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">ADDRESS (2)</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">NO (2-1)</label>
                                    <input type="text" name="house_number" class="form-control @error('house_number') is-invalid @enderror" 
                                           value="{{ old('house_number') }}" placeholder="House/Block/Lot Number" >
                                    @error('house_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-8 mb-3">
                                    <label class="form-label">NAME OF ZONE/SITIO (2-2) <span class="text-danger">*</span></label>
                                    <input type="text" name="zone_sitio" class="form-control @error('zone_sitio') is-invalid @enderror" 
                                           value="{{ old('zone_sitio', $resident->purok_zone ?? '') }}" placeholder="e.g., Purok 1, Zone A, Sitio Riverside" required>
                                    @error('zone_sitio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- PLACE OF BIRTH (3-3) -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">PLACE OF BIRTH (3-3)</h6>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">PLACE OF BIRTH <span class="text-danger">*</span></label>
                                    <input type="text" name="place_of_birth" class="form-control @error('place_of_birth') is-invalid @enderror" 
                                           value="{{ old('place_of_birth', $resident->place_of_birth ?? '') }}" placeholder="City/Municipality, Province" required>
                                    @error('place_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- DATE OF BIRTH (4) -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">DATE OF BIRTH (4)</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">DATE OF BIRTH (mm/dd/yy) <span class="text-danger">*</span></label>
                                    <input type="date" name="date_of_birth" class="form-control" 
                                           value="{{ old('date_of_birth', $resident->user->birth_date?->format('Y-m-d')) }}" required readonly>
                                </div>
                            </div>
                        </div>

                        <!-- SEX (5) -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">SEX (5)</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">SEX <span class="text-danger">*</span></label>
                                    <input type="text" name="sex" class="form-control" 
                                           value="{{ ucfirst($resident->user->gender) }}" required readonly>
                                </div>
                            </div>
                        </div>

                        <!-- CIVIL STATUS (6) -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">CIVIL STATUS (6)</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">CIVIL STATUS <span class="text-danger">*</span></label>
                                    <select name="civil_status" class="form-control @error('civil_status') is-invalid @enderror" required>
                                        <option value="">Select Civil Status</option>
                                        <option value="Single" {{ old('civil_status', $resident->civil_status) == 'Single' ? 'selected' : '' }}>Single</option>
                                        <option value="Married" {{ old('civil_status', $resident->civil_status) == 'Married' ? 'selected' : '' }}>Married</option>
                                        <option value="Widowed" {{ old('civil_status', $resident->civil_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                        <option value="Separated" {{ old('civil_status', $resident->civil_status) == 'Separated' ? 'selected' : '' }}>Separated</option>
                                        <option value="Divorced" {{ old('civil_status', $resident->civil_status) == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                    </select>
                                    @error('civil_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- CITIZENSHIP (7) -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">CITIZENSHIP (7)</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">CITIZENSHIP <span class="text-danger">*</span></label>
                                    <input type="text" name="citizenship" class="form-control @error('citizenship') is-invalid @enderror" 
                                           value="{{ old('citizenship', $resident->nationality ?? 'Filipino') }}" required>
                                    @error('citizenship')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- OCCUPATION & EDUCATION -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">OCCUPATION & EDUCATION</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">OCCUPATION</label>
                                    <input type="text" name="occupation" class="form-control @error('occupation') is-invalid @enderror" 
                                           value="{{ old('occupation', $resident->occupation) }}" placeholder="e.g., Farmer, Teacher, Student">
                                    @error('occupation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">EDUCATIONAL ATTAINMENT</label>
                                    <select name="educational_attainment" class="form-control @error('educational_attainment') is-invalid @enderror">
                                        <option value="">Select Education Level</option>
                                        <option value="No Formal Education" {{ old('educational_attainment', $resident->educational_attainment) == 'No Formal Education' ? 'selected' : '' }}>No Formal Education</option>
                                        <option value="Elementary Undergraduate" {{ old('educational_attainment', $resident->educational_attainment) == 'Elementary Undergraduate' ? 'selected' : '' }}>Elementary Undergraduate</option>
                                        <option value="Elementary Graduate" {{ old('educational_attainment', $resident->educational_attainment) == 'Elementary Graduate' ? 'selected' : '' }}>Elementary Graduate</option>
                                        <option value="High School Undergraduate" {{ old('educational_attainment', $resident->educational_attainment) == 'High School Undergraduate' ? 'selected' : '' }}>High School Undergraduate</option>
                                        <option value="High School Graduate" {{ old('educational_attainment', $resident->educational_attainment) == 'High School Graduate' ? 'selected' : '' }}>High School Graduate</option>
                                        <option value="Vocational" {{ old('educational_attainment', $resident->educational_attainment) == 'Vocational' ? 'selected' : '' }}>Vocational</option>
                                        <option value="College Undergraduate" {{ old('educational_attainment', $resident->educational_attainment) == 'College Undergraduate' ? 'selected' : '' }}>College Undergraduate</option>
                                        <option value="College Graduate" {{ old('educational_attainment', $resident->educational_attainment) == 'College Graduate' ? 'selected' : '' }}>College Graduate</option>
                                        <option value="Post Graduate" {{ old('educational_attainment', $resident->educational_attainment) == 'Post Graduate' ? 'selected' : '' }}>Post Graduate</option>
                                    </select>
                                    @error('educational_attainment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- CONTACT INFORMATION -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">CONTACT INFORMATION</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CONTACT NUMBER</label>
                                    <input type="text" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" 
                                           value="{{ old('contact_number', $resident->user->contact_number) }}" placeholder="e.g., 09171234567" required>
                                    @error('contact_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- EMERGENCY CONTACT -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">EMERGENCY CONTACT</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">EMERGENCY CONTACT NAME</label>
                                    <input type="text" name="emergency_contact_name" class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                           value="{{ old('emergency_contact_name', $resident->emergency_contact_name) }}" placeholder="Full Name" required>
                                    @error('emergency_contact_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">EMERGENCY CONTACT NUMBER</label>
                                    <input type="text" name="emergency_contact_number" class="form-control @error('emergency_contact_number') is-invalid @enderror" 
                                           value="{{ old('emergency_contact_number', $resident->emergency_contact_number) }}" placeholder="e.g., 09171234567" required>
                                    @error('emergency_contact_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">RELATIONSHIP</label>
                                    <input type="text" name="emergency_contact_relationship" class="form-control @error('emergency_contact_relationship') is-invalid @enderror" 
                                           value="{{ old('emergency_contact_relationship', $resident->emergency_contact_relationship) }}" placeholder="e.g., Spouse, Parent, Sibling" required>
                                    @error('emergency_contact_relationship')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- RESIDENCY INFORMATION -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-home me-2"></i>RESIDENCY INFORMATION
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">RESIDENCY SINCE (Date of Stay) <span class="text-danger">*</span></label>
                                    <input type="date" name="residency_since" class="form-control @error('residency_since') is-invalid @enderror" 
                                           value="{{ old('residency_since', $resident->residency_since?->format('Y-m-d')) }}" required>
                                    <small class="form-text text-muted">When did the person start residing in this barangay?</small>
                                    @error('residency_since')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">RESIDENCY TYPE <span class="text-danger">*</span></label>
                                    <select name="residency_type" class="form-control @error('residency_type') is-invalid @enderror" required>
                                        <option value="">Select Type</option>
                                        <option value="permanent" {{ old('residency_type', $resident->residency_type) == 'permanent' ? 'selected' : '' }}>Permanent</option>
                                        <option value="temporary" {{ old('residency_type', $resident->residency_type) == 'temporary' ? 'selected' : '' }}>Temporary</option>
                                        <option value="transient" {{ old('residency_type', $resident->residency_type) == 'transient' ? 'selected' : '' }}>Transient</option>
                                    </select>
                                    @error('residency_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- PROOF OF RESIDENCY -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-file-alt me-2"></i>PROOF OF RESIDENCY (Optional)
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CEDULA NUMBER</label>
                                    <input type="text" name="cedula_number" class="form-control @error('cedula_number') is-invalid @enderror" 
                                           value="{{ old('cedula_number') }}" placeholder="e.g., 12345678">
                                    <small class="form-text text-muted">Community Tax Certificate Number</small>
                                    @error('cedula_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CERTIFICATE OF RESIDENCY NUMBER</label>
                                    <input type="text" name="certificate_of_residency_number" class="form-control @error('certificate_of_residency_number') is-invalid @enderror" 
                                           value="{{ old('certificate_of_residency_number') }}" placeholder="e.g., CR-2024-001">
                                    <small class="form-text text-muted">If previously issued</small>
                                    @error('certificate_of_residency_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">PROOF OF RESIDENCY DOCUMENT</label>
                                    <input type="file" name="proof_of_residency_file" class="form-control @error('proof_of_residency_file') is-invalid @enderror" 
                                           accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="form-text text-muted">Upload cedula, certificate, or other proof (PDF, JPG, PNG - Max: 5MB)</small>
                                    @error('proof_of_residency_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- HOUSEHOLD INFORMATION -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">HOUSEHOLD INFORMATION</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Household Number <span class="text-danger">*</span></label>
                                    <input type="text" name="household_number" class="form-control @error('household_number') is-invalid @enderror" 
                                           value="{{ old('household_number') }}" placeholder="e.g., HH-001" required>
                                    <small class="form-text text-muted">Used to group family members together</small>
                                    @error('household_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" name="is_household_head" class="form-check-input" 
                                               id="is_household_head" value="1" {{ old('is_household_head') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_household_head">
                                            <strong>Is Household Head?</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PHOTO -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">PHOTO</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Photo (Optional)</label>
                                    <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
                                    <small class="form-text text-muted">Accepted: JPG, PNG (Max: 2MB)</small>
                                    @error('photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- REMARKS -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">REMARKS</h6>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Remarks/Notes</label>
                                    <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" 
                                              rows="3" placeholder="Any additional notes about this resident...">{{ old('remarks') }}</textarea>
                                    @error('remarks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Add to RBI Registry
                            </button>
                            <a href="{{ route('barangay.residents.show', $resident->id) }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-2">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white py-3">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-lightbulb me-2"></i>Quick Tips
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">Auto-Filled Fields</h6>
                        <p class="small text-muted">Personal information is pre-filled from the resident's profile and cannot be edited here.</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-primary">Required Fields</h6>
                        <p class="small text-muted">Fill in the address, household, and residency information to complete the RBI record.</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-primary">Auto-Verification</h6>
                        <p class="small text-muted">This record will be automatically verified since you're creating it as barangay staff.</p>
                    </div>
                    
                    <div class="alert alert-success small">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Ready for Services:</strong> After adding to RBI, resident can request documents immediately if they meet the 6-month residency requirement.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection