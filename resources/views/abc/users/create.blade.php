@extends('layouts.abc')

@section('title', 'Create New User')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Create New User</h1>
        <a href="{{ route('abc.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Users
        </a>
    </div>

    <div class="row">
        <div class="col-lg-10 col-xl-8">
            <div class="card shadow">
                <div class="card-body">
                    <form action="{{ route('abc.users.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Basic Information -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Basic Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" 
                                               value="{{ old('first_name') }}" required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Middle Name</label>
                                        <input type="text" name="middle_name" class="form-control @error('middle_name') is-invalid @enderror" 
                                               value="{{ old('middle_name') }}">
                                        @error('middle_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" 
                                               value="{{ old('last_name') }}" required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-1 mb-3">
                                        <label class="form-label">Suffix</label>
                                        <input type="text" name="suffix" class="form-control @error('suffix') is-invalid @enderror" 
                                               value="{{ old('suffix') }}" placeholder="Jr., Sr.">
                                        @error('suffix')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                               value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Contact Number</label>
                                        <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" 
                                               value="{{ old('phone_number') }}" placeholder="(043) 123-4567">
                                        @error('phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Birth Date</label>
                                        <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" 
                                               value="{{ old('birth_date') }}" max="{{ date('Y-m-d') }}">
                                        @error('birth_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Sex</label>
                                        <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                            <option value="">Select here...</option>
                                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Role & Assignment -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-id-badge me-2"></i>Role & Assignment</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Role <span class="text-danger">*</span></label>
                                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                                            <option value="">Select Role</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                                    {{ ucwords(str_replace('-', ' ', $role->name)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3" id="barangay-field">
                                        <label class="form-label">Barangay Assignment</label>
                                        <select name="barangay_id" id="barangay_id" class="form-select @error('barangay_id') is-invalid @enderror">
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
                                </div>

                                <!-- Councilor-specific fields -->
                                <div id="councilor-fields" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Councilor Number <span class="text-danger">*</span></label>
                                            <select name="councilor_number" class="form-select @error('councilor_number') is-invalid @enderror">
                                                <option value="">Select Number</option>
                                                @for($i = 1; $i <= 7; $i++)
                                                    <option value="{{ $i }}" {{ old('councilor_number') == $i ? 'selected' : '' }}>
                                                        Kagawad {{ $i }}
                                                    </option>
                                                @endfor
                                            </select>
                                            @error('councilor_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Committee Assignment <span class="text-danger">*</span></label>
                                            <select name="committee" class="form-select @error('committee') is-invalid @enderror">
                                                <option value="">Select Committee</option>
                                                @foreach($committees as $key => $label)
                                                    <option value="{{ $key }}" {{ old('committee') == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('committee')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Position Title</label>
                                    <input type="text" name="position_title" class="form-control @error('position_title') is-invalid @enderror" 
                                           value="{{ old('position_title') }}" placeholder="e.g., Chief of Staff, Administrative Officer">
                                    @error('position_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Optional custom position title</small>
                                </div>

                                <!-- Term Information -->
                                <div id="term-fields" style="display: none;">
                                    <hr>
                                    <h6 class="mb-3">Term Information</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Term Start</label>
                                            <input type="date" name="term_start" class="form-control @error('term_start') is-invalid @enderror" 
                                                   value="{{ old('term_start', now()->format('Y-m-d')) }}">
                                            @error('term_start')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Term End</label>
                                            <input type="date" name="term_end" class="form-control @error('term_end') is-invalid @enderror" 
                                                   value="{{ old('term_end', now()->addYears(3)->format('Y-m-d')) }}">
                                            @error('term_end')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Security -->
                        <div class="card mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Account Security</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Minimum 8 characters</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" class="form-control" required>
                                    </div>
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" 
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Account Active (User can login immediately)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('abc.users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const barangayRoles = ['barangay-captain', 'barangay-councilor', 'barangay-secretary','barangay-staff', 'lupon-member'];
    const termRoles = ['abc-president', 'barangay-captain', 'barangay-councilor', 'barangay-secretary'];

    function updateFormFields() {
        const selectedRole = $('#role').val();
        
        // Show/hide barangay field
        if (barangayRoles.includes(selectedRole)) {
            $('#barangay-field').show();
            $('#barangay_id').prop('required', true);
        } else {
            $('#barangay-field').hide();
            $('#barangay_id').prop('required', false);
            $('#barangay_id').val('');
        }

        // Show/hide councilor fields
        if (selectedRole === 'barangay-councilor') {
            $('#councilor-fields').show();
            $('#councilor-fields select').prop('required', true);
        } else {
            $('#councilor-fields').hide();
            $('#councilor-fields select').prop('required', false);
        }

        // Show/hide term fields
        if (termRoles.includes(selectedRole)) {
            $('#term-fields').show();
        } else {
            $('#term-fields').hide();
        }
    }

    $('#role').on('change', updateFormFields);
    updateFormFields();
});
</script>
@endpush