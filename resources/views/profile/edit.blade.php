<!-- resources/views/profile/edit.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Resident Profile</h4>
                        <div class="badge bg-white text-primary">
                            {{ $completion }}% Complete
                        </div>
                    </div>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-white" 
                             role="progressbar" 
                             style="width: {{ $completion }}%" 
                             aria-valuenow="{{ $completion }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show">
                            {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        
                        <!-- Personal Information Section -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">Personal Information</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                           id="first_name" name="first_name" 
                                           value="{{ old('first_name', $profile->first_name) }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="middle_name" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control @error('middle_name') is-invalid @enderror" 
                                           id="middle_name" name="middle_name" 
                                           value="{{ old('middle_name', $profile->middle_name) }}">
                                    @error('middle_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                           id="last_name" name="last_name" 
                                           value="{{ old('last_name', $profile->last_name) }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="birthdate" class="form-label">Birthdate *</label>
                                    <input type="date" class="form-control @error('birthdate') is-invalid @enderror" 
                                           id="birthdate" name="birthdate" 
                                           value="{{ old('birthdate', optional($profile->birthdate)->format('Y-m-d')) }}" required>
                                    @error('birthdate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="gender" class="form-label">Gender *</label>
                                    <select class="form-select @error('gender') is-invalid @enderror" 
                                            id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" @selected(old('gender', $profile->gender) == 'male')>Male</option>
                                        <option value="female" @selected(old('gender', $profile->gender) == 'female')>Female</option>
                                        <option value="other" @selected(old('gender', $profile->gender) == 'other')>Other</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="contact_number" class="form-label">Contact Number *</label>
                                    <input type="text" class="form-control @error('contact_number') is-invalid @enderror" 
                                           id="contact_number" name="contact_number" 
                                           value="{{ old('contact_number', $profile->contact_number) }}" required>
                                    @error('contact_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" 
                                           value="{{ old('email', $profile->email ?? auth()->user()->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Address Information Section -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">Address Information</h5>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="house_number" class="form-label">House Number </label>
                                    <input type="text" class="form-control @error('house_number') is-invalid @enderror" 
                                           id="house_number" name="house_number" 
                                           value="{{ old('house_number', $profile->house_number) }}" >
                                    @error('house_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-5">
                                    <label for="street" class="form-label">Street </label>
                                    <input type="text" class="form-control @error('street') is-invalid @enderror" 
                                           id="street" name="street" 
                                           value="{{ old('street', $profile->street) }}" >
                                    @error('street')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="purok" class="form-label">Purok/Zone *</label>
                                    <input type="text" class="form-control @error('purok') is-invalid @enderror" 
                                           id="purok" name="purok"                                            value="{{ old('purok', $profile->purok) }}" required>
                                    @error('purok')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="barangay" class="form-label">Barangay *</label>
                                  
                                               <select name="barangay_id" class="form-select" required>
                                                    <option value="">Select Barangay</option>
                                                    @foreach($barangays as $barangay)
                                                        <option value="{{ $barangay->id }}" 
                                                            {{ old('barangay_id', $profile->barangay_id) == $barangay->id ? 'selected' : '' }}>
                                                            {{ $barangay->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                    @error('barangay')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="municipality" class="form-label">Municipality *</label>
                                    <input type="text" class="form-control @error('municipality') is-invalid @enderror" 
                                           id="municipality" name="municipality" 
                                           value="{{ old('municipality', $profile->municipality ?? 'Sablayan') }}" required>
                                    @error('municipality')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="province" class="form-label">Province *</label>
                                    <input type="text" class="form-control @error('province') is-invalid @enderror" 
                                           id="province" name="province" 
                                           value="{{ old('province', $profile->province ?? 'Occidental Mindoro') }}" required>
                                    @error('province')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Identification Section -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">Identification</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="valid_id_type" class="form-label">Valid ID Type *</label>
                                    <select class="form-select @error('valid_id_type') is-invalid @enderror" 
                                            id="valid_id_type" name="valid_id_type" required>
                                        <option value="">Select ID Type</option>
                                        @foreach($idTypes as $type)
                                            <option value="{{ $type }}" @selected(old('valid_id_type', $profile->valid_id_type) == $type)>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('valid_id_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="valid_id_number" class="form-label">ID Number *</label>
                                    <input type="text" class="form-control @error('valid_id_number') is-invalid @enderror" 
                                           id="valid_id_number" name="valid_id_number" 
                                           value="{{ old('valid_id_number', $profile->valid_id_number) }}" required>
                                    @error('valid_id_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="valid_id_path" class="form-label">Upload Valid ID *</label>
                                    <input type="file" class="form-control @error('valid_id_path') is-invalid @enderror" 
                                           id="valid_id_path" name="valid_id_path" 
                                           accept="image/*,.pdf">
                                    @error('valid_id_path')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    @if($profile->valid_id_path)
                                        <div class="mt-2">
                                            <small>Current file: </small>
                                            <a href="{{ Storage::url($profile->valid_id_path) }}" target="_blank" class="text-decoration-none">
                                                View Uploaded ID
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="proof_of_residency_path" class="form-label">Proof of Residency</label>
                                    <input type="file" class="form-control @error('proof_of_residency_path') is-invalid @enderror" 
                                           id="proof_of_residency_path" name="proof_of_residency_path" 
                                           accept="image/*,.pdf">
                                    @error('proof_of_residency_path')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    @if($profile->proof_of_residency_path)
                                        <div class="mt-2">
                                            <small>Current file: </small>
                                            <a href="{{ Storage::url($profile->proof_of_residency_path) }}" target="_blank" class="text-decoration-none">
                                                View Uploaded Proof
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Information Section -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">Additional Information</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="occupation" class="form-label">Occupation</label>
                                    <input type="text" class="form-control @error('occupation') is-invalid @enderror" 
                                           id="occupation" name="occupation" 
                                           value="{{ old('occupation', $profile->occupation) }}">
                                    @error('occupation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="civil_status" class="form-label">Civil Status</label>
                                    <select class="form-select @error('civil_status') is-invalid @enderror" 
                                            id="civil_status" name="civil_status">
                                        <option value="">Select Status</option>
                                        <option value="single" @selected(old('civil_status', $profile->civil_status) == 'single')>Single</option>
                                        <option value="married" @selected(old('civil_status', $profile->civil_status) == 'married')>Married</option>
                                        <option value="widowed" @selected(old('civil_status', $profile->civil_status) == 'widowed')>Widowed</option>
                                        <option value="separated" @selected(old('civil_status', $profile->civil_status) == 'separated')>Separated</option>
                                    </select>
                                    @error('civil_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="nationality" class="form-label">Nationality</label>
                                    <input type="text" class="form-control @error('nationality') is-invalid @enderror" 
                                           id="nationality" name="nationality" 
                                           value="{{ old('nationality', $profile->nationality ?? 'Filipino') }}">
                                    @error('nationality')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-1"></i> Save Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection