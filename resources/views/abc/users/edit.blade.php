@extends('layouts.abc')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit User: {{ $user->name }}</h1>
        <div>
            <a href="{{ route('abc.users.show', $user) }}" class="btn btn-info">
                <i class="fas fa-eye me-2"></i>View Profile
            </a>
            <a href="{{ route('abc.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Users
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10 col-xl-8">
            <div class="card shadow">
                <div class="card-body">
                    <form action="{{ route('abc.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

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
                                               value="{{ old('first_name', $user->first_name) }}" required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Middle Name</label>
                                        <input type="text" name="middle_name" class="form-control @error('middle_name') is-invalid @enderror" 
                                               value="{{ old('middle_name', $user->middle_name) }}">
                                        @error('middle_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" 
                                               value="{{ old('last_name', $user->last_name) }}" required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-1 mb-3">
                                        <label class="form-label">Suffix</label>
                                        <input type="text" name="suffix" class="form-control @error('suffix') is-invalid @enderror" 
                                               value="{{ old('suffix', $user->suffix) }}" placeholder="Jr., Sr.">
                                        @error('suffix')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                               value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Contact Number</label>
                                        <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" 
                                               value="{{ old('phone_number', $user->phone_number) }}" placeholder="(043) 123-4567">
                                        @error('phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Birth Date</label>
                                        <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" 
                                               value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}">
                                        @error('birth_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Gender</label>
                                        <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                            <option value="">Select Gender</option>
                                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $user->address) }}</textarea>
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
                                                <option value="{{ $role->name }}" {{ old('role', $user->getRoleNames()->first()) == $role->name ? 'selected' : '' }}>
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
                                                <option value="{{ $barangay->id }}" {{ old('barangay_id', $user->barangay_id) == $barangay->id ? 'selected' : '' }}>
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
                                                    <option value="{{ $i }}" {{ old('councilor_number', $user->councilor_number) == $i ? 'selected' : '' }}>
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
                                                    <option value="{{ $key }}" {{ old('committee', $user->committee) == $key ? 'selected' : '' }}>
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
                                           value="{{ old('position_title', $user->position_title) }}" placeholder="e.g., Chief of Staff, Administrative Officer">
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
                                                   value="{{ old('term_start', $termStart) }}">
                                            @error('term_start')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Term End</label>
                                            <input type="date" name="term_end" class="form-control @error('term_end') is-invalid @enderror" 
                                                   value="{{ old('term_end', $termEnd) }}">
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
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Leave password fields empty to keep current password
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">New Password</label>
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Minimum 8 characters</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Confirm New Password</label>
                                        <input type="password" name="password_confirmation" class="form-control">
                                    </div>
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" 
                                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Account Active
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <div>
                                @if(!$user->hasRole('municipality-admin'))
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#archiveModal">
                                    <i class="fas fa-archive me-2"></i>Archive User
                                </button>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('abc.users.show', $user) }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update User
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Archive Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Archive User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('abc.users.archive', $user) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Are you sure you want to archive <strong>{{ $user->name }}</strong>?</p>
                    <p class="text-muted">This will deactivate the account and mark it as archived. You can restore it later if needed.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Archive User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const barangayRoles = ['barangay-captain', 'barangay-secretary', 'barangay-treasurer', 'barangay-staff', 'lupon-member'];
    const termRoles = ['abc-president', 'barangay-captain', 'barangay-secretary'];

    function updateFormFields() {
        const selectedRole = $('#role').val();
        
        if (barangayRoles.includes(selectedRole)) {
            $('#barangay-field').show();
            $('#barangay_id').prop('required', true);
        } else {
            $('#barangay-field').hide();
            $('#barangay_id').prop('required', false);
        }

        // Councilor fields removed - officials are now managed via organizational chart
        $('#councilor-fields').hide();
        $('#councilor-fields select').prop('required', false);

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